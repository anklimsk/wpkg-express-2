<?php
/**
 * This file is the application level ExtendCakeTestCase class.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

App::uses('ExtendCakeTestCase', 'CakeExtendTest.Test');

/**
 * Bit mask for user roles
 */
if (!defined('LDAP_AUTH_TEST_USER_ROLE_USER')) {
	define('LDAP_AUTH_TEST_USER_ROLE_USER', 1);
}

if (!defined('LDAP_AUTH_TEST_USER_ROLE_EXTENDED')) {
	define('LDAP_AUTH_TEST_USER_ROLE_EXTENDED', 2);
}

if (!defined('LDAP_AUTH_TEST_USER_ROLE_ADMIN')) {
	define('LDAP_AUTH_TEST_USER_ROLE_ADMIN', 4);
}

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
