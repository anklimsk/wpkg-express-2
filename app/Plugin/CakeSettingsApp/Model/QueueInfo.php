<?php
/**
 * This file is the model file of the plugin.
 * Methods for management queue of tasks.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('ExtendQueuedTask', 'CakeTheme.Model');

/**
 * QueueInfo for CakeSettingsApp.
 *
 * @package plugin.Model
 */
class QueueInfo extends ExtendQueuedTask {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'QueueInfo';

/**
 * Process group action
 *
 * @param string $groupAction Name of group action for processing
 * @param array $conditions Conditions of group action for processing
 * @return null|bool Return Null, on failure. If success, return True,
 *  False otherwise.
 */
	public function processGroupAction($groupAction = null, $conditions = null) {
		if (($groupAction === false) || empty($conditions)) {
			return null;
		}

		$result = false;
		switch ($groupAction) {
			case 'group-data-del':
				$result = $this->deleteAll($conditions, false);
				break;
		}

		return $result;
	}

/**
 * Return list of tasks state
 *
 * @return array Return array list of tasks state.
 */
	public function getListTaskState() {
		$taskStateList = [
			'NOT_READY' => __d('cake_settings_app', 'Not ready'),
			'NOT_STARTED' => __d('cake_settings_app', 'Not started'),
			'IN_PROGRESS' => __d('cake_settings_app', 'In progress'),
			'COMPLETED' => __d('cake_settings_app', 'Completed'),
			'FAILED' => __d('cake_settings_app', 'Failed'),
			'UNKNOWN' => __d('cake_settings_app', 'Unknown'),
		];

		return $taskStateList;
	}

/**
 * Return list of class for queue of tasks state
 *
 * @return array Return array list of class for queue of tasks state.
 */
	public function getListBarStateClass() {
		$result = [
			'NOT_READY' => 'progress-bar-warning',
			'NOT_STARTED' => 'progress-bar-success progress-bar-striped',
			'IN_PROGRESS' => 'progress-bar-info',
			'COMPLETED' => 'progress-bar-success',
			'FAILED' => 'progress-bar-danger',
			'UNKNOWN' => 'progress-bar-danger progress-bar-striped',
		];

		return $result;
	}

/**
 * Return information about queue of tasks for bar state
 *
 * @return array Return array information about queue tasks.
 */
	public function getBarStateInfo() {
		$result = [];
		$fields = [
			'count(*) AS amount',
			$this->alias . '.status'
		];
		$group = $this->alias . '.status';
		$order = [$this->alias . '.status' => 'asc'];
		$this->recursive = -1;
		$data = $this->find('all', compact('fields', 'group', 'order'));
		if (empty($data)) {
			return $result;
		}

		$taskStateList = $this->getListTaskState();
		$taskClassList = $this->getListBarStateClass();
		foreach ($data as $dataItem) {
			$stateId = $dataItem[$this->alias]['status'];
			$stateName = Hash::get($taskStateList, $stateId);
			$class = Hash::get($taskClassList, $stateId);
			$amount = (int)Hash::get($dataItem, '0.amount');
			$stateUrl = ['controller' => 'queues', 'action' => 'index', 'plugin' => 'cake_settings_app',
				'?' => ['data[FilterData][0][' . $this->alias . '][status]' => $stateId]];
			$result[] = compact('stateName', 'stateId', 'amount', 'stateUrl', 'class');
		}

		return $result;
	}
}
