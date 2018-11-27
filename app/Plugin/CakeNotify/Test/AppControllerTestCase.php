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

/**
 * Bit mask for user roles
 */
if (!defined('LDAP_AUTH_TEST_USER_ROLE_USER')) {
	define('LDAP_AUTH_TEST_USER_ROLE_USER', 1);
}

if (!defined('LDAP_AUTH_TEST_USER_ROLE_ADMIN')) {
	define('LDAP_AUTH_TEST_USER_ROLE_ADMIN', 4);
}

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
		'RequestHandler',
	];
}

/**
 * Application level ControllerTestCase class
 *
 */
class AppControllerTestCase extends ExtendControllerTestCase {

/**
 * Information about the logged in user.
 *
 * @var array
 */
	protected $_userInfo = [
		'user' => 'Миронов Г.Н.',
		'role' => LDAP_AUTH_TEST_USER_ROLE_USER | LDAP_AUTH_TEST_USER_ROLE_ADMIN,
		'id' => 1
	];

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * @return void
 */
	public function setUp() {
		$path = __DIR__ . DS;
		$this->applyTestConfig($path);

		parent::setUp();
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
