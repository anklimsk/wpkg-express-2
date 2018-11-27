<?php
App::uses('ExtendCakeTestCase', 'CakeExtendTest.Test');
App::import(
	'Vendor',
	'CakeExtendTest.PeekAndPoke',
	['file' => 'PeekAndPoke' . DS . 'autoload.php']
);

/**
 * Resturn value of param with label.
 * Used for testing getting information about
 *  function with default value.
 *
 * @param string $param Data for return.
 * @return string Value of param with label.
 */
function test_some_func($param = null) {
	if (empty($param)) {
		$param = 'test';
	}

	$result = 'Param value: ' . $param;

	return $result;
}

/**
 * Test_Some_Class class
 *
 * @package     plugin.Test.Case.TestCase
 */
class Test_Some_Class {

/**
 * Method `some_method`.
 * Used for testing getting information about
 * method of class with default value.
 *
 * @param int $i Data for increment.
 * @return int Increment value
 */
// @codingStandardsIgnoreStart
	public function some_method($i = 0) {
	// @codingStandardsIgnoreEnd
		$result = ++$i;

		return $result;
	}

/**
 * Method `some_wo_def_val`.
 * Used for testing getting information about
 * method of class without default value.
 *
 * @param mixed $data Data for checking is empty.
 * @return bool True, if data is not empty.
 *  False otherwise.
 */
// @codingStandardsIgnoreStart
	public function some_wo_def_val($data) {
	// @codingStandardsIgnoreEnd
		$result = empty($data);

		return $result;
	}

}

/**
 * ExtendCakeTestCaseTest Test Case
 */
class ExtendCakeTestCaseTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->_targetObject = new ExtendCakeTestCase();
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
 * testSetUpTearDown method
 *
 * @return void
 */
	public function testSetUpTearDown() {
		Configure::write('Config.language', 'tst');
		$this->_targetObject->setUp();

		Configure::write('Config.language', 'rus');
		$this->_targetObject->tearDown();

		$result = Configure::read('Config.language');
		$expected = 'tst';
		$this->assertSame($expected, $result);
	}

/**
 * testRunClassMethodGroup method
 *
 * @return void
 */
	public function testRunClassMethodGroup() {
		$params = [
			'',
			'some value',
			[
				'Test text value'
			]
		];
		$expected = [
			'Param value: test',
			[
				'assertRegExp' => '/Param\svalue\:\s.+/'
			],
			[
				'expecteds' => [
					'assertSame' => 'Param value: Test text value',
					'assertRegExp' => '/Param\svalue\:\s.+/',
					'Param value: Test text value'
				]
			]
		];

		$this->_targetObject->runClassMethodGroup('test_some_func', $params, $expected);
	}

/**
 * testGetClassInfo method
 *
 * @return void
 */
	public function testGetClassInfo() {
		$target = new ExtendCakeTestCase();
		$proxy = new SebastianBergmann\PeekAndPoke\Proxy($target);
		$result = $proxy->_getClassInfo(null, 'test_some_func', 'test');
		$expected = "test_some_func(\$param = null),\r\n" . __d('cake_extend_test', 'where') . ":\r\n\$param = 'test'";
		$this->assertSame($expected, $result);

		$testSomeClass = new Test_Some_Class();
		$result = $proxy->_getClassInfo($testSomeClass, 'some_method', 5);
		$expected = "Test_Some_Class::some_method(\$i = 0),\r\n" . __d('cake_extend_test', 'where') . ":\r\n\$i = (int) 5";
		$this->assertSame($expected, $result);

		$result = $proxy->_getClassInfo($testSomeClass, 'some_wo_def_val');
		$expected = "Test_Some_Class::some_wo_def_val(\$data),\r\n" . __d('cake_extend_test', 'where') . ":\r\n\$data = null";
		$this->assertSame($expected, $result);
	}
}
