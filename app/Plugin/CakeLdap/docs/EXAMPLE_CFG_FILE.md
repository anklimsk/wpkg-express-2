# Example of configuration file

```php
$config['CakeLdap'] = [
    'LdapSync' => [
        // Configurations of LDAP fields
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect GUID of employee'),
                        'allowEmpty' => false,
                        'required' => true,
                        'last' => true
                    ],
                    'isUnique' => [
                        'rule' => ['isUnique'],
                        'message' => __d('cake_ldap_validation_errors', 'GUID of employee is not unique'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect distinguished name of employee'),
                        'allowEmpty' => false,
                        'required' => true,
                        'last' => true
                    ],
                    'isUnique' => [
                        'rule' => ['isUnique'],
                        'message' => __d('cake_ldap_validation_errors', 'Distinguished name of employee is not unique'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect full name of employee'),
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
/*
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect last name of employee'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect first name of employee'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect middle name of employee'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect position of employee'),
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
                        'message' => __d('cake_ldap_validation_errors', 'Incorrect E-mail address'),
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
                'truncate' => true,
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
                'default' => null
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
*/
        ],
        // Limits
        'Limits' => [
            'Query' => 1000,
            'Sync' => 5000,
        ],
        // Tree of subordinate employee
        'TreeSubordinate' => [
            'Enable' => false,
            'Draggable' => false
        ],
        // Company name for synchronization with AD
        'Company' => '',
        // Deleting information when synchronizing with AD
        'Delete' => [
            'Departments' => true,
            'Employees' => false,
        ],
        // Configurations query to DB
        'Query' => [
            'UseFindByLdapMultipleFields' => false
        ],
        // The distinguished name of the search base object for searching employees in LDAP.
        'SearchBase' => ''
    ]
];
```
