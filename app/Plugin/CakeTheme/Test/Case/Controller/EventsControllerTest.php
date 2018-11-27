<?php
App::uses('AppControllerTestCase', 'CakeTheme.Test');
App::uses('EventsController', 'CakeTheme.Controller');

/**
 * EventsController Test Case
 */
class EventsControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeTheme.Events';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_theme.queued_task',
	];

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * Actions:
 * - Checking Plugin 'Queue' is loaded.
 *
 * @return void
 */
	public function setUp() {
		$this->skipIf(!CakePlugin::loaded('Queue'), "Plugin 'Queue' is not loaded");

		parent::setUp();
	}

/**
 * testSeecfgGet method
 *
 * @return void
 */
	public function testSeecfgGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/events/ssecfg.json', $opt);
	}

/**
 * testSeecfgNotAjaxPost method
 *
 * @return void
 */
	public function testSeecfgNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/events/ssecfg', $opt);
	}

/**
 * testSeecfgPost method
 *
 * @return void
 */
	public function testSeecfgPost() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/events/ssecfg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'text' => 'Waiting to run task',
			'label' => [
				'task' => 'Task',
				'completed' => 'completed'
			],
			'retries' => 5,
			'delayDeleteTask' => 5
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testTasksGet method
 *
 * @return void
 */
	public function testTasksGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/events/tasks.json', $opt);
	}

/**
 * testTasksNotAjaxPost method
 *
 * @return void
 */
	public function testTasksNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/events/tasks', $opt);
	}

/**
 * testTasksPostEmptyData method
 *
 * @return void
 */
	public function testTasksPostEmptyData() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST',
			'return' => 'contents'
		];
		$tasks = [
			'SomeTask',
			'TestTask'
		];
		CakeSession::write('SSE.progress', $tasks);
		$result = $this->testAction('/cake_theme/events/tasks.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'result' => true,
			'tasks' => $tasks
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
		CakeSession::delete('SSE.progress');
	}

/**
 * testTasksPostInvalidDataDelete method
 *
 * @return void
 */
	public function testTasksPostInvalidDataDelete() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'tasks' => 'SomeTask',
				'delete' => 1
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$tasks = [
			'SomeTask',
			'TestTask'
		];
		CakeSession::write('SSE.progress', $tasks);
		$result = $this->testAction('/cake_theme/events/tasks.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'result' => false,
			'tasks' => []
		];
		$this->assertData($expected, $result);

		$result = CakeSession::read('SSE.progress', $tasks);
		$expected = ['SomeTask', 'TestTask'];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
		CakeSession::delete('SSE.progress');
	}

/**
 * testTasksPostValidDataDelete method
 *
 * @return void
 */
	public function testTasksPostValidDataDelete() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'tasks' => ['SomeTask'],
				'delete' => 1
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$tasks = [
			'SomeTask',
			'TestTask'
		];
		CakeSession::write('SSE.progress', $tasks);
		$result = $this->testAction('/cake_theme/events/tasks.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'result' => true,
			'tasks' => []
		];
		$this->assertData($expected, $result);

		$result = CakeSession::read('SSE.progress', $tasks);
		$expected = ['TestTask'];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
		CakeSession::delete('SSE.progress');
	}

/**
 * testQueueGetNotSse method
 *
 * @return void
 */
	public function testQueueGetNotSse() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/events/queue', $opt);
	}

/**
 * testQueueGetEmptyType method
 *
 * @return void
 */
	public function testQueueGetEmptyType() {
		$this->_generateMockedController(false);
		$retry = 3000;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'type' => '',
			'progress' => 0,
			'msg' => '',
			'result' => null
		];

		$result = $this->testAction('/cake_theme/events/queue.sse', $opt);
		$expected = "event: progressBar\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * testQueueGetInvalidType method
 *
 * @return void
 */
	public function testQueueGetInvalidType() {
		$this->_generateMockedController([]);
		$type = 'test';
		$retry = 3000;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'type' => $type,
			'progress' => 0,
			'msg' => '',
			'result' => false
		];

		$result = $this->testAction('/cake_theme/events/queue/' . $type . '.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: progressBar\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * testQueueGetValidTypeProgress method
 *
 * @return void
 */
	public function testQueueGetValidTypeProgress() {
		$type = 'Sync';
		$jobInfo = [
			'ExtendQueuedTask' => [
				'jobtype' => $type,
				'created' => '2016-09-26 08:30:17',
				'fetched' => '2016-09-26 08:31:01',
				'progress' => '0.85',
				'completed' => null,
				'reference' => null,
				'failed' => '0',
				'failure_message' => 'Some text',
				'status' => 'IN_PROGRESS',
			]
		];
		$this->_generateMockedController($jobInfo);
		$retry = 1000;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'type' => $type,
			'progress' => 0.85,
			'msg' => 'Some text',
			'result' => null
		];

		$result = $this->testAction('/cake_theme/events/queue/' . $type . '/' . $retry . '.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: progressBar\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * testQueue method
 *
 * @return void
 */
	public function testQueueGetValidTypeComplete() {
		$type = 'parse';
		$jobInfo = [
			'ExtendQueuedTask' => [
				'jobtype' => $type,
				'created' => '2016-10-24 08:47:01',
				'fetched' => '2016-10-24 08:47:14',
				'progress' => '1',
				'completed' => '2016-10-24 08:47:29',
				'reference' => null,
				'failed' => '0',
				'failure_message' => null,
				'status' => 'COMPLETED',
			]
		];
		$this->_generateMockedController($jobInfo);
		$retry = 1000;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'type' => $type,
			'progress' => 1,
			'msg' => '',
			'result' => true
		];

		$result = $this->testAction('/cake_theme/events/queue/' . $type . '/' . $retry . '.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: progressBar\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * testQueue method
 *
 * @return void
 */
	public function testQueueGetValidTypeFailed() {
		$type = 'Build';
		$jobInfo = [
			'ExtendQueuedTask' => [
				'jobtype' => $type,
				'created' => '2016-10-21 11:17:25',
				'fetched' => '2016-10-21 11:18:09',
				'progress' => '0.75',
				'completed' => null,
				'reference' => null,
				'failed' => '2',
				'failure_message' => 'Error',
				'status' => 'FAILED',
			]
		];
		$this->_generateMockedController($jobInfo);
		$retry = 1000;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'type' => $type,
			'progress' => 0.75,
			'msg' => 'Error',
			'result' => false
		];

		$result = $this->testAction('/cake_theme/events/queue/' . $type . '/' . $retry . '.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: progressBar\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * Generate mocked EventsController.
 *
 * @param array|bool $jobInfo Statistics about job.
 * @return bool Success
 */
	protected function _generateMockedController($jobInfo = []) {
		$mocks = [
			'models' => [
				'CakeTheme.ExtendQueuedTask' => [
					'getPendingJob',
				]
			],
			'components' => [
				'Auth',
				'Security',
			],
		];
		if (!$this->generateMockedController($mocks)) {
			return false;
		}

		$this->Controller->ExtendQueuedTask->expects($this->any())
			->method('getPendingJob')
			->will($this->returnValue($jobInfo));

		return true;
	}
}
