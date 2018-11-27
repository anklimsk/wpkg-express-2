<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('Filter', 'CakeTheme.Model');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * Filter Test Case
 */
class FilterTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_theme.employees',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeTheme.Filter');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * testGetAutocomplete method
 *
 * @return void
 */
	public function testGetAutocomplete() {
		$params = [
			[
				'', // $query
				'', // $type
				null, // $plugin
				0, // $limit
			],
			[
				['Бир'], // $query
				'EmployeeTest.last_name', // $type
				null, // $plugin
				0, // $limit
			],
			[
				'Бир', // $query
				['EmployeeTest.last_name'], // $type
				null, // $plugin
				0, // $limit
			],
			[
				'инж', // $query
				'test', // $type
				null, // $plugin
				0, // $limit
			],
			[
				'инж', // $query
				'EmployeeTest.test', // $type
				null, // $plugin
				0, // $limit
			],
			[
				'инж', // $query
				'EmployeeTest.photo', // $type
				null, // $plugin
				0, // $limit
			],
			[
				'инж', // $query
				'EmployeeTest.position.test', // $type
				null, // $plugin
				'test', // $limit
			],
			[
				'инж', // $query
				'EmployeeTest.position', // $type
				null, // $plugin
				'test', // $limit
			],
			[
				'ал', // $query
				'EmployeeTest.first_name', // $type
				null, // $plugin
				1, // $limit
			],
			[
				'кос', // $query
				'EmployeeTest.name', // $type
				null, // $plugin
				0, // $limit
			],
		];
		$expected = [
			[],
			[],
			[],
			[],
			[],
			[],
			[],
			[
				'Инженер 1 категории',
				'Инженер-электроник 1 категории'
			],
			[
				'Александр'
			],
			[
				'Костин Дмитрий Иванович'
			],
		];

		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testBuildConditions method
 *
 * @return void
 */
	public function testBuildConditions() {
		$params = [
			[
				[], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				['EmployeeTest' => 'test'], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					'badIndex' => [
						'EmployeeTest' => 'test'
					],
				], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					'1' => [
						'EmployeeTest' => 'test'
					],
				], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					2 => [
						'EmployeeTest' => [
							'last_name' => 'Test'
						]
					],
				], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					[
						'EmployeeTest' => [
							'name' => 'Test'
						]
					],
				], // $filterData
				[], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					3 => [
						'EmployeeTest' => [
							'birthday' => '1981-01-15'
						]
					],
				], // $filterData
				[
					3 => [
						'EmployeeTest' => [
							'birthday' => 'le'
						]
					],
				], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					[
						'EmployeeTest' => [
							'position' => 'инж'
						]
					],
					[
						'EmployeeTest' => [
							'birthday' => '1975-05-12'
						]
					],
				], // $filterData
				[
					'group' => 'or',
					1 => [
						'EmployeeTest' => [
							'birthday' => 'ne'
						]
					],
				], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					[
						'EmployeeTest' => [
							'manager' => '1'
						]
					],
					[
						'EmployeeTest' => [
							'block' => '0'
						]
					],
					[
						'EmployeeTest' => [
							'birthday' => 'bad date'
						]
					],
				], // $filterData
				[
					'group' => 'bad',
					[
						'EmployeeTest' => [
							'manager' => 'lt'
						]
					],
					[
						'EmployeeTest' => [
							'birthday' => 'badCond'
						]
					],
				], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					[
						'EmployeeTest' => [
							'mail' => 'some text'
						]
					],
				], // $filterData
				[
					'group' => 'AND',
					[
						'EmployeeTest' => [
							'mail' => 'ne'
						]
					],
				], // $filterConditions
				null, // $plugin
				0, // $limit
			],
			[
				[
					2 => [
						'EmployeeTest' => [
							'position' => 'some text'
						]
					],
					4 => [
						'EmployeeTest' => [
							'position' => 'pos'
						]
					],
					9 => [
						'EmployeeTest' => [
							'position' => 'man'
						]
					],
				], // $filterData
				[
					'group' => 'or',
				], // $filterConditions
				null, // $plugin
				2, // $limit
			],
		];
		$expected = [
			[],
			[],
			[],
			[],
			[
				'LOWER(EmployeeTest.last_name) like' => '%test%'
			],
			[
				'EmployeeTest.name like' => '%Test%'
			],
			[
				'EmployeeTest.birthday <=' => '1981-01-15'
			],
			[
				'OR' => [
					[
						'LOWER(EmployeeTest.position) like' => '%инж%'
					],
					[
						'EmployeeTest.birthday <>' => '1975-05-12'
					]
				]
			],
			[
				'AND' => [
					[
						'EmployeeTest.manager' => '1'
					],
					[
						'EmployeeTest.block' => '0'
					],
					[
						'EmployeeTest.birthday like' => '%bad date%'
					]
				]
			],
			[
				'LOWER(EmployeeTest.mail) like' => '%some text%'
			],
			[
				'OR' => [
					[
						'LOWER(EmployeeTest.position) like' => '%some text%'
					],
					[
						'LOWER(EmployeeTest.position) like' => '%pos%'
					]
				]
			],
		];

		$this->runClassMethodGroup('buildConditions', $params, $expected);
	}

/**
 * testGetCondition method
 *
 * @return void
 */
	public function testGetCondition() {
		$params = [
			[
				'', // $field
				'', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'test', // $field
				'инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.test', // $field
				'инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				['EmployeeTest.position.test'], // $field
				'инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				['EmployeeTest.position'], // $field
				'инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.photo', // $field
				'инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.position', // $field
				['инж', 'зав'], // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.position', // $field
				['инж'], // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.position', // $field
				'Инж', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.position', // $field
				'Инж', // $data
				'', // $conditionSign
				null, // $plugin
				true, // $isAutocomplete
			],
			[
				'EmployeeTest.manager', // $field
				'1', // $data
				'', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.manager', // $field
				'1', // $data
				'', // $conditionSign
				null, // $plugin
				true, // $isAutocomplete
			],
			[
				'EmployeeTest.id', // $field
				'3', // $data
				'ge', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.birthday', // $field
				'1983-01-01', // $data
				'gt', // $conditionSign
				null, // $plugin
				true, // $isAutocomplete
			],
			[
				'EmployeeTest.birthday', // $field
				'1983-01-01', // $data
				'bad', // $conditionSign
				null, // $plugin
				true, // $isAutocomplete
			],
			[
				'EmployeeTest.manager', // $field
				'1', // $data
				'gt', // $conditionSign
				null, // $plugin
				false, // $isAutocomplete
			],
			[
				'EmployeeTest.first_name', // $field
				'бАл', // $data
				'ne', // $conditionSign
				null, // $plugin
				true, // $isAutocomplete
			],
		];
		$expected = [
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			[
				'LOWER(EmployeeTest.position) like' => '%инж%'
			],
			[
				'LOWER(EmployeeTest.position) like' => '%инж%'
			],
			[
				'LOWER(EmployeeTest.position) like' => 'инж%'
			],
			[
				'EmployeeTest.manager' => '1'
			],
			[
				'EmployeeTest.manager' => '1'
			],
			[
				'EmployeeTest.id >=' => '3'
			],
			[
				'EmployeeTest.birthday >' => '1983-01-01'
			],
			[
				'EmployeeTest.birthday' => '1983-01-01'
			],
			[
				'EmployeeTest.manager' => '1'
			],
			[
				'LOWER(EmployeeTest.first_name) like' => 'бал%'
			]
		];

		$this->runClassMethodGroup('getCondition', $params, $expected);
	}
}
