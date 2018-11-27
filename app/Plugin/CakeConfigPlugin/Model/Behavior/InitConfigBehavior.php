<?php
/**
 * This file is the util file of the plugin.
 * InitConfig Utility.
 * Methods to initialize and obtain plugin configuration.
 *
 * CakeConfigPlugin: Initialize and obtain plugin configuration.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('InitConfig', 'CakeConfigPlugin.Utility');

/**
 * Initialize plugin configuration Behavior.
 * Initialize plugin configuration.
 *
 * @package plugin.Model.Behavior
 */
class InitConfigBehavior extends ModelBehavior {

/**
 * Stores the InitConfig() utility object.
 *
 * @var object
 */
	protected $_initConfig = null;

/**
 * Setup this behavior with the specified configuration settings.
 *
 * Actions:
 * - Initialization configuration of plugin.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = []) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = [
				'pluginName' => null,
				'checkPath' => null,
				'configFile' => null,
				'path' => null,
			];
		}
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], $config);
		extract($this->settings[$model->alias]);
		$this->_initConfig = new InitConfig($pluginName, $checkPath, $configFile);
		if (!empty($path)) {
			$this->_initConfig->path = $path;
		}

		$this->_initConfig->initConfig();
	}

/**
 * Initializes configuration for plugin.
 *
 * @param Model $model Model using this behavior
 * @param bool $force If True, force initialize configuration
 * @return void
 */
	public function initConfig(Model $model, $force = false) {
		$this->_initConfig->initConfig($force);
	}
}
