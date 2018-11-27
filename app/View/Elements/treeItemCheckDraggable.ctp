<?php
/**
 * This file is the view file of the application. Used to rendering
 *  item of tree checks with buttons for move this item.
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

	$checksInfo = $this->element('treeItemCheck', compact('data'));
	$idMoveControls = uniqid('move_controls_');

	$checkRefTypeName = constValToLcSingle('CHECK_PARENT_TYPE_', $data['Check']['ref_type']);
	$attrRefType = @constant('ATTRIBUTE_TYPE_' . strtoupper((string)$checkRefTypeName));
	$url = ['controller' => 'checks', 'action' => 'move', $data['Check']['id']];
	$checkName = $this->Check->getLabelCondition($data['Check']);
	$actions = $this->ViewExtension->button(
		'far fa-caret-square-right',
		'btn-default',
		['title' => __('Show or hide move buttons'),
		'data-toggle' => 'collapse', 'data-target' => '#' . $idMoveControls,
		'data-toggle-icons' => 'fa-caret-square-right,fa-caret-square-left',
		'aria-expanded' => 'false']
	) .
	$this->Html->tag(
		'span',
		$this->ViewExtension->buttonsMove($url),
		[
			'id' => $idMoveControls,
			'class' => 'collapse collapse-display-inline'
		]
	) .
	$this->ViewExtension->buttonLink(
		'fas fa-tasks',
		'btn-success',
		['controller' => 'attributes', 'action' => 'modify', $attrRefType, ATTRIBUTE_NODE_CHECK, $data['Check']['id']],
		[
			'title' => __('Edit attributes'),
			'action-type' => 'modal',
		]
	) .
	$this->ViewExtension->buttonLink(
		'fas fa-pencil-alt',
		'btn-warning',
		['controller' => 'checks', 'action' => 'edit', $data['Check']['id']],
		[
			'title' => __('Edit check'),
			'action-type' => 'modal',
		]
	) .
	$this->ViewExtension->buttonLink(
		'fas fa-trash-alt',
		'btn-danger',
		['controller' => 'checks', 'action' => 'delete', $data['Check']['id']],
		[
			'title' => __('Delete check'), 'action-type' => 'confirm-post',
			'data-confirm-msg' => __('Are you sure you wish to delete check \'%s\'?', h($checkName)) .
			(empty($data['children']) ? '' : $this->Html->tag('br') . __('Any children of this logical check will also be removed.')),
			'data-update-modal-content' => true,
		]
	);
	$checksInfo = $this->Html->tag('span', $checksInfo) . '&nbsp;' . $this->Html->tag('span', $actions, ['class' => 'action hide-popup']);

	echo $checksInfo;
