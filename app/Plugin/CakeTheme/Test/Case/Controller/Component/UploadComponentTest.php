<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Folder', 'Utility');
App::uses('UploadComponent', 'CakeTheme.Controller/Component');

/**
 * UploadTestController class
 *
 */
class UploadTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
			'RequestHandler'
	];
}

/**
 * UploadComponent Test Case
 */
class UploadComponentTest extends AppCakeTestCase {

	protected $_uploadDir = TMP . 'test' . DS . 'upload';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->tmpDir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
		$oFolder = new Folder($this->_uploadDir, true);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Upload);
		unset($this->tmpDir);
		$oFolder = new Folder(TMP . 'test');
		$oFolder->delete();
		$this->resetRequestType();

		parent::tearDown();
	}

/**
 * testUpload method
 *
 * @return void
 */
	public function testUploadNotAjaxGet() {
		$this->setJsonRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/upload.json');
		$this->Upload->initialize($this->Controller);
		$this->Upload->upload();
		$this->resetJsonRequest();
	}

/**
 * testUpload method
 *
 * @return void
 */
	public function testUploadNotJsonPost() {
		$this->setAjaxRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/upload', 'POST');
		$this->Upload->initialize($this->Controller);
		$this->Upload->upload();
		$this->resetAjaxRequest();
	}

