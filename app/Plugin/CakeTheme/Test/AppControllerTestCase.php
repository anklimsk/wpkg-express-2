<?php
/**
 * This file is the application level ExtendControllerTestCase class.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

App::uses('Model', 'Model');
App::uses('Controller', 'Controller');
App::uses('ExtendControllerTestCase', 'CakeExtendTest.Test');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Config' . DS . 'const.php';

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
		parent::setUp();

		$path = __DIR__ . DS;
		$this->applyTestConfig($path);
		Configure::write('Config.language', 'eng');
		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();
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
