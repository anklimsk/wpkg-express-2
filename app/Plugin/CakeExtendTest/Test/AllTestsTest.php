<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test.Case
 */

/**
 * A class to contain test cases and run them recursively with
 *  shared fixtures.
 *
 * @package app.Test.Case
 */
class AllTestsTest extends CakeTestSuite {

/**
 * Create test suite for running test recursively from all sub folders.
 *
 * @return object An object of `CakeTestSuite`.
 */
	public static function suite() {
		$suite = new CakeTestSuite(__d('cake_extend_test', 'All tests'));
		$path = __DIR__;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}
}
