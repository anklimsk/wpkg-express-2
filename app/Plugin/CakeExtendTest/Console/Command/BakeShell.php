<?php
/**
 * This file is the console shell file of the plugin.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command
 */

App::uses('AppShell', 'Console/Command');
App::uses('Security', 'Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('String', 'Utility');
App::uses('CakeSchema', 'Model');

/**
 * This shell is used bake extended test case.
 *
 * @package plugin.Console.Command
 */
class BakeShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$tasks
 */
	public $tasks = [
		'CakeExtendTest.ExtendTest'
	];

/**
 * Gets the option parser instance and configures it.
 *
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		return $this->ExtendTest->getOptionParser();
	}

/**
 * Main method for this task (call default).
 *
 * @return void
 */
	public function main() {
		$this->out(__d('cake_extend_test', 'Extended bake task Shell'));
		$this->hr();
		$this->out(__d('cake_extend_test', 'This shell is used to bake extended test case.'));
		$this->ExtendTest->params['theme'] = 'cake_extend_test';
		$this->ExtendTest->execute();
	}
}
