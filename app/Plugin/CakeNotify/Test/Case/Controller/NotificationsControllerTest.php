<?php
App::uses('AppControllerTestCase', 'CakeNotify.Test');
App::uses('NotificationsController', 'CakeNotify.Controller');
App::uses('CakeSession', 'Model/Datasource');

/**
 * NotificationsController Test Case
 */
class NotificationsControllerTest extends AppControllerTestCase {

	public $targetController = 'CakeNotify.Notifications';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_notify.notification',
	];

/**
 * Current session data.
 *
 * @var array
 */
	protected $_prevSessionData = null;

/**
 * The identifier for session data.
 *
 * @var string
 */
	protected $_keySessionData = 'Notifications';

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * Actions:
 * - Write session data.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		if (CakeSession::check($this->_keySessionData)) {
			$this->_prevSessionData = CakeSession::read($this->_keySessionData);
			CakeSession::delete($this->_keySessionData);
		}
	}

/**
 * teardown any static object changes and restore them.
 *
 * Actions:
 * - Restore session data;
 *
 * @return void
 */
	public function tearDown() {
		if (!empty($this->_prevSessionData)) {
			CakeSession::write($this->_keySessionData, $this->_prevSessionData);
		}

		unset($this->_prevSessionData);
		parent::tearDown();
	}

/**
 * testMessageGetNotSse method
 *
 * @return void
 */
	public function testMessageGetNotSse() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_notify/notifications/message', $opt);
	}

/**
 * testMessageGetInvalidUser method
 *
 * @return void
 */
	public function testMessageGetInvalidUser() {
		$retry = CAKE_NOTIFY_SSE_RETRY;
		$opt = [
			'method' => 'GET',
			'return' => 'view',
		];
		$data = [
			'result' => true,
			'messages' => [
				[
					'tag' => null,
					'title' => 'Test data',
					'body' => 'Test WO Tag',
					'icon' => '/favicon.ico',
					'data' => [
						'url' => '/trips/latest'
					]
				]
			],
			'retry' => $retry,
		];
		$this->generateMockedController([]);

		$result = $this->testAction('/cake_notify/notifications/message.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: webNotification\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}

/**
 * testMessageGetSuccess method
 *
 * @return void
 */
	public function testMessageGetSuccess() {
		$retry = CAKE_NOTIFY_SSE_RETRY;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'result' => true,
			'messages' => [
				[
					'tag' => null,
					'title' => 'Test data',
					'body' => 'Test WO Tag',
					'icon' => '/favicon.ico',
					'data' => [
						'url' => '/trips/latest'
					]
				],
				[
					'tag' => 'TestTag',
					'title' => 'Test',
					'body' => 'Test WO data for user',
					'icon' => '/favicon.ico',
					'data' => null
				],
				[
					'tag' => 'new_job',
					'title' => 'New job',
					'body' => 'Received a new job. For role.',
					'icon' => '/img/cake-icon.png',
					'data' => [
						'url' => '/jobs/latest'
					]
				],
			],
			'retry' => $retry,
		];
		$this->applyUserInfo();
		$this->generateMockedController();

		$result = $this->testAction('/cake_notify/notifications/message.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: webNotification\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);

		$result = $this->Controller->Session->read('Notifications.lastId');
		$expected = 4;
		$this->assertEquals($expected, $result);
	}

/**
 * testMessageGetLastIdSuccess method
 *
 * @return void
 */
	public function testMessageGetLastIdSuccess() {
		$retry = CAKE_NOTIFY_SSE_RETRY;
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$data = [
			'result' => true,
			'messages' => [
				[
					'tag' => 'new_job',
					'title' => 'New job',
					'body' => 'Received a new job. For role.',
					'icon' => '/img/cake-icon.png',
					'data' => [
						'url' => '/jobs/latest'
					]
				],
			],
			'retry' => $retry,
		];
		$this->applyUserInfo();
		$this->generateMockedController();
		$this->Controller->Session->write('Notifications.lastId', 2);

		$result = $this->testAction('/cake_notify/notifications/message.sse', $opt);
		header('Content-Type: text/html; charset=utf-8');
		$expected = "event: webNotification\nretry: " . $retry . "\ndata: " . json_encode($data) . "\n\n";
		$this->assertEquals($expected, $result);
	}
}
