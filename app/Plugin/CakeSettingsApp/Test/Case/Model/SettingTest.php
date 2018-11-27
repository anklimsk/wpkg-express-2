<?php
App::uses('AppCakeTestCase', 'CakeSettingsApp.Test');
App::uses('Folder', 'Utility');

/**
 * SettingTest Test Case
 *
 */
class SettingTest extends AppCakeTestCase {

/**
 * Path to application configuration file.
 *
 * @var string
 */
	protected $_pathAppCfg = TMP . 'tests' . DS . 'Config' . DS;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_settings_app.ldap',
		'plugin.cake_settings_app.queued_task',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$oFolder = new Folder($this->_pathAppCfg, true);
		$oFolder->create($this->_pathAppCfg);

		$this->_targetObject = ClassRegistry::init('Setting');
		$this->_targetObject->pathAppCfg = $this->_pathAppCfg;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$Folder = new Folder($this->_pathAppCfg);
		$Folder->delete();

		parent::tearDown();
	}

/**
 * testCreateValidationRules method
 *
 * @return void
 */
	public function testCreateValidationRules() {
		$result = $this->_targetObject->createValidationRules();
		$this->assertTrue($result);

		$validator = $this->_targetObject->validator();
		$result = $validator->count();
		$expected = 15;
		$this->assertData($expected, $result);

		Configure::write('CakeSettingsApp.configSMTP', false);
		Configure::write('CakeSettingsApp.configAcLimit', false);
		Configure::write('CakeSettingsApp.configADsearch', false);
		Configure::write('CakeSettingsApp.configExtAuth', false);
		Configure::write('CakeSettingsApp.authGroups', [
			CAKE_SETTINGS_APP_TEST_USER_ROLE_ADMIN => [
				'field' => 'AdminGroupMember',
				'name' => 'administrator',
				'prefix' => 'admin'
			]
		]);
		$result = $this->_targetObject->createValidationRules();
		$this->assertTrue($result);

		$validator = $this->_targetObject->validator();
		$result = $validator->count();
		$expected = 6;
		$this->assertData($expected, $result);
	}

/**
 * testSaveSucess method
 *
 * @return void
 */
	public function testSaveSucess() {
		$data = $this->_targetObject->getConfig();
		$data[$this->_targetObject->alias]['EmailNotifyUser'] = false;
		$data[$this->_targetObject->alias]['EmailSmtphost'] = '';
		$data[$this->_targetObject->alias]['EmailSmtpport'] = '25';
		$data[$this->_targetObject->alias]['EmailSmtppassword_confirm'] = $data[$this->_targetObject->alias]['EmailSmtppassword'];
		$data[$this->_targetObject->alias]['CountryCode'] = 'RU';
		$data[$this->_targetObject->alias]['ReadOnlyFields'] = [
			'objectguid',
			'dn'
		];
		$result = $this->_targetObject->save($data);
		$this->assertData($data, $result);
		if (!$result) {
			return;
		}

		$result = $this->_targetObject->getConfig();
		unset($data[$this->_targetObject->alias]['EmailSmtppassword_confirm']);
		$this->assertEquals($data, $result);

		$this->assertTrue($this->_targetObject->beforeSaveState);
		$this->assertTrue($this->_targetObject->afterSaveState);
	}

/**
 * testGetVars method
 *
 * @return void
 */
	public function testGetVars() {
		$result = $this->_targetObject->getVars();
		$expected = [
			'countries' => [
				'BY' => 'Belarus',
				'RU' => 'Russia',
				'US' => 'United States'
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetConfig method
 *
 * @return void
 */
	public function testGetConfig() {
		$result = $this->_targetObject->getConfig('Company');
		$expected = 'Test ORG';
		$this->assertData($expected, $result);
		$this->assertTrue($this->_targetObject->afterFindState);
	}
}
