<?php
/**
 * This file configures settings application
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

$config['CakeSettingsApp'] = [
	// The application settings key. Used in `Configure::read('Key')`
	// See https://book.cakephp.org/2.0/en/development/configuration.html#Configure::read
	'configKey' => 'AppConfig',
	// Use configuration UI of SMTP
	'configSMTP' => false,
	// Use configuration UI of Autocomplete limit
	'configAcLimit' => true,
	// Use configuration UI of Search base for LDAP
	'configADsearch' => true,
	// Use configuration UI of External authentication
	'configExtAuth' => true,
/*
	// Setting users with role and prefix
	'authGroups' => [
		// User role bit mask
		1 => [
			// Name of field setting
			'field' => 'AdminGroupMember',
			// Label of field setting
			'name' => __('administrator'),
			// User role prefix
			'prefix' => 'admin'
		]
	],
*/
	// List of languages for UI in format: key - ISO 639-1, value - ISO 639-2
	'UIlangs' => [
		'US' => 'eng',
		'RU' => 'rus',
	],
/*
	// Custom settings scheme
	'schema' => [
		'FieldName' => ['type' => 'string', 'default' => ''],
	],
	// List of fields with multiple value
	'serialize' => [
		'FieldName'
	],
	// List of alias for value of setting
	'alias' => [
		'FieldName' => [
			'ConfigGroup.Key',
		]
	],
*/
];
