<?php
/**
 * This file is the view file of the application. Used to render
 *  list of XML files for downloads.
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

	if (!isset($listExport)) {
		$listExport = [];
	}

	$list = [];
	foreach ($listExport as $controller => $name) {
		$actions = $this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => $controller, 'action' => 'index', 'ext' => 'xml'],
			[
				'title' => __('View XML'),
				'target' => '_blank'
			]
		);
		$list[] = $this->Html->tag('strong', h($name)) . '&nbsp;' .
			$this->Html->tag('span', $actions, ['class' => 'action']);
	}
	echo $this->ViewExtension->showEmpty($list, $this->Html->nestedList($list, ['class' => 'list-inline'], [], 'ul'));
