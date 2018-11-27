<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing profile.
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

if (!isset($profileDependencies)) {
	$profileDependencies = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}

	echo $this->Form->create('Profile', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Profile'); ?></legend>
<?php
	if (!$isAddAction) {
		$hiddenFields = [
			'Profile.id',
		];
		echo $this->Form->hiddenFields($hiddenFields);
	}
	echo $this->Form->input('Profile.enabled', ['label' => [__('Enabled'),
		__('If enabled, this profile will appear in profiles (xml) lists.'), ':'],
		'type' => 'checkbox', 'autocomplete' => 'off']);
	echo $this->Form->input('Profile.template', ['label' => [__('Template'),
		__('If enabled, this profile is used as a template.'), ':'],
		'type' => 'checkbox', 'autocomplete' => 'off']);
	echo $this->Form->input('Profile.id_text', ['label' => __('Profile ID') . ':', 'title' => __('Unique ID containing only letters, numbers, underscores and hyphens (e.g. base_software OR developer-suite OR complabsoft8).'),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
		'data-inputmask-regex' => '^[a-zA-Z0-9]{1}[a-zA-Z0-9_\-]+', 'autofocus' => true]);
	echo $this->Form->input('ProfileDependency', ['label' => [__('Dependencies'), __('Profiles that this profile depends on.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'multiple' => true, 'options' => $profileDependencies, 'autocomplete' => 'off']);
	echo $this->Form->input('Profile.notes', ['label' => __('Notes') . ':', 'title' => __('Additional notes about this profile. Not used by WPKG.'),
		'type' => 'textarea', 'escape' => false, 'data-toggle' => 'tooltip', 'rows' => '3', 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
