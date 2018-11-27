<?php
/**
 * This file is the componet file of the plugin.
 * Return data for tree view.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */
App::uses('Component', 'Controller');

/**
 * Tree Component.
 *
 * Return data for tree view
 * @package plugin.Controller.Component
 */
class TreeComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of target tree model
 *
 * @var object
 */
	protected $_model = null;

/**
 * Name of target tree model
 *
 * @var string
 */
	protected $_modelName = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		if (isset($settings['model']) && !empty($settings['model'])) {
			$this->_modelName = (string)$settings['model'];
		}

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @throws InternalErrorException if invalid Model name for tree view.
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (empty($this->_modelName)) {
			$this->_model = $this->_controller->{$this->_controller->modelClass};
		} else {
			$this->_model = ClassRegistry::init($this->_modelName, true);
			if ($this->_model === false) {
				throw new InternalErrorException(__d('view_extension', 'Invalid Model name for tree view'));
			}
		}
	}

/**
 * Return data for tree view
 *
 * @param int|string $id ID for parent element of tree.
 * @throws InternalErrorException if method Model::getTreeData() is
 *  not exists.
 * @throws BadRequestException if request is not AJAX, POST or JSON.
 * @return object Return response object of exported file.
 */
	public function tree($id = null) {
		Configure::write('debug', 0);
		if (!method_exists($this->_model, 'getTreeData')) {
			throw new InternalErrorException(__d(
				'view_extension',
				'Method "%s" is not exists in model "%s"',
				'getTreeData()',
				$this->_model->name
			));
		}
		if (!$this->_controller->request->is('ajax') || !$this->_controller->request->is('post') ||
			!$this->_controller->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [
			[
				'text' => __d('view_extension', '&lt;None&gt;'),
			]
		];
		$treeData = $this->_model->getTreeData($id);
		if (!empty($treeData)) {
			$data = $treeData;
		}
		$this->_controller->set(compact('data'));
		$this->_controller->set('_serialize', 'data');
	}
}
