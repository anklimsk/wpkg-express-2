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
 * @package app.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used to put parsing client database files in task queue.
 *
 * @package app.Console.Command.Task
 */
class ParseDatabasesTask extends AppShell {

/**
 * Contains models to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$uses
 */
	public $uses = ['Queue.QueuedTask'];

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__('This task is used to parse client database files scheduled.'));

		return $parser;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function execute() {
		$this->out(__('Parsing client database files in progress...'), 1, Shell::NORMAL);
		if ($this->QueuedTask->createJob('ParseDatabases', null, null, 'parse')) {
			$this->out(__('Parsing client database files put in queue successfully.'), 1, Shell::NORMAL);
		} else {
			$this->out('<error>' . __('Parsing client database files put in queue unsuccessfully.') . '</error>');
		}
	}
}
