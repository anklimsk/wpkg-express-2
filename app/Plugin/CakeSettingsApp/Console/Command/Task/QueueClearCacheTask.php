<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used for clear cache in the queue.
 *
 * @package plugin.Console.Command.Task
 */
class QueueClearCacheTask extends AppShell {

/**
 * Adding the QueueTask Model
 *
 * @var array
 */
	public $uses = [
		'Queue.QueuedTask',
		'CakeSettingsApp.QueueInfo',
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
	public $timeout = 30;

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
 *  Used for clear cache.
 *
 * @param array $data The array passed to QueuedTask->createJob()
 * @param int $id The id of the QueuedTask
 * @triggers Model.afterUpdateTree $this->SubordinateDb
 * @return bool Success
 * @throws RuntimeException when seconds are 0;
 */
	public function run($data, $id = null) {
		$this->hr();
		$this->out(__d('cake_settings_app', 'CakePHP Queue clearing cache task.'));
		$queueLength = $this->QueueInfo->getLengthQueue('ClearCache');
		if ($queueLength > 0) {
			$this->out(__d('cake_settings_app', 'Found clearing cache task in queue: %d. Skipped.', $queueLength));

			return true;
		}

		$cacheConfigs = Cache::configured();
		foreach ($cacheConfigs as $cacheConfigName) {
			Cache::clear(false, $cacheConfigName);
		}
		$this->QueuedTask->updateProgress($id, 1);

		return true;
	}
}
