<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('FilterComponent', 'CakeTheme.Controller/Component');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * FilterTestController class
 *
 */
class FilterTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
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
 * FilterComponent Test Case
 */
class FilterComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_theme.employees'
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
		unset($this->Filter);

		parent::tearDown();
	}

/**
 * testGetFilterConditionsEmptyData method
 *
 * @return void
 */
	public function testGetFilterConditionsEmptyData() {
		$this->_createComponet('/cake_theme/filter/index');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilterConditionsInvalidData method
 *
 * @return void
 */
	public function testGetFilterConditionsInvalidData() {
		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][BadModel][test]=Абр');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][test]=Абр');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][photo]=Абр');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilterConditionsValidData method
 *
 * @return void
 */
	public function testGetFilterConditionsValidData() {
		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][last_name]=Абр');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'LOWER(EmployeeTest.last_name) like' => '%абр%'
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][manager]=1');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'EmployeeTest.manager' => '1'
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterCond][0][EmployeeTest][birthday]=ge&data[FilterData][0][EmployeeTest][birthday]=january 1980');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'EmployeeTest.birthday >=' => '1980-01-01'
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][birthday]=1978-01-05');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'EmployeeTest.birthday' => '1978-01-05'
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index?data[FilterCond][0][EmployeeTest][block]=gt&data[FilterData][0][EmployeeTest][block]=0');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'EmployeeTest.block' => '0'
		];
		$this->assertData($expected, $result);

		$this->setPostData([
			'FilterCond' => [
				[
					'EmployeeTest' => [
						'birthday' => 'ge'
					]
				]
			],
			'FilterData' => [
				[
					'EmployeeTest' => [
						'birthday' => 'january 1980'
					]
				]
			]
		]);
		$this->_createComponet('/cake_theme/filter/index', 'POST');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getFilterConditions();
		$expected = [
			'EmployeeTest.birthday >=' => '1980-01-01'
		];
		$this->assertData($expected, $result);
		$this->resetPostData();
	}

/**
 * testGetGroupActionInvalidData method
 *
 * @return void
 */
	public function testGetGroupActionInvalidData() {
		$this->_createComponet('/cake_theme/test');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getGroupAction();
		$this->assertFalse($result);

		$this->setPostData([
			'FilterGroup' => [
				'action' => 'test_act'
			]
		]);
		$this->_createComponet('/cake_theme/filter/index', 'POST');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getGroupAction('test_act');
		$this->assertFalse($result);
		$this->resetPostData();

		$this->setPostData([
			'FilterGroup' => [
				'action' => 'inv_action'
			]
		]);
		$this->_createComponet('/cake_theme/filter/index', 'POST');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getGroupAction(['SomeAction']);
		$this->assertFalse($result);
		$this->resetPostData();
	}

/**
 * testGetGroupActionValidData method
 *
 * @return void
 */
	public function testGetGroupActionValidData() {
		$this->setPostData([
			'FilterGroup' => [
				'action' => 'some_act'
			]
		]);
		$this->_createComponet('/cake_theme/filter/index', 'POST');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getGroupAction();
		$expected = 'some_act';
		$this->assertData($expected, $result);
		$this->resetPostData();

		$this->setPostData([
			'FilterGroup' => [
				'action' => 'test_act'
			]
		]);
		$this->_createComponet('/cake_theme/filter/index', 'POST');
		$this->Filter->initialize($this->Controller);
		$result = $this->Filter->getGroupAction(['test_act']);
		$expected = 'test_act';
		$this->assertData($expected, $result);
		$this->resetPostData();
	}

/**
 * testGetExtendPaginationOptionsWoPaginationComponent method
 *
 * @return void
 */
	public function testGetExtendPaginationOptionsWoPaginationComponent() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][last_name]=Руб', 'GET', [], true);
		$this->Filter->initialize($this->Controller);
		$options = [
			'limit' => 10
		];
		$result = $this->Filter->getExtendPaginationOptions($options);
	}

/**
 * testGetExtendPaginationOptions method
 *
 * @return void
 */
	public function testGetExtendPaginationOptions() {
		$this->_createComponet('/cake_theme/filter/index?data[FilterData][0][EmployeeTest][last_name]=Абр');
		$this->Filter->initialize($this->Controller);
		$options = [
			'limit' => 5
		];
		$result = $this->Filter->getExtendPaginationOptions($options);
		$expected = [
			'limit' => 5
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index.prt?data[FilterData][0][EmployeeTest][manager]=0', 'GET', ['ext' => 'prt']);
		$this->Filter->initialize($this->Controller);
		$options = [
			'limit' => 8
		];
		$result = $this->Filter->getExtendPaginationOptions($options);
		$expected = [
			'limit' => CAKE_THEME_PRINT_DATA_LIMIT,
			'maxLimit' => CAKE_THEME_PRINT_DATA_LIMIT,
		];
		$this->assertData($expected, $result);

		$this->_createComponet('/cake_theme/filter/index.prt/page:2?data[FilterData][0][EmployeeTest][manager]=1', 'GET', ['ext' => 'prt', 'named' => ['page' => 2]]);
		$this->Filter->initialize($this->Controller);
		$options = [
			'limit' => 6
		];
		$result = $this->Filter->getExtendPaginationOptions($options);
		$expected = [
			'limit' => 6
		];
		$this->assertData($expected, $result);
	}

/**
 * Create FilterComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`
 * @param array $params Array of parameters for request
 * @param bool $useDefaultController Flag of use default controller
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = [], $useDefaultController = false) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		if ($useDefaultController) {
			$this->Controller = new Controller($request, $response);
			$this->Controller->constructClasses();
		} else {
			$this->Controller = new FilterTestController($request, $response);
			$this->Controller->constructClasses();
			$this->Controller->RequestHandler->initialize($this->Controller);
			$this->Controller->Paginator->initialize($this->Controller);
		}
		$this->Filter = new FilterComponent($Collection);
	}
}
