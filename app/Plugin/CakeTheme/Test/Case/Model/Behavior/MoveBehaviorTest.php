<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('MoveBehavior', 'CakeTheme.Model/Behavior');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * MoveBehavior Test Case
 */
class MoveBehaviorTest extends AppCakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = ['plugin.cake_theme.tree'];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = new TreeDataTest();
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
 * testMoveItemBadDelta method
 *
 * @return void
 */
	public function testMoveItemBadDelta() {
		$this->setExpectedException('InternalErrorException');
		$this->_targetObject->moveItem('down', 1, -1);
	}

/**
 * testMoveItemBadDirect method
 *
 * @return void
 */
	public function testMoveItemBadDirect() {
		$this->setExpectedException('InternalErrorException');
		$this->_targetObject->moveItem('bad_dir', 3, 1);
	}

/**
 * testMoveItemBottomBadDelta method
 *
 * @return void
 */
	public function testMoveItemBottomBadDelta() {
		$result = $this->_targetObject->moveItem('bottom', 3, -1);
		$this->assertTrue($result);
	}

/**
 * testMoveItemUpForTopItemSuccess method
 *
 * @return void
 */
	public function testMoveItemUpForTopItemSuccess() {
		$result = $this->_targetObject->moveItem('up', 3);
		$this->assertFalse($result);
	}

/**
 * testMoveItemUpLargeDeltaSuccess method
 *
 * @return void
 */
	public function testMoveItemUpLargeDeltaSuccess() {
		$result = $this->_targetObject->moveItem('up', 5, 3);
		$this->assertTrue($result);
	}

/**
 * testMoveItemDownSuccess method
 *
 * @return void
 */
	public function testMoveItemDownSuccess() {
		$result = $this->_targetObject->moveItem('down', 4, 2);
		$this->assertTrue($result);
	}

/**
 * testMoveItemTopSuccess method
 *
 * @return void
 */
	public function testMoveItemTopSuccess() {
		$result = $this->_targetObject->moveItem('top', 2);
		$this->assertTrue($result);
	}

/**
 * testMoveItemCallback method
 *
 * @return void
 */
	public function testMoveItemCallback() {
		$result = $this->_targetObject->moveItem('down', 4, 2);
		$this->assertTrue($result);

		$result = $this->_targetObject->callbackActions;
		$expected = [
			'beforeUpdateTree' => [
				'id' => 4,
				'newParentId' => null,
				'method' => 'moveDown',
				'delta' => 2,
			],
			'afterUpdateTree' => null
		];
		$this->assertData($expected, $result);
	}

/**
 * testMoveDropEmptyData method
 *
 * @return void
 */
	public function testMoveDropEmptyData() {
		$result = $this->_targetObject->moveDrop(null);
		$this->assertFalse($result);
	}

/**
 * testMoveDropRootSucess method
 *
 * @return void
 */
	public function testMoveDropRootSucess() {
		$dropData = [
			0 => [
				[
					'id' => '2'
				],
				[
					'id' => '1'
				]
			]
		];
		$result = $this->_targetObject->moveDrop(2, null, null, $dropData);
		$this->assertTrue($result);

		$result = $this->_targetObject->generateTreeList(null, '{n}.TreeDataTest.id', '{n}.TreeDataTest.name');
		$expected = [
			2 => 'root - 2 - 1',
			3 => '_level - 2.1 - 1',
			4 => '_level - 2.2 - 1',
			5 => '_level - 2.3 - 1',
			6 => '_level - 2.4 - 1',
			1 => 'root - 1 - 1'
		];
		$this->assertData($expected, $result);
	}

/**
 * testMoveDropInsideLevelSuccess method
 *
 * @return void
 */
	public function testMoveDropInsideLevelSuccess() {
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
		$result = $this->_targetObject->moveDrop(4, 2, 2, $dropData);
		$this->assertTrue($result);

		$result = $this->_targetObject->generateTreeList(null, '{n}.TreeDataTest.id', '{n}.TreeDataTest.name');
		$expected = [
			1 => 'root - 1 - 1',
			2 => 'root - 2 - 1',
			3 => '_level - 2.1 - 1',
			5 => '_level - 2.3 - 1',
			6 => '_level - 2.4 - 1',
			4 => '_level - 2.2 - 1',
		];
		$this->assertData($expected, $result);
	}

/**
 * testMoveDropChangeRootSuccess method
 *
 * @return void
 */
	public function testMoveDropChangeRootSuccess() {
		$dropData = [
			0 => [
				[
					'id' => '2'
				],
				[
					'id' => '5'
				],
				[
					'id' => '1'
				],
			]
		];
		$result = $this->_targetObject->moveDrop(5, null, 2, $dropData);
		$this->assertTrue($result);

		$result = $this->_targetObject->generateTreeList(null, '{n}.TreeDataTest.id', '{n}.TreeDataTest.name');
		$expected = [
			1 => 'root - 1 - 1',
			5 => 'level - 2.3 - 1',
			2 => 'root - 2 - 1',
			3 => '_level - 2.1 - 1',
			4 => '_level - 2.2 - 1',
			6 => '_level - 2.4 - 1',
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result);
	}

/**
 * testMoveDropChangeScopeSuccess method
 *
 * @return void
 */
	public function testMoveDropChangeScopeSuccess() {
		$scope = ['TreeDataTest.type' => 2];
		$this->_targetObject->Behaviors->load('Tree', compact('scope'));
		$dropData = [
			0 => [
				[
					'id' => '9'
				],
				[
					'id' => '8'
				],
			]
		];
		$result = $this->_targetObject->moveDrop(8, 7, 7, $dropData);
		$this->assertTrue($result);

		$result = $this->_targetObject->generateTreeList(null, '{n}.TreeDataTest.id', '{n}.TreeDataTest.name');
		$expected = [
			7 => 'root - 1 - 2',
			9 => '_level - 2.2 - 2',
			8 => '_level - 2.1 - 2',
		];
		$this->assertData($expected, $result);

		$result = $this->_targetObject->verify();
		$this->assertTrue($result);
	}

/**
 * testMoveItemCallback method
 *
 * @return void
 */
	public function testMoveDropCallback() {
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
		$result = $this->_targetObject->moveDrop(4, 2, 2, $dropData);
		$this->assertTrue($result);

		$result = $this->_targetObject->callbackActions;
		$expected = [
			'beforeUpdateTree' => [
				'id' => 4,
				'newParentId' => 2,
				'method' => 'moveDown',
				'delta' => 2
			],
			'afterUpdateTree' => null
		];
		$this->assertData($expected, $result);
	}

/**
 * testSetup method
 *
 * @return void
 */
	public function testSetup() {
		$model = new TreeDataTest();
		$behavior = new MoveBehavior();
		$model->Behaviors->unload('Tree');
		$this->setExpectedException('InternalErrorException');
		$behavior->setup($model);
	}

/**
 * testGetBehaviorConfig method
 *
 * @return void
 */
	public function testGetBehaviorConfig() {
		$model = new TreeDataTest();
		$behavior = new MoveBehavior();
		$proxy = $this->createProxyObject($behavior);
		$result = $proxy->_getBehaviorConfig($model, 'bad_cfg');
		$this->assertNull($result);

		$model->Behaviors->load('Tree', ['left' => 'left_field']);
		$result = $proxy->_getBehaviorConfig($model, 'left');
		$expected = 'left_field';
		$this->assertData($expected, $result);
	}
}
