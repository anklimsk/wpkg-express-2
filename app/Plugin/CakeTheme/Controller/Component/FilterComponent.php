<?php
/**
 * This file is the componet file of the plugin.
 * Build database query condition from filter data.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * Filter Component.
 *
 * Build database query condition from filter data.
 * @package plugin.Controller.Component
 */
class FilterComponent extends Component {

/**
 * Object of model `Filter`
 *
 * @var object
 */
	protected $_modelFilter = null;

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_modelFilter = ClassRegistry::init('CakeTheme.Filter');
		parent::__construct($collection, $settings);
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
	}

/**
 * Return database query condition from filter data
 *
 * @param string $plugin Name of plugin for target model of filter.
 * @return array Return database query condition from filter data.
 */
	public function getFilterConditions($plugin = null) {
		$filterData = null;
		$filterCond = null;
		if ($this->_controller->request->is('get')) {
			$filterData = $this->_controller->request->query('data.FilterData');
			$filterCond = $this->_controller->request->query('data.FilterCond');
		} elseif ($this->_controller->request->is('post')) {
			$filterData = $this->_controller->request->data('FilterData');
			$filterCond = $this->_controller->request->data('FilterCond');
		}
		$conditions = $this->_modelFilter->buildConditions($filterData, $filterCond, $plugin);

		return $conditions;
	}

/**
 * Return group action from filter data
 *
 * @param array $groupActions List of allowed group actions
 * @return string|bool Return string name of group action,
 *  or False otherwise.
 */
	public function getGroupAction($groupActions = null) {
		if (!$this->_controller->request->is('post')) {
			return false;
		}

		$action = $this->_controller->request->data('FilterGroup.action');
		if (empty($action) || (!empty($groupActions) && !is_array($groupActions))) {
			return false;
		}

		if (empty($groupActions)) {
			return $action;
		} elseif (in_array($action, $groupActions)) {
			return $action;
		} else {
			return false;
		}
	}

/**
 * Return pagination options for print preview request
 * If request is print preview and page number equal 1 -
 * set new limits for pagination.
 *
 * @param array $options Options for pagination component
 * @throws InternalErrorException if Paginator component is
 *  not loaded.
 * @return array Return pagination options.
 */
	public function getExtendPaginationOptions($options = null) {
		if (empty($options) || !is_array($options)) {
			$options = [];
		}

		if (!property_exists($this->_controller, 'Paginator')) {
			throw new InternalErrorException(__d('view_extension', 'Paginator component is not loaded'));
		}

		$paginatorOptions = $this->_controller->Paginator->mergeOptions(null);
		$page = (int)Hash::get($paginatorOptions, 'page');
		if (!$this->_controller->RequestHandler->prefers('prt') || ($page > 1)) {
			return $options;
		}

		$options['limit'] = CAKE_THEME_PRINT_DATA_LIMIT;
		$options['maxLimit'] = CAKE_THEME_PRINT_DATA_LIMIT;

		return $options;
	}
}
