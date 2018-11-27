<?php
App::uses('AppControllerTestCase', 'CakeSettingsApp.Test');
App::uses('SettingsController', 'CakeSettingsApp.Controller');
App::uses('Model', 'Model');

/**
 * SettingsController Test Case
 *
 */
class SettingsControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeSettingsApp.Settings';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_settings_app.ldap'
	];

/**
 * testIndex method
 *
 * Method: GET
 *
 * @return void
 */
	public function testIndexGet() {
		$this->_generateMockedController();

		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/cake_settings_app/settings/index', $opt);
		$expected = [
			'groupList' => [
				'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Admin',
				'CN=Web.Extend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Extend',
			],
			'containerList' => [
				'OU=Группы,DC=fabrikam,DC=com',
				'OU=Компьютеры,DC=fabrikam,DC=com'
			],
			'configUIlangs' => true,
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
			'languages' => [
				'US' => 'English',
				'RU' => 'Русский язык'
			],
			'varsExt' => [
				'countries' => [
					'BY' => 'Belarus',
					'RU' => 'Russia',
					'US' => 'United States'
				]
			],
			'pageHeader' => __d('cake_settings_app', 'Application settings'),
			'breadCrumbs' => [
				[
					__d('cake_settings_app', 'Application settings'),
					[
						'plugin' => 'cake_settings_app',
						'controller' => 'settings',
						'action' => 'index'
					],
				],
				__d('cake_settings_app', 'Settings')
			],
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng'
		];
		$this->assertData($expected, $result);
	}

/**
 * testIndex method
 *
 * Method: POST
 * Save: success
 *
 * @return void
 */
	public function testIndexSuccessPost() {
		$this->_generateMockedController(true);
		$opt = [
			'data' => [
				'Setting' => [
					'EmailContact' => 'adm@fabrikam.com',
					'EmailSubject' => 'Test msg',
					'Company' => 'Test ORG',
					'SearchBase' => '',
					'AutocompleteLimit' => '5',
					'ExternalAuth' => false,
					'AdminGroupMember' => 'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
					'ManagerGroupMember' => 'CN=Web.Manager,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
					'EmailSmtphost' => 'localhost',
					'EmailSmtpport' => '25',
					'EmailSmtpuser' => 'usr',
					'EmailSmtppassword' => 'test',
					'EmailSmtppassword_confirm' => 'test',
					'EmailNotifyUser' => true,
					'Language' => 'US',
					'CountryCode' => 'RU'
				],
			],
			'method' => 'POST',
		];
		$this->testAction('/cake_settings_app/settings/index', $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'Application settings has been saved.'));
		$this->checkRedirect('/settings');
	}

/**
 * testIndex method
 *
 * Method: POST
 * Save: Unsuccess
 *
 * @return void
 */
	public function testIndexUnsuccessPostMsg() {
		$this->_generateMockedController(false);

		$opt = [
			'data' => [
				'Setting' => [
					'EmailContact' => 'adm@fabrikam.com',
					'EmailSubject' => 'Test msg',
					'Company' => 'Test ORG',
					'SearchBase' => '',
					'AutocompleteLimit' => '5',
					'ExternalAuth' => false,
					'AdminGroupMember' => 'TEST',
					'ManagerGroupMember' => 'CN=Web.Manager,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com',
					'EmailSmtphost' => 'localhost',
					'EmailSmtpport' => '25',
					'EmailSmtpuser' => 'usr',
					'EmailSmtppassword' => 'test',
					'EmailSmtppassword_confirm' => 'test',
					'EmailNotifyUser' => true,
					'Language' => 'eng'
				],
			],
			'method' => 'POST',
		];
		$result = $this->testAction('/cake_settings_app/settings/index', $opt);
		$this->checkFlashMessage(__d('cake_settings_app', 'Unable to save application settings.'));
	}

/**
 * Generate mocked SettingsController.
 *
 * @param mixed $saveResult Result of call Setting::save().
 * @return bool Success
 */
	protected function _generateMockedController($saveResult = true) {
		$mocks = [
			'components' => [
				'Security',
				'Auth',
			],
			'models' => [
				'CakeSettingsApp.Setting' => ['save'],
				'CakeSettingsApp.Ldap' => [
					'getGroupList',
					'getTopLevelContainerList'
				],
			],
		];
		if (!$this->generateMockedController($mocks)) {
			return false;
		}

		$this->Controller->Setting->expects($this->any())
			->method('save')
			->will($this->returnValue($saveResult));
		$this->Controller->Ldap->expects($this->any())
			->method('getGroupList')
			->will($this->returnValue([
				'CN=Web.Admin,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Admin',
				'CN=Web.Extend,OU=Web,OU=Soft,OU=Группы,DC=fabrikam,DC=com' => 'Web.Extend'
			]));
		$this->Controller->Ldap->expects($this->any())
			->method('getTopLevelContainerList')
			->will($this->returnValue([
				'OU=Группы,DC=fabrikam,DC=com',
				'OU=Компьютеры,DC=fabrikam,DC=com'
			]));

		return true;
	}
}
