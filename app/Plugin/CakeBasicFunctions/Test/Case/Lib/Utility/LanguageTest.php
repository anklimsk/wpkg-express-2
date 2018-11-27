<?php
App::uses('AppCakeTestCase', 'CakeBasicFunctions.Test');
App::uses('Language', 'CakeBasicFunctions.Utility');

/**
 * PhoneNumberTest file
 *
 */
class LanguageTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = new Language();
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
 * testGetCurrentUiLang method
 *
 * @return void
 */
	public function testGetCurrentUiLang() {
		Configure::write('Config.language', 'tst');
		$params = [
			[
				false, // $isTwoLetter
			],
			[
				true, // $isTwoLetter
			],
		];
		$expected = [
			'tst',
			''
		];
		$this->runClassMethodGroup('getCurrentUiLang', $params, $expected);

		Configure::write('Config.language', 'eng');
		$params = [
			[
				false, // $isTwoLetter
			],
			[
				true, // $isTwoLetter
			],
		];
		$expected = [
			'eng',
			'en'
		];
		$this->runClassMethodGroup('getCurrentUiLang', $params, $expected);

		Configure::write('Config.language', 'rus');
		$params = [
			[
				false, // $isTwoLetter
			],
			[
				true, // $isTwoLetter
			],
		];
		$expected = [
			'rus',
			'ru'
		];
		$this->runClassMethodGroup('getCurrentUiLang', $params, $expected);
	}

/**
 * testConvertLangCode method
 *
 * @return void
 */
	public function testConvertLangCode() {
		$params = [
			[
				'', // $langCode
				'iso639-2/t', // $output
			],
			[
				'ru', // $langCode
				'iso639-2/t', // $output
			],
			[
				'bad', // $langCode
				'iso639-2/t', // $output
			],

		];
		$expected = [
			'',
			'rus',
			'',
		];
		$this->runClassMethodGroup('convertLangCode', $params, $expected);
	}

/**
 * testGetLangForNumberText method
 *
 * @return void
 */
	public function testGetLangForNumberText() {
		Configure::write('Config.language', 'rus');
		$params = [
			[
				'', // $language
			],
			[
				'de', // $langCode
			],
			[
				'fra', // $langCode
			],
			[
				'bad', // $langCode
			],
		];
		$expected = [
			'ru_RU',
			'de_DE',
			'fr_FR',
			'en_US'
		];
		$this->runClassMethodGroup('getLangForNumberText', $params, $expected);
	}
}
