<?php
/**
 * This file configures installer
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Config
 */

$config['CakeInstaller'] = [
	// Version of PHP for check
	'PHPversion' => [
		[
			// Version
			'5.4.0',
			// Operator
			'>='
		],
	],
	// PHP Extension for check
	'PHPextensions' => [
		[
			// Extension name
			'pdo',
			// Critical need
			true
		],
		[
			// Extension name
			'ldap',
			// Critical need
			true
		],
		[
			// Extension name
			'bz2',
			// Critical need
			true
		],
		[
			// Extension name
			'xml',
			// Critical need
			true
		],
		[
			// Extension name
			'openssl',
			// Critical need
			true
		],
/*
		[
			// Extension name
			'runkit',
			// Critical need
			false
		],
*/
	],
	// Commands for installer
	'installerCommands' => [
		// Set Installer UI language
		'setuilang',
		// Checking PHP environment
		'check',
		// Set file system permissions on the temporary directory
		'setdirpermiss',
		// Set security key
		'setsecurkey',
		// Set timezone
		'settimezone',
		// Set base URL
		'setbaseurl',
		// Configure database connections
		'configdb',
		// Check connect to database
		'connectdb',
		// Create database and initialize data
		'createdb',
		// Create symlinks to files
		'createsymlinks',
		// Create cron jobs
		'createcronjobs',
		// Install this application
		'install',
	],
	// Tasks for action - install
	'installTasks' => [
		// Set Installer UI language
		'setuilang',
		// Checking PHP environment
		'check',
		// Set file system permissions on the temporary directory
		'setdirpermiss',
		// Set security key
		'setsecurkey',
		// Set timezone
		'settimezone',
		// Set base URL
		'setbaseurl',
		// Configure database connections
		'configdb',
		// Check connect to database
		'connectdb',
		// Create database and initialize data
		'createdb',
		// Create symlinks to files
		'createsymlinks',
		// Create cron jobs
		'createcronjobs',
	],
	// List of database connection for configure
	'configDBconn' => [
		// Main connection for application
		'default',
		// LDAP connection for application
		'ldap',
		// Test connection for application
		//'test',
	],
	'customConnections' => [
		'ldap' => [
			'datasource' => [
				'value' => 'CakeLdap.LdapExtSource',
			],
			'persistent' => [],
			'host' => [
				'defaultValue' => '',
				'label' => __d('cake_installer_label', 'LDAP host'),
			],
			'port' => [
				'defaultValue' => 389,
			],
			'login' => [
				'defaultValue' => '',
				'label' => __("LDAP Bind RDN: RDN (cn=user,ou=users,dc=fabrikam,dc=com) for OpenLDAP or User principal name (user@fabrikam.com) for ActiveDirectory"),
				'validationPattern' => '/(^([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*(,([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*)*$)|(\w{2,}\@\w{2,}\.\w{2,})/i',
			],
			'password' => [
				'alowEmpty' => false,
			],
			'database' => [
				'value' => '',
			],
			'basedn' => [
				'label' => __d('cake_installer_label', 'The DN of the search base (DC=fabrikam,DC=com)'),
				'validationPattern' => '/^([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*(,([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*)*$/i',
			],
			'type' => [
				'defaultValue' => 'ActiveDirectory',
				'label' => __d('cake_installer_label', 'LDAP server type'),
				'options' => ['ActiveDirectory', 'OpenLDAP', 'Netscape'],
			],
			'tls' => [
				'defaultValue' => 'n',
				'label' => __d('cake_installer_label', 'Use TLS?'),
				'options' => ['n' => false, 'y' => true],
			],
			'version' => [
				'defaultValue' => 3,
				'label' => __d('cake_installer_label', 'Version of LDAP protocol'),
				'options' => [2, 3],
			],
		],
	],
	// List of additional schemes for creation, as
	// console command `cake schema create` options
	'schemaCreationList' => [
		'sessions',
		'-p Queue',
	],
	// List of additional schemes for checking exists in
	// database
	'schemaCheckingList' => [
		'sessions',
		'-p Queue',
	],
	// List of symlinks for creation in format:
	// key - link; value - target.
	'symlinksCreationList' => [
		APP . 'webroot' . DS . 'cake_theme' => APP . 'Plugin' . DS . 'CakeTheme' . DS . 'webroot',
		APP . 'webroot' . DS . 'cake_installer' => APP . 'Plugin' . DS . 'CakeInstaller' . DS . 'webroot',
	],
	// List of cron job for creation in format:
	// key - command; value - start time.
	'cronJobs' => [
		'cd ' . APP . ' && Console/cake Queue.Queue runworker -q' => '*/10 * * * *',
		'cd ' . APP . ' && Console/cake cron parse_logs -q' => '*/15 * * * *',
		'cd ' . APP . ' && Console/cake cron parse_databases -q' => '0 */2 * * *',
		'cd ' . APP . ' && Console/cake cron disable_unused -q' => '0 6 * * *',
	],
	// List of languages for installer UI in format: ISO639-2
	'UIlangList' => [
		'eng',
		'rus',
	]
];
