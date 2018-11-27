<?php
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('LdapAuthenticate', 'CakeLdap.Controller/Component/Auth');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('AppCakeTestCase', 'CakeLdap.Test');

/**
 * Test case for LdapAuthenticate
 *
 */
class LdapAuthenticateTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.employee_ldap_auth',
		'plugin.cake_ldap.user_tests'
	];

/**
 * Collection object
 *
 * @var object
 */
	protected $_Collection = null;

/**
 * Response object
 *
 * @var object
 */
	protected $_response = null;

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_Collection);
		unset($this->_response);

		parent::tearDown();
	}

/**
 * test applying settings in the constructor
 *
 * @return void
 */
	public function testConstructor() {
		$this->_createAuthComponet(false);
		$authGroups = [
			CAKE_LDAP_TEST_USER_ROLE_USER => 'default',
			CAKE_LDAP_TEST_USER_ROLE_EXTENDED => 'CN=Web.PbExtend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_LDAP_TEST_USER_ROLE_ADMIN => 'CN=Web.PbAdmin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com'
		];
		$authPrefixes = [
			CAKE_LDAP_TEST_USER_ROLE_EXTENDED => 'extend',
			CAKE_LDAP_TEST_USER_ROLE_ADMIN => 'admin'
		];
		$bindFields = [CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'UserTest.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID];
		$object = new LdapAuthenticate($this->_Collection, [
			'userModel' => 'CakeLdap.User',
			'externalAuth' => false,
			'groups' => $authGroups,
			'prefixes' => $authPrefixes,
			'bindFields' => $bindFields,
			'includeFields' => CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME
		]);
		$this->assertEquals('CakeLdap.User', $object->settings['userModel']);
		$this->assertEquals(false, $object->settings['externalAuth']);
		$this->assertEquals($authGroups, $object->settings['groups']);
		$this->assertEquals($authPrefixes, $object->settings['prefixes']);
		$this->assertEquals($bindFields, $object->settings['bindFields']);
		$this->assertEquals(CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME, $object->settings['includeFields']);
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoUsername() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => ['password' => 'foobar']];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateExternalNoUsername() {
		$this->_createAuthComponet(true);
		if (isset($_SERVER['REMOTE_USER'])) {
			unset($_SERVER['REMOTE_USER']);
		}
		if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
			unset($_SERVER['REDIRECT_REMOTE_USER']);
		}
		$request = new CakeRequest('posts/index', false);
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoPassword() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => 'lmoiseeva@fabrikam.com',
				'password' => null
			]];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test authenticate password is false method
 *
 * @return void
 */
	public function testAuthenticatePasswordIsFalse() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => 'lmoiseeva@fabrikam.com',
				'password' => null
			]];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test authenticate field is not string
 *
 * @return void
 */
	public function testAuthenticateFieldsAreNotString() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = [
			'User' => [
				'user' => ['lmoiseeva@fabrikam.com', 'test'],
				'password' => 'my password'
			]];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));

		$request->data = [
			'User' => [
				'user' => [],
				'password' => 'my password'
			]];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));

		$request->data = [
			'User' => [
				'user' => 'lmoiseeva@fabrikam.com',
				'password' => ['password1', 'password2']
			]];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test authenticate field is not string
 *
 * @return void
 */
	public function testAuthenticateExternalFieldsAreNotString() {
		$this->_createAuthComponet(true);
		$request = new CakeRequest('posts/index', false);
		$_SERVER['REMOTE_USER'] = ['lmoiseeva@fabrikam.com', 'test'];
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test authenticate failure
 *
 * @return void
 */
	public function testAuthenticateFail() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'akirillov@fabrikam.com',
			'password' => 'password'
		]];

		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateExternalFail() {
		$this->_createAuthComponet(true);
		if (isset($_SERVER['REMOTE_USER'])) {
			unset($_SERVER['REMOTE_USER']);
		}
		$_SERVER['REDIRECT_REMOTE_USER'] = 'mvoropaev@fabrikam.com';
		$request = new CakeRequest('posts/index', false);
		$this->assertFalse($this->_targetObject->authenticate($request, $this->_response));
	}

