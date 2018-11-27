<?php
/**
 * This file is the console shell file of the plugin.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command
 */

App::uses('AppShell', 'Console/Command');
App::uses('CakeText', 'Utility');

/**
 * This shell is used to execute tasks on a schedule.
 *
 * @package plugin.Console.Command
 */
class CronShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$tasks
 */
	public $tasks = ['CakeNotify.Clear'];

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

		$parser->addSubcommands([
			CAKE_NOTIFY_CRON_TASK_CLEAR_NOTIFICATIONS => [
					'help' => __d('cake_notify', 'Clearing expired notifications'),
					'parser' => $this->Clear->getOptionParser()
			]]);

		return $parser;
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function main() {
		$this->out(__d('cake_notify', 'Cron task Shell'));
		$this->hr();
		$this->out(__d('cake_notify', 'This shell is used to execute task scheduled.'));
		$this->out(__d('cake_notify', 'Available tasks: %s.', CakeText::toList(constsVals('CAKE_NOTIFY_CRON_TASK_'), __d('cake_notify', 'and'))));
	}
}
