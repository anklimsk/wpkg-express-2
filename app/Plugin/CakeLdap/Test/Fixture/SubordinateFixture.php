<?php
/**
 * Subordinate Fixture
 */
App::uses('CakeTestFixture', 'TestSuite/Fixture');

class SubordinateFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
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
			'parent_id' => null,
			'lft' => '1',
			'rght' => '2',
			'name' => 'Миронов В.М.'
		],
		[
			'id' => '2',
			'parent_id' => '3',
			'lft' => '4',
			'rght' => '5',
			'name' => 'Егоров Т.Г.'
		],
		[
			'id' => '3',
			'parent_id' => null,
			'lft' => '3',
			'rght' => '6',
			'name' => 'Суханова Л.Б.'
		],
		[
			'id' => '4',
			'parent_id' => '8',
			'lft' => '10',
			'rght' => '15',
			'name' => 'Дементьева А.С.'
		],
		[
			'id' => '5',
			'parent_id' => null,
			'lft' => '7',
			'rght' => '8',
			'name' => 'Матвеев Р.М.'
		],
		[
			'id' => '6',
			'parent_id' => '7',
			'lft' => '12',
			'rght' => '13',
			'name' => 'Козловская Е.М.'
		],
		[
			'id' => '7',
			'parent_id' => '4',
			'lft' => '11',
			'rght' => '14',
			'name' => 'Хвощинский В.В.'
		],
		[
			'id' => '8',
			'parent_id' => null,
			'lft' => '9',
			'rght' => '16',
			'name' => 'Голубев Е.В.'
		],
		[
			'id' => '9',
			'parent_id' => null,
			'lft' => '19',
			'rght' => '20',
			'name' => 'Марчук А.М.'
		],
		[
			'id' => '10',
			'parent_id' => null,
			'lft' => '17',
			'rght' => '18',
			'name' => 'Чижов Я.С.'
		],
	];

/**
 * Truncates the current fixture. Can be overwritten by classes extending
 * CakeFixture to trigger other events before / after truncate.
 *
 * @param DboSource $db A reference to a db instance
 * @return bool
 */
	public function truncate($db) {
		$existsTables = $db->listSources();
		if (!in_array($this->table, $existsTables)) {
			return true;
		}

		$fullDebug = $db->fullDebug;
		$db->fullDebug = false;
		$return = $db->truncate($this->table);
		$db->fullDebug = $fullDebug;

		return $return;
	}
}
