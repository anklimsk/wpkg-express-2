<?php
/**
 * This file is the model file of the plugin.
 * Get configuration of plugin from file.
 * Methods for retrieve configuration of plugin from file.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeThemeAppModel', 'CakeTheme.Model');

/**
 * ConfigTheme for CakeTheme.
 *
 * @package plugin.Model
 */
class ConfigTheme extends CakeThemeAppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = false;

/**
 * Get configuration for plugin.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed Configuration for plugin
 */
	public function getConfig($key = null) {
		$configPath = 'CakeTheme';
		if (!empty($key)) {
			$configPath .= '.' . $key;
		}

		$result = Configure::read($configPath);

		return $result;
	}

/**
 * Returns the list of additional CSS files
 *
 * @return array List of additional CSS files.
 */
	public function getListCssFiles() {
		$result = [];
		$config = (array)$this->getConfig('AdditionalFiles.css');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Returns the list of additional JS files
 *
 * @return array List of additional JS files.
 */
	public function getListJsFiles() {
		$result = [];
		$config = (array)$this->getConfig('AdditionalFiles.js');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Returns configuration of AjaxFlash
 *
 * @return array Return configuration of AjaxFlash.
 */
	public function getConfigAjaxFlash() {
		$result = [];
		$config = (array)$this->getConfig('AjaxFlash');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Returns configuration of steps TourApp
 *
 * @return array Return configuration of steps TourApp.
 */
	public function getStepsConfigTourApp() {
		$result = [];
		$config = (array)$this->getConfig('TourApp.Steps');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Returns configuration of Unoconv
 *
 * @return string Return configuration of Unoconv.
 */
	public function getUnoconvConfig() {
		$result = [];
		$config = (array)$this->getConfig('ViewExtension.Unoconv');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Returns configuration of SSE
 *
 * @return string Return configuration of SSE.
 */
	public function getSseConfig() {
		$result = [];
		$config = (array)$this->getConfig('ViewExtension.SSE');
		if (empty($config)) {
			return $result;
		}

		return $config;
	}

/**
 * Get limit for autocomplete in input filter form
 *
 * @return int Limit for autocomplete
 */
	public function getAutocompleteLimitConfig() {
		$limit = (int)$this->getConfig('ViewExtension.AutocompleteLimit');
		if ($limit <= 0) {
			$limit = CAKE_THEME_AUTOCOMPLETE_LIMIT;
		}

		return $limit;
	}
}
