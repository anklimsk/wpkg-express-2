<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('DynSchema', 'CakeLdap.Model');

/**
 * DynSchema Test Case
 */
class DynSchemaTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->_targetObject = ClassRegistry::init('CakeLdap.DynSchema');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testUpdateSchemaUnsuccess method
 *
 * @return void
 */
	public function testUpdateSchemaUnsuccess() {
		$params = [
			[
				null, // $event
				'test', // $connection
			],
			[
				['create' => 'bad_table'], // $event
				'test', // $connection
			],
			[
				['create' => 'departments', 'errors' => ['Some error']], // $event
				'test', // $connection
			],
		];
		$expected = [
			false,
			false,
			false,
		];
		$this->runClassMethodGroup('updateSchema', $params, $expected);
	}

/**
 * testUpdateSchemaDepartmentNotUseBlock method
 *
 * @return void
 */
	public function testUpdateSchemaDepartmentNotUseBlock() {
		$this->markTestIncomplete('testUpdateSchemaDepartmentNotUseBlock not implemented.'); // Comment or remove this string for implement test

		Configure::write('CakeLdap.LdapSync.Delete.Departments', true);
		$result = $this->_targetObject->updateSchema(['create' => 'departments'], 'test');
		$this->assertTrue($result);

		$ds = $this->_targetObject->getDataSource();
		$departmentsSchema = $ds->describe('departments');
		$expectedSchema = [];
		$this->assertData($expectedSchema, $departmentsSchema);
	}

/**
 * testUpdateSchemaDepartmentUseBlock method
 *
 * @return void
 */
	public function testUpdateSchemaDepartmentUseBlock() {
		$this->markTestIncomplete('testUpdateSchemaDepartmentUseBlock not implemented.'); // Comment or remove this string for implement test

		Configure::write('CakeLdap.LdapSync.Delete.Departments', false);
		$result = $this->_targetObject->updateSchema(['create' => 'departments'], 'test');
		$this->assertTrue($result);

		$ds = $this->_targetObject->getDataSource();
		$departmentsSchema = $ds->describe('departments');
		$expectedSchema = [];
		$this->assertData($expectedSchema, $departmentsSchema);
	}

/**
 * testUpdateSchemaEmployee method
 *
 * @return void
 */
	public function testUpdateSchemaEmployee() {
		$this->markTestIncomplete('testUpdateSchemaEmployee not implemented.'); // Comment or remove this string for implement test

		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL);
		$result = $this->_targetObject->updateSchema(['create' => 'employees'], 'test');
		$this->assertTrue($result);

		$ds = $this->_targetObject->getDataSource();
		$employeesSchema = $ds->describe('employees');
		$expectedSchema = [
			'id' => [
				'type' => 'integer',
				'null' => false,
				'default' => null,
				'length' => 10,
				'unsigned' => false,
				'key' => 'primary'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
				'type' => 'string',
				'null' => false,
				'default' => null,
				'length' => 36,
				'key' => 'unique',
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
				'type' => 'string',
				'null' => false,
				'default' => null,
				'length' => 256,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'type' => 'string',
				'null' => false,
				'default' => null,
				'length' => 256,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
				'type' => 'string',
				'null' => false,
				'default' => null,
				'length' => 256,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 6,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 128,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 256,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 128,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
				'type' => 'binary',
				'null' => true,
				'default' => null,
				'length' => null,
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 2048,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 16,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 64,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 256,
				'collate' => 'utf8_general_ci',
				'charset' => 'utf8'
			],
			'block' => [
				'type' => 'boolean',
				'null' => false,
				'default' => '0',
				'length' => 1,
			]
		];
		$this->assertData($expectedSchema, $employeesSchema);
	}
}
