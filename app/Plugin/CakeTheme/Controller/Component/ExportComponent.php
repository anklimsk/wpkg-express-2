<?php
/**
 * This file is the componet file of the plugin.
 * Export files from server.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Hash', 'Utility');
App::import(
	'Vendor',
	'CakeTheme.PhpUnoconv',
	['file' => 'PhpUnoconv' . DS . 'autoload.php']
);

/**
 * Export Component.
 *
 * Export files from server. Cleanup the export and preview
 *  directories from old files.
 * @package plugin.Controller.Component
 */
class ExportComponent extends Component {

/**
 * Other Components this component uses.
 *
 * @var array
 */
	public $components = [
		'CakeTheme.ViewExtension'
	];

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of target export model
 *
 * @var object
 */
	protected $_model = null;

/**
 * Object of target ConfigTheme model
 *
 * @var object
 */
	protected $_modelConfigTheme = null;

/**
 * Path to export directory
 *
 * @var string
 */
	protected $_pathExportDir = CAKE_THEME_EXPORT_DIR;

/**
 * Path to preview directory
 *
 * @var string
 */
	protected $_pathPreviewDir = null;

/**
 * Maximum time of store exported file
 *
 * @var int
 */
	protected $_storageTimeExport = 600;

/**
 * Maximum time of store preview file
 *
 * @var int
 */
	protected $_storageTimePreview = 600;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if empty configuration 'App.www_root'.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$wwwRoot = Configure::read('App.www_root');
		if (empty($wwwRoot)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid path to "www_root" directory'));
		}

		$previewDir = $wwwRoot . CAKE_THEME_PREVIEW_DIR;
		$this->_setPreviewDir($previewDir);

		$settingsMethods = [
			'storageTimeExport' => '_setStorageTimeExport',
			'storageTimePreview' => '_setStorageTimePreview',
			'exportDir' => '_setExportDir',
			'previewDir' => '_setPreviewDir',
		];
		foreach ($settingsMethods as $settingName => $setSettingMethod) {
			$settingValue = Hash::get($settings, $settingName);
			$this->$setSettingMethod($settingValue);
		}

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		$this->_model = $this->_controller->{$this->_controller->modelClass};
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}

		$this->_modelConfigTheme = ClassRegistry::init('CakeTheme.ConfigTheme');
	}

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * Actions:
 *  - Cleanup the export and preview directories from old files.
 *
 * @param Controller $controller Controller instance.
 * @return void
 */
	public function startup(Controller $controller) {
		$cleanupTypes = [
			'export',
			'preview',
		];
		foreach ($cleanupTypes as $type) {
			$this->_clearDir($type);
		}
	}

/**
 * Export file from server
 *
 * @param int|string $id ID for exported data.
 * @param string $type Extension of exported file.
 * @throws BadRequestException if not empty $type and extension of
 *  requested file is not equal $type.
 * @throws InternalErrorException if method Model::generateExportFile() is
 *  not exists.
 * @throws InternalErrorException if empty result of call method Model::generateExportFile().
 * @return object Return response object of exported file.
 */
	public function export($id = null, $type = 'docx') {
		$type = mb_strtolower($type);
		if (!empty($type) && !$this->_controller->RequestHandler->prefers($type)) {
			throw new BadRequestException(__d('view_extension', 'Invalid request'));
		}

		if (!method_exists($this->_model, 'generateExportFile') || !method_exists($this->_model, 'getExportFilename')) {
			throw new InternalErrorException(__d('view_extension', 'Export method not found'));
		}

		$tempFile = $this->_model->generateExportFile($id);
		if (empty($tempFile)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid generated file'));
		}

		$exportFile = pathinfo($tempFile, PATHINFO_FILENAME);

		return $this->download($id, $exportFile);
	}

/**
 * Generate preview images of exported file
 *
 * @param int|string $id ID for exported data.
 * @throws InternalErrorException if method Model::generateExportFile() is
 *  not exists.
 * @throws InternalErrorException if empty result of call method Model::generateExportFile().
 * @throws InternalErrorException if the exported file is not converted into the PDF file.
 * @return array Return array of information for preview exported file. Format:
 *  - key `exportFileName`; value - name of preview file;
 *  - key `preview` - array of preview images; value - relative path to preview images.
 *  - key `download` - array of files for downloading; value:
 *   - key `orig`; value - file name of original file;
 *   - key `pdf`; value - file name of PDF preview file.
 */
	public function preview($id = null) {
		if (!method_exists($this->_model, 'generateExportFile')) {
			throw new InternalErrorException(__d(
				'view_extension',
				'Method "%s" is not exists in model "%s"',
				'generateExportFile()',
				$this->_model->name
			));
		}
		if (!method_exists($this->_model, 'getExportFilename')) {
			throw new InternalErrorException(__d(
				'view_extension',
				'Method "%s" is not exists in model "%s"',
				'getExportFilename()',
				$this->_model->name
			));
		}

		$tempFile = $this->_model->generateExportFile($id);
		if (empty($tempFile)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid generated file'));
		}

		$previewFile = $tempFile;
		$exportPath = $this->_getExportDir();
		$pdfTempFile = $exportPath . uniqid() . '.pdf';
		if ($this->_convertFileToPdf($tempFile, $pdfTempFile)) {
			$previewFile = $pdfTempFile;
		}

		$exportFileName = $this->_model->getExportFilename($id, false);
		$preview = $this->_createImgPreview($previewFile);
		$orig = pathinfo($tempFile, PATHINFO_FILENAME);
		$pdf = null;
		if (!empty($pdfTempFile)) {
			$pdf = pathinfo($pdfTempFile, PATHINFO_FILENAME);
		}

		$download = compact('orig', 'pdf');
		$result = compact('exportFileName', 'preview', 'download');

		return $result;
	}

