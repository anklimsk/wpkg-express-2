<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  list or tree of subordinate employees
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	$this->loadHelper('CakeTheme.ViewExtension');
	$this->loadHelper('Tools.Tree');

if (!isset($subordinate)) {
	$subordinate = [];
}

if (!isset($draggable)) {
	$draggable = false;
}

if (!isset($expandAll)) {
	$expandAll = false;
}

if (!isset($dropUrl)) {
	$dropUrl = '/cake_ldap/employees/drop.json';
}

if (empty($subordinate)) {
	echo $this->ViewExtension->showEmpty('');

	return;
}

if (!Hash::check($subordinate, '{n}.children')) {
	$subordinateList = [];
	foreach ($subordinate as $subordinateItem) {
		$subordinateList[] = $this->element('infoEmployeeShort', ['employee' => ['Employee' => $subordinateItem]]);
	}
	echo $this->Html->nestedList($subordinateList, ['class' => 'list-unstyled list-compact'], [], 'ul');

	return;
}

	$expandClass = '';
if ($expandAll) {
	$expandClass = ' bonsai-expand-all';
}

	$treeOptions = [
		'class' => 'bonsai-treeview' . $expandClass,
		'model' => 'SubordinateDb',
		'id' => 'employee-tree'
	];
	$treeWrapOptions = [
		'data-url' => $dropUrl,
		'data-nested' => 'true',
		'data-change-parent' => 'false',
		'data-toggle' => 'draggable',

	];
	$elementName = 'CakeLdap.treeItemEmployeeFull';
	if ($draggable) {
		$elementName = 'CakeLdap.treeItemEmployeeFullDraggable';
		if ($this->elementExists('treeItemEmployeeFullDraggable')) {
			$elementName = 'treeItemEmployeeFullDraggable';
		}
	} else {
		if ($this->elementExists('treeItemEmployeeFull')) {
			$elementName = 'treeItemEmployeeFull';
		}
	}
	$treeOptions['element'] = $elementName;

	$treeSubordinate = $this->Tree->generate($subordinate, $treeOptions);
	if ($draggable) {
		$treeSubordinate = $this->Html->div(null, $treeSubordinate, $treeWrapOptions);
	}
	echo $treeSubordinate;
