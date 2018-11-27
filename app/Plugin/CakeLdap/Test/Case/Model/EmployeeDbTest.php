<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('EmployeeDb', 'CakeLdap.Model');

/**
 * EmployeeDb Test Case
 */
class EmployeeDbTest extends AppCakeTestCase {

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
		$this->_targetObject = ClassRegistry::init('CakeLdap.EmployeeDb');
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
 * testBeforeFindUseFindByLdapMultipleFieldsDisable method
 *
 * @return void
 */
	public function testBeforeFindUseFindByLdapMultipleFieldsDisable() {
		$query = [
			'conditions' => [
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER . ' like' => '%ива%',
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT . ' like' => '%бух%',
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER . ' like' => '%364%'
			],
			'fields' => 'COUNT(*) AS `count`',
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => [
				0 => false
			],
			'page' => (int)1,
			'group' => null,
			'callbacks' => true
		];
		Configure::write('CakeLdap.LdapSync.Query.UseFindByLdapMultipleFields', false);
		$result = $this->_targetObject->beforeFind($query);
		$this->assertTrue($result);
	}

/**
 * testBeforeFindUseFindByLdapMultipleFieldsEnable method
 *
 * @return void
 */
	public function testBeforeFindUseFindByLdapMultipleFieldsEnable() {
		$query = [
			'conditions' => [
				'OR' => [
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER . ' like' => '%ива%',
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT . ' like' => '%бух%',
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER . ' like' => '%364%'
				]
			],
			'fields' => 'COUNT(*) AS `count`',
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => [
				0 => false
			],
			'page' => (int)1,
			'group' => null,
			'callbacks' => true
		];
		Configure::write('CakeLdap.LdapSync.Query.UseFindByLdapMultipleFields', true);
		$result = $this->_targetObject->beforeFind($query);
		$expected = [
			'conditions' => [
				'OR' => [
					'Manager.name like' => '%ива%',
					'Department.value like' => '%бух%',
					(object)[
						'type' => 'expression',
						'value' => 'Employee.id IN (SELECT Othertelephone.employee_id FROM othertelephones AS `Othertelephone`   WHERE `Othertelephone`.`value` like \'%364%\') '
					]
				]
			],
			'fields' => 'COUNT(*) AS `count`',
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => [
				false
			],
			'page' => 1,
			'group' => null,
			'callbacks' => true,
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testGetFieldsDefaultValue method
 *
 * @return void
 */
	public function testGetFieldsDefaultValue() {
		$result = $this->_targetObject->getFieldsDefaultValue();
		$expected = [
			'id' => null,
			'department_id' => null,
			'manager_id' => null,
			'block' => false,
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '-x-',
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => null,
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => null,
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListContain method
 *
 * @return void
 */
	public function testGetListContain() {
		$params = [
			[
				[], // $excludeModels
				[], // $fields
				[], // $excludeFields
			],
			[
				'Othertelephone', // $excludeModels
				[], // $fields
				[], // $excludeFields
			],
			[
				[
					'Manager',
					'Subordinate',
				], // $excludeModels
				[], // $fields
				[], // $excludeFields
			],
			[
				[
					'Manager',
				], // $excludeModels
				[
					'manager_id',
					'department_id',
				], // $fields
				[], // $excludeFields
			],
			[
				[
					'Subordinate',
				], // $excludeModels
				[
					'manager_id',
					'department_id',
				], // $fields
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER
				], // $excludeFields
			]
		];
		$expected = [
			[
				'Department',
				'Manager',
				'Subordinate',
				'Othertelephone',
				'Othermobile',
			],
			[
				'Department',
				'Manager',
				'Subordinate',
				'Othermobile',
			],
			[
				'Department',
				'Othertelephone',
				'Othermobile',
			],
			[
				'Department',
				'Subordinate',
				'Othertelephone',
				'Othermobile',
			],
			[
				'Department',
				'Manager',
				'Othertelephone',
			],
		];
		$this->runClassMethodGroup('getListContain', $params, $expected);

		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER);
		$result = $this->_targetObject->getListContain();
		$expected = [
			'Manager',
			'Subordinate',
			'Othermobile',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListLocalFields method
 *
 * @return void
 */
	public function testGetListLocalFields() {
		$params = [
			[
				null, // $excludeFields
			],
			[
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE, // $excludeFields
			],
			[
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
				], // $excludeFields
			],
		];
		$expected = [
			[
				'id',
				'department_id',
				'manager_id',
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
				'block',
			],
			[
				'id',
				'department_id',
				'manager_id',
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
				'block',
			],
			[
				'id',
				'department_id',
				'manager_id',
				CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
				CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
				CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
				CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
				CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
				'block',
			],
		];
	}

/**
 * testGetOrderFiled method
 *
 * @return void
 */
	public function testGetOrderFiled() {
		$result = $this->_targetObject->getOrderFiled();
		$expected = [
			$this->_targetObject->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'asc'
		];
		$this->assertData($expected, $result);
	}

/**
 * testExistsEmployee method
 *
 * @return void
 */
	public function testExistsEmployee() {
		$params = [
			[
				null, // $id
			],
			[
				1000, // $id
			],
			[
				'CN=SomeUser,OU=Users,DC=fabrikam,DC=com', // $id
			],
			[
				4, // $id
			],
			[
				'CN=Егоров Т.Г.,OU=14-01,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com', // $id
			],
			[
				'dd518c55-35ce-4a5c-85c5-b5fb762220bf', // $id
			],
		];
		$expected = [
			false,
			false,
			false,
			true,
			true,
			true,
		];
		$this->runClassMethodGroup('existsEmployee', $params, $expected);
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
				null, // $excludeFields
				false, // $includeExtend
				null, // $fieldsList
				null, // $contain
			],
			[
				1001, // $id
				null, // $excludeFields
				false, // $includeExtend
				null, // $fieldsList
				null, // $contain
			],
			[
				'CN=SomeUser,OU=Users,DC=fabrikam,DC=com', // $id
				null, // $excludeFields
				false, // $includeExtend
				null, // $fieldsList
				null, // $contain
			],
			[
				'CN=Хвощинский В.В.,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com', // $id
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO, // $excludeFields
				false, // $includeExtend
				null, // $fieldsList
				null, // $contain
			],
			[
				8, // $id
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID
				], // $excludeFields
				true, // $includeExtend
				null, // $fieldsList
				null, // $contain
			],
			[
				'd4bd663f-37da-4737-bfd8-e6442e723722', // $id
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
				], // $excludeFields
				false, // $includeExtend
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME
				], // $fieldsList
				null, // $contain
			],
			[
				1, // $id
				[CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER], // $excludeFields
				true, // $includeExtend
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME
				], // $fieldsList
				null, // $contain
			],
		];
		$expected = [
			false,
			[],
			[],
			[
				'EmployeeDb' => [
					'id' => '7',
					'department_id' => '3',
					'manager_id' => '4',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '8c149661-7215-47de-b40e-35320a1ea508',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Хвощинский В.В.,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Хвощинский В.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'В.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Хвощинский',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Виктор',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '320',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000004',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '217',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'v.hvoshchinskiy@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0386',
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1304',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-08-08',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501321',
					'block' => false,
				],
				'Department' => [
					'id' => '3',
					'value' => 'ОИТ',
					'block' => false,
				],
				'Manager' => [
					'id' => '4',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
				],
				'Subordinate' => [
					[
						'id' => '6',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
						'manager_id' => '7',
					]
				],
				'Othertelephone' => [
					[
						'id' => '8',
						'value' => '+375171000008',
						'employee_id' => '7',
					],
					[
						'id' => '9',
						'value' => '+375171000009',
						'employee_id' => '7',
					]
				],
				'Othermobile' => []
			],
			[
				'EmployeeDb' => [
					'id' => '8',
					'department_id' => '5',
					'manager_id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Голубев Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Голубев',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Егор',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000005',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => 'Гараж',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.golubev@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1950-12-14',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '',
					'block' => false,
				],
				'Department' => [
					'id' => '5',
					'value' => 'АТО',
					'block' => false,
				],
				'Manager' => [
					'id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
				],
				'Subordinate' => [
					[
						'SubordinateDb' => [
							'id' => '4',
							'parent_id' => '8',
							'lft' => '10',
							'rght' => '15',
						],
						'Employee' => [
							'id' => '4',
							CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
							CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
						],
						'children' => [
							[
								'SubordinateDb' => [
									'id' => '7',
									'parent_id' => '4',
									'lft' => '11',
									'rght' => '14',
								],
								'Employee' => [
									'id' => '7',
									CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
									CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
								],
								'children' => [
									[
										'SubordinateDb' => [
											'id' => '6',
											'parent_id' => '7',
											'lft' => '12',
											'rght' => '13',
										],
										'Employee' => [
											'id' => '6',
											CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
											CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
										],
										'children' => []
									]
								]
							]
						]
					]
				],
				'Othertelephone' => [],
				'Othermobile' => [
					[
						'id' => '4',
						'value' => '+375291000004',
						'employee_id' => '8',
					]
				]
			],
			[
				'EmployeeDb' => [
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
					'id' => '4',
				],
				'Othertelephone' => [],
				'Othermobile' => [
					[
						'id' => '3',
						'value' => '+375291000003',
						'employee_id' => '4',
					]
				]
			],
			[
				'EmployeeDb' => [
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Миронов В.М.',
					'id' => '1'
				],
				'Othertelephone' => [
					[
						'id' => '1',
						'value' => '+375171000001',
						'employee_id' => '1'
					]
				],
			],
		];
		$this->runClassMethodGroup('get', $params, $expected);
	}

