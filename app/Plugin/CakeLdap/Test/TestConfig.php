<?php
/**
 * This file contain configure for testing
 *
 * To modify parameters, copy this file into your own CakePHP APP/Test directory.
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

$config['CakeLdap'] = [
	'LdapSync' => [
		'LdapFields' => [
			//--- Begin of required fields ---
			// Ldap attribute name
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
				// Label
				'label' => __d('cake_ldap_field_name', 'GUID'),
				// Alternative label
				'altLabel' => __d('cake_ldap_field_name', 'GUID'),
				// Order priority
				'priority' => 20,
				// Truncate text in table
				'truncate' => false,
				// Validation rules
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
				// Default value
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
			//--- End of required fields ---
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
		],
		'Limits' => [
			'Query' => 1000,
			'Sync' => 5000,
		],
		'TreeSubordinate' => [
			'Enable' => true,
			'Draggable' => false
		],
		'Company' => 'ТестОрг',
		'Delete' => [
			'Departments' => false,
			'Employees' => false,
		],
		'Query' => [
			'UseFindByLdapMultipleFields' => false
		],
		'SearchBase' => ''
	]
];
