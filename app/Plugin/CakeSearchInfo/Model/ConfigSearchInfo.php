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

App::uses('CakeSearchInfoAppModel', 'CakeSearchInfo.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * ConfigInstaller for installer.
 *
 * @package plugin.Model
 */
class ConfigSearchInfo extends CakeSearchInfoAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'ConfigSearchInfo';

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
		$configPath = 'CakeSearchInfo';
		if (!empty($key)) {
			$configPath .= '.' . $key;
		}

		$result = Configure::read($configPath);

		return $result;
	}

/**
 * Get deep of search in project database
 *
 * @return int Deep value
 */
	public function getTargetDeep() {
		$targetDeep = (int)$this->getConfig('TargetDeep');

		return $targetDeep;
	}

/**
 * Get list of target models
 *
 * @return array List of target models
 */
	public function getTargetModels() {
		$targetModels = (array)$this->getConfig('TargetModels');

		return $targetModels;
	}

/**
 * Get list of include fields for models
 *
 * @return array List of include fields
 */
	public function getIncludeFields() {
		$includeFields = (array)$this->getConfig('IncludeFields');

		return $includeFields;
	}

/**
 * Get limit for autocomplete in input search form
 *
 * @return int Limit for autocomplete
 */
	public function getAutocompleteLimit() {
		$limit = (int)$this->getConfig('AutocompleteLimit');
		if ($limit < 1) {
			$limit = CAKE_SEARCH_INFO_AUTOCOMPLETE_LIMIT;
		}

		return $limit;
	}

/**
 * Get minimal length of query string
 *
 * @return int Minimal length of query string
 */
	public function getQuerySearchMinLength() {
		$querySearchMinLength = (int)$this->getConfig('QuerySearchMinLength');
		if ($querySearchMinLength < 1) {
			$querySearchMinLength = CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH;
		}

		return $querySearchMinLength;
	}

/**
 * Return state of flag default search in any
 *  part string
 *
 * @return bool State of flag
 */
	public function getFlagDefaultSearchAnyPart() {
		$flagDefaultSearch = (bool)$this->getConfig('DefaultSearchAnyPart');

		return $flagDefaultSearch;
	}
}
