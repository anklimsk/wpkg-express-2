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

$config['CakeSearchInfo'] = [
	'QuerySearchMinLength' => 3,
	'AutocompleteLimit' => 2,
	'TargetDeep' => 0,
	'DefaultSearchAnyPart' => true,
	'TargetModels' => [
		'City' => [
			'fields' => [
				'City.name' => 'City name',
				'City.zip' => 'ZIP code',
				'City.population' => 'Population of city',
				'City.description' => 'Description of city',
				'City.virt_zip_name' => 'ZIP code with city name',
			],
			'order' => ['City.name' => 'asc'],
			'name' => 'Citys'
		],
		'Pcode' => [
			'fields' => [
				'Pcode.code' => 'Telephone code',
			],
			'order' => ['Pcode.code' => 'asc'],
			'name' => 'Telephone code'
		],
		'BadModel' => [
			'fields' => [
				'BadModel.id' => 'ID',
				'BadModel.Name' => 'Name',
			],
			'order' => ['BadModel.name' => 'asc'],
			'name' => 'Bad Model'
		],
		'CityPcode' => [
			'fields' => [
				'CityPcode.name' => 'City name',
				'CityPcode.zip' => 'ZIP code',
				'CityPcode.population' => 'Population of city',
				'CityPcode.description' => 'Description of city',
				'Pcode.code' => 'Telephone code',
			],
			'order' => ['CityPcode.name' => 'asc'],
			'name' => 'Citys with code',
			'recursive' => 0,
		],
	],
	'IncludeFields' => [],
];
