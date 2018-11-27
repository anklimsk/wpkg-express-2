<?php
/**
 * This file is the application level ExtendCakeTestCase class.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

App::uses('AppModel', 'Model');
App::uses('ExtendCakeTestCase', 'CakeExtendTest.Test');
require_once App::pluginPath('CakeSettingsApp') . 'Test' . DS . 'Config' . DS . 'const.php';
require_once App::pluginPath('CakeSettingsApp') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * Application level CakeTestCase class
 *
 */
class AppCakeTestCase extends ExtendCakeTestCase {

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * Actions:
 * - Write test configuration.
 *
 * @return void
 */
	public function setUp() {
		$pathPlugin = CakePlugin::path('CakeSettingsApp');
		$pathModel = $pathPlugin . 'Test' . DS . 'test_app' . DS . 'Model' . DS;
		$pathView = $pathPlugin . 'Test' . DS . 'test_app' . DS . 'View' . DS;
		App::build(
			[
				'Model' => $pathModel,
				'View' => $pathView,
			],
			App::RESET
		);
		parent::setUp();

		$path = __DIR__ . DS;
		$this->applyTestConfig($path);
	}

/**
 * teardown any static object changes and restore them.
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}
}
