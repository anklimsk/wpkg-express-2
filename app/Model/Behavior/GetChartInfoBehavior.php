<?php
/**
 * This file is the behavior file of the application. Is used to
 *  process data for chart.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');

/**
 * The behavior is used to process data for chart.
 *
 * @package app.Model.Behavior
 */
class GetChartInfoBehavior extends ModelBehavior {

/**
 * Return data array for render chart
 *
 * @param Model $model Model using this behavior
 * @param int|string $id The ID of the package to retrieve data.
 * @return array Return data array for render chart
 */
	public function getChartData(Model $model, $id = null) {
		$result = [];

		return $result;
	}

/**
 * Return random background color of chart data
 *
 * @param Model $model Model using this behavior
 * @param int|string $min Minimum value of color
 * @param int|string $max Maximum value of color
 * @return string Return random background color
 */
	public function getRandomColor(Model $model, $min = 100, $max = 240) {
		$result = '';
		if (($min < 0) || ($min >= 255) ||
			($max < 0) || ($max > 255)) {
			return $result;
		}

		$result = '#';
		for ($i = 0; $i < 3; $i++) {
			$d = rand($min, $max);
			$result .= sprintf('%02x', $d);
		}

		return $result;
	}

/**
 * Return title for chart.
 *
 * @param Model $model Model using this behavior
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return string Return title for chart.
 */
	public function getChartTitle(Model $model, $refType = null, $refId = null) {
		$result = '';
		$typeName = $model->getFullName($refId, null, null, null, false);
		if (empty($typeName)) {
			return $result;
		}
		$result = __('Chart of the %s', $typeName);

		return $result;
	}

/**
 * Return the URL to use when clicking on the chart element.
 *
 * @param Model $model Model using this behavior
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return array Return array URL.
 */
	public function getChartClickUrl(Model $model, $refType = null, $refId = null) {
		$result = [];

		return $result;
	}
}
