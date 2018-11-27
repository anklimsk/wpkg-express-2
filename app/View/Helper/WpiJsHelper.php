<?php
/**
 * This file is the helper file of the application.
 * WPI information helper.
 * Methods to prepare data for WPI configuration.
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
 * @package app.View.Helper
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Methods to prepare data for WPI configuration.
 *
 * @package app.View.Helper
 */
class WpiJsHelper extends AppHelper {

/**
 * Return list for WPI configuration.
 *
 * @param array $data Data for processing
 * @return string Return list for WPI configuration.
 */
	public function toList($data = null) {
		$result = '';
		if (empty($data)) {
			return $result;
		}

		$result = $this->toString(implode('\',\'', (array)$data));
		return $result;
	}

/**
 * Return boolean for WPI configuration.
 *
 * @param array $data Data for processing
 * @return string Return boolean for WPI configuration.
 */
	public function toBool($data = null) {
		$result = 'no';
		if ($data) {
			$result = 'yes';
		}
		$result = $this->toString($result);

		return $result;
	}

/**
 * Return string for WPI configuration.
 *
 * @param array $data Data for processing
 * @return string Return string for WPI configuration.
 */
	public function toString($data = null) {
		$result = '';
		if (!empty($data)) {
			$result = '\'' . $data . '\'';
		}

		return $result;
	}

/**
 * Return integer for WPI configuration.
 *
 * @param array $data Data for processing
 * @return string Return integer for WPI configuration.
 */
	public function toInt($data = null) {
		$result = (int)$data;

		return $result;
	}
}
