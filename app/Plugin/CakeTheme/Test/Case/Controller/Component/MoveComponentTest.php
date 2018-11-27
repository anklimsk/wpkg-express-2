<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Hash', 'Utility');
App::uses('MoveComponent', 'CakeTheme.Controller/Component');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * TreeTestController class
 *
 */
class MoveTestController extends Controller {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'MoveTest';

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
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['TreeDataTest'];

	public $redirect = null;

/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param string|array $url A string or array-based URL pointing to another location within the app,
 *     or an absolute URL
 * @param int $status Optional HTTP status code (eg: 404)
 * @param bool $exit If true, exit() will be called after the redirect
 * @return void
 * @triggers Controller.beforeRedirect $this, array($url, $status, $exit)
 * @link http://book.cakephp.org/2.0/en/controllers.html#Controller::redirect
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->redirect = $url;
	}

}

/**
 * MoveComponent Test Case
 */
class MoveComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_theme.tree'
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
 * testTreeBadModel method
 *
 * @return void
 */
	public function testTreeBadModel() {
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_theme/move');
		$this->MoveComponent->modelName = 'BadModel';
		$this->MoveComponent->initialize($this->Controller);
	}

/**
 * testMoveItemJson method
 *
 * @return void
 */
	public function testMoveItemJson() {
		$this->setJsonRequest();
		$this->_createComponet('/cake_theme/move');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->moveItem('up', 4, 2);
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [
			'result' => true,
			'direct' => 'up',
			'delta' => 2
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
	}

/**
 * testMoveItemGet method
 *
 * @return void
 */
	public function testMoveItemGet() {
		$this->_createComponet('/cake_theme/move');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->moveItem('bottom', 3, -1);
		$result = $this->Controller->redirect;
		$this->assertFalse(empty($result));
	}

/**
 * testDropItemNotAjaxGet method
 *
 * @return void
 */
	public function testDropItemNotAjaxGet() {
		$this->setJsonRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/drop.json');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->dropItem();
		$this->resetJsonRequest();
	}

/**
 * testDropItemNotJsonPost method
 *
 * @return void
 */
	public function testDropItemNotJsonPost() {
		$this->setAjaxRequest();
		$this->setExpectedException('BadRequestException');
		$this->_createComponet('/cake_theme/drop', 'POST');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->dropItem();
		$this->resetAjaxRequest();
	}

/**
 * testDropItemPostDataInvalid method
 *
 * @return void
 */
	public function testDropItemPostDataInvalid() {
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$this->_createComponet('/cake_theme/drop.json', 'POST');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->dropItem();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [
			'result' => false
		];
		$this->assertData($expected, $result);
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * testDropItemSuccess method
 *
 * @return void
 */
	public function testDropItemSuccess() {
		$this->setJsonRequest();
		$this->setAjaxRequest();
		$dropData = [
			0 => [
				[
					'id' => '3'
				],
				[
					'id' => '5'
				],
				[
					'id' => '6'
				],
				[
					'id' => '4'
				]
			]
		];
		$this->setPostData([
			'target' => 4,
			'parent' => 2,
			'parentStart' => 2,
			'tree' => json_encode($dropData),
		]);
		$this->_createComponet('/cake_theme/drop.json', 'POST');
		$this->MoveComponent->initialize($this->Controller);
		$this->MoveComponent->dropItem();
		$viewVars = $this->Controller->viewVars;
		$result = Hash::get($viewVars, 'data');
		$expected = [
			'result' => true
		];
		$this->assertData($expected, $result);
		$this->resetPostData();
		$this->resetJsonRequest();
		$this->resetAjaxRequest();
	}

/**
 * Create MoveComponent
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
		$this->Controller = new MoveTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Controller->RequestHandler->initialize($this->Controller);
		$this->MoveComponent = new MoveComponent($Collection);
	}
}
