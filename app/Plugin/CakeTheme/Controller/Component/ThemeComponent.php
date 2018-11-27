<?php
/**
 * This file is the componet file of the plugin.
 * Set layout base on request.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');

/**
 * Theme Component.
 *
 * Methods for set layout base on request
 * @package plugin.Controller.Component
 */
class ThemeComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of model `ConfigTheme`
 *
 * @var object
 */
	protected $_modelConfigTheme = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_modelConfigTheme = ClassRegistry::init('CakeTheme.ConfigTheme');

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
 *
 * @param Controller $controller Controller instance.
 * @return void
 */
	public function startup(Controller $controller) {
		if (!$this->_controller->ViewExtension->isHtml()) {
			return;
		}

		$this->_setConfigVar($controller);
	}

/**
 * Called before the Controller::beforeRender(), and before
 * the view class is loaded, and before Controller::render()
 *
 * Actions:
 *  - Sets the layer depending on the request.
 *
 * @param Controller $controller Controller with components to beforeRender
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRender
 */
	public function beforeRender(Controller $controller) {
		$this->_setLayout($controller);
	}

/**
 * Sets the layer depending on the request
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _setLayout(Controller &$controller) {
		if ($controller->name == 'CakeError') {
			$controller->layout = 'CakeTheme.error';

			return;
		}

		$isAjax = $controller->request->is('ajax');
		$isPjax = $controller->request->is('pjax');
		$isHtml = $this->_controller->ViewExtension->isHtml();
		if ($isPjax) {
			$controller->layout = 'CakeTheme.pjax';
		} elseif ($isHtml && !$isAjax) {
			if ($controller->request->param('controller') === 'users') {
				$controller->layout = 'CakeTheme.login';
			} else {
				if ($controller->layout === 'default') {
					$controller->layout = 'CakeTheme.main';
				}
			}
		}
	}

/**
 * Set global variable of used plugins
 *
 * Set global variable:
 *  - `additionalCssFiles`: List of additional CSS files;
 *  - `additionalJsFiles`: List of additional JS files.
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _setConfigVar(Controller &$controller) {
		$additionalCssFiles = $this->_modelConfigTheme->getListCssFiles();
		$additionalJsFiles = $this->_modelConfigTheme->getListJsFiles();
		$controller->set(compact('additionalCssFiles', 'additionalJsFiles'));
	}
}
