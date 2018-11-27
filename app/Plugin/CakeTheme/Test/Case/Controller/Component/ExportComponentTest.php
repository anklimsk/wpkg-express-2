<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ExportComponent', 'CakeTheme.Controller/Component');
App::uses('CakeThemeAppModel', 'CakeTheme.Model');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Export for CakeTheme.
 *
 * @package plugin.Model
 */
class ExportTestModel extends CakeThemeAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'ExportTestModel';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = false;

	public $exportDir = null;

/**
 * Create export file
 *
 * @param int|string $id Record ID
 * @return string|bool Return path to created file.
 *  Return False on failure.
 */
	public function generateExportFile($id = null) {
		if (empty($id) || !file_exists($this->exportDir)) {
			return false;
		}

		if ($id === 3) {
			return false;
		}

		$exportFile = $this->exportDir . 'test.txt';
		if (!$this->_createTxtFile($exportFile)) {
			return false;
		}

		return $exportFile;
	}

/**
 * Return export file name
 *
 * @param int|string $id Record ID
 * @return string Return name for export file.
 */
	public function getExportFilename($id = null) {
		$result = '';
		if ($id === 2) {
			return $result;
		}

		$result = 'экспортируемый файл ' . (string)$id;

		return $result;
	}

/**
 * Create text file
 *
 * @param string $file Path to file
 * @return bool Success
 */
	protected function _createTxtFile($file = null) {
		if (empty($file)) {
			return false;
		}

		$oFile = new File($file, true);
		if (!$oFile->write('Some text...')) {
			return false;
		}

		$oFile->close();

		return true;
	}

}

/**
 * ExportTestController class
 *
 */
class ExportTestController extends Controller {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'ExportTest';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'RequestHandler',
		'CakeTheme.ViewExtension'
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * Can be set to several values to express different options:
 *
 * - `true` Use the default inflected model name.
 * - `array()` Use only models defined in the parent class.
 * - `false` Use no models at all, do not merge with parent class either.
 * - `array('Post', 'Comment')` Use only the Post and Comment models. Models
 *   Will also be merged with the parent class.
 *
 * The default value is `true`.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['ExportTestModel'];
}

/**
 * ExportComponent Test Case
 */
class ExportComponentTest extends AppCakeTestCase {

/**
 * Current path to web root.
 *
 * @var string
 */
	protected $_webRoot = '';

	protected $_exportDir = TMP . 'test' . DS . 'export' . DS;

	protected $_previewDir = null;

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

		$this->_webRoot = Configure::read('App.www_root');
		$testWebRoot = CakePlugin::path('CakeTheme') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS;
		$this->_previewDir = $testWebRoot . 'img' . DS . 'prev' . DS;
		Configure::write('App.www_root', $testWebRoot);

		$oFolder = new Folder($this->_exportDir, true);
		$oFolder->create($this->_exportDir);

		$oFolder = new Folder($this->_previewDir, true);
		$oFolder->create($this->_previewDir);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$testDirs = [
			$this->_exportDir,
			$this->_previewDir,
			TMP . 'test',
		];
		foreach ($testDirs as $testDir) {
			$oFolder = new Folder($testDir);
			$oFolder->delete();
		}
		if (!empty($this->_webRoot)) {
			Configure::write('App.www_root', $this->_webRoot);
		}
		unset($this->webRoot);
		unset($this->ExportComponent);

		parent::tearDown();
	}

/**
 * testConstruct method
 *
 * @return void
 */
	public function testConstruct() {
		$this->setExpectedException('InternalErrorException');
		Configure::delete('App.www_root');
		$this->_createComponet('/cake_theme/test');
	}

/**
 * testInitializeInitConfig method
 *
 * @return void
 */
	public function testInitializeInitConfig() {
		Configure::delete('CakeTheme');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$result = Configure::read('CakeTheme.ViewExtension');
		$this->assertFalse(empty($result));
	}

/**
 * testClearDirBadType method
 *
 * @return void
 */
	public function testClearDirBadType() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);

		$result = $this->ExportComponent->_clearDir('bad-type');
		$this->assertFalse($result);
	}

/**
 * testClearDirEmptyFolder method
 *
 * @return void
 */
	public function testClearDirEmptyFolder() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);

		$cleanupTypes = [
			'export',
			'preview',
		];
		foreach ($cleanupTypes as $type) {
			$result = $this->ExportComponent->_clearDir($type);
			$this->assertTrue($result);
		}
	}

