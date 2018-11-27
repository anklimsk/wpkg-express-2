<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('AppShell', 'Console/Command');

/**
 * This task is used to clearing expired notifications.
 *
 * @package plugin.Console.Command.Task
 */
class ClearTask extends AppShell {

/**
 * Contains models to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$uses
 */
	public $uses = ['CakeNotify.Notification'];

/**
 * Gets the option parser instance and configures it.
 *
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__d('cake_notify', 'This task is used to clearing expired notifications.'));

		return $parser;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function execute() {
		$this->out(__d('cake_notify', 'Clear notifications in progress...'), 1, Shell::NORMAL);
		if ($this->Notification->clearNotifications()) {
			$this->out('<success>' . __d('cake_notify', 'Notifications clear successfully.') . '</success>', 1, Shell::NORMAL);
		} else {
			$this->out('<error>' . __d('cake_notify', 'Notifications clear unsuccessfully.') . '</error>');
		}
	}
}
