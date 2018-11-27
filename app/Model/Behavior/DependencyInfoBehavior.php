<?php
/**
 * This file is the behavior file of the application. Is used to
 *  manage information of packages dependency.
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
 * The behavior is used to manage information of packages dependency
 *
 * @package app.Model.Behavior
 */
class DependencyInfoBehavior extends ModelBehavior {

/**
 * Return information of package dependency
 *
 * @param Model $model Model using this behavior
 * @param int|string $id The ID of the record to read.
 * @return array|bool Return information of package dependency,
 *  or False on failure.
 */
	public function get(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}
		$conditions = [$model->alias . '.id' => $id];
		$fields = [
			$model->alias . '.package_id',
			$model->alias . '.dependency_id',
		];
		$recursive = -1;

		return $model->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Return array for render package dependency XML elements
 *
 * @param Model $model Model using this behavior
 * @param array $data Information of package chains
 * @param string $elementName Name of element
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getDependsXMLdata(Model $model, $data = [], $elementName = null) {
		$result = [];
		if (empty($data) || !is_array($data) || empty($elementName)) {
			return $result;
		}

		$data = Hash::sort($data, '{n}.PackageDependency.id_text', 'asc', ['type' => 'string', 'ignoreCase' => true]);
		foreach ($data as $dataItem) {
			if (!$dataItem['PackageDependency']['enabled']) {
				continue;
			}
			$attribs = ['@package-id' => $dataItem['PackageDependency']['id_text']];
			if (isset($dataItem['Attribute'])) {
				$attribs = Hash::merge($attribs, $model->Attribute->getXMLnodeAttr($dataItem['Attribute']));
			}
			$result[$elementName][] = $attribs;
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
		if (empty($refId)) {
			return $result;
		}
		$data = $model->get($refId);
		if (empty($data)) {
			return $result;
		}
		$packageId = $data[$model->alias]['package_id'];
		$dependencyId = $data[$model->alias]['dependency_id'];
		$result = $model->Package->getBreadcrumbInfo($packageId);
		if (!empty($label)) {
			$result[] = $label;
		}
		$package = $model->PackageDependency->getBreadcrumbInfo($dependencyId, null, null, null, false);
		$result = array_merge($result, $package);

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
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getDependsNameExt(Model $model, $id = null, $typeName = null, $primary = true, $format = null) {
		if (empty($format)) {
			return false;
		}
		if (is_array($id)) {
			if (!isset($id[$model->alias]['package_id']) || !isset($id[$model->alias]['dependency_id'])) {
				return false;
			}
			$data = $id;
		} else {
			$data = $model->get($id);
			if (empty($data)) {
				return false;
			}
		}

		$packageId = $data[$model->alias]['package_id'];
		$dependencyId = $data[$model->alias]['dependency_id'];
		$packageName = $model->Package->getFullName($packageId, null, null, null, false);
		$packageDependencyName = $model->PackageDependency->getFullName($dependencyId, null, null, null, false);
		if (empty($packageName) || empty($packageDependencyName)) {
			return false;
		}

		$result = sprintf($format, $packageName, $packageDependencyName);
		if ($primary) {
			$result = mb_ucfirst($result);
		}

		return $result;
	}
}
