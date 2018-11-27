<?php
App::uses('AppCakeTestCase', 'CakeSettingsApp.Test');
App::uses('QueueInfo', 'CakeSettingsApp.Model');

/**
 * QueueInfoTest Test Case
 *
 */
class QueueInfoTest extends AppCakeTestCase {

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
		$this->_targetObject = ClassRegistry::init('CakeSettingsApp.QueueInfo');
	}

/**
 * testProcessGroupAction method
 *
 * @return void
 */
	public function testProcessGroupAction() {
		$params = [
			[
				null, // $groupAction
				null, // $conditions
			],
			[
				'bad-action', // $groupAction
				[
					'QueueInfo.id' => [1]
				], // $conditions
			],
			[
				'group-data-del', // $groupAction
				[
					'QueueInfo.id' => [2]
				], // $conditions
			],
		];
		$expected = [
			null,
			false,
			true,
		];
		$this->runClassMethodGroup('processGroupAction', $params, $expected);
	}

/**
 * testGetListTaskState method
 *
 * @return void
 */
	public function testGetListTaskState() {
		$result = $this->_targetObject->getListTaskState();
		$expected = [
			'NOT_READY' => __d('cake_settings_app', 'Not ready'),
			'NOT_STARTED' => __d('cake_settings_app', 'Not started'),
			'IN_PROGRESS' => __d('cake_settings_app', 'In progress'),
			'COMPLETED' => __d('cake_settings_app', 'Completed'),
			'FAILED' => __d('cake_settings_app', 'Failed'),
			'UNKNOWN' => __d('cake_settings_app', 'Unknown'),
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListBarStateClass method
 *
 * @return void
 */
	public function testGetListBarStateClass() {
		$result = $this->_targetObject->getListBarStateClass();
		$expected = [
			'NOT_READY' => 'progress-bar-warning',
			'NOT_STARTED' => 'progress-bar-success progress-bar-striped',
			'IN_PROGRESS' => 'progress-bar-info',
			'COMPLETED' => 'progress-bar-success',
			'FAILED' => 'progress-bar-danger',
			'UNKNOWN' => 'progress-bar-danger progress-bar-striped',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetBarStateInfo method
 *
 * @return void
 */
	public function testGetBarStateInfo() {
		$this->skipIf(!CakePlugin::loaded('Queue'), "Plugin 'Queue' is not loaded");

		$result = $this->_targetObject->getBarStateInfo();
		$expected = [
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
		];
		$this->assertData($expected, $result);
	}
}
