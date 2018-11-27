<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used for synchronization information of employees
 *  with Active Directory in the queue.
 *
 * @package plugin.Console.Command.Task
 */
class QueueSyncEmployeeTask extends AppShell {

/**
 * Adding the QueueTask Model
 *
 * @var array
 */
	public $uses = [
		'Queue.QueuedTask',
		'CakeTheme.ExtendQueuedTask',
		'CakeLdap.Sync'
	];

/**
 * ZendStudio Codecomplete Hint
 *
 * @var QueuedTask
 */
	public $QueuedTask;

/**
 * Timeout for run, after which the Task is reassigned to a new worker.
 *
 * @var int
 */
	public $timeout = SYNC_EMPLOYEE_TIME_LIMIT;

/**
 * Number of times a failed instance of this task should be restarted before giving up.
 *
 * @var int
 */
	public $retries = 1;

/**
 * Stores any failure messages triggered during run()
 *
 * @var string
 */
	public $failureMessage = '';

/**
 * Flag auto unserialize data. If true, unserialize data before run task.
 *
 * @var bool
 */
	public $autoUnserialize = true;

/**
 * Main function.
 *  Used for synchronization information of employees with Active Directory.
 *
 * @param array $data The array passed to QueuedTask->createJob()
 * @param int $id The id of the QueuedTask
 * @return bool Success
 * @throws RuntimeException when seconds are 0;
 */
	public function run($data, $id = null) {
		$this->hr();
		$this->out(__d('cake_ldap', 'CakePHP Queue Sync task.'));
		if (empty($data) || !is_array($data)) {
			$data = [];
		}
		$dataDefault = [
			'guid' => null,
		];
		$data += $dataDefault;
		extract($data);

		if (empty($guid)) {
			$queueLength = $this->ExtendQueuedTask->getLengthQueue('SyncEmployee');
			if ($queueLength > 0) {
				$this->out(__d('cake_ldap', 'Found sync task in queue: %d. Skipped.', $queueLength));

				return true;
			}
		}

		$this->Sync->syncInformation($guid, $id);

		return true;
	}
}
