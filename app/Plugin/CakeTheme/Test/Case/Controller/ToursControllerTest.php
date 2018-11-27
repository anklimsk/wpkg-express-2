<?php
App::uses('AppControllerTestCase', 'CakeTheme.Test');
App::uses('ComponentCollection', 'Controller');

class ToursControllerTest extends AppControllerTestCase {

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
 * testSteps method
 *
 * @return void
 */
	public function testStepsGet() {
		$opt = [
			'method' => 'GET'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/tours/steps.json', $opt);
	}

/**
 * testSteps method
 *
 * @return void
 */
	public function testStepsNotAjaxPost() {
		$opt = [
			'method' => 'POST'
		];
		$this->setExpectedException('BadRequestException');
		$this->testAction('/cake_theme/tours/steps', $opt);
	}

/**
 * testSteps method
 *
 * @return void
 */
	public function testStepsPost() {
		$this->setAjaxRequest();
		$opt = [
			'method' => 'POST',
			'return' => 'contents'
		];
		$result = $this->testAction('/cake_theme/tours/steps.json', $opt);
		$result = json_decode($result, true);
		$expected = [
			[
				'path' => '/',
				'element' => 'ul.nav',
				'title' => 'Title',
				'content' => 'Some text.',
			],
			[
				'element' => '#content',
				'title' => 'Content area',
				'content' => 'Content',
			]
		];
		$this->assertData($expected, $result);
		$this->resetAjaxRequest();
	}
}
