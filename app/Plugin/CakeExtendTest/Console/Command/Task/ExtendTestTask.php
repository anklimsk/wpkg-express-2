<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('TestTask', 'Console/Command/Task');

/**
 * Task class for creating and updating test files.
 *
 * @package plugin.Console.Command.Task
 */
class ExtendTestTask extends TestTask {

/**
 * Gets the path for output. Checks the plugin property
 * and returns the correct path. Correction of path for plugin.
 *
 * @return string Path to output.
 */
	public function getPath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . 'Test' . DS;
		}

		return $path;
	}

/**
 * Generate the uses() calls for a type & class name
 *  Inlclude `AppControllerTestCase` and `AppCakeTestCase` class.
 *
 * @param string $type The Type of object you are generating tests for eg. controller
 * @param string $realType The package name for the class.
 * @param string $className The Classname of the class the test is being generated for.
 * @return array An array containing used classes
 */
	public function generateUses($type, $realType, $className) {
		list($pluginName, $realTypeName) = pluginSplit($realType, true, '');
		$uses = [];
		$type = strtolower($type);
		if ($type === 'controller') {
			$uses[] = ['AppControllerTestCase', $pluginName . 'Test'];
		} else {
			$uses[] = ['AppCakeTestCase', $pluginName . 'Test'];
		}
		if ($type === 'component') {
			$uses[] = ['ComponentCollection', 'Controller'];
			$uses[] = ['Component', 'Controller'];
		}
		if ($type === 'helper') {
			$uses[] = ['View', 'View'];
			$uses[] = ['Helper', 'View'];
		}
		$uses[] = [$className, $pluginName . $realTypeName];

		return $uses;
	}
}
