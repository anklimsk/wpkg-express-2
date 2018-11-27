<?php
/**
 * SyncTask Test Case
 *
 */
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('SyncTask', 'CakeLdap.Console/Command/Task');

/**
 * SyncTaskTest class
 *
 */
class SyncTaskTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee',
		'plugin.cake_ldap.employee_ldap',
		'plugin.cake_ldap.othermobile',
		'plugin.cake_ldap.othertelephone',
		'plugin.cake_ldap.subordinate',
		'plugin.queue.queued_task'
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
			'SyncTask',
			['in', 'out', 'err', 'hr', '_stop'],
			[$out, $out, $in]
		);
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
 * testExecute
 *
 * @return void
 */
	public function testExecute() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'SyncTask',
			['in', 'out', '_stop'],
			[$out, $out, $in]
		);

		$this->_targetObject->expects($this->at(1))->method('out')->with(__d('cake_ldap', 'Synchronization with LDAP server set in queue successfully.'));
		$this->_targetObject->execute();
	}
}
