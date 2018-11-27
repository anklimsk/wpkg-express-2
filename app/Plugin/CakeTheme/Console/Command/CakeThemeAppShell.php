<?php
/**
 * This file is the console shell file of the plugin.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command
 */

App::uses('Shell', 'Console');
App::uses('CakeThemeConsoleOutput', 'CakeTheme.Console');

/**
 * This shell is used as application Shell
 *
 * This Shell contain methods for convert encoding of
 * console input and output.
 * @package plugin.Console.Command
 */
class CakeThemeAppShell extends Shell {

/**
 *  Constructs this Shell instance.
 *
 * Actions:
 * - Set wrapper for outputting information from a shell application
 *  with conversion to another encoding system depending on OS.
 *
 * @param ConsoleOutput $stdout A ConsoleOutput object for stdout.
 * @param ConsoleOutput $stderr A ConsoleOutput object for stderr.
 * @param ConsoleInput $stdin A ConsoleInput object for stdin.
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);

		$this->stdout = $stdout ? $stdout : new CakeThemeConsoleOutput('php://stdout');
		$this->stderr = $stderr ? $stderr : new CakeThemeConsoleOutput('php://stderr');
	}
}
