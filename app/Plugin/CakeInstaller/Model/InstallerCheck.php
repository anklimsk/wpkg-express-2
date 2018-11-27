<?php
/**
 * This file is the model file of the plugin.
 * Checks for installer model.
 * Methods to check installation of application
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeInstallerAppModel', 'CakeInstaller.Model');
App::uses('ConnectionManager', 'Model');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeSchema', 'Model');

/**
 * InstallerCheck for installer.
 *
 * @package plugin.Model
 */
class InstallerCheck extends CakeInstallerAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'InstallerCheck';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Object of model `ConfigInstaller`
 *
 * @var object
 */
	protected $_modelConfigInstaller = null;

/**
 * Path to marker file for checking application is installed.
 *
 * @var string
 */
	public $markerFileInstalled = CAKE_INSTALLER_MARKER_FILE_INSTALLED;

/**
 * Path to marker file for checking if need restart installation process.
 *
 * @var string
 */
	public $markerFileRestart = CAKE_INSTALLER_MARKER_FILE_RESTART;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct();

		$this->_modelConfigInstaller = ClassRegistry::init('CakeInstaller.ConfigInstaller');
	}

/**
 * Return path to marker file for checking application is installed.
 *
 * @return string Return path to marker file.
 */
	public function getPathMarkerFileIsInstalled() {
		return (string)$this->markerFileInstalled;
	}

/**
 * Return path to marker file for checking if need restart
 *  installation process.
 *
 * @return string Return path to marker file.
 */
	public function getPathMarkerFileNeedRestart() {
		return (string)$this->markerFileRestart;
	}

/**
 * Return WEB server user.
 *
 * @return string Return user name of WEB server
 */
	public function getWebSrvUser() {
		$result = '';
		if ($this->isOsWindows()) {
			return $result;
		}

		$cmd = "ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1 2>/dev/null";
		$output = [];
		$exitCode = -1;
		exec($cmd, $output, $exitCode);
		if (($exitCode !== 0) || (count($output) < 1)) {
			return $result;
		}

		$result = array_shift($output);

		return $result;
	}

/**
 * Return group of user.
 *
 * @param string $username Username for retrieving group.
 * @return string Return group of user
 */
	public function getWebSrvUserGroup($username = null) {
		$result = '';
		if ($this->isOsWindows() || empty($username)) {
			return $result;
		}

		$cmd = 'groups ' . $username . ' | head -1 | cut -d\  -f1 2>/dev/null';
		$output = [];
		$exitCode = -1;
		exec($cmd, $output, $exitCode);
		if (($exitCode !== 0) || (count($output) < 1)) {
			return $result;
		}

		$result = array_shift($output);

		return $result;
	}

/**
 * Checking OS is Windows.
 *
 * @return bool Return True, if OS is Windows.
 *  Otherwise return False.
 */
	public function isOsWindows() {
		if (DIRECTORY_SEPARATOR === '\\') {
			return true;
		} else {
			return false;
		}
	}

/**
 * Check application is installed successfully
 *
 * @param string $configKey The identifier to check configuration
 *  for application.
 * @param bool $createMarkerFile If True, create marker file,
 *  if application is installed successfully.
 * @return bool True, if application is installed successfully.
 *  False otherwise.
 */
	public function isAppInstalled($configKey = null, $createMarkerFile = true) {
		$pathMarkerFileIsInstalled = $this->getPathMarkerFileIsInstalled();
		if ($this->_checkMarkerFile($pathMarkerFileIsInstalled)) {
			return true;
		}

		$installTasks = $this->_modelConfigInstaller->getListInstallerTasks();
		if (empty($installTasks)) {
			return false;
		}

		if (in_array('check', $installTasks) && ($this->checkPhpVersion() === false)) {
			return false;
		}

		if (in_array('check', $installTasks) && ($this->checkPhpExtensions(true) === false)) {
			return false;
		}

		if (in_array('configdb', $installTasks) && ($this->checkConnectDb(null, true) === false)) {
			return false;
		}

		if (in_array('createdb', $installTasks) && !$this->checkDbTableExists()) {
			return false;
		}

		if (in_array('createsymlinks', $installTasks) && !$this->checkSymLinksExists()) {
			return false;
		}

		if (in_array('createcronjobs', $installTasks) && !$this->checkCronJobsExists()) {
			return false;
		}

		if (!empty($configKey) && !Configure::check($configKey)) {
			return false;
		}

		if (!$createMarkerFile) {
			return true;
		}

		return $this->_createMarkerFile($pathMarkerFileIsInstalled);
	}

