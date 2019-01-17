<?php
/**
 * This file is the helper file of the application.
 * Time out information helper.
 * Methods to make time out data more readable.
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
 * Time out information helper used to make time out
 *  data more readable.
 *
 * @package app.View.Helper
 */
class TimeOutHelper extends AppHelper {

/**
 * Return information of time out.
 *
 * @param int|string $timeOut Time out for processing
 * @return string Return information of time out.
 */
	public function getTimeOut($timeOut = 0) {
		$result = '';
		$timeOut = (int)$timeOut;
		if ($timeOut <= 0) {
			return $result;
		}

		$infoTimeOut = [];
		$hours = floor($timeOut / 3600);
		$minutes = floor(($timeOut / 60) % 60);
		$seconds = $timeOut % 60;

		if ($hours > 0) {
			$infoTimeOut[] = __x('time out', '%dh', $hours);
		}
		if ($minutes > 0) {
			$infoTimeOut[] = __x('time out', '%dm', $minutes);
		}
		if ($seconds > 0) {
			$infoTimeOut[] = __x('time out', '%ds', $seconds);
		}
		$result = implode(' ', $infoTimeOut);

		return $result;
	}
}
