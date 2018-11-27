<?php
App::uses('AppControllerTestCase', 'CakeSettingsApp.Test');
App::uses('QueuesController', 'CakeSettingsApp.Controller');
App::uses('Model', 'Model');

/**
 * QueuesController Test Case
 *
 */
class QueuesControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeSettingsApp.Queues';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_settings_app.queued_task',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->skipIf(!CakePlugin::loaded('Queue'), "Plugin 'Queue' is not loaded");
	}

/**
 * testQueueGet method
 *
 * Method: GET
 *
 * @return void
 */
	public function testQueueGet() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/cake_settings_app/queues/index', $opt);
		$expected = [
			'queue' => [
				[
					'QueueInfo' => [
						'id' => '2',
						'jobtype' => 'Sync',
						'created' => '2016-09-26 08:28:15',
						'fetched' => null,
						'progress' => null,
						'completed' => null,
						'reference' => null,
						'failed' => '0',
						'failure_message' => null,
						'status' => 'NOT_STARTED'
					]
				],
				[
					'QueueInfo' => [
						'id' => '1',
						'jobtype' => 'Sync',
						'created' => '2016-09-26 08:27:17',
						'fetched' => '2016-09-26 08:28:01',
						'progress' => '1.00',
						'completed' => '2016-09-26 08:28:05',
						'reference' => null,
						'failed' => '0',
						'failure_message' => null,
						'status' => 'COMPLETED',
					]
				]
			],
			'groupActions' => [
				'group-data-del' => __d('cake_settings_app', 'Delete selected tasks')
			],
			'taskStateList' => [
				'NOT_READY' => __d('cake_settings_app', 'Not ready'),
				'NOT_STARTED' => __d('cake_settings_app', 'Not started'),
				'IN_PROGRESS' => __d('cake_settings_app', 'In progress'),
				'COMPLETED' => __d('cake_settings_app', 'Completed'),
				'FAILED' => __d('cake_settings_app', 'Failed'),
				'UNKNOWN' => __d('cake_settings_app', 'Unknown'),
			],
			'stateData' => [
				[
					'stateName' => __d('cake_settings_app', 'Completed'),
					'stateId' => 'COMPLETED',
					'amount' => 1,
					'stateUrl' => [
						'controller' => 'queues',
						'action' => 'index',
						'plugin' => 'cake_settings_app',
						'?' => [
							'data[FilterData][0][QueueInfo][status]' => 'COMPLETED',
						]
					],
					'class' => 'progress-bar-success'
				],
				[
					'stateName' => __d('cake_settings_app', 'Not started'),
					'stateId' => 'NOT_STARTED',
					'amount' => 1,
					'stateUrl' => [
						'controller' => 'queues',
						'action' => 'index',
						'plugin' => 'cake_settings_app',
						'?' => [
							'data[FilterData][0][QueueInfo][status]' => 'NOT_STARTED',
						]
					],
					'class' => 'progress-bar-success progress-bar-striped',
				]
			],
			'usePost' => true,
			'pageHeader' => __d('cake_settings_app', 'Queue of tasks'),
			'headerMenuActions' => [
				[
					'fas fa-trash-alt',
					__d('cake_settings_app', 'Clear queue of tasks'),
					['controller' => 'queues', 'action' => 'clear', 'plugin' => 'cake_settings_app', 'prefix' => false],
					[
						'title' => __d('cake_settings_app', 'Clear queue of tasks'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_settings_app', 'Are you sure you wish to clear queue of tasks?'),
					]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_settings_app', 'Application settings'),
					[
						'plugin' => 'cake_settings_app',
						'controller' => 'settings',
						'action' => 'index'
					],
				],
				__d('cake_settings_app', 'Queue of tasks')
			],
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng'
		];
		$this->assertData($expected, $result);
	}

