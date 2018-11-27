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
App::uses('ExtendControllerTestCase', 'CakeExtendTest.Test');
require_once App::pluginPath('CakeLdap') . 'Test' . DS . 'Config' . DS . 'const.php';
require_once App::pluginPath('CakeLdap') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

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
 * @return void
 */
	public function setUp() {
		$userInfo = [
			'user' => 'Моисеева Л.Б.',
			'role' => CAKE_LDAP_TEST_USER_ROLE_USER | CAKE_LDAP_TEST_USER_ROLE_ADMIN,
			'includedFields' => [
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf'
			],
			'prefix' => 'admin',
			'id' => '1'
		];
		$this->setDefaultUserInfo($userInfo);
		$pathPlugin = CakePlugin::path('CakeLdap');
		$pathModel = $pathPlugin . 'Test' . DS . 'test_app' . DS . 'Model' . DS;
		App::build(
			[
				'Model' => $pathModel,
			],
			App::RESET
		);
		parent::setUp();

		$path = __DIR__ . DS;
		$this->applyTestConfig($path);
		Configure::write('Routing.prefixes', ['manager', 'admin']);
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
