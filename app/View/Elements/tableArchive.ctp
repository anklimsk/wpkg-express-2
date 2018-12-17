<?php
/**
 * This file is the view file of the application. Used to render
 *  table of archive packages.
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

if (!isset($archives)) {
	$archives = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($useGroupActions)) {
	$useGroupActions = true;
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<table class="table table-hover table-striped table-condensed">
<?php
	if (!empty($fullName)):
?>
		<caption><?php echo h($fullName); ?></caption>
<?php
	endif;
?>
		<thead>
<?php
	$formInputs = [];
	if ($useGroupActions) {
		$formInputs['Archive.id'] = [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action hide-popup',
			'not-use-input' => true
		];
	}
	$formInputs += [
		'Archive.name' => [
			'label' => __('Name'),
		],
		'Archive.revision' => [
			'label' => __('Revision'),
		],
		'Archive.modified' => [
			'label' => __('Last modified'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
<?php if (!empty($archives) && $usePost && $useGroupActions) : ?>
		<tfoot class="hide-popup">
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
foreach ($archives as $archive) {
	$tableRow = [];
	$attrRow = [];
	$archiveName = h($archive['Archive']['name']) . ' (' . h($archive['Archive']['revision']) . ')';
	$actions = $this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => 'archives', 'action' => 'preview', $archive['Archive']['id']],
			[
				'title' => __('Preview XML'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-file-download',
			'btn-info',
			['controller' => 'archives', 'action' => 'download', $archive['Archive']['id'], 'ext' => 'xml'],
			[
				'title' => __('Download XML file'),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-undo-alt',
			'btn-warning',
			['controller' => 'archives', 'action' => 'restore', $archive['Archive']['id']],
			[
				'title' => __('Restore package from archive'),
				'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to restore package from archive \'%s\'?', $archiveName),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'archives', 'action' => 'delete', $archive['Archive']['id']],
			[
				'title' => __('Delete package'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete package \'%s\' from archive?', $archiveName),
			]
		);

	if ($archive['Archive']['revision'] === $archive['Package']['revision']) {
		$attrRow['class'] = 'success';
	}
	if ($useGroupActions) {
		$tableRow[] = [
			$this->Filter->createFilterRowCheckbox('Archive.id', $archive['Archive']['id']),
			['class' => 'action text-center hide-popup']
		];
	}
	$tableRow[] = $this->ViewExtension->popupModalLink(
		h($archive['Archive']['name']),
		['controller' => 'packages', 'action' => 'view', $archive['Package']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	);
	$tableRow[] = [
		$this->ViewExtension->popupModalLink(
			h($archive['Archive']['revision']),
			['controller' => 'archives', 'action' => 'view', $archive['Package']['id']],
			[
				'data-modal-size' => 'lg',
				'data-popover-size' => 'lg'
			]
		),
		['class' => 'text-right text-nowrap']
	];
	$tableRow[] = $this->ViewExtension->timeAgo($archive['Archive']['modified']);
	$tableRow[] = [$actions, ['class' => 'action text-center hide-popup']];

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
