<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('InstallerComponent', 'CakeInstaller.Controller/Component');
App::uses('InstallerCheck', 'CakeInstaller.Model');

/**
 * InstallerTestController class
 *
 */
class InstallerTestController extends Controller {

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
 * InstallerComponent Test Case
 */
class InstallerComponentTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Installer);
		unset($this->Controller);

		parent::tearDown();
	}

/**
 * testInitializeNotInstalledRedirect method
 *
 * @return void
 */
	public function testInitializeNotInstalledRedirect() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', false, $params);
		$this->Installer->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$expected = '/cake_installer/check';
		$this->assertData($expected, $result);
	}

/**
 * testInitializeInstalledNoRedirect method
 *
 * @return void
 */
	public function testInitializeInstalledNoRedirect() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', true, $params);
		$this->Installer->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$expected = null;
		$this->assertData($expected, $result);
	}

/**
 * testInitializeNotInstalledNoRedirectIndex method
 *
 * @return void
 */
	public function testInitializeNotInstalledNoRedirectIndex() {
		$params = [
			'plugin' => 'cake_installer',
			'controller' => 'check',
			'action' => 'index',
		];
		$this->_createComponet('/cake_installer/check/index', false, $params);
		$this->Installer->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$expected = null;
		$this->assertData($expected, $result);
	}

/**
 * testInitializeInstalledNoRedirectIndex method
 *
 * @return void
 */
	public function testInitializeInstalledNoRedirectIndex() {
		$params = [
			'plugin' => 'cake_installer',
			'controller' => 'check',
			'action' => 'index',
		];
		$this->_createComponet('/cake_installer/check/index', true, $params);
		$this->Installer->initialize($this->Controller);
		$result = $this->Controller->testUrl;
		$expected = null;
		$this->assertData($expected, $result);
	}

/**
 * testIsAppInstalledTrue method
 *
 * @return void
 */
	public function testIsAppInstalledTrue() {
		$this->_createComponet('/cake_installer/check/index', true);
		$result = $this->Installer->isAppInstalled();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testIsAppInstalledFalse method
 *
 * @return void
 */
	public function testIsAppInstalledFalse() {
		$this->_createComponet('/cake_installer/check/index', false);
		$result = $this->Installer->isAppInstalled();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * Create InstallerComponent
 *
 * @param string|array $url A string or array-based URL
 * @param bool $isAppInstalled Flag of application installed state
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $isAppInstalled = true, $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new InstallerTestController($request, $response);
		$this->Controller->constructClasses();
		$Installer = new InstallerComponent($Collection);
		$this->Installer = $this->createProxyObject($Installer);
		$this->Installer->_modelInstallerCheck = $this->getMockBuilder('InstallerCheck')
			->setMethods(['isAppInstalled'])
			->getMock();

		$this->Installer->_modelInstallerCheck->expects($this->any())
			->method('isAppInstalled')
			->will($this->returnValue($isAppInstalled));
	}
}