/**
 * testQueueGetUseFilter method
 *
 * Method: GET
 *
 * @return void
 */
	public function testQueueGetUseFilter() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$data = http_build_query(
			[
				'data' => [
					'FilterData' => [
						[
							'QueueInfo' => [
								'id' => [2]
							]
						]
					],
				]
			]
		);
		$result = $this->testAction('/cake_settings_app/queues/index?' . $data, $opt);
		$expected = [
			'queue' => [
				[
					'QueueInfo' => [
						'id' => '2',
						'jobtype' => 'Sync',
						'created' => '2016-09-26 08:28:15',
						'fetched' => null,
						'progress' => null,
						'completed' => null,
						'reference' => null,
						'failed' => '0',
						'failure_message' => null,
						'status' => 'NOT_STARTED'
					]
				],
			],
			'groupActions' => [
				'group-data-del' => __d('cake_settings_app', 'Delete selected tasks')
			],
			'taskStateList' => [
				'NOT_READY' => __d('cake_settings_app', 'Not ready'),
				'NOT_STARTED' => __d('cake_settings_app', 'Not started'),
				'IN_PROGRESS' => __d('cake_settings_app', 'In progress'),
				'COMPLETED' => __d('cake_settings_app', 'Completed'),
				'FAILED' => __d('cake_settings_app', 'Failed'),
				'UNKNOWN' => __d('cake_settings_app', 'Unknown'),
			],
			'stateData' => [],
			'usePost' => false,
			'pageHeader' => __d('cake_settings_app', 'Queue of tasks'),
			'headerMenuActions' => [
				[
					'fas fa-trash-alt',
					__d('cake_settings_app', 'Clear queue of tasks'),
					['controller' => 'queues', 'action' => 'clear', 'plugin' => 'cake_settings_app', 'prefix' => false],
					[
						'title' => __d('cake_settings_app', 'Clear queue of tasks'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_settings_app', 'Are you sure you wish to clear queue of tasks?'),
					]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_settings_app', 'Application settings'),
					[
						'plugin' => 'cake_settings_app',
						'controller' => 'settings',
						'action' => 'index'
					],
				],
				__d('cake_settings_app', 'Queue of tasks')
			],
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng'
		];
		$this->assertData($expected, $result);
	}

/**
 * testQueuePostInvalid method
 *
 * Method: POST
 * Group process: invalid data
 *
 * @return void
 */
	public function testQueuePostInvalid() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
			'return' => 'vars',
			'data' => [
				'FilterData' => [
					[
						'QueueInfo' => [
							'id' => [2]
						]
					]
				],
				'FilterGroup' => [
					'action' => 'bad-action'
				]
			],
		];
		$this->checkFlashMessage(__d('cake_settings_app', 'Selected tasks has been deleted.'), false, true);
		$this->checkFlashMessage(__d('cake_settings_app', 'Selected tasks could not be deleted. Please, try again.'), false, true);
	}

/**
 * testQueuePostSuccessMsg method
 *
 * Method: POST
 * Group process: success
 *
 * @return void
 */
	public function testQueuePostSuccessMsg() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
			'data' => [
				'FilterData' => [
					[
						'QueueInfo' => [
							'id' => [2]
						]
					]
				],
				'FilterGroup' => [
					'action' => 'group-data-del'
				]
			],
		];
		$result = $this->testAction('/cake_settings_app/queues/index', $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'Selected tasks has been deleted.'));
	}

