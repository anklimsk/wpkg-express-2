<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing list hosts where there is a profile.
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

if (!isset($hosts)) {
	$hosts = [];
}

	echo $this->Form->create('Profile', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Profiles'); ?></legend>
<?php
	$hiddenFields = [
		'Profile.id',
		'Profile.id_text',
	];
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Profile ID') . ':', h($this->request->data('Profile.id_text')));
	echo $this->Form->input('Host', ['label' => [__('Hosts containing this profile'), __('Hosts that contain this profile as an additional associated profile.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'multiple' => true, 'options' => $hosts, 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
