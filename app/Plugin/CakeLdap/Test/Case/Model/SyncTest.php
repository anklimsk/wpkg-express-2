<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('Sync', 'CakeLdap.Model');

/**
 * Sync Test Case
 */
class SyncTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee',
		'plugin.cake_ldap.employee_ldap',
		'plugin.cake_ldap.othermobile',
		'plugin.cake_ldap.othertelephone',
		'plugin.cake_ldap.subordinate',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeLdap.Sync');
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
 * testSyncInformationBadGuid method
 *
 * @return void
 */
	public function testSyncInformationBadGuid() {
		$result = $this->_targetObject->syncInformation('5bcf5956-3178-468c-8c0a-c9e23b9440e1', null);
		$this->assertFalse($result);
	}

/**
 * testSyncInformationDepartment method
 *
 * @return void
 */
	public function testSyncInformationDepartment() {
		$modelDepartment = ClassRegistry::init('CakeLdap.DepartmentDb');
		$modelDepartment->id = 2;
		$result = $modelDepartment->saveField('value', 'Dept');
		$expected = [
			$modelDepartment->alias => [
				'id' => 2,
				'value' => 'Dept',
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationOtherTelephone method
 *
 * @return void
 */
	public function testSyncInformationOtherTelephone() {
		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$modelEmployee->id = 2;
		$result = $modelEmployee->saveField(CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME, 'Test');
		$expected = [
			$modelEmployee->alias => [
				'id' => 2,
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Test',
			]
		];
		$this->assertData($expected, $result);

		$modelOthertelephone = ClassRegistry::init('CakeLdap.OthertelephoneDb');
		$modelOthertelephone->id = 2;
		$result = $modelOthertelephone->saveField('value', '+375171000000');
		$expected = [
			$modelOthertelephone->alias => [
				'id' => 2,
				'value' => '+375171000000',
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation('0010b7b8-d69a-4365-81ca-5f975584fe5c', null);
		$this->assertTrue($result);

		$result = $modelEmployee->read(null, 2);
		$expected = [
			'EmployeeDb' => [
				'id' => '2',
				'department_id' => '2',
				'manager_id' => '3',
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0010b7b8-d69a-4365-81ca-5f975584fe5c',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Егоров Т.Г.,OU=14-01,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Егоров Т.Г.',
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Т.Г.',
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Егоров',
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Тимофей',
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Геннадьевич',
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа связи №1',
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '261',
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '504',
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 't.egorov@fabrikam.com',
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0390',
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1631',
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-07-27',
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501261',
				'block' => false,
			],
			'Department' => [
				'id' => '2',
				'value' => 'ОС',
				'block' => false,
			],
			'Manager' => [
				'id' => '3',
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
			],
			'Subordinate' => [],
			'Othertelephone' => [
				[
					'id' => '11',
					'value' => '+375171000002',
					'employee_id' => '2',
				]
			],
			'Othermobile' => []
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationOtherMobile method
 *
 * @return void
 */
	public function testSyncInformationOtherMobile() {
		$modelEmployeeLdap = ClassRegistry::init('CakeLdap.EmployeeLdap');
		$modelEmployeeLdap->id = 1;
		$telephones = serialize(['+375291000001', '+375295000009']);
		$result = $modelEmployeeLdap->saveField(CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER, $telephones);
		$expected = [
			$modelEmployeeLdap->alias => [
				$modelEmployeeLdap->primaryKey => 1,
				CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => $telephones,
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation('1dde2cdc-5264-4286-9273-4a88b230237c', null);
		$this->assertTrue($result);

		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$result = $modelEmployee->read(null, 1);
		$expected = [
			'EmployeeDb' => [
				'id' => '1',
				'department_id' => '1',
				'manager_id' => null,
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '1dde2cdc-5264-4286-9273-4a88b230237c',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Миронов В.М.,OU=12-05,OU=УИЗ,OU=Пользователи,DC=fabrikam,DC=com',
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Миронов В.М.',
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'В.М.',
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Миронов',
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Вячеслав',
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Миронович',
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Геологический отдел (ГО)',
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '380',
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '214',
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'v.mironov@fabrikam.com',
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '8060',
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '2015-07-20',
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '50380',
				'block' => false,
			],
			'Department' => [
				'id' => '1',
				'value' => 'УИЗ',
				'block' => false,
			],
			'Manager' => [
				'id' => null,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
			],
			'Subordinate' => [],
			'Othertelephone' => [
				[
					'id' => '1',
					'value' => '+375171000001',
					'employee_id' => '1',
				],
			],
			'Othermobile' => [
				[
					'id' => '1',
					'value' => '+375291000001',
					'employee_id' => '1',
				],
				[
					'id' => '5',
					'value' => '+375295000009',
					'employee_id' => '1',
				]
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationSubordinate method
 *
 * @return void
 */
	public function testSyncInformationSubordinate() {
		$modelSubordinateDb = ClassRegistry::init('CakeLdap.SubordinateDb');
		$result = $modelSubordinateDb->delete(3);
		$this->assertTrue($result);

		$modelSubordinateDb->read(null, 7);
		$result = (bool)$modelSubordinateDb->save(['parent_id' => 5]);
		$this->assertTrue($result);

		$result = $this->_targetObject->syncInformation(null, null);
		$this->assertTrue($result);

		$modelEmployee = ClassRegistry::init('CakeLdap.EmployeeDb');
		$modelEmployee->id = 9;
		$result = $modelEmployee->field('block');
		$this->assertFalse($result);

		$result = $modelSubordinateDb->getListTreeEmployee(null, true, true);
		$expected = [
			1 => 'Миронов В.М.',
			5 => 'Матвеев Р.М.',
			8 => 'Голубев Е.В.',
			4 => '--Дементьева А.С.',
			7 => '----Хвощинский В.В.',
			6 => '------Козловская Е.М.',
			10 => 'Чижов Я.С.',
			9 => 'Марчук А.М.',
			3 => 'Суханова Л.Б.',
			2 => '--Егоров Т.Г.',
		];
		$this->assertData($expected, $result);

		$result = $modelSubordinateDb->verify();
		$this->assertTrue($result);
	}
}
