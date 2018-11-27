<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSession', 'Model/Datasource');
App::uses('ViewExtensionComponent', 'CakeTheme.Controller/Component');

/**
 * ViewExtensionTestController class
 *
 */
class ViewExtensionTestController extends Controller {

/**
 * testUrl property
 *
 * @var mixed
 */
	public $testUrl = null;

/**
 * testStatus property
 *
 * @var mixed
 */
	public $testStatus = null;

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'RequestHandler'
	];

/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param string|array $url A string or array-based URL pointing to another location within the app,
 *    or an absolute URL
 * @param int|array|null $status HTTP status code (eg: 301). Defaults to 302 when null is passed.
 * @param bool $exit If true, exit() will be called after the redirect
 * @return CakeResponse|null
 * @triggers Controller.beforeRedirect $this, array($url, $status, $exit)
 * @link https://book.cakephp.org/2.0/en/controllers.html#Controller::redirect
 */
	public function redirect($url, $status = null, $exit = true) {
		if (!empty($url)) {
			$this->testUrl = Router::url($url);
		}
		$this->testStatus = $status;

		return false;
	}

}

/**
 * ViewExtensionComponent Test Case
 */
class ViewExtensionComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session'
	];

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
		unset($this->ViewExtension);

		parent::tearDown();
	}

/**
 * testStartupSetVar method
 *
 * @return void
 */
	public function testStartupSetVar() {
		Configure::write('Config.language', 'eng');
		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/test', 'GET', ['ext' => 'prt']);
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
		];
		$this->assertData($expected, $result);

		Configure::write('Config.language', 'rus');
		$this->setAjaxRequest();
		$this->_createComponet('/cake_theme/test.pop', 'POST', ['ext' => 'pop']);
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'uiLcid2' => 'ru',
			'uiLcid3' => 'rus',
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();

		$this->setPjaxRequest();
		$this->_createComponet('/cake_theme/test.mod', 'GET', ['ext' => 'mod']);
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'uiLcid2' => 'ru',
			'uiLcid3' => 'rus',
		];
		$this->assertData($expected, $result);
		$this->resetPjaxRequest();

		$this->setJsonRequest();
		$this->_createComponet('/cake_theme/test.json', 'GET', ['ext' => 'json']);
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
	}

/**
 * testInitializeSetLocaleEng method
 *
 * @return void
 */
	public function testInitializeSetLocaleEng() {
		$this->skipIf(DIRECTORY_SEPARATOR !== '\\', '');

		Configure::write('Config.language', 'eng');
		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$result = setlocale(LC_ALL, '0');
		$expected = 'English_United Kingdom.1252';
		$this->assertContains($expected, $result);
	}

/**
 * testInitializeSetLocaleRus method
 *
 * @return void
 */
	public function testInitializeSetLocaleRus() {
		$this->skipIf(DIRECTORY_SEPARATOR !== '\\', '');

		Configure::write('Config.language', 'rus');
		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$result = setlocale(LC_ALL, '0');
		$expected = 'Russian_Russia.1251';
		$this->assertContains($expected, $result);
	}

/**
 * testBeforeRenderSetLayout method
 *
 * @return void
 */
	public function testBeforeRenderSetLayout() {
		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$this->Controller->layout = 'main';
		$this->ViewExtension->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'main';
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/test.mod', 'POST', ['ext' => 'mod']);
		$this->ViewExtension->initialize($this->Controller);
		$this->Controller->layout = 'main';
		$this->ViewExtension->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.default';
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/test.sse', 'POST', ['ext' => 'sse']);
		$this->ViewExtension->initialize($this->Controller);
		$this->Controller->layout = 'main';
		$this->ViewExtension->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'CakeTheme.default';
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/test.prt', 'GET', ['ext' => 'prt']);
		$this->ViewExtension->initialize($this->Controller);
		$this->Controller->layout = 'main';
		$this->ViewExtension->beforeRender($this->Controller);
		$result = $this->Controller->layout;
		$expected = 'main';
		$this->assertData($expected, $result);
	}

