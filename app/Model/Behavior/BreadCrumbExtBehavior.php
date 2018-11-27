<?php
/**
 * This file is the behavior file of the application. Is used for getting
 *  information for creating breadcrumb navigation.
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
 * @package app.Model.Behavior
 */

App::uses('BreadCrumbBehavior', 'CakeTheme.Model/Behavior');
App::uses('ClassRegistry', 'Utility');

/**
 * The behavior is used for getting nformation for creating breadcrumb navigation.
 *
 * @package app.Model.Behavior
 */
class BreadCrumbExtBehavior extends BreadCrumbBehavior {

/**
 * Return name of data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param string $typeName Object type name
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getNameExt(Model $model, $id = null, $typeName = null, $primary = true) {
		return false;
	}

/**
 * Return full name of data.
 *
 * @param Model $model Model using this behavior.
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullDataName(Model $model) {
		return false;
	}

/**
 * Return full name of data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving full name
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullName(Model $model, $id = null, $refType = null, $refNode = null, $refId = null, $primary = true) {
		if (empty($id)) {
			$result = $model->getFullDataName();
		} else {
			$result = $model->getNameExt($id, null, $primary);
		}

		return $result;
	}

/**
 * Return value of field by the record ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id ID of record
 *  for retrieving value
 * @param string $fieldName Name of field for retrieving value
 * @return mixed|bool Return value of field,
 *  or False on failure.
 */
	protected function _getFieldValue(Model $model, $id = null, $fieldName = null) {
		if (empty($id) || !$model->hasField($fieldName)) {
			return false;
		}

		$model->id = $id;
		return $model->field($fieldName);
	}

/**
 * Return ID of the associated record by the record ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id ID of record
 *  for retrieving associated record ID
 * @return string|bool Return associated record ID,
 *  or False on failure.
 */
	public function getRefId(Model $model, $id = null) {
		return $this->_getFieldValue($model, $id, 'ref_id');
	}

/**
 * Return the type ID of associated record by the record ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id ID of record
 *  for retrieving type ID of associated record
 * @return string|bool Return type ID of associated record,
 *  or False on failure.
 */
	public function getRefType(Model $model, $id = null) {
		return $this->_getFieldValue($model, $id, 'ref_type');
	}

/**
 * Return constant name by prefix and value
 *
 * @param Model $model Model using this behavior.
 * @param string $prefix Prefix of constant.
 * @param mixed $val Value of constant.
 * @return string Return constant name
 */
	public function getNameConstantForVal(Model $model, $prefix = null, $val = null) {
		return constValToLcSingle($prefix, $val, false, false, false);
	}

/**
 * Return type name by type ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refType ID of type.
 * @return string Return type name
 */
	public function getNameTypeFor(Model $model, $refType = null) {
		$result = '';

		return $result;
	}

/**
 * Return node name by node ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refNode ID of node
 * @return string Return node name
 */
	public function getNameNodeFor(Model $model, $refNode = null) {
		$result = '';

		return $result;
	}

/**
 * Return object Model for type by ID type.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refType ID type of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel(Model $model, $refType = null) {
		$type = $model->getNameTypeFor($refType);
		if (empty($type)) {
			return false;
		}

		$modelName = ucfirst($type);
		return ClassRegistry::init($modelName, true);
	}

/**
 * Return object Model for node by ID node.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refNode ID node of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefNodeModel(Model $model, $refNode = null) {
		return false;
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo(Model $model, $id = null, $refType = null, $refNode = null, $refId = null, $includeRoot = null) {
		return parent::getBreadcrumbInfo($model, $id, $includeRoot);
	}
}
