<?php
App::uses('AppCakeTestCase', 'CakeSearchInfo.Test');
App::uses('Search', 'CakeSearchInfo.Model');
require_once App::pluginPath('CakeSearchInfo') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * Search Test Case
 */
class SearchTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_search_info.cities',
		'plugin.cake_search_info.pcodes'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeSearchInfo.Search');
	}

/**
 * testGetQueryConfigDeep0 method
 *
 * @return void
 */
	public function testGetQueryConfigDeep0() {
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$targetModels = [
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
		];
		$params = [
			[
				null, // $target
			],
			[
				'test', // $target
			],
			[
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City'
				], // $target
			],
			[
				'BadModel', // $target
			],
		];
		$expected = [
			[
				'anyPart' => true,
				'modelConfig' => $targetModels,
			],
			[
				'anyPart' => false,
				'modelConfig' => []
			],
			[
				'anyPart' => true,
				'modelConfig' => array_intersect_key($targetModels, ['City' => null])
			],
			[
				'anyPart' => false,
				'modelConfig' => array_intersect_key($targetModels, ['BadModel' => null])
			],
		];
		$this->runClassMethodGroup('getQueryConfig', $params, $expected);
	}

/**
 * testGetAnyPartFlag method
 *
 * @return void
 */
	public function testGetAnyPartFlag() {
		$params = [
			[
				'', // $target
			],
			[
				'test.id', // $target
			],
			[
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.name',
					'City.City.zip'
				], // $target
			],
			[
				'BadModel.Name', // $target
			],
		];
		$expected = [
			true,
			false,
			true,
			false,
		];
		$this->runClassMethodGroup('getAnyPartFlag', $params, $expected);
	}

/**
 * testGetQueryStr method
 *
 * @return void
 */
	public function testGetQueryStr() {
		$params = [
			[
				'', // $queryData
			],
			[
				' some data   ', // $queryData
			],
			[
				'test', // $queryData
			],
		];
		$expected = [
			'',
			'some data',
			'test',
		];
		$this->runClassMethodGroup('getQueryStr', $params, $expected);
	}

/**
 * testGetQueryConfigDeep1 method
 *
 * @return void
 */
	public function testGetQueryConfigDeep1() {
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$targetModels = [
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
		];
		$params = [
			[
				null, // $target
			],
			[
				'test.id', // $target
			],
			[
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.name',
					'City.City.zip'
				], // $target
			],
			[
				'BadModel.Name', // $target
			],
		];
		$expected = [
			[
				'anyPart' => true,
				'modelConfig' => $targetModels,
			],
			[
				'anyPart' => false,
				'modelConfig' => []
			],
			[
				'anyPart' => true,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code'
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					]
				],
			],
			[
				'anyPart' => false,
				'modelConfig' => []
			],
		];
		$this->runClassMethodGroup('getQueryConfig', $params, $expected);
	}

