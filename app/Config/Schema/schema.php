<?php
/**
 * Schema database management for CakePHP.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       Cake.Model
 * @since         CakePHP(tm) v 1.2.0.5550
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('InstallerInit', 'CakeInstaller.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * Class for Schema management.
 *
 * @package       app.Config.Schema
 */
class AppSchema extends CakeSchema {

/**
 * Before callback to be implemented in subclasses.
 *
 * Actions:
 *  - Disabling caching available tables and schema descriptions.
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
 *  - Initialization of database tables the initial values.
 *
 * @param array $event Schema object properties.
 * @return void
 */
	public function after($event = []) {
		if (!empty($event['errors']) || !isset($event['create'])) {
			return;
		}

		$installerInitModel = ClassRegistry::init('CakeInstaller.InstallerInit');
		$installerInitModel->initDbTable($event['create']);
	}

/**
 * Schema of table `archives`.
 *
 * @var array
 */
	public $archives = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'ref_type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'ref_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'revision' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'data' => ['type' => 'binary', 'null' => false, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `attributes`.
 *
 * @var array
 */
	public $attributes = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'ref_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'ref_type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'ref_node' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'pcre_parsing' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'hostname' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'os' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'architecture' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'ipaddresses' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'domainname' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'groups' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'lcid' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'lcidOS' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `checks`.
 *
 * @var array
 */
	public $checks = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'ref_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'ref_type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'condition' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'path' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'value' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `config_languages`.
 *
 * @var array
 */
	public $config_languages = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'lcid' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notifyUserStart' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notifyUserStop' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notifyUserFail' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notifyUserReboot' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `configs`.
 *
 * @var array
 */
	public $configs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'key' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 40, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'value' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			'key' => ['column' => 'key', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `exitcode_reboot_types`.
 *
 * @var array
 */
	public $exitcode_reboot_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `exit_codes`.
 *
 * @var array
 */
	public $exit_codes = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_action_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'reboot_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false],
		'code' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `garbage`.
 *
 * @var array
 */
	public $garbage = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'ref_type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'ref_id' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'data' => ['type' => 'binary', 'null' => false, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `garbage_types`.
 *
 * @var array
 */
	public $garbage_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `hosts`.
 *
 * @var array
 */
	public $hosts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'mainprofile_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'enabled' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'template' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'id_text' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notes' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			//'id_text' => ['column' => 'id_text', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `hosts_profiles`.
 *
 * @var array
 */
	public $hosts_profiles = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'host_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'profile_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `log_hosts`.
 *
 * @var array
 */
	public $log_hosts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `log_types`.
 *
 * @var array
 */
	public $log_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `logs`.
 *
 * @var array
 */
	public $logs = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'type_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'host_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'message' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_action_types`.
 *
 * @var array
 */
	public $package_action_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'builtin' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'command' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_actions`.
 *
 * @var array
 */
	public $package_actions = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'action_type_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'command_type_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'include_action_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'command' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'timeout' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'workdir' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'expand_url' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_reboot_types`.
 *
 * @var array
 */
	public $package_reboot_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_execute_types`.
 *
 * @var array
 */
	public $package_execute_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_notify_types`.
 *
 * @var array
 */
	public $package_notify_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_precheck_types`.
 *
 * @var array
 */
	public $package_precheck_types = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `packages`.
 *
 * @var array
 */
	public $packages = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'reboot_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'execute_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'notify_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'precheck_install_id' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 10, 'unsigned' => false],
		'precheck_remove_id' => ['type' => 'integer', 'null' => false, 'default' => '2', 'length' => 10, 'unsigned' => false],
		'precheck_upgrade_id' => ['type' => 'integer', 'null' => false, 'default' => '2', 'length' => 10, 'unsigned' => false],
		'precheck_downgrade_id' => ['type' => 'integer', 'null' => false, 'default' => '2', 'length' => 10, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'id_text' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'enabled' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'template' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'revision' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'priority' => ['type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false],
		'notes' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			//'id_text' => ['column' => 'id_text', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `packages_chains`.
 *
 * @var array
 */
	public $packages_chains = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'dependency_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `packages_includes`.
 *
 * @var array
 */
	public $packages_includes = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'dependency_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `packages_packages`.
 *
 * @var array
 */
	public $packages_packages = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'dependency_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `packages_profiles`.
 *
 * @var array
 */
	public $packages_profiles = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'profile_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'installdate' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'uninstalldate' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `package_priorities`.
 *
 * @var array
 */
	public $package_priorities = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'value' => ['type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `profiles`.
 *
 * @var array
 */
	public $profiles = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'enabled' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'template' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'id_text' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'notes' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			//'id_text' => ['column' => 'id_text', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `profiles_profiles`.
 *
 * @var array
 */
	public $profiles_profiles = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'profile_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'dependency_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `report_hosts`.
 *
 * @var array
 */
	public $report_hosts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'hash' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `report_states`.
 *
 * @var array
 */
	public $report_states = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `reports`.
 *
 * @var array
 */
	public $reports = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'state_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'host_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'revision' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `variables`.
 *
 * @var array
 */
	public $variables = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'ref_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'ref_type' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 80, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'value' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `wpi`.
 *
 * @var array
 */
	public $wpi = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'package_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'category_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false],
		'default' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'force' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Schema of table `wpi_categories`.
 *
 * @var array
 */
	public $wpi_categories = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'builtin' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

}
