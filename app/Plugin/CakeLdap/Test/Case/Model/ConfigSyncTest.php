<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ConfigSync', 'CakeLdap.Model');

/**
 * ConfigSync Test Case
 */
class ConfigSyncTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.employee_ldap'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeLdap.ConfigSync');
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
 * testGetQueryLimit method
 *
 * @return void
 */
	public function testGetQueryLimit() {
		$result = $this->_targetObject->getQueryLimit();
		$expected = 1000;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Limits.Query', 5);
		$result = $this->_targetObject->getQueryLimit();
		$expected = 5;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Limits.Query');
		$result = $this->_targetObject->getQueryLimit();
		$expected = CAKE_LDAP_GLOBAL_QUERY_LIMIT;
		$this->assertData($expected, $result);
	}

/**
 * testGetSyncLimit method
 *
 * @return void
 */
	public function testGetSyncLimit() {
		$result = $this->_targetObject->getSyncLimit();
		$expected = 5000;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Limits.Sync', 10);
		$result = $this->_targetObject->getSyncLimit();
		$expected = 10;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Limits.Sync');
		$result = $this->_targetObject->getSyncLimit();
		$expected = CAKE_LDAP_SYNC_AD_LIMIT;
		$this->assertData($expected, $result);
	}

/**
 * testGetCompanyName method
 *
 * @return void
 */
	public function testGetCompanyName() {
		$result = $this->_targetObject->getCompanyName();
		$expected = 'ТестОрг';
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Company');
		$result = $this->_targetObject->getCompanyName();
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagTreeSubordinateEnable method
 *
 * @return void
 */
	public function testGetFlagTreeSubordinateEnable() {
		$result = $this->_targetObject->getFlagTreeSubordinateEnable();
		$expected = true;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER);
		$result = $this->_targetObject->getFlagTreeSubordinateEnable();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$result = $this->_targetObject->getFlagTreeSubordinateEnable();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.TreeSubordinate.Enable');
		$result = $this->_targetObject->getFlagTreeSubordinateEnable();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagTreeSubordinateDraggable method
 *
 * @return void
 */
	public function testGetFlagTreeSubordinateDraggable() {
		$result = $this->_targetObject->getFlagTreeSubordinateDraggable();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Draggable', true);
		$result = $this->_targetObject->getFlagTreeSubordinateDraggable();
		$expected = true;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$result = $this->_targetObject->getFlagTreeSubordinateDraggable();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', true);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER);
		$result = $this->_targetObject->getFlagTreeSubordinateDraggable();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagDeleteDepartments method
 *
 * @return void
 */
	public function testGetFlagDeleteDepartments() {
		Configure::write('CakeLdap.LdapSync.Delete.Departments', true);
		$result = $this->_targetObject->getFlagDeleteDepartments();
		$expected = true;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Delete.Departments', false);
		$result = $this->_targetObject->getFlagDeleteDepartments();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Delete.Departments');
		$result = $this->_targetObject->getFlagDeleteDepartments();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagDeleteEmployees method
 *
 * @return void
 */
	public function testGetFlagDeleteEmployees() {
		Configure::write('CakeLdap.LdapSync.Delete.Employees', false);
		$result = $this->_targetObject->getFlagDeleteEmployees();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Delete.Employees', true);
		$result = $this->_targetObject->getFlagDeleteEmployees();
		$expected = true;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Delete.Employees');
		$result = $this->_targetObject->getFlagDeleteEmployees();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagQueryUseFindByLdapMultipleFields method
 *
 * @return void
 */
	public function testGetFlagQueryUseFindByLdapMultipleFields() {
		Configure::write('CakeLdap.LdapSync.Query.UseFindByLdapMultipleFields', false);
		$result = $this->_targetObject->getFlagQueryUseFindByLdapMultipleFields();
		$expected = false;
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Query.UseFindByLdapMultipleFields', true);
		$result = $this->_targetObject->getFlagQueryUseFindByLdapMultipleFields();
		$expected = true;
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.Query.UseFindByLdapMultipleFields');
		$result = $this->_targetObject->getFlagQueryUseFindByLdapMultipleFields();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetSearchBase method
 *
 * @return void
 */
	public function testGetSearchBase() {
		Configure::write('CakeLdap.LdapSync.SearchBase', '');
		$result = $this->_targetObject->getSearchBase();
		$expected = '';
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.SearchBase', 'CN=Users,DC=fabrikam,DC=com');
		$result = $this->_targetObject->getSearchBase();
		$expected = 'CN=Users,DC=fabrikam,DC=com';
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.SearchBase');
		$result = $this->_targetObject->getSearchBase();
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testGetLocalFieldsInfo method
 *
 * @return void
 */
	public function testGetLocalFieldsInfo() {
		$result = $this->_targetObject->getLocalFieldsInfo();
		$expected = [
			'id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => [
							'naturalNumber'
						],
						'message' => 'Incorrect primary key',
						'allowEmpty' => false,
						'required' => true,
						'last' => true,
						'on' => 'update'
					]
				],
				'default' => null
			],
			'department_id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => [
							'naturalNumber'
						],
						'message' => 'Incorrect foreign key',
						'allowEmpty' => true,
						'required' => true,
						'last' => true
					]
				],
				'default' => null
			],
			'manager_id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => [
							'naturalNumber'
						],
						'message' => 'Incorrect foreign key',
						'allowEmpty' => true,
						'required' => false,
						'last' => true
					]
				],
				'default' => null
			],
			'block' => [
				'label' => __d('cake_ldap_field_name', 'Block'),
				'altLabel' => __d('cake_ldap_field_name', 'Block'),
				'priority' => 100,
				'rules' => [
					'boolean' => [
						'rule' => [
							'boolean'
						],
						'message' => 'Incorrect state of blocking',
						'allowEmpty' => true,
						'required' => false,
						'last' => false
					]
				],
				'default' => false
			]
		];
		$this->assertData($expected, $result);

		Configure::write('CakeLdap.LdapSync.Delete.Employees', true);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER);
		$result = $this->_targetObject->getLocalFieldsInfo();
		$expected = [
			'id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => [
							'naturalNumber'
						],
						'message' => 'Incorrect primary key',
						'allowEmpty' => false,
						'required' => true,
						'last' => true,
						'on' => 'update'
					]
				],
				'default' => null
			],
			'department_id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => [
							'naturalNumber'
						],
						'message' => 'Incorrect foreign key',
						'allowEmpty' => true,
						'required' => true,
						'last' => true
					]
				],
				'default' => null
			],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetLdapFieldsInfo method
 *
 * @return void
 */
	public function testGetLdapFieldsInfo() {
		$result = $this->_targetObject->getLdapFieldsInfo();
		$expected = [
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
				'label' => __d('cake_ldap_field_name', 'GUID'),
				'altLabel' => __d('cake_ldap_field_name', 'GUID'),
				'priority' => 20,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect GUID of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
					'isUnique' => [
						'rule' => ['isUnique'],
						'message' => 'GUID of employee is not unique',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
				'label' => __d('cake_ldap_field_name', 'Distinguished name'),
				'altLabel' => __d('cake_ldap_field_name', 'Disting. name'),
				'priority' => 21,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect distinguished name of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
					'isUnique' => [
						'rule' => ['isUnique'],
						'message' => 'distinguished name of employee is not unique',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Full name'),
				'altLabel' => __d('cake_ldap_field_name', 'Full name'),
				'priority' => 1,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect full name of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
				'label' => __d('cake_ldap_field_name', 'Display name'),
				'altLabel' => __d('cake_ldap_field_name', 'Displ. name'),
				'priority' => 2,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
				'label' => __d('cake_ldap_field_name', 'Initials'),
				'altLabel' => __d('cake_ldap_field_name', 'Init.'),
				'priority' => 24,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
				'label' => __d('cake_ldap_field_name', 'Surname'),
				'altLabel' => __d('cake_ldap_field_name', 'Surn.'),
				'priority' => 3,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect last name of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
				'label' => __d('cake_ldap_field_name', 'Given name'),
				'altLabel' => __d('cake_ldap_field_name', 'Giv. name'),
				'priority' => 4,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect first name of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Middle name'),
				'altLabel' => __d('cake_ldap_field_name', 'Mid. name'),
				'priority' => 5,
				'truncate' => false,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect middle name of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
				'label' => __d('cake_ldap_field_name', 'Position'),
				'altLabel' => __d('cake_ldap_field_name', 'Pos.'),
				'priority' => 15,
				'truncate' => true,
				'rules' => [
					'notBlank' => [
						'rule' => ['notBlank'],
						'message' => 'Incorrect position of employee',
						'allowEmpty' => false,
						'required' => true,
						'last' => true
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
				'label' => __d('cake_ldap_field_name', 'Subdivision'),
				'altLabel' => __d('cake_ldap_field_name', 'Subdiv.'),
				'priority' => 14,
				'truncate' => true,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT => [
				'label' => __d('cake_ldap_field_name', 'Department'),
				'altLabel' => __d('cake_ldap_field_name', 'Depart.'),
				'priority' => 13,
				'truncate' => true,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Telephone'),
				'altLabel' => __d('cake_ldap_field_name', 'Tel.'),
				'priority' => 8,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Other telephone'),
				'altLabel' => __d('cake_ldap_field_name', 'Other tel.'),
				'priority' => 9,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Mobile telephone'),
				'altLabel' => __d('cake_ldap_field_name', 'Mob. tel.'),
				'priority' => 10,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => [
				'label' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'altLabel' => __d('cake_ldap_field_name', 'Other mob. tel.'),
				'priority' => 11,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
				'label' => __d('cake_ldap_field_name', 'Office room'),
				'altLabel' => __d('cake_ldap_field_name', 'Office'),
				'priority' => 12,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
				'label' => __d('cake_ldap_field_name', 'E-mail'),
				'altLabel' => __d('cake_ldap_field_name', 'E-mail'),
				'priority' => 6,
				'truncate' => false,
				'rules' => [
					'email' => [
						'rule' => ['email'],
						'message' => 'Incorrect E-mail address',
						'allowEmpty' => true,
						'required' => false,
						'last' => false,
					],
				],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER => [
				'label' => __d('cake_ldap_field_name', 'Manager'),
				'altLabel' => __d('cake_ldap_field_name', 'Manag.'),
				'priority' => 16,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
				'label' => __d('cake_ldap_field_name', 'Photo'),
				'altLabel' => __d('cake_ldap_field_name', 'Photo'),
				'priority' => 22,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
				'label' => __d('cake_ldap_field_name', 'Computer'),
				'altLabel' => __d('cake_ldap_field_name', 'Comp.'),
				'priority' => 18,
				'truncate' => true,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
				'label' => __d('cake_ldap_field_name', 'Employee ID'),
				'altLabel' => __d('cake_ldap_field_name', 'Empl. ID'),
				'priority' => 19,
				'truncate' => false,
				'rules' => [],
				'default' => '-x-'
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
				'label' => __d('cake_ldap_field_name', 'Company name'),
				'altLabel' => __d('cake_ldap_field_name', 'Comp. name'),
				'priority' => 23,
				'truncate' => true,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
				'label' => __d('cake_ldap_field_name', 'Birthday'),
				'altLabel' => __d('cake_ldap_field_name', 'Birthd.'),
				'priority' => 17,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
				'label' => __d('cake_ldap_field_name', 'SIP telephone'),
				'altLabel' => __d('cake_ldap_field_name', 'SIP tel.'),
				'priority' => 7,
				'truncate' => false,
				'rules' => [],
				'default' => null
			],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListFieldsDb method
 *
 * @return void
 */
	public function testGetListFieldsDb() {
		$result = $this->_targetObject->getListFieldsDb();
		$expected = [
			'id',
			'department_id',
			'manager_id',
			'block',
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
		];
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL);
		$result = $this->_targetObject->getListFieldsDb();
		$expected = [
			'id',
			'department_id',
			'block',
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
			CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListFieldsLdap method
 *
 * @return void
 */
	public function testGetListFieldsLdap() {
		$result = $this->_targetObject->getListFieldsLdap();
		$expected = [
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
			CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT,
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
			CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER,
			CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
		];
		$this->assertData($expected, $result);

		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO);
		Configure::delete('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY);
		$result = $this->_targetObject->getListFieldsLdap();
		$expected = [
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
			CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE,
			CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION,
			CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT,
			CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_MAIL,
			CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER,
			CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
			CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
			CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
			CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE,
		];
		$this->assertData($expected, $result);
	}
}
