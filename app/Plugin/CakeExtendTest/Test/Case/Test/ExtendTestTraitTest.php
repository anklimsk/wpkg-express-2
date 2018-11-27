<?php
App::uses('ExtendTestTrait', 'CakeExtendTest.Utility');
App::uses('CakeSession', 'Model/Datasource');

class TestTrait {
	use ExtendTestTrait;
}

/**
 * Test_Alt_Some_Class class
 *
 * @package     plugin.Test.Case.TestCase
 */
class Test_Alt_Some_Class {

/**
 * Method `_some_method`.
 * Used for testing access to protected method.
 *
 * @return string Text
 */
	// @codingStandardsIgnoreStart
	protected function _some_method() {
		// @codingStandardsIgnoreEnd
		$result = 'Some protected method';

		return $result;
	}

}

/**
 * ExtendCakeTestCaseTest Test Case
 *
 */
class ExtendTestTraitTest extends CakeTestCase {

/**
 * Object for testing
 *
 * @var object
 */
	protected $_targetObject = null;

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
		$this->_targetObject = new TestTrait();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);
		parent::tearDown();
	}

/**
 * testGetDefaultUserInfo method
 *
 * @return void
 */
	public function testGetDefaultUserInfo() {
		$this->_targetObject->setDefaultUserInfo(['name' => 'Some User']);
		$result = $this->_targetObject->getDefaultUserInfo();
		$expected = [
			'name' => 'Some User',
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testApplyUserInfo method
 *
 * @return void
 */
	public function testApplyUserInfo() {
		$this->_targetObject->setDefaultUserInfo(['name' => 'Some User']);
		$result = $this->_targetObject->applyUserInfo();
		$this->assertTrue($result);

		$result = CakeSession::read('Auth.User');
		$expected = [
			'name' => 'Some User'
		];
		$this->assertEquals($expected, $result);

		$userInfo = ['role' => 1];
		$result = $this->_targetObject->applyUserInfo($userInfo);
		$this->assertTrue($result);

		$result = CakeSession::read('Auth.User');
		$expected = [
			'name' => 'Some User',
			'role' => 1
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testClearUserInfo method
 *
 * @return void
 */
	public function testClearUserInfo() {
		CakeSession::clear();
		$result = CakeSession::write('Auth.User', ['name' => 'Some user']);
		$this->assertTrue($result);

		$this->_targetObject->clearUserInfo();

		$result = CakeSession::read();
		$this->assertFalse($result);
	}

/**
 * testCheckFlashMessageExists method
 *
 * @return void
 */
	public function testCheckFlashMessageExists() {
		$result = CakeSession::write('Message.flash.message', 'Test message text for flash');
		$this->assertTrue($result);
		$this->_targetObject->checkFlashMessage($this, 'Test message text for flash', false, false);
	}

/**
 * testCheckFlashMessageNotExists method
 *
 * @return void
 */
	public function testCheckFlashMessageNotExists() {
		$result = CakeSession::write('Message.flash.message', 'Test message text for flash');
		$this->assertTrue($result);
		$this->_targetObject->checkFlashMessage($this, 'Test message text for flash Invert', false, true);
	}

/**
 * testCheckFlashMessagePcreExists method
 *
 * @return void
 */
	public function testCheckFlashMessagePcreExists() {
		$result = CakeSession::write('Message.flash.message', 'Some text... test');
		$this->assertTrue($result);
		$this->_targetObject->checkFlashMessage($this, '/^Some\stext.*/', true, false);
	}

/**
 * testCheckFlashMessagePcreNotExists method
 *
 * @return void
 */
	public function testCheckFlashMessagePcreNotExists() {
		$result = CakeSession::write('Message.flash.message', 'Some text... test');
		$this->assertTrue($result);
		$this->_targetObject->checkFlashMessage($this, '/^Some_invert_text*/', true, true);
	}

/**
 * testApplyTestConfig method
 *
 * @return void
 */
	public function testApplyTestConfig() {
		$path = App::pluginPath('CakeExtendTest') . 'Test' . DS . 'Config' . DS;
		$result = $this->_targetObject->applyTestConfig('BabPath');
		$this->assertFalse($result);

		$result = $this->_targetObject->applyTestConfig($path);
		$this->assertTrue($result);

		$result = Configure::read('TestKey');
		$expected = ['SomeKey' => 'Some data...'];
		$this->assertEquals($expected, $result);

		$result = Configure::write('TestKey.SomeKey', 'New data');
		$this->assertTrue($result);

		$result = $this->_targetObject->applyTestConfig($path);
		$this->assertTrue($result);

		$result = Configure::read('TestKey');
		$expected = ['SomeKey' => 'Some data...'];
		$this->assertEquals($expected, $result);
	}

/**
 * testStoreRestoreUIlang method
 *
 * @return void
 */
	public function testStoreRestoreUIlang() {
		Configure::write('Config.language', 'tst');
		$result = $this->_targetObject->storeUIlang();
		$expected = 'tst';
		$this->assertSame($expected, $result);

		$result = Configure::read('Config.language');
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'eng');
		$result = $this->_targetObject->restoreUIlang();
		$this->assertTrue($result);

		$result = Configure::read('Config.language');
		$expected = 'tst';
		$this->assertSame($expected, $result);
	}

/**
 * testSetEngLocale method
 *
 * @return void
 */
	public function testSetEngLocale() {
		$engLocale = 'en_US';
		if (DS === '\\') {
			$engLocale = 'english';
		}
		$result = $this->_targetObject->setEngLocale();
		$this->assertTrue($result);

		$result = setlocale(LC_ALL, 0);
		$this->assertContains($engLocale, $result, '', true);
	}

/**
 * testStoreRestoreLocale method
 *
 * @return void
 */
	public function testStoreRestoreLocale() {
		$engLocale = 'en_US';
		$rusLocale = 'ru_RU';
		if (DS === '\\') {
			$engLocale = 'english';
			$rusLocale = 'russian';
		}
		$this->skipIf(setlocale(LC_ALL, $engLocale) === false, "The English locale isn't available.");
		$result = $this->_targetObject->storeLocale();
		$this->assertContains($engLocale, $result, '', true);

		$result = setlocale(LC_ALL, 0);
		$this->assertContains($engLocale, $result, '', true);

		$this->skipIf(setlocale(LC_ALL, $rusLocale) === false, "The Russian locale isn't available.");
		$result = $this->_targetObject->restoreLocale();
		$this->assertTrue($result);

		$result = setlocale(LC_ALL, 0);
		$this->assertContains($engLocale, $result, '', true);
	}

/**
 * testSetRequestType method
 *
 * @return void
 */
	public function testSetRequestType() {
		if (isset($_SERVER['REQUEST_METHOD'])) {
			unset($_SERVER['REQUEST_METHOD']);
		}
		$this->_targetObject->setRequestType();
		$result = env('REQUEST_METHOD');
		$expected = 'POST';
		$this->assertEquals($expected, $result);

		$this->_targetObject->setRequestType('get');
		$result = env('REQUEST_METHOD');
		$expected = 'GET';
		$this->assertEquals($expected, $result);
	}

/**
 * testResetRequestType method
 *
 * @return void
 */
	public function testResetRequestType() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->_targetObject->resetRequestType();
		$this->assertFalse(isset($_SERVER['REQUEST_METHOD']));
	}

/**
 * testSetJsonRequest method
 *
 * @return void
 */
	public function testSetJsonRequest() {
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			unset($_SERVER['HTTP_ACCEPT']);
		}
		$this->_targetObject->setJsonRequest();
		$result = env('HTTP_ACCEPT');
		$expected = 'application/json';
		$this->assertEquals($expected, $result);

		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';
		$this->_targetObject->setJsonRequest();
		$result = env('HTTP_ACCEPT');
		$expected = 'application/json,text/html,application/xhtml+xml';
		$this->assertEquals($expected, $result);

		$this->_targetObject->setJsonRequest();
		$result = env('HTTP_ACCEPT');
		$expected = 'application/json,text/html,application/xhtml+xml';
		$this->assertEquals($expected, $result);
	}

/**
 * testResetJsonRequest method
 *
 * @return void
 */
	public function testResetJsonRequest() {
		$_SERVER['HTTP_ACCEPT'] = 'application/json';
		$this->_targetObject->resetJsonRequest();
		$this->assertEmpty($_SERVER['HTTP_ACCEPT']);

		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/json';
		$this->_targetObject->resetJsonRequest();
		$result = env('HTTP_ACCEPT');
		$expected = 'text/html,application/xhtml+xml';
		$this->assertEquals($expected, $result);
	}

/**
 * testSetAjaxRequest method
 *
 * @return void
 */
	public function testSetAjaxRequest() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			unset($_SERVER['HTTP_X_REQUESTED_WITH']);
		}
		$this->_targetObject->setAjaxRequest();
		$result = env('HTTP_X_REQUESTED_WITH');
		$expected = 'XMLHttpRequest';
		$this->assertEquals($expected, $result);
	}

