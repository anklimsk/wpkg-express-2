<?php
/**
 * This file is the util file of the application.
 * CheckPcrePattern Utility.
 * Methods to check PCRE pattern.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Lib.Utility
 */

/**
 * PCRE pattern checking library.
 * Methods to check PCRE pattern.
 *
 * @package app.Lib.Utility
 */
class CheckPcrePattern {

/**
 * Flag of PCRE pattern error
 *
 * @var bool
 */
	protected static $_hasRegexError = false;

/**
 * Default error handler for setting error flag.
 *
 * @param int $code Code of error
 * @param string $description Error description
 * @param string $file File on which error occurred
 * @param int $line Line that triggered the error
 * @param array $context Context
 * @return bool true if error was handled
 */
	public static function handlerRegexError($code, $description, $file = null, $line = null, $context = null) {
		static::$_hasRegexError = true;

		return true;
	}

/**
 * PCRE pattern validation
 *
 * @param string $pattern PCRE pattern for check.
 * @return bool Return True, indicates the pattern is valid.
 */
	public static function checkPattern($pattern = null) {
		if (empty($pattern)) {
			return false;
		}

		static::$_hasRegexError = false;
		set_error_handler('CheckPcrePattern::handlerRegexError');
		$error = preg_match('/' . $pattern . '/', 'foo');
		restore_error_handler();

		if ((static::$_hasRegexError !== true) && ($error !== false)) {
			return true;
		}

		return false;
	}
}
