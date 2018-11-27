<?php
/**
 * This file is the view file of the application. Used to render
 *  table of deleted data.
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

if (!isset($garbages)) {
	$garbages = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

if (!isset($listTypes)) {
	$listTypes = [];
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'Garbage.id' => [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action hide-popup',
			'not-use-input' => true
		],
		'Garbage.ref_type' => [
			'label' => __('Type'),
			'options' => $listTypes
		],
		'Garbage.name' => [
			'label' => __('Name'),
		],
		'Garbage.modified' => [
			'label' => __('Last modified'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
<?php if (!empty($garbages) && $usePost) : ?>
		<tfoot class="hide-popup">
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
foreach ($garbages as $garbage) {
	$tableRow = [];
	$attrRow = [];
	$garbageName = h($garbage['Garbage']['name']);
	$actions = $this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => 'garbage', 'action' => 'preview', $garbage['Garbage']['id']],
			[
				'title' => __('Preview XML'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-file-download',
			'btn-info',
			['controller' => 'garbage', 'action' => 'download', $garbage['Garbage']['id'], 'ext' => 'xml'],
			[
				'title' => __('Download XML file'),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-undo-alt',
			'btn-warning',
			['controller' => 'garbage', 'action' => 'restore', $garbage['Garbage']['id']],
			[
				'title' => __('Restore data from recycle bin'),
				'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to restore data \'%s\' from recycle bin?', $garbageName),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'garbage', 'action' => 'delete', $garbage['Garbage']['id']],
			[
				'title' => __('Delete data'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete data \'%s\' from recycle bin?', $garbageName),
			]
		);
	$tableRow[] = [
		$this->Filter->createFilterRowCheckbox('Garbage.id', $garbage['Garbage']['id']),
		['class' => 'action text-center hide-popup']
	];
	$tableRow[] = __d('garbage', h($garbage['GarbageType']['name']));
	$tableRow[] = h($garbage['Garbage']['name']);
	$tableRow[] = $this->ViewExtension->timeAgo($garbage['Garbage']['modified']);
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
