<?php
/**
 * Tree Fixture
 */
class TreeFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => false],
		'type' => ['type' => 'integer', 'null' => false],
		'parent_id' => ['type' => 'integer', 'null' => true],
		'lft' => ['type' => 'integer', 'null' => false],
		'rght' => ['type' => 'integer', 'null' => false],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			'id_UNIQUE' => ['column' => 'id', 'unique' => 1],
			'parent_id' => ['column' => 'parent_id', 'unique' => 0],
			'lft' => ['column' => 'lft', 'unique' => 0],
			'rght' => ['column' => 'rght', 'unique' => 0],
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
			'name' => 'root - 1 - 1',
			'type' => '1',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '2',
		],
		[
			'id' => '2',
			'name' => 'root - 2 - 1',
			'type' => '1',
			'parent_id' => null,
			'lft' => '3',
			'rght' => '12',
		],
		[
			'id' => '3',
			'name' => 'level - 2.1 - 1',
			'type' => '1',
			'parent_id' => '2',
			'lft' => '4',
			'rght' => '5',
		],
		[
			'id' => '4',
			'name' => 'level - 2.2 - 1',
			'type' => '1',
			'parent_id' => '2',
			'lft' => '6',
			'rght' => '7',
		],
		[
			'id' => '5',
			'name' => 'level - 2.3 - 1',
			'type' => '1',
			'parent_id' => '2',
			'lft' => '8',
			'rght' => '9',
		],
		[
			'id' => '6',
			'name' => 'level - 2.4 - 1',
			'type' => '1',
			'parent_id' => '2',
			'lft' => '10',
			'rght' => '11',
		],
		[
			'id' => '7',
			'name' => 'root - 1 - 2',
			'type' => '2',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '6',
		],
		[
			'id' => '8',
			'name' => 'level - 2.1 - 2',
			'type' => '2',
			'parent_id' => '7',
			'lft' => '2',
			'rght' => '3',
		],
		[
			'id' => '9',
			'name' => 'level - 2.2 - 2',
			'type' => '2',
			'parent_id' => '7',
			'lft' => '4',
			'rght' => '5',
		],
	];
}
