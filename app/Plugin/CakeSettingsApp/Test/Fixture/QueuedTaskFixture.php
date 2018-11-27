<?php
/**
 * QueuedTask Fixture
 */
class QueuedTaskFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'],
		'jobtype' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 45, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'data' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'group' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'reference' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'notbefore' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'fetched' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'progress' => ['type' => 'float', 'null' => true, 'default' => null, 'length' => '3,2', 'unsigned' => false],
		'completed' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'failed' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => false],
		'failure_message' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'workerkey' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	];

/**
 * Records
 *
 * @var array
 */
	public $records = [
		[
			'id' => '1',
			'jobtype' => 'Sync',
			'data' => 'N;',
			'group' => 'sync',
			'reference' => null,
			'created' => '2016-09-26 08:27:17',
			'notbefore' => null,
			'fetched' => '2016-09-26 08:28:01',
			'progress' => '1.00',
			'completed' => '2016-09-26 08:28:05',
			'failed' => '0',
			'failure_message' => null,
			'workerkey' => '09adcca25a14ae45c9c22cbbdaaa64eadf568bc1'
		],
		[
			'id' => '2',
			'jobtype' => 'Sync',
			'data' => 'N;',
			'group' => 'sync',
			'reference' => null,
			'created' => '2016-09-26 08:28:15',
			'notbefore' => null,
			'fetched' => null,
			'progress' => null,
			'completed' => null,
			'failed' => '0',
			'failure_message' => null,
			'workerkey' => null
		],
	];
}
