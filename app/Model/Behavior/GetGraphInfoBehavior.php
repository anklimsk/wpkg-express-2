<?php
/**
 * This file is the behavior file of the application. Is used to
 *  process data to build a dependency graph.
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
App::uses('Inflector', 'Utility');
App::uses('Router', 'Routing');
App::uses('UserInfo', 'CakeLdap.Utility');

/**
 * The behavior is used to process data to build a
 *  dependency graph.
 *
 * @package app.Model.Behavior
 */
class GetGraphInfoBehavior extends ModelBehavior {

/**
 * Defaults
 *
 * @var array
 */
	protected $_defaults = [
		'deepLimit' => GRAPH_DEEP_LIMIT,
		'dataLimit' => GRAPH_DATA_LIMIT,
	];

/**
 * Cache of processed records ID
 *
 * @var array
 */
	protected $_listProcessedId = [];

/**
 * Model name during recursion initialization
 *
 * @var string
 */
	protected $_initModelName = null;

/**
 * Prefix of user role
 *
 * @var string
 */
	protected $_userRolePrefix = null;

/**
 * Controller name for target model
 *
 * @var string
 */
	protected $_controllerName = null;

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = []) {
		$this->settings[$model->alias] = $config + $this->_defaults;

		$objUserInfo = new UserInfo();
		$this->_userRolePrefix = $objUserInfo->getUserField('prefix');

		$this->_controllerName = Inflector::pluralize(mb_strtolower($model->name));
	}

/**
 * Store model name during recursion initialization
 *
 * @param string $modelName Model name to store
 * @return void
 */
	protected function _setInitModelName($modelName = null) {
		$this->_initModelName = $modelName;
	}

/**
 * Return model name during recursion initialization
 *
 * @return string Return model name
 */
	public function getInitModelName() {
		return $this->_initModelName;
	}

/**
 * Return limit for processing data
 *
 * @param Model $model Model using this behavior
 * @return int Return limit for processing data
 */
	protected function _getLimitGraphData(Model $model) {
		return (int)$this->settings[$model->alias]['dataLimit'];
	}

/**
 * Return limit for deep recursion
 *
 * @param Model $model Model using this behavior
 * @return int Return limit for deep recursion
 */
	public function getLimitGraphDeep(Model $model) {
		return (int)$this->settings[$model->alias]['deepLimit'];
	}

/**
 * Clear cache of processed records ID
 *
 * @param Model $model Model using this behavior
 * @return void
 */
	public function clearListProcessedId(Model $model) {
		$this->_listProcessedId[$model->alias] = [];
	}

/**
 * Add ID to cache of processed records
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to add
 * @return void
 */
	public function addToListProcessedId(Model $model, $id = null) {
		if (empty($id)) {
			return;
		}

		$id = (int)$id;
		$this->_listProcessedId[$model->alias][$id] = $id;
	}

/**
 * Checking record ID is processed record
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to check
 * @return bool Return True, if ID is processed
 */
	public function checkIsProcessedId(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}

		$id = (int)$id;
		if (!isset($this->_listProcessedId[$model->alias][$id])) {
			return false;
		}

		return true;
	}

/**
 * Checking that the limit of processing data has been reached
 *
 * @param Model $model Model using this behavior
 * @return bool Return True, if limit of processing data has been reached
 */
	public function checkLimitListProcessedId(Model $model) {
		$dataLimit = $this->_getLimitGraphData($model);
		if ((count($this->_listProcessedId, COUNT_RECURSIVE) - count($this->_listProcessedId, COUNT_NORMAL)) > $dataLimit) {
			return false;
		}

		return true;
	}

/**
 * Return the node ID of the graph by name
 *
 * @param Model $model Model using this behavior
 * @param string $name Name of graph node
 * @return string Return the node ID
 */
	public function getIdNode(Model $model, $name = null) {
		if (empty($name)) {
			return false;
		}
		$result = $model->name . '_' . Inflector::slug($name);

		return $result;
	}

/**
 * Return the name of the controller
 *
 * @param Model $model Model using this behavior
 * @return string Return the name of the controller
 */
	public function getControllerName(Model $model) {
		return $this->_controllerName;
	}

/**
 * Returns the role prefix for the current user.
 *
 * @return string Returns the role prefix
 */
	protected function _getUserRolePrefix() {
		return $this->_userRolePrefix;
	}

/**
 * Return full data for build a dependency graph.
 *  Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to retrieve data
 * @param string $parent Name of parent graph node
 * @return array Return full data for build a dependency graph.
 */
	public function getGraphDataFull(Model $model, $id = null, $parent = null) {
		$result = [];
		return $result;
	}

/**
 * Return data for build a dependency graph.
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to retrieve data
 * @param bool $full Flag of including full data in the result
 * @return array Return data for build a dependency graph.
 */
	public function getGraphData(Model $model, $id = null, $full = false) {
		$result = [];
		if (empty($id)) {
			return $result;
		}

		set_time_limit(GRAPH_GENERATE_TIME_LIMIT);
		$this->_setInitModelName($model->name);
		$level = 1;
		$deepLimit = $this->getLimitGraphDeep($model);
		$this->clearListProcessedId($model);
		$this->_getGraphDataRec($model, $result, $id, null, $full, $level, $deepLimit);

		return $result;
	}

/**
 * Return information about data dependencies.
 *  Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @return array Return information about data dependencies
 */
	public function getGraphDependencyInfo(Model $model) {
		$result = [];
		return $result;
	}

/**
 * Return information about graph data style
 *  Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @return array Return information about graph data style
 */
	public function getGraphDataStyle(Model $model) {
		$result = [];
		return $result;
	}

/**
 * Return information about graph data shape
 *  Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @return array Return information about graph data shape
 */
	public function getGraphDataShape(Model $model) {
		$result = 'ellipse';
		return $result;
	}

