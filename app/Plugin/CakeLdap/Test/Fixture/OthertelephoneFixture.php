<?php
/**
 * Othertelephone Fixture
 */
App::uses('CakeTestFixture', 'TestSuite/Fixture');

class OthertelephoneFixture extends CakeTestFixture {

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
			'value' => '+375171000001'
		],
		[
			'id' => '2',
			'employee_id' => '2',
			'value' => '+375171000002'
		],
		[
			'id' => '3',
			'employee_id' => '3',
			'value' => '+375171000003'
		],
		[
			'id' => '4',
			'employee_id' => '3',
			'value' => '+375171000004'
		],
		[
			'id' => '5',
			'employee_id' => '3',
			'value' => '+375171000005'
		],
		[
			'id' => '6',
			'employee_id' => '5',
			'value' => '+375171000006'
		],
		[
			'id' => '7',
			'employee_id' => '6',
			'value' => '+375171000007'
		],
		[
			'id' => '8',
			'employee_id' => '7',
			'value' => '+375171000008'
		],
		[
			'id' => '9',
			'employee_id' => '7',
			'value' => '+375171000009'
		],
		[
			'id' => '10',
			'employee_id' => '10',
			'value' => '+375171000010'
		],
	];
}
