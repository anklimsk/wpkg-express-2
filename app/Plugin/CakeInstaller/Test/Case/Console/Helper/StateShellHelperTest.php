<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('StateShellHelper', 'CakeInstaller.Console/Helper');
App::uses('ConsoleOutputStub', 'TestSuite/Stub');

/**
 * StateShellHelper test case.
 * @property ConsoleOutputStub $consoleOutput
 * @property ProgressShellHelper $helper
 */
class StateShellHelperTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->consoleOutput = new ConsoleOutputStub();
		$this->_targetObject = new StateShellHelper($this->consoleOutput);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->consoleOutput);

		parent::tearDown();
	}

/**
 * Test getState
 *
 * @return void
 */
	public function testGetState() {
		$params = [
			[
				null, // $message
				null, // $state
				null, // $maxWidth
			],
			[
				'Test message', // $message
				'[Ok]', // $state
				20, // $maxWidth
			],
			[
				'Test message', // $message
				'[Ok]', // $state
				10, // $maxWidth
			],
			[
				'Test message', // $message
				'[Ok]', // $state
				- 1, // $maxWidth
			],
		];
		$expected = [
			null,
			'Test message    [Ok]',
			'Test message [Ok]',
			'Test message',
		];

		$this->runClassMethodGroup('getState', $params, $expected);
	}

/**
 * Test output
 *
 * @return void
 */
	public function testDefaultOutput() {
		$data = [
			'Test message', // $message
			'[Ok]', // $state
			20, // $maxWidth
		];
		$this->_targetObject->output($data);

		$data = [
			'Test message', // $message
			'[Ok]', // $state
			10, // $maxWidth
		];
		$this->_targetObject->output($data);

		$data = [
			'Test message', // $message
			'[Ok]', // $state
			- 1, // $maxWidth
		];
		$this->_targetObject->output($data);

		$expected = [
			'Test message    [Ok]',
			'Test message [Ok]',
			'Test message',
		];
		$this->assertEquals($expected, $this->consoleOutput->messages());
	}
}