/**
 * testResetAjaxRequest method
 *
 * @return void
 */
	public function testResetAjaxRequest() {
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->_targetObject->resetAjaxRequest();
		$this->assertFalse(isset($_SERVER['HTTP_X_REQUESTED_WITH']));
	}

/**
 * testSetPjaxRequest method
 *
 * @return void
 */
	public function testSetPjaxRequest() {
		if (isset($_SERVER['HTTP_X_PJAX'])) {
			unset($_SERVER['HTTP_X_PJAX']);
		}
		$this->_targetObject->setPjaxRequest();
		$result = env('HTTP_X_PJAX');
		$this->assertTrue($result);
	}

/**
 * testResetPjaxRequest method
 *
 * @return void
 */
	public function testResetPjaxRequest() {
		$_SERVER['HTTP_X_PJAX'] = true;
		$this->_targetObject->resetPjaxRequest();
		$this->assertFalse(isset($_SERVER['HTTP_X_PJAX']));
	}

/**
 * testSetGetData method
 *
 * @return void
 */
	public function testSetGetData() {
		$data = ['TST' => 'some value'];
		$this->_targetObject->setGetData($data);
		$result = $_GET;
		$this->assertEquals($result, $data);
	}

/**
 * testResetGetData method
 *
 * @return void
 */
	public function testResetGetData() {
		$_GET['TST'] = 'some value';
		$this->_targetObject->resetGetData();
		$this->assertTrue(empty($_GET));
	}

