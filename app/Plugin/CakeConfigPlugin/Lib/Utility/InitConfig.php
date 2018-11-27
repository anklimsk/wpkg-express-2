<?php
/**
 * This file is the util file of the plugin.
 * InitConfig Utility.
 * Methods to initialize and obtain plugin configuration.
 *
 * CakeConfigPlugin: Initialize and obtain plugin configuration.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Lib.Utility
 */

App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');
App::uses('PhpReader', 'Configure');

/**
 * Plugin configuration library.
 * Methods to initialize and obtain plugin configuration.
 *
 * @package plugin.Lib.Utility
 */
class InitConfig {

/**
 * Path to application config folder
 *
 * @var string
 */
	public $path = null;

/**
 * Plugin name for process
 *
 * @var string
 */
	protected $_pluginName = null;

/**
 * Path for checking configuration is initialized
 *
 * @var string
 */
	protected $_checkPath = null;

/**
 * Name of file configuration
 *
 * @var string
 */
	protected $_configFile = null;

/**
 * Constructor
 *
 * @param string $pluginName Plugin name for process
 * @param string $checkPath Path for checking configuration is initialized
 * @param string $configFile Name of file configuration without extension
 * @throws InternalErrorException if plugin name is not specified
 * @return void
 */
	public function __construct($pluginName = null, $checkPath = null, $configFile = null) {
		$this->_pluginName = (string)$pluginName;
		if (empty($this->_pluginName)) {
			throw new InternalErrorException(__d('cake_config_plugin', 'The plugin name for initialize configuration is not specified'));
		}

		$this->_checkPath = $this->_pluginName;
		if (!empty($checkPath)) {
			$this->_checkPath = (string)$checkPath;
		}

		$this->_configFile = mb_strtolower($this->_pluginName);
		if (!empty($configFile)) {
			$this->_configFile = (string)$configFile;
		}

		if (empty($this->path)) {
			$this->path = CONFIG;
		}
	}

/**
 * Return plugin name for process
 *
 * @return string plugin name
 */
	protected function _getPluginName() {
		return (string)$this->_pluginName;
	}

/**
 * Return path for checking configuration is initialized
 *
 * @return string Path for checking
 */
	protected function _getCheckPath() {
		return (string)$this->_checkPath;
	}

/**
 * Return Name of file configuration without extension
 *
 * @return string Name of file configuration
 */
	protected function _getConfigFile() {
		return (string)$this->_configFile;
	}

/**
 * Get configuration for plugin.
 *
 * @return array Configuration for plugin
 */
	public function getConfig() {
		$result = [];
		$pluginName = $this->_getPluginName();
		if (empty($pluginName) || !Configure::check($pluginName)) {
			return $result;
		}

		$result = (array)Configure::read($pluginName);

		return $result;
	}

/**
 * Initializes configuration for plugin.
 *
 * @param bool $force If True, force initialize configuration
 * @author MGriesbach@gmail.com
 * @return void
 */
	public function initConfig($force = false) {
		$pluginName = $this->_getPluginName();
		$checkPath = $this->_getCheckPath();
		$configFile = $this->_getConfigFile();
		$language = (string)Configure::read('Config.language');
		if (!$force && !empty($checkPath) && Configure::check($checkPath)) {
			return;
		}

		// Local config without extra config file
		$conf = $this->getConfig();

		$cachePath = 'static_cfg_' . Inflector::underscore($pluginName) . '_' . $language;
		$cached = Cache::read($cachePath, CAKE_CONFIG_PLUGIN_CACHE_CFG);
		if ($cached !== false) {
			$conf = Hash::mergeDiff($conf, (array)$cached);
			Configure::write($pluginName, $conf);

			return;
		}
		Configure::config('phpcfg', new PhpReader($this->path));
		// Fallback to Plugin config which can be overwritten via local app config.
		Configure::load($pluginName . '.' . $configFile, 'phpcfg', false);
		$defaultConf = $this->getConfig();

		// Local app config
		if (file_exists($this->path . $configFile . '.php')) {
			Configure::load($configFile, 'phpcfg', false);
			$localConf = $this->getConfig();
			$conf = Hash::mergeDiff($conf, $localConf);
		}
		Configure::drop('phpcfg');
		// BC comp:
		$conf = Hash::mergeDiff($conf, $defaultConf);
		Cache::write($cachePath, $conf, CAKE_CONFIG_PLUGIN_CACHE_CFG);
		Configure::write($pluginName, $conf);
	}
}
