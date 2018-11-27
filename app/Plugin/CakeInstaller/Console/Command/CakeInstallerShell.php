<?php
/**
 * This file is the console shell file of the plugin.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command
 */

App::uses('AppShell', 'Console/Command');
App::uses('CakeInstallerShellTrait', 'CakeInstaller.Utility');
App::uses('Security', 'Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('String', 'Utility');
App::uses('CakeText', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeSchema', 'Model');
App::uses('Language', 'CakeBasicFunctions.Utility');

/**
 * This shell is used to install application.
 *
 * @package plugin.Console.Command
 */
class CakeInstallerShell extends AppShell {

	use CakeInstallerShellTrait;

/**
 * Contains models to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$uses
 */
	public $uses = [
		'CakeInstaller.InstallerCheck',
		'CakeInstaller.ConfigInstaller',
	];

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$tasks
 */
	public $tasks = [
		'Project',
		'CakeInstaller.DbConfigExtend'
	];

/**
 * Path to application
 *
 * @var string
 */
	public $path = APP;

/**
 * Progress helper
 *
 * @var object
 */
	public $progress = null;

/**
 * State helper
 *
 * @var object
 */
	public $state = null;

/**
 * Waiting helper
 *
 * @var object
 */
	public $waiting = null;

/**
 * Console width
 *
 * @var int
 */
	public $maxWidth = 63;

/**
 * Flag of using notification if action is completed
 *
 * @var bool
 */
	public $useActionNotify = true;

/**
 * Object of model `InstallerCompleted`
 *
 * @var object
 */
	public $InstallerCompleted = null;

/**
 * Initializes the Shell
 * acts as constructor for subclasses
 * allows configuration of tasks prior to shell execution
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::initialize
 */
	public function initialize() {
		Configure::write('Cache.disable', true);
		$this->_initUiLang();
		parent::initialize();

		$this->InstallerCompleted = ClassRegistry::init('InstallerCompleted', true);
		if ($this->InstallerCompleted === false) {
			$this->InstallerCompleted = ClassRegistry::init('CakeInstaller.InstallerCompleted');
		}

		$this->progress = $this->helper('Progress');
		$this->state = $this->helper('CakeInstaller.State');
		$this->waiting = $this->helper('CakeInstaller.Waiting');
	}

/**
 * Gets the option parser instance and configures it.
 *
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$yes = [
			'short' => 'y',
			'help' => __d('cake_installer', 'Do not prompt for confirmation. Be careful!'),
			'boolean' => true
		];
		$parserDefault = ['options' => compact('yes')];
		$optionParser = parent::getOptionParser();

		$installerCommands = $this->ConfigInstaller->getListInstallerCommands(false);
		if (empty($installerCommands)) {
			return $optionParser;
		}

		$excludeYesOptCmds = [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETUILANG,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETDIRPERMISS,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_INSTALL,
		];

		$subcommands = [];
		foreach ($installerCommands as $installerCommand => $help) {
			$parser = $parserDefault;
			if (in_array($installerCommand, $excludeYesOptCmds)) {
				unset($parser['options']['yes']);
			}
			$subcommands[$installerCommand] = compact('help', 'parser');
		}
		$optionParser->addSubcommands($subcommands);

		return $optionParser;
	}

/**
 * Check to see if this shell has a callable method by the given name.
 *
 * @param string $name The method name to check.
 * @return bool
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::hasMethod
 */
	public function hasMethod($name) {
		try {
			$method = new ReflectionMethod($this, $name);
			if (!$method->isPublic() || substr($name, 0, 1) === '_') {
				return false;
			}
			if ($method->getDeclaringClass()->name === 'Shell') {
				return false;
			}
			$installerCommands = $this->ConfigInstaller->getListInstallerCommands(false);
			$defaultCommands = ['main' => null];
			$commands = $installerCommands + $defaultCommands;
			if (!array_key_exists($name, $commands)) {
				return false;
			}

			return true;
		} catch (ReflectionException $e) {
			return false;
		}

		return true;
	}

/**
 * Load given shell helper class
 *
 * @param string $name Name of the helper class. Supports plugin syntax.
 * @return BaseShellHelper Instance of helper class
 * @throws RuntimeException If invalid class name is provided
 */
	public function helper($name) {
		if (isset($this->_helpers[$name])) {
			return $this->_helpers[$name];
		}
		list($plugin, $helperClassName) = pluginSplit($name, true);
		$helperClassName = Inflector::camelize($helperClassName) . "ShellHelper";
		App::uses($helperClassName, $plugin . "Console/Helper");
		if (!class_exists($helperClassName)) {
			throw new RuntimeException("Class " . $helperClassName . " not found");
		}
		$helper = new $helperClassName($this->stdout);
		$this->_helpers[$name] = $helper;

		return $helper;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function main() {
		$this->clear();
		$installerCommands = $this->ConfigInstaller->getListInstallerCommands(false);
		if (empty($installerCommands)) {
			$this->out('<error>' . __d('cake_installer', 'Empty command list. See config file.') . '</error>');

			return;
		}

		$isNeedRestart = $this->InstallerCheck->isNeedRestart();
		if ($isNeedRestart && $this->_checkSure(__d('cake_installer', 'Continue installation process?'), 'y')) {
			$this->InstallerCheck->removeMarkerFileNeedRestart();

			return $this->install();
		}

		$this->out(__d('cake_installer', 'Installer task Shell'));
		$this->hr();
		$inputMessage = __d('cake_installer', 'Input the number of command from list');
		$titleMessage = __d('cake_installer', 'Please choose command of installer:');
		$actionName = $this->inputFromList($this, $installerCommands, $inputMessage, $titleMessage, 'exit');
		$this->clear();
		$methodName = '_' . $actionName;
		call_user_func([$this, $methodName]);
	}

/**
 * Asks the user if he is sure he wants to perform this action.
 *
 * @param string $message Question text
 * @param string $default Default response, e.g.: `y` or `n`.
 * @return bool Return True, if user is sure. Otherwise return False.
 */
	protected function _checkSure($message = null, $default = null) {
		if (empty($message)) {
			$message = __d('cake_installer', 'Are you sure?');
		}
		if (empty($default) || !in_array($default, ['y', 'n'])) {
			$default = 'n';
		}
		$options = ['y', 'n'];

		if (!empty($this->param('yes')) || ($this->in($message, $options, $default) === 'y')) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Set installer UI language from settings file value.
 *
 * @return bool Success
 */
	protected function _initUiLang() {
		$path = $this->path;
		$UIlang = $this->_readConfigCore($path, '/(?<![\/]{2})Configure::write\(\'Config\.language\', \'([A-z]{3})\'\)[\s]*;/');
		if (empty($UIlang)) {
			$UIlang = 'eng';
		}

		if (!Configure::write('Config.language', $UIlang)) {
			return false;
		}

		if (!setlocale(LC_ALL, $UIlang)) {
			return false;
		}

		return true;
	}

/**
 * Method of command `install`.
 *
 * Is used to start the application installation.
 *
 * @return void
 */
	public function install() {
		$this->clear();
		$this->_install();

		return $this->_stop();
	}

/**
 * Method of command `setuilang`.
 *
 * Is used to setting application UI language.
 *
 * @return void
 */
	public function setuilang() {
		$this->_setuilang();

		return $this->_stop();
	}

/**
 * Method of command `check`.
 *
 * Is used to check loaded PHP extension.
 *
 * @return void
 */
	public function check() {
		$this->_check();

		return $this->_stop();
	}

/**
 * Method of command `setdirpermiss`.
 *
 * Is used to set access rights to folders and application files.
 *
 * @return void
 */
	public function setdirpermiss() {
		$this->_setdirpermiss();

		return $this->_stop();
	}

/**
 * Method of command `setsecurkey`.
 *
 * Is used to create and write security keys in the settings file.
 *
 * @return void
 */
	public function setsecurkey() {
		$this->_setsecurkey(false, false);

		return $this->_stop();
	}

/**
 * Method of command `settimezone`.
 *
 * Is used to create and write time zone in the settings file.
 *
 * @return void
 */
	public function settimezone() {
		$this->_settimezone();

		return $this->_stop();
	}

/**
 * Method of command `setbaseurl`.
 *
 * Is used to create and write base URL of application in the
 *  settings file.
 *
 * @return void
 */
	public function setbaseurl() {
		$this->_setbaseurl();

		return $this->_stop();
	}

/**
 * Method of command `configdb`.
 *
 * Is used to configure database connections.
 *
 * @return void
 */
	public function configdb() {
		$this->_configdb();

		return $this->_stop();
	}

/**
 * Method of command `createdb`.
 *
 * Used for deploying the database.
 *
 * @return void
 */
	public function createdb() {
		return $this->_createdb();
	}

/**
 * Method of command `createsymlinks`.
 *
 * Used for create symbolic links to files.
 *
 * @return void
 */
	public function createsymlinks() {
		return $this->_createsymlinks();
	}

/**
 * Method of command `createcronjobs`.
 *
 * Used for create cron jobs.
 *
 * @return void
 */
	public function createcronjobs() {
		return $this->_createcronjobs();
	}

/**
 * Method of command `connectdb`.
 *
 * Is used to check connection to database.
 *
 * @return void
 */
	public function connectdb() {
		$this->_connectdb();

		return $this->_stop();
	}

/**
 * Install this application.
 *
 * @see CakeInstallerShell::install() CakeInstallerShell::install() Install this application.
 * @return void
 */
	protected function _install() {
		$this->waiting->animateMessage();

		$installTasks = $this->ConfigInstaller->getListInstallerTasks();
		$this->waiting->animateMessage();
		if (empty($installTasks)) {
			$this->waiting->hideMessage();
			$this->out('<error>' . __d('cake_installer', 'Empty command list for action install. See config file.') . '</error>');

			return;
		}

		$installerCommands = $this->ConfigInstaller->getListInstallerCommands();
		$this->waiting->animateMessage();
		if (empty($installerCommands)) {
			$this->waiting->hideMessage();
			$this->out('<error>' . __d('cake_installer', 'Empty command list. See config file.') . '</error>');

			return;
		}

		$tasks = array_values(array_intersect($installerCommands, $installTasks));
		$this->waiting->animateMessage();
		if (empty($tasks)) {
			$this->waiting->hideMessage();
			$this->out('<error>' . __d('cake_installer', 'Invalid command list for action install. See config file.') . '</error>');

			return;
		}

		$this->waiting->hideMessage();
		$isAppInstalled = $this->InstallerCheck->isAppInstalled(null, false);
		if ($isAppInstalled) {
			if (!$this->_checkSure(__d('cake_installer', 'Application is installed. Reinstall?'))) {
				return;
			}

			$this->InstallerCheck->removeMarkerFileIsInstalled();
		}

		$taskCount = count($tasks);
		$result = true;
		$this->progress->init([
			'total' => $taskCount,
			'width' => $this->maxWidth,
		]);
		for ($step = 0; $step < $taskCount; $step++) {
			$this->clear();
			$this->hr();
			$this->out('<info>' . __d('cake_installer', 'Intsall: step %d of %d', $step + 1, $taskCount) . '</info>');
			$this->out(null, 0);
			$this->progress->draw();
			$this->hr(1);
			$timeProcess = 0;
			$taskName = '_' . $tasks[$step];
			if (!is_callable([$this, $taskName])) {
				$result = false;
				break;
			} else {
				$timeStart = microtime(true);
				$stepResult = call_user_func([$this, $taskName], true);
				$timeEnd = microtime(true);
				$timeProcess = $timeEnd - $timeStart;
				if (is_null($stepResult)) {
					$this->out('<success>' . __d('cake_installer', 'To apply the changes, restart the installer.') . '</success>');
					$this->InstallerCheck->setNeedRestart();

					return $this->_stop();
				} elseif (!$stepResult) {
					$result = false;
					break;
				}
			}

			$this->progress->increment(1);
			if ($timeProcess < 0.2) {
				usleep(200000);
			}
		}
		if ($result) {
			$this->clear();
			$this->hr();
			$this->out('<info>' . __d('cake_installer', 'Intsall: completed') . '</info>');
			$this->out(null, 0);
			$this->progress->draw();
			$this->hr(1);
			if (!$this->InstallerCheck->isAppInstalled(null, true)) {
				$result = false;
			}
			if ($result) {
				$this->out('<info>' . __d('cake_installer', 'Running post-install actions') . '</info>');
				$this->waiting->animateMessage();
				if (!$this->InstallerCompleted->intsallCompleted()) {
					$result = false;
				}
				$this->waiting->hideMessage();
			}
		}
		if ($result) {
			$this->out('<success>' . __d('cake_installer', 'The installation process is completed successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'The installation process is completed unsuccessfully.') . '</error>');
		}
		$this->hr();
	}

/**
 * Configure database connections.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::configdb() CakeInstallerShell::configdb() Configure database connections.
 * @return null|bool Return Null, if neet restart task shell, or True on success, False otherwise.
 */
	protected function _configdb($check = false) {
		$this->out(__d('cake_installer', 'Configure database connections'));
		$this->hr();
		if ($check && ($this->InstallerCheck->checkConnectDb($this->path, true) === true)) {
			$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

			return true;
		}

		$result = $this->DbConfigExtend->execute();
		if ($check && $result) {
			$result = null;
		}

		return $result;
	}

/**
 * Create and write random key for `Security.key` in the settings file.
 *
 * @param string $path Base path of application
 * @see CakeInstallerShell::_setsecurkey() CakeInstallerShell::_setsecurkey() Setting security keys.
 * @return bool Success
 */
	protected function _securityKey($path = null) {
		if (empty($path)) {
			return false;
		}

		$oFile = new File($path . 'Config' . DS . 'core.php');
		if (!$oFile->exists()) {
			return false;
		}

		$contents = $oFile->read();
		$key = '';
		for ($i = 0; $i < 2; $i++) {
			$key .= substr(Security::generateAuthKey(), 0, 32);
		}

		return $this->_writeConfigCore(
			$path,
			'Configure::write(\'Security.key\', \'%s\')',
			$key,
			'A random numeric string (digits only) used to encrypt/decrypt strings.'
		);
	}

/**
 * Deploying the database.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::createdb() CakeInstallerShell::createdb() Deploying the database.
 * @return bool Success
 */
	protected function _createdb($check = false) {
		$this->out(__d('cake_installer', 'Creating database and initializing data'));
		$this->hr();

		$resultCheck = $this->InstallerCheck->checkDbTableExists();
		if ($resultCheck) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'All schemas exists. Re-create?'))) {
					return true;
				}
			}
		}

		$this->out(__d('cake_installer', 'Create schema of application'));
		$this->hr();
		$yesArg = '';
		if (!empty($this->param('yes'))) {
			$yesArg = ' --yes';
		}
		$defaultSchemaArgs = 'schema create --quiet' . $yesArg;
		$this->dispatchShell($defaultSchemaArgs);
		$this->hr();
		$this->nl(1);
		$schemaList = $this->ConfigInstaller->getListSchemaCreation();
		if (empty($schemaList)) {
			return true;
		}

		$this->out(__d('cake_installer', 'Create additional schemes'));
		foreach ($schemaList as $schemaName) {
			if (empty($schemaName)) {
				continue;
			}

			$this->hr();
			$this->out(__d('cake_installer', 'Create additional scheme: \'%s\'', $schemaName));
			$this->hr();
			$this->nl(1);
			$additionalSchemaArgs = 'schema create ' . $schemaName . ' --quiet' . $yesArg;
			$this->dispatchShell($additionalSchemaArgs);
		}
		$this->hr();
		$this->out('<info>' . __d('cake_installer', 'Creating additional schemes completed.') . '</info>');

		return true;
	}

/**
 * Create symbolic links to files.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::createsymlinks() CakeInstallerShell::createsymlinks() Create symbolic links to files.
 * @return bool Success
 */
	protected function _createsymlinks($check = false) {
		$this->out(__d('cake_installer', 'Creating symbolic links to files'));
		$this->hr();

		$resultCheck = $this->InstallerCheck->checkSymLinksExists();
		if ($resultCheck) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'All symbolic links to files exists. Rewrite?'))) {
					return true;
				}
			}
		}

		$symlinksList = $this->ConfigInstaller->getListSymlinksCreation();
		if (empty($symlinksList)) {
			$this->out('<info>' . __d('cake_installer', 'List of symbolic links is empty') . '</info>');

			return true;
		}

		$this->waiting->animateMessage();
		$truncateOpt = [
			'ellipsis' => '...',
			'exact' => true,
			'html' => false
		];
		$result = true;
		$messages = [];
		foreach ($symlinksList as $link => $target) {
			if (empty($link)) {
				continue;
			}

			$badTarget = ((empty($target) || !file_exists($target)) ? true : false);
			$linkExists = false;
			if (file_exists($link)) {
				if (is_link($link) && (stripos(readlink($link), $target) === 0)) {
					$linkExists = true;
				} else {
					if (is_dir($link)) {
						//@codingStandardsIgnoreStart
						@rmdir($link);
						//@codingStandardsIgnoreEnd
					} else {
						unlink($link);
					}
				}
			}

			//@codingStandardsIgnoreStart
			if ($linkExists || (!$badTarget && @symlink($target, $link))) {
				//@codingStandardsIgnoreEnd
				$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
			} else {
				$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
				$result = false;
			}

			$state = '[' . $state . ']';
			$message = ' * ' . CakeText::truncate($link, $this->maxWidth - 12, $truncateOpt);
			$messages[] = $this->state->getState($message, $state, $this->maxWidth - 1);
			$this->waiting->animateMessage();
		}
		if (empty($messages)) {
			$messages[] = '<info>' . __d('cake_installer', 'List of symbolic links is empty') . '</info>';
		}

		$this->waiting->hideMessage();
		$this->out($messages, 1);
		$this->hr();
		if ($result) {
			$this->out('<success>' . __d('cake_installer', 'Creating symbolic links completed successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Creating symbolic links unsuccessfully.') . '</error>');
		}

		return $result;
	}

