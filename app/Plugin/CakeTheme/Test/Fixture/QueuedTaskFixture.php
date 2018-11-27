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
			'created' => '2016-09-26 08:30:17',
			'notbefore' => null,
			'fetched' => '2016-09-26 08:31:01',
			'progress' => '0.85',
			'completed' => null,
			'failed' => '0',
			'failure_message' => 'Some text',
			'workerkey' => '09adcca25a14ae45c9c22cbbdaaa64eadf568bc1'
		],
		[
			'id' => '2',
			'jobtype' => 'Sync',
			'data' => 'N;',
			'group' => 'sync',
			'reference' => null,
			'created' => '2016-09-26 08:34:15',
			'notbefore' => null,
			'fetched' => null,
			'progress' => null,
			'completed' => null,
			'failed' => '0',
			'failure_message' => null,
			'workerkey' => null
		],
		[
			'id' => '3',
			'jobtype' => 'Build',
			'data' => 'N;',
			'group' => 'build',
			'reference' => null,
			'created' => '2016-10-21 11:17:25',
			'notbefore' => null,
			'fetched' => '2016-10-21 11:18:09',
			'progress' => '0.75',
			'completed' => null,
			'failed' => '2',
			'failure_message' => 'Error',
			'workerkey' => '09adcca25a14ae45c9c22cbbdaaa64eadf568bc1'
		],
		[
			'id' => '4',
			'jobtype' => 'Parse',
			'data' => 'N;',
			'group' => 'parse',
			'reference' => null,
			'created' => '2016-10-24 08:47:01',
			'notbefore' => null,
			'fetched' => '2016-10-24 08:47:14',
			'progress' => '1',
			'completed' => '2016-10-24 08:47:29',
			'failed' => '0',
			'failure_message' => null,
			'workerkey' => '09adcca25a14ae45c9c22cbbdaaa64eadf568bc1'
		]
	];
}
