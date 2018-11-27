<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('SubordinateDb', 'CakeLdap.Model');

/**
 * SubordinateDb Test Case
 */
class SubordinateDbTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee',
		'plugin.cake_ldap.employee_ldap',
		'plugin.cake_ldap.subordinate'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeLdap.SubordinateDb');
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
 * testGetAllSubordinates method
 *
 * @return void
 */
	public function testGetAllSubordinates() {
		$params = [
			[
				null, // $guid
				4, // $limit
			],
			[
				'e3be343c-601e-4b61-a774-3d7fe8f1cf34', // $guid
				null, // $limit
			],
			[
				'9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf', // $guid
				null, // $limit
			],
		];
		$expected = [
			[
				[
					'SubordinateDb' => [
						'id' => '1',
						'parent_id' => null,
						'lft' => '1',
						'rght' => '2',
						'name' => 'Миронов В.М.',
					]
				],
				[
					'SubordinateDb' => [
						'id' => '2',
						'parent_id' => '3',
						'lft' => '4',
						'rght' => '5',
						'name' => 'Егоров Т.Г.',
					]
				],
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
						'name' => 'Суханова Л.Б.',
					]
				],
				[
					'SubordinateDb' => [
						'id' => '4',
						'parent_id' => '8',
						'lft' => '10',
						'rght' => '15',
						'name' => 'Дементьева А.С.',
					]
				]
			],
			[],
			[
				[
					'SubordinateDb' => [
						'id' => '8',
						'parent_id' => null,
						'lft' => '9',
						'rght' => '16',
						'name' => 'Голубев Е.В.'
					]
				]
			],
		];
		$this->runClassMethodGroup('getAllSubordinates', $params, $expected);
	}

/**
 * testSyncInformationBadGuid method
 *
 * @return void
 */
	public function testSyncInformationBadGuid() {
		$result = $this->_targetObject->syncInformation('93604a99-bfdc-4071-b8c0-429389b6f7bc', null);
		$this->assertFalse($result);
	}

/**
 * testSyncInformationNotChangedData method
 *
 * @return void
 */
	public function testSyncInformationNotChangedData() {
		$result = $this->_targetObject->syncInformation(null, null);
		$this->assertTrue($result);
	}

