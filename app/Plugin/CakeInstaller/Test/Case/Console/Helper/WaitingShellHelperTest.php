<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('WaitingShellHelper', 'CakeInstaller.Console/Helper');
App::uses('ConsoleOutputStub', 'TestSuite/Stub');

/**
 * WaitingShellHelper test case.
 * @property ConsoleOutputStub $consoleOutput
 * @property WaitingShellHelper $helper
 */
class WaitingShellHelperTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->consoleOutput = new ConsoleOutputStub();
		$this->_targetObject = new WaitingShellHelper($this->consoleOutput);
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
 * Test animateMessage
 *
 * @return void
 */
	public function testAnimateMessage() {
		$this->_targetObject->animateMessage();
		$this->_targetObject->animateMessage();
		$this->_targetObject->animateMessage();
		$expected = [
			__d('cake_installer', 'Please wait...') . ' |',
			'',
			'/',
			'',
			'-',
		];
		$this->assertEquals($expected, $this->consoleOutput->messages());
	}

/**
 * Test hideMessage
 *
 * @return void
 */
	public function testHideMessage() {
		$this->_targetObject->config(['message' => '...']);
		$this->_targetObject->animateMessage();
		$this->_targetObject->hideMessage();
		$this->_targetObject->hideMessage();

		$msg = __d('cake_installer', 'Please wait...') . ' |';
		$expected = [
			$msg,
			str_repeat("\x08", mb_strlen($msg))
		];
		$this->assertEquals($expected, $this->consoleOutput->messages());
	}

/**
 * Test output
 *
 * @return void
 */
	public function testOutput() {
		$this->_targetObject->output([]);
		$expected = [
			__d('cake_installer', 'Please wait...')
		];
		$this->assertEquals($expected, $this->consoleOutput->messages());
	}
}
