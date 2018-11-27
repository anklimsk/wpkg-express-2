<?php
/**
 * Pcodes Fixture
 */
class PcodesFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'pcodes';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'code' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
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
			'code' => '+375 17'
		],
		[
			'id' => '2',
			'code' => '+375 152'
		],
		[
			'id' => '3',
			'code' => '+375 212'
		],
		[
			'id' => '4',
			'code' => '+375 162'
		],
	];
}
