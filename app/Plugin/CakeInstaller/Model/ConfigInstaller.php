<?php
/**
 * This file is the model file of the plugin.
 * Retrieving configuration of plugin.
 * Methods to check installation of application
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeInstallerAppModel', 'CakeInstaller.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * ConfigInstaller for installer.
 *
 * @package plugin.Model
 */
class ConfigInstaller extends CakeInstallerAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'ConfigInstaller';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Return configuration value by key
 *
 * @param string $configKey The identifier to read configuration
 *  of application.
 * @return array Configuration value
 */
	protected function _getConfigValueArray($configKey = null) {
		$result = [];
		if (empty($configKey)) {
			return $result;
		}

		$configPath = 'CakeInstaller.' . $configKey;
		if (!Configure::check($configPath)) {
			return $result;
		}

		$configValue = (array)Configure::read($configPath);
		if (!empty($configValue)) {
			$result = $configValue;
		}

		return $result;
	}

/**
 * Return configuration value by key
 *
 * @param string $configKey The identifier to read configuration
 *  of application.
 * @return array Configuration value as multidimensional array
 */
	protected function _getConfigValueArrayMultiple($configKey = null) {
		$result = $this->_getConfigValueArray($configKey);
		if ((count($result) > 0) && (count($result) === count($result, COUNT_RECURSIVE))) {
			$result = [$result];
		}

		return $result;
	}

/**
 * Return configuration for checking version of PHP
 *
 * @return array
 */
	public function getPhpVersionConfig() {
		return $this->_getConfigValueArrayMultiple('PHPversion');
	}

/**
 * Return configuration for checking extensions of PHP
 *
 * @return array
 */
	public function getPhpExtensionsConfig() {
		return $this->_getConfigValueArrayMultiple('PHPextensions');
	}

/**
 * Return full list of installer commands
 *
 * @return array Full list of installer commands
 */
	protected function _getListInstallerCommandsFull() {
		$result = [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETUILANG => __d('cake_installer', 'Setting application UI language'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK => __d('cake_installer', 'Checking PHP environment'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETDIRPERMISS => __d('cake_installer', 'Setting file system permissions on the temporary directory'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY => __d('cake_installer', 'Setting security key'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETTIMEZONE => __d('cake_installer', 'Setting timezone'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETBASEURL => __d('cake_installer', 'Setting base URL'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONNECT_DB => __d('cake_installer', 'Checking connect to database'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB => __d('cake_installer', 'Configure database connections'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_DB => __d('cake_installer', 'Creation database and initialization data'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_SYMLINKS => __d('cake_installer', 'Creation symbolic links to files'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_CRONJOBS => __d('cake_installer', 'Creation cron jobs'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_INSTALL => __d('cake_installer', 'Install this application'),
		];

		return $result;
	}

/**
 * Return list of installer commands
 *
 * @param bool $returnList If True, return only list of commands.
 *  Otherwise, return list of commands with descriptions.
 * @return array List of installer commands
 */
	public function getListInstallerCommands($returnList = true) {
		$commandsList = $this->_getListInstallerCommandsFull();
		$installerCommands = $this->_getConfigValueArray('installerCommands');
		$result = array_intersect_key($commandsList, array_flip($installerCommands));
		if ($returnList) {
			$result = array_keys($result);
		}

		return $result;
	}

/**
 * Return list of installer tasks
 *
 * @return array List of installer tasks
 */
	public function getListInstallerTasks() {
		return $this->_getConfigValueArray('installTasks');
	}

/**
 * Return list of schema creation
 *
 * @return array List of schema creation
 */
	public function getListSchemaCreation() {
		return $this->_getConfigValueArray('schemaCreationList');
	}

/**
 * Return list of schema checking
 *
 * @return array List of schema checking
 */
	public function getListSchemaChecking() {
		return $this->_getConfigValueArray('schemaCheckingList');
	}

/**
 * Return list of symbolic links creation
 *
 * @return array List of symbolic links creation
 */
	public function getListSymlinksCreation() {
		return $this->_getConfigValueArray('symlinksCreationList');
	}

/**
 * Return list of cron jobs creation
 *
 * @return array List of cron jobs creation
 */
	public function getListCronJobsCreation() {
		return $this->_getConfigValueArray('cronJobs');
	}

/**
 * Return list of symbolic links creation
 *
 * @return array List of symbolic links creation
 */
	public function getListUiLangs() {
		return $this->_getConfigValueArray('UIlangList');
	}

/**
 * Return custom connections configuration
 *
 * @return array
 */
	public function getCustomConnectionsConfig() {
		return $this->_getConfigValueArray('customConnections');
	}

/**
 * Return list of connections for creation configuration
 *
 * @return array
 */
	public function getListDbConnConfigs() {
		return $this->_getConfigValueArray('configDBconn');
	}
}