/**
 * Create cron jobs.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::createcronjobs() CakeInstallerShell::createcronjobs() Create cron jobs.
 * @return bool Success
 */
	protected function _createcronjobs($check = false) {
		$this->out(__d('cake_installer', 'Creating cron jobs'));
		$this->hr();

		$result = true;
		if ($this->InstallerCheck->isOsWindows()) {
			$this->out(__d('cake_installer', 'Server OS is Windows. Creating cron jobs skipped.'));

			return $result;
		}

		$resultCheck = $this->InstallerCheck->checkCronJobsExists();
		if ($resultCheck) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'All cron jobs exists. Rewrite?'), 'n')) {
					return true;
				}
			}
		}

		$cronjobsList = $this->ConfigInstaller->getListCronJobsCreation();
		if (empty($cronjobsList)) {
			$this->out('<info>' . __d('cake_installer', 'List of cron jobs is empty') . '</info>');

			return true;
		}

		$apacheUser = $this->InstallerCheck->getWebSrvUser();
		if (empty($apacheUser)) {
			$this->out('<error>' . __d('cake_installer', 'Error on getting the username of the web server process') . '</error>');

			return false;
		}

		$tmpFile = TMP . 'installer' . DS . uniqid('cron_');
		$tmpCrurrFile = TMP . 'installer' . DS . uniqid('cron_');
		$bkpFile = TMP . 'installer' . DS . 'crontab.bkp';
		$output = [];
		$exitCode = -1;
		$cmd = 'crontab -u ' . $apacheUser . ' -l >' . $tmpFile . ' 2>/dev/null';
		exec($cmd, $output, $exitCode);
		if (($exitCode !== 0) && !$this->_createCrontabFile($tmpFile)) {
			$this->out('<error>' . __d('cake_installer', 'Error on getting crontab for user \'%s\'', $apacheUser) . '</error>');

			return false;
		}

		$tailOpt = [
			'ellipsis' => '...',
			'exact' => true,
			'html' => false
		];
		$messages = [];
		$oFileCurr = new File($tmpFile, true);
		if (!$oFileCurr->copy($tmpCrurrFile)) {
			$this->out('<error>' . __d('cake_installer', 'Error on creation copy of current crontab.') . '</error>');
			$oFileCurr->delete();

			return false;
		}
		$oFileBkp = new File($tmpCrurrFile, false);

		$needUpdate = false;
		$cronFile = $oFileCurr->read();
		foreach ($cronjobsList as $cmd => $time) {
			if (empty($cmd) || empty($time)) {
				continue;
			}

			$jobState = false;
			if (!empty($cronFile)) {
				if (preg_match('/^[^#]+' . preg_quote($cmd, '/') . '$/m', $cronFile)) {
					$jobState = true;
				} else {
					$jobState = $oFileCurr->append($time . ' ' . $cmd . "\n");
					$needUpdate = true;
				}
			} else {
				$jobState = $oFileCurr->append($time . ' ' . $cmd . "\n");
				$needUpdate = true;
			}
			if ($jobState) {
				$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
			} else {
				$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
				$result = false;
			}

			$state = '[' . $state . ']';
			$message = ' * ' . CakeText::tail($cmd, $this->maxWidth - 12, $tailOpt);
			$messages[] = $this->state->getState($message, $state, $this->maxWidth - 1);
		}
		if (!$oFileCurr->close()) {
			$result = false;
			$messages[] = '<error>' . __d('cake_installer', 'Error on closing cron file.') . '</error>';
		} elseif ($needUpdate) {
			$output = [];
			$exitCode = -1;
			$cmd = 'crontab -u ' . $apacheUser . ' ' . $tmpFile . ' 2>/dev/null';
			exec($cmd, $output, $exitCode);
			if ($exitCode !== 0) {
				$result = false;
				$messages[] = '<error>' . __d('cake_installer', 'Error on updating crontab.') . '</error>';
			} else {
				if (!$oFileBkp->copy($bkpFile, true)) {
					$this->out('<error>' . __d('cake_installer', 'Error on creation copy of previous crontab.') . '</error>');
					$result = false;
				}
			}
		}
		$oFileCurr->delete();
		$oFileBkp->delete();

		if (empty($messages)) {
			$messages[] = '<info>' . __d('cake_installer', 'List of cron jobs is empty') . '</info>';
		} elseif (!$needUpdate) {
			$messages[] = '<info>' . __d('cake_installer', 'Crontab update is not required.') . '</info>';
		}

		$this->out($messages, 1);
		$this->hr();
		if ($result) {
			$this->out('<success>' . __d('cake_installer', 'Creating cron jobs completed successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Creating cron jobs unsuccessfully.') . '</error>');
		}

		return $result;
	}

