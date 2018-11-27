<?php
/**
 * This file is global basic functions file of the application.
 *
 * CakeBasicFunctions: Basic global utilities for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Vendor
 */

if (!function_exists('constsToWords')) {

/**
 * Return array of constants value with name in format:
 *  - `key` constant value;
 *  - `value` constant name.
 *
 * @param string $prefix Prefix of constans.
 * @param int $skipWords If false, don't skip words. If integer,
 *  skip $skipWords + 1 words in result
 * @return array Constants value with name
 * @copyright Copyright 2009 Brian White
 */
	function constsToWords($prefix, $skipWords = false) {
		$constants = [];
		foreach (get_defined_constants() as $k => $v) {
			if (($pos = stristr($k, $prefix)) === false) {
				continue;
			}
			if (strpos($k, $pos) === 0) {
				$keylower = strtolower(substr($k, strlen($prefix)));
				if ($skipWords !== false) {
					$keylower = implode("_", array_slice(explode("_", $keylower), $skipWords + 1));
				}
				if (substr($keylower, 0, 1) === '_') {
					$keylower = substr($keylower, 1);
				}
				$constants[constant($k)] = mb_ucfirst(str_replace("_", " ", $keylower));
			}
		}

		return $constants;
	}

}

if (!function_exists('constsVals')) {

/**
 * Return array of constants value
 *
 * @param string $prefix Prefix of constans.
 * @return array Constants value
 * @copyright Copyright 2009 Brian White
 */
	function constsVals($prefix) {
		$constants = [];
		foreach (get_defined_constants() as $k => $v) {
			if (($pos = stristr($k, $prefix)) === false) {
				continue;
			}

			if (strpos($k, $pos) === 0) {
				$constants[] = constant($k);
			}
		}

		return $constants;
	}

}

if (!function_exists('constValToLcSingle')) {

/**
 * Return constant name
 *
 * @param string $prefix Prefix of constant.
 * @param mixed $val Value of constant.
 * @param bool|string $keepUnderscore If false, don't keep underscore.
 *  If string, replace underscore to this.
 * @param bool|int $skipWords If false, don't skip words. If integer,
 *  skip $skipWords + 1 words in result
 * @param bool $useUnknown If true, return string `Unknown`, null otherwise.
 * @return mixed String name of constant, or null otherwise. See param $useUnknown.
 * @copyright Copyright 2009 Brian White
 */
	function constValToLcSingle($prefix, $val, $keepUnderscore = false, $skipWords = false, $useUnknown = true) {
		$name = ($useUnknown ? __d('cake_basic_functions', 'Unknown') : null);
		foreach (get_defined_constants() as $k => $v) {
			if (($pos = stristr($k, $prefix)) === false) {
				continue;
			}

			if (strpos($k, $pos) === 0 && $v == $val) {
				$k = strtolower(substr($k, strlen($prefix)));
				if ($skipWords !== false) {
					$k = implode("_", array_slice(explode("_", $k), $skipWords + 1));
				}
				$name = $k;
				if (substr($name, 0, 1) === '_') {
					$name = substr($name, 1);
				}
				if ($keepUnderscore !== true) {
					$replace = ($keepUnderscore === false ? "" : $keepUnderscore);
					$name = str_replace("_", $replace, $name);
				}
				break;
			}
		}

		return $name;
	}

}

if (!function_exists('translArray')) {

/**
 * Return array with translated value
 *
 * @param array &$data Data to translate.
 * @param string $domain Domain name for translate.
 * @See Cake::__d() Allows you to override the current domain for a single
 *  message lookup.
 * @return bool Success
 */
	function translArray(array &$data = null, $domain = null) {
		if (empty($data) || !is_array($data) || empty($domain)) {
			return false;
		}

		return array_walk($data, create_function('&$item,$key,$domain_transl', '$item = __d($domain_transl, $item);'), (string)$domain);
	}

}

if (!function_exists('mb_ucfirst')) {

/**
 * Returns a string with the first character of str capitalized
 *
 * @param string $string The input string.
 * @param string $encoding The character encoding. If it is omitted, the internal character
 *  encoding value will be used.
 * @return string Returns the resulting string.
 * @author zneak
 * @link http://stackoverflow.com/a/2518021
 */
// @codingStandardsIgnoreStart
	function mb_ucfirst($string, $encoding = 'UTF-8') {
		// @codingStandardsIgnoreEnd
		if (empty($encoding)) {
			$encoding = 'UTF-8';
		}
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);

		return mb_strtoupper($firstChar, $encoding) . $then;
	}

}

if (!function_exists('isGuid')) {

/**
 * Checking whether the string is GUID
 *
 * @param string $guid String to check
 * @return bool Return True, if string is GUID. Otherwise return False.
 */
	function isGuid($guid) {
		if (empty($guid)) {
			return false;
		}

		return (bool)preg_match('/^\{?[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}\}?$/i', (string)$guid);
	}

}

