<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing package action type.
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

if (!isset($isAddAction)) {
	$isAddAction = false;
}

	echo $this->Form->create('PackageActionType', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Package action type'); ?></legend>
<?php
	$hiddenFields = [
		'PackageActionType.builtin',
		'PackageActionType.command',
	];
	if (!$isAddAction) {
		$hiddenFields[] = 'PackageActionType.id';
	}
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->input('PackageActionType.name', ['label' => __('Action type') . ':', 'title' => __('The name of the type of package action.'),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
		'data-inputmask-regex' => '[a-zA-Z0-9_\-]+', 'autofocus' => true]);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
