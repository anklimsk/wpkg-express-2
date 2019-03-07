<?php
/**
 * This file is the model file of the application. Used to
 *  manage packages of WPI.
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
App::uses('Hash', 'Utility');

/**
 * The model is used to manage packages of WPI.
 *
 * @package app.Model
 */
class Wpi extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'package_id';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = 'wpi';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'GroupAction',
		'BreadCrumbExt',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_WPI],
		'ChangeState' => ['conditionsField' => null]
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'package_id' => [
			'naturalNumber' => [
				'rule' => 'naturalNumber',
				'message' => 'Incorrect foreign key',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'on' => 'create',
				'required' => true,
				'message' => 'The package already exists.',
				'last' => true
			],
		],
		'category_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'default' => [
			'rule' => 'boolean',
			'message' => "The package's default state must be true or false.",
			'last' => true
		],
		'force' => [
			'rule' => 'boolean',
			'message' => "The package's force state must be true or false.",
			'last' => true
		],
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'Package' => [
			'className' => 'Package',
			'foreignKey' => 'package_id',
			'dependent' => false,
			'fields' => [
				'Package.id',
				'Package.enabled',
				'Package.id_text',
				'Package.name',
				'Package.notes'
			],
		],
		'WpiCategory' => [
			'className' => 'WpiCategory',
			'foreignKey' => 'category_id',
			'dependent' => false,
			'fields' => [
				'WpiCategory.id',
				'WpiCategory.name'
			],
		]
	];

/**
 * Called after each successful save operation.
 *
 * Actions:
 *  - Clear View cache.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
		clearCache('wpkg_wpi_config_js');
		clearCache('wpkg_wpi_profiles_xml');
	}

/**
 * Called after every deletion operation.
 *
 * Actions:
 *  - Clear cache.
 *
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterdelete
 */
	public function afterDelete() {
		clearCache('wpkg_wpi_config_js');
		clearCache('wpkg_wpi_profiles_xml');
	}

/**
 * Return information of WPI package
 *
 * @param int|string $id The ID of the record to read.
 * @param bool $full Flag of inclusion in the result
 *  full information.
 * @return array|bool Return information of WPI package,
 *  or False on failure.
 */
	public function get($id = null, $full = true) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.package_id',
			$this->alias . '.category_id',
			$this->alias . '.default',
			$this->alias . '.force',
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [];
		if ($full) {
			$contain = [
				'Package',
				'WpiCategory'
			];
		}

		return $this->find('first', compact('conditions', 'fields', 'contain'));
	}