/**
 * testSyncInformationRenamedEmployee method
 *
 * @return void
 */
	public function testSyncInformationRenamedEmployee() {
		$this->_targetObject->id = 7;
		$result = $this->_targetObject->saveField('name', 'SomeName');
		$expected = [
			'SubordinateDb' => [
				'id' => 7,
				'name' => 'SomeName',
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation('8c149661-7215-47de-b40e-35320a1ea508', null);
		$this->assertTrue($result);

		$result = $this->_targetObject->recursive = -1;
		$result = $this->_targetObject->read(null, 7);
		$expected = [
			'SubordinateDb' => [
				'id' => '7',
				'parent_id' => '4',
				'lft' => '11',
				'rght' => '14',
				'name' => 'Хвощинский В.В.',
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationDeletedEmployee method
 *
 * @return void
 */
	public function testSyncInformationDeletedEmployee() {
		$result = $this->_targetObject->delete(4);
		$this->assertTrue($result);

		$result = $this->_targetObject->syncInformation(null, null);
		$this->assertTrue($result);

		$result = $this->_targetObject->recursive = -1;
		$result = $this->_targetObject->read(null, 4);
		$expected = [
			'SubordinateDb' => [
				'id' => '4',
				'parent_id' => '8',
				'lft' => '10',
				'rght' => '15',
				'name' => 'Дементьева А.С.'
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationChangedManagerEmployee method
 *
 * @return void
 */
	public function testSyncInformationChangedManagerEmployee() {
		$this->_targetObject->id = 6;
		$result = $this->_targetObject->saveField('parent_id', null);
		$expected = [
			'SubordinateDb' => [
				'id' => 6,
				'parent_id' => null,
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation(null, null);
		$this->assertTrue($result);

		$result = $this->_targetObject->recursive = -1;
		$result = $this->_targetObject->read(null, 6);
		$expected = [
			'SubordinateDb' => [
				'id' => '6',
				'parent_id' => '7',
				'lft' => '12',
				'rght' => '13',
				'name' => 'Козловская Е.М.'
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result);

		$result = $this->_targetObject->getListTreeEmployee();
		$expected = [
			1 => 'Миронов В.М.',
			3 => 'Суханова Л.Б.',
			2 => '--Егоров Т.Г.',
			5 => 'Матвеев Р.М.',
			8 => 'Голубев Е.В.',
			4 => '--Дементьева А.С.',
			7 => '----Хвощинский В.В.',
			6 => '------Козловская Е.М.',
			10 => 'Чижов Я.С.',
		];
		$this->assertData($expected, $result);
	}

/**
 * testSyncInformationBrokenTree method
 *
 * @return void
 */
	public function testSyncInformationBrokenTree() {
		$this->_targetObject->id = 4;
		$result = $this->_targetObject->saveField('rght', null);
		$expected = [
			'SubordinateDb' => [
				'id' => 4,
				'rght' => null
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->syncInformation(null, null);
		$this->assertFalse($result);
	}

/**
 * testGetListTreeEmployee method
 *
 * @return void
 */
	public function testGetListTreeEmployee() {
		$params = [
			[
				null, // $id
				true, // $includeRoot
				true, // $includeBlocked
			],
			[
				null, // $id
				true, // $includeRoot
				false, // $includeBlocked
			],
			[
				1000, // $id
				true, // $includeRoot
				true, // $includeBlocked
			],
			[
				3, // $id
				true, // $includeRoot
				true, // $includeBlocked
			],
			[
				4, // $id
				false, // $includeRoot
				true, // $includeBlocked
			],
			[
				10, // $id
				false, // $includeRoot
				false, // $includeBlocked
			],
		];
		$expected = [
			[
				1 => 'Миронов В.М.',
				3 => 'Суханова Л.Б.',
				2 => '--Егоров Т.Г.',
				5 => 'Матвеев Р.М.',
				8 => 'Голубев Е.В.',
				4 => '--Дементьева А.С.',
				7 => '----Хвощинский В.В.',
				6 => '------Козловская Е.М.',
				10 => 'Чижов Я.С.',
				9 => 'Марчук А.М.',
			],
			[
				1 => 'Миронов В.М.',
				3 => 'Суханова Л.Б.',
				2 => '--Егоров Т.Г.',
				5 => 'Матвеев Р.М.',
				8 => 'Голубев Е.В.',
				4 => '--Дементьева А.С.',
				7 => '----Хвощинский В.В.',
				6 => '------Козловская Е.М.',
				10 => 'Чижов Я.С.',
			],
			[],
			[
				3 => 'Суханова Л.Б.',
				2 => '--Егоров Т.Г.',
			],
			[
				7 => 'Хвощинский В.В.',
				6 => '--Козловская Е.М.',
			],
			[]
		];
		$this->runClassMethodGroup('getListTreeEmployee', $params, $expected);
	}

/**
 * testGetArrayTreeEmployee method
 *
 * @return void
 */
	public function testGetArrayTreeEmployee() {
		$params = [
			[
				null, // $id
				true, // $includeRoot
				true, // $includeBlocked
				null, // $includeFields
			],
			[
				null, // $id
				true, // $includeRoot
				false, // $includeBlocked
				null, // $includeFields
			],
			[
				1000, // $id
				true, // $includeRoot
				true, // $includeBlocked
				null, // $includeFields
			],
			[
				3, // $id
				true, // $includeRoot
				true, // $includeBlocked
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID, // $includeFields
			],
			[
				4, // $id
				false, // $includeRoot
				true, // $includeBlocked
				['Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL], // $includeFields
			],
			[
				10, // $id
				false, // $includeRoot
				false, // $includeBlocked
				null, // $includeFields
			],
		];
		$expected = [
			[
				[
					'SubordinateDb' => [
						'id' => '1',
						'parent_id' => null,
						'lft' => '1',
						'rght' => '2',
					],
					'Employee' => [
						'id' => '1',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
					],
					'Employee' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
						'block' => false,
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '2',
								'parent_id' => '3',
								'lft' => '4',
								'rght' => '5',
							],
							'Employee' => [
								'id' => '2',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
								'block' => false,
							],
							'children' => []
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '5',
						'parent_id' => null,
						'lft' => '7',
						'rght' => '8',
					],
					'Employee' => [
						'id' => '5',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '8',
						'parent_id' => null,
						'lft' => '9',
						'rght' => '16',
					],
					'Employee' => [
						'id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
						'block' => false,
					],
					'children' => [
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
								'block' => false,
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
										'block' => false,
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
												'block' => false,
											],
											'children' => []
										]
									]
								]
							]
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '10',
						'parent_id' => null,
						'lft' => '17',
						'rght' => '18',
					],
					'Employee' => [
						'id' => '10',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер 1 категории',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '9',
						'parent_id' => null,
						'lft' => '19',
						'rght' => '20',
					],
					'Employee' => [
						'id' => '9',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Марчук А.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер по охране труда',
						'block' => true,
					],
					'children' => []
				]
			],
			[
				[
					'SubordinateDb' => [
						'id' => '1',
						'parent_id' => null,
						'lft' => '1',
						'rght' => '2',
					],
					'Employee' => [
						'id' => '1',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
					],
					'Employee' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '2',
								'parent_id' => '3',
								'lft' => '4',
								'rght' => '5',
							],
							'Employee' => [
								'id' => '2',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
							],
							'children' => []
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '5',
						'parent_id' => null,
						'lft' => '7',
						'rght' => '8',
					],
					'Employee' => [
						'id' => '5',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '8',
						'parent_id' => null,
						'lft' => '9',
						'rght' => '16',
					],
					'Employee' => [
						'id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
					],
					'children' => [
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
					]
				],
				[
					'SubordinateDb' => [
						'id' => '10',
						'parent_id' => null,
						'lft' => '17',
						'rght' => '18',
					],
					'Employee' => [
						'id' => '10',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер 1 категории',
					],
					'children' => []
				]
			],
			[],
			[
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
					],
					'Employee' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
						'block' => false,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '2',
								'parent_id' => '3',
								'lft' => '4',
								'rght' => '5',
							],
							'Employee' => [
								'id' => '2',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
								'block' => false,
								CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0010b7b8-d69a-4365-81ca-5f975584fe5c',
							],
							'children' => []
						]
					]
				]
			],
			[
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
						'block' => false,
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'v.hvoshchinskiy@fabrikam.com',
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
								'block' => false,
								CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.kozlovskaya@fabrikam.com'
							],
							'children' => []
						]
					]
				]
			],
			[],
		];
		$this->runClassMethodGroup('getArrayTreeEmployee', $params, $expected);
	}

