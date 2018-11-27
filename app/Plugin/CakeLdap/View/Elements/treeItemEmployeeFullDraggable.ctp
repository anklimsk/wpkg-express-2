<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  item of tree subordinate employees with buttons for move this item.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	$employeeInfo = $this->element('CakeLdap.treeItemEmployeeFull', compact('data'));
	$idControls = uniqid('controls_');

	$url = ['controller' => 'employees', 'action' => 'move', $data['SubordinateDb']['id']];
	$actions = $this->ViewExtension->button(
		'far fa-caret-square-right',
		'btn-default',
		[
			'title' => __d('cake_ldap', 'Show or hide buttons'),
			'data-toggle' => 'collapse', 'data-target' => '#' . $idControls,
			'data-toggle-icons' => 'fa-caret-square-right,fa-caret-square-left',
			'aria-expanded' => 'false'
		]
	) .
	$this->Html->tag(
		'span',
		$this->ViewExtension->buttonsMove($url),
		[
			'id' => $idControls,
			'class' => 'collapse collapse-display-inline'
		]
	);
	$employeeInfo = $this->Html->tag('span', $employeeInfo) . '&nbsp;' . $this->Html->tag('span', $actions, ['class' => 'action hide-popup']);

	echo $employeeInfo;
