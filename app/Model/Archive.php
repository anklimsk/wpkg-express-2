<?php
/**
 * This file is the model file of the application. Used to
 *  archive package versions.
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
 * The model is used to archive package versions.
 *
 * @package app.Model
 */
class Archive extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'revision';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Restore',
		'TrimStringField',
		'BreadCrumbExt',
		'GroupAction',
		'ValidationRules'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'revision' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Incorrect package revision',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
			'isUnique' => [
				'rule' => [
					'isUnique',
					[
						'ref_id',
						'revision'
					],
					false
				],
				'on' => 'create',
				'required' => true,
				'message' => 'That package revision already exists.',
				'last' => true
			],
		],
		'ref_type' => [
			'rule' => ['checkRange', 'GARBAGE_TYPE_', false],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Incorrect foreign key'
		],
		'ref_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Incorrect foreign key'
		],
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Archive name is invalid.'
		],
		'data' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Archive data is invalid.'
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
			'foreignKey' => 'ref_id',
			'dependent' => false,
			'fields' => [
				'Package.id',
				'Package.enabled',
				'Package.id_text',
				'Package.name',
				'Package.revision'
			],
			'order' => ''
		]
	];

/**
 * Add package to archive.
 *
 * @param int|string $id ID of package record
 * @return bool|null Return True, on success or False on failure.
 *  If package already exists in archive, return Null.
 */
	public function addPackage($id = null) {
		if (empty($id)) {
			return false;
		}
		$this->Package->id = $id;
		$revision = $this->Package->field('revision');
		if (empty($revision)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_type' => GARBAGE_TYPE_PACKAGE,
			$this->alias . '.ref_id' => $id,
			$this->alias . '.revision' => $revision,
		];
		$recursive = -1;
		$amount = $this->find('count', compact('conditions', 'recursive'));
		if ($amount > 0) {
			return null;
		}

		return $this->storeData(GARBAGE_TYPE_PACKAGE, $id);
	}

/**
 * Clear archive.
 *
 * @param int|string $id Record ID of package
 * @return bool Success
 */
	public function clearArchive($id = null) {
		return $this->clearData(GARBAGE_TYPE_PACKAGE, $id);
	}

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('GARBAGE_TYPE_', $refType);
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Archive');

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
		if (empty($typeName)) {
			return false;
		}
		if ($primary) {
			$result = __('Archive of the %s', $typeName);
		} else {
			$result = __('archive %s', $typeName);
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
		$result = false;
		if (empty($id) && (empty($refType) || empty($refId))) {
			return $result;
		}
		if (empty($refType) || empty($refId)) {
			$data = $this->read(null, $id);
			if (empty($data)) {
				return $result;
			}
			$refType = $data[$this->alias]['ref_type'];
			$refId = $data[$this->alias]['ref_id'];
		}

		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}
		$typeName = $modelType->getFullName($refId, null, null, null, false);
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
		$result = $this->Package->getBreadcrumbInfo($refId);
		$result[] = $this->createBreadcrumb();

		return $result;
	}
}
