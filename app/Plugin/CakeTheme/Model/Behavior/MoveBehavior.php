<?php
/**
 * This file is the behavior file of the plugin. Is used for processing
 *  moving and drag and drop items.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');

/**
 * The behavior is used for processing moving and drag and drop items.
 *
 * @package plugin.Model.Behavior
 */
class MoveBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings.
 *
 * Actions:
 *  - Checking Tree behavior is loaded.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if Tree behavior is not loaded.
 * @return void
 */
	public function setup(Model $model, $config = []) {
		if (!$model->Behaviors->loaded('Tree')) {
			throw new InternalErrorException(__d('view_extension', 'Tree behavior is not loaded'));
		}
	}

/**
 * Return value of configuration behavior by parameter.
 *
 * @param Model $model Model using this behavior.
 * @param string $configParam Parameter of configuration behavior.
 * @return mixed|null Return value of configuration behavior for parameter,
 *  or null on failure.
 */
	protected function _getBehaviorConfig(Model $model, $configParam = null) {
		if (empty($configParam) ||
			!isset($model->Behaviors->Tree->settings[$model->alias][$configParam])) {
			return null;
		}

		$result = $model->Behaviors->Tree->settings[$model->alias][$configParam];

		return $result;
	}

/**
 * Change parent ID of item.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id The ID of item for change parent.
 * @param int|string|null $parentId New parent ID of item.
 * @return bool
 */
	protected function _changeParent(Model $model, $id = null, $parentId = null) {
		$parentField = $this->_getBehaviorConfig($model, 'parent');
		if (empty($id) || ($parentField === null)) {
			return false;
		}

		$model->recursive = -1;
		$treeItem = $model->read(null, $id);
		if (empty($treeItem)) {
			return false;
		}

		$treeItem[$model->alias][$parentField] = $parentId;
		$result = (bool)$model->save($treeItem);

		return $result;
	}

/**
 * Return list ID of subtree for item.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id The ID of the item to get a list ID of subtree.
 * @return array Return list of ID.
 */
	protected function _getSubTree(Model $model, $id = null) {
		$result = [];
		$parentField = $this->_getBehaviorConfig($model, 'parent');
		$leftField = $this->_getBehaviorConfig($model, 'left');
		if (($parentField === null) || ($leftField === null)) {
			return $result;
		}

		$conditions = [
			$model->alias . '.' . $parentField => null
		];
		if (!empty($id)) {
			$conditions[$model->alias . '.' . $parentField] = (int)$id;
		}
		if ($model->Behaviors->Tree->settings[$model->alias]['scope'] !== '1 = 1') {
			$conditions[] = $model->Behaviors->Tree->settings[$model->alias]['scope'];
		}

		$fields = [
			$model->alias . '.' . $model->primaryKey,
		];
		$order = [
			$model->alias . '.' . $leftField => 'asc'
		];
		$model->recursive = -1;
		$data = $model->find('list', compact('conditions', 'fields', 'order'));
		if (!empty($data)) {
			$result = array_keys($data);
		}

		return $result;
	}

/**
 * Move item to new position of tree
 *
 * @param Model $model Model using this behavior.
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int $id ID of record for moving
 * @param int $delta Delta for moving
 * @throws InternalErrorException if delta for moving < 0
 * @throws InternalErrorException if direction for moving not is: `up`, `down`, `top` or `bottom`
 * @triggers Model.beforeUpdateTree $model array($options)
 * @triggers Model.afterUpdateTree $model
 * @return bool Success
 */
	public function moveItem(Model $model, $direct = null, $id = null, $delta = 1) {
		$direct = mb_strtolower($direct);
		$delta = (int)$delta;
		if (in_array($direct, ['up', 'down']) && ($delta < 0)) {
			throw new InternalErrorException(__d('view_extension', 'Invalid delta for moving record'));
		}

		if (!in_array($direct, ['top', 'up', 'down', 'bottom'])) {
			throw new InternalErrorException(__d('view_extension', 'Invalid direction for moving record'));
		}

		switch ($direct) {
			case 'top':
				$delta = true;
				// no break
			case 'up':
				$method = 'moveUp';
				break;
			case 'bottom':
				$delta = true;
				// no break
			case 'down':
				$method = 'moveDown';
				break;
		}

		$newParentId = null;
		$optionsEvent = compact('id', 'newParentId', 'method', 'delta');
		$event = new CakeEvent('Model.beforeUpdateTree', $model, [$optionsEvent]);
		$model->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		$result = $model->$method($id, $delta);
		if ($result) {
			$event = new CakeEvent('Model.afterUpdateTree', $model);
			$model->getEventManager()->dispatch($event);
		}

		return $result;
	}

/**
 * Move item to new position of tree use drag and drop.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id The ID of the item to moving to new position.
 * @param int|string|null $newParentId New parent ID of item.
 * @param int|string|null $oldParentId Old parent ID of item.
 * @param array $dropData Array of ID subtree for item.
 * @return bool
 * @triggers Model.beforeUpdateTree $model array($options)
 * @triggers Model.afterUpdateTree $model
 * @see https://github.com/johnny/jquery-sortable
 */
	public function moveDrop(Model $model, $id = null, $newParentId = null, $oldParentId = null, $dropData = null) {
		if (empty($id)) {
			return false;
		}

		$changeRoot = false;
		$dataSource = $model->getDataSource();
		$dataSource->begin();
		if ($newParentId != $oldParentId) {
			$changeRoot = true;
			if (!$this->_changeParent($model, $id, $newParentId)) {
				$dataSource->rollback();

				return false;
			}
		}

		$newDataList = [];
		if (!empty($dropData) && is_array($dropData)) {
			$newDataList = Hash::extract($dropData, '0.{n}.id');
		}
		$oldDataList = $this->_getSubTree($model, $newParentId);
		if ($newDataList == $oldDataList) {
			if (!$changeRoot) {
				return true;
			}

			$dataSource->rollback();

			return false;
		}

		$indexNew = array_search($id, $newDataList);
		$indexOld = array_search($id, $oldDataList);
		if (($indexNew === false) || ($indexOld === false)) {
			return false;
		}

		$delta = $indexNew - $indexOld;
		$method = 'moveDown';
		if ($delta < 0) {
			$delta *= -1;
			$method = 'moveUp';
		}

		$optionsEvent = compact('id', 'newParentId', 'method', 'delta');
		$event = new CakeEvent('Model.beforeUpdateTree', $model, [$optionsEvent]);
		$model->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			$dataSource->rollback();

			return false;
		}

		$result = $model->$method($id, $delta);
		if ($result) {
			$dataSource->commit();
			$event = new CakeEvent('Model.afterUpdateTree', $model);
			$model->getEventManager()->dispatch($event);
		} else {
			$dataSource->rollback();
		}

		return $result;
	}
}