/**
 * testGetListEmployees method
 *
 * @return void
 */
	public function testGetListEmployees() {
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
				8 => 'Голубев Е.В.',
				4 => 'Дементьева А.С.',
				2 => 'Егоров Т.Г.',
				6 => 'Козловская Е.М.',
				5 => 'Матвеев Р.М.',
				1 => 'Миронов В.М.',
				3 => 'Суханова Л.Б.',
				7 => 'Хвощинский В.В.',
				10 => 'Чижов Я.С.',
			],
			[
				8 => 'Голубев Е.В.',
				4 => 'Дементьева А.С.',
				2 => 'Егоров Т.Г.',
				6 => 'Козловская Е.М.',
				9 => 'Марчук А.М.',
				5 => 'Матвеев Р.М.',
				1 => 'Миронов В.М.',
				3 => 'Суханова Л.Б.',
				7 => 'Хвощинский В.В.',
				10 => 'Чижов Я.С.',
			]
		];
		$this->runClassMethodGroup('getListEmployees', $params, $expected);
	}

/**
 * testGetAllEmployees method
 *
 * @return void
 */
	public function testGetAllEmployees() {
		$params = [
			[
				null, // $guid
				2, // $limit
			],
			[
				'44bd3667-0787-4329-9f3f-c7bef8eb4f98', // $guid
				null, // $limit
			],
			[
				'0400f8f5-6cba-4f1e-8471-fa6e73415673', // $guid
				null, // $limit
			],
		];
		$expected = [
			[
				[
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
					'Othertelephone' => [
						[
							'id' => '2',
							'value' => '+375171000002',
							'employee_id' => '2',
						],
					],
					'Othermobile' => []
				],
				[
					'EmployeeDb' => [
						'id' => '5',
						'department_id' => '4',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0400f8f5-6cba-4f1e-8471-fa6e73415673',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Матвеев Р.М.,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Матвеев',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Руслан',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Михайлович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа №3',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '292',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000002',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '407',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'r.matveev@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0276',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '6058',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-03-03',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501292',
						'block' => false,
					],
					'Department' => [
						'id' => '4',
						'value' => 'ОРС',
						'block' => false,
					],
					'Othertelephone' => [
						[
							'id' => '6',
							'value' => '+375171000006',
							'employee_id' => '5',
						],
					],
					'Othermobile' => [],
				]
			],
			[],
			[
				[
					'EmployeeDb' => [
						'id' => '5',
						'department_id' => '4',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0400f8f5-6cba-4f1e-8471-fa6e73415673',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Матвеев Р.М.,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Матвеев',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Руслан',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Михайлович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа №3',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '292',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000002',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '407',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'r.matveev@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0276',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '6058',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-03-03',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501292',
						'block' => false,
					],
					'Department' => [
						'id' => '4',
						'value' => 'ОРС',
						'block' => false,
					],
					'Othertelephone' => [
						[
							'id' => '6',
							'value' => '+375171000006',
							'employee_id' => '5',
						]
					],
					'Othermobile' => []
				]
			],
		];
		$this->runClassMethodGroup('getAllEmployees', $params, $expected);
	}

