<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing host.
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

	echo $this->Form->create('Host', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Host'); ?></legend>
<?php
	if (!$isAddAction) {
		$hiddenFields = [
			'Host.id'
		];
		echo $this->Form->hiddenFields($hiddenFields);
	}
	echo $this->Form->input('Host.enabled', ['label' => [__('Enabled'),
		__('If enabled, this host will appear in hosts (xml) lists.'), ':'],
		'type' => 'checkbox', 'autocomplete' => 'off']);
	echo $this->Form->input('Host.template', ['label' => [__('Template'),
		__('If enabled, this host is used as a template.'), ':'],
		'type' => 'checkbox', 'autocomplete' => 'off']);
	echo $this->Form->input('Host.id_text', ['label' => __('Name') . ':', 'title' => nl2br(__("Hostname might contain regular expressions as well as well as IP-address ranges.\n<i>Direct match:</i> This is tried first always. If the hostname matches exactly the value of 'name' this host node is applied to the machine. \n<i>IP-Ranges:</i> format has to be specified as follows: start[-end].start[-end].start[-end].start[-end], e.g.: 192.168.1.1 192.168.1.1-254 192.168.1-5.20-50\n<i>Regular expressions:</i> example: 'test-.*' will match all machines where the hostname is starting with 'test-' string.")),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off', 'autofocus' => true]);
	echo $this->Form->input('Host.mainprofile_id', ['label' => [__('Main profile'), __('The main profile that will always be evaluated for this host.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $profiles, 'autocomplete' => 'off']);
	echo $this->Form->input('Profile', ['label' => [__('Additional associated profiles'), __('Additional profiles associated with the host.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'multiple' => true, 'options' => $profiles, 'autocomplete' => 'off']);
	echo $this->Form->input('Host.notes', ['label' => __('Notes') . ':', 'title' => __('Additional notes about this host. Not used by WPKG.'),
		'type' => 'textarea', 'escape' => false, 'data-toggle' => 'tooltip', 'rows' => '3', 'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
