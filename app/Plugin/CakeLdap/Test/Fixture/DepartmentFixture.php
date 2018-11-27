<?php
/**
 * Department Fixture
 */
App::uses('CakeTestFixture', 'TestSuite/Fixture');

class DepartmentFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'value' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'block' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
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
			'value' => 'УИЗ',
			'block' => 0,
		],
		[
			'id' => '2',
			'value' => 'ОС',
			'block' => 0,
		],
		[
			'id' => '3',
			'value' => 'ОИТ',
			'block' => 0
		],
		[
			'id' => '4',
			'value' => 'ОРС',
			'block' => 0
		],
		[
			'id' => '5',
			'value' => 'АТО',
			'block' => 0
		],
		[
			'id' => '6',
			'value' => 'Охрана труда',
			'block' => 0
		],
		[
			'id' => '7',
			'value' => 'СО',
			'block' => 1
		],
	];
}
