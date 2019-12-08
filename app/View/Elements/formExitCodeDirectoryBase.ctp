<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing record of exit code directory.
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

if (!isset($isAddAction)) {
	$isAddAction = false;
}

	echo $this->Form->create('ExitCodeDirectory', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Record of exit code directory'); ?></legend>
<?php
	$hiddenFields = [
		'ExitCodeDirectory.lcid'
	];
	if (!$isAddAction) {
		$hiddenFields[] = 'ExitCodeDirectory.id';
	}
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->input('ExitCodeDirectory.code', ['label' => __('Exit code') . ':', 'title' => __('Value of exit code.'),
		'type' => 'text', 'autocomplete' => 'off',
		'data-inputmask-regex' => '^\-?\d+$', 'autofocus' => true]);
	echo $this->Form->input('ExitCodeDirectory.hexadecimal', ['label' => __('Hexadecimal') . ':', 'title' => __('Hexadecimal value of exit code. Format: 0x00000000'),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
		'data-inputmask-mask' => '0x#{8}']);
	echo $this->Form->input('ExitCodeDirectory.constant', ['label' => __('Constant') . ':', 'title' => __('Constant of exit code. Format: letters A-Z, numbers, and underscores.'),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
		'data-inputmask-casing' => 'upper', 'data-inputmask-regex' => '^[0-9A-za-z_]{2,}$']);
	echo $this->Form->input('ExitCodeDirectory.description', ['label' => __('Description') . ':', 'title' => __('Description of exit code.'),
		'type' => 'textarea', 'data-toggle' => 'tooltip', 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
