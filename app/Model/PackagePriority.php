<?php
/**
 * This file is the model file of the application. Used to
 *  manage package priorities.
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
 * The model is used to manage package priorities.
 *
 * @package app.Model
 */
class PackagePriority extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'name';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'TrimStringField',
		'BreadCrumbExt',
	];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The priority name is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The priority name already exists.',
				'last' => true
			],
		],
		'value' => [
			'numeric' => [
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The priority is not an integer.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The priority already exists.',
				'last' => true
			],
		]
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
			'foreignKey' => '',
			'conditions' => ['Package.revision = PackagePriority.value'],
			'fields' => '',
			'order' => ''
		],
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Convert first char of priority to uppercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		if (isset($this->data[$this->alias]['name'])) {
			$this->data[$this->alias]['name'] = mb_ucfirst($this->data[$this->alias]['name']);
		}

		return true;
	}

/**
 * Called after each successful save operation.
 *
 * Actions:
 *  - Clear cache.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = array()) {
		return Cache::clear(false, CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY);
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
		return Cache::clear(false, CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY);
	}

/**
 * Initialization of database table the initial values
 *
 * @return bool Success
 */
	public function initDbTable() {
		$dataToSave = [];
		$packagePriorities = constsToWords('PACKAGE_PRIORITY_');
		foreach ($packagePriorities as $priorityValue => $priorityName) {
			$dataToSave[] = [
				'name' => mb_ucfirst($priorityName),
				'value' => $priorityValue,
			];
		}

		return (bool)$this->saveAll($dataToSave);
	}

/**
 * Return list of package priorities
 *
 * @return array Return list of package priorities
 */
	public function getListPriorities() {
		$currUIlang = (string)Configure::read('Config.language');
		$cachePath = 'ListInfo.' . md5($currUIlang);
		$cached = Cache::read($cachePath, CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY);
		if (!empty($cached)) {
			return $cached;
		}

		$result = [];
		$conditions = [];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.name',
			$this->alias . '.value',
		];
		$order = [$this->alias . '.value' => 'asc'];
		$recursive = -1;
		$data = $this->find('all', compact('conditions', 'fields', 'order', 'recursive'));
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $dataItem) {
			$result[$dataItem[$this->alias]['value']] = __d('package_priority', h($dataItem[$this->alias]['name'])) .
				' (' . h($dataItem[$this->alias]['value']) . ')';
		}
		Cache::write($cachePath, $result, CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY);

		return $result;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Package priority');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Package priorities');

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
			$name = "'" . $name . "'";
		}
		if ($primary) {
			$result = __('Package priority %s', $name);
		} else {
			$result = __('package priority %s', $name);
		}

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
		$result = $this->getNameExt($id, $typeName, $primary);

		return $result;
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
		$result = $this->Package->getBreadcrumbInfo();
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

}
