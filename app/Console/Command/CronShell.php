<?php
/**
 * This file is the console shell file of the application.
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
 * @package app.Console.Command
 */

App::uses('AppShell', 'Console/Command');
App::uses('CakeText', 'Utility');

/**
 * This shell is used to execute tasks on a schedule.
 *
 * @package app.Console.Command
 */
class CronShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$tasks
 */
	public $tasks = [
		'ParseLogs',
		'ParseDatabases',
	];

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->addSubcommands([
			SHELL_CRON_TASK_PARSE_LOGS => [
					'help' => __('Parsing log files'),
					'parser' => $this->ParseLogs->getOptionParser()
			],
			SHELL_CRON_TASK_PARSE_DATABASES => [
					'help' => __('Parsing client database files'),
					'parser' => $this->ParseDatabases->getOptionParser()
			],
		]);

		return $parser;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function main() {
		$this->out(__('Cron task of the shell'));
		$this->hr();
		$this->out(__('This shell is used to execute task scheduled.'));
		$this->out(__('Available tasks: %s.', CakeText::toList(constsVals('SHELL_CRON_TASK_'), __('and'))));
	}
}
