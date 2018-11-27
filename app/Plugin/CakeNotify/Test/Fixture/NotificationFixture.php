<?php
/**
 * Notification Fixture
 */
class NotificationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => false],
		'user_role' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 1, 'unsigned' => false],
		'expires' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'tag' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'body' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'data' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
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
			'user_id' => null,
			'user_role' => null,
			'expires' => '+1 day',
			'tag' => null,
			'title' => 'Test data',
			'body' => 'Test WO Tag',
			'data' => 'a:1:{s:3:"url";a:3:{s:10:"controller";s:5:"trips";s:6:"action";s:6:"latest";s:6:"plugin";N;}}'
		],
		[
			'id' => '2',
			'user_id' => '1',
			'user_role' => null,
			'expires' => '+1 day',
			'tag' => 'TestTag',
			'title' => 'Test',
			'body' => 'Test WO data for user',
			'data' => null
		],
		[
			'id' => '3',
			'user_id' => '5',
			'user_role' => null,
			'expires' => '+1 day',
			'tag' => 'TestTag',
			'title' => 'Test',
			'body' => 'Test WO data for invalid user',
			'data' => null
		],
		[
			'id' => '4',
			'user_id' => null,
			'user_role' => 'init_admin',
			'expires' => '+1 day',
			'tag' => 'new_job',
			'title' => 'New job',
			'body' => 'Received a new job. For role.',
			'data' => 'a:2:{s:3:"url";a:3:{s:10:"controller";s:4:"jobs";s:6:"action";s:6:"latest";s:6:"plugin";N;}s:4:"icon";s:18:"/img/cake-icon.png";}'
		],
		[
			'id' => '5',
			'user_id' => null,
			'user_role' => '1000',
			'expires' => '+1 day',
			'tag' => 'new_job',
			'title' => 'New job',
			'body' => 'Received a new job. For invalid role.',
			'data' => null
		],
		[
			'id' => '6',
			'user_id' => null,
			'user_role' => null,
			'expires' => '-1 day',
			'tag' => 'test',
			'title' => 'Test expired',
			'body' => 'Test for exired',
			'data' => null
		],
	];

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		foreach ($this->records as $i => &$record) {
			if ($record['user_role'] === 'init_admin') {
				$record['user_role'] = LDAP_AUTH_TEST_USER_ROLE_ADMIN;
			}

			$record['expires'] = date('Y-m-d', strtotime($record['expires']));
		}
		parent::init();
	}
}
