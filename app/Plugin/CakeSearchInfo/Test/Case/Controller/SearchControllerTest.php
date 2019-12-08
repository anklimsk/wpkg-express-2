<?php
App::uses('AppControllerTestCase', 'CakeSearchInfo.Test');
App::uses('Controller', 'Controller');
App::uses('SearchController', 'CakeSearchInfo.Controller');
App::uses('CakeText', 'Utility');
require_once App::pluginPath('CakeSearchInfo') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * SearchController Test Case
 */
class SearchControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeSearchInfo.Search';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_search_info.cities',
		'plugin.cake_search_info.pcodes'
	];

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
		Configure::write('Config.language', 'eng');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/cake_search_info/search/index', $opt);
		$expected = [
			'search_urlActionSearch' => null,
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'New search')
			],
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode',
			],
			'search_targetFieldsSelected' => [
				'City',
				'Pcode',
				'CityPcode',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearch method
 *
 * @return void
 */
	public function testSearch() {
		Configure::write('Config.language', 'eng');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/cake_search_info/search/search', $opt);
		$expected = [
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'Results of search')
			],
			'query' => '',
			'queryCorrect' => '',
			'queryConfig' => [
				'anyPart' => true,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code',
							'City.population' => 'Population of city',
							'City.description' => 'Description of city',
							'City.virt_zip_name' => 'ZIP code with city name',
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					],
					'Pcode' => [
						'fields' => [
							'Pcode.code' => 'Telephone code'
						],
						'order' => [
							'Pcode.code' => 'asc'
						],
						'name' => 'Telephone code'
					],
					'CityPcode' => [
						'fields' => [
							'CityPcode.name' => 'City name',
							'CityPcode.zip' => 'ZIP code',
							'CityPcode.population' => 'Population of city',
							'CityPcode.description' => 'Description of city',
							'Pcode.code' => 'Telephone code'
						],
						'order' => [
							'CityPcode.name' => 'asc'
						],
						'name' => 'Citys with code',
						'recursive' => 0
					]
				]
			],
			'result' => false,
			'target' => [
				'City',
				'Pcode',
				'CityPcode',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'correct' => false,
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
				'Pcode',
				'CityPcode',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearchGetInvalidRequest method
 *
 * @return void
 */
	public function testSearchGetInvalidRequest() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'data' => [
				'query' => 'город',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'BadModel'
				]
			]
		];
		$this->setExpectedException('InternalErrorException');
		$this->testAction('/cake_search_info/search/search', $opt);
	}

