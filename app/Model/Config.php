<?php
/**
 * This file is the model file of the application. Used to
 *  manage configuration of WPKG.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used to manage configuration of WPKG.
 *
 * @package app.Model
 */
class Config extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'key';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'TrimStringField',
		'BreadCrumbExt',
		'GetList' => ['cacheConfig' => 'default'],
		'Tools.Bitmasked' => [
			'field' => 'logLevel',
			'bits' => 'getListLogLevel',
		],
		'ClearViewCache'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'key' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The configuration key is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The configuration key already exists.',
				'last' => true
			],
		],
		'value' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The configuration value is invalid.',
				'last' => true
			]
		]
	];

/**
 * Schema of conditions WPKG.
 *
 * @var array
 */
	protected $_schemaConfig = [
		'force' => 'bool',
		'forceInstall' => 'bool',
		'quitonerror' => 'bool',
		'debug' => 'bool',
		'dryrun' => 'bool',
		'quiet' => 'bool',
		'nonotify' => 'bool',
		'notificationDisplayTime' => 'int',
		'execTimeout' => 'int',
		'noreboot' => 'bool',
		'noRunningState' => 'bool',
		'caseSensitivity' => 'bool',
		'applyMultiple' => 'bool',
		'noDownload' => 'bool',
		'rebootCmd' => 'string',
		'settings_file_name' => 'string',
		'settings_file_path' => 'string',
		'noForcedRemove' => 'bool',
		'noRemove' => 'bool',
		'sendStatus' => 'bool',
		'noUpgradeBeforeRemove' => 'bool',
		'settingsHostInfo' => 'bool',
		'volatileReleaseMarker' => 'string',
		'queryMode' => 'string',
		'logAppend' => 'bool',
		'logLevel' => 'int',
		'log_file_path' => 'string',
		'logfilePattern' => 'string',
		'packages_path' => 'string',
		'profiles_path' => 'string',
		'hosts_path' => 'string',
		'sRegPath' => 'string',
		'sRegWPKG_Running' => 'string',
	];

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 *  - Unserialized data of configuration parameters.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		if (!$primary || empty($results)) {
			return parent::afterFind($results, $primary);
		}

		foreach ($results as &$resultItem) {
			if (isset($resultItem[$this->alias]['value']) && !empty($resultItem[$this->alias]['value'])) {
				//@codingStandardsIgnoreStart
				$resultItem[$this->alias]['value'] = @unserialize($resultItem[$this->alias]['value']);
				//@codingStandardsIgnoreEnd 
			}
		}
		unset($resultItem);

		return parent::afterFind($results, $primary);
	}

/**
 * Called during validation operations, before validation.
 *
 * Actions:
 *  - Preparing data to validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		return $this->_prepareDataForSave();
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Preparing data to save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		return $this->_prepareDataForSave();
	}

/**
 * Return type of configuration parameter
 *
 * @param string $key Parameter for retrieving parameter value.
 * @return mixed Return type of configuration parameter,
 *  or False on failure.
 */
	public function getConfigValueType($key = null) {
		if (empty($key) || !isset($this->_schemaConfig[$key])) {
			return false;
		}

		$result = $this->_schemaConfig[$key];
		return $result;
	}

/**
 * Preparing data to save.
 *  Set type of value and serializing data.
 *
 * @return bool Success.
 */
	protected function _prepareDataForSave() {
		if (!isset($this->data[$this->alias]['key']) ||
			!isset($this->data[$this->alias]['value'])) {
			return false;
		}

		//@codingStandardsIgnoreStart
		$data = @unserialize($this->data[$this->alias]['value']);
		//@codingStandardsIgnoreEnd 
		if (($data !== false) || ($this->data[$this->alias]['value'] === 'b:0;')) {
			return true;
		}

		$type = $this->getConfigValueType($this->data[$this->alias]['key']);
		if (!$type) {
			return false;
		}

		$value = $this->data[$this->alias]['value'];
		if (!settype($value, $type)) {
			return false;
		}

		$this->data[$this->alias]['value'] = serialize($value);
		return true;
	}

/**
 * Set default configuration.
 *  Re-initialization WPKG configuration values.
 *
 * @return bool Success
 */
	public function setDefault() {
		$wwwRoot = Configure::read('App.www_root');
		if (empty($wwwRoot)) {
			return false;
		}

		$xmlFile = $wwwRoot . 'files' . DS . 'XML' . DS . 'CONFIG_TEMPLATE.xml';
		$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
		$taskParam = ['fileName' => $xmlFile];
		return (bool)$modelExtendQueuedTask->createJob('ImportXml', $taskParam, null, 'import');
	}

