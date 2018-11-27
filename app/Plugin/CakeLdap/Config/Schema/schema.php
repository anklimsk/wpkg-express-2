<?php
/**
 * This file is the schema file of the plugin.
 *  Use for database management.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('ClassRegistry', 'Utility');
App::uses('ConnectionManager', 'Model');
App::uses('DynSchema', 'CakeLdap.Model');

/**
 * Schema for CakeLdap.
 *
 * @package plugin.Config.Schema
 */
class CakeLdapSchema extends CakeSchema {

/**
 * Before callback to be implemented in subclasses.
 *
 * Actions:
 *  - Disabling cached available tables and schema descriptions.
 *
 * @param array $event Schema object properties.
 * @return bool Should process continue.
 */
	public function before($event = []) {
		$ds = ConnectionManager::getDataSource($this->connection);
		$ds->cacheSources = false;

		return true;
	}

/**
 * After callback to be implemented in subclasses.
 *
 * Actions:
 *  - Removing unused columns from database tables in
 *   accordance with the configuration.
 *
 * @param array $event Schema object properties.
 * @return void
 */
	public function after($event = []) {
		$modelDynSchema = ClassRegistry::init('CakeLdap.DynSchema');
		$modelDynSchema->updateSchema($event, $this->connection);
	}

/**
 * Schema of database table `departments`.
 *
 * @var array
 */
	public $departments = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary'
		],
		'value' => [
			'type' => 'string',
			'null' => true,
			'default' => null,
			'length' => 64,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'block' => [
			'type' => 'boolean',
			'null' => false,
			'default' => '0',
			'length' => 1,
		],
		'indexes' => [
			'PRIMARY' => [
				'column' => 'id',
				'unique' => 1
			],
			'id_UNIQUE' => [
				'column' => 'id',
				'unique' => 1
			]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of database table `othertelephones`.
 *
 * @var array
 */
	public $othertelephones = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary'
		],
		'employee_id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'index'
		],
		'value' => [
			'type' => 'string',
			'length' => 256,
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'indexes' => [
			'PRIMARY' => [
				'column' => 'id',
				'unique' => 1
			],
			'id_UNIQUE' => [
				'column' => 'id',
				'unique' => 1
			]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of database table `othermobiles`.
 *
 * @var array
 */
	public $othermobiles = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary'
		],
		'employee_id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'index'
		],
		'value' => [
			'type' => 'string',
			'length' => 256,
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'indexes' => [
			'PRIMARY' => [
				'column' => 'id',
				'unique' => 1
			],
			'id_UNIQUE' => [
				'column' => 'id',
				'unique' => 1
			]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of database table `employees`.
 *
 * @var array
 */
	public $employees = [
		//--- Begin of required fields ---
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'length' => 10,
			'unsigned' => false,
			'key' => 'primary'
		],
		'department_id' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false
		],
		'manager_id' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false
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
		//--- End of required fields ---
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
		CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
			'type' => 'string',
			'null' => true,
			'default' => null,
			'length' => 256,
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
		],
		'indexes' => [
			'PRIMARY' => [
				'column' => 'id',
				'unique' => 1
			],
			'id_UNIQUE' => [
				'column' => 'id',
				'unique' => 1
			],
			'guid_UNIQUE' => [
				'column' => CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
				'unique' => 1
			],
			'managerId' => [
				'column' => 'manager_id',
				'unique' => 0
			],
		],
		'tableParameters' => [
			'charset' => 'utf8',
			'collate' => 'utf8_general_ci',
			'engine' => 'InnoDB'
		]
	];

/**
 * Schema of database table `subordinates`.
 *
 * @var array
 */
	public $subordinates = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'unsigned' => false,
			'key' => 'primary'
		],
		'parent_id' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false
		],
		'lft' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false
		],
		'rght' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false
		],
		'name' => [
			'type' => 'string',
			'length' => 256,
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'indexes' => [
			'PRIMARY' => [
				'column' => 'id',
				'unique' => 1
			],
			'id_UNIQUE' => [
				'column' => 'id',
				'unique' => 1
			]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];
}
