<?php
/**
 * This file is the model file of the application. Used to
 *  manage WPI categories.
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
 * The model is used to manage WPI categories.
 *
 * @package app.Model
 */
class WpiCategory extends AppModel {

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
		'CanDisable',
		'TrimStringField',
		'BreadCrumbExt',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_WPI_CATEGORY],
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
				'message' => 'The WPI category is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The WPI category already exists.',
				'last' => true
			],
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'Wpi' => [
			'className' => 'Wpi',
			'foreignKey' => 'category_id',
			'dependent' => true,
			'fields' => [
				'Wpi.id',
				'Wpi.category_id'
			]
		],
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Protect builtin records;
 *  - Convert first char of category to uppercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		if (!isset($this->data[$this->alias]['builtin'])) {
			$this->data[$this->alias]['builtin'] = false;
		} elseif (isset($this->data[$this->alias]['id']) &&
			!empty($this->data[$this->alias]['id']) &&
			$this->data[$this->alias]['builtin']) {
			return false;
		}

		$this->data[$this->alias]['name'] = mb_ucfirst($this->data[$this->alias]['name']);

		return true;
	}

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
 * Called before every deletion operation.
 *
 * Actions:
 *  - Protect builtin and used records.
 *
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
 */
	public function beforeDelete($cascade = true) {
		$builtin = $this->field('builtin');
		if ($builtin) {
			return false;
		}

		return true;
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
 * Initialization of database table the initial values
 *
 * @return bool Success
 */
	public function initDbTable() {
		$dataToSave = [];
		$wpiCategories = constsVals('WPI_CATEGORY_');
		foreach ($wpiCategories as $wpiCategoryName) {
			$dataToSave[] = [
				'name' => mb_ucfirst($wpiCategoryName),
				'builtin' => true,
			];
		}

		return (bool)$this->saveAll($dataToSave, ['callbacks' => false]);
	}

/**
 * Return information of WPI category
 *
 * @param int|string $id The ID of the record to read.
 * @return array|bool Return information of WPI category,
 *  or False on failure.
 */
	public function get($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [$this->alias . '.id' => $id];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.name',
			$this->alias . '.builtin',
		];
		$recursive = -1;
		return $this->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Checking the ability to perform operations `delete`
 *
 * @param int|string $id Record ID to check
 * @return bool|string Return True, if possible. False on failure or 
 *  error message if not possible.
 */
	public function checkDisable($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [
			$this->Wpi->alias . '.category_id' => $this->id
		];
		$recursive = -1;
		$count = $this->Wpi->find('count', compact('conditions', 'recursive'));
		if ($count === 0) {
			return true;
		}

		$result = __(
			'The category of WPI cannot be removed because it is used in %d %s.',
			$count,
			__dxn('plural', 'WPI category', 'package', 'packages', $count)
		);

		return $result;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('WPI category');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('WPI categories');

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
		if ($name !== '') {
			$name = "'" . $name . "' ";
		}
		$result = __('WPI category %s', $name);

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
		$result = $this->Wpi->getBreadcrumbInfo();
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}
}
