<?php
App::uses('AppCakeTestCase', 'CakeSearchInfo.Test');
App::uses('ConfigSearchInfo', 'CakeSearchInfo.Model');

/**
 * ConfigSearchInfo Test Case
 */
class ConfigSearchInfoTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeSearchInfo.ConfigSearchInfo');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testGetConfig method
 *
 * @return void
 */
	public function testGetConfig() {
		$result = $this->_targetObject->getConfig();
		$expected = [
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
			'IncludeFields' => []
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetDeep method
 *
 * @return void
 */
	public function testGetTargetDeep() {
		$result = $this->_targetObject->getTargetDeep();
		$expected = 0;
		$this->assertData($expected, $result);

		$expected = 2;
		Configure::write('CakeSearchInfo.TargetDeep', $expected);
		$result = $this->_targetObject->getTargetDeep();
		$this->assertData($expected, $result);

		Configure::delete('CakeSearchInfo.TargetDeep');
		$result = $this->_targetObject->getTargetDeep();
		$expected = 0;
		$this->assertData($expected, $result);
	}

/**
 * testGetTargetModels method
 *
 * @return void
 */
	public function testGetTargetModels() {
		$result = $this->_targetObject->getTargetModels();
		$expected = [
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
		$this->assertData($expected, $result);

		Configure::delete('CakeSearchInfo.TargetModels');
		$result = $this->_targetObject->getTargetModels();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetIncludeFields method
 *
 * @return void
 */
	public function testGetIncludeFields() {
		$includeFields = ['City' => ['City.id']];
		Configure::write('CakeSearchInfo.IncludeFields', $includeFields);
		$result = $this->_targetObject->getIncludeFields();
		$expected = $includeFields;
		$this->assertData($expected, $result);

		Configure::delete('CakeSearchInfo.IncludeFields');
		$result = $this->_targetObject->getIncludeFields();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetAutocompleteLimit method
 *
 * @return void
 */
	public function testGetAutocompleteLimit() {
		$result = $this->_targetObject->getAutocompleteLimit();
		$expected = 2;
		$this->assertData($expected, $result);

		$expected = 8;
		Configure::write('CakeSearchInfo.AutocompleteLimit', $expected);
		$result = $this->_targetObject->getAutocompleteLimit();
		$this->assertData($expected, $result);

		Configure::delete('CakeSearchInfo.AutocompleteLimit');
		$result = $this->_targetObject->getAutocompleteLimit();
		$expected = CAKE_SEARCH_INFO_AUTOCOMPLETE_LIMIT;
		$this->assertData($expected, $result);
	}

/**
 * testGetQuerySearchMinLength method
 *
 * @return void
 */
	public function testGetQuerySearchMinLength() {
		$result = $this->_targetObject->getQuerySearchMinLength();
		$expected = 3;
		$this->assertData($expected, $result);

		$expected = 4;
		Configure::write('CakeSearchInfo.QuerySearchMinLength', $expected);
		$result = $this->_targetObject->getQuerySearchMinLength();
		$this->assertData($expected, $result);

		Configure::delete('CakeSearchInfo.QuerySearchMinLength');
		$result = $this->_targetObject->getQuerySearchMinLength();
		$expected = CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH;
		$this->assertData($expected, $result);
	}

/**
 * testGetFlagDefaultSearchAnyPart method
 *
 * @return void
 */
	public function testGetFlagDefaultSearchAnyPart() {
		$result = $this->_targetObject->getFlagDefaultSearchAnyPart();
		$this->assertTrue($result);

		Configure::write('CakeSearchInfo.DefaultSearchAnyPart', false);
		$result = $this->_targetObject->getFlagDefaultSearchAnyPart();
		$this->assertFalse($result);

		Configure::delete('CakeSearchInfo.DefaultSearchAnyPart');
		$result = $this->_targetObject->getFlagDefaultSearchAnyPart();
		$this->assertFalse($result);
	}
}
