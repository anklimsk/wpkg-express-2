<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing exit code.
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

if (!isset($listRebootType)) {
	$listRebootType = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}
	echo $this->Form->create('ExitCode', $this->ViewExtension->getFormOptions(['class' => 'form-default']));
?>
	<fieldset>
		<legend><?php echo __('Package action type'); ?></legend>
<?php
	$hiddenFields = [
		'ExitCode.package_action_id'
	];
	if (!$isAddAction) {
		$hiddenFields[] = 'ExitCode.id';
	}
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Exit code type') . ':', h($fullName));
	echo $this->Form->input('ExitCode.code', ['label' => __('Exit code') . ':', 'title' => __('This is the expected exit code produced by the associated command/package action.'),
		'type' => 'text', 'autocomplete' => 'off',
		'data-inputmask-regex' => '^(?:any|\d+|\*)$', 'autofocus' => true,
		'data-toggle' => 'autocomplete', 'data-autocomplete-url' => '/cake_theme/filter/autocomplete.json',
		'data-autocomplete-type' => 'ExitCode.code']);
	echo $this->Form->input('ExitCode.reboot_id', ['label' => [__('Reboot'), __('Determines if and what kind of a reboot is performed when the specified exit code is detected.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listRebootType, 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