/**
 * testReorderEmployeeTree method
 *
 * @return void
 */
	public function testReorderEmployeeTree() {
		$result = $this->_targetObject->reorderEmployeeTree(true);
		$this->assertTrue($result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result);

		$result = $this->_targetObject->getListTreeEmployee();
		$expected = [
			8 => 'Голубев Е.В.',
			4 => '--Дементьева А.С.',
			7 => '----Хвощинский В.В.',
			6 => '------Козловская Е.М.',
			5 => 'Матвеев Р.М.',
			1 => 'Миронов В.М.',
			3 => 'Суханова Л.Б.',
			2 => '--Егоров Т.Г.',
			10 => 'Чижов Я.С.',
		];
		$this->assertData($expected, $result);

		$this->_targetObject->id = 5;
		$result = $this->_targetObject->saveField('lft', null);
		$expected = [
			'SubordinateDb' => [
				'id' => 5,
				'lft' => null
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->reorderEmployeeTree(true);
		$this->assertFalse($result);

		$result = $this->_targetObject->reorderEmployeeTree(false);
		$this->assertTrue($result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result !== true);
	}

/**
 * testRecoverEmployeeTree method
 *
 * @return void
 */
	public function testRecoverEmployeeTree() {
		$result = $this->_targetObject->recoverEmployeeTree(true);
		$this->assertTrue($result);

		$this->_targetObject->id = 4;
		$result = $this->_targetObject->saveField('rght', null);
		$expected = [
			'SubordinateDb' => [
				'id' => 4,
				'rght' => null
			]
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->recoverEmployeeTree(true);
		$this->assertTrue($result);

		$this->_targetObject->id = 8;
		$result = $this->_targetObject->saveField('lft', null);
		$expected = [
			'SubordinateDb' => [
				'id' => 8,
				'lft' => null
			]
		];

		$result = $this->_targetObject->recoverEmployeeTree(false);
		$this->assertTrue($result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result);
	}
}
