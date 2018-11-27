<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('CronShell', 'CakeLdap.Console/Command');
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
			->with($this->stringContains(CakeText::toList(constsVals('CAKE_LDAP_SHELL_CRON_TASK_'), __d('cake_ldap', 'and'))));
		$this->_targetObject->main();
	}
}
