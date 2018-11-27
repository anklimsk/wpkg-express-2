<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('FlashMessage', 'CakeTheme.Model');
App::uses('CakeSession', 'Model/Datasource');

/**
 * FlashMessage Test Case
 */
class FlashMessageTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

	protected $_defaultMessages = [
		'flash' => [
			[
				'message' => 'Some text',
				'key' => 'flash',
				'element' => 'Flash/success',
				'params' => ['code' => 200]
			]
		],
		'info' => [
			[
				'message' => 'Info text',
				'key' => 'info',
				'element' => 'Flash/information',
				'params' => []
			]
		]
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeTheme.FlashMessage');
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
 * testGetMessageEmptyData method
 *
 * @return void
 */
	public function testGetMessageEmptyData() {
		CakeSession::delete('Message');
		$result = $this->_targetObject->getMessage();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetMessage method
 *
 * @return void
 */
	public function testGetMessageBadData() {
		CakeSession::write('Message', 'BAD_DATA');
		$result = $this->_targetObject->getMessage();
		$expected = [];
		$this->assertData($expected, $result);
		CakeSession::delete('Message');
	}

/**
 * testGetMessageValidData method
 *
 * @return void
 */
	public function testGetMessageValidData() {
		CakeSession::write('Message', $this->_defaultMessages);
		$key = 'flash';
		$result = $this->_targetObject->getMessage($key);
		$expected = $this->_defaultMessages[$key];
		$this->assertData($expected, $result);
		CakeSession::delete('Message');
	}

/**
 * testDeleteQueuedTaskEmptyData method
 *
 * @return void
 */
	public function testDeleteQueuedTaskEmptyData() {
		CakeSession::write('Message', $this->_defaultMessages);
		$result = $this->_targetObject->deleteMessage();
		$this->assertFalse($result);
		CakeSession::delete('Message');
	}

/**
 * testDeleteMessageInvalidData method
 *
 * @return void
 */
	public function testDeleteMessageInvalidData() {
		CakeSession::write('Message', $this->_defaultMessages);
		$result = $this->_targetObject->deleteMessage('BAD_DATA');
		$this->assertFalse($result);
		$result = CakeSession::read('Message');
		$this->assertData($this->_defaultMessages, $result);
		CakeSession::delete('Message');
	}

/**
 * testDeleteMessageValidData method
 *
 * @return void
 */
	public function testDeleteMessageValidData() {
		CakeSession::write('Message', $this->_defaultMessages);
		$result = $this->_targetObject->deleteMessage('flash');
		$this->assertTrue($result);
		$expected = [
			'info' => [
				[
					'message' => 'Info text',
					'key' => 'info',
					'element' => 'Flash/information',
					'params' => []
				]
			]
		];
		$result = CakeSession::read('Message');
		$this->assertData($expected, $result);
		CakeSession::delete('Message');
	}
}
