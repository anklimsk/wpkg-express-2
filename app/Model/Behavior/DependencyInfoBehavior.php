<?php
/**
 * This file is the behavior file of the application. Is used to
 *  manage dependency information of package or profile.
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

App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

/**
 * The behavior is used to manage dependency information of package or profile
 *
 * @package app.Model.Behavior
 */
class DependencyInfoBehavior extends ModelBehavior {

/**
 * Defaults
 *
 * @var array
 */
	protected $_defaults = [
		'mainModelName' => 'Package',
		'dependencyModelName' => 'PackageDependency',
		'mainFieldName' => 'package_id',
		'dependencyFieldName' => 'dependency_id',
	];

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if specified main model is not associated
 *  with the current model
 * @throws InternalErrorException if specified dependency model is not
 *  associated with the current model
 * @throws InternalErrorException if main ID field is not found in model
 * @throws InternalErrorException if dependency ID field is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config + $this->_defaults;
		extract($this->settings[$model->alias]);
		if (empty($mainModelName) || !$model->getAssociated($mainModelName)) {
			throw new InternalErrorException(__("Invalid name of main model for model '%s'", $model->name));
		}
		if (empty($dependencyModelName) || !$model->getAssociated($mainModelName)) {
			throw new InternalErrorException(__("Invalid name of dependency model for model '%s'", $model->name));
		}
		if (!empty($mainFieldName) && !$model->hasField($mainFieldName)) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", $mainFieldName, $model->name));
		}
		if (!empty($dependencyFieldName) && !$model->hasField($dependencyFieldName)) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", $dependencyFieldName, $model->name));
		}
	}

/**
 * Return information of dependency
 *
 * @param Model $model Model using this behavior
 * @param int|string $id The ID of the record to read.
 * @return array|bool Return information of dependency,
 *  or False on failure.
 */
	public function get(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}
		extract($this->settings[$model->alias]);
		$conditions = [$model->alias . '.id' => $id];
		$fields = [
			$model->alias . '.' . $mainFieldName,
			$model->alias . '.' . $dependencyFieldName,
		];
		$recursive = -1;

		return $model->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Return array for render dependency XML elements
 *
 * @param Model $model Model using this behavior
 * @param array $data Information of dependency
 * @param string $dependencyIdAttribute ID attribute of dependency element
 * @param string $elementName Name of dependency element
 * @throws InternalErrorException if $dependencyIdAttribute is empty
 * @throws InternalErrorException if $elementName is empty
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getDependsXMLdata(Model $model, $data = [], $dependencyIdAttribute = null, $elementName = null) {
		if (empty($dependencyIdAttribute)) {
			throw new InternalErrorException(__("Invalid dependency ID attribute for model '%s'", $model->name));
		}
		if (empty($elementName)) {
			throw new InternalErrorException(__("Invalid dependency element name for model '%s'", $model->name));
		}

		$result = [];
		if (empty($data) || !is_array($data)) {
			return $result;
		}

		$dependencyModelName = $this->settings[$model->alias]['dependencyModelName'];
		$data = $this->_sortDependencyData($model, $data);
		foreach ($data as $dataItem) {
			if (!$dataItem[$dependencyModelName]['enabled']) {
				continue;
			}
			$attribs = ['@' . $dependencyIdAttribute => $dataItem[$dependencyModelName]['id_text']];
			if (isset($dataItem['Attribute']) && !empty($dataItem['Attribute'])) {
				$attribs = Hash::merge($attribs, $model->Attribute->getXMLnodeAttr($dataItem['Attribute']));
			}
			if (isset($dataItem['Check']) && !empty($dataItem['Check'])) {
				$attribs['condition'] = $model->Check->getXMLdata($dataItem['Check']);
			}
			$result[$elementName][] = $attribs;
		}

		return $result;
	}

/**
 * Return information of main and dependency ID fields.
 *
 * @param Model $model Model using this behavior
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @return string|bool Return information of ID,
 *  or False on failure.
 */
	protected function _getIdInfo(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}

		if (is_array($id)) {
			$data = $id;
		} else {
			$data = $this->get($model, $id);
			if (empty($data)) {
				return false;
			}
		}

		extract($this->settings[$model->alias]);
		$mainId = Hash::get($data, $model->alias . '.' . $mainFieldName);
		$dependencyId = Hash::get($data, $model->alias . '.' . $dependencyFieldName);
		if (empty($mainId) || empty($dependencyId)) {
			return false;
		}

		$result = compact('mainId', 'dependencyId');
		return $result;
	}

/**
 * Return name of data.
 *
 * @param Model $model Model using this behavior
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param string $typeName Object type name
 * @param bool $primary Flag of direct method call or nested
 * @param string $format Format string for sprintf with two parameters
 * @param bool $reverseFormatName Flag of reverse parameters order in
 *  format string
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getDependsNameExt(Model $model, $id = null, $typeName = null, $primary = true, $format = null, $reverseFormatName = false) {
		if (empty($format)) {
			return false;
		}

		$idInfo = $this->_getIdInfo($model, $id);
		if (!$idInfo) {
			return false;
		}
		extract($idInfo);
		extract($this->settings[$model->alias]);

		$name = $model->$mainModelName->getFullName($mainId, null, null, null, false);
		$dependencyName = $model->$dependencyModelName->getFullName($dependencyId, null, null, null, false);
		if (empty($name) || empty($dependencyName)) {
			return false;
		}

		if ($reverseFormatName) {
			$tempName = $name;
			$name = $dependencyName;
			$dependencyName = $tempName;
		}
		$result = sprintf($format, $name, $dependencyName);
		if ($primary) {
			$result = mb_ucfirst($result);
		}

		return $result;
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param Model $model Model using this behavior
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @param string $label Label for breadcrumb
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getDependsBreadcrumbInfo(Model $model, $id = null, $refType = null, $refNode = null, $refId = null, $includeRoot = null, $label = null) {
		$result = [];
		$idInfo = $this->_getIdInfo($model, $id);
		if (!$idInfo) {
			return $result;
		}
		extract($idInfo);
		extract($this->settings[$model->alias]);

		$result = $model->$mainModelName->getBreadcrumbInfo($mainId);
		if (!empty($label)) {
			$result[] = $label;
		}
		$breadCrumbsDependency = $model->$dependencyModelName->getBreadcrumbInfo($dependencyId, null, null, null, false);
		$result = array_merge($result, $breadCrumbsDependency);

		return $result;
	}

/**
 * Return sorted data by `id_text` value.
 *
 * @param Model $model Model using this behavior
 * @param array $data Data to sort
 * @return mixed Return sorted data.
 */
	protected function _sortDependencyData(Model $model, $data = []) {
		if (empty($data) || !is_array($data)) {
			return $data;
		}

		$dependencyModelName = $this->settings[$model->alias]['dependencyModelName'];
		$data = Hash::sort(
			$data,
			'{n}.' . $dependencyModelName . '.id_text',
			'asc',
			[
				'type' => 'string',
				'ignoreCase' => true
			]
		);

		return $data;
	}

/**
 * Return sorted data by `id_text` value include model name.
 *
 * @param Model $model Model using this behavior
 * @param array $data Data to sort
 * @return mixed Return sorted data.
 */
	public function sortDependencyData(Model $model, $data = []) {
		if (empty($data) || !is_array($data) ||
			!isset($data[$model->alias]) || empty($data[$model->alias])) {
			return $data;
		}

		$data[$model->alias] = $this->_sortDependencyData($model, $data[$model->alias]);
		return $data;
	}
}