/**
 * test authenticate success
 *
 * @return void
 */
	public function testAuthenticateSuccess() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'lmoiseeva@fabrikam.com',
			'password' => 'password'
		]];
		$this->_prepareLdapAuth();
		$result = $this->_targetObject->authenticate($request, $this->_response);
		$expected = [
			'user' => 'Моисеева Л.Б.',
			'role' => CAKE_LDAP_TEST_USER_ROLE_USER | CAKE_LDAP_TEST_USER_ROLE_ADMIN,
			'includedFields' => [
				CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'lmoiseeva@fabrikam.com'
			],
			'prefix' => 'admin',
			'id' => '1'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test authenticate success
 *
 * @return void
 */
	public function testAuthenticateExternalSuccess() {
		$_SERVER['REDIRECT_REMOTE_USER'] = 'akirillov@fabrikam.com';
		$this->_createAuthComponet(true);
		$request = new CakeRequest('posts/index', false);
		$this->_prepareLdapAuth();
		$result = $this->_targetObject->authenticate($request, $this->_response);
		$expected = [
			'user' => 'Кириллов А.М.',
			'role' => CAKE_LDAP_TEST_USER_ROLE_USER,
			'includedFields' => [
				CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'akirillov@fabrikam.com'
			],
			'prefix' => null,
			'id' => '3'
		];

		$this->assertEquals($expected, $result);
	}

/**
 * test authenticate success
 *
 * @return void
 */
	public function testAuthenticateSuccessEmptyGroupMember() {
		$this->_createAuthComponet(false);
		$request = new CakeRequest('posts/index', false);
		$request->data = ['User' => [
			'user' => 'akirillov@fabrikam.com',
			'password' => 'password'
		]];
		$this->_prepareLdapAuth();
		$result = $this->_targetObject->authenticate($request, $this->_response);
		$expected = [
			'user' => 'Кириллов А.М.',
			'role' => CAKE_LDAP_TEST_USER_ROLE_USER,
			'includedFields' => [
				CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => 'akirillov@fabrikam.com'
			],
			'prefix' => null,
			'id' => '3'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Override internal function:
 *  - `ldap_bind()`
 *
 * @return void
 */
	protected function _prepareLdapAuth() {
		$message = '';
		if (!extension_loaded('runkit')) {
			$message = __d('app_test', 'Extension "Runkit" is not loaded');
		} elseif (!ini_get('runkit.internal_override')) {
			$message = __d('app_test', 'Option "runkit.internal_override" is False');
		}

		$this->skipIf(!empty($message), $message);
		$result = runkit_function_redefine('ldap_bind', '', 'return true;');
		$this->assertTrue($result);
	}

/**
 * Create LdapAuthenticate
 *
 * @param bool $externalAuth Flag of use external authentication
 * @return void
 */
	protected function _createAuthComponet($externalAuth = false) {
		$this->_Collection = $this->getMock('ComponentCollection');
		$authGroups = [
			CAKE_LDAP_TEST_USER_ROLE_USER => 'default',
			CAKE_LDAP_TEST_USER_ROLE_EXTENDED => 'CN=Web.PbExtend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
			CAKE_LDAP_TEST_USER_ROLE_ADMIN => 'CN=Web.PbAdmin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com'
		];
		$authPrefixes = [
			CAKE_LDAP_TEST_USER_ROLE_EXTENDED => 'extend',
			CAKE_LDAP_TEST_USER_ROLE_ADMIN => 'admin'
		];
		$this->_targetObject = new LdapAuthenticate($this->_Collection, [
				'fields' => [
					'username' => 'user',
					'password' => 'password'
				],
				'userModel' => 'CakeLdap.User',
				'externalAuth' => (bool)$externalAuth,
				'groups' => $authGroups,
				'prefixes' => $authPrefixes,
				'bindFields' => [CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'UserTest.guid'],
				'includeFields' => CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME
		]);

		$this->_response = $this->getMock('CakeResponse');
	}
}
