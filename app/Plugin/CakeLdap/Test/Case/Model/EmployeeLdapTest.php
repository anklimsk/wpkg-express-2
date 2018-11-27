<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('EmployeeLdap', 'CakeLdap.Model');

/**
 * EmployeeLdap Test Case
 */
class EmployeeLdapTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.employee_ldap',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeLdap.EmployeeLdap');
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
 * testGetRequiredFields method
 *
 * @return void
 */
	public function testGetRequiredFields() {
		$result = $this->_targetObject->getRequiredFields();
		$expected = [
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetLdapQuery method
 *
 * @return void
 */
	public function testGetLdapQuery() {
		$params = [
			[
				null, // $conditions
				null, // $objectClass
			],
			[
				[
					$this->_targetObject->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'b3ec524a-69d0-4fce-b9c2-3b59956cfa25'
				], // $conditions
				null, // $objectClass
			],
			[
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'PC01'
				], // $conditions
				'computer', // $objectClass
			],
		];
		$expected = [
			'(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=user)(userAccountControl:1.2.840.113556.1.4.803:=512)',
			'(&(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=user)(userAccountControl:1.2.840.113556.1.4.803:=512)(objectguid=\4A\52\EC\B3\D0\69\CE\4F\B9\C2\3B\59\95\6C\FA\25))',
			'(&(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=computer)(name=PC01))',
		];
		$this->runClassMethodGroup('getLdapQuery', $params, $expected);
	}
}