if (!function_exists('GuidToString')) {

/**
 * Return readable GUID from binary string
 *
 * @param string $ADguid GUID as binary string.
 * @return string Return formatted GUID without brackets.
 * @author Antti Hurme
 * @link https://www.null-byte.org/development/php-active-directory-ldap-authentication/
 */
// @codingStandardsIgnoreStart
	function GuidToString($ADguid) {
		// @codingStandardsIgnoreEnd
		$guid = "";
		if (empty($ADguid)) {
			return $guid;
		}
		// Detect if string is binary
		if (!isBinary($ADguid)) {
			return $guid;
		}

		$ADguid = bin2hex($ADguid);
		$guidinhex = str_split($ADguid, 2);
		//Take the first 4 octets and reverse their order
		$first = array_reverse(array_slice($guidinhex, 0, 4));
		foreach ($first as $value) {
			$guid .= $value;
		}
		$guid .= "-";
		// Take the next two octets and reverse their order
		$second = array_reverse(array_slice($guidinhex, 4, 2, true), true);
		foreach ($second as $value) {
			$guid .= $value;
		}
		$guid .= "-";
		// Repeat for the next two
		$third = array_reverse(array_slice($guidinhex, 6, 2, true), true);
		foreach ($third as $value) {
			$guid .= $value;
		}
		$guid .= "-";
		// Take the next two but do not reverse
		$fourth = array_slice($guidinhex, 8, 2, true);
		foreach ($fourth as $value) {
			$guid .= $value;
		}
		$guid .= "-";
		//Take the last part
		$last = array_slice($guidinhex, 10, 16, true);
		foreach ($last as $value) {
			$guid .= $value;
		}

		return $guid;
	}

}

if (!function_exists('GuidStringToLdap')) {

/**
 * Return GUID for LDAP query from readable GUID string
 *
 * @param string $guid GUID as string.
 * @return string Return GUID as a hex string with escape character.
 * @see GuidToString()
 */
// @codingStandardsIgnoreStart
	function GuidStringToLdap($guid) {
		// @codingStandardsIgnoreEnd
		$ADguid = '';
		if (empty($guid)) {
			return $ADguid;
		}

		$guid = strtoupper($guid);
		if (!isGuid($guid)) {
			return $ADguid;
		}

		$guid = rtrim($guid, '}');
		$guid = ltrim($guid, '{');
		$guidinhex = str_split(str_replace('-', '', $guid), 2);
		//Take the first 4 octets and reverse their order
		$first = array_reverse(array_slice($guidinhex, 0, 4));
		foreach ($first as $value) {
			$ADguid .= '\\' . $value;
		}

		// Take the next two octets and reverse their order
		$second = array_reverse(array_slice($guidinhex, 4, 2, true), true);
		foreach ($second as $value) {
			$ADguid .= '\\' . $value;
		}

		// Repeat for the next two
		$third = array_reverse(array_slice($guidinhex, 6, 2, true), true);
		foreach ($third as $value) {
			$ADguid .= '\\' . $value;
		}

		// Take the next two but do not reverse
		$fourth = array_slice($guidinhex, 8, 2, true);
		foreach ($fourth as $value) {
			$ADguid .= '\\' . $value;
		}

		//Take the last part
		$last = array_slice($guidinhex, 10, 16, true);
		foreach ($last as $value) {
			$ADguid .= '\\' . $value;
		}

		return $ADguid;
	}

}

if (!function_exists('mb_str_pad')) {

/**
 * Analog of PHP function `str_pad` for using unicode
 *  Pad a string to a certain length with another string
 *
 * @param string $str The input string.
 * @param int $padLen If the value of pad_length is negative,
 *  less than, or equal to the length of the input string, no padding takes
 *  place, and input will be returned.
 * @param string $padStr The symbol for addition to the length.
 * @param int $dir Direction for additions.
 * @return string Returns the padded string.
 * @author K-Gun
 * @link http://stackoverflow.com/a/14773775
 */
// @codingStandardsIgnoreStart
	function mb_str_pad($str, $padLen, $padStr = ' ', $dir = STR_PAD_RIGHT) {
		// @codingStandardsIgnoreEnd
		$strLen = mb_strlen($str);
		$padStrLen = mb_strlen($padStr);
		if (!$strLen && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
			$strLen = 1; // @debug
		}
		if (!$padLen || !$padStrLen || $padLen <= $strLen) {
			return $str;
		}

		$result = null;
		if ($dir == STR_PAD_BOTH) {
			$length = ($padLen - $strLen) / 2;
			$repeat = ceil($length / $padStrLen);
			$result = mb_substr(str_repeat($padStr, $repeat), 0, floor($length)) .
				$str .
				mb_substr(str_repeat($padStr, $repeat), 0, ceil($length));
		} else {
			$repeat = ceil($strLen - $padStrLen + $padLen);
			if ($dir == STR_PAD_RIGHT) {
				$result = $str . str_repeat($padStr, $repeat);
				$result = mb_substr($result, 0, $padLen);
			} elseif ($dir == STR_PAD_LEFT) {
				$result = str_repeat($padStr, $repeat);
				$result = mb_substr(
					$result,
					0,
					$padLen - (($strLen - $padStrLen) + $padStrLen)
				) .
					$str;
			}
		}

		return $result;
	}

}

if (!function_exists('unichr')) {

/**
 * Return unicode char by its code
 *
 * @param int $u Code of char
 * @return char Return unicode char.
 * @author Madara Uchiha
 * @link http://stackoverflow.com/a/9878531
 */
	function unichr($u) {
		return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
	}

}

if (!function_exists('isAssoc')) {

/**
 * Checking whether the array is zero-indexed and sequential
 *
 * @param array $arr Array to check
 * @return bool Return True, if array is associated. Otherwise return False.
 * @author zanderwar
 * @link http://stackoverflow.com/a/173479
 */
	function isAssoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

}

if (!function_exists('isBinary')) {

/**
 * Checking string is binary
 *
 * @param string $str String to check
 * @return bool Return True, if string is binary. Otherwise return False.
 * @author mpen
 * @link http://stackoverflow.com/a/25344979
 */
	function isBinary($str) {
		return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
	}

}