/**
 * Create dafault crontab file.
 *
 * @param string $path Path to file.
 * @return bool Success
 */
	protected function _createCrontabFile($path = null) {
		$data = <<<EOD
# Edit this file to introduce tasks to be run by cron.
#
# Each task to run has to be defined through a single line
# indicating with different fields when the task will be run
# and what command to run for the task
#
# To define the time you can provide concrete values for
# minute (m), hour (h), day of month (dom), month (mon),
# and day of week (dow) or use '*' in these fields (for 'any').#
# Notice that tasks will be started based on the cron's system
# daemon's notion of time and timezones.
#
# Output of the crontab jobs (including errors) is sent through
# email to the user the crontab file belongs to (unless redirected).
#
# For example, you can run a backup of all your user accounts
# at 5 a.m every week with:
# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/
#
# For more information see the manual pages of crontab(5) and cron(8)
#
# m h  dom mon dow   command

EOD;
		$oFile = new File($path, true);
		if (!$oFile->write($data)) {
			return false;
		}

		return $oFile->close();
	}

/**
 * Create and write time zone in the settings file.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::settimezone() CakeInstallerShell::settimezone() Create and write
 *  time zone in the settings file.
 * @return bool Success
 */
	protected function _settimezone($check = false) {
		$this->out(__d('cake_installer', 'Setting default timezone'));
		$this->hr();

		$path = $this->path;
		$currentTimeZone = $this->_readConfigCore($path, '/(?<![\/]{2})date_default_timezone_set\(\'([A-z\/]{3,})\'\)[\s]*;/');
		if (!empty($currentTimeZone)) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'Default timezone set to \'%s\'. Change?', $currentTimeZone))) {
					return true;
				}
			}
		}

		$useExit = !$check;
		$timeZone = $this->_getTimeZone($useExit);

		$result = null;
		if ($timeZone !== $currentTimeZone) {
			$result = $this->_writeTimezone($path, $timeZone);
		}
		$this->hr();
		if ($result === null) {
			$this->out('<info>' . __d('cake_installer', 'Default timezone is not changed.') . '</info>');
			$result = true;
		} elseif ($result) {
			$this->out('<success>' . __d('cake_installer', 'Default timezone set to \'%s\' successfully.', $timeZone) . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Unable to set default timezone, you should change it in %s', $path . 'Config' . DS . 'core.php') . '</error>');
		}

		return $result;
	}

