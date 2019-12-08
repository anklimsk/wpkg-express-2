<?php
/**
 * This file is the componet file of the plugin.
 * Setting search form input and parse URL GET parameters
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::import(
	'Vendor',
	'CakeSearchInfo.LangCorrect',
	['file' => 'LangCorrect' . DS . 'autoload.php']
);

/**
 * SearchFilter Component.
 *
 * Setting search form input and parse URL GET parameters
 * @package plugin.Controller.Component
 */
class SearchFilterComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of model `Search`
 *
 * @var object
 */
	protected $_modelSearch = null;

/**
 * Object of model `ConfigSearchInfo`
 *
 * @var object
 */
	protected $_modelConfigSearchInfo = null;

/**
 * Configuration of component
 *
 * @var array
 */
	protected $_searchInfo = [];

/**
 * Limit for autocomplete in search form
 *
 * @var int
 */
	public $AutocompleteLimit = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_modelSearch = ClassRegistry::init('CakeSearchInfo.Search');
		$this->_modelConfigSearchInfo = ClassRegistry::init('CakeSearchInfo.ConfigSearchInfo');
		if (isset($settings['CakeSearchInfo']) && !empty($settings['CakeSearchInfo'])) {
			$this->_searchInfo = (array)$settings['CakeSearchInfo'];
		}

		if (isset($settings['AutocompleteLimit']) && !empty($settings['AutocompleteLimit'])) {
			$this->AutocompleteLimit = (int)$settings['AutocompleteLimit'];
		}

		parent::__construct($collection, $settings);
	}

/**
 * Merge configuration of plugin with configuration of component and write it.
 *
 * @return void
 */
	protected function _setConfig() {
		$config = [];
		$currentConfig = $this->_modelConfigSearchInfo->getConfig();
		if (!empty($this->_searchInfo) && is_array($this->_searchInfo)) {
			$config = $this->_searchInfo + $currentConfig;
		}

		if (!empty($this->AutocompleteLimit) && is_numeric($this->AutocompleteLimit)) {
			$config = Hash::merge($config, ['AutocompleteLimit' => $this->AutocompleteLimit] + $currentConfig);
		}

		if (!empty($config)) {
			Configure::write('CakeSearchInfo', $config);
		}
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('CakeTheme.ViewExtension')) {
			$this->_controller->ViewExtension = $this->_controller->Components->load('CakeTheme.ViewExtension');
			$this->_controller->ViewExtension->initialize($this->_controller);
		}
	}

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * Actions:
 *  - Set global variables for View;
 *  - Setting search form input;
 *  - Update configuration of plugin.
 *
 * @param Controller $controller Controller instance.
 * @return void
 */
	public function startup(Controller $controller) {
		$this->_setConfig();
		if (!$this->_controller->ViewExtension->isHtml()) {
			return;
		}

		$targetDeep = $this->_modelConfigSearchInfo->getTargetDeep();
		$querySearchMinLength = $this->_modelConfigSearchInfo->getQuerySearchMinLength();
		$targetFieldsSelected = $this->getTargetList();
		$targetFields = $this->_modelSearch->getTargetFields();

		$controller->set('search_targetDeep', $targetDeep);
		$controller->set('search_querySearchMinLength', $querySearchMinLength);
		$controller->set('search_targetFieldsSelected', $targetFieldsSelected);
		$controller->set('search_targetFields', $targetFields);
	}

/**
 * Get list of target fields by URL GET parameters
 *
 * @return array List of target fields
 * @throws InternalErrorException
 */
	public function getTargetList() {
		$targetFieldsList = $this->_modelSearch->getTargetFieldsList();
		$targetData = $this->_controller->request->query('target');
		if (empty($targetData)) {
			$target = $targetFieldsList;
		} else {
			$target = array_intersect((array)$targetData, $targetFieldsList);
			if (empty($target)) {
				throw new InternalErrorException(__d('cake_search_info', 'Invalid fields for filter of search scope.'));
			}
			$target = array_values($target);
		}
		if ($this->_modelSearch->getAnyPartFlag($targetData)) {
			$target[] = CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART;
		}

		return $target;
	}

/**
 * Get state of flag any part search by URL GET parameters
 *
 * @return bool State of flag any part search
 */
	public function getAnyPartFlag() {
		$targetData = $this->_controller->request->query('target');
		$anyPart = $this->_modelSearch->getAnyPartFlag($targetData);

		return $anyPart;
	}

