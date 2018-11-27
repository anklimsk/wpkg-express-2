<?php
App::uses('UsersController', 'CakeLdap.Controller');
App::uses('AppControllerTestCase', 'CakeLdap.Test');

/**
 * UsersController Test Case
 */
class UsersControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeLdap.Users';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.employee_ldap_auth',
		'core.cake_session'
	];

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginGetExtarnalAuthInvalidUser() {
		$this->_genarateController(false, true);
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$result = $this->testAction('/users/login', $opt);
		$expected = '/<form action=\"\/users\/login\"/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginGetExtarnalAuthValidUser() {
		$this->_genarateController(true, true);
		$opt = [
			'method' => 'GET',
		];
		$this->testAction('/users/login', $opt);
		$this->checkRedirect('/home');
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginGetInternalAuthInvalidUser() {
		$this->_genarateController(false, false);
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$result = $this->testAction('/users/login', $opt);
		$expected = '/<form action=\"\/users\/login\"/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginGetInternalAuthValidUser() {
		$this->_genarateController(true, false);
		$opt = [
			'method' => 'GET',
			'return' => 'view'
		];
		$result = $this->testAction('/users/login', $opt);
		$expected = '/<form action=\"\/users\/login\"/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostExtarnalAuthInvalidUser() {
		$this->_genarateController(false, true);
		$opt = [
			'data' => [
				'username' => 'testuser',
				'password' => 'testuser',
			],
			'method' => 'POST',
			'return' => 'view'
		];
		$result = $this->testAction('/users/login', $opt);
		$expected = '/<form action=\"\/users\/login\"/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostExtarnalAuthValidUser() {
		$this->_genarateController(true, true);
		$opt = [
			'data' => [
				'username' => 'testuser',
				'password' => 'testuser',
			],
			'method' => 'POST',
		];
		$this->testAction('/users/login', $opt);
		$this->checkRedirect('/home');
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostInternalAuthEmptyUser() {
		$this->_genarateController(false, false);
		$opt = [
			'data' => [
				'User' => [
					'username' => '',
					'password' => 'test',
				]
			],
			'method' => 'POST',
		];
		$this->testAction('/users/login', $opt);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid username or password, try again'));
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostInternalAuthEmptyPass() {
		$this->_genarateController(false, false);
		$opt = [
			'data' => [
				'User' => [
					'username' => 'test',
					'password' => '',
				]
			],
			'method' => 'POST',
		];
		$this->testAction('/users/login', $opt);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid username or password, try again'));
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostInternalAuthInvalidUser() {
		$this->_genarateController(false, false);
		$opt = [
			'data' => [
				'User' => [
					'username' => 'testuser',
					'password' => 'testuser',
				]
			],
			'method' => 'POST'
		];
		$this->testAction('/users/login', $opt);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid username or password, try again'));
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testLoginPostInternalAuthValidUser() {
		$this->_genarateController(true, false);
		$opt = [
			'data' => [
				'User' => [
					'username' => 'testuser',
					'password' => 'testuser',
				]
			],
			'method' => 'POST'
		];
		$this->testAction('/users/login', $opt);
		$this->checkRedirect('/home');
	}

/**
 * testLogout method
 *
 * @return void
 */
	public function testLogout() {
		$this->_genarateController(true, true);
		$opt = [
			'method' => 'GET',
		];
		$this->testAction('/users/logout', $opt);
		$this->checkRedirect('/test/login');
	}

/**
 * Create UsersController
 *
 * @param bool $login Result of method AuthComponent::login()
 * @param bool $externalAuth Flag of use external authentication
 * @return void
 */
	protected function _genarateController($login = true, $externalAuth = false) {
		$mocks = [
			'components' => [
				'Security',
				'Auth' => ['login', 'logout', 'redirectUrl']
			]
		];
		$this->generateMockedController($mocks);
		$this->Controller->Auth->expects($this->any())
			->method('login')
			->will($this->returnValue($login));
		$this->Controller->Auth->expects($this->any())
			->method('logout')
			->will($this->returnValue('/test/login'));
		$this->Controller->Auth->expects($this->any())
			->method('redirectUrl')
			->will($this->returnValue('/home'));
		$this->Controller->Auth->authenticate = ['CakeLdap.Ldap' => ['externalAuth' => $externalAuth]];
		$this->Controller->layout = 'default';
	}
}
