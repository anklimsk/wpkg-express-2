<?php
/**
 * This file configures settings application
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Config
 */

$config['CakeSettingsApp'] = [
	'configKey' => PROJECT_CONFIG_NAME,
	'configSMTP' => true,
	'configAcLimit' => true,
	'configADsearch' => false,
	'configExtAuth' => true,
	'authGroups' => [
		USER_ROLE_ADMIN => [
			'field' => 'AdminGroupMember',
			'name' => __('administrator'),
			'prefix' => 'admin'
		]
	],
	'UIlangs' => [
		'US' => 'eng',
		'RU' => 'rus',
	],
	'schema' => [
		'IntAuthUser' => ['type' => 'string', 'default' => ''],
		'IntAuthPassword' => ['type' => 'string', 'default' => ''],
		'DefaultSearchAnyPart' => ['type' => 'boolean', 'default' => false],
		'FormatXml' => ['type' => 'boolean', 'default' => ''],
		'ExportDisable' => ['type' => 'boolean', 'default' => ''],
		'ExportNotes' => ['type' => 'boolean', 'default' => ''],
		'ExportDisabled' => ['type' => 'boolean', 'default' => ''],
		'ProtectXml' => ['type' => 'boolean', 'default' => ''],
		'XmlAuthUser' => ['type' => 'string', 'default' => ''],
		'XmlAuthPassword' => ['type' => 'string', 'default' => ''],
		'AutoVarRevision' => ['type' => 'boolean', 'default' => ''],
		'SmbAuthUser' => ['type' => 'string', 'default' => ''],
		'SmbAuthPassword' => ['type' => 'string', 'default' => ''],
		'SmbWorkgroup' => ['type' => 'string', 'default' => ''],
		'SmbServer' => ['type' => 'string', 'default' => ''],
		'SmbLogShare' => ['type' => 'string', 'default' => ''],
		'SmbDbShare' => ['type' => 'string', 'default' => ''],
		'SearchBaseUser' => ['type' => 'string', 'default' => ''],
		'SearchBaseComp' => ['type' => 'string', 'default' => ''],
	],
	'serialize' => [
	],
	'alias' => [
		'AutocompleteLimit' => [
			'CakeSearchInfo.AutocompleteLimit',
			'CakeTheme.ViewExtension.AutocompleteLimit',
		],
		'SearchBaseUser' => [
			'CakeLdap.LdapSync.SearchBase'
		],
		'EmailContact' => [
			'Config.adminEmail'
		],
		'EmailNotifyUser' => [
			'Email.live'
		],
		'DefaultSearchAnyPart' => [
			'CakeSearchInfo.DefaultSearchAnyPart'
		]
	]
];
