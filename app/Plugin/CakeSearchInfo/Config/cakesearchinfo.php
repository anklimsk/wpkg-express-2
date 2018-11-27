<?php
/**
 * This file configures search info
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

$config['CakeSearchInfo'] = [
	'QuerySearchMinLength' => 0,
	'AutocompleteLimit' => 10,
	'TargetDeep' => 0,
	'DefaultSearchAnyPart' => true,
	'TargetModels' => [
/*
		'ModelName' => [
			'fields' => [
				'ModelName.FieldName' => __('Field name'),
				'ModelName.FieldName2' => __('Field name 2'),
			],
			'order' => ['ModelName.FieldName' => 'direction'],
			'name' => __('Scope name'),
			'recursive' => 0, // not necessary - default: -1
			'contain' => null, // not necessary - default: null
			'conditions' => ['ModelName.FieldName' => 'SomeValue'], // not necessary - used as global conditions
			'url' => [
				'controller' => 'modelnames',
				'action' => 'view',
				'plugin' => 'pluginname',
			],  // not necessary - used in link to result
			'id' => 'ModelName.id', // not necessary - used in link to result
		],
*/
	],
	'IncludeFields' => [
/*
		'ModelName' => [
			'ModelName.FieldName',
			'ModelName.FieldName2',
		]
*/
	],
];
