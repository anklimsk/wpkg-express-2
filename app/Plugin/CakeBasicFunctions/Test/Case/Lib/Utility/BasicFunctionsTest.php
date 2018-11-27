<?php
App::uses('AppCakeTestCase', 'CakeBasicFunctions.Test');

/**
 * BasicFunctionsTest Test Case
 */
class BasicFunctionsTest extends AppCakeTestCase {

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
 * testConstsToWords method
 *
 * @return void
 */
	public function testConstsToWords() {
		if (!defined('TEST_CONSTANT_TEST')) {
			define('TEST_CONSTANT_TEST', 1);
		}

		if (!defined('TEST_CONSTANT_SOME_CONST')) {
			define('TEST_CONSTANT_SOME_CONST', 'Some value');
		}

		if (!defined('TEST_CONSTANT_TEST_WORD_CONST')) {
			define('TEST_CONSTANT_TEST_WORD_CONST', 'Test value');
		}

		$params = [
			[
				'BAD_PREFIX', // $prefix
				false, // $skipWords
			],
			[
				'test_constant', // $prefix
				false, // $skipWords
			],
			[
				'test_constant', // $prefix
				1, // $skipWords
			],
		];
		$expected = [
			[],
			[
				1 => 'Test',
				'Some value' => 'Some const',
				'Test value' => 'Test word const',
			],
			[
				1 => '',
				'Some value' => 'Const',
				'Test value' => 'Word const',
			],
		];
		$this->runClassMethodGroup('constsToWords', $params, $expected);
	}

/**
 * testConstsVals method
 *
 * @return void
 */
	public function testConstsVals() {
		if (!defined('TEST_CONSTANT_TEST')) {
			define('TEST_CONSTANT_TEST', 1);
		}

		if (!defined('TEST_CONSTANT_SOME_CONST')) {
			define('TEST_CONSTANT_SOME_CONST', 'Some value');
		}

		if (!defined('TEST_CONSTANT_TEST_WORD_CONST')) {
			define('TEST_CONSTANT_TEST_WORD_CONST', 'Test value');
		}

		$params = [
			[
				'BAD_PREFIX', // $prefix
			],
			[
				'test_constant', // $prefix
			],
		];
		$expected = [
			[],
			[
				0 => 1,
				1 => 'Some value',
				2 => 'Test value',
			],
		];
		$this->runClassMethodGroup('constsVals', $params, $expected);
	}

/**
 * testConstValToLcSingle method
 *
 * @return void
 */
	public function testConstValToLcSingle() {
		if (!defined('TEST_CONSTANT_TEST')) {
			define('TEST_CONSTANT_TEST', 1);
		}

		if (!defined('TEST_CONSTANT_SOME_CONST')) {
			define('TEST_CONSTANT_SOME_CONST', 'Some value');
		}

		if (!defined('TEST_CONSTANT_TEST_WORD_CONST')) {
			define('TEST_CONSTANT_TEST_WORD_CONST', 'Test value');
		}

		$params = [
			[
				'BAD_PREFIX', // $prefix
				'test', // $val
				false, // $keepUnderscore
				false, // $skipWords
				true, // $useUnknown
			],
			[
				'TEST_CONSTANT', // $prefix
				'bad value', // $val
				false, // $keepUnderscore
				false, // $skipWords
				false, // $useUnknown
			],
			[
				'test_constant', // $prefix
				'Test value', // $val
				false, // $keepUnderscore
				false, // $skipWords
				true, // $useUnknown
			],
			[
				'test_constant', // $prefix
				'Some value', // $val
				' ', // $keepUnderscore
				false, // $skipWords
				true, // $useUnknown
			],
			[
				'test_constant', // $prefix
				'Some value', // $val
				' ', // $keepUnderscore
				1, // $skipWords
				true, // $useUnknown
			],
		];
		$expected = [
			__d('cake_basic_functions', 'Unknown'),
			null,
			'testwordconst',
			'some const',
			'const'
		];
		$this->runClassMethodGroup('constValToLcSingle', $params, $expected);
	}

/**
 * testTranslArray method
 *
 * @return void
 */
	public function testTranslArray() {
		$dataArray = [];
		$dataArrayVal = ['test'];
		// @codingStandardsIgnoreStart
		$params = [
			[
				&$dataArray, // $data
				'', // $domain
			],
			[
				&$dataArrayVal, // $data
				'', // $domain
			],
			[
				&$dataArrayVal, // $data
				'test', // $domain
			],
		];
		// @codingStandardsIgnoreEnd
		$expected = [
			false,
			false,
			true,
		];
		$this->runClassMethodGroup('translArray', $params, $expected);
	}

/**
 * testMbUcfirst method
 *
 * @return void
 */
	public function testMbUcfirst() {
		$params = [
			[
				'', // $string
				'', // $encoding
			],
			[
				'проверка функции mb_ucfirst', // $string
				'', // $encoding
			],
			[
				'проверкА ФункцИИ mb_ucfirst', // $string
				'UTF-8', // $encoding
			],
		];
		$expected = [
			'',
			'Проверка функции mb_ucfirst',
			'ПроверкА ФункцИИ mb_ucfirst',
		];
		$this->runClassMethodGroup('mb_ucfirst', $params, $expected);
	}

/**
 * testIsGuid method
 *
 * @return void
 */
	public function testIsGuid() {
		$params = [
			[
				'', // $guid
			],
			[
				'7ccff67e-0b89-428', // $guid
			],
			[
				'7ccff67e-0b89-4284-b4d2-4e58a6c5c30c', // $guid
			],
			[
				'{0B2DDA2A-955F-45B3-95BC-FD37BF2720F0}', // $guid
			],
		];
		$expected = [
			false,
			false,
			true,
			true
		];
		$this->runClassMethodGroup('isGuid', $params, $expected);
	}

/**
 * testGuidToString method
 *
 * @return void
 */
	public function testGuidToString() {
		$params = [
			[
				'', // $ADguid
			],
			[
				'c48f1ed0a65e7d43bdf71bd2005f9830', // $ADguid
			],
			[
				hex2bin('c48f1ed0a65e7d43bdf71bd2005f9830'), // $ADguid
			],
		];
		$expected = [
			'',
			'',
			'd01e8fc4-5ea6-437d-bdf7-1bd2005f9830',
		];
		$this->runClassMethodGroup('GuidToString', $params, $expected);
	}

/**
 * testGuidStringToLdap method
 *
 * @return void
 */
	public function testGuidStringToLdap() {
		$params = [
			[
				'', // $guid
			],
			[
				'{008f7a163f-cfde-43a2-acd5-778531d3bc62}', // $guid
			],
			[
				'{8f7a163f-cfde-43a2-acd5-778531d3bc62}', // $guid
			],
		];
		$expected = [
			'',
			'',
			'\3F\16\7A\8F\DE\CF\A2\43\AC\D5\77\85\31\D3\BC\62',
		];
		$this->runClassMethodGroup('GuidStringToLdap', $params, $expected);
	}

/**
 * testMbStrPad method
 *
 * @return void
 */
	public function testMbStrPad() {
		$params = [
			[
				'', // $str
				1, // $padLen
				'*', // $padStr
				STR_PAD_RIGHT, // $dir
			],
			[
				'test', // $str
				3, // $padLen
				'', // $padStr
				STR_PAD_RIGHT, // $dir
			],
			[
				'test', // $str
				3, // $padLen
				'*', // $padStr
				STR_PAD_RIGHT, // $dir
			],
			[
				'test', // $str
				6, // $padLen
				'*', // $padStr
				STR_PAD_RIGHT, // $dir
			],
			[
				'test', // $str
				6, // $padLen
				'*', // $padStr
				STR_PAD_LEFT, // $dir
			],
			[
				'test', // $str
				8, // $padLen
				'*', // $padStr
				STR_PAD_BOTH, // $dir
			],
		];
		$expected = [
			'',
			'test',
			'test',
			'test**',
			'**test',
			'**test**'
		];
		$this->runClassMethodGroup('mb_str_pad', $params, $expected);
	}

/**
 * testUnichr method
 *
 * @return void
 */
	public function testUnichr() {
		$params = [
			[
				32, // $u
			],
			[
				1025, // $u
			],
		];
		$expected = [
			' ',
			'Ё',
		];
		$this->runClassMethodGroup('unichr', $params, $expected);
	}

/**
 * testIsAssoc method
 *
 * @return void
 */
	public function testIsAssoc() {
		$params = [
			[
				[
					2 => 'value',
					'test' => 'some value',
				], // $arr
			],
			[
				[
					0 => 'value',
					2 => 'some value',
					3 => null,
				], // $arr
			],
			[
				[
					0 => 'value',
					1 => 'some value',
					2 => null,
				], // $arr
			],
		];
		$expected = [
			true,
			true,
			false,
		];
		$this->runClassMethodGroup('isAssoc', $params, $expected);
	}

/**
 * testIsBinary method
 *
 * @return void
 */
	public function testIsBinary() {
		$params = [
			[
				'', // $str
			],
			[
				'2375fe', // $str
			],
			[
				hex2bin('558c51ddce355c4a85c5b5fb762220bf'), // $str
			],
		];
		$expected = [
			false,
			false,
			true,
		];
		$this->runClassMethodGroup('isBinary', $params, $expected);
	}
}