/**
 * Return default values of WPI package
 *
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of WPI package.
 */
	public function getDefaultValues($includeModelAlias = true) {
		$defaultValues = [
			'package_id' => null,
			'category_id' => null,
			'default' => false,
			'force' => false,
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return data array for JS
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of host.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled hosts.
 * @return array Return data array for JS
 */
	public function getAllForJs($id = null, $exportnotes = false, $exportdisabled = false) {
		$conditions = [];
		if (!$exportdisabled) {
			$conditions['Package.enabled'] = true;
		}
		if (!empty($id)) {
			$conditions[$this->alias . '.id'] = $id;
		}

		$fields = [
			$this->alias . '.package_id',
			$this->alias . '.category_id',
			$this->alias . '.default',
			$this->alias . '.force',
			'Package.enabled',
			'Package.name',
			'Package.id_text',
			'Package.revision',
			'Package.priority',
			'WpiCategory.name',
		];
		if ($exportnotes) {
			$fields[] = 'Package.notes';
		}

		$order = [
			'Package.priority' => 'desc',
			'Package.id_text' => 'asc'
		];
		$contain = [
			'Package',
			'WpiCategory'
		];

		return $this->find('all', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return data array for XML
 *
 * @param int|string $type ID of type XML.
 * @return array Return data array for XML
 */
	public function getAllForXML($type = null) {
		$result = [];
		if ($type != WPI_XML_TYPE_PROFILES) {
			return $result;
		}

		$conditions = [
			'Package.enabled' => true
		];
		$fields = [
			$this->alias . '.package_id',
		];
		$order = ['Package.id_text' => 'asc'];
		$contain = [
			'Package'
		];

		return $this->find('all', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return data array for render XML
 *
 * @param int|string $type ID or name of type XML.
 * @param bool $exportdisable The flag of disable data export to XML.
 * @return array Return data array for render XML
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($type = null, $exportdisable = false) {
		$result = [];
		if (!empty($type) && !ctype_digit((string)$type)) {
			// @codingStandardsIgnoreStart
			$type = @constant('WPI_XML_TYPE_' . strtoupper($type));
			// @codingStandardsIgnoreEnd
		}

		$modelType = $this->getXmlTypeModel($type);
		if (empty($modelType)) {
			return $result;
		}

		$result = $modelType->getXMLdata(null, true);
		if ($exportdisable) {
			return $result;
		}

		$xmlItemArray = [];
		$xpathAttr = null;
		if ($type == WPI_XML_TYPE_HOSTS) {
			$xmlItemArray = [
				'@name' => WPI_XML_HOST_NAME,
				'@profile-id' => WPI_XML_PROFILE_NAME
			];
			$dataXpath = $modelType->getDataXpath();
			$xpathAttr = $dataXpath . '.0';
		} elseif ($type == WPI_XML_TYPE_PROFILES) {
			$xmlItemArray = [
			'@id' => WPI_XML_PROFILE_NAME
			];
			$packages = $this->getAllForXML($type);
			$modelPackagesProfile = ClassRegistry::init('PackagesProfile');
			$xmlItemArray += $modelPackagesProfile->getXMLdata($packages);
			$dataXpath = $modelType->getDataXpath();
			$xpathAttr = $dataXpath . '.0';
		} elseif ($type == WPI_XML_TYPE_WPKG) {
			$xpathAttr = $modelType->getDataXpath();
			$paramOrder = ['@name', '@value'];
			$xmlItemArray = Hash::extract($result, $xpathAttr);
			foreach ($xmlItemArray as &$param) {
				switch ($param['@name']) {
					case 'forceInstall':
						$param['@value'] = 'true';
						break;
					case 'quiet':
						$param['@value'] = 'false';
						break;
					case 'nonotify':
						$param['@value'] = 'true';
						break;
					case 'noreboot':
						$param['@value'] = 'true';
						break;
					case 'noRunningState':
						$param['@value'] = 'true';
						break;
					case 'settings_file_name':
						$param['@value'] = 'wpkg-wpi.xml';
						break;
					case 'settings_file_path':
						$param['@value'] = '%TEMP%';
						break;
					case 'noRemove':
						$param['@value'] = 'true';
						break;
					case 'sendStatus':
						$param['@value'] = 'false';
						break;
					case 'logLevel':
						$logLevels = constsVals('WPKG_CONFIG_LOG_LEVEL_');
						$logLevel = array_sum($logLevels);
						$param['@value'] = '0x' . dechex($logLevel);
						break;
					case 'log_file_path':
						$param['@value'] = '%TEMP%';
						break;
					case 'logfilePattern':
						$param['@value'] = 'wpkg-wpi-[HOSTNAME]@[DD]-[MM]-[YYYY]-[hh]-[mm]-[ss].log';
						break;
					case 'profiles_path':
					case 'hosts_path':
						$pathType = mb_strstr($param['@name'], '_path', true);
						if ($pathType === false) {
							continue;
						}

						$param['@value'] = mb_ereg_replace($pathType . '\.xml$', 'wpi/' . $pathType . '.xml', $param['@value']);
						break;
				}
				$param = array_merge(array_flip($paramOrder), $param);
			}
			unset($param);
		}
		if (empty($xpathAttr)) {
			return $result;
		}

		$result = Hash::insert($result, $xpathAttr, $xmlItemArray);

		return $result;
	}

/**
 * Return download name from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @param bool $isFullData Flag of full data
 * @return string Return download name
 */
	public function getDownloadName($xmlDataArray = [], $isFullData = false) {
		$downloadName = __('unknown') . '.xml';
		$listXmlTypes = $this->getListDataFromConstant('WPI_XML_TYPE_');
		foreach ($listXmlTypes as $type => $name) {
			$modelType = $this->getXmlTypeModel($type);
			if (empty($modelType)) {
				continue;
			}

			$path = $modelType->getDataXpath();
			if (empty($path)) {
				continue;
			}

			if (Hash::check($xmlDataArray, $path)) {
				$downloadName = $modelType->getDownloadName($xmlDataArray, true);
				break;
			}
		}

		return $downloadName;
	}

/**
 * Return list of not used packages for WPI
 *
 * @return array Return list of not used packages for WPI
 */
	public function getListPackagesForWPI() {
		$existsPackages = $this->getList();
		$conditions = ['Package.enabled' => true];
		if (!empty($existsPackages)) {
			$conditions['Package.id NOT IN'] = $existsPackages;
		}

		return $this->Package->getListPackages($conditions);
	}

/**
 * Return data array for render JS
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportdisable The flag of disable data export to JS.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of host.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled hosts.
 * @return array Return data array for render JS
 */
	public function getJSdata($id = null, $exportdisable = false, $exportnotes = false, $exportdisabled = false) {
		$result = [];
		if ($exportdisable) {
			return $result;
		}

		$wpiPackages = $this->getAllForJs($id, $exportnotes, $exportdisabled);
		if (empty($wpiPackages)) {
			return $result;
		}

		$Configurations = [];
		$SortOrder = $this->WpiCategory->getList();
		$result = compact('Configurations', 'SortOrder');

		$ordr = 0;
		foreach ($wpiPackages as $wpiPackage) {
			$ordr += 10;
			$enabled = $wpiPackage['Package']['enabled'];
			$prog = h($wpiPackage['Package']['name']) . ' (' . h($wpiPackage['Package']['revision']) . ')';
			$uid = h($wpiPackage['Package']['id_text']);
			$dflt = $wpiPackage[$this->alias]['default'];
			$forc = $wpiPackage[$this->alias]['force'];
			$cat = h($wpiPackage['WpiCategory']['name']);
			$cmds = strtr(sprintf(WPI_INSTALL_CMD_WPKG, WPI_WPKG_SCRIPT_PATH, $wpiPackage['Package']['id_text']), ['\\' => '\\\\']);
			$desc = '';
			if ($exportnotes && !empty($wpiPackage['Package']['notes'])) {
				$desc = strtr(h($wpiPackage['Package']['notes']), ["\n" => '<br />', "\r" => '']);
			}
			$result['Programs'][] = compact(
				'enabled',
				'prog',
				'uid',
				'ordr',
				'dflt',
				'forc',
				'cat',
				'cmds',
				'desc'
			);
		}

		return $result;
	}

/**
 * Return object Model for type by ID type.
 *
 * @param int|string $xmlType ID type of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getXmlTypeModel($xmlType = null) {
		$modelName = null;
		switch ($xmlType) {
			case WPI_XML_TYPE_PROFILES;
				$modelName = 'Profile';
				break;
			case WPI_XML_TYPE_HOSTS;
				$modelName = 'Host';
				break;
			case WPI_XML_TYPE_WPKG;
				$modelName = 'Config';
				break;
			default:
				return false;
		}
		$result = ClassRegistry::init($modelName, true);

		return $result;
	}

/**
 * Return the name of the controller
 *
 * @return string Return the name of the controller
 */
	public function getControllerName() {
		$result = 'wpi';
		return $result;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('WPI package');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('WPI packages');

		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getName($id = null) {
		if (empty($id)) {
			return false;
		}
		$fields = [
			'Package.' . $this->Package->displayField,
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = ['Package'];
		$data = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($data)) {
			return false;
		}

		$result = $data['Package'][$this->Package->displayField];

		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param string $typeName Object type name
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getNameExt($id = null, $typeName = null, $primary = true) {
		$name = (string)$this->getName($id);
		if (!empty($name)) {
			$name = "'" . $name . "' ";
		}
		$result = __('WPI package %s', $name);

		return $result;
	}

/**
 * Return full name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving full name
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullName($id = null, $refType = null, $refNode = null, $refId = null, $primary = true) {
		return $this->getNameExt($id, null, $primary);
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo($id = null, $refType = null, $refNode = null, $refId = null, $includeRoot = null) {
		$result = [];
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

}

