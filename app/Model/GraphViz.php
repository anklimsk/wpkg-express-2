<?php
/**
 * This file is the model file of the application. Used to
 *  manage GraphViz data.
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
App::uses('Hash', 'Utility');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::import(
	'Vendor',
	'Graph',
	['file' => 'graphviz' . DS . 'autoload.php']
);

/**
 * The model is used to manage GraphViz data.
 *
 * @package app.Model
 */
class GraphViz extends AppModel {

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
 * Path to temporary directory for store rendered files.
 *
 * @var string
 */
	public $outputDir = GRAPH_DIR;

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('GRAPH_TYPE_', $refType);
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'graph';
		return $controllerName;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Graph');

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
			$result = __('Graph of the %s', $typeName);
		} else {
			$result = __('graph %s', $typeName);
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

		if (empty($refId)) {
			$typeName = __x('graph', 'data for host');
		} else {
			$typeName = $modelType->getFullName($refId, null, null, null, false);
		}
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
 * Return path to temporary directory
 *
 * @return string Path to temporary directory
 */
	public function getGraphDir() {
		return (string)$this->outputDir;
	}

/**
 * Path to temporary file
 *
 * @return string|bool Path to temporary file,
 *  or False on failure.
 */
	protected function _getTempFilename() {
		$pathTempDir = $this->getGraphDir();
		$oGraphDir = new Folder($pathTempDir, true, 0755);
		$outputDir = $oGraphDir->path;
		$filename = tempnam($outputDir, 'graph_');

		return $filename;
	}

/**
 * Return the contents of file as a string.
 *
 * @param string $filename Path to file.
 * @return string|bool Return string on success, or False on failure
 */
	protected function _consumeContent($filename = null) {
		$oFile = new File($filename, false);
		if (!$oFile->exists()) {
			return false;
		}
		$result = $oFile->read();

		return $result;
	}

/**
 * Set attributes for `Edge` or `Node` objects.
 *
 * @param object $targetObj Target object: `Edge` or `Node`.
 * @param array $attributes Array of attributes for setting
 * @return bool Success
 */
	protected function _setAttributes($targetObj, $attributes = []) {
		if (!is_object($targetObj) || empty($attributes)) {
			return false;
		}

		$result = true;
		foreach ($attributes as $attrName => $attrValue) {
			$methodName = 'set' . $attrName;
			if (!call_user_func([$targetObj, $methodName], $attrValue)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Create `Node` object
 *
 * @param array $nodeInfo Array of information for `Node`
 * @return object|bool Return object `Node`, or False on failure.
 */
	protected function _createNode($nodeInfo = []) {
		if (empty($nodeInfo)) {
			return false;
		}
		if (empty($nodeInfo['label']) || empty($nodeInfo['name'])) {
			return false;
		}

		$node = new \phpDocumentor\GraphViz\Node($nodeInfo['name'], $nodeInfo['label']);
		$attributes = $nodeInfo['attrib']['common'] + $nodeInfo['attrib']['node'];
		if (!$this->_setAttributes($node, $attributes)) {
			return false;
		}

		return $node;
	}

/**
 * Return array of information for creating `Node` object
 *  from array by name.
 *
 * @param string $name Name of `Node` for processing
 * @param array $info Array of informations for `Node`
 * @return array Return array of information
 */
	protected function _getParentInfo($name = null, $info = null) {
		$result = [];
		if (empty($name) || empty($info) || !is_array($info)) {
			return $result;
		}

		foreach ($info as $i => $dataItem) {
			if ($dataItem['name'] === $name) {
				$result = $info[$i];
				break;
			}
		}

		return $result;
	}

/**
 * Create graph as a generated image by data.
 *
 * @param array $data Data for creating graph
 * @param string $graphName Name of graph
 * @param string $exportType Output format
 * @return string|bool Return string with content of graph in output format,
 *  or False on failure.
 * @link https://graphviz.gitlab.io/_pages/doc/info/output.html
 */
	protected function _createGraph($data = [], $graphName = null, $exportType = null) {
		if (empty($data) || !is_array($data)) {
			return false;
		}

		if (empty($exportType)) {
			$exportType = GRAPH_OUTPUT_FORMAT;
		}

		$graphLabel = $graphName;
		if (empty($graphName)) {
			$graphName = 'G';
		}
		$graph = \phpDocumentor\GraphViz\Graph::create($graphName);
		if (!empty($graphLabel)) {
			$graph->setlabel($graphLabel);
		}
		$data = Hash::sort($data, '{n}.parent', 'asc');
		foreach ($data as $i => &$info) {
			$node = $this->_createNode($info);
			if (!$node) {
				continue;
			}
			$graph->setNode($node);

			if (!empty($info['parent'])) {
				$nodeParent = $graph->findNode($info['parent']);
				if (!$nodeParent) {
					$parentInfo = $this->_getParentInfo($info['parent'], $data);
					if (!empty($parentInfo)) {
						$nodeParent = $this->_createNode($parentInfo);
					}
				}
				if (!$nodeParent) {
					continue;
				}

				$edge = new \phpDocumentor\GraphViz\Edge($nodeParent, $node);
				$attributes = $info['attrib']['common'] + $info['attrib']['edge'];
				if (!$this->_setAttributes($edge, $attributes)) {
					return false;
				}
				$graph->link($edge);
			}
		}
		unset($info);
		$this->clearDir();
		$filename = $this->_getTempFilename();
		$graph->export($exportType, $filename);
		$result = $this->_consumeContent($filename);

		return $result;
	}

/**
 * Return graph as a generated image by type ID and record ID.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating graph
 * @param bool $full Flag of including full data in the result
 * @param string $exportType Output format
 * @return string|bool Return string with content of graph in output format,
 *  or False on failure.
 * @link https://graphviz.gitlab.io/_pages/doc/info/output.html
 */
	public function getGraph($refType = null, $refId = null, $full = false, $exportType = null) {
		$graphName = $this->getFullName(null, $refType, null, $refId);
		if (empty($graphName)) {
			return false;
		}
		if ($full) {
			$graphName .= ' ' . __x('graph', 'full');
		}

		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return false;
		}

		$result = '';
		$data = $modelType->getGraphData($refId, $full);
		if (empty($data)) {
			return $result;
		}

		return $this->_createGraph($data, $graphName, $exportType);
	}

/**
 * Return graph as a generated image by host name.
 *
 * @param string $hostName Name of host for generating graph
 * @param bool $full Flag of including full data in the result
 * @param string $exportType Output format
 * @return string|bool Return string with content of graph in output format,
 *  or False on failure.
 * @link https://graphviz.gitlab.io/_pages/doc/info/output.html
 */
	public function buildGraph($hostName = null, $full = false, $exportType = null) {
		if (empty($hostName)) {
			return false;
		}

		$refType = GRAPH_TYPE_HOST;
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return false;
		}
		$result = '';
		$hosts = $modelType->getAllHostsForGraph($hostName);
		if (empty($hosts)) {
			return $result;
		}

		$data = [];
		foreach ($hosts as $hostId) {
			$dataItem = $modelType->getGraphData($hostId, $full);
			if (!empty($dataItem)) {
				$data = array_merge($data, $dataItem);
			}
		}
		$graphName = __("Graph for host '%s'", $hostName);

		return $this->_createGraph($data, $graphName, $exportType);
	}

/**
 * Cleanup the graph directory from old files
 *
 * @param int $storageTime Maximum time of store old graph files.
 * @param int $timeNow Timestamp of current date and time.
 * @return bool Success
 */
	public function clearDir($storageTime = null, $timeNow = null) {
		if (empty($storageTime)) {
			$storageTime = GRAPH_STORE_FILE_TIME_LIMIT;
		} else {
			$storageTime = (int)$storageTime;
		}

		$clearPath = $this->getGraphDir();
		if (!file_exists($clearPath)) {
			return false;
		}

		$result = true;
		$oFolder = new Folder($clearPath, true);
		$uploadedFiles = $oFolder->find('.*', false);
		if (empty($uploadedFiles)) {
			return $result;
		}

		if (!empty($timeNow)) {
			$timeNow = (int)$timeNow;
		} else {
			$timeNow = time();
		}

		$uploadedFilesPath = $oFolder->pwd();
		foreach ($uploadedFiles as $uploadedFile) {
			$oFile = new File($uploadedFilesPath . DS . $uploadedFile);
			$lastChangeTime = $oFile->lastChange();
			if ($lastChangeTime === false) {
				continue;
			}

			if (($timeNow - $lastChangeTime) > $storageTime) {
				if (!$oFile->delete()) {
					$result = false;
				}
			}
		}

		return true;
	}
}
