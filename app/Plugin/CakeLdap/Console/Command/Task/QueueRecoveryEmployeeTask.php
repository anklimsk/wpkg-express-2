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
 * This task is used for recovery tree of employee in the queue.
 *
 * @package plugin.Console.Command.Task
 */
class QueueRecoveryEmployeeTask extends AppShell {

/**
 * Adding the QueueTask Model
 *
 * @var array
 */
	public $uses = [
		'Queue.QueuedTask',
		'CakeTheme.ExtendQueuedTask',
		'CakeLdap.SubordinateDb'
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
	public $timeout = RECOVER_TREE_EMPLOYEE_TIME_LIMIT;

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
 *  Used for recovery tree of employee.
 *
 * @param array $data The array passed to QueuedTask->createJob()
 * @param int $id The id of the QueuedTask
 * @triggers Model.afterUpdateTree $this->SubordinateDb
 * @return bool Success
 * @throws RuntimeException when seconds are 0;
 */
	public function run($data, $id = null) {
		$result = true;
		$this->hr();
		$this->out(__d('cake_ldap', 'CakePHP Queue Recovery task.'));
		$queueLength = $this->ExtendQueuedTask->getLengthQueue('RecoveryEmployee');
		if ($queueLength > 0) {
			$this->out(__d('cake_ldap', 'Found recovery task in queue: %d. Skipped.', $queueLength));

			return true;
		}

		set_time_limit(RECOVER_TREE_EMPLOYEE_TIME_LIMIT);
		$this->QueuedTask->updateProgress($id, 0);
		if ($this->SubordinateDb->verify() === true) {
			$this->QueuedTask->markJobFailed($id, __d('cake_ldap', 'The recovery tree of employees is not required'));

			return true;
		}

		$this->QueuedTask->updateProgress($id, 0.33);
		if (!$this->SubordinateDb->recoverEmployeeTree(false)) {
			$result = false;
			$this->QueuedTask->markJobFailed($id, __d('cake_ldap', 'Error on recovery tree of employee.'));
		}

		$this->QueuedTask->updateProgress($id, 0.66);
		if ($result && !$this->SubordinateDb->syncInformation(null, $id)) {
			$this->QueuedTask->markJobFailed($id, __d('cake_ldap', 'Error on synchronizing the employee tree with LDAP server.'));
		}

		$this->QueuedTask->updateProgress($id, 1);

		return true;
	}
}
