<?php
App::uses('AppCakeTestCase', 'CakeNotify.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('CronShell', 'CakeNotify.Console/Command');
App::uses('CakeText', 'Utility');

/**
 * CronShell Test Case
 *
 */
class CronShellTest extends AppCakeTestCase {

/**
 * setup test
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);

		$this->_targetObject = $this->getMock(
			'CronShell',
			['in', 'out', 'hr', 'err', 'createFile', '_stop', '_checkUnitTest'],
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
 * testMain method
 *
 * @return void
 */
	public function testMain() {
		$this->_targetObject->expects($this->at(3))->method('out')
			->with($this->stringContains(CakeText::toList(constsVals('CAKE_NOTIFY_CRON_TASK_'), __d('cake_notify', 'and'))));
		$this->_targetObject->main();
	}
}
