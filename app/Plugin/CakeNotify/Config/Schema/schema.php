<?php
/**
 * This file is the schema file of the plugin.
 *  Use for database management.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

/**
 * Schema for CakeNotify.
 *
 * @package plugin.Config.Schema
 */
class CakeNotifySchema extends CakeSchema {

/**
 * Schema of database table `notifications`.
 *
 * @var array
 */
	public $notifications = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'default' => null,
			'length' => 10,
			'unsigned' => false,
			'key' => 'primary'
		],
		'user_id' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 10,
			'unsigned' => false,
		],
		'user_role' => [
			'type' => 'integer',
			'null' => true,
			'default' => null,
			'length' => 1,
			'unsigned' => false,
		],
		'expires' => [
			'type' => 'datetime',
			'null' => false,
			'default' => null,
		],
		'tag' => [
			'type' => 'string',
			'null' => true,
			'default' => null,
			'length' => 30,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'title' => [
			'type' => 'string',
			'null' => false,
			'default' => null,
			'length' => 30,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'body' => [
			'type' => 'text',
			'null' => false,
			'default' => null,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		],
		'data' => [
			'type' => 'text',
			'null' => true,
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