/**
 * Get query string by URL GET parameters
 *
 * @return string Query string
 */
	public function getQueryStr() {
		$queryData = (string)$this->_controller->request->query('query');
		$query = $this->_modelSearch->getQueryStr($queryData);

		return $query;
	}

/**
 * Get state of flag correct query
 *
 * @return bool State of flag correct query
 */
	public function getCorrectFlag() {
		$correct = (bool)$this->_controller->request->query('correct');

		return $correct;
	}

/**
 * Action `search`. Used to search information in database.
 *
 * @param array $whitelist List of allowed fields for ordering. This allows you to prevent ordering
 *  on non-indexed, or undesirable columns. See PaginatorComponent::validateSort() for additional details
 *  on how the whitelisting and sort field validation works.
 * @return void
 */
	public function search($whitelist = []) {
		if (!$this->_controller->Components->loaded('Paginator')) {
			$this->_controller->Paginator = $this->_controller->Components->load('Paginator');
			$this->_controller->Paginator->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('Flash')) {
			$this->_controller->Flash = $this->_controller->Components->load('Flash');
			$this->_controller->Flash->initialize($this->_controller);
		}
		if (!property_exists($this->_controller, 'Search')) {
			$this->_controller->loadModel('CakeSearchInfo.Search');
		}
		$this->_controller->disableCache();

		$target = $this->getTargetList();
		$query = $this->getQueryStr();
		$correct = $this->getCorrectFlag();
		$querySearchMinLength = $this->_modelConfigSearchInfo->getQuerySearchMinLength();

		$result = false;
		$conditions = null;
		$queryCorrect = '';
		$lang = (string)Configure::read('Config.language');
		if (property_exists($this->_controller, 'paginate')) {
			$this->_controller->Paginator->settings = $this->_controller->paginate;
		}
		if (!empty($query)) {
			if (mb_strlen($query) >= $querySearchMinLength) {
				$this->_controller->request->data('Search.query', $query);
				$conditions = compact('query', 'target');
				$result = $this->_controller->Paginator->paginate('Search', $conditions, $whitelist);
				if (empty($result) && (mb_strtolower($lang) === 'rus')
					&& version_compare(PHP_VERSION, '7.3.0', '<')) {
					$textConv = new Text_LangCorrect();
					$queryCorrect = $textConv->parse($query, Text_LangCorrect::SIMILAR_CHARS | Text_LangCorrect::KEYBOARD_LAYOUT);
					if (($query === $queryCorrect) || $correct) {
						$queryCorrect = '';
					} else {
						$this->_controller->request->data('Search.query', $queryCorrect);
						$conditions = compact('target') + ['query' => $queryCorrect];
						$result = $this->_controller->Paginator->paginate('Search', $conditions, $whitelist);
					}
				}
				if (empty($result)) {
					$this->_controller->Flash->information(__d('cake_search_info', 'No results for searching "%s"', $query));
				} elseif (isset($result['count'])) {
					$params = $this->_controller->request->params;
					$params['paging']['Search']['current'] = $result['count'];
					$this->_controller->request->addParams($params);
				}
			} elseif (mb_strlen($query) < $querySearchMinLength) {
				$this->_controller->Flash->warning(__d(
					'cake_search_info',
					'Input minimum %d %s',
					$querySearchMinLength,
					__dn('cake_search_info', 'character', 'characters', $querySearchMinLength)
				));
			}
		} else {
			$this->_controller->Flash->information(__d('cake_search_info', 'Enter your query in the search bar'));
		}
		$queryConfig = $this->_modelSearch->getQueryConfig($target);
		$this->_controller->set(compact('query', 'queryCorrect', 'queryConfig', 'result', 'target', 'correct'));
	}

/**
 * Action `autocomplete`. Used to autocomplte input fields.
 *
 * POST Data:
 *  - `query` query string for autocomple;
 *  - `target` target field for autocomple;
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function autocomplete() {
		Configure::write('debug', 0);
		if (!$this->_controller->request->is('ajax') || !$this->_controller->request->is('post') ||
			!$this->_controller->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [];
		$query = $this->_controller->request->data('query');
		$target = (array)$this->_controller->request->data('target');
		$limit = $this->_modelConfigSearchInfo->getAutocompleteLimit();
		$data = $this->_modelSearch->getAutocomplete($query, $target, $limit);

		$this->_controller->set(compact('data'));
		$this->_controller->set('_serialize', 'data');
	}
}
