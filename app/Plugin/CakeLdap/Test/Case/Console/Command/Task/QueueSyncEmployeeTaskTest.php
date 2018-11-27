<?php
/**
 * QueueSyncEmployeeTask Test Case
 *
 */
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Hash', 'Utility');
App::uses('QueueSyncEmployeeTask', 'CakeLdap.Console/Command/Task');

/**
 * QueueSyncEmployeeTaskTest class
 *
 */
class QueueSyncEmployeeTaskTest extends AppCakeTestCase {

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
			'QueueSyncEmployeeTask',
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
			'QueueSyncEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);

		$modelSubordinateDb = ClassRegistry::init('CakeLdap.SubordinateDb');
		$modelSubordinateDb->id = 4;
		$result = (bool)$modelSubordinateDb->saveField('rght', null);
		$this->assertTrue($result);

		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', null, null, 'sync');
		$capabilities = [
			'SyncEmployee' => [
				'name' => 'SyncEmployee',
				'timeout' => SYNC_EMPLOYEE_TIME_LIMIT,
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
		$resultMessages = [
			' * ' . __d('cake_ldap', 'Blocked') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Saved') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
		];
		$expected = __d('cake_ldap', 'Tree of employees is broken. Perform a restore.') . "\n" .
			__d('cake_ldap', 'Result of synchronization') . ' ' . __dx('cake_ldap', 'res_msg_type', 'employees') . "\n" . implode("\n", $resultMessages);
		$this->assertData($expected, $failureMessage);
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
			'QueueSyncEmployeeTask',
			['in', 'out', '_stop'],
			[$out, $out, $in]
		);

		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$modelEmployee->id = 2;
		$result = (bool)$modelEmployee->saveField(CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME, 'Test');
		$this->assertTrue($result);

		$modelDepartment = ClassRegistry::init('CakeLdap.DepartmentDb');
		$modelDepartment->id = 2;
		$result = (bool)$modelDepartment->saveField('value', 'Dept');
		$this->assertTrue($result);

		$modelOthertelephone = ClassRegistry::init('CakeLdap.OthertelephoneDb');
		$modelOthertelephone->id = 2;
		$result = (bool)$modelOthertelephone->saveField('value', '+375171000000');
		$this->assertTrue($result);

		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', null, null, 'sync');
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', null, null, 'sync');
		$capabilities = [
			'SyncEmployee' => [
				'name' => 'SyncEmployee',
				'timeout' => SYNC_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->expects($this->at(4))->method('out')->with(__d('cake_ldap', 'Found sync task in queue: %d. Skipped.', 1));
		$this->_targetObject->run($data, $id);
	}

/**
 * testRunEmptyGuid
 *
 * @return void
 */
	public function testRunEmptyGuid() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueSyncEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);

		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$modelEmployee->id = 2;
		$result = (bool)$modelEmployee->saveField(CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME, 'Test');
		$this->assertTrue($result);

		$modelDepartment = ClassRegistry::init('CakeLdap.DepartmentDb');
		$modelDepartment->id = 2;
		$result = (bool)$modelDepartment->saveField('value', 'Dept');
		$this->assertTrue($result);

		$modelOthertelephone = ClassRegistry::init('CakeLdap.OthertelephoneDb');
		$modelOthertelephone->id = 2;
		$result = (bool)$modelOthertelephone->saveField('value', '+375171000000');
		$this->assertTrue($result);

		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', null, null, 'sync');
		$capabilities = [
			'SyncEmployee' => [
				'name' => 'SyncEmployee',
				'timeout' => SYNC_EMPLOYEE_TIME_LIMIT,
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
		$resultMessagesDepartment = [
			' * ' . __d('cake_ldap', 'Blocked') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Saved') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
		];
		$resultMessagesEmployees = [
			' * ' . __d('cake_ldap', 'Blocked') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Deleted binded') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Saved') . ': 3 ' . __dn('cake_ldap', 'record', 'records', 3),
		];
		$expected = __d('cake_ldap', 'Result of synchronization') . ' ' . __dx('cake_ldap', 'res_msg_type', 'departments') . "\n" . implode("\n", $resultMessagesDepartment) . "\n" .
			__d('cake_ldap', 'Result of synchronization') . ' ' . __dx('cake_ldap', 'res_msg_type', 'employees') . "\n" . implode("\n", $resultMessagesEmployees);
		$this->assertData($expected, $failureMessage);
	}

/**
 * testRunNotEmptyGuid
 *
 * @return void
 */
	public function testRunNotEmptyGuid() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueSyncEmployeeTask',
			['in', 'out', '_stop'],
			[$out, $out, $in]
		);

		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$modelEmployee->id = 2;
		$result = (bool)$modelEmployee->saveField(CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME, 'Test');
		$this->assertTrue($result);

		$modelDepartment = ClassRegistry::init('CakeLdap.DepartmentDb');
		$modelDepartment->id = 2;
		$result = (bool)$modelDepartment->saveField('value', 'Dept');
		$this->assertTrue($result);

		$modelOthertelephone = ClassRegistry::init('CakeLdap.OthertelephoneDb');
		$modelOthertelephone->id = 2;
		$result = (bool)$modelOthertelephone->saveField('value', '+375171000000');
		$this->assertTrue($result);

		$taskParam = ['guid' => '0010b7b8-d69a-4365-81ca-5f975584fe5c'];
		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', $taskParam, null, 'sync');
		$this->_targetObject->QueuedTask->createJob('SyncEmployee', $taskParam, null, 'sync');
		$capabilities = [
			'SyncEmployee' => [
				'name' => 'SyncEmployee',
				'timeout' => SYNC_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->expects($this->any())->method('out')->with(new PHPUnit_Framework_Constraint_Not(__d('cake_ldap', 'Found sync task in queue: %d. Skipped.', 1)));
		$this->_targetObject->run($data, $id);
		$taskInfo = $this->_targetObject->QueuedTask->read(null, $id);
		$this->assertTrue(is_array($taskInfo));

		$progress = Hash::get($taskInfo, 'QueuedTask.progress');
		$expected = '1';
		$this->assertData($expected, $progress);

		$failureMessage = Hash::get($taskInfo, 'QueuedTask.failure_message');
		$resultMessagesDepartment = [
			' * ' . __d('cake_ldap', 'Blocked') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Saved') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
		];
		$resultMessagesEmployees = [
			' * ' . __d('cake_ldap', 'Deleted binded') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
			' * ' . __d('cake_ldap', 'Saved') . ': 1 ' . __dn('cake_ldap', 'record', 'records', 1),
		];
		$expected = __d('cake_ldap', 'Result of synchronization') . ' ' . __dx('cake_ldap', 'res_msg_type', 'departments') . "\n" . implode("\n", $resultMessagesDepartment) . "\n" .
			__d('cake_ldap', 'Result of synchronization') . ' ' . __dx('cake_ldap', 'res_msg_type', 'employees') . "\n" . implode("\n", $resultMessagesEmployees);
		$this->assertData($expected, $failureMessage);
	}
}
