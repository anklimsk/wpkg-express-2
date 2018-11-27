<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing WPI package.
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

if (!isset($isAddAction)) {
	$isAddAction = false;
}

if (!isset($listPackages)) {
	$listPackages = [];
}

if (!isset($listWpiCategories)) {
	$listWpiCategories = [];
}

if (!isset($packageName)) {
	$packageName = __('Unknown');
}

	echo $this->Form->create('Wpi', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('WPI package'); ?></legend>
<?php
	$hiddenFields = [];
	if (!$isAddAction) {
		$hiddenFields[] = 'Wpi.id';
		$hiddenFields[] = 'Wpi.package_id';
	}
	echo $this->Form->hiddenFields($hiddenFields);
	if ($isAddAction) {
		echo $this->Form->input('Wpi.package_id', ['label' => [__('Package'), __('Package of WPI'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listPackages, 'autocomplete' => 'off']);
	} else {
		echo $this->Form->staticControl(__('Package of WPI') . ':', h($packageName));
	}
	echo $this->Form->input('Wpi.category_id', ['label' => [__('Category'), __('Category of WPI'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listWpiCategories, 'autocomplete' => 'off']);
	echo $this->Form->input('Wpi.default', ['label' => [__('Default'),
		__('Default checked'), ':'], 'type' => 'checkbox', 'autocomplete' => 'off']);
	echo $this->Form->input('Wpi.force', ['label' => [__('Forcibly'),
		__('Forcibly execute package'), ':'], 'type' => 'checkbox', 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
