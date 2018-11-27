<?php
/**
 * This file is the view file of the application. Used to render
 *  form for creating new data from template.
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

if (!isset($listTemplates)) {
	$listTemplates = [];
}

if (!isset($labelAdditAttrib)) {
	$labelAdditAttrib = '';
}

if (!isset($modelName)) {
	return;
}

	echo $this->Form->create('Template', ['role' => 'form']);
?>
	<fieldset>
		<legend><?php echo __('Data to create'); ?></legend>
<?php
	echo $this->Form->input('Template.template_id', ['label' => [__('Template'), __('Template for creating new data'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listTemplates, 'autocomplete' => 'off']);

	echo $this->Form->input($modelName . '.id_text', ['label' => __('ID text') . ':', 'title' => __('ID text of new data'),
		'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off', 'autofocus' => true]);
	if (!empty($labelAdditAttrib)) {
		echo $this->Form->input($modelName . '.addit_attrib', ['label' => $labelAdditAttrib . ':', 'title' => __('Additional attribute'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off']);
	} else {
		echo $this->Form->hidden($modelName . '.addit_attrib');
	}
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Create'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
