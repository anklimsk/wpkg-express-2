<?php
/**
 * This file is the componet file of the plugin.
 * Upload files to server.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Hash', 'Utility');
App::import(
	'Vendor',
	'CakeTheme.UploadHandler',
	['file' => 'UploadHandler' . DS . 'UploadHandler.php']
);

/**
 * Upload Component.
 *
 * AJAX upload files to server.
 * @package plugin.Controller.Component
 */
class UploadComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Maximum time of store uploaded file
 *
 * @var int
 */
	protected $_storageTimeUpload = 600;

/**
 * Path to upload directory
 *
 * @var string
 */
	protected $_pathUploadDir = CAKE_THEME_UPLOAD_DIR;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$settingsMethods = [
			'storageTimeUpload' => '_setStorageTimeUpload',
			'uploadDir' => '_setUploadDir',
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
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
	}

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * Actions:
 *  - Cleanup the upload directories from old files.
 *
 * @param Controller $controller Controller instance.
 * @return void
 */
	public function startup(Controller $controller) {
		$this->_clearDir();
	}

/**
 * Set time for storage old uploaded files
 *
 * @param int $time Time limit in seconds for storage uploaded file.
 * @return void
 */
	protected function _setStorageTimeUpload($time = null) {
		$time = (int)$time;
		if ($time > 0) {
			$this->_storageTimeUpload = $time;
		}
	}

/**
 * Return time for storage old uploaded files
 *
 * @return int Time for storage old uploaded files
 */
	protected function _getStorageTimeUpload() {
		return (int)$this->_storageTimeUpload;
	}

/**
 * Set path to upload directory
 *
 * @param string $path Path to upload directory
 * @return void
 */
	protected function _setUploadDir($path = null) {
		$path = (string)$path;
		if (file_exists($path)) {
			$this->_pathUploadDir = $path;
		}
	}

/**
 * Return path to upload directory
 *
 * @return string Path to upload directory
 */
	public function getUploadDir() {
		return (string)$this->_pathUploadDir;
	}

/**
 * Cleanup the upload directory from old files
 *
 * @param int $timeNow Timestamp of current date and time.
 * @return bool Success
 */
	protected function _clearDir($timeNow = null) {
		$clearPath = $this->getUploadDir();
		$storageTime = $this->_getStorageTimeUpload();

		$result = true;
		$oFolder = new Folder($clearPath, true);
		$uploadedFiles = $oFolder->find('.*', false);
		if (empty($uploadedFiles)) {
			return $result;
		}

		if (!empty($timeNow)) {
			$timeNow = (int)$timeNow;
		} else {
			$timeNow = time();
		}

		$uploadedFilesPath = $oFolder->pwd();
		foreach ($uploadedFiles as $uploadedFile) {
			$oFile = new File($uploadedFilesPath . DS . $uploadedFile);
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
 * AJAX upload file to server
 *
 * @param int $maxFileSize Maximum size of uploaded file
 * @param string $acceptFileTypes The regular expression pattern
 *  to validate uploaded files.
 *  Default `/.+$/i`.
 * @throws BadRequestException If request is not `AJAX` or not `JSON`
 * @throws MethodNotAllowedException If request is not in list:
 *  - `OPTIONS`;
 *  - `HEAD`;
 *  - `GET`;
 *  - `PATCH`;
 *  - `PUT`;
 *  - `POST`.
 * @return array Result of upload file
 * @link https://github.com/blueimp/jQuery-File-Upload
 */
	public function upload($maxFileSize = null, $acceptFileTypes = null) {
		Configure::write('debug', 0);
		if (!$this->_controller->request->is('ajax') || !$this->_controller->RequestHandler->prefers('json')) {
			throw new BadRequestException(__d('view_extension', 'Invalid request'));
		}

		$uploadDir = $this->getUploadDir();
		if (empty($acceptFileTypes)) {
			$acceptFileTypes = '/.+$/i';
		}

		$opt = [
			'script_url' => '',
			'upload_url' => '',
			'param_name' => 'files',
			'upload_dir' => $uploadDir,
			'max_file_size' => $maxFileSize,
			'image_versions' => [
				'' => ['auto_orient' => false]
			],
			'correct_image_extensions' => false,
			'accept_file_types' => $acceptFileTypes
		];
		$uploadHandler = new UploadHandler($opt, false);

		switch ($this->_controller->request->method()) {
			case 'OPTIONS':
			case 'HEAD':
				$data = $uploadHandler->head(false);
				break;
			case 'GET':
				$data = $uploadHandler->get(false);
				break;
			case 'PATCH':
			case 'PUT':
			case 'POST':
				$data = $uploadHandler->post(false);
				break;
			default:
				throw new MethodNotAllowedException();
		}

		return $data;
	}
}