/**
 * testIsHtml method
 *
 * @return void
 */
	public function testIsHtml() {
		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->isHtml();
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test.mod', 'POST', ['ext' => 'mod']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->isHtml();
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test.sse', 'POST', ['ext' => 'sse']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->isHtml();
		$this->assertFalse($result);

		$this->_createComponet('/cake_theme/test.prt', 'GET', ['ext' => 'prt']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->isHtml();
		$this->assertTrue($result);
	}

/**
 * testSetRedirectUrl method
 *
 * @return void
 */
	public function testSetRedirectUrl() {
		$key = 'testKeySet';
		Configure::write('App.fullBaseUrl', false);
		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setRedirectUrl(null, $key);
		$result = CakeSession::read(md5($key));
		$expected = '/';
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/test');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setRedirectUrl(true, $key);
		$result = CakeSession::read(md5($key));
		$expected = '/\/cake_theme\/test.*/';
		$this->assertRegExp($expected, $result);

		$this->_createComponet('/cake_theme/index');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setRedirectUrl('/test', $key);
		$result = CakeSession::read(md5($key));
		$expected = '/test';
		$this->assertData($expected, $result);
		CakeSession::delete(md5($key));
	}

/**
 * testGetRedirectUrl method
 *
 * @return void
 */
	public function testGetRedirectUrl() {
		$key = 'testKeyGet';
		CakeSession::delete(md5($key));
		$this->_createComponet('/cake_theme/some_act');
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->getRedirectUrl(null, $key);
		$expected = ['action' => 'index'];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/some_act');
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->getRedirectUrl(true, $key);
		$expected = '/\/cake_theme\/some_act.*/';
		$this->assertRegExp($expected, $result);

		$this->_createComponet('/cake_theme/test_act');
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->getRedirectUrl('/test/act', $key);
		$expected = '/test/act';
		$this->assertData($expected, $result);

		$result = CakeSession::write(md5($key), '/home/index');
		$this->_createComponet('/cake_theme/test_act');
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->ViewExtension->getRedirectUrl('/test/act', $key);
		$expected = '/home/index';
		$this->assertData($expected, $result);
		CakeSession::delete(md5($key));
	}

/**
 * testRedirectByUrl method
 *
 * @return void
 */
	public function testRedirectByUrl() {
		$key = 'testKeyGet';
		CakeSession::delete(md5($key));
		$this->_createComponet('/cake_theme/view_extension_test');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->redirectByUrl(null, $key);
		$result = $this->Controller->testUrl;
		$expected = '/';
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/some_act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->redirectByUrl(true, $key);
		$result = $this->Controller->testUrl;
		$expected = '/\/cake_theme\/some_act.*/';
		$this->assertRegExp($expected, $result);

		$this->_createComponet('/cake_theme/test_act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->redirectByUrl('/test/act', $key);
		$result = $this->Controller->testUrl;
		$expected = '/test/act';
		$this->assertData($expected, $result);

		$result = CakeSession::write(md5($key), '/home/index');
		$this->_createComponet('/cake_theme/test_act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->redirectByUrl('/test/act', $key);
		$result = $this->Controller->testUrl;
		$expected = '/home/index';
		$this->assertData($expected, $result);
		CakeSession::delete(md5($key));
	}

/**
 * testDetect method
 *
 * @return void
 */
	public function testDetect() {
		$this->_createComponet('/cake_theme/test_act');
		$this->ViewExtension->initialize($this->Controller);

		$result = $this->Controller->request->is('sse');
		$this->assertFalse($result);

		$result = $this->Controller->request->is('modal');
		$this->assertFalse($result);

		$result = $this->Controller->request->is('popup');
		$this->assertFalse($result);

		$result = $this->Controller->request->is('print');
		$this->assertFalse($result);

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0';
		$result = $this->Controller->request->is('msie');
		$this->assertFalse($result);

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko';
		$result = $this->Controller->request->is('msie');
		$this->assertTrue($result);

		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; SAMSUNG; OMNIA7)';
		$result = $this->Controller->request->is('msie');
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test_act.sse', 'POST', ['ext' => 'sse']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->Controller->request->is('sse');
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test_act.mod', 'POST', ['ext' => 'mod']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->Controller->request->is('modal');
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test_act.pop', 'GET', ['ext' => 'pop']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->Controller->request->is('popup');
		$this->assertTrue($result);

		$this->_createComponet('/cake_theme/test_act.prt', 'GET', ['ext' => 'prt']);
		$this->ViewExtension->initialize($this->Controller);
		$result = $this->Controller->request->is('print');
		$this->assertTrue($result);
	}

/**
 * testSetProgressSseTask method
 *
 * @return void
 */
	public function testSetProgressSseTask() {
		CakeSession::delete('SSE.progress');
		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setProgressSseTask();
		$result = CakeSession::read('SSE.progress');
		$this->assertEmpty($result);

		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setProgressSseTask('SomeTask');
		$this->ViewExtension->setProgressSseTask('SomeTask');
		$this->ViewExtension->setProgressSseTask('TestTask');
		$result = CakeSession::read('SSE.progress');
		$expected = [
			'SomeTask',
			'TestTask'
		];
		$this->assertData($expected, $result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testSetExceptionMessageText method
 *
 * @return void
 */
	public function testSetExceptionMessageText() {
		CakeSession::delete('Message');
		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setExceptionMessage('Invalid ID of record', '/cake_theme/some_controller');
		$result = CakeSession::read('Message.flash');
		$expected = [
			[
				'message' => 'Invalid ID of record',
				'key' => 'flash',
				'element' => 'Flash/error',
				'params' => [],
			]
		];
		$this->assertData($expected, $result);

		$result = $this->Controller->testUrl;
		$expected = '/cake_theme/some_controller';
		$this->assertData($expected, $result);
	}

/**
 * testSetExceptionMessageException method
 *
 * @return void
 */
	public function testSetExceptionMessageException() {
		CakeSession::delete('Message');
		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setExceptionMessage(new NotFoundException('Invalid ID of record'), '/cake_theme/some_controller/some_act');
		$result = CakeSession::read('Message.flash');
		$expected = [
			[
				'message' => 'Invalid ID of record',
				'key' => 'flash',
				'element' => 'Flash/error',
				'params' => [
					'code' => 404
				],
			]
		];
		$this->assertData($expected, $result);

		$result = $this->Controller->testUrl;
		$expected = '/cake_theme/some_controller/some_act';
		$this->assertData($expected, $result);
	}

/**
 * testSetExceptionMessageExceptionAjax method
 *
 * @return void
 */
	public function testSetExceptionMessageExceptionAjax() {
		CakeSession::delete('Message');
		$this->setAjaxRequest();
		$this->_createComponet('/cake_theme/act');
		$this->ViewExtension->initialize($this->Controller);
		$this->ViewExtension->setExceptionMessage(new NotFoundException('Invalid ID of record'), '/cake_theme/some_controller/some_act');
		$this->resetAjaxRequest();
		$result = CakeSession::check('Message.flash');
		$this->assertFalse($result);

		$result = $this->Controller->testStatus;
		$expected = 404;
		$this->assertData($expected, $result);

		$result = $this->Controller->testUrl;
		$this->assertNull($result);
	}

/**
 * Create ViewExtensionComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();

		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new ViewExtensionTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Controller->RequestHandler->initialize($this->Controller);
		$this->ViewExtension = new ViewExtensionComponent($Collection);
	}
}