/**
 * testQueuePostSuccessVars method
 *
 * Method: POST
 * Group process: success
 *
 * @return void
 */
	public function testQueuePostSuccess() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
			'return' => 'vars',
			'data' => [
				'FilterData' => [
					[
						'QueueInfo' => [
							'id' => [2]
						]
					]
				],
				'FilterGroup' => [
					'action' => 'group-data-del'
				]
			],
		];
		$result = $this->testAction('/cake_settings_app/queues/index', $opt);
		$expected = [
			'queue' => [
				[
					'QueueInfo' => [
						'id' => '1',
						'jobtype' => 'Sync',
						'created' => '2016-09-26 08:27:17',
						'fetched' => '2016-09-26 08:28:01',
						'progress' => '1.00',
						'completed' => '2016-09-26 08:28:05',
						'reference' => null,
						'failed' => '0',
						'failure_message' => null,
						'status' => 'COMPLETED',
					]
				]
			],
			'groupActions' => [
				'group-data-del' => __d('cake_settings_app', 'Delete selected tasks')
			],
			'taskStateList' => [
				'NOT_READY' => __d('cake_settings_app', 'Not ready'),
				'NOT_STARTED' => __d('cake_settings_app', 'Not started'),
				'IN_PROGRESS' => __d('cake_settings_app', 'In progress'),
				'COMPLETED' => __d('cake_settings_app', 'Completed'),
				'FAILED' => __d('cake_settings_app', 'Failed'),
				'UNKNOWN' => __d('cake_settings_app', 'Unknown'),
			],
			'stateData' => [
				[
					'stateName' => __d('cake_settings_app', 'Completed'),
					'stateId' => 'COMPLETED',
					'amount' => 1,
					'stateUrl' => [
						'controller' => 'queues',
						'action' => 'index',
						'plugin' => 'cake_settings_app',
						'?' => [
							'data[FilterData][0][QueueInfo][status]' => 'COMPLETED',
						]
					],
					'class' => 'progress-bar-success'
				],
			],
			'usePost' => true,
			'pageHeader' => __d('cake_settings_app', 'Queue of tasks'),
			'headerMenuActions' => [
				[
					'fas fa-trash-alt',
					__d('cake_settings_app', 'Clear queue of tasks'),
					['controller' => 'queues', 'action' => 'clear', 'plugin' => 'cake_settings_app', 'prefix' => false],
					[
						'title' => __d('cake_settings_app', 'Clear queue of tasks'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_settings_app', 'Are you sure you wish to clear queue of tasks?'),
					]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_settings_app', 'Application settings'),
					[
						'plugin' => 'cake_settings_app',
						'controller' => 'settings',
						'action' => 'index'
					],
				],
				__d('cake_settings_app', 'Queue of tasks')
			],
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng'
		];
		$this->assertData($expected, $result);
	}

/**
 * testDelete method
 *
 * Method: GET
 *
 * @return void
 */
	public function testDeleteGet() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('MethodNotAllowedException');
		$this->testAction('/cake_settings_app/queues/delete', $opt);
	}

/**
 * testDelete method
 *
 * Method: POST
 * ID: invalid
 *
 * @return void
 */
	public function testDeleteInvalidIdPost() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST'
		];
		$data = http_build_query(
			[
				'jobtype' => 'Sync',
				'created' => '2016-09-26 00:00:00',
				'failed' => '0',
				'status' => 'NOT_STARTED'
			]
		);
		$this->testAction('/cake_settings_app/queues/delete?' . $data, $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'The task could not be deleted. Please, try again.'));
	}

/**
 * testDelete method
 *
 * Method: POST
 * Delete: success
 *
 * @return void
 */
	public function testDeleteSuccessPost() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST'
		];
		$data = http_build_query(
			[
				'jobtype' => 'Sync',
				'created' => '2016-09-26 08:28:15',
				'failed' => '0',
				'status' => 'NOT_STARTED'
			]
		);
		$this->testAction('/cake_settings_app/queues/delete?' . $data, $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'The task has been deleted.'));
	}

/**
 * testClear method
 *
 * Method: GET
 *
 * @return void
 */
	public function testClearGet() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('MethodNotAllowedException');
		$this->testAction('/cake_settings_app/queues/clear', $opt);
	}

/**
 * testClear method
 *
 * Method: POST
 * Delete: success
 *
 * @return void
 */
	public function testClearSuccessPost() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST'
		];
		$this->testAction('/cake_settings_app/queues/clear', $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'The task queue has been cleared.'));
	}

/**
 * Generate mocked QueuesController.
 *
 * @return bool Success
 */
	protected function _generateMockedController() {
		$mocks = [
			'components' => [
				'Security',
				'Auth',
			]
		];
		if (!$this->generateMockedController($mocks)) {
			return false;
		}

		return true;
	}
}
