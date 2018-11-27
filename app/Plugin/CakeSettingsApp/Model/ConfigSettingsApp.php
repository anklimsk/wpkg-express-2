<?php
/**
 * This file is the model file of the plugin.
 * Retrieving configuration of plugin.
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeSettingsAppAppModel', 'CakeSettingsApp.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * ConfigInstaller for installer.
 *
 * @package plugin.Model
 */
class ConfigSettingsApp extends CakeSettingsAppAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'ConfigSettingsApp';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Get configuration for plugin.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed Configuration for plugin
 */
	public function getConfig($key = null) {
		$configPath = 'CakeSettingsApp';
		if (!empty($key)) {
			$configPath .= '.' . $key;
		}

		$result = Configure::read($configPath);

		return $result;
	}

/**
 * Get name of key application configuration
 *
 * @return string Deep value
 */
	public function getNameConfigKey() {
		$configKey = (string)$this->getConfig('configKey');

		return $configKey;
	}

/**
 * Return state of flag configuration SMTP
 *
 * @return bool State of flag
 */
	public function getFlagConfigSmtp() {
		$configSMTP = (bool)$this->getConfig('configSMTP');

		return $configSMTP;
	}

/**
 * Return state of flag configuration autocomplete limit
 *
 * @return bool State of flag
 */
	public function getFlagConfigAcLimit() {
		$configSMTP = (bool)$this->getConfig('configAcLimit');

		return $configSMTP;
	}

/**
 * Return state of flag configuration search in AD
 *
 * @return bool State of flag
 */
	public function getFlagConfigADsearch() {
		$configSMTP = (bool)$this->getConfig('configADsearch');

		return $configSMTP;
	}

/**
 * Return state of flag configuration external authentication
 *
 * @return bool State of flag
 */
	public function getFlagConfigExtAuth() {
		$configSMTP = (bool)$this->getConfig('configExtAuth');

		return $configSMTP;
	}

/**
 * Return state of flag configuration UI languages
 *
 * @return bool State of flag
 */
	public function getFlagConfigUiLangs() {
		$configSMTP = (bool)$this->getConfig('UIlangs');

		return $configSMTP;
	}

/**
 * Get list of groups for configuration user roles
 *
 * @return array List of groups
 */
	public function getAuthGroups() {
		$authGroups = (array)$this->getConfig('authGroups');

		return $authGroups;
	}

/**
 * Get list of UI languages
 *
 * @return array List of UI languages
 */
	public function getUiLangs() {
		$UIlangs = (array)$this->getConfig('UIlangs');

		return $UIlangs;
	}

/**
 * Returns an array of extended settings fields metadata
 *
 * @return array Extended settings fields metadata.
 */
	public function getExtendSchemaData() {
		$schema = (array)$this->getConfig('schema');

		return $schema;
	}

/**
 * Returns an array of list serialized fields
 *
 * @return array List serialized fields.
 */
	public function getListSerializeFields() {
		$serialize = (array)$this->getConfig('serialize');

		return $serialize;
	}

/**
 * Get list of aliases for application configuration parameters
 *
 * @return array List of aliases
 */
	public function getAliasConfig() {
		$targetModels = (array)$this->getConfig('alias');

		return $targetModels;
	}
}
