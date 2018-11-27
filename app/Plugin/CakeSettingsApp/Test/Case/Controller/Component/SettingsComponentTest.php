<?php
App::uses('AppCakeTestCase', 'CakeSettingsApp.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('SettingsComponent', 'CakeSettingsApp.Controller/Component');
App::uses('CakeSession', 'Model/Datasource');

/**
 * SettingsTestController class
 *
 */
class SettingsTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * Example: `public $components = array('Session', 'RequestHandler', 'Acl');`
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
			'RequestHandler',
			'Flash',
			'Session',
			'Auth'
		];

/**
 * testUrl property
 *
 * @var mixed
 */
	public $testUrl = null;

/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param string|array $url A string or array-based URL pointing to another location within the app,
 *     or an absolute URL
 * @param int|array|null $status HTTP status code (eg: 301). Defaults to 302 when null is passed.
 * @param bool $exit If true, exit() will be called after the redirect
 * @return CakeResponse|null
 * @triggers Controller.beforeRedirect $this, array($url, $status, $exit)
 * @link https://book.cakephp.org/2.0/en/controllers.html#Controller::redirect
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->testUrl = Router::url($url);

		return false;
	}

}

/**
 * SettingsComponent Test Case
 */
class SettingsComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$userInfo = [
			'user' => 'Моисеева Л.Б.',
			'role' => CAKE_SETTINGS_APP_TEST_USER_ROLE_USER | CAKE_SETTINGS_APP_TEST_USER_ROLE_ADMIN,
			'includedFields' => [
				CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID => '38868aa0-33ef-4317-9a1b-e56b2b59c27d'
			],
			'prefix' => 'admin',
			'id' => '1'
		];
		$this->setDefaultUserInfo($userInfo);
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Settings);
		unset($this->Controller);

		parent::tearDown();
	}

/**
 * testInitializeNotConfigured method
 *
 * @return void
 */
	public function testInitializeNotConfigured() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', false, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertEmpty($result);
	}

/**
 * testInitializeConfiguredNoRedirect method
 *
 * @return void
 */
	public function testInitializeConfiguredNoRedirect() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', true, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertEmpty($result);
	}

/**
 * testInitializeNotConfiguredNoRedirectIndexLoggedIn method
 *
 * @return void
 */
	public function testInitializeNotConfiguredNoRedirectIndexLoggedIn() {
		$params = [
			'plugin' => 'cake_settings_app',
			'controller' => 'settings',
			'action' => 'index',
		];
		$this->_createComponet('/cake_settings_app/settings/index', false, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertEmpty($result);

		$result = CakeSession::read('Settings.FirstLogon');
		$this->assertEmpty($result);
	}

/**
 * testInitializeNotConfiguredNoRedirectIndexNoLoggedIn method
 *
 * @return void
 */
	public function testInitializeNotConfiguredNoRedirectIndexNoLoggedIn() {
		$this->clearUserInfo();
		$params = [
			'plugin' => 'cake_settings_app',
			'controller' => 'settings',
			'action' => 'index',
		];
		$this->_createComponet('/cake_settings_app/settings/index', false, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertEmpty($result);

		$result = CakeSession::read('Settings.FirstLogon');
		$this->assertTrue($result);
	}

/**
 * testInitializeConfiguredNoRedirectIndex method
 *
 * @return void
 */
	public function testInitializeConfiguredNoRedirectIndex() {
		$params = [
			'plugin' => 'cake_settings_app',
			'controller' => 'settings',
			'action' => 'index',
		];
		$this->_createComponet('/cake_settings_app/settings/index', true, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertEmpty($result);

		$result = CakeSession::read('Settings.FirstLogon');
		$this->assertNull($result);
	}

/**
 * testInitializeConfiguredRedirectIndexNoLoggedIn method
 *
 * @return void
 */
	public function testInitializeConfiguredRedirectIndexNoLoggedIn() {
		$this->clearUserInfo();
		$result = CakeSession::write('Settings.FirstLogon', true);
		$this->assertTrue($result);

		$params = [
			'plugin' => 'cake_settings_app',
			'controller' => 'settings',
			'action' => 'index',
		];
		$this->_createComponet('/cake_settings_app/settings/index', true, $params);
		$this->Settings->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$this->assertNotEmpty($result);

		$result = CakeSession::read('Settings.FirstLogon');
		$this->assertEmpty($result);
	}

/**
 * Create SettingsComponent
 *
 * @param string|array $url A string or array-based URL
 * @param bool $isAuthGroupConfigured Result of Model method
 *  Settings::isAuthGroupConfigured()
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $isAuthGroupConfigured = true, $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new SettingsTestController($request, $response);
		$this->Controller->constructClasses();
		$Settings = new SettingsComponent($Collection);
		$this->Settings = $this->createProxyObject($Settings);
		$this->Settings->_modelSetting = $this->getMockBuilder('Setting')
			->setMethods(['isAuthGroupConfigured'])
			->getMock();

		$this->Settings->_modelSetting->expects($this->any())
			->method('isAuthGroupConfigured')
			->will($this->returnValue($isAuthGroupConfigured));
	}
}
