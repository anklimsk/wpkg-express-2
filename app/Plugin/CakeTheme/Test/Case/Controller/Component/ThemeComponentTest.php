<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ThemeComponent', 'CakeTheme.Controller/Component');

/**
 * ThemeTestController class
 *
 */
class ThemeTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
			'RequestHandler',
		];
}

/**
 * ThemeComponent Test Case
 */
class ThemeComponentTest extends AppCakeTestCase {

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
		unset($this->Theme);

		parent::tearDown();
	}

/**
 * testStartupSetVar method
 *
 * @return void
 */
	public function testStartupSetVar() {
		$this->_createComponet('/cake_theme/test');
		$this->Theme->initialize($this->Controller);
		$this->Theme->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'additionalCssFiles' => [
				'extendCssFile'
			],
			'additionalJsFiles' => [
				'someJsFile'
			]
		];
		$this->assertData($expected, $result);

		$this->setJsonRequest();
		$this->_createComponet('/cake_theme/test.json', ['ext' => 'json']);
		$this->Theme->initialize($this->Controller);
		$this->Theme->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
	}

/**
 * testBeforeRenderDefaultRequest method
 *
 * @return void
 */
	public function testBeforeRenderDefaultRequest() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', $params);
		$this->Theme->initialize($this->Controller);
		$this->Theme->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.main';
		$this->assertData($expected, $result);
	}

/**
 * testBeforeRenderLoginRequest method
 *
 * @return void
 */
	public function testBeforeRenderLoginRequest() {
		$params = [
			'plugin' => 'cake_ldap',
			'controller' => 'users',
			'action' => 'login',
		];
		$this->_createComponet('/cake_ldap/users/login', $params);
		$this->Theme->initialize($this->Controller);
		$this->Theme->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.login';
		$this->assertData($expected, $result);
	}

/**
 * testBeforeRenderErrorRequest method
 *
 * @return void
 */
	public function testBeforeRenderErrorRequest() {
		$this->_createComponet();
		$this->Theme->initialize($this->Controller);
		$this->Controller->name = 'CakeError';
		$this->Theme->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.error';
		$this->assertData($expected, $result);
	}

/**
 * testBeforeRenderAjaxRequestHtml method
 *
 * @return void
 */
	public function testBeforeRenderAjaxRequestHtml() {
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->setAjaxRequest();
		$this->_createComponet('/test/index', $params);
		$this->Theme->initialize($this->Controller);
		$this->Theme->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'default';
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testBeforeRenderPjaxRequest method
 *
 * @return void
 */
	public function testBeforeRenderPjaxRequest() {
		$_SERVER['HTTP_X_PJAX'] = true;
		$params = [
			'controller' => 'test',
			'action' => 'index',
		];
		$this->_createComponet('/test/index', $params);
		$this->Theme->initialize($this->Controller);
		$this->Theme->beforeRender($this->Controller);
		unset($_SERVER['HTTP_X_PJAX']);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.pjax';
		$this->assertData($expected, $result);
	}

/**
 * Create ThemeComponent
 *
 * @param string|array $url A string or array-based URL
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new ThemeTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Theme = new ThemeComponent($Collection);
	}
}
