<?php
/**
 * This file is the helper file of the application.
 * Indicator helper.
 * Indicator helper helper used to create indicator of state.
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
 * Indicator helper helper used to create indicator of state.
 *
 * @package app.View.Helper
 */
class IndicatorHelper extends AppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = [
		'Html',
	];

/**
 * Return indicator of state.
 *
 * @param mixed $condition Condition for creating indicator.
 * @param string $name Name of state
 * @param string $tooltip Tooltip for indicator
 * @return string Return information of check.
 */
	public function createIndicator($condition = false, $name = '', $tooltip = '') {
		$result = '';
		if (empty($condition) || empty($name)) {
			return $result;
		}

		$optIndicator = [];
		if (!empty($tooltip)) {
			$optIndicator = [
				'class' => 'help text-success',
				'data-toggle' => 'title',
				'title' => $tooltip
			];
		}
		$result = ' [' .
			$this->Html->tag(
				'samp',
				h($name),
				$optIndicator
			) . ']';

		return $result;
	}

}