/**
 * testGetListEmployeesManager method
 *
 * @return void
 */
	public function testGetListEmployeesManager() {
		$params = [
			[
				null, // $guid
				4, // $limit
			],
			[
				'e3ad890f-ad1c-45a1-9ecc-5c4a574d4f05', // $guid
				null, // $limit
			],
			[
				'81817f32-44a7-4b4a-8eff-b837ba387077', // $guid
				null, // $limit
			],
		];
		$expected = [
			[
				1 => [
					'id' => '1',
					'manager_id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
				],
				2 => [
					'id' => '2',
					'manager_id' => '3',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
				],
				3 => [
					'id' => '3',
					'manager_id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
				],
				4 => [
					'id' => '4',
					'manager_id' => '8',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
				]
			],
			[],
			[
				6 => [
					'id' => '6',
					'manager_id' => '7',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.'
				]
			],
		];
		$this->runClassMethodGroup('getListEmployeesManager', $params, $expected);
	}

/**
 * testGetPaginateOptions method
 *
 * @return void
 */
	public function testGetPaginateOptions() {
		$params = [
			[
				null, // $excludeFields
			],
			[
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO, // $excludeFields
			],
			[
				[
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER
				], // $excludeFields
			],
		];
		$expected = [
			[
				'page' => 1,
				'limit' => 20,
				'fields' => [
					'id',
					'department_id',
					'manager_id',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
					'block',
				],
				'order' => [
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'asc'
				],
				'contain' => [
					'Department',
					'Manager',
					'Othertelephone',
					'Othermobile',
				]
			],
			[
				'page' => 1,
				'limit' => 20,
				'fields' => [
					'id',
					'department_id',
					'manager_id',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
					'block',
				],
				'order' => [
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'asc'
				],
				'contain' => [
					'Department',
					'Manager',
					'Othertelephone',
					'Othermobile',
				]
			],
			[
				'page' => 1,
				'limit' => 20,
				'fields' => [
					'id',
					'department_id',
					'manager_id',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
					'block',
				],
				'order' => [
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'asc'
				],
				'contain' => [
					'Department',
					'Manager',
					'Othertelephone',
					'Othermobile',
				]
			],
		];
		$this->runClassMethodGroup('getPaginateOptions', $params, $expected);
	}

