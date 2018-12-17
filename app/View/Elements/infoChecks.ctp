<?php
/**
 * This file is the view file of the application. Used to render
 *  information about checks.
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

if (!isset($checks)) {
	$checks = [];
}

if (!isset($nest)) {
	$nest = false;
}

if (!isset($expandAll)) {
	$expandAll = false;
}

if (!isset($draggable)) {
	$draggable = false;
}

if (!isset($dropUrl)) {
	$dropUrl = null;
}

$expandClass = '';
if ($expandAll) {
	$expandClass = ' bonsai-expand-all';
}

	$treeOptions = [
		'class' => 'bonsai-treeview' . $expandClass,
		'id' => uniqid('check-tree-')
	];
	$treeWrapOptions = [
		'data-url' => $dropUrl,
		'data-nested' => 'true',
		'data-change-parent' => 'true',
		'data-toggle' => 'draggable',

	];
	$model = 'Check';
	$elementName = 'treeItemCheck';
	if ($draggable) {
		$elementName = 'treeItemCheckDraggable';
	}
	$idPath = '{n}.Check.id';
	$parentPath = '{n}.Check.parent_id';
	if ($nest) {
		$model = false;
		$elementName .= 'Nest';
		$idPath = '{n}.id';
		$parentPath = '{n}.parent_id';
	}
	$checks = Hash::nest($checks, compact('idPath', 'parentPath'));
	$treeOptions['model'] = $model;
	$treeOptions['element'] = $elementName;
	$treeCheck = $this->Tree->generate($checks, $treeOptions);
	$treeCheck = $this->ViewExtension->showEmpty($treeCheck);
	if ($draggable) {
		$treeCheck = $this->Html->div(null, $treeCheck, $treeWrapOptions);
	}
	echo $treeCheck;