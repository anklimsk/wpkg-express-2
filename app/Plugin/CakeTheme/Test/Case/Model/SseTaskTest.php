<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('SseTask', 'CakeTheme.Model');
App::uses('CakeSession', 'Model/Datasource');

/**
 * SseTask Test Case
 */
class SseTaskTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

	protected $_defaultTasks = [
		'SomeTask',
		'TestTask'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeTheme.SseTask');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testGetListQueuedTaskEmptyData method
 *
 * @return void
 */
	public function testGetListQueuedTaskEmptyData() {
		CakeSession::delete('SSE.progress');
		$result = $this->_targetObject->getListQueuedTask();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListQueuedTask method
 *
 * @return void
 */
	public function testGetListQueuedTaskBadData() {
		CakeSession::write('SSE.progress', 'BAD_DATA');
		$result = $this->_targetObject->getListQueuedTask();
		$expected = [];
		$this->assertData($expected, $result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testGetListQueuedTaskValidData method
 *
 * @return void
 */
	public function testGetListQueuedTaskValidData() {
		CakeSession::write('SSE.progress', $this->_defaultTasks);
		$result = $this->_targetObject->getListQueuedTask();
		$expected = $this->_defaultTasks;
		$this->assertData($expected, $result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testDeleteQueuedTaskEmptyData method
 *
 * @return void
 */
	public function testDeleteQueuedTaskEmptyData() {
		CakeSession::write('SSE.progress', $this->_defaultTasks);
		$result = $this->_targetObject->deleteQueuedTask();
		$this->assertFalse($result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testDeleteQueuedTaskBadData method
 *
 * @return void
 */
	public function testDeleteQueuedTaskBadData() {
		CakeSession::write('SSE.progress', $this->_defaultTasks);
		$result = $this->_targetObject->deleteQueuedTask('BAD_DATA');
		$this->assertFalse($result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testDeleteQueuedTaskInvalidData method
 *
 * @return void
 */
	public function testDeleteQueuedTaskInvalidData() {
		CakeSession::write('SSE.progress', $this->_defaultTasks);
		$result = $this->_targetObject->deleteQueuedTask(['BAD_DATA']);
		$this->assertTrue($result);
		$result = CakeSession::read('SSE.progress');
		$this->assertData($this->_defaultTasks, $result);
		CakeSession::delete('SSE.progress');
	}

/**
 * testDeleteQueuedTaskValidData method
 *
 * @return void
 */
	public function testDeleteQueuedTaskValidData() {
		CakeSession::write('SSE.progress', $this->_defaultTasks);
		$result = $this->_targetObject->deleteQueuedTask(['SomeTask']);
		$this->assertTrue($result);
		$expected = ['TestTask'];
		$result = CakeSession::read('SSE.progress');
		$this->assertData($expected, $result);
		CakeSession::delete('SSE.progress');
	}
}
