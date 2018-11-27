<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ExtendQueuedTask', 'CakeTheme.Model');

/**
 * ExtendQueuedTask Test Case
 */
class ExtendQueuedTaskTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_theme.queued_task'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$this->skipIf(!CakePlugin::loaded('Queue'), "Plugin 'Queue' is not loaded");

		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
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
 * testGetLengthQueue method
 *
 * @return void
 */
	public function testGetLengthQueue() {
		$params = [
			[
				'', // $jobType
			],
			[
				'Test', // $jobType
			],
			[
				'Sync', // $jobType
			],
		];
		$expected = [
			1,
			0,
			1
		];
		$this->runClassMethodGroup('getLengthQueue', $params, $expected);
	}

/**
 * testGetPendingJob method
 *
 * @return void
 */
	public function testGetPendingJob() {
		$timestamp = mktime(0, 0, 0, 9, 1, 2016);
		$params = [
			[
				'', // $jobType
				false, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Test', // $jobType
				false, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Parse', // $jobType
				true, // $includeFinished
				null, // $timestamp
			],
			[
				'Sync', // $jobType
				false, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Build', // $jobType
				false, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Build', // $jobType
				true, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Parse', // $jobType
				true, // $includeFinished
				$timestamp, // $timestamp
			],
			[
				'Parse', // $jobType
				true, // $includeFinished
				mktime(12, 0, 0, 10, 24, 2016), // $timestamp
			],
		];
		$expected = [
			false,
			[],
			[],
			[
				'ExtendQueuedTask' => [
					'jobtype' => 'Sync',
					'created' => '2016-09-26 08:34:15',
					'fetched' => null,
					'progress' => null,
					'completed' => null,
					'reference' => null,
					'failed' => '0',
					'failure_message' => null,
					'status' => 'NOT_STARTED',
					'age' => '0',
				]
			],
			[],
			[
				'ExtendQueuedTask' => [
					'jobtype' => 'Build',
					'created' => '2016-10-21 11:17:25',
					'fetched' => '2016-10-21 11:18:09',
					'progress' => '0.75',
					'completed' => null,
					'reference' => null,
					'failed' => '2',
					'failure_message' => 'Error',
					'status' => 'FAILED',
					'age' => '0',
				]
			],
			[
				'ExtendQueuedTask' => [
					'jobtype' => 'Parse',
					'created' => '2016-10-24 08:47:01',
					'fetched' => '2016-10-24 08:47:14',
					'progress' => '1.00',
					'completed' => '2016-10-24 08:47:29',
					'reference' => null,
					'failed' => '0',
					'failure_message' => null,
					'status' => 'COMPLETED',
					'age' => '0',
				]
			],
			[],
		];
		$this->runClassMethodGroup('getPendingJob', $params, $expected);
	}

/**
 * testUpdateMessage method
 *
 * @return void
 */
	public function testUpdateMessage() {
		$params = [
			[
				'', // $id
				'Test', // $message
			],
			[
				'6', // $id
				'', // $message
			],
			[
				'3', // $id
				'Some text', // $message
			],
		];
		$expected = [
			false,
			false,
			true
		];
		$this->runClassMethodGroup('updateMessage', $params, $expected);
	}

/**
 * testUpdateTaskProgress method
 *
 * @return void
 */
	public function testUpdateTaskProgress() {
		$step = 2;
		$params = [
			[
				'', // $id
				&$step, // $step
				3, // $maxStep
			],
			[
				'6', // $id
				&$step, // $step
				3, // $maxStep
			],
			[
				'3', // $id
				&$step, // $step
				3, // $maxStep
			],
			[
				'3', // $id
				&$step, // $step
				1, // $maxStep
			],
		];
		$expected = [
			false,
			false,
			true,
			false,
		];
		$this->runClassMethodGroup('updateTaskProgress', $params, $expected);

		$step = 3;
		$result = $this->_targetObject->updateTaskProgress(3, $step, 5);
		$this->assertTrue($result);
		if ($result) {
			$this->assertEquals(4, $step);
		}

		$step = 2;
		$result = $this->_targetObject->updateTaskProgress(7, $step, 4);
		$this->assertFalse($result);
		if (!$result) {
			$this->assertEquals(2, $step);
		}
	}

/**
 * testUpdateTaskErrorMessage method
 *
 * @return void
 */
	public function testUpdateTaskErrorMessage() {
		$params = [
			[
				'', // $id
				'Test', // $errorMessage
				false, // $keepExistingMessage
			],
			[
				'8', // $id
				'', // $errorMessage
				false, // $keepExistingMessage
			],
			[
				'3', // $id
				'Some text of error', // $errorMessage
				false, // $keepExistingMessage
			],
		];
		$expected = [
			false,
			false,
			true
		];
		$this->runClassMethodGroup('updateTaskErrorMessage', $params, $expected);
	}

/**
 * testUpdateTaskErrorMessageKeepExistingMessage method
 *
 * @return void
 */
	public function testUpdateTaskErrorMessageKeepExistingMessage() {
		$result = $this->_targetObject->updateTaskErrorMessage('3', 'Some text of error', true);
		$this->assertTrue($result);

		$result = $this->_targetObject->updateTaskErrorMessage('3', 'New text of error', true);
		$this->assertTrue($result);

		$result = $this->_targetObject->field('failure_message');
		$expected = "Error\nSome text of error\nNew text of error";
		$this->assertData($expected, $result);
	}

/**
 * testDeleteTasks method
 *
 * @return void
 */
	public function testDeleteTasks() {
		$params = [
			[
				null, // $data
			],
			[
				'5', // $data
			],
			[
				[
					'jobtype' => 'Bad type',
					'fetched' => '2016-01-01 10:00:01',
					'progress' => '0',
					'failed' => '1',
					'failure_message' => 'Some message',
				], // $data
			],
			[
				[
					'jobtype' => 'Build',
					'created' => '2016-10-21 11:17:25',
					'fetched' => '2016-10-21 11:18:09',
					'progress' => '0.75',
					'completed' => '',
					'failed' => '2',
					'failure_message' => 'Error',
					'workerkey' => '09adcca25a14ae45c9c22cbbd00064eadf568bc1',
					'data' => ''
				], // $data
			],
		];
		$expected = [
			false,
			false,
			false,
			true
		];
		$this->runClassMethodGroup('deleteTasks', $params, $expected);
	}
}
