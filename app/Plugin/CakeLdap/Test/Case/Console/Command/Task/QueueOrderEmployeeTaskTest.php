<?php
/**
 * QueueOrderEmployeeTask Test Case
 *
 */
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Hash', 'Utility');
App::uses('QueueOrderEmployeeTask', 'CakeLdap.Console/Command/Task');

/**
 * QueueOrderEmployeeTaskTest class
 *
 */
class QueueOrderEmployeeTaskTest extends AppCakeTestCase {

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
			'QueueOrderEmployeeTask',
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
			'QueueOrderEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);

		$this->_targetObject->SubordinateDb->id = 4;
		$result = (bool)$this->_targetObject->SubordinateDb->saveField('rght', null);
		$this->assertTrue($result);

		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('OrderEmployee', null, null, 'order');
		$capabilities = [
			'OrderEmployee' => [
				'name' => 'OrderEmployee',
				'timeout' => REORDER_TREE_EMPLOYEE_TIME_LIMIT,
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
		$expected = __d('cake_ldap', 'Tree of employees is broken. Perform a restore.');
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
			'QueueOrderEmployeeTask',
			['in', 'out', '_stop'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('OrderEmployee', null, null, 'order');
		$this->_targetObject->QueuedTask->createJob('OrderEmployee', null, null, 'order');
		$capabilities = [
			'OrderEmployee' => [
				'name' => 'OrderEmployee',
				'timeout' => REORDER_TREE_EMPLOYEE_TIME_LIMIT,
				'retries' => 2,
			]
		];
		$jobInfo = $this->_targetObject->QueuedTask->requestJob($capabilities);
		$id = $jobInfo['id'];
		$data = unserialize($jobInfo['data']);
		$this->_targetObject->expects($this->at(4))->method('out')->with(__d('cake_ldap', 'Found order task in queue: %d. Skipped.', 1));
		$this->_targetObject->run($data, $id);
	}

/**
 * testRun
 *
 * @return void
 */
	public function testRun() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'QueueOrderEmployeeTask',
			['in', '_stop'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
		$this->_targetObject->QueuedTask->initConfig();
		$this->_targetObject->QueuedTask->createJob('OrderEmployee', null, null, 'order');
		$capabilities = [
			'OrderEmployee' => [
				'name' => 'OrderEmployee',
				'timeout' => REORDER_TREE_EMPLOYEE_TIME_LIMIT,
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

		$result = $this->_targetObject->SubordinateDb->generateTreeList();
		$expected = [
			8 => 'Голубев Е.В.',
			4 => '_Дементьева А.С.',
			7 => '__Хвощинский В.В.',
			6 => '___Козловская Е.М.',
			9 => 'Марчук А.М.',
			5 => 'Матвеев Р.М.',
			1 => 'Миронов В.М.',
			3 => 'Суханова Л.Б.',
			2 => '_Егоров Т.Г.',
			10 => 'Чижов Я.С.',
		];
		$this->assertData($expected, $result);
	}
}
