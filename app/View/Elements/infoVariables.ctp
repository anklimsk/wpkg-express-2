<?php
/**
 * This file is the view file of the application. Used to render
 *  information about variables.
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

if (!isset($variables)) {
	$variables = [];
}

if (!isset($refType)) {
	$refType = null;
}

if (!isset($refId)) {
	$refId = null;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($showShort)) {
	$showShort = false;
}

if (!isset($showBtnExpand)) {
	$showBtnExpand = false;
}

	$dropUrl = $this->Html->url(['controller' => 'variables', 'action' => 'drop', 'ext' => 'json']);
	$treeOptions = [
		'id' => uniqid('variable-list-')
	];
	$treeWrapOptions = [
		'data-url' => $dropUrl,
		'data-nested' => 'false',
		'data-change-parent' => 'false',
		'data-toggle' => 'draggable',
	];

	$list = '';
	foreach ($variables as $variable) {
		if (isset($variable['Variable'])) {
			$variable = array_merge($variable, $variable['Variable']);
			unset($variable['Variable']);
		}

		$variableName = $this->Html->tag('strong', h($variable['name'])) . ' = ' . $this->Html->tag('var', h($variable['value']));
		$varRefTypeName = constValToLcSingle('VARIABLE_TYPE_', $variable['ref_type']);
		$attrRefType = @constant('ATTRIBUTE_TYPE_' . strtoupper((string)$varRefTypeName));
		$idMoveControls = uniqid('move_controls_');

		$attributes = '';
		$checks = '';
		if (isset($variable['Attribute']) && !empty($variable['Attribute'])) {
			$attributes = ' ' . $this->element('infoAttributes', ['attributes' => $variable['Attribute'], 'displayInline' => true]);
		}
		if (isset($variable['Check']) && !empty($variable['Check'])) {
			$checks = $this->element('infoChecks', ['checks' => $variable['Check'], 'nest' => true, 'expandAll' => false]);
		}

		$actions = $this->ViewExtension->button(
				'far fa-caret-square-left',
				'btn-default',
				['title' => __('Show or hide move buttons'),
				'data-toggle' => 'collapse', 'data-target' => '#' . $idMoveControls,
				'data-toggle-icons' => 'fa-caret-square-left,fa-caret-square-right',
				'aria-expanded' => 'false']
			) .
			$this->Html->tag(
				'span',
				$this->ViewExtension->buttonsMove(['controller' => 'variables', 'action' => 'move', $variable['id']]),
				[
					'id' => $idMoveControls,
					'class' => 'collapse collapse-display-inline'
				]
			) .
			$this->ViewExtension->buttonLink(
				'fas fa-tasks',
				'btn-success',
				['controller' => 'attributes', 'action' => 'modify', $attrRefType, ATTRIBUTE_NODE_VARIABLE, $variable['id']],
				[
					'title' => __('Edit attributes'),
					'action-type' => 'modal',
				]
			);
			if ($variable['ref_type'] != VARIABLE_TYPE_CONFIG) {
				$actions .= $this->ViewExtension->buttonLink(
					'far fa-check-circle',
					'btn-success',
					['controller' => 'checks', 'action' => 'view', CHECK_PARENT_TYPE_VARIABLE, $variable['id']],
					[
						'title' => __('Edit checks'),
						'action-type' => 'modal',
						'data-modal-size' => 'lg',
					]
				);
			}
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-pencil-alt',
				'btn-warning',
				['controller' => 'variables', 'action' => 'edit', $variable['id']],
				[
					'title' => __('Edit variable'),
					'action-type' => 'modal',
				]
			) .
			$this->ViewExtension->buttonLink(
				'fas fa-trash-alt',
				'btn-danger',
				['controller' => 'variables', 'action' => 'delete', $variable['id']],
				[
					'title' => __('Delete variable'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete variable \'%s\'?', $variableName),
					'data-update-modal-content' => true,
				]
			);
		$row = $this->Html->div('variable', $this->Html->tag('span', $variableName . $attributes) . '&nbsp;' .
			$this->Html->tag('span', $actions, ['class' => 'action hide-popup'])) . $checks;
		$list .= $this->Html->tag('li', $row, ['data-id' => $variable['id']]);
	}

	$infoVariablesFull = '';
	if (!empty($refType) && !empty($refId)) {
		$actions = $this->ViewExtension->buttonLink(
			'fas fa-plus',
			'btn-success',
			['controller' => 'variables', 'action' => 'add', $refType, $refId],
			[
				'title' => __('Add variable'),
				'action-type' => 'modal',
			]
		);
		if (!empty($variables)) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-clipboard-check',
				'btn-info',
				['controller' => 'variables', 'action' => 'verify', $refType, $refId],
				[
					'title' => __('Verify state of list variables'),
					'action-type' => 'modal',
				]
			);
		}
		if ($showBtnExpand) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-expand-arrows-alt',
				'btn-info',
				['controller' => 'variables', 'action' => 'view', $refType, $refId],
				[
					'title' => __('Open in new window'),
					'action-type' => 'modal',
					'data-modal-size' => 'lg'
				]
			);
		}
		$infoVariablesFull .= $this->Html->div('action pull-right hide-popup', $actions);
	}
	$infoVariables = $this->ViewExtension->showEmpty($list, $this->Html->tag('ul', $list, ['class' => 'list-unstyled']));
	$infoVariablesFull .= $this->Html->div('pull-left', $this->Html->div(null, $infoVariables, $treeWrapOptions));

	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
	if ($showShort) {
		echo $infoVariablesFull;
		return;
	}
?>
<dl class="dl-horizontal dl-popup-modal">
<?php
	if (!empty($fullName)):
?>
	<dt><?php echo __('Variable type') . ':'; ?></dt>
	<dd><?php echo h($fullName); ?></dd>
<?php
	endif;
?>
	<dt><?php echo __('Variables') . ':'; ?></dt>
	<dd>
<?php
	echo $infoVariablesFull;
?>
	</dd>
</dl>
