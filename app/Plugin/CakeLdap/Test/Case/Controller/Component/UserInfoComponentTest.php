<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSession', 'Model/Datasource');
App::uses('UserInfoComponent', 'CakeLdap.Controller/Component');
App::uses('AppCakeTestCase', 'CakeLdap.Test');

/**
 * UserInfoComponent Test Case
 */
class UserInfoComponentTest extends AppCakeTestCase {

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
		$Collection = new ComponentCollection();
		$settings = ['prefixes' => [
			CAKE_LDAP_TEST_USER_ROLE_EXTENDED => 'extend',
			CAKE_LDAP_TEST_USER_ROLE_ADMIN => 'admin'
		]];
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
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Controller = new Controller($request, $response);
		$this->Controller->constructClasses();
		$this->_targetObject = new UserInfoComponent($Collection, $settings);
		$this->_targetObject->initialize($this->Controller);
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
				null // $field
			],
			[
				'prefix' // $field
			],
			[
				'id' // $field
			],
			[
				'name' // $field
			],
		];
		$expected = [
			null,
			'admin',
			'1',
			null
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

/**
 * testIsAuthorized method
 *
 * @return void
 */
	public function testIsAuthorized() {
		$this->Controller->request->params['action'] = 'admin_index';
		$result = $this->_targetObject->isAuthorized(null);
		$expected = true;
		$this->assertData($expected, $result);

		$this->Controller->request->params['action'] = 'padmin_index';
		$result = $this->_targetObject->isAuthorized(null);
		$expected = false;
		$this->assertData($expected, $result);

		$this->Controller->request->params['action'] = 'index';
		$result = $this->_targetObject->isAuthorized(null);
		$expected = false;
		$this->assertData($expected, $result);

		$this->Controller->request->params['action'] = 'extend_index';
		$result = $this->_targetObject->isAuthorized(array_merge($this->_userInfoData, ['role' => CAKE_LDAP_TEST_USER_ROLE_EXTENDED, 'prefix' => 'extend']));
		$expected = true;
		$this->assertData($expected, $result);

		$this->Controller->request->params['action'] = 'index';
		$result = $this->_targetObject->isAuthorized(array_merge($this->_userInfoData, ['role' => CAKE_LDAP_TEST_USER_ROLE_EXTENDED, 'prefix' => 'extend']));
		$expected = false;
		$this->assertData($expected, $result);

		$this->Controller->request->params['action'] = 'test_index';
		$result = $this->_targetObject->isAuthorized(array_merge($this->_userInfoData, ['role' => CAKE_LDAP_TEST_USER_ROLE_EXTENDED, 'prefix' => 'test']));
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testIsExternalAuth method
 *
 * @return void
 */
	public function testIsExternalAuth() {
		if (isset($_SERVER['REMOTE_USER'])) {
			unset($_SERVER['REMOTE_USER']);
		}
		if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
			unset($_SERVER['REDIRECT_REMOTE_USER']);
		}
		$result = $this->_targetObject->isExternalAuth();
		$expected = false;
		$this->assertData($expected, $result);

		$_SERVER['REMOTE_USER'] = 'some_user';
		$result = $this->_targetObject->isExternalAuth();
		$expected = true;
		$this->assertData($expected, $result);

		unset($_SERVER['REMOTE_USER']);
		$_SERVER['REDIRECT_REMOTE_USER'] = 'some_user';
		$result = $this->_targetObject->isExternalAuth();
		$expected = true;
		$this->assertData($expected, $result);
	}
}
