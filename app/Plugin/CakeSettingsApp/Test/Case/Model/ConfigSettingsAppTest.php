<?php
App::uses('AppCakeTestCase', 'CakeSettingsApp.Test');
App::uses('ConfigSettingsApp', 'CakeSettingsApp.Model');

/**
 * ConfigSettingsApp Test Case
 */
class ConfigSettingsAppTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [

	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeSettingsApp.ConfigSettingsApp');
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
 * testGetConfig method
 *
 * @return void
 */
	public function testGetConfig() {
		$params = [
			[
				null, // $key
			], // Params for step 1
			[
				'configSMTP', // $key
			], // Params for step 2
			[
				'BAD_KEY', // $key
			], // Params for step 3
		];
		$expected = [
			[
				'configKey' => 'test_cfg',
				'configSMTP' => true,
				'configAcLimit' => true,
				'configADsearch' => true,
				'configExtAuth' => true,
				'authGroups' => [
					CAKE_SETTINGS_APP_TEST_USER_ROLE_EXTENDED => [
						'field' => 'ManagerGroupMember',
						'name' => 'manager',
						'prefix' => 'manager'
					],
					CAKE_SETTINGS_APP_TEST_USER_ROLE_ADMIN => [
						'field' => 'AdminGroupMember',
						'name' => 'administrator',
						'prefix' => 'admin'
					]
				],
				'UIlangs' => [
					'US' => 'eng',
					'RU' => 'rus',
				],
				'schema' => [
					'CountryCode' => ['type' => 'string', 'default' => 'BY'],
					'ReadOnlyFields' => ['type' => 'string', 'default' => ''],
				],
				'serialize' => [
					'ReadOnlyFields',
				],
				'alias' => [
					'AutocompleteLimit' => [
						'ExtConfig.AC',
					]
				],
			], // Result of step 1
			true, // Result of step 2
			null, // Result of step 3
		];
		$this->runClassMethodGroup('getConfig', $params, $expected);
	}

/**
 * testGetNameConfigKey method
 *
 * @return void
 */
	public function testGetNameConfigKey() {
		$result = $this->_targetObject->getNameConfigKey();
		$expected = 'test_cfg';
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagConfigSmtp method
 *
 * @return void
 */
	public function testGetFlagConfigSmtp() {
		$result = $this->_targetObject->getFlagConfigSmtp();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagConfigAcLimit method
 *
 * @return void
 */
	public function testGetFlagConfigAcLimit() {
		$result = $this->_targetObject->getFlagConfigAcLimit();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagConfigExtAuth method
 *
 * @return void
 */
	public function testGetFlagConfigExtAuth() {
		$result = $this->_targetObject->getFlagConfigExtAuth();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagConfigUiLangs method
 *
 * @return void
 */
	public function testGetFlagConfigUiLangs() {
		$result = $this->_targetObject->getFlagConfigUiLangs();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testGetAuthGroups method
 *
 * @return void
 */
	public function testGetAuthGroups() {
		$result = $this->_targetObject->getAuthGroups();
		$expected = [
			CAKE_SETTINGS_APP_TEST_USER_ROLE_EXTENDED => [
				'field' => 'ManagerGroupMember',
				'name' => 'manager',
				'prefix' => 'manager'
			],
			CAKE_SETTINGS_APP_TEST_USER_ROLE_ADMIN => [
				'field' => 'AdminGroupMember',
				'name' => 'administrator',
				'prefix' => 'admin'
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetUiLangs method
 *
 * @return void
 */
	public function testGetUiLangs() {
		$result = $this->_targetObject->getUiLangs();
		$expected = [
			'US' => 'eng',
			'RU' => 'rus',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetExtendSchemaData method
 *
 * @return void
 */
	public function testGetExtendSchemaData() {
		$result = $this->_targetObject->getExtendSchemaData();
		$expected = [
			'CountryCode' => ['type' => 'string', 'default' => 'BY'],
			'ReadOnlyFields' => ['type' => 'string', 'default' => ''],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSerializeFields method
 *
 * @return void
 */
	public function testGetListSerializeFields() {
		$result = $this->_targetObject->getListSerializeFields();
		$expected = [
			'ReadOnlyFields',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetAliasConfig method
 *
 * @return void
 */
	public function testGetAliasConfig() {
		$result = $this->_targetObject->getAliasConfig();
		$expected = [
			'AutocompleteLimit' => [
				'ExtConfig.AC',
			]
		];
		$this->assertData($expected, $result);
	}
}
