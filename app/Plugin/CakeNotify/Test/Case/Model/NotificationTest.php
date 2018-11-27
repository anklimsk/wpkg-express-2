<?php
App::uses('AppCakeTestCase', 'CakeNotify.Test');
App::uses('Notification', 'CakeNotify.Model');

/**
 * Notification Test Case
 */
class NotificationTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_notify.notification'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeNotify.Notification');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testCreateNotification method
 *
 * @return void
 */
	public function testCreateNotification() {
		$params = [
			[
				null, // $tag
				null, // $title
				null, // $body
				null, // $extendInfo
			], // Params for step 1
			[
				'TestTag', // $tag
				null, // $title
				null, // $body
				null, // $extendInfo
			], // Params for step 2
			[
				'TestTag', // $tag
				'Title text', // $title
				null, // $body
				null, // $extendInfo
			], // Params for step 3
			[
				'Tag', // $tag
				'Title', // $title
				'Some text..', // $body
				null, // $extendInfo
			], // Params for step 4
			[
				'T1', // $tag
				'Test', // $title
				'Text...', // $body
				[
					'data' => [
						'url' => '/home',
						'user_id' => 5,
						'user_role' => LDAP_AUTH_TEST_USER_ROLE_EXTENDED,
					]
				], // $extendInfo
			], // Params for step 5
		];
		$expected = [
			false, // Result of step 1
			false, // Result of step 2
			false, // Result of step 3
			true, // Result of step 4
			true, // Result of step 5
		];
		$this->runClassMethodGroup('createNotification', $params, $expected);

		$result = $this->_targetObject->getNotifications(6);
		$expected = [
			[
				'Notification' => [
					'id' => '7',
					'user_id' => null,
					'user_role' => null,
					'tag' => 'Tag',
					'title' => 'Title',
					'body' => 'Some text..',
					'data' => null,
				]
			],
			[
				'Notification' => [
					'id' => '8',
					'user_id' => null,
					'user_role' => null,
					'tag' => 'T1',
					'title' => 'Test',
					'body' => 'Text...',
					'data' => [
						'url' => '/home',
						'user_id' => 5,
						'user_role' => LDAP_AUTH_TEST_USER_ROLE_EXTENDED
					]
				]
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetNotifications method
 *
 * @return void
 */
	public function testGetNotifications() {
		$params = [
			[
				null, // $id
				null, // $userId
				null, // $userRole
			], // Params for step 1
			[
				null, // $id
				1, // $userId
				null, // $userRole
			], // Params for step 2
			[
				null, // $id
				5, // $userId
				LDAP_AUTH_TEST_USER_ROLE_ADMIN, // $userRole
			], // Params for step 3
			[
				3, // $id
				5, // $userId
				LDAP_AUTH_TEST_USER_ROLE_ADMIN, // $userRole
			], // Params for step 3
		];
		$expected = [
			[
				[
					'Notification' => [
						'id' => '1',
						'user_id' => null,
						'user_role' => null,
						'tag' => null,
						'title' => 'Test data',
						'body' => 'Test WO Tag',
						'data' => [
							'url' => [
								'controller' => 'trips',
								'action' => 'latest',
								'plugin' => null,
							]
						]
					]
				]
			], // Result of step 1
			[
				[
					'Notification' => [
						'id' => '1',
						'user_id' => null,
						'user_role' => null,
						'tag' => null,
						'title' => 'Test data',
						'body' => 'Test WO Tag',
						'data' => [
							'url' => [
								'controller' => 'trips',
								'action' => 'latest',
								'plugin' => null
							]
						]
					]
				],
				[
					'Notification' => [
						'id' => '2',
						'user_id' => '1',
						'user_role' => null,
						'tag' => 'TestTag',
						'title' => 'Test',
						'body' => 'Test WO data for user',
						'data' => null,
					]
				]
			], // Result of step 2
			[
				[
					'Notification' => [
						'id' => '1',
						'user_id' => null,
						'user_role' => null,
						'tag' => null,
						'title' => 'Test data',
						'body' => 'Test WO Tag',
						'data' => [
							'url' => [
								'controller' => 'trips',
								'action' => 'latest',
								'plugin' => null
							]
						]
					]
				],
				[
					'Notification' => [
						'id' => '3',
						'user_id' => '5',
						'user_role' => null,
						'tag' => 'TestTag',
						'title' => 'Test',
						'body' => 'Test WO data for invalid user',
						'data' => null,
					]
				],
				[
					'Notification' => [
						'id' => '4',
						'user_id' => null,
						'user_role' => (string)LDAP_AUTH_TEST_USER_ROLE_ADMIN,
						'tag' => 'new_job',
						'title' => 'New job',
						'body' => 'Received a new job. For role.',
						'data' => [
							'url' => [
								'controller' => 'jobs',
								'action' => 'latest',
								'plugin' => null
							],
							'icon' => '/img/cake-icon.png',
						]
					]
				]
			], // Result of step 3
			[
				[
					'Notification' => [
						'id' => '4',
						'user_id' => null,
						'user_role' => (string)LDAP_AUTH_TEST_USER_ROLE_ADMIN,
						'tag' => 'new_job',
						'title' => 'New job',
						'body' => 'Received a new job. For role.',
						'data' => [
							'url' => [
								'controller' => 'jobs',
								'action' => 'latest',
								'plugin' => null
							],
							'icon' => '/img/cake-icon.png',
						],
					]
				]
			], // Result of step 4
		];
		$this->runClassMethodGroup('getNotifications', $params, $expected);
	}

/**
 * testClearNotifications method
 *
 * @return void
 */
	public function testClearNotifications() {
		$id = 6;
		$result = $this->_targetObject->exists($id);
		$this->assertTrue($result);

		$result = $this->_targetObject->clearNotifications();
		$this->assertTrue($result);

		$result = $this->_targetObject->exists($id);
		$this->assertFalse($result);
	}
}
