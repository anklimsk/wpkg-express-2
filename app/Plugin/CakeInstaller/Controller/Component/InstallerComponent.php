<?php
/**
 * This file is the componet file of the plugin.
 * Redirect to installer, if application is not installed.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');

/**
 * Installer Component.
 *
 * Redirect to installer, if application is not installed.
 * @package plugin.Controller.Component
 */
class InstallerComponent extends Component {

/**
 * Object of model `InstallerCheck`
 *
 * @var object
 */
	protected $_modelInstallerCheck = null;

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * The identifier to read configuration for application
 *
 * @var string
 */
	protected $_configKey = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		if (isset($settings['ConfigKey']) && !empty($settings['ConfigKey'])) {
			$this->_configKey = (string)$settings['ConfigKey'];
		}

		$this->_modelInstallerCheck = ClassRegistry::init('CakeInstaller.InstallerCheck');
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
		$isAppInstalled = $this->isAppInstalled();
		if (($this->_controller->request->param('plugin') !== 'cake_installer') ||
			($this->_controller->request->param('controller') !== 'check') ||
			($this->_controller->request->param('action') !== 'index')) {
			if (!$isAppInstalled) {
				$urlRedirect = ['controller' => 'check', 'action' => 'index', 'plugin' => 'cake_installer'];
				$this->_controller->redirect($urlRedirect);
			}
		}
	}

/**
 * Return state of installation for application
 *
 * @return bool True, if application is installed, false otherwise.
 */
	public function isAppInstalled() {
		$isAppInstalled = $this->_modelInstallerCheck->isAppInstalled($this->_configKey);

		return $isAppInstalled;
	}
}
