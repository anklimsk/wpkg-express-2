<?php
/**
 * This file is the view file of the application. Used to render
 *  form for generating new hosts based on template by
 *  name from LDAP.
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

if (!isset($listHostTemplates)) {
	$listHostTemplates = [];
}

if (!isset($listProfileTemplates)) {
	$listProfileTemplates = [];
}

	echo $this->Form->create('Host', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Host'); ?></legend>
<?php
	echo $this->Form->input('Host.computers', ['label' => [__('Computers to generate'), __('The list of computers from LDAP to create an XML file.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'multiple' => true, 'actions-box' => 'true', 'autocomplete' => 'off',
		'data-abs-ajax-url' => $this->Html->url(['controller' => 'hosts', 'action' => 'computers', 'ext' => 'json']),
		'data-abs-min-length' => '2', 'data-abs-request-delay' => '1200',
		'data-actions-box' => 'true']);
	echo $this->Form->input('Host.host_template_id', ['label' => [__('Host template'), __('Template for creating new host'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listHostTemplates, 'autocomplete' => 'off']);
	echo $this->Form->input('Host.profile_template_id', ['label' => [__('Profile template'), __('Template for creating new profile'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listProfileTemplates, 'autocomplete' => 'off',
		'empty' => __('Use the main profile from the host template')]);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Generate'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
