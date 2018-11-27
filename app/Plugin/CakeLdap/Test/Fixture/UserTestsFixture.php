<?php
/**
 * UserTestsFixture
 *
 */
class UserTestsFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'user_tests';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'],
		'guid' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
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
			'guid' => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf',
			'name' => 'Моисеева Л.Б.',
		],
		[
			'id' => '2',
			'guid' => '81817f32-44a7-4b4a-8eff-b837ba387077',
			'name' => 'Белова Н.М.',
		],
		[
			'id' => '3',
			'guid' => 'b3ec524a-69d0-4fce-b9c2-3b59956cfa25',
			'name' => 'Кириллов А.М.',
		]
	];
}
