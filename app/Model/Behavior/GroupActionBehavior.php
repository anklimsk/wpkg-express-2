<?php
/**
 * This file is the behavior file of the application. Is used for processing
 *  group of data.
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
App::uses('ClassRegistry', 'Utility');
App::uses('RenderXmlData', 'Utility');

/**
 * The behavior is used for processing group of data.
 *
 * @package app.Model.Behavior
 */
class GroupActionBehavior extends ModelBehavior {

/**
 * Object of model `ExtendQueuedTask`
 *
 * @var object
 */
	protected $_modelExtendQueuedTask = null;

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if method "getTargetName" and "getGroupName" is not found in model
 * @return void
 */
	public function setup(Model $model, $config = []) {
		$requiredMethods = [
			'getTargetName',
			'getGroupName'
		];
		foreach ($requiredMethods as $requiredMethod) {
			if (!method_exists($model, $requiredMethod)) {
				throw new InternalErrorException(__("Method '%s' is not found in model %s", $requiredMethod, $model->name));
			}
		}

		$this->_modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
	}

/**
 * Return list of data to process
 *
 * @param Model $model Model using this behavior
 * @param array $conditions SQL conditions
 * @return array Return list of data to process.
 */
	protected function _getListProcessingData(Model $model, $conditions = []) {
		$fields = [
			$model->alias . '.id',
			$model->alias . '.' . $model->displayField
		];
		$order = [$model->alias . '.' . $model->displayField => 'asc'];
		$recursive = -1;
		return $model->find('list', compact('conditions', 'fields', 'order', 'recursive'));
	}

/**
 * Processing group data
 *
 * @param Model $model Model using this behavior
 * @param array $conditions SQL conditions
 * @param array $action Action for processing in format:
 *  - `key`: action name. Can be one of `change-state` or `delete`
 *  - `value`: action value, e.g. state
 * @param int $idTask The ID of the QueuedTask
 * @throws InternalErrorException if $action is empty or not associated array
 * @return array|bool Return array list of processed data, or False on failure.
 */
	protected function _processingData(Model $model, $conditions = [], $action = [], $idTask = null) {
		if (empty($action) || !is_array($action) || !isAssoc($action)) {
			throw new InternalErrorException(__('Invalid action for processing'));
		}

		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		if (empty($conditions)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid condition for processing'));
			return false;
		}

		$data = $this->_getListProcessingData($model, $conditions);
		if (empty($data)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('The list of data for processing is empty'));
			return false;
		}

		$step = 0;
		$maxStep = 1;
		$result = [];
		$messages = [];
		$useCanDisable = false;
		if ($model->Behaviors->loaded('CanDisable')) {
			$useCanDisable = true;
		}
		$groupName = mb_strtolower($model->getGroupName());
		$targetName = mb_strtolower($model->getTargetName());
		$operationName = __('Processed %s', $groupName);
		$actionParam = reset($action);
		$actionName = key($action);
		$maxStep += count($data);
		foreach ($data as $id => $name) {
			$resultOperation = false;
			switch ($actionName) {
				case 'change-state':
					$dataToSave = [
						$model->alias => [
							'id' => $id,
							'enabled' => $actionParam,
						]
					];
					$model->clear();
					$resultOperation = $model->save($dataToSave, false);
					break;
				case 'delete':
					$resultOperation = $model->delete($id, true);
					break;
				default:
					continue 2;
			}
			if ($resultOperation) {
				$result[$operationName][] = $name;
			} else {
				$message = '';
				if ($useCanDisable) {
					$message = $model->getMessageCheckDisable();
				}
				if (empty($message)) {
					$message = __("Error on processing: %s '%s'", $targetName, $name);
				}
				$messages[__('Errors')][] = $message;
			}
			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
		}
		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		$messages += $result;
		if (!empty($idTask) && !empty($messages)) {
			$messagesText = RenderXmlData::renderErrorMessages($messages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $messagesText, true);
		}

		return $result;
	}

/**
 * Change field state for multiple records
 *
 * @param Model $model Model using this behavior
 * @param bool $state Value to change
 * @param array $conditions SQL conditions
 * @param int $idTask The ID of the QueuedTask
 * @throws InternalErrorException if field "enabled" is not found in model
 * @return array|bool Return array list of processed data, or False on failure.
 */
	public function changeStateGroupRecords(Model $model, $state = false, $conditions = [], $idTask = null) {
		if (!$model->schema('enabled')) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", 'enabled', $model->name));
		}

		$action = ['change-state' => $state];
		return $this->_processingData($model, $conditions, $action, $idTask);
	}

/**
 * Delete multiple records
 *
 * @param Model $model Model using this behavior
 * @param array $conditions SQL conditions
 * @param int $idTask The ID of the QueuedTask
 * @return array|bool Return array list of processed data, or False on failure.
 */
	public function deleteGroupRecords(Model $model, $conditions = [], $idTask = null) {
		$action = ['delete' => null];
		return $this->_processingData($model, $conditions, $action, $idTask);
	}

/**
 * Put group action processing in the task queue
 *
 * @param Model $model Model using this behavior
 * @param string $groupAction Name of group action for processing
 * @param array $conditions SQL conditions of group action for processing
 * @return null|bool Return Null, on failure. If success, return True,
 *  False otherwise.
 */
	public function putQueueProcessGroupAction(Model $model, $groupAction = null, $conditions = null) {
		if (($groupAction === false) || empty($conditions)) {
			return null;
		}

		$modelName = $model->name;
		$taskParam = compact('groupAction', 'conditions', 'modelName');
		return (bool)$this->_modelExtendQueuedTask->createJob('ProcessGroupAction', $taskParam, null, 'process');
	}

/**
 * Process group action
 *
 * @param Model $model Model using this behavior
 * @param string $groupAction Name of group action for processing
 * @param array $conditions SQL conditions of group action for processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success.
 */
	public function processGroupAction(Model $model, $groupAction = null, $conditions = null, $idTask = null) {
		if (($groupAction === false) || empty($conditions)) {
			return false;
		}

		set_time_limit(PROCESSING_GROUP_DATA_TIME_LIMIT);
		$result = false;
		$state = null;
		switch ($groupAction) {
			case 'group-data-dis':
				$state = false;
			case 'group-data-enb':
				if ($state === null) {
					$state = true;
				}
				$result = $this->changeStateGroupRecords($model, $state, $conditions, $idTask);
				break;
			case 'group-data-del':
				$result = $this->deleteGroupRecords($model, $conditions, $idTask);
				break;
		}
		if (is_array($result)) {
			$result = true;
		}

		return $result;
	}

}