/**
 * testClearDirSuccess method
 *
 * @return void
 */
	public function testClearDirSuccess() {
		$testDirs = [
			$this->_exportDir,
			$this->_previewDir,
		];
		$testFileName = 'test.file';
		foreach ($testDirs as $testDir) {
			$oFile = new File($testDir . DS . $testFileName, true);
			$oFile->write(time());
			$oFile->close();
		}

		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);

		$cleanupTypes = [
			'export',
			'preview',
		];
		$timeNow = strtotime('+1 minute');
		foreach ($cleanupTypes as $type) {
			$result = $this->ExportComponent->_clearDir($type, $timeNow);
			$this->assertTrue($result);
		}

		$expected = [$testFileName];
		foreach ($testDirs as $testDir) {
			$oFolder = new Folder($testDir);
			$filesExport = $oFolder->find('.*\.file', false);
			$this->assertData($expected, $filesExport);
		}

		$timeNow = strtotime('+10 minute');
		foreach ($cleanupTypes as $type) {
			$result = $this->ExportComponent->_clearDir($type, $timeNow);
			$this->assertTrue($result);
		}
		foreach ($testDirs as $testDir) {
			$oFolder = new Folder($testDir);
			$filesExport = $oFolder->find('.*\.file', false);
			$this->assertEmpty($filesExport);
		}
	}

/**
 * testExportBadRequest method
 *
 * @return void
 */
	public function testExportBadRequest() {
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$this->ExportComponent->export(1, 'txt');
	}

/**
 * testExportBadId method
 *
 * @return void
 */
	public function testExportBadId() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$this->ExportComponent->export(null, 'txt');
	}

/**
 * testExportBadExportFile method
 *
 * @return void
 */
	public function testExportBadExportFile() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$request = $this->ExportComponent->export(3, 'txt');
	}

/**
 * testExportSuccess method
 *
 * @return void
 */
	public function testExportSuccess() {
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$request = $this->ExportComponent->export(1, 'txt');
		$result = $request->header();
		$expected = [
			'Content-Disposition' => 'attachment; filename="экспортируемый файл 1.txt"',
			'Content-Transfer-Encoding' => 'binary',
			'Accept-Ranges' => 'bytes',
			'Content-Length' => '12'
		];
		$this->assertData($expected, $result);
	}

/**
 * testPreviewUnoconvNotCfg method
 *
 * @return void
 */
	public function testPreviewUnoconvNotCfg() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		Configure::delete('CakeTheme.ViewExtension.Unoconv');
		$this->ExportComponent->preview(1);
	}

/**
 * testPreviewBadId method
 *
 * @return void
 */
	public function testPreviewBadId() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$this->ExportComponent->preview(null);
	}

/**
 * testPreviewBadFileName method
 *
 * @return void
 */
	public function testPreviewBadFileName() {
		Configure::delete('ViewExtension');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$unoconvCfg = (string)Configure::read('CakeTheme.ViewExtension.Unoconv.binaries');
		$this->skipIf(empty($unoconvCfg), __d('plugin_test', 'Path to binaries for Unoconv is not configured'));
		$this->skipIf(!file_exists($unoconvCfg), __d('plugin_test', 'Invalid path to binaries for Unoconv: "%s"', $unoconvCfg));

		$result = $this->ExportComponent->preview(2);

		$this->assertTrue(isset($result['preview'][0]));
		$preview = $result['preview'][0];
		unset($result['preview'][0]);

		$this->assertTrue(isset($result['download']['pdf']));
		$pdf = $result['download']['pdf'];
		unset($result['download']['pdf']);
		$expected = [
			'exportFileName' => '',
			'preview' => [],
			'download' => [
				'orig' => 'test'
			]
		];
		$this->assertData($expected, $result);
		$this->assertRegExp('/\/img\/prev\/[0-9a-f]+\[0\]\.jpg/i', $preview);
		$this->assertRegExp('/[0-9a-f]+/i', $pdf);
	}

/**
 * testPreviewBadExportFile method
 *
 * @return void
 */
	public function testPreviewBadExportFile() {
		$this->setExpectedException('InternalErrorException');
		Configure::delete('ViewExtension');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$unoconvCfg = (string)Configure::read('CakeTheme.ViewExtension.Unoconv.binaries');
		$this->skipIf(empty($unoconvCfg), __d('plugin_test', 'Path to binaries for Unoconv is not configured'));
		$this->skipIf(!file_exists($unoconvCfg), __d('plugin_test', 'Invalid path to binaries for Unoconv: "%s"', $unoconvCfg));

		$this->ExportComponent->preview(3);
	}

/**
 * testPreviewSuccess method
 *
 * @return void
 */
	public function testPreviewSuccess() {
		Configure::delete('ViewExtension');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$unoconvCfg = (string)Configure::read('CakeTheme.ViewExtension.Unoconv.binaries');
		$this->skipIf(empty($unoconvCfg), __d('plugin_test', 'Path to binaries for Unoconv is not configured'));
		$this->skipIf(!file_exists($unoconvCfg), __d('plugin_test', 'Invalid path to binaries for Unoconv: "%s"', $unoconvCfg));

		$result = $this->ExportComponent->preview(1);

		$this->assertTrue(isset($result['preview'][0]));
		$preview = $result['preview'][0];
		unset($result['preview'][0]);

		$this->assertTrue(isset($result['download']['pdf']));
		$pdf = $result['download']['pdf'];
		unset($result['download']['pdf']);
		$expected = [
			'exportFileName' => 'экспортируемый файл 1',
			'preview' => [],
			'download' => [
				'orig' => 'test'
			]
		];
		$this->assertData($expected, $result);
		$this->assertRegExp('/\/img\/prev\/[0-9a-f]+\[0\]\.jpg/i', $preview);
		$this->assertRegExp('/[0-9a-f]+/i', $pdf);
	}

