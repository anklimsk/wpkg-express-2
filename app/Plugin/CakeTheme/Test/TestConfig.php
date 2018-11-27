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

$config['CakeTheme'] = [
	'AdditionalFiles' => [
		'css' => [
			'extendCssFile'
		],
		'js' => [
			'someJsFile'
		],
	],
	'AjaxFlash' => [
		'flashKeys' => [
			'flash',
			'auth',
			'test'
		],
		'timeOut' => 15,
		'delayDeleteFlash' => 5,
		'globalAjaxComplete' => false,
		'theme' => 'mint',
		'layout' => 'top',
		'open' => 'animated flipInX',
		'close' => 'animated flipOutX',
	],
	'TourApp' => [
		'Steps' => [
			[
				'path' => '/',
				'element' => 'ul.nav',
				'title' => 'Title',
				'content' => 'Some text.'
			],
			[
				'element' => '#content',
				'title' => 'Content area',
				'content' => 'Content'
			],
		]
	],
	'ViewExtension' => [
		// Autocomplete limit for filter of table
		'AutocompleteLimit' => 3,
		// Server-Sent Events
		'SSE' => [
			// Default text for Noty message
			'text' => 'Waiting to run task',
			// Labels for data
			'label' => [
				// Task name
				'task' => 'Task',
				// Completed percentage
				'completed' => 'completed'
			],
			// The number of repeated attempts to start pending tasks
			'retries' => 5,
			// Delay to delete flash messages
			'delayDeleteTask' => 5
		],
		// ViewExtension Helper
		'Helper' => [
			// Default FontAwesome icon prefix
			'defaultIconPrefix' => 'fas',
			// Default FontAwesome icon size
			'defaultIconSize' => 'fa-lg',
			// Default Bootstrap button prefix
			'defaultBtnPrefix' => 'btn',
			// Default Bootstrap button size
			'defaultBtnSize' => 'btn-xs',
		],
		// PHP Unoconv
		'Unoconv' => [
			// The timeout for the underlying process.
			'timeout' => 30,
			// The path (or an array of paths) for a custom binary.
			'binaries' => ''
		]
	],
];