/**
 * testSetPostData method
 *
 * @return void
 */
	public function testSetPostData() {
		$data = ['TST' => 'some value'];
		$this->_targetObject->setPostData($data);
		$result = $_POST;
		$this->assertEquals($result, $data);
	}

/**
 * testResetPostData method
 *
 * @return void
 */
	public function testResetPostData() {
		$_POST['TST'] = 'some value';
		$this->_targetObject->resetPostData();
		$this->assertTrue(empty($_POST));
	}

/**
 * testAssertData method
 *
 * @return void
 */
	public function testAssertData() {
		$result = true;
		$expected = true;
		$this->_targetObject->assertData($this, $expected, $result);

		$result = 'Test text';
		$expected = 'Test text';
		$this->_targetObject->assertData($this, $expected, $result);

		$result = [
			'a' => 'test',
			'Some text'
		];
		$expected = [
			'a' => 'test',
			'Some text'
		];
		$this->_targetObject->assertData($this, $expected, $result);
	}

/**
 * testCreateProxyObject method
 *
 * @return void
 */
	public function testCreateProxyObject() {
		$target = new Test_Alt_Some_Class();
		$proxy = $this->_targetObject->createProxyObject($target);
		$this->assertTrue(is_object($proxy));
		$result = $proxy->_some_method();
		$expected = 'Some protected method';
		$this->assertEquals($expected, $result);
	}

/**
 * testGetNumberItemsByCssSelector method
 *
 * @return void
 */
	public function testGetNumberItemsByCssSelector() {
		$html = <<<EOD
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Test</title>		
	</head>
	<body>
	<div class="some_class">1</div>
	<div class="some_class">2</div>
	<div class="other_class">None</div>
	</body>
</html>
EOD;
		$result = $this->_targetObject->getNumberItemsByCssSelector('', 'div.some_class');
		$this->assertFalse($result);

		$result = $this->_targetObject->getNumberItemsByCssSelector($html, '');
		$this->assertFalse($result);

		$result = $this->_targetObject->getNumberItemsByCssSelector($html, 'div.some_class');
		$expected = 2;
		$this->assertEquals($expected, $result);

		$result = $this->_targetObject->getNumberItemsByCssSelector($html, 'span.some_class');
		$expected = 0;
		$this->assertEquals($expected, $result);
	}
}
