<?php
/**
 * QueueRecoveryEmployeeTask Test Case
 *
 */
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Hash', 'Utility');
App::uses('QueueRecoveryEmployeeTask', 'CakeLdap.Console/Command/Task');

/**
 * QueueRecoveryEmployeeTaskTest class
 *
 */
class QueueRecoveryEmployeeTaskTest extends AppCakeTestCase {

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
			'QueueRecoveryEmployeeTask',
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
 * testRunTreeBroken
 *
 * @return void
 */
	public function testRunTreeBroken() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueRecoveryEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);

		$this->_targetObject->SubordinateDb->id = 4;
		$result = (bool)$this->_targetObject->SubordinateDb->saveField('rght', null);
		$this->assertTrue($result);

		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('RecoveryEmployee', null, null, 'recovery');
		$capabilities = [
			'RecoveryEmployee' => [
				'name' => 'RecoveryEmployee',
				'timeout' => RECOVER_TREE_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->run($data, $id);
		$taskInfo = $this->_targetObject->QueuedTask->read(null, $id);
		$this->assertTrue(is_array($taskInfo));

		$progress = Hash::get($taskInfo, 'QueuedTask.progress');
		$expected = '1';
		$this->assertData($expected, $progress);

		$failureMessage = Hash::get($taskInfo, 'QueuedTask.failure_message');
		$expected = null;
		$this->assertData($expected, $failureMessage);

		$result = $this->_targetObject->SubordinateDb->verify();
		$this->assertTrue($result);
	}

/**
 * testRunLengthQueue
 *
 * @return void
 */
	public function testRunLengthQueue() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueRecoveryEmployeeTask',
			['in', 'out', '_stop'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('RecoveryEmployee', null, null, 'recovery');
		$this->_targetObject->QueuedTask->createJob('RecoveryEmployee', null, null, 'recovery');
		$capabilities = [
			'RecoveryEmployee' => [
				'name' => 'RecoveryEmployee',
				'timeout' => RECOVER_TREE_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->expects($this->at(4))->method('out')->with(__d('cake_ldap', 'Found recovery task in queue: %d. Skipped.', 1));
		$this->_targetObject->run($data, $id);
	}

/**
 * testRunRecoveryNotRequired
 *
 * @return void
 */
	public function testRunRecoveryNotRequired() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueRecoveryEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('RecoveryEmployee', null, null, 'recovery');
		$capabilities = [
			'RecoveryEmployee' => [
				'name' => 'RecoveryEmployee',
				'timeout' => RECOVER_TREE_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->run($data, $id);
		$taskInfo = $this->_targetObject->QueuedTask->read(null, $id);
		$this->assertTrue(is_array($taskInfo));

		$progress = Hash::get($taskInfo, 'QueuedTask.progress');
		$expected = '0';
		$this->assertData($expected, $progress);

		$failureMessage = Hash::get($taskInfo, 'QueuedTask.failure_message');
		$expected = __d('cake_ldap', 'The recovery tree of employees is not required');
		$this->assertData($expected, $failureMessage);
	}
}
