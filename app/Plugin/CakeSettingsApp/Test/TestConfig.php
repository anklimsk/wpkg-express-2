<?php
/**
 * This file contain configure for testing
 *
 * To modify parameters, copy this file into your own CakePHP APP/Test directory.
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Test
 */

$config['CakeSettingsApp'] = [
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
];

$config['test_cfg'] = [
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
	'EmailSmtpuser' => '',
	'EmailSmtppassword' => '',
	'EmailNotifyUser' => true,
	'CountryCode' => 'US',
	'ReadOnlyFields' => 'a:1:{i:0;s:10:"objectguid";}'
];

$config['Config'] = [
	'language' => 'eng',
];
