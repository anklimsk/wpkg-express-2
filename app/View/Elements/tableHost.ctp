<?php
/**
 * This file is the view file of the application. Used to render
 *  table of host.
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

if (!isset($profiles)) {
	$profiles = [];
}

if (!isset($groupActions)) {
	$groupActions = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

$dataUrl = $this->Html->url(['controller' => 'hosts', 'action' => 'drop', 'ext' => 'json']);
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<div data-toggle="draggable" data-url="<?php echo $dataUrl; ?>">
		<table class="table table-hover table-striped table-condensed">
			<thead>
<?php
	$formInputs = [
		'Host.id' => [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action',
			'not-use-input' => true
		],
		'Host.lft' => [
			'label' => __('Position'),
			'class-header' => 'action',
			'not-use-input' => true,
		],
		'Host.enabled' => [
			'label' => __('Enabled'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Host.template' => [
			'label' => __('Template'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Host.id_text' => [
			'label' => __('Host ID'),
		],
		'MainProfile.id_text' => [
			'label' => __('Main profile'),
		],
		'Host.notes' => [
			'label' => __('Notes'),
		],
		'Host.modified' => [
			'label' => __('Last modified'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
			</thead>
<?php if (!empty($hosts) && $usePost) : ?>
			<tfoot>
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
			</tfoot>
<?php endif; ?>
			<tbody>
<?php
foreach ($hosts as $host) {
	$tableRow = [];
	$attrRow = ['data-id' => $host['Host']['id']];
	$idMoveControls = uniqid('move_controls_');
	$hostState = $host['Host']['enabled'];
	$hostName = h($host['Host']['id_text']);
	$mainProfileName = h($host['MainProfile']['id_text']);
	if (!$host['MainProfile']['enabled']) {
		$mainProfileName = $this->Html->tag('s', $mainProfileName);
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
			$this->ViewExtension->buttonsMove(['controller' => 'hosts', 'action' => 'move', $host['Host']['id']]),
			[
				'id' => $idMoveControls,
				'class' => 'collapse collapse-display-inline'
			]
		) .
		$this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => 'hosts', 'action' => 'preview', $host['Host']['id']],
			[
				'title' => __('Preview XML'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-file-download',
			'btn-info',
			['controller' => 'hosts', 'action' => 'download', $host['Host']['id'], 'ext' => 'xml'],
			[
				'title' => __('Download XML file'),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-project-diagram',
			'btn-info',
			['controller' => 'graph', 'action' => 'view', GRAPH_TYPE_HOST, $host['Host']['id']],
			[
				'title' => __('Graph of host'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		);
		if ($host['Host']['template']) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-plus-square',
				'btn-warning',
				['controller' => 'hosts', 'action' => 'create', $host['Host']['id']],
				[
					'title' => __('Create host from this template'),
					'action-type' => 'modal',
				]
			);
		}
		$actions .= $this->ViewExtension->buttonLink(
			'fas fa-copy',
			'btn-warning',
			['controller' => 'hosts', 'action' => 'copy', $host['Host']['id']],
			[
				'title' => __('Copy host'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to copy host \'%s\'?', $hostName),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-pencil-alt',
			'btn-warning',
			['controller' => 'hosts', 'action' => 'edit', $host['Host']['id']],
			[
				'title' => __('Edit host'),
				'action-type' => 'modal',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'hosts', 'action' => 'delete', $host['Host']['id']],
			[
				'title' => __('Delete host'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete host \'%s\'?', $hostName),
			]
		);

	if (!$hostState) {
		$attrRow['class'] = 'warning';
	} elseif ($host['Host']['template']) {
		$attrRow['class'] = 'info';
	}
	$tableRow[] = [
		$this->Filter->createFilterRowCheckbox('Host.id', $host['Host']['id']),
		['class' => 'action text-center']
	];
	$tableRow[] = [$this->Number->format(
		($host['Host']['lft'] + 1) / 2,
		['thousands' => ' ', 'before' => '', 'places' => 0]
	), ['class' => 'text-center']];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($host['Host']['enabled']),
			['controller' => 'hosts', 'action' => 'enabled', $host['Host']['id'], !$host['Host']['enabled']],
			['title' => ($host['Host']['enabled'] ? __('Disable host') : __('Enable host'))]
		),
		['class' => 'text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($host['Host']['template']),
			['controller' => 'hosts', 'action' => 'template', $host['Host']['id'], !$host['Host']['template']],
			['title' => ($host['Host']['template'] ? __('Don\'t use as template') : __('Use as template'))]
		),
		['class' => 'text-center']
	];
	if (!$hostState) {
		$hostName = $this->Html->tag('s', $hostName);
	}
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$hostName,
		['controller' => 'hosts', 'action' => 'view', $host['Host']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	);
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$mainProfileName,
		['controller' => 'profiles', 'action' => 'view', $host['MainProfile']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	);
	$tableRow[] = $this->ViewExtension->showEmpty(
		$host['Host']['notes'],
		$this->ViewExtension->truncateText(h($host['Host']['notes']), 50)
	);
	$tableRow[] = $this->ViewExtension->timeAgo($host['Host']['modified']);
	$tableRow[] = [$actions, ['class' => 'action text-right']];

	echo $this->Html->tableCells([$tableRow], $attrRow, $attrRow);
}
?>
			</tbody>
		</table>
	</div>
<?php
	echo $this->Filter->closeFilterForm();
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
?>
</div>
<?php
	echo $this->ViewExtension->buttonsPaging();
