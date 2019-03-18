<?php
/**
 * This file is the model file of the application. Used to
 *  manage exit code directory.
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

/**
 * The model is used to manage exit code directory.
 *
 * @package app.Model
 */
class ExitCodeDirectory extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'description';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = 'exit_code_directory';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'TrimStringField',
		'BreadCrumbExt',
		'GetList' => [
			'cacheConfig' => CACHE_KEY_LISTS_INFO_EXIT_CODE_DIRECTORY
		],
		'TrimStringField'
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
		'code' => [
			'numeric' => [
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The exit code is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The exit code already exists.',
				'last' => true
			],
		],
		'hexadecimal' => [
			'rule' => ['custom', '/^0x[0-9A-Fa-f]{8}$/'],
			'required' => false,
			'allowEmpty' => true,
			'message' => 'The hexadecimal value of the exit code is invalid. Format: 0x00000000.',
			'last' => true
		],
		'constant' => [
			'rule' => ['custom', '/^[0-9A-za-z_]{2,}$/'],
			'required' => false,
			'allowEmpty' => true,
			'message' => 'The constant name of the exit code is invalid. Format: letters A-Z, numbers, and underscores.',
			'last' => true
		],
		'description' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The exit code description is invalid.',
				'last' => true
			],
		]
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Convert hexadecimal and constant to uppercase;
 *  - Convert first char of description to uppercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$listFields = [
			'hexadecimal' => 'mb_strtoupper',
			'constant' => 'mb_strtoupper',
			'description' => 'mb_ucfirst'
		];
		foreach ($listFields as $fieldName => $funcName) {
			if (!isset($this->data[$this->alias][$fieldName]) || empty($this->data[$this->alias][$fieldName])) {
				continue;
			}

			$this->data[$this->alias][$fieldName] = call_user_func($funcName, $this->data[$this->alias][$fieldName]);
			if ($fieldName !== 'hexadecimal') {
				continue;
			}

			$this->data[$this->alias][$fieldName] = str_replace('0X', '0x', $this->data[$this->alias][$fieldName]);
		}

		return true;
	}

/**
 * Return record of exit code directory
 *
 * @param int|string $id The ID of the record to read.
 * @return array|bool Return record of exit code directory,
 *  or False on failure.
 */
	public function get($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [$this->alias . '.id' => $id];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.lcid',
			$this->alias . '.code',
			$this->alias . '.hexadecimal',
			$this->alias . '.constant',
			$this->alias . '.description',
		];
		$recursive = -1;

		return $this->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Return description of exit code
 *
 * @param int|string $code The exit code for retrieve description.
 * @return array|bool Return description of exit code,
 *  or False on failure.
 */
	public function getDescription($code = null) {
		$code = (string)$code;
		if ((empty($code) && ($code !== '0')) || !ctype_digit($code)) {
			return false;
		}

		$conditions = [$this->alias . '.code' => $code];
		$recursive = -1;

		return $this->field('description', $conditions);
	}

/**
 * Return default values of exit code directory record
 *
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of exit code directory record.
 */
	public function getDefaultValues($includeModelAlias = true) {
		$defaultValues = [
			'lcid' => null,
			'hexadecimal' => null,
			'constant' => null,
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return object package action Model.
 *
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel() {
		return ClassRegistry::init('Package', true);
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'exit_code_directory';
		return $controllerName;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Record of exit code directory');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Exit code directory');

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
			$result = __('Record %s of the exit code directory', $name);
		} else {
			$result = __('record %s of the exit code directory', $name);
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
		$result = [];
		$modelType = $this->getRefTypeModel();
		if (empty($modelType)) {
			return $result;
		}

		$result = $modelType->getBreadcrumbInfo();
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

}
