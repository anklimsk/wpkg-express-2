<?php
/**
 * This file is the componet file of the plugin.
 * Is used for processing moving and drag and drop items.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');

/**
 * Move Component.
 *
 * Is used for processing moving and drag and drop items.
 * @package plugin.Controller.Component
 */
class MoveComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of target model
 *
 * @var object
 */
	protected $_model = null;

/**
 * Name of target model
 *
 * @var string
 */
	public $modelName = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		if (isset($settings['model']) && !empty($settings['model'])) {
			$this->modelName = (string)$settings['model'];
		}

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @throws InternalErrorException if set invalid model in settings.
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (empty($this->modelName)) {
			$this->_model = $this->_controller->{$this->_controller->modelClass};
		} else {
			$this->_model = ClassRegistry::init($this->modelName, true);
			if ($this->_model === false) {
				throw new InternalErrorException(__d('view_extension', 'Invalid name of target model for component'));
			}
		}
		if (!$this->_model->Behaviors->loaded('Tree')) {
			throw new InternalErrorException(__d('view_extension', 'Tree behavior is not loaded'));
		}
	}

/**
 * Action `moveItem`. Used to move item to new position.
 *
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int $id ID of record for moving
 * @param int $delta Delta for moving
 * @return void
 */
	public function moveItem($direct = null, $id = null, $delta = 1) {
		$isJson = $this->_controller->RequestHandler->prefers('json');
		if ($isJson) {
			Configure::write('debug', 0);
		}
		$result = $this->_model->moveItem($direct, $id, $delta);
		if (!$isJson) {
			if (!$result) {
				$this->_controller->Flash->error(__d('view_extension', 'Error move record %d %s', $id, __d('view_extension_direct', $direct)));
			}

			return $this->_controller->redirect($this->_controller->request->referer(true));
		} else {
			$data = compact('result', 'direct', 'delta');
			$this->_controller->set(compact('data'));
			$this->_controller->set('_serialize', 'data');
		}
	}

/**
 * Action `dropItem`. Used to drag and drop item.
 *
 * POST Data:
 *  - `target` The ID of the item to moving to new position;
 *  - `parent` New parent ID of item;
 *  - `parentStart` Old parent ID of item;
 *  - `tree` Array of ID subtree for item.
 *
 * @throws BadRequestException if request is not AJAX, POST or JSON.
 * @return void
 */
	public function dropItem() {
		Configure::write('debug', 0);
		if (!$this->_controller->request->is('ajax') || !$this->_controller->request->is('post') ||
			!$this->_controller->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$id = $this->_controller->request->data('target');
		$newParentId = $this->_controller->request->data('parent');
		$oldParentId = $this->_controller->request->data('parentStart');
		$dropData = $this->_controller->request->data('tree');
		$dropData = json_decode($dropData, true);
		$result = $this->_model->moveDrop($id, $newParentId, $oldParentId, $dropData);
		$data = compact('result');
		$this->_controller->set(compact('data'));
		$this->_controller->set('_serialize', 'data');
	}
}
