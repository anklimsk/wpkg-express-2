<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('TreeComponent', 'CakeTheme.Controller/Component');
App::uses('CakeThemeAppModel', 'CakeTheme.Model');
App::uses('Hash', 'Utility');

/**
 * Tree for CakeTheme.
 *
 * @package plugin.Model
 */
class TreeTestWoMethodModel extends CakeThemeAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'TreeTestWoMethod';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = false;
}

/**
 * Tree for CakeTheme.
 *
 * @package plugin.Model
 */
class TreeTestModel extends CakeThemeAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'TreeTestModel';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = false;

/**
 * Return information of tree
 *
 * @param int|string $id Parent ID
 * @return array|bool Array information of tree.
 *  Return False on failure.
 */
	public function getTreeData($id = null) {
		if ($id === 3) {
			return false;
		}

		if ($id === 2) {
			$result = [
				[
					'href' => '/employees/view/2',
					'text' => 'Another user',
				]
			];
		}

		if (empty($id)) {
			$result = [
				[
					'href' => '/employees/view/1',
					'text' => 'Some user',
					'nodes' => [
						[
							'href' => '/employees/view/2',
							'text' => 'Another user',
						],
					]
				]
			];
		}

		return $result;
	}

}

/**
 * TreeTestController class
 *
 */
class TreeTestController extends Controller {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'TreeTest';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'RequestHandler'
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * The default value is `true`.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['TreeTestModel'];
}

/**
 * TreeComponentTest Test Case
 */
class TreeComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session'
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
		parent::tearDown();
	}

/**
 * testTreeBadModelWoMethod method
 *
 * @return void
 */
	public function testTreeBadModelWoMethod() {
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/tree');
		$this->Controller->modelClass = 'TreeTestWoMethodModel';
		$this->TreeComponent->initialize($this->Controller);
		$this->TreeComponent->tree();
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testTreeNotAjaxGet method
 *
 * @return void
 */
	public function testTreeNotAjaxGet() {
		$this->setJsonRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/tree.json');
		$this->TreeComponent->initialize($this->Controller);
		$this->TreeComponent->tree();
		$this->resetJsonRequest();
	}

/**
 * testTreeNotJsonPost method
 *
 * @return void
 */
	public function testTreeNotJsonPost() {
		$this->setAjaxRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/tree', 'POST');
		$this->TreeComponent->initialize($this->Controller);
		$this->TreeComponent->tree();
		$this->resetAjaxRequest();
	}

/**
 * testTreeSuccessForId method
 *
 * @return void
 */
	public function testTreeSuccessForId() {
		$this->setJsonRequest();
		$this->setAjaxRequest();

		$this->_createComponet('/cake_theme/tree/2.json', 'POST');
		$this->TreeComponent->initialize($this->Controller);
		$this->TreeComponent->tree(2);
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [
			[
				'href' => '/employees/view/2',
				'text' => 'Another user',
			]
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testTreeSuccessFull method
 *
 * @return void
 */
	public function testTreeSuccessFull() {
		$this->setJsonRequest();
		$this->setAjaxRequest();

		$this->_createComponet('/cake_theme/tree.json', 'POST');
		$this->TreeComponent->initialize($this->Controller);
		$this->TreeComponent->tree();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [
			[
				'href' => '/employees/view/1',
				'text' => 'Some user',
				'nodes' => [
					[
						'href' => '/employees/view/2',
						'text' => 'Another user',
					],
				]
			]
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * Create TreeComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = []) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();

		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		$this->Controller = new TreeTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Controller->RequestHandler->initialize($this->Controller);
		$this->TreeComponent = new TreeComponent($Collection);
	}
}