/**
 * Return path to log files.
 *
 * @param string $type Type of path: `logs` or `databases`.
 * @return string Return path to log files.
 */
	protected function _getLogFilePath($type = null) {
		$result = '';
		$type = mb_strtolower($type);
		$setttingParam = null;
		switch ($type) {
			case 'logs':
				$setttingParam = 'SmbLogShare';
				break;
			case 'databases':
				$setttingParam = 'SmbDbShare';
				break;
			default:
				return $result;
		}
		$modelSetting = ClassRegistry::init('Setting');
		$host = $modelSetting->getConfig('SmbServer');
		$share = $modelSetting->getConfig($setttingParam);
		if (empty($host) || empty($share)) {
			return $result;
		}

		$result = '\\\\' . $host . '\\' . $share;
		$result = str_replace('/', '\\', $result);

		return $result;
	}

/**
 * Return path to XML files: packages, profiles and hosts.
 *  Include user name and password, if needed.
 *
 * @param string $controller Controller of path: `package`, `profile`, `host`
 *  or `wpi`.
 * @param string $action Action of path.
 * @param string $extension File extension. Default `XML`.
 * @param bool $hidePass Flag of hiding password.
 * @return string|bool Return path to XML files,
 *  or False on failure.
 */
	public function getPathXml($controller = null, $action = null, $extension = null, $hidePass = false) {
		$controller = mb_strtolower($controller);
		if (empty($extension)) {
			$extension = 'xml';
		}

		$listControllers = [
			'packages',
			'profiles',
			'hosts',
			'wpi'
		];
		if (empty($controller) || !in_array($controller, $listControllers)) {
			return false;
		}

		$fullBaseUrl = Configure::read('App.fullBaseUrl');
		if (empty($fullBaseUrl)) {
			return false;
		}

		$urlInfo = parse_url($fullBaseUrl);
		if (!$urlInfo) {
			return false;
		}

		$urlInfo += ['host' => '', 'scheme' => ''];
		extract($urlInfo);
		if (empty($host) || empty($scheme)) {
			return false;
		}

		$scheme .= '://';
		$modelSetting = ClassRegistry::init('Setting');
		$protectXml = $modelSetting->getConfig('ProtectXml');
		$user = '';
		$pass = '';
		if ($protectXml) {
			$usr = $modelSetting->getConfig('XmlAuthUser');
			$pswd = $modelSetting->getConfig('XmlAuthPassword');
			if (!empty($usr) && !empty($pswd)) {
				$user = $usr . ':';
				if ($hidePass) {
					$pswd = str_repeat('*', 5);
				}
				$pass = $pswd . '@';
			}
		}

		$path = '/' . $controller;
		if (!empty($action)) {
			$path .= '/' . $action;
		}
		$path .= '.' . $extension;
		$data = [$scheme, $user, $pass, $host, $path];
		$result = implode('', $data);

		return $result;
	}

/**
 * Return value of configuration parameter.
 *
 * @param string $key Parameter name.
 * @return mixed Return value of configuration parameter.
 */
	public function getConfig($key = null) {
		if (empty($key)) {
			return null;
		}

		$conditions = [
			$this->alias . '.key' => $key
		];

		return $this->field('value', $conditions);
	}

/**
 * Return all values of configuration parameters.
 *
 * @param bool $hidePass Flag of hiding password.
 * @return array Return value of configuration parameter.
 */
	public function getAllConfig($hidePass = false) {
		$result = [];
		$fields = [
			$this->alias . '.key',
			$this->alias . '.value',
		];
		$recursive = -1;
		$data = $this->find('all', compact('fields', 'recursive'));
		if (empty($data)) {
			return $result;
		}

		$result[$this->alias] = Hash::combine(
			$data,
			'{n}.' . $this->alias . '.key',
			'{n}.' . $this->alias . '.value'
		);
		$pathTypes = [
			'logs' => 'log_file_path',
			'databases' => 'settings_file_path'
		];
		foreach ($pathTypes as $pathType => $paramName) {
			$logFilePath = $this->_getLogFilePath($pathType);
			if (!empty($logFilePath)) {
				$result[$this->alias][$paramName] = $logFilePath;
			}
		}
		$xmlTypes = ['packages', 'profiles', 'hosts'];
		foreach ($xmlTypes as $xmlType) {
			$pathXml = $this->getPathXml($xmlType, null, null, $hidePass);
			if (!empty($pathXml)) {
				$configField = $xmlType . '_path';
				$result[$this->alias][$configField] = $pathXml;
			}
		}

		return $result;
	}