/**
 * testGetTargetFieldsList0 method
 *
 * @return void
 */
	public function testGetTargetFieldsList0() {
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$result = $this->_targetObject->getTargetFieldsList();
		$expected = [
			'City',
			'Pcode',
			'CityPcode'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetFieldsList1 method
 *
 * @return void
 */
	public function testGetTargetFieldsList1() {
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$result = $this->_targetObject->getTargetFieldsList();
		$expected = [
			'City.City.name',
			'City.City.zip',
			'City.City.description',
			'City.City.virt_zip_name',
			'Pcode.Pcode.code',
			'CityPcode.CityPcode.name',
			'CityPcode.CityPcode.zip',
			'CityPcode.CityPcode.description',
			'CityPcode.Pcode.code'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetFields0 method
 *
 * @return void
 */
	public function testGetTargetFields0() {
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$result = $this->_targetObject->getTargetFields();
		$expected = [
			'Citys' => 'City',
			'Telephone code' => 'Pcode',
			'Citys with code' => 'CityPcode'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetFields1 method
 *
 * @return void
 */
	public function testGetTargetFields1() {
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$result = $this->_targetObject->getTargetFields();
		$expected = [
			'Citys' => [
				'City.City.name' => 'City name',
				'City.City.zip' => 'ZIP code',
				'City.City.description' => 'Description of city',
				'City.City.virt_zip_name' => 'ZIP code with city name',
			],
			'Telephone code' => [
				'Pcode.Pcode.code' => 'Telephone code'
			],
			'Citys with code' => [
				'CityPcode.CityPcode.name' => 'City name',
				'CityPcode.CityPcode.zip' => 'ZIP code',
				'CityPcode.CityPcode.description' => 'Description of city',
				'CityPcode.Pcode.code' => 'Telephone code'
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testPaginateEng0 method
 *
 * @return void
 */
	public function testPaginateEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$params = [
			[
				null, // $conditions
				null, // $fields
				null, // $order
				null, // $limit
				null, // $page
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City',
						'Pcode',
						'BadModel'
					],
				], // $conditions
				null, // $fields
				null, // $order
				10, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						'City',
						'Pcode',
						'BadModel'
					],
				], // $conditions
				null, // $fields
				null, // $order
				10, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '375',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'Pcode',
						'City',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '375',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'Pcode',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				2, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			false,
			[
				'City' => [
					'amount' => 3,
					'data' => [
						[
							'City' => [
								'id' => '4',
								'name' => 'Брест',
								'zip' => '224000',
								'population' => '340141',
								'description' => 'Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.',
								'virt_zip_name' => '224000 Брест',
							]
						],
						[
							'City' => [
								'id' => '3',
								'name' => 'Витебск',
								'zip' => '210000',
								'population' => '376226',
								'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.',
								'virt_zip_name' => '210000 Витебск',
							]
						],
						[
							'City' => [
								'id' => '2',
								'name' => 'Гродно',
								'zip' => '230000',
								'population' => '365610',
								'description' => 'Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.',
								'virt_zip_name' => '230000 Гродно',
							]
						]
					]
				],
				'count' => 3,
				'total' => 3,
			],
			[],
			[
				'City' => [
					'amount' => 1,
					'data' => [
						[
							'City' => [
								'id' => '3',
								'name' => 'Витебск',
								'zip' => '210000',
								'population' => '376226',
								'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.',
								'virt_zip_name' => '210000 Витебск',
							]
						]
					]
				],
				'Pcode' => [
					'amount' => 4,
					'data' => [
						[
							'Pcode' => [
								'id' => '2',
								'code' => '+375 152'
							]
						],
					]
				],
				'count' => 2,
				'total' => 5,
			],
			[
				'Pcode' => [
					'amount' => 4,
					'data' => [
						[
							'Pcode' => [
								'id' => '1',
								'code' => '+375 17'
							]
						],
						[
							'Pcode' => [
								'id' => '3',
								'code' => '+375 212'
							]
						]
					]
				],
				'count' => 2,
				'total' => 4,
			]
		];
		$this->runClassMethodGroup('paginate', $params, $expected);
	}

/**
 * testPaginateEng1 method
 *
 * @return void
 */
	public function testPaginateEng1() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$params = [
			[
				null, // $conditions
				null, // $fields
				null, // $order
				null, // $limit
				null, // $page
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City.City.test.description',
						'City.City.population',
						'City.City.test',
						'Pcode.Pcode.code',
						'BadModel.BadModel.Name'
					],
				], // $conditions
				null, // $fields
				null, // $order
				10, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City.City.description',
						'City.City.population',
						'City.City.test',
						'Pcode.Pcode.code',
						'BadModel.BadModel.Name'
					],
				], // $conditions
				null, // $fields
				null, // $order
				10, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '+375',
					'target' => [
						'City.City.description',
						'City.City.population',
						'Pcode.Pcode.code',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '210',
					'target' => [
						'City.City.virt_zip_name',
						'Pcode.Pcode.code',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			false,
			[],
			[
				'City' => [
					'amount' => 3,
					'data' => [
						[
							'City' => [
								'id' => '4',
								'description' => 'Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.',
								'population' => '340141',
								'name' => 'Брест',
								'zip' => '224000',
								'virt_zip_name' => '224000 Брест',
							]
						],
						[
							'City' => [
								'id' => '3',
								'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.',
								'population' => '376226',
								'name' => 'Витебск',
								'zip' => '210000',
								'virt_zip_name' => '210000 Витебск',
							]
						],
						[
							'City' => [
								'id' => '2',
								'description' => 'Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.',
								'population' => '365610',
								'name' => 'Гродно',
								'zip' => '230000',
								'virt_zip_name' => '230000 Гродно',
							]
						]
					]
				],
				'count' => 3,
				'total' => 3
			],
			[
				'Pcode' => [
					'amount' => 4,
					'data' => [
						[
							'Pcode' => [
								'id' => '2',
								'code' => '+375 152',
							]
						],
						[
							'Pcode' => [
								'id' => '4',
								'code' => '+375 162',
							]
						]
					]
				],
				'count' => 2,
				'total' => 4
			],
			[
				'City' => [
					'amount' => 1,
					'data' => [
						[
							'City' => [
								'id' => '3',
								'name' => 'Витебск',
								'zip' => '210000',
								'population' => '376226',
								'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.',
								'virt_zip_name' => '210000 Витебск',
							]
						]
					]
				],
				'count' => 1,
				'total' => 1
			],
		];
		$this->runClassMethodGroup('paginate', $params, $expected);
	}

/**
 * testPaginateContainEng0 method
 *
 * @return void
 */
	public function testPaginateContainEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.recursive', -1);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.contain', ['Pcode']);
		$params = [
			[
				null, // $conditions
				null, // $fields
				null, // $order
				null, // $limit
				null, // $page
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => '375 1',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'CityPcode',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				2, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			false,
			[
				'CityPcode' => [
					'amount' => 3,
					'data' => [
						[
							'CityPcode' => [
								'id' => '1',
								'name' => 'Минск',
								'zip' => '220000',
								'population' => '1964200',
								'description' => 'Минск (белор. Мінск) — столица Республики Беларусь, административный центр Минской области и Минского района, в состав которых не входит, поскольку является самостоятельной административно-территориальной единицей с особым (столичным) статусом.',
							],
							'Pcode' => [
								'code' => '+375 17',
								'id' => '1',
							]
						]
					]
				],
				'count' => 1,
				'total' => 3
			]
		];
		$this->runClassMethodGroup('paginate', $params, $expected);
	}

/**
 * testPaginateContainIncludeFieldsEng0 method
 *
 * @return void
 */
	public function testPaginateContainIncludeFieldsEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.recursive', -1);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.contain', ['Pcode']);
		Configure::write('CakeSearchInfo.IncludeFields', ['CityPcode' => ['CityPcode.virt_zip_name']]);
		$params = [
			[
				null, // $conditions
				null, // $fields
				null, // $order
				null, // $limit
				null, // $page
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => '375 2',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'CityPcode',
					],
				], // $conditions
				null, // $fields
				null, // $order
				2, // $limit
				1, // $page
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			false,
			[
				'CityPcode' => [
					'amount' => 1,
					'data' => [
						[
							'CityPcode' => [
								'id' => '3',
								'name' => 'Витебск',
								'zip' => '210000',
								'population' => '376226',
								'description' => 'Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.',
								'virt_zip_name' => '210000 Витебск',
							],
							'Pcode' => [
								'code' => '+375 212',
								'id' => '3',
							]
						]
					]
				],
				'count' => 1,
				'total' => 1
			]
		];
		$this->runClassMethodGroup('paginate', $params, $expected);
	}

