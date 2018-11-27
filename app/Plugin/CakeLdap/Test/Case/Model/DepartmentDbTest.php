<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('DepartmentDb', 'CakeLdap.Model');

/**
 * DepartmentDb Test Case
 */
class DepartmentDbTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee_ldap'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeLdap.DepartmentDb');
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
 * testGet method
 *
 * @return void
 */
	public function testGet() {
		$params = [
			[
				null, // $id
			],
			[
				1000, // $id
			],
			[
				2, // $id
			],
		];
		$expected = [
			false,
			[],
			[
				'DepartmentDb' => [
					'id' => '2',
					'value' => 'ОС',
					'block' => false,
				]
			],
		];
		$this->runClassMethodGroup('get', $params, $expected);
	}

/**
 * testGetListDepartments method
 *
 * @return void
 */
	public function testGetListDepartments() {
		$params = [
			[
				false, // $includeBlock
			],
			[
				true, // $includeBlock
			],
		];
		$expected = [
			[
				5 => 'АТО',
				3 => 'ОИТ',
				4 => 'ОРС',
				2 => 'ОС',
				6 => 'Охрана труда',
				1 => 'УИЗ',
			],
			[
				5 => 'АТО',
				3 => 'ОИТ',
				4 => 'ОРС',
				2 => 'ОС',
				6 => 'Охрана труда',
				7 => 'СО',
				1 => 'УИЗ',
			],
		];
		$this->runClassMethodGroup('getListDepartments', $params, $expected);
	}

/**
 * testSyncInformationDeletedDepartment method
 *
 * @return void
 */
	public function testSyncInformationDeletedDepartment() {
		$this->_targetObject->delete(5);
		$result = $this->_targetObject->syncInformation();
		$expected = true;
		$this->assertData($expected, $result);

		$result = $this->_targetObject->find('count');
		$expected = 7;
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationBlockDepartment method
 *
 * @return void
 */
	public function testSyncInformationBlockDepartment() {
		Configure::write('CakeLdap.LdapSync.Delete.Departments', false);
		$this->_targetObject->id = 2;
		$result = $this->_targetObject->saveField('value', 'SomeDepartment');
		$expected = [
			'DepartmentDb' => [
				'id' => 2,
				'value' => 'SomeDepartment',
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation();
		$expected = true;
		$this->assertData($expected, $result);

		$result = $this->_targetObject->find('count');
		$expected = 8;
		$this->assertData($expected, $result);

		$this->_targetObject->id = 2;
		$result = $this->_targetObject->field('block');
		$this->assertTrue($result);
	}
}