/**
 * testGetFilterOptions method
 *
 * @return void
 */
	public function testGetFilterOptions() {
		$result = $this->_targetObject->getFilterOptions();
		$expected = [
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Full name')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
				'label' => __d('cake_ldap_field_name', 'Displ. name')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
				'label' => __d('cake_ldap_field_name', 'Surn.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
				'label' => __d('cake_ldap_field_name', 'Giv. name')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Mid. name')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
				'label' => __d('cake_ldap_field_name', 'E-mail')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
				'label' => __d('cake_ldap_field_name', 'SIP tel.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Tel.')
			],
			'Othertelephone.{n}.value' => [
				'label' => __d('cake_ldap_field_name', 'Other tel.'),
				'disabled' => true
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Mob. tel.')
			],
			'Othermobile.{n}.value' => [
				'label' => __d('cake_ldap_field_name', 'Other mob. tel.'),
				'disabled' => true
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Office')
			],
			'Department.value' => [
				'label' => __d('cake_ldap_field_name', 'Depart.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
				'label' => __d('cake_ldap_field_name', 'Subdiv.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
				'label' => __d('cake_ldap_field_name', 'Pos.')
			],
			'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Manag.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
				'label' => __d('cake_ldap_field_name', 'Birthd.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
				'label' => __d('cake_ldap_field_name', 'Comp.')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
				'label' => __d('cake_ldap_field_name', 'Empl. ID')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
				'label' => __d('cake_ldap_field_name', 'Photo')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
				'label' => __d('cake_ldap_field_name', 'Comp. name')
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
				'label' => __d('cake_ldap_field_name', 'Init.')
			],
			'EmployeeDb.block' => [
				'label' => __d('cake_ldap_field_name', 'Block')
			],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetExtendFieldsInfo method
 *
 * @return void
 */
	public function testGetExtendFieldsInfo() {
		$result = $this->_targetObject->getExtendFieldsInfo();
		$expected = [
			'Subordinate.{n}' => [
				'label' => __d('cake_ldap_field_name', 'Subordinate'),
				'altLabel' => __d('cake_ldap_field_name', 'Subord.'),
				'priority' => 10,
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListFieldsLabel method
 *
 * @return void
 */
	public function testGetListFieldsLabel() {
		$params = [
			[
				null, // $excludeFields
				false, // $useAlternative
			],
			[
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL, // $excludeFields
				true, // $useAlternative
			],
			[
				[
					'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
					'EmployeeDb.block'
				], // $excludeFields
				false, // $useAlternative
			],
		];
		$expected = [
			[
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => __d('cake_ldap_field_name', 'GUID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => __d('cake_ldap_field_name', 'Distinguished name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => __d('cake_ldap_field_name', 'Company name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => __d('cake_ldap_field_name', 'Initials'),
				'EmployeeDb.block' => __d('cake_ldap_field_name', 'Block'),
			],
			[
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Displ. name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surn.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Giv. name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Mid. name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP tel.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Tel.'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other tel.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mob. tel.'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mob. tel.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office'),
				'Department.value' => __d('cake_ldap_field_name', 'Depart.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdiv.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Pos.'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manag.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthd.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Comp.'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Empl. ID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => __d('cake_ldap_field_name', 'GUID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => __d('cake_ldap_field_name', 'Disting. name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => __d('cake_ldap_field_name', 'Comp. name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => __d('cake_ldap_field_name', 'Init.'),
				'EmployeeDb.block' => __d('cake_ldap_field_name', 'Block'),
			],
			[
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => __d('cake_ldap_field_name', 'GUID'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => __d('cake_ldap_field_name', 'Distinguished name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => __d('cake_ldap_field_name', 'Company name'),
				'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => __d('cake_ldap_field_name', 'Initials'),
			],
		];
		$this->runClassMethodGroup('getListFieldsLabel', $params, $expected);
	}

/**
 * testGetListFieldsLabelExtend method
 *
 * @return void
 */
	public function testGetListFieldsLabelExtend() {
		$params = [
			[
				false, // $useAlternative
				[] // $excludeFields
			],
			[
				true, // $useAlternative
				[] // $excludeFields
			],
			[
				true, // $useAlternative
				['Subordinate.{n}'] // $excludeFields
			],
		];
		$expected = [
			[
				'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subordinate')
			],
			[
				'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subord.')
			],
			[],
		];
		$this->runClassMethodGroup('getListFieldsLabelExtend', $params, $expected);
	}

/**
 * testGetExtendFieldsConfig method
 *
 * @return void
 */
	public function testGetExtendFieldsConfig() {
		$result = $this->_targetObject->getExtendFieldsConfig();
		$expected = [
			'Subordinate.{n}' => [
				'type' => 'element',
				'truncate' => false,
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFieldsConfig method
 *
 * @return void
 */
	public function testGetFieldsConfig() {
		$result = $this->_targetObject->getFieldsConfig();
		$expected = [
			'EmployeeDb.id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'EmployeeDb.department_id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'EmployeeDb.manager_id' => [
				'type' => 'integer',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
				'type' => 'guid',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
				'type' => 'string',
				'truncate' => true,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
				'type' => 'string',
				'truncate' => true,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
				'type' => CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
				'type' => 'photo',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
				'type' => 'string',
				'truncate' => true,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
				'type' => 'string',
				'truncate' => true,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
				'type' => 'date',
				'truncate' => false,
			],
			'EmployeeDb.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
				'type' => 'string',
				'truncate' => false,
			],
			'EmployeeDb.block' => [
				'type' => 'boolean',
				'truncate' => false,
			],
			'Department.value' => [
				'type' => 'string',
				'truncate' => true,
			],
			'Othertelephone.{n}.value' => [
				'type' => 'string',
				'truncate' => false,
			],
			'Othermobile.{n}.value' => [
				'type' => 'string',
				'truncate' => false,
			],
			'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'type' => 'manager',
				'truncate' => false,
			],
			'Subordinate.{n}' => [
				'type' => 'element',
				'truncate' => false,
			],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetPluginName method
 *
 * @return void
 */
	public function testGetPluginName() {
		$result = $this->_targetObject->getPluginName();
		$expected = 'cake_ldap';
		$this->assertData($expected, $result);
	}

/**
 * testGetControllerName method
 *
 * @return void
 */
	public function testGetControllerName() {
		$result = $this->_targetObject->getControllerName();
		$expected = 'employees';
		$this->assertData($expected, $result);
	}

/**
 * testGetGroupName method
 *
 * @return void
 */
	public function testGetGroupName() {
		$result = $this->_targetObject->getGroupName();
		$expected = __d('cake_ldap', 'Employees');
		$this->assertData($expected, $result);
	}
}