/**
 * Converting input file into PDf file
 *
 * @param string $inputFile Input file for converting.
 * @param string $outputFile Output file of converting.
 * @throws InternalErrorException if Unoconv is not configured.
 * @throws InternalErrorException if input file is
 *  not exists.
 * @throws InternalErrorException if empty path to output file.
 * @throws InternalErrorException if PDF file is not created.
 * @return bool Return True, if input file is successfully converted
 *  into PDF file. False otherwise.
 */
	protected function _convertFileToPdf($inputFile = null, $outputFile = null) {
		$cfgUnoconv = $this->_getUnoconvConfig();
		if ($cfgUnoconv === false) {
			throw new InternalErrorException(__d('view_extension', 'Unoconv is not configured'));
		}

		if (!file_exists($inputFile)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid input file for converting to PDF'));
		}

		if (empty($outputFile)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid output file for converting to PDF'));
		}

		if (mime_content_type($inputFile) === 'application/pdf') {
			return false;
		}

		extract($cfgUnoconv);
		if ($timeout <= 0) {
			$timeout = 60;
		}
		$unoconvOpt = [
			'timeout' => $timeout,
			'unoconv.binaries' => $binaries,
		];
		$unoconv = Unoconv\Unoconv::create($unoconvOpt);
		$unoconv->transcode($inputFile, 'pdf', $outputFile);
		if (!file_exists($outputFile)) {
			throw new InternalErrorException(__d('view_extension', 'Error on creation PDF file'));
		}

		return true;
	}

/**
 * Creating images of every page for preview file
 *
 * @param string $inputFile Input file for creating images.
 * @throws InternalErrorException if empty path to www_root folder.
 * @throws InternalErrorException if Path to preview folder is not
 *  contain path to www_root folder.
 * @throws InternalErrorException if empty path to preview folder.
 * @return array Return array of relative path to preview images of pages.
 */
	protected function _createImgPreview($inputFile = null) {
		$result = [];
		if (empty($inputFile) || !file_exists($inputFile)) {
			return $result;
		}

		$previewFullPath = $this->_getPreviewDir();
		$wwwRoot = Configure::read('App.www_root');
		if (empty($wwwRoot)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid path to "www_root" directory'));
		}

		$wwwRootPos = mb_stripos($previewFullPath, $wwwRoot);
		if ($wwwRootPos !== 0) {
			throw new InternalErrorException(__d('view_extension', 'Path to preview directory is not contain path to "www_root" directory'));
		}

		$previewWebRootPath = mb_substr($previewFullPath, mb_strlen($wwwRoot) - 1);
		if (empty($previewWebRootPath)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid path to preview directory'));
		}

		$jpgTempFile = uniqid();
		$im = new Imagick();
		$im->setResolution(150, 150);
		if (!$im->readimage($inputFile)) {
			$im->clear();
			$im->destroy();

			return $result;
		}

		$numPages = $im->getNumberImages();
		for ($page = 0; $page < $numPages; $page++) {
			$postfix = '[' . $page . ']';
			$previewFile = $jpgTempFile . $postfix . '.jpg';
			if (!$im->readimage($inputFile . $postfix)) {
				continue;
			}

			$im->setImageFormat('jpeg');
			$im->setImageCompressionQuality(90);
			if ($im->writeImage($previewFullPath . $previewFile)) {
				$result[] = $previewWebRootPath . $previewFile;
			}
		}

		$im->clear();
		$im->destroy();
		if (!empty($result) && (DIRECTORY_SEPARATOR === '\\')) {
			foreach ($result as &$resultItem) {
				$resultItem = mb_ereg_replace('\\\\', '/', $resultItem);
			}
		}

		return $result;
	}

/**
 * Set path to export directory
 *
 * @param string $path Path to export directory
 * @return void
 */
	protected function _setExportDir($path = null) {
		$path = (string)$path;
		if (file_exists($path)) {
			$this->_pathExportDir = $path;
		}
	}

/**
 * Return path to export directory
 *
 * @return string Path to export directory
 */
	protected function _getExportDir() {
		return (string)$this->_pathExportDir;
	}

