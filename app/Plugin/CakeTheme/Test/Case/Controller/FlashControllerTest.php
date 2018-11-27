<?php
App::uses('AppControllerTestCase', 'CakeTheme.Test');
App::uses('FlashComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::uses('CakeSession', 'Model/Datasource');

class FlashControllerTest extends AppControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Components = new ComponentCollection();
		$this->Flash = new FlashComponent($this->Components);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		CakeSession::destroy();
	}

/**
 * testFlashcfgGet method
 *
 * @return void
 */
	public function testFlashcfgGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/flash/flashcfg.json', $opt);
	}

/**
 * testFlashcfgNotAjaxPost method
 *
 * @return void
 */
	public function testFlashcfgNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/flash/flashcfg', $opt);
	}

/**
 * testFlashcfgPost method
 *
 * @return void
 */
	public function testFlashcfgPost() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashcfg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'flashKeys' => [
				'flash',
				'auth',
				'test'
			],
			'timeOut' => 15,
			'delayDeleteFlash' => 5,
			'globalAjaxComplete' => false,
			'theme' => 'mint',
			'layout' => 'top',
			'open' => 'animated flipInX',
			'close' => 'animated flipOutX',
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgGet method
 *
 * @return void
 */
	public function testFlashmsgGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/flash/flashmsg.json', $opt);
	}

/**
 * testFlashmsgNotAjaxPost method
 *
 * @return void
 */
	public function testFlashmsgNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/flash/flashmsg', $opt);
	}

/**
 * testFlashmsgPost method
 *
 * @return void
 */
	public function testFlashmsgPost() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/flash/flashmsg', $opt);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataEmpty method
 *
 * Method: POST
 * Data: empty
 *
 * @return void
 */
	public function testFlashmsgPostDataEmpty() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'result' => false,
				'key' => null,
				'messages' => [],
			]];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataEmptyKey method
 *
 * Method: POST
 * Data: empty
 *
 * @return void
 */
	public function testFlashmsgPostDataEmptyKey() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'keys' => '',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'result' => false,
				'key' => null,
				'messages' => [],
			]];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataInvalid method
 *
 * Method: POST
 * Data: invalid
 *
 * @return void
 */
	public function testFlashmsgPostDataInvalid() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'bad_key' => 'tst',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'result' => false,
				'key' => null,
				'messages' => [],
			]
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataNotExists method
 *
 * Method: POST
 * Data: invalid
 *
 * @return void
 */
	public function testFlashmsgPostDataNotExists() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'keys' => 'tst',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'result' => false,
				'key' => 'tst',
				'messages' => [],
			]
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataValid method
 *
 * Method: POST
 * Data: valid
 *
 * @return void
 */
	public function testFlashmsgPostDataValid() {
		$key = 'test_flash';
		$message = 'Test information';
		$element = 'flash_information';
		$params = [];

		$this->Flash->set($message, compact('key', 'element', 'params'));
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'keys' => $key,
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$element = 'Flash/' . $element;
		$expected = [
			[
				'result' => true,
				'key' => $key,
				'messages' => [compact('message', 'key', 'element', 'params')],
			]
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataNotExistsDelete method
 *
 * Method: POST
 * Data: invalid
 *
 * @return void
 */
	public function testFlashmsgPostDataNotExistsDelete() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'keys' => 'tst',
				'delete' => 1
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'result' => false,
				'key' => 'tst',
				'messages' => [],
			]
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testFlashmsgPostDataValidDelete method
 *
 * Method: POST
 * Data: valid
 *
 * @return void
 */
	public function testFlashmsgPostDataValidDelete() {
		$key = 'test_flash';
		$message = 'Test information';
		$element = 'flash_information';
		$params = [];

		$this->Flash->set($message, compact('key', 'element', 'params'));
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'keys' => $key,
				'delete' => 1
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/flash/flashmsg.json', $opt);
		$result = json_decode($result, true);
		$element = 'Flash/' . $element;
		$expected = [
			[
				'result' => true,
				'key' => $key,
				'messages' => [],
			]
		];
		$this->assertData($expected, $result);

		$result = CakeSession::check('Message.' . $key);
		$this->assertFalse($result);
		$this->resetAjaxRequest();
	}
}
