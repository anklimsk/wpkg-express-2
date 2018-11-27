<?php
/**
 * This file is the util library file of the plugin.
 * Methods get information of current UI language and convert
 *  language coge to new format.
 *
 * CakeBasicFunctions: Basic global utilities for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Vendor
 */

App::import(
	'Vendor',
	'CakeBasicFunctions.langcode-conv',
	['file' => 'langcode-conv' . DS . 'autoload.php']
);

/**
 * Language library.
 * Methods to get information of current UI language and convert
 *  language coge to new format.
 *
 * @package plugin.Lib.Utility
 */
class Language {

/**
 * Options object of converter
 *
 * @var options
 */
	public $options = null;

/**
 * Converter object
 *
 * @var options
 */
	public $converter = null;

/**
 * Constructor
 *
 */
	public function __construct() {
		$adapter = new \Conversio\Adapter\LanguageCode();
		$this->options = new \Conversio\Adapter\Options\LanguageCodeOptions();
		$this->converter = new \Conversio\Conversion($adapter);
		$this->converter->setAdapterOptions($this->options);
	}

/**
 * Get current UI language in format `ISO 639-1` or `ISO 693-2`
 *
 * @param bool $isTwoLetter Flag of using `ISO 639-1` if True.
 *  Use `ISO 693-2` otherwise.
 * @return string Current UI language
 */
	public function getCurrentUiLang($isTwoLetter = false) {
		$uiLcid3 = Configure::read('Config.language');
		if (empty($uiLcid3)) {
			$uiLcid3 = 'eng';
		}
		$uiLcid3 = mb_strtolower($uiLcid3);
		if (!$isTwoLetter) {
			return $uiLcid3;
		}

		return $this->convertLangCode($uiLcid3, 'iso639-1');
	}

/**
 * Convert language from format `ISO 693-2` to `ISO 639-1`
 *
 * @param string $langCode Languge code for converting
 * @param string $output Output format:
 *  - name: The international (often english) name of the language;
 *  - native: The language name written in native representation/s;
 *  - iso639-1: The ISO 639-1 (two-letters code) language representation;
 *  - iso639-2/t: The ISO 639-2/T (three-letters code for terminology applications) language representation;
 *  - iso639-2/b: The ISO 639-2/B (three-letters code, for bibliographic applications) language representation;
 *  - iso639-3: The ISO 639-3 (same as ISO 639-2/T except that for the macrolanguages) language representation.
 * @return string Language code in format `ISO 639-1`,
 *  or empty string on failure.
 * @link https://github.com/leodido/langcode-conv Language Codes Converter
 */
	public function convertLangCode($langCode = null, $output = null) {
		$result = '';
		if (empty($langCode) || empty($output)) {
			return $result;
		}

		$cachePath = 'lang_code_info_' . md5(serialize(func_get_args()));
		$cached = Cache::read($cachePath, CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE);
		if (!empty($cached)) {
			return $cached;
		}

		$langCode = mb_strtolower($langCode);
		$this->options->setOutput($output);
		$result = (string)$this->converter->filter($langCode);
		Cache::write($cachePath, $result, CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE);

		return $result;
	}

/**
 * Return current UI language name for library Tools.NumberTextLib in format RFC5646.
 *
 * @param string $langCode Languge code in format `ISO 639-1` or `ISO 639-2`
 * @return string Return language name in format RFC5646.
 * @link http://numbertext.org/ Universal number to text conversion languag
 */
	public function getLangForNumberText($langCode = null) {
		$cachePath = 'lang_code_number_name_' . md5(serialize(func_get_args()));
		$cached = Cache::read($cachePath, CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE);
		if (!empty($cached)) {
			return $cached;
		}

		if (!empty($langCode)) {
			$langUI = $this->convertLangCode($langCode, 'iso639-1');
		} else {
			$langUI = $this->getCurrentUiLang(true);
		}

		$result = 'en_US';
		$locales = [
			'af' => 'af_ZA',
			'ca' => 'ca_ES',
			'cs' => 'cs_CZ',
			'da' => 'da_DK',
			'de' => 'de_DE',
			'el' => 'el_EL',
			'en' => 'en_US',
			'es' => 'es_ES',
			'fi' => 'fi_FI',
			'fr' => 'fr_FR',
			'he' => 'he_IL',
			'hu' => 'hu_HU',
			'id' => 'id_ID',
			'it' => 'it_IT',
			'ja' => 'ja_JP',
			'ko' => 'ko_KR',
			'lb' => 'lb_LU',
			'lt' => 'lt_LT',
			'lv' => 'lv_LV',
			'nl' => 'nl_NL',
			'pl' => 'pl_PL',
			'pt' => 'pt_PT',
			'ro' => 'ro_RO',
			'ru' => 'ru_RU',
			'sh' => 'sh_RS',
			'sl' => 'sl_SI',
			'sr' => 'sr_RS',
			'sv' => 'sv_SE',
			'th' => 'th_TH',
			'tr' => 'tr_TR',
			'vi' => 'vi_VN',
			'zh' => 'zh_ZH',
		];
		if (isset($locales[$langUI])) {
			$result = $locales[$langUI];
		}

		Cache::write($cachePath, $result, CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE);

		return $result;
	}
}