/**
 * Set path to preview directory
 *
 * @param string $path Path to preview derictory
 * @return void
 */
	protected function _setPreviewDir($path = null) {
		$path = (string)$path;
		if (file_exists($path)) {
			$this->_pathPreviewDir = $path;
		}
	}

/**
 * Return path to preview directory
 *
 * @return string Path to preview directory
 */
	protected function _getPreviewDir() {
		return (string)$this->_pathPreviewDir;
	}

/**
 * Set time for storage old exported files
 *
 * @param int $time Time limit in seconds for storage exported file.
 * @return void
 */
	protected function _setStorageTimeExport($time = null) {
		$time = (int)$time;
		if ($time > 0) {
			$this->_storageTimeExport = $time;
		}
	}

/**
 * Return time for storage old exported files
 *
 * @return int Time for storage old exported files
 */
	protected function _getStorageTimeExport() {
		return (int)$this->_storageTimeExport;
	}

/**
 * Set time for storage old preview files
 *
 * @param int $time limit in seconds for storage preview file.
 * @return void
 */
	protected function _setStorageTimePreview($time = null) {
		$time = (int)$time;
		if ($time > 0) {
			$this->_storageTimePreview = $time;
		}
	}

/**
 * Return time for storage old preview files
 *
 * @return int Time for storage old preview files
 */
	protected function _getStorageTimePreview() {
		return (int)$this->_storageTimePreview;
	}

/**
 * Downloading preview file
 *
 * @param int|string $id ID for downloaded data.
 * @param string $file File name of downloaded file.
 * @throws InternalErrorException if downloaded is not exists.
 * @return CakeRequest Return CakeRequest object for download request.
 */
	public function download($id = null, $file = null) {
		$type = $this->_controller->RequestHandler->prefers();
		$exportFileName = null;
		if (method_exists($this->_model, 'getExportFilename')) {
			$exportFileName = $this->_model->getExportFilename($id, true);
		}

		$exportPath = $this->_getExportDir();
		$downloadFile = $exportPath . $file;
		if (!empty($type)) {
			$downloadFile .= '.' . $type;
		}

		if (!file_exists($downloadFile)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid file for downloading'));
		}

		if (!empty($type) && !empty($exportFileName)) {
			$this->_controller->response->type($type);
			$exportFileName .= '.' . $type;
		}
		$responseOpt = [
			'download' => true,
		];
		if (!empty($exportFileName)) {
			if ($this->_controller->request->is('msie')) {
				$exportFileName = rawurlencode($exportFileName);
			}
			$responseOpt['name'] = $exportFileName;
		}
		$this->_controller->response->file($downloadFile, $responseOpt);

		return $this->_controller->response;
	}

/**
 * Cleanup the export directory from old files
 *
 * @param string $type Type of target directory. One of:
 *  - `export`;
 *  - `preview`;
 * @param int $timeNow Timestamp of current date and time.
 * @return bool Success
 */
	protected function _clearDir($type = null, $timeNow = null) {
		switch (mb_strtolower($type)) {
			case 'export':
				$clearPath = $this->_getExportDir();
				$storageTime = $this->_getStorageTimeExport();
				break;
			case 'preview':
				$clearPath = $this->_getPreviewDir();
				$storageTime = $this->_getStorageTimePreview();
				break;
			default:
				return false;
		}

		$result = true;
		$oFolder = new Folder($clearPath, true);
		$exportFiles = $oFolder->find('.*', false);
		if (empty($exportFiles)) {
			return $result;
		}

		if (!empty($timeNow)) {
			$timeNow = (int)$timeNow;
		} else {
			$timeNow = time();
		}

		$exportFilesPath = $oFolder->pwd();
		foreach ($exportFiles as $exportFile) {
			$oFile = new File($exportFilesPath . DS . $exportFile);
			$lastChangeTime = $oFile->lastChange();
			if ($lastChangeTime === false) {
				continue;
			}

			if (($timeNow - $lastChangeTime) > $storageTime) {
				if (!$oFile->delete()) {
					$result = false;
				}
			}
		}

		return true;
	}

/**
 * Return configuration of Unoconv
 *
 * @return array|bool Return array of Unoconv configuration,
 *  or False on failure.
 */
	protected function _getUnoconvConfig() {
		$cfgUnoconv = $this->_modelConfigTheme->getUnoconvConfig();
		if (empty($cfgUnoconv)) {
			return false;
		}

		$timeout = (int)Hash::get($cfgUnoconv, 'timeout');
		$binaries = (string)Hash::get($cfgUnoconv, 'binaries');

		$result = compact('timeout', 'binaries');

		return $result;
	}

/**
 * Readiness check use the configuration Unoconv
 *
 * @return bool Return True, if configuration Unoconv is ready.
 *  False otherwise.
 */
	public function isUnoconvReady() {
		$cfgUnoconv = $this->_getUnoconvConfig();
		if ($cfgUnoconv === false) {
			return false;
		}

		if (!file_exists($cfgUnoconv['binaries'])) {
			return false;
		}

		return true;
	}
}
