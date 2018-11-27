<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('CheckResultHelper', 'CakeInstaller.View/Helper');

/**
 * CheckResultHelper Test Case
 */
class CheckResultHelperTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->_targetObject = new CheckResultHelper($View);
	}

/**
 * testGetStateElement method
 *
 * @return void
 */
	public function testGetStateElement() {
		$params = [
			[
				null, // $state
			],
			[
				false, // $state
			],
			[
				'-1', // $state
			],
			[
				true, // $state
			],
			[
				0, // $state
			],
			[
				1, // $state
			],
			[
				'2', // $state
			],
			[
				5, // $state
			],
		];
		$expected = [
			'<span class="label label-danger" title="' . __d('cake_installer', 'Bad') . '" data-toggle="tooltip"><span class="fas fa-times"></span></span>',
			'<span class="label label-danger" title="' . __d('cake_installer', 'Bad') . '" data-toggle="tooltip"><span class="fas fa-times"></span></span>',
			'<span class="label label-danger" title="' . __d('cake_installer', 'Bad') . '" data-toggle="tooltip"><span class="fas fa-times"></span></span>',
			'<span class="label label-success" title="' . __d('cake_installer', 'Ok') . '" data-toggle="tooltip"><span class="fas fa-check"></span></span>',
			'<span class="label label-danger" title="' . __d('cake_installer', 'Bad') . '" data-toggle="tooltip"><span class="fas fa-times"></span></span>',
			'<span class="label label-warning" title="' . __d('cake_installer', 'Ok') . '" data-toggle="tooltip"><span class="fas fa-minus"></span></span>',
			'<span class="label label-success" title="' . __d('cake_installer', 'Ok') . '" data-toggle="tooltip"><span class="fas fa-check"></span></span>',
			'<span class="label label-danger" title="' . __d('cake_installer', 'Bad') . '" data-toggle="tooltip"><span class="fas fa-times"></span></span>',
		];

		$this->runClassMethodGroup('getStateElement', $params, $expected);
	}

/**
 * testGetStateItemClass method
 *
 * @return void
 */
	public function testGetStateItemClass() {
		$params = [
			[
				null, // $state
			],
			[
				false, // $state
			],
			[
				'-1', // $state
			],
			[
				true, // $state
			],
			[
				0, // $state
			],
			[
				1, // $state
			],
			[
				'2', // $state
			],
			[
				5, // $state
			],
		];
		$expected = [
			'list-group-item-danger',
			'list-group-item-danger',
			'list-group-item-danger',
			'',
			'list-group-item-danger',
			'list-group-item-warning',
			'',
			'list-group-item-danger',
		];

		$this->runClassMethodGroup('getStateItemClass', $params, $expected);
	}

/**
 * testGetStateList method
 *
 * @return void
 */
	public function testGetStateList() {
		$params = [
			[
				[], // $list
			],
			[
				[
					'Some text',
					'Test'
				], // $state
			],
			[
				[
					[
						'Text'
					]
				], // $state
			],
			[
				[
					[
						'textItem' => 'Test text',
						'classItem' => 'list-group-item-warning'
					],
					[
						'textItem' => 'Some text',
					],
					[
						'Bad item'
					]
				], // $state
			],
		];
		$expected = [
			'',
			'<ul class="list-group"><li class="list-group-item">Some text</li><li class="list-group-item">Test</li></ul>',
			'',
			'<ul class="list-group"><li class="list-group-item list-group-item-warning">Test text</li><li class="list-group-item">Some text</li></ul>',
		];

		$this->runClassMethodGroup('getStateList', $params, $expected);
	}
}
