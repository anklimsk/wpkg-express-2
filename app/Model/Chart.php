<?php
/**
 * This file is the model file of the application. Used to
 *  manage chart data.
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
 * The model is used to manage chart data.
 *
 * @package app.Model
 */
class Chart extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt'
	];

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('CHART_TYPE_', $refType);
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'charts';
		return $controllerName;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Chart');

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
 * @return bool Return False.
 */
	public function getName() {
		return false;
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
			$result = __('Chart of the %s', $typeName);
		} else {
			$result = __('chart %s', $typeName);
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
		$result = [];
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}

		$result = $modelType->getBreadcrumbInfo($refId);
		$result[] = $this->createBreadcrumb(null, false);

		return $result;
	}

/**
 * Return graph as a generated image by type ID and record ID.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating graph
 * @return string|bool Return string with content of graph in output format,
 *  or False on failure.
 */
	public function getChartData($refType = null, $refId = null) {
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return false;
		}

		return $modelType->getChartData($refId);
	}

/**
 * Return title for chart.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return string Return title for chart.
 */
	public function getChartTitle($refType = null, $refId = null) {
		$result = '';
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}

		return $modelType->getChartTitle($refType, $refId);
	}

/**
 * Return the URL to use when clicking on the chart element.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return array Return array URL.
 */
	public function getChartClickUrl($refType = null, $refId = null) {
		$result = [];
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}

		return $modelType->getChartClickUrl($refType, $refId);
	}
}
