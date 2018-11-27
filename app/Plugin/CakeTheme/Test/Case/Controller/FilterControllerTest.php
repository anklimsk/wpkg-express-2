<?php
App::uses('AppControllerTestCase', 'CakeTheme.Test');
App::uses('FilterController', 'CakeTheme.Controller');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * FilterController Test Case
 */
class FilterControllerTest extends AppControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_theme.employees'
	];

/**
 * testAutocomplete method
 *
 * User role: user
 *
 * @return void
 */
	public function testAutocompleteGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/filter/autocomplete.json', $opt);
	}

/**
 * testAutocomplete method
 *
 * User role: user
 *
 * @return void
 */
	public function testAutocompleteNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/filter/autocomplete', $opt);
	}

/**
 * testAutocomplete method
 *
 * User role: user
 *
 * @return void
 */
	public function testAutocompletePost() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/filter/autocomplete', $opt);
		$this->resetAjaxRequest();
	}

/**
 * testAutocomplete method
 *
 * User role: user
 *
 * Method: POST
 * Data: empty
 *
 * @return void
 */
	public function testAutocompletePostDataEmpty() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'query' => '',
				'type' => 'EmployeeTest.last_name',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testAutocomplete method
 *
 * User role: user
 * Method: POST
 * Data: invalid
 *
 * @return void
 */
	public function testAutocompletePostDataInvalid() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => 'test',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);

		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => 'EmployeeTest.test',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);

		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => 'EmployeeTest.photo',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);

		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => 'EmployeeTest',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);

		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => ['test'],
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}

/**
 * testAutocomplete method
 *
 * User role: user
 * Method: POST
 * Data: valid
 *
 * @return void
 */
	public function testAutocompletePostDataValid() {
		$this->setAjaxRequest();
		$opt = [
			'data' => [
				'query' => 'инж',
				'type' => 'EmployeeTest.position',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'Инженер 1 категории',
			'Инженер-электроник 1 категории'
		];
		$this->assertData($expected, $result);

		$opt = [
			'data' => [
				'query' => 'ал',
				'type' => 'EmployeeTest.first_name',
			],
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/filter/autocomplete.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			'Александр',
			'Алексей'
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}
}
