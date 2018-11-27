<?php
/**
 * This file is the console shell task file of the application.
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
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used for parse client database files in the queue.
 *
 * @package app.Console.Command.Task
 */
class QueueParseDatabasesTask extends AppShell {

/**
 * Adding the QueueTask Model
 *
 * @var array
 */
	public $uses = [
		'Queue.QueuedTask',
		'CakeTheme.ExtendQueuedTask',
		'Report',
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
	public $timeout = CLIENT_DATABASE_PARSE_TIME_LIMIT;

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
 * Used for parsing client database files.
 *
 * @param array $data The array passed to QueuedTask->createJob()
 * @param int $id The id of the QueuedTask
 * @return bool Success
 */
	public function run($data, $id = null) {
		$this->hr();
		$this->out(__('CakePHP Queue task for parsing client database files.'));
		if (empty($data) || !is_array($data)) {
			$data = [];
		}
		$dataDefault = [
			'hostName' => null,
		];
		$data += $dataDefault;
		extract($data);
		$this->Report->createReports($hostName, $id);

		return true;
	}
}
