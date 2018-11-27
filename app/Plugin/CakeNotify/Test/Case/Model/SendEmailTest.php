<?php
App::uses('AppCakeTestCase', 'CakeNotify.Test');
App::uses('SendEmail', 'CakeNotify.Model');

/**
 * SendEmail Test Case
 */
class SendEmailTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.queue.queued_task'
	];

	public $mailConfig = [
		'from' => ['some@example.com' => 'My website'],
		'to' => ['test@example.com' => 'Testname'],
		'subject' => 'Test mail subject',
		'transport' => 'Debug',
		'helpers' => ['Html', 'Form'],
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$testView = CakePlugin::path('CakeNotify') . 'Test' . DS . 'test_app' . DS . 'View' . DS;
		App::build(['View' => [$testView]]);
		parent::setUp();
		$config = [
			'default' => [
				'host' => 'localhost',
				'port' => 25,
				'username' => '',
			],
			'live' => false,
			'useSmtp' => false,
			'config' => 'default',
		];
		Configure::write('Email', $config);

		$this->_targetObject = ClassRegistry::init('CakeNotify.SendEmail');
	}

/**
 * testPutQueueEmail method
 *
 * @return void
 */
	public function testGetDomain() {
		Configure::delete('App.fullBaseUrl');
		$result = $this->_targetObject->getDomain();
		$expected = 'localhost';
		$this->assertData($expected, $result);

		Configure::write('App.fullBaseUrl', 'http://test.local');
		$result = $this->_targetObject->getDomain();
		$expected = 'test.local';
		$this->assertData($expected, $result);
	}

/**
 * testPutQueueEmail method
 *
 * @return void
 */
	public function testPutQueueEmail() {
		$params = [
			[
				null // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'template' => ['test_mail', 'CakeNotify.test'],
				] // $data
			],
		];
		$expected = [
			false,
			false,
			true
		];

		$this->runClassMethodGroup('putQueueEmail', $params, $expected);
	}

/**
 * testSendEmailNow method
 *
 * @return void
 */
	public function testSendEmailNow() {
		$params = [
			[
				[
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'subject' => 'Test Mail',
					'template' => ['test_mail', 'CakeNotify.test'],
					'resultType' => 'headers',
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'template' => ['test_mail', 'CakeNotify.test'],
					'resultType' => 'headers'
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'template' => ['test_mail', 'CakeNotify.test'],
					'resultType' => 'message',
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'template' => ['test_mail', 'CakeNotify.test'],
					'emailFormat' => 'text',
					'resultType' => 'message',
				] // $data
			],
			[
				[
					'config' => $this->mailConfig,
					'to' => 'test@localhost.local',
					'template' => ['test_mail', 'CakeNotify.test'],
					'emailFormat' => 'html',
					'resultType' => 'message'
				] // $data
			],
		];
		$expected = [
			false,
			false,
			[
				'expecteds' => [
					'assertRegExp' => '/Subject\:\sTest Mail/',
					'assertRegExp' => '/To\:\stest@localhost\.local/',
				]
			],
			['assertRegExp' => '/Subject\:\sreport@/'],
			[
				'expecteds' => [
					'assertRegExp' => '/Test message as plain text/',
					'assertRegExp' => '/<p[^>]*>Test message as HTML<\/p>/',
				]
			],
			[
				'expecteds' => [
					'assertRegExp' => '/Test message as plain text/',
				]
			],
			[
				'expecteds' => [
					'assertRegExp' => '/<p[^>]*>Test message as HTML<\/p>/',
				]
			]
		];

		$this->runClassMethodGroup('sendEmailNow', $params, $expected);
	}
}