/**
 * testPaginateCountEng0 method
 *
 * @return void
 */
	public function testPaginateCountEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$params = [
			[
				null, // $conditions
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City',
						'Pcode',
						'BadModel'
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						'City',
						'Pcode',
						'BadModel'
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '375',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'Pcode',
						'City',
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '375',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'Pcode',
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			0,
			3,
			0,
			5,
			4
		];
		$this->runClassMethodGroup('paginateCount', $params, $expected);
	}

/**
 * testPaginateCountEng1 method
 *
 * @return void
 */
	public function testPaginateCountEng1() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$params = [
			[
				null, // $conditions
				null, // $recursive
				null, // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City.City.test.description',
						'City.City.population',
						'City.City.test',
						'Pcode.Pcode.code',
						'BadModel.BadModel.Name'
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => 'Город',
					'target' => [
						CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
						'City.City.description',
						'City.City.population',
						'City.City.test',
						'Pcode.Pcode.code',
						'BadModel.BadModel.Name'
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				[
					'query' => '+375',
					'target' => [
						'City.City.description',
						'City.City.population',
						'Pcode.Pcode.code',
					],
				], // $conditions
				- 1, // $recursive
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
		];
		$expected = [
			0,
			0,
			3,
			4,
		];
		$this->runClassMethodGroup('paginateCount', $params, $expected);
	}

/**
 * testGetAutocompleteEnd0 method
 *
 * @return void
 */
	public function testGetAutocompleteEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		$querySearchMinLength = (int)Configure::read('CakeSearchInfo.QuerySearchMinLength');
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$params = [
			[
				null, // $query
				null, // $target
				null, // $limit
			],
			[
				mb_substr('Город', 0, $querySearchMinLength - 1), // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'Pcode',
					'BadModel'
				], // $target
				0, // $limit
			],
			[
				'Город', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'Pcode',
					'BadModel'
				], // $target
				0, // $limit
			],
			[
				'город', // $query
				[
					'City',
					'Pcode',
					'BadModel'
				], // $target
				0, // $limit
			],
			[
				'375', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'Pcode',
					'City',
				], // $target
				2, // $limit
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				'+375', // $query
				[
					'Pcode',
					'City',
				], // $target
				0, // $limit
			],
			[
				'230', // $query
				[
					'City',
				], // $target
				0, // $limit
			],
		];
		$expected = [
			[],
			[],
			[
				CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[],
			[
				CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 17', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 212', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[
				CakeText::truncate('230000', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('230000 Гродно', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			]
		];
		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testGetAutocompleteEng1 method
 *
 * @return void
 */
	public function testGetAutocompleteEng1() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$params = [
			[
				null, // $query
				null, // $target
				null, // $limit
			],
			[
				'Город', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.description',
					'City.City.test.description',
					'Pcode.Pcode.code',
					'BadModel.BadModel.Name'
				], // $target
				0, // $limit
			],
			[
				'Город', // $query
				[
					'City.City.description',
					'Pcode.Pcode.code',
					'BadModel.BadModel.Name'
				], // $target
				0, // $limit
			],
			[
				'375', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'Pcode.Pcode.code',
					'City.City.description',
				], // $target
				2, // $limit
				[
					'maxLimit' => 100,
					'paramType' => 'named'
				], // $extra
			],
			[
				'+375', // $query
				[
					'Pcode.Pcode.code',
					'City.City.description',
				], // $target
				0, // $limit
			],
			[
				'+375', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'Pcode.Pcode.code',
					'City',
				], // $target
				0, // $limit
			],
			[
				'вит', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.virt_zip_name',
				], // $target
				0, // $limit
			],
		];
		$expected = [
			[],
			[
				CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[],
			[
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 17', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 212', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 17', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 212', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
			[
				CakeText::truncate('210000 Витебск', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
		];
		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testGetAutocompleteRusCorrect1 method
 *
 * @return void
 */
	public function testGetAutocompleteRusCorrect1() {
		$this->skipIf(version_compare(PHP_VERSION, '7.3.0', '>='), 'Skipped for PHP 7.3 or higher');

		Configure::write('Config.language', 'rus');
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$params = [
			[
				null, // $query
				null, // $target
				null, // $limit
			],
			[
				'ujhjl', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.description',
					'City.City.test.description',
					'Pcode.Pcode.code',
					'BadModel.BadModel.Name'
				], // $target
				0, // $limit
			],
		];
		$expected = [
			[],
			[
				CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			],
		];
		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testGetAutocompleteEngWoCorrect1 method
 *
 * @return void
 */
	public function testGetAutocompleteEngWoCorrect1() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 1);
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$params = [
			[
				null, // $query
				null, // $target
				null, // $limit
			],
			[
				'ujhjl', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City.City.description',
					'City.City.test.description',
					'Pcode.Pcode.code',
					'BadModel.BadModel.Name'
				], // $target
				0, // $limit
			],
		];
		$expected = [
			[],
			[],
		];
		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testGetAutocompleteContainEng0 method
 *
 * @return void
 */
	public function testGetAutocompleteContainEng0() {
		Configure::write('Config.language', 'eng');
		Configure::write('CakeSearchInfo.TargetDeep', 0);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.recursive', -1);
		Configure::write('CakeSearchInfo.TargetModels.CityPcode.contain', ['Pcode']);
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$params = [
			[
				null, // $query
				null, // $target
				null, // $limit
			],
			[
				'375 1', // $query
				[
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'CityPcode',
					'BadModel'
				], // $target
				0, // $limit
			],
		];
		$expected = [
			[],
			[
				CakeText::truncate('+375 152', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 162', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
				CakeText::truncate('+375 17', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			]
		];
		$this->runClassMethodGroup('getAutocomplete', $params, $expected);
	}

/**
 * testGetPluginName method
 *
 * @return void
 */
	public function testGetPluginName() {
		$result = $this->_targetObject->getPluginName();
		$expected = 'cake_search_info';
		$this->assertData($expected, $result);
	}

/**
 * testGetControllerName method
 *
 * @return void
 */
	public function testGetControllerName() {
		$result = $this->_targetObject->getControllerName();
		$expected = 'search';
		$this->assertData($expected, $result);
	}

/**
 * testGetGroupName method
 *
 * @return void
 */
	public function testGetGroupName() {
		$result = $this->_targetObject->getGroupName();
		$expected = __d('cake_search_info', 'Search information');
		$this->assertData($expected, $result);
	}
}
