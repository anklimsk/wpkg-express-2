<?php
/**
 * This file is the helper file of the application.
 * Check information helper.
 * Methods to make check data more readable.
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
App::uses('RenderCheckData', 'Utility');

/**
 * Check information helper used to make check
 *  data more readable.
 *
 * @package app.View.Helper
 */
class CheckHelper extends AppHelper {

/**
 * Return information of check.
 *
 * @param array $data Data of check for processing
 * @return string Return information of check.
 */
	public function getLabelCondition($data = []) {
		return RenderCheckData::getTextCheckCondition($data);
	}
}
