<?php
App::uses('AppCakeTestCase', 'CakeSearchInfo.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSession', 'Model/Datasource');
App::uses('SearchFilterComponent', 'CakeSearchInfo.Controller/Component');
App::uses('Hash', 'Utility');
require_once App::pluginPath('CakeSearchInfo') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * SearchTestController class
 *
 */
class SearchTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * Example: `public $components = array('Session', 'RequestHandler', 'Acl');`
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
			'RequestHandler',
			'Paginator'
	];
}

/**
 * SearchFilterComponent Test Case
 */
class SearchFilterComponentTest extends AppCakeTestCase {

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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		CakeSession::destroy();
		parent::tearDown();
	}

/**
 * testStartupGetHtml method
 *
 * @return void
 */
	public function testStartupGetHtml() {
		$this->_createComponet('/test');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
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
 * testStartupGetJson method
 *
 * @return void
 */
	public function testStartupGetJson() {
		$url = '/test.json';
		$this->setJsonRequest();
		$this->_createComponet($url, 'GET', ['ext' => 'json']);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
	}

/**
 * testStartupGetValidParam method
 *
 * @return void
 */
	public function testStartupGetValidParam() {
		$data = [
			'query' => 'tst',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'CityPcode'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->Controller->viewVars;
		$expected = [
			'search_targetFields' => [
				'Citys' => 'City',
				'Telephone code' => 'Pcode',
				'Citys with code' => 'CityPcode'
			],
			'search_targetFieldsSelected' => [
				'City',
				'CityPcode',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'search_querySearchMinLength' => 3,
			'search_targetDeep' => 0,
		];
		$this->assertData($expected, $result);
	}

/**
 * testStartupGetInvalidParam method
 *
 * @return void
 */
	public function testStartupGetInvalidParam() {
		$data = [
			'target' => [
				'BadModel'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
	}

/**
 * testGetTargetListDefault method
 *
 * @return void
 */
	public function testGetTargetListDefault() {
		$this->_createComponet();
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getTargetList();
		$expected = [
			'City',
			'Pcode',
			'CityPcode',
			CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetListGetParam method
 *
 * @return void
 */
	public function testGetTargetListGetParam() {
		$data = [
			'target' => [
				'City',
				'CityPcode',
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getTargetList();
		$expected = [
			'City',
			'CityPcode'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetListGetParamAnyPart method
 *
 * @return void
 */
	public function testGetTargetListGetParamAnyPart() {
		$data = [
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getTargetList();
		$expected = [
			'City',
			CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetAnyPartFlagDefault method
 *
 * @return void
 */
	public function testGetAnyPartFlagDefault() {
		$this->_createComponet();
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getAnyPartFlag();
		$this->assertTrue($result);
	}

/**
 * testGetAnyPartFlagWoAnyPart method
 *
 * @return void
 */
	public function testGetAnyPartFlagWoAnyPart() {
		$data = [
			'target' => [
				'CityPcode',
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getAnyPartFlag();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetAnyPartFlagWithAnyPart method
 *
 * @return void
 */
	public function testGetAnyPartFlagWithAnyPart() {
		$data = [
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'Pcode',
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getAnyPartFlag();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testGetQueryStrDefault method
 *
 * @return void
 */
	public function testGetQueryStrDefault() {
		$this->_createComponet();
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getQueryStr();
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testGetQueryStrGetParam method
 *
 * @return void
 */
	public function testGetQueryStrGetParam() {
		$data = [
			'query' => 'tst'
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getQueryStr();
		$expected = 'tst';
		$this->assertData($expected, $result);
	}

/**
 * testGetCorrectFlagDefault method
 *
 * @return void
 */
	public function testGetCorrectFlagDefault() {
		$this->_createComponet();
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getCorrectFlag();
		$expected = false;
		$this->assertData($expected, $result);
	}

/**
 * testGetCorrectFlagGetParam method
 *
 * @return void
 */
	public function testGetCorrectFlagGetParam() {
		$data = [
			'correct' => '1'
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$result = $this->_targetObject->getCorrectFlag();
		$expected = true;
		$this->assertData($expected, $result);
	}

/**
 * testSearch method
 *
 * @return void
 */
	public function testSearch() {
		$url = '/search';
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
		$result = $this->Controller->viewVars;
		$expected = [
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
 * testSearchInvalidRequest method
 *
 * @return void
 */
	public function testSearchInvalidRequest() {
		$data = [
			'query' => 'город',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'BadModel'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
	}

/**
 * testSearchGetValidRequest method
 *
 * @return void
 */
	public function testSearchGetValidRequest() {
		$data = [
			'query' => 'город',
			'target' => [
				'City',
				'BadModel'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
		$result = $this->Controller->viewVars;
		$expected = [
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
 * testSearchGetValidRequestRusCorrectFalse method
 *
 * @return void
 */
	public function testSearchGetValidRequestRusCorrectFalse() {
		Configure::write('Config.language', 'rus');
		$data = [
			'query' => 'ujhjl',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
		$result = $this->Controller->viewVars;
		$expected = [
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
				'total' => 3,
			],
			'target' => [
				'City',
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
			],
			'correct' => false,
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
 * testSearchGetValidRequestEngCorrectTrue method
 *
 * @return void
 */
	public function testSearchGetValidRequestEngCorrectTrue() {
		Configure::write('Config.language', 'eng');
		$data = [
			'query' => 'ujhjl',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			],
			'correct' => true
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
		$result = $this->Controller->viewVars;
		$expected = [
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
		$data = [
			'query' => '',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
		$this->checkFlashMessage(__d('cake_search_info', 'Enter your query in the search bar'));
	}

/**
 * testSearchGetValidRequestMinChars method
 *
 * @return void
 */
	public function testSearchGetValidRequestMinChars() {
		$querySearchMinLength = (int)Configure::read('CakeSearchInfo.QuerySearchMinLength');
		$data = [
			'query' => mb_substr('город', 0, $querySearchMinLength - 1),
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			]
		];
		$url = '/search?' . http_build_query($data);
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->search();
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
		$url = '/cake_search_info/search/autocomplete';
		$this->setAjaxRequest();
		$this->_createComponet($url, 'POST');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->setExpectedException('BadRequestException');
		$this->_targetObject->autocomplete();
	}

/**
 * testAutocompleteGetInvalidRequest method
 *
 * @return void
 */
	public function testAutocompleteGetInvalidRequest() {
		$url = '/cake_search_info/search/autocomplete.json';
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->_createComponet($url);
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->setExpectedException('BadRequestException');
		$this->_targetObject->autocomplete();
	}

/**
 * testAutocompletePostNotAjaxInvalidRequest method
 *
 * @return void
 */
	public function testAutocompletePostNotAjaxInvalidRequest() {
		$url = '/cake_search_info/search/autocomplete.json';
		$this->setJsonRequest();
		$this->_createComponet($url, 'POST');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->setExpectedException('BadRequestException');
		$this->_targetObject->autocomplete();
	}

/**
 * testAutocompletePostInvalidTarget method
 *
 * @return void
 */
	public function testAutocompletePostInvalidTarget() {
		$url = '/cake_search_info/search/autocomplete.json';
		$data = [
			'query' => 'го',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			]
		];
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->setPostData($data);
		$this->_createComponet($url, 'POST');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->autocomplete();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
		$this->resetPostData();
	}

/**
 * testAutocompleteValidRequest method
 *
 * @return void
 */
	public function testAutocompleteValidRequest() {
		$url = '/cake_search_info/search/autocomplete.json';
		$data = [
			'query' => 'горо',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			]
		];
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->setPostData($data);
		$this->_createComponet($url, 'POST');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->autocomplete();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$expected = [
			CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt)
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
		$this->resetPostData();
	}

/**
 * testAutocompleteValidRequestTextCorrect method
 *
 * @return void
 */
	public function testAutocompleteValidRequestTextCorrect() {
		Configure::write('Config.language', 'rus');
		$url = '/cake_search_info/search/autocomplete.json';
		$data = [
			'query' => 'ujhjl',
			'target' => [
				CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART,
				'City',
				'BadModel'
			]
		];
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->setPostData($data);
		$this->_createComponet($url, 'POST');
		$this->_targetObject->initialize($this->Controller);
		$this->_targetObject->startup($this->Controller);
		$this->_targetObject->autocomplete();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		$expected = [
			CakeText::truncate('Брест (белор. Брэст, укр. Берестя, польск. Brześć) — город на юго-западе Белоруссии, административный центр Брестской области и Брестского района.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt),
			CakeText::truncate('Ви́тебск (белор. Ві́цебск) — город на северо-востоке Белоруссии, административный центр Витебской области и Витебского района. Телефонный код +375 212.', CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt)
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
		$this->resetPostData();
	}

/**
 * Create SearchFilterComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`.
 * @param array $params Array of parameters for request
 * @param string $contentType Complete mime-type string.
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = [], $contentType = null) {
		$responseOptions = [];
		if (!empty($contentType)) {
			$responseOptions['type'] = $contentType;
		}
		$request = new CakeRequest($url);
		$response = new CakeResponse($responseOptions);
		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new SearchTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Controller->RequestHandler->initialize($this->Controller);
		$this->Controller->Paginator->initialize($this->Controller);
		$this->_targetObject = new SearchFilterComponent($Collection);
	}
}
