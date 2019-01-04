<?php
/**
 * This file is the model file of the application. Used to
 *  manage package actions types.
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
 * The model is used to manage package actions types.
 *
 * @package app.Model
 */
class PackageActionType extends AppModel {

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
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PACKAGE_ACTION_TYPE],
		'ClearViewCache',
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
				'message' => 'The action type is invalid.',
				'last' => true
			],
			'isValidName' => [
				'rule' => '/^[a-zA-Z0-9]+$/',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The action type is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The action type already exists.',
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
		'PackageAction' => [
			'className' => 'PackageAction',
			'foreignKey' => 'action_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Protect builtin records;
 *  - Convert action type to lowercase.
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

		$this->data[$this->alias]['name'] = mb_strtolower($this->data[$this->alias]['name']);

		return true;
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
 * Check whether action type is a command.
 *
 * @param string $actionType Action type to check
 * @return string Return name of group data
 */
	public function isCommand($actionType = null) {
		$actionType = mb_strtolower($actionType);
		switch ($actionType) {
			case 'download':
				$result = false;
				break;
			default:
				$result = true;
		}

		return $result;
	}

/**
 * Initialization of database table the initial values
 *
 * @return bool Success
 */
	public function initDbTable() {
		$dataToSave = [];
		$actionTypes = constsToWords('ACTION_TYPE_');
		foreach ($actionTypes as $actionTypeId => $actionTypeName) {
			$dataToSave[] = [
				'id' => $actionTypeId,
				'name' => mb_strtolower($actionTypeName),
				'builtin' => true,
				'command' => $this->isCommand($actionTypeName),
			];
		}

		return (bool)$this->saveAll($dataToSave, ['callbacks' => false]);
	}

/**
 * Return information of package action type
 *
 * @param int|string $id The ID of the record to read.
 * @return array|bool Return information of package action type,
 *  or False on failure.
 */
	public function get($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [$this->alias . '.id' => $id];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.builtin',
			$this->alias . '.name',
			$this->alias . '.command',
		];
		$recursive = -1;

		return $this->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Return default values of package action type
 *
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of package action type.
 */
	public function getDefaultValues($includeModelAlias = true) {
		$defaultValues = [
			'name' => '',
			'builtin' => false,
			'command' => true,
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return list of package actions types
 *
 * @return array Return list of package actions types
 */
	public function getListActionTypes() {
		return $this->getList(null, 'package_action_type', null, null, true);
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
			$this->PackageAction->alias . '.action_type_id' => $id
		];
		$recursive = -1;
		$count = $this->PackageAction->find('count', compact('conditions', 'recursive'));
		if ($count === 0) {
			return true;
		}

		$result = __(
			'The action type of the package cannot be removed because it is used in %d %s.',
			$count,
			__dxn('plural', 'Package action type', 'action', 'actions', $count)
		);

		return $result;
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->PackageAction->Package->getParamClearCache();
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$result = 'action_types';
		return $result;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Action type');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Action types');

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
			$result = __('Package action type %s', $name);
		} else {
			$result = __('package action type %s', $name);
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
		$result = $this->PackageAction->getBreadcrumbInfo();
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

}
