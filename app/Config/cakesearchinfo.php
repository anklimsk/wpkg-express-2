<?php
/**
 * This file configures search info
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Config
 */

$config['CakeSearchInfo'] = [
	'QuerySearchMinLength' => 2,
	'AutocompleteLimit' => 10,
	'TargetDeep' => 1,
	'DefaultSearchAnyPart' => false,
	'TargetModels' => [
		'Package' => [
			'fields' => [
				'Package.id_text' => __('Package ID'),
				'Package.name' => __('Name'),
				'Package.notes' => __('Notes'),
			],
			'order' => ['Package.id_text' => 'asc'],
			'name' => __('Packages'),
		],
		'Profile' => [
			'fields' => [
				'Profile.id_text' => __('Profile ID'),
				'Profile.notes' => __('Notes'),
			],
			'order' => ['Profile.id_text' => 'asc'],
			'name' => __('Profiles'),
		],
		'Host' => [
			'fields' => [
				'Host.id_text' => __('Host ID'),
				'Host.notes' => __('Notes'),
			],
			'order' => ['Host.id_text' => 'asc'],
			'name' => __('Hosts'),
		],
	],
];
