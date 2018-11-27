<?php
App::uses('AppCakeTestCase', 'CakeConfigPlugin.Test');
App::uses('InitConfigBehavior', 'CakeConfigPlugin.Model/Behavior');

/**
 * InBehavior Test Case
 */
class InitConfigBehaviorTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('Test');
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
 * testInitConfigWithoutParams method
 *
 * @return void
 */
	public function testInitConfigWithoutParams() {
		$this->setExpectedException('InternalErrorException');
		$this->_targetObject->Behaviors->load('CakeConfigPlugin.InitConfig');
	}

/**
 * testInitConfigCheckPathSuccess method
 *
 * @return void
 */
	public function testInitConfigSuccess() {
		Configure::delete('CakeConfigPlugin');
		$path = CakePlugin::path('CakeConfigPlugin') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$this->_targetObject->Behaviors->load('CakeConfigPlugin.InitConfig', ['pluginName' => 'CakeConfigPlugin', 'path' => $path]);
		$result = Configure::read('CakeConfigPlugin');
		$expected = [
			'SomeParam' => 'val',
			'boolParam' => false
		];
		$this->assertData($expected, $result);
	}

/**
 * testInitConfigCheckPathSuccess method
 *
 * @return void
 */
	public function testInitConfigCheckPathSuccess() {
		$path = CakePlugin::path('CakeConfigPlugin') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$this->_targetObject->Behaviors->load('CakeConfigPlugin.InitConfig', ['pluginName' => 'CakeConfigPlugin', 'path' => $path]);
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
		$path = CakePlugin::path('CakeConfigPlugin') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$this->_targetObject->Behaviors->load('CakeConfigPlugin.InitConfig', ['pluginName' => 'CakeConfigPlugin', 'path' => $path]);
		Configure::write('CakeConfigPlugin.param', 'newValue');
		$this->_targetObject->initConfig(true);
		$result = Configure::read('CakeConfigPlugin');
		$expected = [
			'param' => 'newValue',
			'boolParam' => true,
			'SomeParam' => 'val'
		];
		$this->assertData($expected, $result);
	}
}
