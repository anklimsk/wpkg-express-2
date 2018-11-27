<?php
/**
 * This file is the application level ExtendControllerTestCase class.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

App::uses('Controller', 'Controller');
App::uses('AppModel', 'Model');
App::uses('ExtendControllerTestCase', 'CakeExtendTest.Test');
require_once App::pluginPath('CakeSettingsApp') . 'Test' . DS . 'Config' . DS . 'const.php';
require_once App::pluginPath('CakeSettingsApp') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * Application Controller
 *
 * @package     app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

/**
 * helpers property
 *
 * @var array
 */
	public $helpers = ['Html'];

/**
 * uses property
 *
 * @var array
 */
	public $uses = [];

/**
 * components property
 *
 * @var array
 */
	public $components = [
		'Auth',
		'Session',
		'Security',
		'Flash',
		'RequestHandler',
	];

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure components;
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Auth->allow();

		parent::beforeFilter();
	}

}

/**
 * Application level ControllerTestCase class
 *
 */
class AppControllerTestCase extends ExtendControllerTestCase {

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
