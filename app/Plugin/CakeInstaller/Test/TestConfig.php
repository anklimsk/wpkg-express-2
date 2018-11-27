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

$config['CakeInstaller'] = [
	'PHPversion' => [
		[
			PHP_VERSION,
			'>='
		],
	],
	'PHPextensions' => [
		[
			'PDO',
			true
		],
	],
	'installerCommands' => [
		'setuilang',
		'check',
		'setdirpermiss',
		'setsecurkey',
		'settimezone',
		'setbaseurl',
		'configdb',
		'createdb',
		'createsymlinks',
		'install',
	],
	'installTasks' => [
		'setuilang',
		'check',
		'setdirpermiss',
		'setsecurkey',
		'settimezone',
		'setbaseurl',
		'configdb',
	],
	'configDBconn' => [
		'default',
		'test',
	],
	'symlinksCreationList' => [
		TMP . 'tests' . DS . 'link.php' => TMP . 'tests' . DS . 'Config' . DS . 'core.php'
	],
	'cronJobs' => [
	],
	'UIlangList' => [
		'eng',
		'rus',
	]
];
