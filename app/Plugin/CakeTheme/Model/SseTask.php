<?php
/**
 * This file is the model file of the plugin.
 * Get information of queued tasks for SSE.
 * Methods for retrieve information of queued tasks.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeThemeAppModel', 'CakeTheme.Model');
App::uses('CakeSession', 'Model/Datasource');

/**
 * SseTask for CakeTheme.
 *
 * @package plugin.Model
 */
class SseTask extends CakeThemeAppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = false;

/**
 * Session key for store SSE task information
 *
 * @var array
 */
	protected $_sessionKey = 'SSE.progress';

/**
 * Get list of queued tasks.
 *
 * @return array List of queued tasks.
 */
	public function getListQueuedTask() {
		$result = [];
		if (!CakeSession::check($this->_sessionKey)) {
			return $result;
		}

		$tasks = CakeSession::read($this->_sessionKey);
		if (!is_array($tasks)) {
			return $result;
		}

		return $tasks;
	}

/**
 * Delete queued tasks.
 *
 * @param array $tasks List of tasks to delete
 * @return bool Success.
 */
	public function deleteQueuedTask($tasks = []) {
		if (empty($tasks) || !is_array($tasks)) {
			return false;
		}

		if (!CakeSession::check($this->_sessionKey)) {
			return false;
		}

		$existsTasks = CakeSession::read($this->_sessionKey);
		if (empty($existsTasks)) {
			return false;
		}
		$resultTasks = array_values(array_diff((array)$existsTasks, $tasks));

		return CakeSession::write($this->_sessionKey, $resultTasks);
	}
}