/**
 * testUpload method
 *
 * @return void
 */
	public function testUploadInvalidRequest() {
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->setExpectedException('MethodNotAllowedException');
		$this->_createComponet('/cake_theme/upload.json', 'TEST');
		$this->Upload->initialize($this->Controller);
		$this->Upload->upload();
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testUpload method
 *
 * Method: POST
 * Data: invalid format
 *
 * @return void
 */
	public function testUploadPostDataInvalidFormat() {
		$this->setJsonRequest();
		$this->setAjaxRequest();

		$uploadFile = $this->tmpDir . DS . 'test.gif';
		$uploadFileName = basename($uploadFile);

		$this->prepareUploadTest();
		$im = imagecreatetruecolor(100, 100);
		$textColor = imagecolorallocate($im, 255, 255, 255);
		imagestring($im, 1, 5, 5, 'Test', $textColor);
		$result = imagegif($im, $uploadFile);
		$this->assertTrue($result);
		imagedestroy($im);

		$fileSize = filesize($uploadFile);
		if ($result) {
			$this->assertTrue($fileSize > 0);
		}

		$_FILES = [
			'files' => [
				'name' => $uploadFileName,
				'type' => 'image/gif',
				'tmp_name' => $uploadFile,
				'error' => 0,
				'size' => $fileSize,
			],
		];

		$this->_createComponet('/cake_theme/upload.json');
		$this->Upload->initialize($this->Controller);
		$result = $this->Upload->upload(null, '/\.(jpe?g)$/i');
		$this->assertTrue(isset($result['files'][0]));

		$result['files'][0] = (array)$result['files'][0];
		$expected = [
			'files' => [
				[
					'name' => $uploadFileName,
					'size' => $fileSize,
					'type' => 'image/gif',
					'error' => 'Filetype not allowed'
				],
			]
		];

		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testUpload method
 *
 * Method: POST
 * Data: valid
 *
 * @return void
 */
	public function testUploadPostDataInvalidFileSize() {
		$this->setJsonRequest();
		$this->setAjaxRequest();

		$uploadFile = $this->tmpDir . DS . 'test.jpg';
		$uploadFileName = basename($uploadFile);

		$this->prepareUploadTest();
		$im = imagecreatetruecolor(100, 100);
		$textColor = imagecolorallocate($im, 255, 255, 255);
		imagestring($im, 1, 5, 5, 'Test', $textColor);
		$result = imagejpeg($im, $uploadFile);
		$this->assertTrue($result);
		imagedestroy($im);

		$fileSize = filesize($uploadFile);
		if ($result) {
			$this->assertTrue($fileSize > 0);
		}

		$_FILES = [
			'files' => [
				'name' => $uploadFileName,
				'type' => 'image/jpeg',
				'tmp_name' => $uploadFile,
				'error' => 0,
				'size' => $fileSize,
			],
		];

		$this->_createComponet('/cake_theme/upload.json');
		$this->Upload->initialize($this->Controller);
		$result = $this->Upload->upload(1, '/\.(jpe?g)$/i');
		$this->assertTrue(isset($result['files'][0]));

		$result['files'][0] = (array)$result['files'][0];
		$expected = [
			'files' => [
				[
					'name' => $uploadFileName,
					'size' => $fileSize,
					'type' => 'image/jpeg',
					'error' => 'File is too big'
				],
			]
		];

		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testUpload method
 *
 * Method: POST
 * Data: valid
 *
 * @return void
 */
	public function testUploadPostData() {
		$this->setJsonRequest();
		$this->setAjaxRequest();

		$uploadFile = $this->tmpDir . DS . 'test.jpg';
		$uploadFileName = basename($uploadFile);

		$this->prepareUploadTest();
		$im = imagecreatetruecolor(100, 100);
		$textColor = imagecolorallocate($im, 255, 255, 255);
		imagestring($im, 1, 5, 5, 'Test', $textColor);
		$result = imagejpeg($im, $uploadFile);
		$this->assertTrue($result);
		imagedestroy($im);

		$fileSize = filesize($uploadFile);
		if ($result) {
			$this->assertTrue($fileSize > 0);
		}

		$_FILES = [
			'files' => [
				'name' => $uploadFileName,
				'type' => 'image/jpeg',
				'tmp_name' => $uploadFile,
				'error' => 0,
				'size' => $fileSize,
			],
		];

		$this->_createComponet('/cake_theme/upload.json');
		$this->Upload->initialize($this->Controller);
		$result = $this->Upload->upload(null, '/\.(jpe?g)$/i');
		$this->assertTrue(isset($result['files'][0]));

		$result['files'][0] = (array)$result['files'][0];
		$expected = [
			'files' => [
				[
					'name' => $uploadFileName,
					'size' => $fileSize,
					'type' => 'image/jpeg',
					'url' => 'test.jpg',
					'deleteUrl' => '?file=test.jpg',
					'deleteType' => 'DELETE'
				],
			]
		];

		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testClearDirSuccess method
 *
 * @return void
 */
	public function testClearDirSuccess() {
		$testFileName = 'test.file';
		$oFile = new File($this->_uploadDir . DS . $testFileName, true);
		$oFile->write(time());
		$oFile->close();

		$this->_createComponet('/cake_theme/test');
		$this->Upload->initialize($this->Controller);

		$timeNow = strtotime('+1 minute');
		$result = $this->Upload->_clearDir($timeNow);
		$this->assertTrue($result);

		$expected = [$testFileName];
		$oFolder = new Folder($this->_uploadDir);
		$filesExport = $oFolder->find('.*\.file', false);
		$this->assertData($expected, $filesExport);

		$timeNow = strtotime('+10 minute');
		$result = $this->Upload->_clearDir($timeNow);
		$this->assertTrue($result);

		$oFolder = new Folder($this->_uploadDir);
		$filesExport = $oFolder->find('.*\.file', false);
		$this->assertEmpty($filesExport);
	}

/**
 * Create UploadComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`
 * @return void
 */
	protected function _createComponet($url = null, $type = 'POST') {
		if (!isset($_SERVER)) {
			$_SERVER = [];
		}

		if (!isset($_SERVER['SERVER_NAME'])) {
			$_SERVER['SERVER_NAME'] = 'somehost.local';
		}

		if (!isset($_SERVER['SERVER_PORT'])) {
			$_SERVER['SERVER_PORT'] = '80';
		}

		$request = new CakeRequest($url);
		$response = new CakeResponse();

		if (!empty($type)) {
			$this->setRequestType($type);
		}

		$settings = [
			'storageTimeUpload' => 120,
			'uploadDir' => $this->_uploadDir,
		];
		$Collection = new ComponentCollection();
		$this->Controller = new UploadTestController($request, $response);
		$this->Controller->constructClasses();
		$UploadComponent = new UploadComponent($Collection, $settings);
		$this->Upload = $this->createProxyObject($UploadComponent);
	}
}