/**
 * Create and write base URL of application in the settings file.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::setbaseurl() CakeInstallerShell::setbaseurl() Create and write
 *  base URL of application in the settings file.
 * @return bool Success
 */
	protected function _setbaseurl($check = false) {
		$this->out(__d('cake_installer', 'Setting base URL'));
		$this->hr();

		$path = $this->path;
		$currentBaseUrl = $this->_readConfigCore($path, '/(?<![\/]{2})Configure::write\(\'App\.fullBaseUrl\', \'(http[s]?\:\/\/[A-z0-9\.\/]+)\'\)[\s]*;/');
		if (!empty($currentBaseUrl)) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'Base URL set to \'%s\'. Change?', $currentBaseUrl))) {
					return true;
				}
			}
		}
		$baseUrl = $this->_getBaseUrl();

		$result = null;
		if ($baseUrl !== $currentBaseUrl) {
			$result = $this->_writeBaseUrl($path, $baseUrl);
		}
		$this->hr();
		if ($result === null) {
			$this->out('<info>' . __d('cake_installer', 'Base URL is not changed.') . '</info>');
			$result = true;
		} elseif ($result) {
			$this->out('<success>' . __d('cake_installer', 'Base URL set to \'%s\' successfully.', $baseUrl) . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Unable to set Base URL, you should change it in %s', $path . 'Config' . DS . 'core.php') . '</error>');
		}

		return $result;
	}

