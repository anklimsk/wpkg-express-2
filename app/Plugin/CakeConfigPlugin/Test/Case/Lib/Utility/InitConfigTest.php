<?php
App::uses('AppCakeTestCase', 'CakeConfigPlugin.Test');
App::uses('InitConfig', 'CakeConfigPlugin.Utility');

/**
 * InitConfigTest Test Case
 */
class InitConfigTest extends AppCakeTestCase {

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
		parent::tearDown();
	}

/**
 * testGetConfigBadKey method
 *
 * @return void
 */
	public function testGetConfigBadKey() {
		$target = new InitConfig('BadKey');
		$result = $target->getConfig();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetConfigSuccess method
 *
 * @return void
 */
	public function testGetConfigSuccess() {
		$target = new InitConfig('CakeConfigPlugin');
		$result = $target->getConfig();
		$expected = [
			'param' => 'someValue',
			'boolParam' => true
		];
		$this->assertData($expected, $result);
	}

/**
 * testInitConfigWithoutParams method
 *
 * @return void
 */
	public function testInitConfigWithoutParams() {
		$this->setExpectedException('InternalErrorException');
		$target = new InitConfig();
	}

/**
 * testInitConfigCheckPathSuccess method
 *
 * @return void
 */
	public function testInitConfigCheckPathSuccess() {
		$target = new InitConfig('CakeConfigPlugin');
		$target->path = CakePlugin::path('CakeConfigPlugin') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$target->initConfig(false);
		$result = Configure::read('CakeConfigPlugin');
		$expected = [
			'param' => 'someValue',
			'boolParam' => true,
		];
		$this->assertData($expected, $result);
	}

/**
 * testInitConfigOverwriteSuccess method
 *
 * @return void
 */
	public function testInitConfigOverwriteSuccess() {
		$target = new InitConfig('CakeConfigPlugin');
		$target->path = CakePlugin::path('CakeConfigPlugin') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		Configure::write('CakeConfigPlugin.param', 'newValue');
		$target->initConfig(true);
		$result = Configure::read('CakeConfigPlugin');
		$expected = [
			'param' => 'newValue',
			'boolParam' => true,
			'SomeParam' => 'val',
		];
		$this->assertData($expected, $result);
	}
}
