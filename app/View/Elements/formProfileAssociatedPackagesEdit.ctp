<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing list of associated packages of profile.
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

	echo $this->Form->create('Profile', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Associated packages of profile'); ?></legend>
<?php
	$hiddenFields = [
		'Profile.id',
		'Profile.id_text',
	];
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Profile ID') . ':', h($this->request->data('Profile.id_text')));
	echo $this->Form->input('Package', ['label' => [__('Associated packages'), __('Associated packages of this profile'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'multiple' => true, 'options' => $packages,
		'actions-box' => 'true', 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