/**
 * Check already written security keys in the settings file.
 *
 * @param string $path Project path
 * @return bool Success
 */
	protected function _checkSetSecurKey($path = null) {
		if (empty($path) && !file_exists($path)) {
			return false;
		}

		$checkResult = true;
		$File = new File($path . 'Config' . DS . 'core.php');
		$contents = $File->read();
		if (empty($contents)) {
			return false;
		}

		$patterns = [
			'/(?<![\/]{2})Configure::write\(\'Security.salt\',[\s\'A-z0-9]*\);/',
			'/(?<![\/]{2})Configure::write\(\'Security.cipherSeed\',[\s\'A-z0-9]*\);/',
			'/(?<![\/]{2})Configure::write\(\'Security.key\',[\s\'A-z0-9]*\);/'
		];
		foreach ($patterns as $pattern) {
			$checkResult = $checkResult && (bool)preg_match($pattern, $contents);
		}

		return $checkResult;
	}

/**
 * Create and write security keys in the settings file.
 *
 * @param bool $check If True, check this action has already been completed.
 * @param bool $boot Whether to do bootstrapping.
 * @see CakeInstallerShell::setsecurkey() CakeInstallerShell::setsecurkey() Create and write
 *  security keys in the settings file.
 * @return bool Success
 */
	protected function _setsecurkey($check = false, $boot = true) {
		$this->out(__d('cake_installer', 'Setting security keys'));
		$this->hr();

		$path = $this->path;
		$resultCheck = $this->_checkSetSecurKey($path);
		if ($resultCheck) {
			if ($check) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			} elseif ($this->useActionNotify) {
				if (!$this->_checkSure(__d('cake_installer', 'The security keys is set. Rewrite?'))) {
					return true;
				}
			}
		}

		$result = true;
		if ($this->Project->securitySalt($path) === true) {
			$this->out('<success> * ' . __d('cake_installer', 'Random hash key created for \'Security.salt\'') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Unable to generate random hash for \'Security.salt\', you should change it in %s', $path . 'Config' . DS . 'core.php') . '</error>');
			$result = false;
		}

		if ($this->Project->securityCipherSeed($path) === true) {
			$this->out('<success> * ' . __d('cake_installer', 'Random seed created for \'Security.cipherSeed\'') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Unable to generate random seed for \'Security.cipherSeed\', you should change it in %s', $path . 'Config' . DS . 'core.php') . '</error>');
			$result = false;
		}

		if ($this->_securityKey($path) === true) {
			$this->out('<success> * ' . __d('cake_installer', 'Random key created for \'Security.key\'') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Unable to generate random key for \'Security.key\', you should change it in %s', $path . 'Config' . DS . 'core.php') . '</error>');
			$result = false;
		}

		$this->hr();
		if ($result) {
			Configure::bootstrap($boot);
			$this->_initUiLang();
			$this->out('<success>' . __d('cake_installer', 'The security keys is written successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'The security keys is written unsuccessfully.') . '</error>');
		}

		return $result;
	}

/**
 * Create and write application UI language in the settings file.
 *
 * @param bool $check If True, check this action has already been completed.
 * @see CakeInstallerShell::setuilang() CakeInstallerShell::_setuilang() Setting UI language.
 * @return null|bool Return Null, if neet restart task shell, or True on success, False otherwise.
 */
	protected function _setuilang($check = false) {
		$this->out(__d('cake_installer', 'Setting application UI language'));
		$this->hr();

		$path = $this->path;
		$currentUIlang = $this->_readConfigCore($path, '/(?<![\/]{2})Configure::write\(\'Config\.language\', \'([A-z]{3})\'\)[\s]*;/');
		if ($check) {
			if (!empty($currentUIlang)) {
				$this->out('<success>' . __d('cake_installer', 'This action has already been successfully completed. Skipped.') . '</success>');

				return true;
			}
		}
		$currentValueUIlang = $currentUIlang;
		if (empty($currentValueUIlang)) {
			$currentValueUIlang = 'eng';
		}

		$UIlangsList = $this->ConfigInstaller->getListUiLangs();
		if (empty($UIlangsList)) {
			$this->out('<warning>' . __d('cake_installer', 'List of UI languages is empty') . '</warning>');

			return false;
		}

		$language = new Language();
		$languages = [];
		foreach ($UIlangsList as $UIlangsItem) {
			$langCode = $language->convertLangCode($UIlangsItem, 'iso639-2/t');
			if (empty($langCode)) {
				$this->out('<debug>' . __d('cake_installer', 'Incorrect language code \'%s\'', $UIlangsItem) . '</debug>', 1, Shell::VERBOSE);
				continue;
			}

			$langName = $language->convertLangCode($langCode, 'native');
			if (empty($langName)) {
				$langName = $langCode;
			}
			$languages[$langCode] = mb_ucfirst($langName);
		}

		if (empty($languages)) {
			$this->out('<warning>' . __d('cake_installer', 'List of UI languages is empty') . '</warning>');

			return false;
		}

		$inputMessage = __d('cake_installer', 'Input the number of language from list');
		$titleMessage = __d('cake_installer', 'Please choose language of application UI:');
		$useExit = !$check;
		$UIlang = $this->inputFromList($this, $languages, $inputMessage, $titleMessage, $currentValueUIlang, $useExit);

		$result = null;
		if ($UIlang !== $currentUIlang) {
			$result = $this->_writeConfigLanguage($path, $UIlang);
		}
		$this->hr();
		if ($result === null) {
			$this->out('<info>' . __d('cake_installer', 'Application UI language is not changed.') . '</info>');
			$result = true;
		} elseif ($result) {
			$this->out('<success>' . __d('cake_installer', 'Application UI language set successfully.') . '</success>');
			if ($check) {
				$result = null;
			}
		} else {
			$this->out('<error>' . __d('cake_installer', 'Application UI language set unsuccessfully.') . '</error>');
		}

		return $result;
	}

/**
 * Checking PHP environment.
 *
 * @see CakeInstallerShell::check() CakeInstallerShell::check() Checking
 *  PHP environment.
 * @return bool Success
 */
	protected function _check() {
		$result = true;
		$this->out(__d('cake_installer', 'Checking PHP version'));
		$this->hr();
		$phpVesion = $this->InstallerCheck->checkPHPversion();
		if ($phpVesion !== null) {
			if ($phpVesion) {
				$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
			} else {
				$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
				$result = false;
			}
			$state = '[' . $state . ']';

			$message = ' * ' . __d('cake_installer', 'Current PHP version: %s', PHP_VERSION);
			$formattedMessage = $this->state->getState($message, $state, $this->maxWidth - 1);
		} else {
			$formattedMessage = __d('cake_installer', 'Ok');
		}
		$this->out($formattedMessage, 1);
		$this->hr();
		$this->nl(1);

		$this->out(__d('cake_installer', 'Checking PHP extensions'));
		$this->hr();
		$phpModules = $this->InstallerCheck->checkPhpExtensions();
		if ($phpModules !== null) {
			$result = true;
			$messages = [];
			$this->waiting->animateMessage();
			foreach ($phpModules as $moduleName => $moduleState) {
				switch ($moduleState) {
					case 2:
						$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
						break;
					case 1:
						$state = '<warning>' . __d('cake_installer', 'Ok') . '</warning>';
						break;
					default:
						$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
						$result = false;
				}
				$state = '[' . $state . ']';
				$message = ' * ' . __d('cake_installer', 'Checking module is loaded: \'%s\'', $moduleName);
				$messages[] = $this->state->getState($message, $state, $this->maxWidth - 1);
				$this->waiting->animateMessage();
			}
			$this->waiting->hideMessage();
		} else {
			$messages = __d('cake_installer', 'Ok');
		}
		$this->out($messages, 1);
		$this->hr();
		$this->nl(1);
		if ($result) {
			$this->out('<success>' . __d('cake_installer', 'Check completed successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Check completed unsuccessfully.') . '</error>');
		}

		return $result;
	}

/**
 * Check DB connections.
 *
 * @param bool $notProposeChangeConfig If False and result, propose
 *  to change the DB connection.
 * @see CakeInstallerShell::connectdb() CakeInstallerShell::connectdb() Checking
 *  connections to database.
 * @return bool Success
 */
	protected function _connectdb($notProposeChangeConfig = false) {
		$result = true;
		$this->out(__d('cake_installer', 'Checking database connections'));
		$this->hr();

		$this->out(__d('cake_installer', 'Checking database configuration file'));
		$this->hr();
		$this->waiting->animateMessage();
		$connections = $this->InstallerCheck->checkConnectDb($this->path);
		if ($connections !== null) {
			$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
		} else {
			$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
		}
		$state = '[' . $state . ']';
		$message = ' * ' . __d('cake_installer', 'Database configuration file');
		$formattedMessage = $this->state->getState($message, $state, $this->maxWidth - 1);
		$this->waiting->hideMessage();
		$this->out($formattedMessage, 1);
		$this->hr();
		if ($connections !== null) {
			$this->out(__d('cake_installer', 'Checking database connections'), 1);
			$this->hr();
			$messages = [];
			$this->waiting->animateMessage();
			foreach ($connections as $connectionName => $connectionState) {
				$errorMessages = [];
				if ($connectionState === true) {
					$state = '<success>' . __d('cake_installer', 'Ok') . '</success>';
				} else {
					if (is_array($connectionState)) {
						$shell = $this;
						array_walk(
							$connectionState,
							function (&$value) use ($shell) {
								$value = '<debug>' . $shell->wrapText($value, ['width' => $shell->maxWidth]) . '</debug>';
							}
						);
						$errorMessages = $connectionState;
					}
					$state = '<error>' . __d('cake_installer', 'Bad') . '</error>';
					$result = false;
				}
				$state = '[' . $state . ']';
				$message = ' * ' . __d('cake_installer', 'Checking database connection: \'%s\'', $connectionName);
				$messages[] = $this->state->getState($message, $state, $this->maxWidth - 1);
				if (!empty($errorMessages)) {
					$messages = array_merge($messages, $errorMessages);
				}
				$this->waiting->animateMessage();
			}
			$this->waiting->hideMessage();
			$this->out($messages, 1);
			$this->hr();
		}

		if ($result) {
			$this->out('<success>' . __d('cake_installer', 'Check completed successfully.') . '</success>');
		} else {
			$this->out('<error>' . __d('cake_installer', 'Check completed unsuccessfully.') . '</error>');
			if ($notProposeChangeConfig || (!$notProposeChangeConfig && $this->_checkSure(__d('cake_installer', 'Change database connections?')))) {
				$result = $this->_configdb($notProposeChangeConfig);
			}
		}

		return $result;
	}

/**
 * Recursive change parameters of directory.
 *
 * @param string $path Path to target directory
 * @param string $funcName Function name, e.g.: `chown`, `chmod` or `chgrp`.
 * @param int $param Parameter for function, e.g.:
 *  user name of owner or mode for access.
 * @return bool Success
 */
	protected function _changeDirParam($path = null, $funcName = null, $param = null) {
		if (empty($path) || !file_exists($path) ||
			!in_array($funcName, ['chown', 'chmod', 'chgrp']) || empty($param)) {
			return false;
		}

		$result = true;
		if (is_file($path) || is_dir($path)) {
			//@codingStandardsIgnoreStart
			$resultCall = @call_user_func($funcName, $path, $param);
			//@codingStandardsIgnoreEnd
			if ($resultCall) {
				if (is_file($path)) {
					return true;
				}
			} else {
				$result = false;
			}
		} else {
			return false;
		}

		$oFolder = new Folder($path, false);
		$dirContent = $oFolder->read(true, false, true);
		foreach ($dirContent as $targetPaths) {
			foreach ($targetPaths as $targetPath) {
				if (!$this->_changeDirParam($targetPath, $funcName, $param)) {
					$result = false;
				}
			}
		}

		return $result;
	}

/**
 * Recursive change owner of directory.
 *
 * @param string $path Path to target directory
 * @param string $user User name of owner
 * @return bool Success
 */
	protected function _changeDirOwner($path = null, $user = null) {
		return $this->_changeDirParam($path, 'chown', $user);
	}

/**
 * Recursive change owner group of directory.
 *
 * @param string $path Path to target directory
 * @param string $group Group name of owner
 * @return bool Success
 */
	protected function _changeDirGroupOwner($path = null, $group = null) {
		return $this->_changeDirParam($path, 'chgrp', $group);
	}

/**
 * Recursive change access mode of directory.
 *
 * @param string $path Path to target directory
 * @param int $mode Access mode
 * @return bool Success
 */
	protected function _changeDirMode($path = null, $mode = null) {
		return $this->_changeDirParam($path, 'chmod', $mode);
	}

/**
 * Set access rights to folders and application files.
 *
 * @see CakeInstallerShell::setdirpermiss() CakeInstallerShell::setdirpermiss() Set access rights
 *  to folders and application files.
 * @return bool Success
 */
	protected function _setdirpermiss() {
		$this->out(__d('cake_installer', 'Setting permissions'));
		$this->hr();

		$result = true;
		if ($this->InstallerCheck->isOsWindows()) {
			$this->out(__d('cake_installer', 'Server OS is Windows. Change permissions skipped.'));

			return $result;
		}

		$path = $this->path;
		$tempDir = $path . 'tmp';
		$mode = 0760;
		$dmode = decoct($mode);
		$fileConfig = $path . 'Config' . DS . 'config.php';
		if (!$this->_changeDirMode($tempDir, $mode)) {
			$this->out('<error>' . __d('cake_installer', 'Could not change the mode on \'%s\' to \'%s\'', $tempDir, $dmode) . '</error>');
			$this->out('<debug>chmod -R ' . $dmode . ' ' . $tempDir . '</debug>', 1, Shell::VERBOSE);
			$result = false;
		} else {
			$this->out('<success>' . __d('cake_installer', 'Access permissions on \'%s\' changed to \'%s\' successfully.', $tempDir, $dmode) . '</success>');
		}

		if (!chmod($fileConfig, $mode)) {
			$this->out('<error>' . __d('cake_installer', 'Could not change the mode on \'%s\' to \'%s\'', $fileConfig, $dmode) . '</error>');
			$this->out('<debug>chmod ' . $dmode . ' ' . $fileConfig . '</debug>', 1, Shell::VERBOSE);
			$result = false;
		} else {
			$this->out('<success>' . __d('cake_installer', 'Access permissions on \'%s\' changed to \'%s\' successfully.', $fileConfig, $dmode) . '</success>');
		}

		$apacheUser = $this->InstallerCheck->getWebSrvUser();
		if (empty($apacheUser)) {
			$this->out('<error>' . __d('cake_installer', 'Error getting the username of the web server process') . '</error>');

			return false;
		}

		if (!$this->_changeDirOwner($tempDir, $apacheUser)) {
			$this->out('<error>' . __d('cake_installer', 'Could not change owner on \'%s\' to \'%s\'', $tempDir . DS . '*.*', $apacheUser) . '</error>');
			$this->out('<debug>find ' . $tempDir . ' -type f | xargs chown ' . $apacheUser . '</debug>', 1, Shell::VERBOSE);
		} else {
			$this->out('<success>' . __d('cake_installer', 'Owner of \'%s\' changed to \'%s\' successfully.', $tempDir . DS . '*.*', $apacheUser) . '</success>');
		}

		/*$apacheUserGroup = $this->InstallerCheck->getWebSrvUserGroup($apacheUser);
		if (empty($apacheUserGroup)) {
			$this->out('<error>' . __d('cake_installer', 'Error getting the group of user the web server process') . '</error>');
			return false;
		}

		if (!$this->_changeDirGroupOwner($tempDir, $apacheUserGroup)) {
			$this->out('<error>' . __d('cake_installer', 'Could not change group on \'%s\' to \'%s\'', $tempDir . DS . '*.*', $apacheUserGroup) . '</error>');
			$this->out('<debug>chgrp -R ' . $apacheUserGroup . ' ' . $tempDir . '</debug>', 1, Shell::VERBOSE);
		} else {
			$this->out('<success>' . __d('cake_installer', 'Group of \'%s\' changed to \'%s\' successfully.', $tempDir . DS . '*.*', $apacheUserGroup) . '</success>');
		}

		//@codingStandardsIgnoreStart
		if (!@chgrp($fileConfig, $apacheUserGroup)) {
			//@codingStandardsIgnoreEnd
			$this->out('<error>' . __d('cake_installer', 'Could not change group on \'%s\' to \'%s\'', $fileConfig, $apacheUserGroup) . '</error>');
			$this->out('<debug>chgrp ' . $apacheUserGroup . ' ' . $fileConfig . '</debug>', 1, Shell::VERBOSE);
			$result = false;
		} else {
			$this->out('<success>' . __d('cake_installer', 'Group of \'%s\' changed to \'%s\' successfully.', $fileConfig, $apacheUserGroup) . '</success>');
		}*/

		//@codingStandardsIgnoreStart
		if (!@chown($fileConfig, $apacheUser)) {
			//@codingStandardsIgnoreEnd
			$this->out('<error>' . __d('cake_installer', 'Could not change owner on \'%s\' to \'%s\'', $fileConfig, $apacheUser) . '</error>');
			$this->out('<debug>chown ' . $apacheUser . ' ' . $fileConfig . '</debug>', 1, Shell::VERBOSE);
			$result = false;
		} else {
			$this->out('<success>' . __d('cake_installer', 'Owner of \'%s\' changed to \'%s\' successfully.', $fileConfig, $apacheUser) . '</success>');
		}

		return $result;
	}

/**
 * Return time zone from list.
 *
 * @param bool $useExit If True, Add list item `Exit`.
 * @see CakeInstallerShell::_settimezone() CakeInstallerShell::_settimezone() Create and write
 *  time zone in the settings file.
 * @return string Time zone.
 */
	protected function _getTimeZone($useExit = true) {
		$scopeTimeZoneList = [
			DateTimeZone::AFRICA => 'Africa',
			DateTimeZone::AMERICA => 'America',
			DateTimeZone::ANTARCTICA => 'Antarctica',
			DateTimeZone::ARCTIC => 'Arctic',
			DateTimeZone::ASIA => 'Asia',
			DateTimeZone::ATLANTIC => 'Atlantic',
			DateTimeZone::AUSTRALIA => 'Australia',
			DateTimeZone::EUROPE => 'Europe',
			DateTimeZone::INDIAN => 'Indian',
			DateTimeZone::PACIFIC => 'Pacific',
			DateTimeZone::UTC => 'UTC'
		];

		$currentTz = date_default_timezone_get();
		$currentTzInfoScope = null;
		$currentTzInfo = explode('/', $currentTz, 2);
		if (count($currentTzInfo) == 2) {
			$currentTzInfoScopeName = array_shift($currentTzInfo);
			$currentTzInfoScopeKey = array_search($currentTzInfoScopeName, $scopeTimeZoneList);
			if ($currentTzInfoScopeKey) {
				$currentTzInfoScope = $currentTzInfoScopeKey;
			}
		}
		$inputMessage = __d('cake_installer', 'Input the number of scope time zone from list');
		$titleMessage = __d('cake_installer', 'Please choose scope of time zone:');
		$scopeTimeZone = $this->inputFromList($this, $scopeTimeZoneList, $inputMessage, $titleMessage, $currentTzInfoScope, $useExit);

		$timeZoneIds = DateTimeZone::listIdentifiers($scopeTimeZone);
		$timeZoneList = array_combine($timeZoneIds, $timeZoneIds);

		$inputMessage = __d('cake_installer', 'Input the number of timezone from list');
		$titleMessage = __d('cake_installer', 'Please choose time zone:');
		$this->clear();

		return $this->inputFromList($this, $timeZoneList, $inputMessage, $titleMessage, $currentTz, false);
	}

/**
 * Return base URL for application.
 *
 * @see CakeInstallerShell::_setbaseurl() CakeInstallerShell::_setbaseurl() Create and write
 *  base URL of application in the settings file.
 * @return string Base URL.
 */
	protected function _getBaseUrl() {
		do {
			$baseUrl = $this->in(__d('cake_installer', 'Input base URL (e.g. %s):', 'http://someapp.fabrikam.com'));
			$baseUrl = trim($baseUrl);
		} while (!preg_match('/http[s]?\:\/\/[A-z0-9\.\/]+/', $baseUrl));

		return trim($baseUrl, '/');
	}

/**
 * Read configuration from the settings file.
 *
 * @param string $path Base path of application.
 * @param string $configPattern Configuration pattern for PCRE.
 * @return bool|string Return string of configuration or False
 *  on failure.
 */
	protected function _readConfigCore($path = null, $configPattern = null) {
		if (empty($path) || empty($configPattern)) {
			return false;
		}

		$oFile = new File($path . 'Config' . DS . 'core.php');
		if (!$oFile->exists()) {
			return false;
		}

		$contents = $oFile->read();
		if (!preg_match($configPattern, $contents, $match) || (count($match) < 1)) {
			return false;
		}

		return $match[1];
	}

/**
 * Create and write configuration in the settings file.
 *
 * @param string $path Base path of application.
 * @param string $configTempl Configuration template in sprintf format.
 * @param string $configValue Configuration value.
 * @param string $configComment Commentary of configuration.
 * @return bool Success
 */
	protected function _writeConfigCore($path = null, $configTempl = null, $configValue = null, $configComment = null) {
		if (empty($path) || empty($configTempl) || empty($configValue)) {
			return false;
		}

		$oFile = new File($path . 'Config' . DS . 'core.php');
		if (!$oFile->exists()) {
			return false;
		}

		$contents = $oFile->read();
		$pattern = '/^[\s]*(?:[\/]{2}|)[\s]*(' . sprintf(preg_quote($configTempl, '/'), '.*') . '[\s]*;)/m';
		$configStr = sprintf($configTempl, $configValue) . ';';
		if (preg_match($pattern, $contents, $match)) {
			$result = str_replace($match[0], "\t" . $configStr, $contents);
		} else {
			$result = $contents;
			if (!empty($configComment)) {
				$result .= sprintf("\n/**\n * %s\n */", $configComment);
			}
			$result .= "\n\t" . $configStr . "\n";
		}

		return $oFile->write($result);
	}

/**
 * Create and write time zone in the settings file.
 *
 * @param string $path Base path of application.
 * @param string $timeZone Time zone name.
 * @see CakeInstallerShell::settimezone() CakeInstallerShell::_settimezone() Create and write
 *  time zone in the settings file.
 * @return bool Success
 */
	protected function _writeTimezone($path = null, $timeZone = null) {
		if (!$this->_writeConfigCore($path, 'date_default_timezone_set(\'%s\')', $timeZone)) {
			return false;
		}

		if (!$this->_writeConfigCore($path, 'Configure::write(\'Config.timezone\', \'%s\')', $timeZone)) {
			return false;
		}

		return true;
	}

/**
 * Create and write base URL of application in the settings file.
 *
 * @param string $path Base path of application.
 * @param string $baseUrl Time zone name.
 * @see CakeInstallerShell::setbaseurl() CakeInstallerShell::_setbaseurl() Create and write
 *  base URL of application in the settings file.
 * @return bool Success
 */
	protected function _writeBaseUrl($path = null, $baseUrl = null) {
		return $this->_writeConfigCore($path, 'Configure::write(\'App.fullBaseUrl\', \'%s\')', $baseUrl);
	}

/**
 * Create and write language of application UI in the settings file.
 *
 * @param string $path Base path of application.
 * @param string $language Language of UI.
 * @see CakeInstallerShell::setuilang() CakeInstallerShell::_setuilang() Store language of UI installer,
 *  Create and write language of UI application in the settings file.
 * @return bool Success
 */
	protected function _writeConfigLanguage($path = null, $language = null) {
		if (!$this->_writeConfigCore($path, 'Configure::write(\'Config.language\', \'%s\')', $language)) {
			return false;
		}

		if (!$this->_writeConfigCore($path, 'setLocale(LC_ALL, \'%s\')', $language)) {
			return false;
		}

		return true;
	}
}
