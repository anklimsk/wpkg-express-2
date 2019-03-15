<?php
/**
 * This file is the view file of the application. Used to render
 *  table of WPI packages.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.View.Elements
 */

if (!isset($wpis)) {
	$wpis = [];
}

if (!isset($groupActions)) {
	$groupActions = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

if (!isset($listWpiCategories)) {
	$listWpiCategories = [];
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'Wpi.id' => [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action',
			'not-use-input' => true
		],
		'Package.id_text' => [
			'label' => __('Package ID'),
			'class-header' => 'fit',
			'style' => 'min-width: 180px'
		],
		'Package.name' => [
			'label' => __('Name'),
		],
		'Package.notes' => [
			'label' => __('Notes'),
		],
		'Wpi.category_id' => [
			'label' => __('Category'),
			'options' => $listWpiCategories,
			'class-header' => 'fit',
		],
		'Wpi.default' => [
			'label' => __('Default'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'fit',
		],
		'Wpi.force' => [
			'label' => __('Forcibly'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'fit',
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
<?php if (!empty($wpis) && $usePost) : ?>
		<tfoot>
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
foreach ($wpis as $wpiPackage) {
	$tableRow = [];
	$attrRow = [];
	$packageState = $wpiPackage['Package']['enabled'];
	$packageName = h($wpiPackage['Package']['id_text']);
	$actions = $this->ViewExtension->buttonLink(
			'fas fa-pencil-alt',
			'btn-warning',
			['controller' => 'wpi', 'action' => 'edit', $wpiPackage['Wpi']['id']],
			[
				'title' => __('Edit WPI package'),
				'action-type' => 'modal',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'wpi', 'action' => 'delete', $wpiPackage['Wpi']['id']],
			[
				'title' => __('Delete WPI package'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete WPI package \'%s\'?', $packageName),
			]
		);

	if (!$packageState) {
		$attrRow['class'] = 'warning';
	} elseif ($wpiPackage['Wpi']['default']) {
		$attrRow['class'] = 'success';
	} elseif ($wpiPackage['Wpi']['force']) {
		$attrRow['class'] = 'danger';
	}
	$tableRow[] = [
		$this->Filter->createFilterRowCheckbox('Wpi.id', $wpiPackage['Wpi']['id']),
		['class' => 'action text-center']
	];
	if (!$packageState) {
		$packageName = $this->Html->tag('s', $packageName);
	}
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$packageName,
		['controller' => 'wpi', 'action' => 'view', $wpiPackage['Wpi']['id']]
	);
	$tableRow[] = $this->ViewExtension->popupModalLink(
		h($wpiPackage['Package']['name']),
		['controller' => 'packages', 'action' => 'view', $wpiPackage['Package']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	);
	$tableRow[] = $this->ViewExtension->showEmpty(
		$wpiPackage['Package']['notes'],
		$this->ViewExtension->truncateText(h($wpiPackage['Package']['notes']), 50)
	);
	$tableRow[] = [$this->ViewExtension->showEmpty(h($wpiPackage['WpiCategory']['name'])),
		['class' => 'text-center']];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($wpiPackage['Wpi']['default']),
			['controller' => 'wpi', 'action' => 'default', $wpiPackage['Wpi']['id'], !$wpiPackage['Wpi']['default']],
			['title' => ($wpiPackage['Wpi']['default'] ? __('Don\'t selected by default') : __('Selected by default'))]
		),
		['class' => 'text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($wpiPackage['Wpi']['force']),
			['controller' => 'wpi', 'action' => 'force', $wpiPackage['Wpi']['id'], !$wpiPackage['Wpi']['force']],
			['title' => ($wpiPackage['Wpi']['force'] ? __('Don\'t selected forcibly') : __('Forced selected'))]
		),
		['class' => 'text-center']
	];
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
