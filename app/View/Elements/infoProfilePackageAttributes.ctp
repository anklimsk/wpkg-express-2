<?php
/**
 * This file is the view file of the application. Used to render
 *  information about attributes of package in profile.
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
 * @package app.View.Elements
 */

App::uses('Hash', 'Utility');

	if (!isset($data)) {
		$data = [];
	}

	if (empty($data)) {
		return;
	}

	$displayData = [];
	$pathValues = [
		__('Instal.') => 'installdate',
		__('Uninst.') => 'uninstalldate',
	];
	foreach ($pathValues as $label => $path) {
		$itemVal = Hash::get($data, $path);
		if (empty($itemVal)) {
			continue;
		}
		$displayData[$label] = $this->ViewExtension->showEmpty(h($itemVal));
	}

	$result = '';
	foreach ($displayData as $label => $value) {
		$result .= (empty($result) ? '' : '; ') .
			$this->Html->tag('strong', $label . ':') . ' ' . $this->Html->tag('var', $this->Time->niceShort($value));
	}
	if (empty($result)) {
		return;
	}
	$result = '(' . $result . ')';

	echo $this->Html->tag('samp', $result);
