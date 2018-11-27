<?php
/**
 * This file is the componet file of the plugin.
 * Redirect to settings of application, if it is not configured.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');

/**
 * Settings Component.
 *
 * Redirect to settings of application, if it is not configured.
 * @package plugin.Controller.Component
 */
class SettingsComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of model `Setting`
 *
 * @var object
 */
	protected $_modelSetting = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_modelSetting = ClassRegistry::init('Setting', true);
		if ($this->_modelSetting === false) {
			$this->_modelSetting = ClassRegistry::init('CakeSettingsApp.Setting');
		}

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * Actions:
 *  - Redirect to settings of application, if it is not configured.
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
		if (!$this->_controller->Components->loaded('Session')) {
			$this->_controller->Session = $this->_controller->Components->load('Session');
			$this->_controller->Session->initialize($this->_controller);
		}
		if ($this->_controller->Auth->loggedIn() ||
			$this->_controller->RequestHandler->prefers('json')) {
			return;
		}

		$isAuthCfg = $this->_modelSetting->isAuthGroupConfigured();
		if ($isAuthCfg) {
			if ($this->_controller->Session->read('Settings.FirstLogon')) {
				$this->_controller->Session->delete('Settings.FirstLogon');

				return $this->_controller->redirect($this->_controller->Auth->logout());
			}

			return;
		}

		$pluginName = $this->_controller->request->param('plugin');
		$controllerName = $this->_controller->request->param('controller');
		$actionName = $this->_controller->request->param('action');
		if (($pluginName !== 'cake_settings_app') ||
			($controllerName !== 'settings') ||
			($actionName !== 'index')) {
			return;
		}

		$this->_controller->Auth->allow('index');
		$this->_controller->Session->write('Settings.FirstLogon', true);
	}
}
