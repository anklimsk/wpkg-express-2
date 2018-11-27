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
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');

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
 * Return list of not used packages for WPI
 *
 * @return array Return list of not used packages for WPI
 */
	public function getListPackagesForWPI() {
		$existsPackages = $this->getList();
		$conditions = [];
		if (!empty($existsPackages)) {
			$conditions['Package.id NOT IN'] = $existsPackages;
		}

		return $this->Package->getList($conditions);
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
			if ($exportnotes) {
				$desc = h($wpiPackage['Package']['notes']);
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

