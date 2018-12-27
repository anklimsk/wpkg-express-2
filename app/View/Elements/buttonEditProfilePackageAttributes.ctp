<?php
/**
 * This file is the view file of the application. Used to render
 *  button for editing information about attributes of package in profile.
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
 * @package app.View.Elements
 */

App::uses('Hash', 'Utility');

	if (!isset($data)) {
		$data = [];
	}

	if (empty($data) || !isset($data['id']) ||
		empty($data['id'])) {
		return;
	}

	$result = $this->ViewExtension->buttonLink(
		'far fa-calendar-check',
		'btn-success',
		['controller' => 'profiles', 'action' => 'installdate', $data['id']],
		[
			'title' => __('Edit installation date'),
			'action-type' => 'modal'
		]
	);

	echo $result;