/**
 * Recursive data processing for build a dependency graph
 *
 * @param Model $model Model using this behavior
 * @param array &$result Result of processing data
 * @param int|string $id Record ID to processing data
 * @param string $parent Name of parent graph node
 * @param bool $full Flag of including full data in the result
 * @param int $level Current level of recursion
 * @param int $deepLimit Limit for deep recursion
 * @throws InternalErrorException if method "getAllForGraph" is not found in model
 * @return void
 */
	protected function _getGraphDataRec(Model $model, array &$result, $id = null, $parent = null, $full = false, $level = 1, $deepLimit = GRAPH_DEEP_LIMIT) {
		if (!method_exists($model, 'getAllForGraph')) {
			throw new InternalErrorException(__("Method '%s' is not found in model %s", 'getAllForGraph', $model->name));
		}

		if (($level > $deepLimit) || $this->checkIsProcessedId($model, $id) ||
			!$this->checkLimitListProcessedId($model)) {
			return;
		}

		$graphDataItem = $model->getAllForGraph($id);
		if (empty($graphDataItem)) {
			return;
		}

		$graphDataItemId = Hash::get($graphDataItem, $model->alias . '.id');
		if (empty($graphDataItemId)) {
			return;
		}

		$this->addToListProcessedId($model, $graphDataItemId);
		if (empty($parent)) {
			$node = $model->getGraphDataNode($graphDataItem[$model->alias], $parent);
			if (empty($node)) {
				return;
			}
			$result[] = $node;
			$parent = $node['name'];
			if (empty($parent)) {
				return;
			}
		}
		if ($full) {
			$fullData = $model->getGraphDataFull($graphDataItemId, $parent);
			if (!empty($fullData)) {
				$result = array_merge($result, $fullData);
			}
		}

		$dependencyInfo = $model->getGraphDependencyInfo();
		if (empty($dependencyInfo)) {
			return;
		}

		$level++;
		foreach ($dependencyInfo as $dependName => $dependDataInfo) {
			if (!isset($graphDataItem[$dependName])) {
				continue;
			}

			if (isAssoc($graphDataItem[$dependName])) {
				$graphDataItem[$dependName] = [$graphDataItem[$dependName]];
			}

			$dependModel = Hash::get($dependDataInfo, 'dependModel');
			$tergetModel = Hash::get($dependDataInfo, 'targetModel');
			if (empty($tergetModel)) {
				$objTargetModel = $model;
			} else {
				$objTargetModel = $model->$tergetModel;
			}
			foreach ($graphDataItem[$dependName] as $dependData) {
				if (empty($dependModel)) {
					$nodeData = $dependData;
				} elseif (isset($dependData[$dependModel])) {
					$nodeData = $dependData[$dependModel];
				} else {
					continue;
				}

				$nodeDataId = Hash::get($nodeData, 'id');
				$edgeLabel = Hash::get($dependDataInfo, 'dependLabel');
				$arrowhead = Hash::get($dependDataInfo, 'arrowhead');
				$node = $objTargetModel->getGraphDataNode($nodeData, $parent, $arrowhead, $edgeLabel);
				if (empty($node)) {
					continue;
				}

				$result[] = $node;
				$parentNode = $node['name'];
				if (empty($parentNode) || !$full) {
					continue;
				}

				$this->_getGraphDataRec($objTargetModel, $result, $nodeDataId, $parentNode, $full, $level, $deepLimit);
			}
		}
	}

/**
 * Return data node graph
 *
 * @param Model $model Model using this behavior
 * @param array $nodeData Data of node for processing
 * @param string $parent Name of parent graph node
 * @param string $arrowhead Style of arrowhead on the head node of an edge.
 * @param string $edgeLabel Text label attached to edge
 * @throws InternalErrorException if method "getControllerName" is not found in model
 * @return array Return data node graph
 */
	public function getGraphDataNode(Model $model, $nodeData = null, $parent = null, $arrowhead = 'normal', $edgeLabel = null) {
		$result = [];
		if (empty($nodeData)) {
			return $result;
		}

		$graphDataItemName = Hash::get($nodeData, $model->displayField);
		$name = $model->getIdNode($graphDataItemName);
		if (empty($graphDataItemName) || empty($name)) {
			return $result;
		}

		$graphDataItemId = Hash::get($nodeData, 'id');
		$graphDataItemEnabled = Hash::get($nodeData, 'enabled');

		if (empty($arrowhead)) {
			$arrowhead = 'normal';
		}
		$label = addslashes($graphDataItemName);
		$style = $model->getGraphDataStyle();
		$fillcolor = '';
		if (!$graphDataItemEnabled) {
			$style[] = 'dashed';
			$fillcolor = 'grey';
		}
		$style = implode(',', $style);
		$shape = $model->getGraphDataShape();
		$URL = null;
		if ($graphDataItemId) {
			if (!$model->Behaviors->loaded('GetGraphInfo') &&
				!method_exists($model, 'getControllerName')) {
				throw new InternalErrorException(__("Method '%s' is not found in model %s", 'getControllerName', $model->name));
			}

			$controller = $model->getControllerName();
			$action = 'view';
			$urlData = compact('controller', 'action');
			$urlData[] = $graphDataItemId;
			$userRole = $this->_getUserRolePrefix();
			if (!empty($userRole)) {
				$urlData[$userRole] = true;
			}
			$URL = Router::url($urlData);
		}
		$target = '_blank';
		$attrib = [
			'common' => compact('style', 'fillcolor', 'arrowhead'),
			'node' => compact('shape', 'URL', 'target'),
			'edge' => ['label' => $edgeLabel]
		];

		$result = compact('name', 'parent', 'label', 'attrib');
		return $result;
	}

}
