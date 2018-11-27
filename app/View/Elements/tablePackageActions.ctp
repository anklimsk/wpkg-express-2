<?php
/**
 * This file is the view file of the application. Used to render
 *  table of package actions.
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

if (!isset($packageActions)) {
	$packageActions = [];
}

if (!isset($refId)) {
	$refId = null;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($showBtnExpand)) {
	$showBtnExpand = false;
}

	$dataUrl = $this->Html->url(['controller' => 'actions', 'action' => 'drop', 'ext' => 'json']);

	$emptyText = $this->ViewExtension->showEmpty();
	$prefixActionsTab = 'actionTab';
	$listActions = [];
	$tableBodyActions = [];
	foreach ($packageActions as $packageAction) {
		if (isset($packageAction['PackageAction'])) {
			$packageAction = array_merge($packageAction, $packageAction['PackageAction']);
			unset($packageAction['PackageAction']);
		}

		$idMoveControls = uniqid('move_controls_');
		$attrRow = ['data-id' => $packageAction['id']];
		$actionTypeId = $packageAction['action_type_id'];
		$listActions[$actionTypeId] = $packageAction['PackageActionType']['name'];

		$attributes = '';
		$checks = '';
		if (isset($packageAction['Attribute']) && !empty($packageAction['Attribute'])) {
			$attributes = ' ' . $this->element('infoAttributes', ['attributes' => $packageAction['Attribute'], 'displayInline' => true]);
		}
		if (isset($packageAction['Check']) && !empty($packageAction['Check'])) {
			$checks = $this->element('infoChecks', ['checks' => $packageAction['Check'], 'nest' => true, 'expandAll' => false]);
		}

		if ($packageAction['command_type_id'] == ACTION_COMMAND_TYPE_INCLUDE) {
			$includeCommand = __('Unknown');
			if (isset($packageAction['IncludeAction']['name'])) {
				$includeCommand = __d('package_action_type', h($packageAction['IncludeAction']['name']));
			}
			$command = $this->Html->tag('samp', __('Includes the action: %s', h($includeCommand)));
			$timeOut = $emptyText;
			$exitCodes = $emptyText;
		} else {
			$command = $this->Html->tag('samp', h($packageAction['command']));
			if (!empty($packageAction['workdir'])) {
				$command .= ' (' . $this->Html->tag('var', h($packageAction['workdir'])) . ')';
			}

			if ($actionTypeId == ACTION_TYPE_DOWNLOAD) {
				if ($packageAction['timeout']) {
					$command .= ' ' . __('Expand URL');
				}
				$timeOut = $emptyText;
			} else {
				$timeOut = gmdate('H:i:s', $packageAction['timeout']);
			}
			$exitCodes = $this->element('infoExitCodes', ['exitCodes' => $packageAction['ExitCode'], 'showShort' => true]);
		}

		$tableRow = [
			$command . $attributes . $checks,
			$timeOut,
			$exitCodes,
			[
				$this->ViewExtension->button(
					'far fa-caret-square-left',
					'btn-default',
					['title' => __('Show or hide move buttons'),
					'data-toggle' => 'collapse', 'data-target' => '#' . $idMoveControls,
					'data-toggle-icons' => 'fa-caret-square-left,fa-caret-square-right',
					'aria-expanded' => 'false']
				) .
				$this->Html->tag(
					'span',
					$this->ViewExtension->buttonsMove(['controller' => 'actions', 'action' => 'move', $packageAction['id']]),
					[
						'id' => $idMoveControls,
						'class' => 'collapse collapse-display-inline'
					]
				) .
				$this->ViewExtension->buttonLink(
					'fas fa-tasks',
					'btn-success',
					['controller' => 'attributes', 'action' => 'modify', ATTRIBUTE_TYPE_PACKAGE, ATTRIBUTE_NODE_ACTION, $packageAction['id']],
					[
						'title' => __('Edit attributes'),
						'action-type' => 'modal',
					]
				) .
				$this->ViewExtension->buttonLink(
					'far fa-check-circle',
					'btn-success',
					['controller' => 'checks', 'action' => 'view', CHECK_PARENT_TYPE_ACTION, $packageAction['id']],
					[
						'title' => __('Edit checks'),
						'action-type' => 'modal',
						'data-modal-size' => 'lg'
					]
				) .
				$this->ViewExtension->buttonLink(
					'fas fa-external-link-alt',
					'btn-success',
					['controller' => 'exit_codes', 'action' => 'view', $packageAction['id']],
					[
						'title' => __('Edit exit codes'),
						'action-type' => 'modal',
					]
				) .
				$this->ViewExtension->buttonLink(
					'fas fa-pencil-alt',
					'btn-warning',
					['controller' => 'actions', 'action' => 'edit', $packageAction['id']],
					[
						'title' => __('Edit package action'),
						'action-type' => 'modal',
					]
				) .
				$this->ViewExtension->buttonLink(
					'fas fa-trash-alt',
					'btn-danger',
					['controller' => 'actions', 'action' => 'delete', $packageAction['id']],
					[
						'title' => __('Delete package action'), 'action-type' => 'confirm-post',
						'data-confirm-msg' => __('Are you sure you wish to delete package action \'%s\'?', h($packageAction['command'])),
						'data-update-modal-content' => true,
					]
				),
				['class' => 'action text-right hide-popup']
			],
		];
		$tableBodyActions[$actionTypeId][] = $this->Html->tableCells($tableRow, $attrRow, $attrRow);
	}

	$listTabs = '';
	$isActive = true;
	$listTabsOptions = ['role' => 'presentation'];
	$tabLinkOptions = ['data-toggle' => 'pill', 'role' => 'tab'];
	foreach ($listActions as $actionTypeId => $actionName) {
		$tabOptions = $listTabsOptions;
		if ($isActive) {
			$tabClass = 'active';
		} else {
			$tabClass = 'hide-popup';
		}
		$tabOptions['class'] = $tabClass;
		$linkOptions = $tabLinkOptions + ['aria-controls' => $prefixActionsTab . $actionTypeId, 'role' => 'tab', 'data-toggle' => 'tab'];
		$listTabs .= $this->Html->tag(
			'li',
			$this->Html->link(mb_ucfirst(__d('package_action_type', h($actionName))), '#' . $prefixActionsTab . $actionTypeId, $linkOptions),
			$tabOptions
		);
		$isActive = false;
	}
	echo $this->Html->div('pull-left',
		$this->ViewExtension->showEmpty(
			$listTabs,
			$this->Html->tag(
				'ul',
				$listTabs,
				[
					'class' => 'nav nav-pills',
					'role' => 'tablist'
				]
			)
		)
	);

	if (!empty($refId)) {
		$actions = $this->ViewExtension->buttonLink(
			'fas fa-plus',
			'btn-success',
			['controller' => 'actions', 'action' => 'add', $refId],
			[
				'title' => __('Add action'),
				'action-type' => 'modal',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-clipboard-list',
			'btn-success',
			['controller' => 'action_types', 'action' => 'index'],
			[
				'title' => __('Edit action types'),
				'action-type' => 'modal',
			]
		);
		if (!empty($packageActions)) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-clipboard-check',
				'btn-info',
				['controller' => 'actions', 'action' => 'verify', $refId],
				[
					'title' => __('Verify state of list actions'),
					'action-type' => 'modal',
				]
			);
		}
		if ($showBtnExpand) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-expand-arrows-alt',
				'btn-info',
				['controller' => 'actions', 'action' => 'view', $refId],
				[
					'title' => __('Open in new window'),
					'action-type' => 'modal',
					'data-modal-size' => 'lg'
				]
			);
		}
		echo $this->Html->div('action pull-right hide-popup', $actions);
	}

	$tableHeader = [
		__('Command, work directory'),
		[__('Timeout') => ['class' => 'action']],
		[__('Exit codes, reboot') => ['class' => 'action']],
		[__('Actions') => ['class' => 'action hide-popup']],
	];
?>
<div class="tab-content">
<?php
	$isActive = true;
	foreach ($tableBodyActions as $actionTypeId => $tableBodyItem):
?>
	<div role="tabpanel" class="tab-pane<?php echo ($isActive ? ' active' : ''); ?>" id="<?php echo $prefixActionsTab . $actionTypeId; ?>">
		<div data-toggle="draggable" data-url="<?php echo $dataUrl; ?>">
			<table class="table table-hover table-striped table-condensed">
<?php
		if (!empty($fullName)) {
			echo $this->Html->tag('caption', h($fullName));
		}
		echo $this->Html->tag('thead', $this->Html->tableHeaders($tableHeader));
		echo $this->Html->tag('tbody', implode('', $tableBodyItem));
?>
			</table>
		</div>
	</div>
<?php
		$isActive = false;
	endforeach;
?>
</div>
<?php
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
