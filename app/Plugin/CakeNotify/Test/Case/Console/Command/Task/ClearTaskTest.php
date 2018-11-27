<?php
/**
 * ClearTask Test Case
 *
 */

App::uses('AppCakeTestCase', 'CakeNotify.Test');
App::uses('ShellDispatcher', 'Console');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('ClearTask', 'CakeNotify.Console/Command/Task');

/**
 * DbConfigTest class
 *
 */
class ClearTaskTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_notify.notification'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);

		$this->_targetObject = $this->getMock(
			'ClearTask',
			['in', 'out', 'err', 'hr', 'createFile', '_stop', '_checkUnitTest', '_verify'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
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
 * test execute and by extension _interactive
 *
 * @return void
 */
	public function testExecuteIntoInteractiveDefault() {
		$id = 6;
		$result = $this->_targetObject->Notification->exists($id);
		$this->assertTrue($result);

		$this->_targetObject->expects($this->at(1))->method('out')
			->with($this->stringContains(__d('cake_notify', 'Notifications clear successfully.')));

		$this->_targetObject->execute();

		$result = $this->_targetObject->Notification->exists($id);
		$this->assertFalse($result);
	}
}
