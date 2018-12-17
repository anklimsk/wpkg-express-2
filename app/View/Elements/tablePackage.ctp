<?php
/**
 * This file is the view file of the application. Used to render
 *  table of packages.
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

if (!isset($packages)) {
	$packages = [];
}

if (!isset($groupActions)) {
	$groupActions = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

if (!isset($listReboot)) {
	$listReboot = [];
}

if (!isset($listExecute)) {
	$listExecute = [];
}

if (!isset($listNotify)) {
	$listNotify = [];
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'Package.id' => [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action',
			'not-use-input' => true
		],
		'Package.enabled' => [
			'label' => __('Enabled'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Package.template' => [
			'label' => __('Template'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Package.id_text' => [
			'label' => __('Package ID'),
		],
		'Package.name' => [
			'label' => __('Name'),
		],
		'Package.revision' => [
			'label' => __('Revision'),
		],
		'Package.priority' => [
			'label' => __('Priority'),
		],
		'Package.reboot_id' => [
			'label' => __('Reboot'),
			'options' => $listReboot,
		],
		'Package.execute_id' => [
			'label' => __('Execute'),
			'options' => $listExecute,
		],
		'Package.notify_id' => [
			'label' => __('Notify'),
			'options' => $listNotify,
		],
		'Package.notes' => [
			'label' => __('Notes'),
		],
		'Package.modified' => [
			'label' => __('Last modified'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
<?php if (!empty($packages) && $usePost) : ?>
		<tfoot>
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
foreach ($packages as $package) {
	$tableRow = [];
	$attrRow = [];
	$packageState = $package['Package']['enabled'];
	$packageName = h($package['Package']['id_text']);
	$actions = $this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => 'packages', 'action' => 'preview', $package['Package']['id']],
			[
				'title' => __('Preview XML'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-file-download',
			'btn-info',
			['controller' => 'packages', 'action' => 'download', $package['Package']['id'], 'ext' => 'xml'],
			[
				'title' => __('Download XML file'),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-archive',
			'btn-info',
			['controller' => 'archives', 'action' => 'view', $package['Package']['id']],
			[
				'title' => __('Archive of package'),
				'target' => '_blank'
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-project-diagram',
			'btn-info',
			['controller' => 'graph', 'action' => 'view', GRAPH_TYPE_PACKAGE, $package['Package']['id']],
			[
				'title' => __('Graph of package'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		);
		if ($package['Package']['template']) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-plus-square',
				'btn-warning',
				['controller' => 'packages', 'action' => 'create', $package['Package']['id']],
				[
					'title' => __('Create package from this template'),
					'action-type' => 'modal',
				]
			);
		}
		$actions .= $this->ViewExtension->buttonLink(
			'fas fa-copy',
			'btn-warning',
			['controller' => 'packages', 'action' => 'copy', $package['Package']['id']],
			[
				'title' => __('Copy package'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to copy package \'%s\'?', $packageName),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-pencil-alt',
			'btn-warning',
			['controller' => 'packages', 'action' => 'edit', $package['Package']['id']],
			[
				'title' => __('Edit package'),
				'action-type' => 'modal',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'packages', 'action' => 'delete', $package['Package']['id']],
			[
				'title' => __('Delete package'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete package \'%s\'?', $packageName),
			]
		);

	if (!$packageState) {
		$attrRow['class'] = 'warning';
	} elseif ($package['Package']['template']) {
		$attrRow['class'] = 'info';
	}
	$tableRow[] = [
		$this->Filter->createFilterRowCheckbox('Package.id', $package['Package']['id']),
		['class' => 'action text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($package['Package']['enabled']),
			['controller' => 'packages', 'action' => 'enabled', $package['Package']['id'], !$package['Package']['enabled']],
			['title' => ($package['Package']['enabled'] ? __('Disable package') : __('Enable package'))]
		),
		['class' => 'text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($package['Package']['template']),
			['controller' => 'packages', 'action' => 'template', $package['Package']['id'], !$package['Package']['template']],
			['title' => ($package['Package']['template'] ? __('Don\'t use as template') : __('Use as template'))]
		),
		['class' => 'text-center']
	];
	if (!$packageState) {
		$packageName = $this->Html->tag('s', $packageName);
	}
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$packageName,
		['controller' => 'packages', 'action' => 'view', $package['Package']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	);
	$tableRow[] = $this->ViewExtension->truncateText(h($package['Package']['name']), 30);
	$tableRow[] = [h($package['Package']['revision']), ['class' => 'text-center']];
	$tableRow[] = [$this->Number->format(
		$package['Package']['priority'],
		['thousands' => ' ', 'before' => '', 'places' => 0]) .
		(!empty($package['PackagePriority']['name']) ? ' (' . __d('package_priority', h($package['PackagePriority']['name'])) . ')' : ''),
		['class' => 'text-right text-nowrap']
	];
	$tableRow[] = [mb_ucfirst(__d('package_reboot', h($package['PackageRebootType']['name']))), ['class' => 'action text-center']];
	$tableRow[] = [mb_ucfirst(__d('package_execute', h($package['PackageExecuteType']['name']))), ['class' => 'action text-center']];
	$tableRow[] = [mb_ucfirst(__d('package_notify', h($package['PackageNotifyType']['name']))), ['class' => 'action text-center']];
	$tableRow[] = $this->ViewExtension->showEmpty(
		$package['Package']['notes'],
		$this->ViewExtension->truncateText(h($package['Package']['notes']), 50)
	);
	$tableRow[] = $this->ViewExtension->timeAgo($package['Package']['modified']);
	$tableRow[] = [$actions, ['class' => 'action text-right']];

	echo $this->Html->tableCells([$tableRow], $attrRow, $attrRow);
}
?>
		</tbody>
	</table>
<?php
	echo $this->Filter->closeFilterForm();
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
?>
</div>
<?php
	echo $this->ViewExtension->buttonsPaging();
