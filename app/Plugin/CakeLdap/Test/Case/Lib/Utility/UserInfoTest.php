<?php
App::uses('CakeSession', 'Model/Datasource');
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('UserInfo', 'CakeLdap.Utility');

/**
 * UserInfoTest file
 *
 */
class UserInfoTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.employee_ldap_auth',
		'core.cake_session'
	];

	protected $_userInfoData = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_userInfoData = [
			'user' => 'Моисеева Л.Б.',
			'role' => CAKE_LDAP_TEST_USER_ROLE_USER | CAKE_LDAP_TEST_USER_ROLE_ADMIN,
			'includedFields' => [
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf'
			],
			'prefix' => 'admin',
			'id' => '1'
		];
		CakeSession::write('Auth.User', $this->_userInfoData);
		$this->_targetObject = new UserInfo();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		CakeSession::delete('Auth.User');
		unset($this->_session);
		unset($this->_userInfoData);

		parent::tearDown();
	}

/**
 * testGetUserField method
 *
 * @return void
 */
	public function testGetUserField() {
		$params = [
			[
				null, // $field
				null, // $userInfo
			],
			[
				'prefix', // $field
				null, // $userInfo
			],
			[
				'id', // $field
				null, // $userInfo
			],
			[
				'name', // $field
				null, // $userInfo
			],
			[
				'prefix', // $field
				array_merge($this->_userInfoData, ['prefix' => 'test']), // $userInfo
			],
		];
		$expected = [
			null,
			'admin',
			'1',
			null,
			'test',
		];

		$this->runClassMethodGroup('getUserField', $params, $expected);
	}

/**
 * testCheckUserRole method
 *
 * @return void
 */
	public function testCheckUserRole() {
		$params = [
			[
				null, // $roles
				true, // $logicalOr
				null, // $userInfo
			],
			[
				CAKE_LDAP_TEST_USER_ROLE_USER, // $roles
				true, // $logicalOr
				null, // $userInfo
			],
			[
				CAKE_LDAP_TEST_USER_ROLE_EXTENDED, // $roles
				true, // $logicalOr
				null, // $userInfo
			],
			[
				[CAKE_LDAP_TEST_USER_ROLE_EXTENDED, CAKE_LDAP_TEST_USER_ROLE_ADMIN], // $roles
				true, // $logicalOr
				null, // $userInfo
			],
			[
				[CAKE_LDAP_TEST_USER_ROLE_EXTENDED, CAKE_LDAP_TEST_USER_ROLE_ADMIN], // $roles
				false, // $logicalOr
				null, // $userInfo
			],
			[
				CAKE_LDAP_TEST_USER_ROLE_EXTENDED, // $roles
				false, // $logicalOr
				array_merge($this->_userInfoData, ['role' => CAKE_LDAP_TEST_USER_ROLE_EXTENDED]), // $userInfo
			],
		];
		$expected = [
			false,
			true,
			false,
			true,
			false,
			true,
		];

		$this->runClassMethodGroup('checkUserRole', $params, $expected);
	}
}