/**
 * Check application is ready to install
 *
 * @return bool True, if application is ready to install.
 *  False otherwise.
 */
	public function isAppReadyToInstall() {
		$checkPHPversion = $this->checkPhpVersion();
		if ($checkPHPversion === false) {
			return false;
		}

		$checkPHPextensions = $this->checkPhpExtensions(true);
		if ($checkPHPextensions === false) {
			return false;
		}

		return true;
	}

/**
 * Check version of PHP
 *
 * @return null|bool Return Null, if checking is not configured.
 *  True, if PHP version is compatible. False otherwise.
 */
	public function checkPhpVersion() {
		$phpVesion = $this->_modelConfigInstaller->getPhpVersionConfig();
		if (empty($phpVesion)) {
			return null;
		}

		$result = true;
		foreach ($phpVesion as $phpVesionItem) {
			if (!is_array($phpVesionItem)) {
				continue;
			}

			$phpVesionItem += ['', null];
			list($version, $operator) = $phpVesionItem;
			if (empty($operator)) {
				$operator = '==';
			}

			$checkResult = false;
			if (!empty($version)) {
				$checkResult = version_compare(PHP_VERSION, $version, $operator);
			}
			if (!$checkResult) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Check extension of PHP
 *
 * @param bool $returnBool If True, return boolean. Otherwise,
 *  return array otherwise in format:
 *   - key - PHP extension name;
 *   - value - check result in format:
 *	 - 0 - Bad;
 *	 - 1 - Ok - Warning;
 *	 - 2 - Ok - Success.
 * @return null|bool|array Return Null, if checking is not configured.
 */
	public function checkPhpExtensions($returnBool = false) {
		$modules = $this->_modelConfigInstaller->getPhpExtensionsConfig();
		if (empty($modules)) {
			return null;
		}

		$result = [];
		$resultBool = true;
		foreach ($modules as $module) {
			if (empty($module) || !is_array($module)) {
				continue;
			}

			$module += ['', true];
			list($moduleName, $critical) = $module;
			$checkResult = false;
			if (!empty($moduleName)) {
				$checkResult = extension_loaded($moduleName);
			}

			if ($checkResult) {
				$result[$moduleName] = 2;
			} else {
				if (!$critical) {
					$result[$moduleName] = 1;
				} else {
					$result[$moduleName] = 0;
					$resultBool = false;
				}
			}
		}
		if ($returnBool) {
			return $resultBool;
		}

		return $result;
	}

/**
 * Return list of paths for checking writing
 *
 * @return Return array list of paths
 */
	protected function _getFilesForCheckingWritable() {
		$result = [
			TMP,
			CONFIG . 'config.php'
		];

		return $result;
	}

/**
 * Check extension of PHP
 *
 * @param bool $returnBool If True, return boolean. Otherwise,
 *  return array otherwise in format:
 *   - key - PHP extension name;
 *   - value - check result in format:
 *	 - 0 - Bad;
 *	 - 1 - Ok - Warning;
 *	 - 2 - Ok - Success.
 * @return null|bool|array Return Null, if checking is not configured.
 */
	public function checkFilesWritable($returnBool = false) {
		$targetFiles = $this->_getFilesForCheckingWritable();
		if (empty($targetFiles)) {
			return null;
		}

		$result = [];
		$resultBool = true;
		foreach ($targetFiles as $targetFile) {
			if (!file_exists($targetFile)) {
				$state = false;
			} else {
				$state = is_writable($targetFile);
			}
			$resultBool = $resultBool && $state;
			$result[$targetFile] = $state;
		}
		if ($returnBool) {
			return $resultBool;
		}

		return $result;
	}

/**
 * Return list of configured database connection.
 *
 * @param string $path Path to database connection configuration file.
 * @return array List of configured database connection.
 */
	public function getListDbConn($path = null) {
		if (empty($path)) {
			$path = APP;
		}

		$configFile = $path . 'Config' . DS . 'database.php';
		$connections = [];
		if (file_exists($configFile)) {
			$connections = array_keys(ConnectionManager::enumConnectionObjects());
		}

		return $connections;
	}

/**
 * Check connections to database
 *
 * @param string $path Path to database connection configuration file.
 * @param bool $returnBool If True, return boolean. Otherwise,
 *  return array otherwise in format:
 *   - key - database connection name;
 *   - value - True, if connection success. Otherwise False or Array or
 *     error messages.
 * @return null|bool|array Return Null, if checking is not configured.
 */
	public function checkConnectDb($path = null, $returnBool = false) {
		$connections = $this->getListDbConn($path);
		if (empty($connections)) {
			return null;
		}

		$cfgConnections = $this->_modelConfigInstaller->getListDbConnConfigs();
		if (empty($cfgConnections)) {
			return null;
		}

		$connections = array_intersect($connections, $cfgConnections);
		$notCfgConnections = array_diff($cfgConnections, $connections);
		$result = [];
		$resultBool = true;
		foreach ($connections as $connectionName) {
			try {
				//@codingStandardsIgnoreStart
				$ds = @ConnectionManager::getDataSource($connectionName);
				//@codingStandardsIgnoreEnd
				$checkResult = $ds->isConnected();
			} catch (Exception $exception) {
				$checkResult = [$exception->getMessage()];
				$attributes = $exception->getAttributes();
				if (isset($attributes['message'])) {
					$checkResult[] = $attributes['message'];
				}
				$resultBool = false;
			}
			if (!$checkResult) {
				$resultBool = false;
			}
			$result[$connectionName] = $checkResult;
		}
		foreach ($notCfgConnections as $connectionName) {
			$result[$connectionName] = false;
			$resultBool = false;
		}
		if ($returnBool) {
			return $resultBool;
		}

		return $result;
	}

/**
 * Check database tables exists
 *
 * @return bool Success
 */
	public function checkDbTableExists() {
		$ds = $this->getDataSource();
		$existsTables = $ds->listSources();
		if (empty($existsTables)) {
			return false;
		}

		$schemaList = [];
		$schemaCheckingList = [''];
		$schemaCheckingListCfg = $this->_modelConfigInstaller->getListSchemaChecking();
		if (!empty($schemaCheckingList)) {
			$schemaCheckingList = array_merge($schemaCheckingList, $schemaCheckingListCfg);
		}
		foreach ($schemaCheckingList as $schemaCheckingListItem) {
			$name = null;
			$path = null;
			$file = null;
			$connection = null;
			$plugin = null;

			if (preg_match('/[\s\b]?\-\-name\s+(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				list($plugin, $name) = pluginSplit($matches[1]);
			} elseif (preg_match('/^(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				$name = $matches[1];
			}

			if (preg_match('/[\s\b]?(?:\-p|\-\-plugin)\s+(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				$plugin = $matches[1];
			}

			if (preg_match('/[\s\b]?(?:\-c|\-\-connection)\s+(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				$connection = $matches[1];
			}

			if (preg_match('/[\s\b]?\-\-path\s+(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				$path = $matches[1];
			}

			if (preg_match('/[\s\b]?\-\-file\s+(\w+)/', $schemaCheckingListItem, $matches) && (count($matches) == 2)) {
				$file = $matches[1];
			}

			if (!empty($name)) {
				$file = Inflector::underscore($name);
			} elseif (empty($file)) {
				$file = 'schema.php';
			}

			if (strpos($file, '.php') === false) {
				$file .= '.php';
			}

			if (!empty($plugin) && empty($name)) {
				$name = $plugin;
			}

			$name = Inflector::camelize($name);
			$schemaList[] = compact('name', 'path', 'file', 'connection', 'plugin');
		}

		$existsTables = array_flip($existsTables);
		foreach ($schemaList as $schemaListItem) {
			$Schema = new CakeSchema($schemaListItem);
			$oldSchema = $Schema->load();
			if (!$oldSchema) {
				return false;
			}

			$diffTables = array_diff_key($oldSchema->tables, $existsTables);
			if (!empty($diffTables)) {
				return false;
			}
			unset($Schema);
		}

		return true;
	}

/**
 * Check symbolic links exists
 *
 * @return bool Success
 */
	public function checkSymLinksExists() {
		$symlinksList = $this->_modelConfigInstaller->getListSymlinksCreation();
		if (empty($symlinksList)) {
			return true;
		}

		foreach ($symlinksList as $link => $target) {
			if (empty($link)) {
				continue;
			}

			if (!file_exists($link) || (!is_link($link) && (DS !== '\\'))) {
				return false;
			}

			if (stripos(readlink($link), $target) !== 0) {
				return false;
			}
		}

		return true;
	}

/**
 * Check cron jobs exists
 *
 * @return bool Success
 */
	public function checkCronJobsExists() {
		if ($this->isOsWindows()) {
			return true;
		}

		$apacheUser = $this->getWebSrvUser();
		if (empty($apacheUser)) {
			return false;
		}

		$cronjobsList = $this->_modelConfigInstaller->getListCronJobsCreation();
		if (empty($cronjobsList)) {
			return true;
		}

		$output = [];
		$exitCode = -1;
		$cmd = 'crontab -u ' . $apacheUser . ' -l 2>/dev/null';
		exec($cmd, $output, $exitCode);
		if ($exitCode !== 0) {
			return false;
		}

		if (empty($output)) {
			return false;
		}

		foreach ($cronjobsList as $cmd => $time) {
			if (empty($cmd)) {
				continue;
			}

			if (count(preg_grep('/^[^#]+' . preg_quote($cmd, '/') . '$/', $output)) == 0) {
				return false;
			}
		}

		return true;
	}

/**
 * Check need restart installation process
 *
 * @return bool True, if need restart installation process.
 *  False otherwise.
 */
	public function isNeedRestart() {
		$pathMarkerFileNeedRestart = $this->getPathMarkerFileNeedRestart();

		return $this->_checkMarkerFile($pathMarkerFileNeedRestart);
	}

/**
 * Create marker file for checking if need restart installation process.
 *
 * @return bool True, if file create success. False otherwise.
 */
	public function setNeedRestart() {
		$pathMarkerFileNeedRestart = $this->getPathMarkerFileNeedRestart();

		return $this->_createMarkerFile($pathMarkerFileNeedRestart);
	}

/**
 * Check marker file exists
 *
 * @param string $path Path to marker file.
 * @return bool True, if check success. False otherwise.
 */
	protected function _checkMarkerFile($path = null) {
		if (empty($path)) {
			return false;
		}

		return file_exists($path);
	}

/**
 * Create marker file
 *
 * @param string $path Path to marker file.
 * @return bool True, if file create success. False otherwise.
 */
	protected function _createMarkerFile($path = null) {
		if (empty($path)) {
			return false;
		}

		$oFile = new File($path, true);
		$now = time();
		$data = CakeTime::i18nFormat($now, '%x %X');
		if (!$oFile->write($data, 'w', true)) {
			return false;
		}

		return $oFile->close();
	}

/**
 * Remove marker file
 *
 * @param string $path Path to marker file.
 * @return bool True, if file removed successful. False otherwise.
 */
	protected function _removeMarkerFile($path) {
		if (empty($path)) {
			return false;
		}

		$oFile = new File($path, false);
		if (!$oFile->exists()) {
			return false;
		}

		return $oFile->delete();
	}

/**
 * Remove marker file for checking application is installed.
 *
 * @return bool True, if file removed successful. False otherwise.
 */
	public function removeMarkerFileIsInstalled() {
		$path = $this->getPathMarkerFileIsInstalled();

		return $this->_removeMarkerFile($path);
	}

/**
 * Remove marker file for checking if need restart installation process.
 *
 * @return bool True, if file removed successful. False otherwise.
 */
	public function removeMarkerFileNeedRestart() {
		$path = $this->getPathMarkerFileNeedRestart();

		return $this->_removeMarkerFile($path);
	}
}
