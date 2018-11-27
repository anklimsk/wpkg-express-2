<?php
/**
 * Othermobile Fixture
 */
App::uses('CakeTestFixture', 'TestSuite/Fixture');

class OthermobileFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'employee_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'value' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			'id_UNIQUE' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

/**
 * Records
 *
 * @var array
 */
	public $records = [
		[
			'id' => '1',
			'employee_id' => '1',
			'value' => '+375291000001'
		],
		[
			'id' => '2',
			'employee_id' => '1',
			'value' => '+375291000002'
		],
		[
			'id' => '3',
			'employee_id' => '4',
			'value' => '+375291000003'
		],
		[
			'id' => '4',
			'employee_id' => '8',
			'value' => '+375291000004'
		],
	];
}