/**
 * testDownloadBadRequest method
 *
 * @return void
 */
	public function testDownloadBadRequest() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$this->ExportComponent->download(1, 'test');
	}

/**
 * testDownloadBadFileName method
 *
 * @return void
 */
	public function testDownloadBadFileName() {
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$resultCreateExportFile = $this->Controller->ExportTestModel->generateExportFile(2);
		$this->assertTrue($resultCreateExportFile !== false);
		$request = $this->ExportComponent->download(2, 'test');
		$result = $request->header();
		$expected = [
			'Content-Disposition' => 'attachment; filename="test.txt"',
			'Content-Transfer-Encoding' => 'binary',
			'Accept-Ranges' => 'bytes',
			'Content-Length' => '12'
		];
		$this->assertData($expected, $result);
	}

/**
 * testDownloadMsieSuccess method
 *
 * @return void
 */
	public function testDownloadMsieSuccess() {
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko';
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$resultCreateExportFile = $this->Controller->ExportTestModel->generateExportFile(1);
		$this->assertTrue($resultCreateExportFile !== false);
		$request = $this->ExportComponent->download(1, 'test');
		$result = $request->header();
		$expected = [
			'Content-Disposition' => 'attachment; filename="' . rawurlencode('экспортируемый файл 1.txt') . '"',
			'Content-Transfer-Encoding' => 'binary',
			'Accept-Ranges' => 'bytes',
			'Content-Length' => '12'
		];
		$this->assertData($expected, $result);
	}

/**
 * testDownloadNonMsieSuccess method
 *
 * @return void
 */
	public function testDownloadNonMsieSuccess() {
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0';
		$this->_createComponet('/cake_theme/test.txt', 'GET', ['ext' => 'txt']);
		$this->ExportComponent->initialize($this->Controller);
		$resultCreateExportFile = $this->Controller->ExportTestModel->generateExportFile(1);
		$this->assertTrue($resultCreateExportFile !== false);
		$request = $this->ExportComponent->download(1, 'test');
		$result = $request->header();
		$expected = [
			'Content-Disposition' => 'attachment; filename="экспортируемый файл 1.txt"',
			'Content-Transfer-Encoding' => 'binary',
			'Accept-Ranges' => 'bytes',
			'Content-Length' => '12'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetUnoconvConfigNotExists method
 *
 * @return void
 */
	public function testGetUnoconvConfigNotExists() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		Configure::delete('CakeTheme');
		$result = $this->ExportComponent->_getUnoconvConfig();
		$this->assertFalse($result);
	}

/**
 * testGetUnoconvConfigSuccess method
 *
 * @return void
 */
	public function testGetUnoconvConfigSuccess() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$config = [
			'timeout' => 45,
			'binaries' => $this->_exportDir,
		];
		Configure::write('CakeTheme.ViewExtension.Unoconv', $config);
		$result = $this->ExportComponent->_getUnoconvConfig();
		$expected = $config;
		$this->assertData($expected, $result);
	}

/**
 * testIsUnoconvReadyConfigNotExists method
 *
 * @return void
 */
	public function testIsUnoconvReadyConfigNotExists() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		Configure::delete('ViewExtension');
		$result = $this->ExportComponent->isUnoconvReady();
		$this->assertFalse($result);
	}

/**
 * testIsUnoconvReadySuccess method
 *
 * @return void
 */
	public function testIsUnoconvReadySuccess() {
		$this->_createComponet('/cake_theme/test');
		$this->ExportComponent->initialize($this->Controller);
		$config = [
			'timeout' => 45,
			'binaries' => $this->_exportDir,
		];
		Configure::write('CakeTheme.ViewExtension.Unoconv', $config);
		$result = $this->ExportComponent->isUnoconvReady();
		$this->assertTrue($result);
	}

/**
 * Create ExportComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();

		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$settings = [
			'storageTimeExport' => 120,
			'storageTimePreview' => 120,
			'exportDir' => $this->_exportDir,
			'previewDir' => $this->_previewDir,
		];
		$Collection = new ComponentCollection();
		$this->Controller = new ExportTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Controller->ExportTestModel->exportDir = $this->_exportDir;
		$this->Controller->RequestHandler->initialize($this->Controller);
		$this->Controller->ViewExtension->initialize($this->Controller);
		$ExportComponent = new ExportComponent($Collection, $settings);
		$this->ExportComponent = $this->createProxyObject($ExportComponent);
	}
}