/**
 * Save all values of configuration parameters.
 *
 * @param array $data Data to save.
 * @return bool Success.
 */
	public function saveConfig($data = null) {
		if (empty($data) || !isset($data[$this->alias])) {
			return false;
		}
		$cacheKeys = $this->getCacheData('key');
		$dataToSave = [];
		foreach ($data[$this->alias] as $key => $value) {
			$dataToSaveItem = compact('key', 'value');
			if (isset($cacheKeys[$key])) {
				$dataToSaveItem['id'] = $cacheKeys[$key];
			}
			$dataToSave[] = [$this->alias => $dataToSaveItem];
		}

		return (bool)$this->saveAll($dataToSave);
	}

/**
 * Deleting WPKG configuration.
 *
 * @return bool Success.
 */
	public function deleteConfig() {
		$dataSource = $this->getDataSource();
		return $dataSource->truncate($this);
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Settings of WPKG');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		return $this->getTargetName();
	}

/**
 * Return full name of data.
 *
 * @return string|bool Return full name of data.
 */
	public function getFullDataName() {
		$result = __('Configuration for script WPKG ver. %s', WPKG_INFO_SCRIPT_VER);
		return $result;
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo($id = null, $includeRoot = null) {
		return parent::getBreadcrumbInfo(null, $includeRoot);
	}

/**
 * Return list of query modes
 *
 * @return array Return list of query modes
 */
	public function getListQueryMode() {
		return $this->getListDataFromConstant('WPKG_CONFIG_QUERY_MODE_', 'wpkg_config_query_mode');
	}

/**
 * Return list of log levels
 *
 * @return array Return list of log levels
 */
	public function getListLogLevel() {
		return $this->getListDataFromConstant('WPKG_CONFIG_LOG_LEVEL_', 'wpkg_config_log_level');
	}

/**
 * Return data array for XML
 *
 * @return array Return data array for XML
 */
	public function getAllForXML() {
		$result = [];
		$configs = $this->getAllConfig(false);
		if (empty($configs)) {
			return $result;
		}

		$result = $configs;
		$modelVariable = ClassRegistry::init('Variable');
		$variables = $modelVariable->getXMLdata(VARIABLE_TYPE_CONFIG, 1);
		if (!empty($variables)) {
			$result['variables'] = $variables;
		}
		$modelConfigLanguage = ClassRegistry::init('ConfigLanguage');
		$languages = $modelConfigLanguage->getXMLdata();
		if (!empty($languages)) {
			$result['languages'] = $languages;
		}

		return $result;
	}

/**
 * Return data array for render XML
 *
 * @return array Return data array for render XML
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata() {
		$baseUrl = Configure::read('App.fullBaseUrl');
		$result = [
			'config' => [
				'@xmlns' => 'http://www.wpkg.org/config',
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'@xsi:schemaLocation' => 'http://www.wpkg.org/config ' . $baseUrl . '/xsd/config.xsd'
			]];
		$configs = $this->getAllForXML();
		if (empty($configs)) {
			return $result;
		}

		foreach ($configs[$this->alias] as $paramName => $paramValue) {
			$type = $this->getConfigValueType($paramName);
			switch ($type) {
				case 'int':
					if ($paramName === 'logLevel') {
						$paramValue = '0x' . dechex($paramValue);
					} else {
						$paramValue = (int)$paramValue;
					}
				break;
				case 'bool':
					$paramValue = ($paramValue ? 'true' : 'false');
				break;
				case 'string':
				default:
					$paramValue = str_replace('\\', '\\\\', $paramValue);
				break;
			}
			$xmlItemArray = [
				'@name' => $paramName,
				'@value' => $paramValue
			];
			$result['config']['param'][] = $xmlItemArray;
		}
		if (isset($configs['languages'])) {
			$result['config']['languages'] = $configs['languages'];
		}
		if (isset($configs['variables'])) {
			$result['config']['variables'] = $configs['variables'];
		}

		return $result;
	}

/**
 * Return download name
 *
 * @return string Return download name
 */
	public function getDownloadName() {
		$downloadName = 'config.xml';

		return $downloadName;
	}

/**
 * Return Xpath for data of configuration parameters
 *
 * @return string Return Xpath
 */
	public function getDataXpath() {
		$dataXpath = 'config.param';
		return $dataXpath;
	}
}
