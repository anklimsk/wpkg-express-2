<?php
App::uses('AppCakeTestCase', 'CakeSettingsApp.Test');
App::uses('Ldap', 'CakeSettingsApp.Model');

/**
 * Ldap Test Case
 *
 */
class LdapTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_settings_app.ldap'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->_targetObject = ClassRegistry::init('CakeSettingsApp.Ldap');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * testGroupExists method
 *
 * @return void
 */
	public function testGroupExists() {
		$params = [
			[
				null, // $group
			],
			[
				'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com', // $group
			],
			[
				'CN=Web.Test,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com', // $group
			],
		];
		$expected = [
			false,
			true,
			false,
		];

		$this->runClassMethodGroup('groupExists', $params, $expected);
	}

/**
 * testGetListGroupEmailEmpty method
 *
 * @return void
 */
	public function testGetListGroupEmailEmpty() {
		$result = $this->_targetObject->getListGroupEmail();
		$expected = [];
		$this->assertEquals($expected, $result);
	}

/**
 * testGetListGroupEmailSuccess method
 *
 * @return void
 */
	public function testGetListGroupEmailSuccess() {
		$result = $this->_targetObject->getListGroupEmail('Web.Admin');
		$expected = [
			'j.doe@mail.com' => 'John Doe',
			'j.smith@mail.org' => 'John Smith'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testGetTopLevelContainerList method
 *
 * @return void
 */
	public function testGetTopLevelContainerList() {
		$this->markTestIncomplete('testGetTopLevelContainerList not implemented.'); // Comment or remove this string for implement test

		$result = $this->_targetObject->getTopLevelContainerList();
		$expected = [];
		$this->assertEquals($expected, $result);
	}

/**
 * testGetGroupList method
 *
 * @return void
 */
	public function testGetGroupList() {
		$result = $this->_targetObject->getGroupList();
		$expected = [
			'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Admin',
			'CN=Web.Extend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Extend',
			'CN=Web.Manager,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Manager',
		];
		$this->assertEquals($expected, $result);
	}
}
