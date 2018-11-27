<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used to synchronization employees with Active Directory.
 *
 * @package plugin.Console.Command.Task
 */
class SyncTask extends AppShell {

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
		$parser->description(__d('cake_ldap', 'This task is used to synchronization information with LDAP server scheduled.'));

		return $parser;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function execute() {
		$this->out(__d('cake_ldap', 'Synchronization with LDAP server in progress...'), 1, Shell::NORMAL);
		if ($this->QueuedTask->createJob('SyncEmployee', null, null, 'sync')) {
			$this->out(__d('cake_ldap', 'Synchronization with LDAP server set in queue successfully.'), 1, Shell::NORMAL);
		} else {
			$this->out('<error>' . __d('cake_ldap', 'Synchronization with LDAP server set in queue unsuccessfully.') . '</error>');
		}
	}
}