/**
 * testSearchGetValidRequest method
 *
 * @return void
 */
	public function testSearchGetValidRequest() {
		Configure::write('Config.language', 'eng');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
			'data' => [
				'query' => 'город',
				'target' => [
					'City',
					'BadModel'
				]
			]
		];
		$result = $this->testAction('/cake_search_info/search/search', $opt);
		$expected = [
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'Results of search')
			],
			'query' => 'город',
			'queryCorrect' => '',
			'queryConfig' => [
				'anyPart' => false,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code',
							'City.population' => 'Population of city',
							'City.description' => 'Description of city',
							'City.virt_zip_name' => 'ZIP code with city name',
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					],
				]
			],
			'result' => [],
			'target' => [
				'City'
			],
			'correct' => false,
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearchGetValidRequestOrder method
 *
 * @return void
 */
	public function testSearchGetValidRequestOrder() {
		Configure::write('Config.language', 'eng');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
			'data' => [
				'query' => 'город',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$result = $this->testAction('/cake_search_info/search/search/sort:City.name/direction:desc', $opt);
		$expected = [
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'Results of search')
			],
			'query' => 'город',
			'queryCorrect' => '',
			'queryConfig' => [
				'anyPart' => true,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code',
							'City.population' => 'Population of city',
							'City.description' => 'Description of city',
							'City.virt_zip_name' => 'ZIP code with city name',
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					],
				]
			],
			'result' => [
				'City' => [
					'amount' => 3,
					'data' => [
						[
							'City' => [
								'id' => '2',
								'name' => 'Гродно',
								'zip' => '230000',
								'population' => '365610',
								'description' => 'Гро́дно (белор. Гродна, польск. Grodno, лит. Gardinas, рус. дореф. Гродна[13]) — город в Белоруссии, административный центр Гродненской области[14], а также Гродненского района, в состав которого город не входит.',
								'virt_zip_name' => '230000 Гродно',
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
								'id' => '4',
								'name' => 'Брест',
								'zip' => '224000',
								'population' => '340141',
								'description' => 'Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.',
								'virt_zip_name' => '224000 Брест',
							]
						],
					]
				],
				'count' => 3,
				'total' => 3
			],
			'target' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'correct' => false,
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearchGetValidRequestCorrectFalse method
 *
 * @return void
 */
	public function testSearchGetValidRequestRusCorrectFalse() {
		$this->skipIf(version_compare(PHP_VERSION, '7.3.0', '>='), 'Skipped for PHP 7.3 or higher');

		Configure::write('Config.language', 'rus');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
			'data' => [
				'query' => 'ujhjl',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$result = $this->testAction('/cake_search_info/search/search', $opt);
		$expected = [
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'Results of search')
			],
			'query' => 'ujhjl',
			'queryCorrect' => 'город',
			'queryConfig' => [
				'anyPart' => true,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code',
							'City.population' => 'Population of city',
							'City.description' => 'Description of city',
							'City.virt_zip_name' => 'ZIP code with city name',
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					],
				]
			],
			'result' => [
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
				'total' => 3
			],
			'target' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'correct' => false,
			'uiLcid2' => 'ru',
			'uiLcid3' => 'rus',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearchGetValidRequestCorrectTrue method
 *
 * @return void
 */
	public function testSearchGetValidRequestEngCorrectTrue() {
		Configure::write('Config.language', 'eng');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
			'data' => [
				'query' => 'ujhjl',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				],
				'correct' => true
			]
		];
		$result = $this->testAction('/cake_search_info/search/search', $opt);
		$expected = [
			'pageTitle' => __d('cake_search_info', 'Search information'),
			'breadCrumbs' => [
				[
					__d('cake_search_info', 'Search information'),
					[
						'plugin' => 'cake_search_info',
						'controller' => 'search',
						'action' => 'index'
					]
				],
				__d('cake_search_info', 'Results of search')
			],
			'query' => 'ujhjl',
			'queryCorrect' => '',
			'queryConfig' => [
				'anyPart' => true,
				'modelConfig' => [
					'City' => [
						'fields' => [
							'City.name' => 'City name',
							'City.zip' => 'ZIP code',
							'City.population' => 'Population of city',
							'City.description' => 'Description of city',
							'City.virt_zip_name' => 'ZIP code with city name',
						],
						'order' => [
							'City.name' => 'asc'
						],
						'name' => 'Citys'
					],
				]
			],
			'result' => [],
			'target' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'correct' => true,
			'uiLcid2' => 'en',
			'uiLcid3' => 'eng',
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testSearchGetEmptyRequest method
 *
 * @return void
 */
	public function testSearchGetEmptyRequest() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'data' => [
				'query' => '',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$this->testAction('/cake_search_info/search/search', $opt);
		$this->checkFlashMessage(__d('cake_search_info', 'Enter your query in the search bar'));
	}

/**
 * testSearchGetValidRequest method
 *
 * @return void
 */
	public function testSearchGetValidRequestMinChars() {
		$querySearchMinLength = (int)Configure::read('CakeSearchInfo.QuerySearchMinLength');
		$this->_generateMockedController();
		$opt = [
			'method' => 'GET',
			'data' => [
				'query' => mb_substr('город', 0, $querySearchMinLength - 1),
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$this->testAction('/cake_search_info/search/search', $opt);
		$this->checkFlashMessage(__d(
			'cake_search_info',
			'Input minimum %d %s',
			$querySearchMinLength,
			__dn('cake_search_info', 'character', 'characters', $querySearchMinLength)
		));
	}

/**
 * testAutocompletePostInvalidRequest method
 *
 * @return void
 */
	public function testAutocompletePostInvalidRequest() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
		];
		$this->setAjaxRequest();
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_search_info/search/autocomplete', $opt);
	}

/**
 * testAutocompleteGetInvalidRequest method
 *
 * @return void
 */
	public function testAutocompleteGetInvalidRequest() {
		$opt = [
			'method' => 'GET',
		];
		$this->setAjaxRequest();
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_search_info/search/autocomplete.json', $opt);
	}

/**
 * testAutocompletePostNotAjaxInvalidRequest method
 *
 * @return void
 */
	public function testAutocompletePostNotAjaxInvalidRequest() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_search_info/search/autocomplete.json', $opt);
	}

/**
 * testAutocompletePostInvalidTarget method
 *
 * @return void
 */
	public function testAutocompletePostInvalidTarget() {
		$opt = [
			'method' => 'POST',
			'return' => 'contents',
			'data' => [
				'query' => 'го',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$this->setAjaxRequest();
		$result = $this->testAction('/cake_search_info/search/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testAutocompleteValidRequest method
 *
 * @return void
 */
	public function testAutocompleteValidRequest() {
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
			'return' => 'contents',
			'data' => [
				'query' => 'горо',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$this->setAjaxRequest();
		$result = $this->testAction('/cake_search_info/search/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt)
		];
		$this->assertData($expected, $result);
	}

/**
 * testAutocompleteValidRequestTextCorrect method
 *
 * @return void
 */
	public function testAutocompleteValidRequestTextCorrect() {
		$this->skipIf(version_compare(PHP_VERSION, '7.3.0', '>='), 'Skipped for PHP 7.3 or higher');

		Configure::write('Config.language', 'rus');
		$this->_generateMockedController();
		$opt = [
			'method' => 'POST',
			'return' => 'contents',
			'data' => [
				'query' => 'ujhjl',
				'target' => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
					'City',
					'BadModel'
				]
			]
		];
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$this->setAjaxRequest();
		$result = $this->testAction('/cake_search_info/search/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt)
		];
		$this->assertData($expected, $result);
	}

/**
 * Generate mocked SearchController.
 *
 * @return bool Success
 */
	protected function _generateMockedController() {
		$mocks = [
			'components' => [
				'Auth',
				'Security',
			],
		];
		if (!$this->generateMockedController($mocks)) {
			return false;
		}

		return true;
	}
}
