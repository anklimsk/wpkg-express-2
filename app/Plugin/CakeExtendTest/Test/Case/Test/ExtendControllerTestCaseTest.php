<?php
App::uses('ExtendControllerTestCase', 'CakeExtendTest.Test');
App::uses('Controller', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeSession', 'Model/Datasource');

/**
 * TestController class
 *
 * @package     plugin.Test.Case.TestCase
 */
class TestController extends Controller {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Test';

/**
 * uses property
 *
 * @var array
 */
	public $uses = [];

/**
 * components property
 *
 * @var array
 */
	public $components = [
		'Auth',
		'Session',
		'Security',
		'Flash',
		'RequestHandler',
	];

/**
 * Action `index`. Used as target for redirect
 *
 * @return void
 */
	public function index() {
	}

/**
 * Action `some_action`. Used to test redirect.
 *
 * @return void
 */
	public function some_action() {
		return $this->redirect('/cake_extend_test/test');
	}

/**
 * Action `download`. Used to test download file.
 *
 * @return void
 */
	public function download() {
		$this->response->type('txt');
		$this->response->file(
			TMP . 'test' . DS . 'some_file.txt',
			['download' => true, 'name' => 'some_file_test']
		);

		return $this->response;
	}

}

/**
 * ExtendControllerTestCase Test Case
 */
class ExtendControllerTestCaseTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

/**
 * Path to test download directory
 *
 * @var string
 */
	protected $_downloadDir = TMP . 'test' . DS;

/**
 * setUp method
 *
 * Actions:
 *  - Create target test object;
 *  - Create file for testing download.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		if (PHP_SAPI === 'cli') {
			Configure::write('App.base', '');
		}
		CakeSession::start();
		$this->_targetObject = new ExtendControllerTestCase();
		$this->_targetObject->targetController = 'Test';
		$oFolder = new Folder($this->_downloadDir, true);
		$oFolder->create($this->_downloadDir);
		$oFile = new File($this->_downloadDir . 'some_file.txt', true);
		$oFile->write('Text...');
		$oFile->close();
	}

/**
 * tearDown method
 *
 * Actions:
 *  - Remove file for testing download.
 *
 * @return void
 */
	public function tearDown() {
		$Folder = new Folder($this->_downloadDir);
		$Folder->delete();
		CakeSession::destroy();
		parent::tearDown();
	}

/**
 * testSetUpTearDown method
 *
 * @return void
 */
	public function testSetUpTearDown() {
		Configure::write('Config.language', 'tst');
		$this->_targetObject->setUp();

		Configure::write('Config.language', 'rus');
		$this->_targetObject->tearDown();

		$result = Configure::read('Config.language');
		$expected = 'tst';
		$this->assertSame($expected, $result);
	}

/**
 * testGenerateMockedController method
 *
 * @return void
 */
	public function testGenerateMockedController() {
		$this->_targetObject->targetController = null;
		$this->_targetObject->setDefaultUserInfo(['name' => 'Some User']);
		$this->_targetObject->applyUserInfo();
		$result = $this->_targetObject->generateMockedController();
		$this->assertFalse($result);

		$this->_targetObject->targetController = 'Test';
		$this->_targetObject->applyUserInfo(['id' => 6]);
		$result = $this->_targetObject->generateMockedController();
		$this->assertTrue($result);

		$result = $this->_targetObject->Controller->Auth->user();
		$expected = [
			'id' => 6,
			'name' => 'Some User',
		];
		$this->assertSame($expected, $result);
	}

/**
 * testAssertData method
 *
 * @return void
 */
	public function testAssertData() {
		$result = true;
		$expected = true;
		$this->_targetObject->assertData($expected, $result);

		$result = 'Test text';
		$expected = 'Test text';
		$this->_targetObject->assertData($expected, $result);

		$result = [
			'a' => 'test',
			'Some text'
		];
		$expected = [
			'a' => 'test',
			'Some text'
		];
		$this->_targetObject->assertData($expected, $result);
	}

/**
 * testCheckRedirect method
 *
 * @return void
 */
	public function testCheckRedirect() {
		$this->_targetObject->generateMockedController();
		$this->_targetObject->Controller->Auth->allow('some_action');
		$this->_targetObject->testAction('/cake_extend_test/test/some_action');
		$this->_targetObject->checkRedirect('/cake_extend_test/test', true);
	}

/**
 * testCheckRedirectExists method
 *
 * @return void
 */
	public function testCheckRedirectExists() {
		$this->_targetObject->generateMockedController();

		$this->_targetObject->Controller->Auth->allow('index');
		$this->_targetObject->testAction('/cake_extend_test/test/some_action');
		$this->_targetObject->checkRedirect(true);
	}

/**
 * testCheckRedirectNotExists method
 *
 * @return void
 */
	public function testCheckRedirectNotExists() {
		$this->_targetObject->generateMockedController();

		$this->_targetObject->Controller->Auth->allow('index');
		$this->_targetObject->testAction('/cake_extend_test/test/index');
		$this->_targetObject->checkRedirect(false);
	}

/**
 * testCheckDownloadFile method
 *
 * @return void
 */
	public function testCheckDownloadFile() {
		$this->_targetObject->generateMockedController();

		$this->_targetObject->Controller->Auth->allow('download');
		$this->_targetObject->testAction('/cake_extend_test/test/download');
		$this->_targetObject->checkDownloadFile('some_file_test', 7);
	}

/**
 * testCheckIsNotAuthorized method
 *
 * @return void
 */
	public function testCheckIsNotAuthorized() {
		$this->_targetObject->generateMockedController();

		$this->_targetObject->Controller->Auth->deny('some_action');
		$this->_targetObject->testAction('/cake_extend_test/test/some_action');
		$this->_targetObject->checkIsNotAuthorized(false);
	}

/**
 * testCheckIsAuthorized method
 *
 * @return void
 */
	public function testCheckIsAuthorized() {
		$this->_targetObject->generateMockedController();

		$this->_targetObject->Controller->Auth->allow('some_action');
		$this->_targetObject->testAction('/cake_extend_test/test/some_action');
		$this->_targetObject->checkIsNotAuthorized(true);
	}
}
