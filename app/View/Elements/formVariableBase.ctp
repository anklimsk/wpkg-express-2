<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing variable.
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

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}
	echo $this->Form->create('Variable', $this->ViewExtension->getFormOptions(['class' => 'form-default']));
?>
	<fieldset>
		<legend><?php echo __('Variable'); ?></legend>
<?php
	$hiddenFields = [
		'Variable.ref_type',
		'Variable.ref_id',
		'Variable.parent_id',
	];
	if (!$isAddAction) {
		$hiddenFieldsExt = [
			'Variable.id',
			'Variable.lft',
			'Variable.rght',
		];
		$hiddenFields = array_merge($hiddenFields, $hiddenFieldsExt);
	}
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Variable type') . ':', h($fullName));
	echo $this->Form->input('Variable.name', ['label' => __('Variable name') . ':', 'title' => __('Name of variable'),
		'type' => 'text', 'autocomplete' => 'off', 'autofocus' => true,
		'data-toggle' => 'autocomplete', 'data-autocomplete-url' => '/cake_theme/filter/autocomplete.json',
		'data-autocomplete-type' => 'Variable.name',
		'data-autocomplete-min-length' => CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH
	]);
	$strategies = [
		[
			'ajaxOptions' => [
				'url' => $this->Html->url(['controller' => 'variables', 'action' => 'autocomplete', 'ext' => 'json']),
				'data' => [
					'ref-type' => $this->request->data('Variable.ref_type'),
					'ref-id' => $this->request->data('Variable.ref_id'),
				]
			],
			'match' => '(%)(\w+)$',
			'replace' => 'return "$1" + value + "%";'
		]
	];
	echo $this->Form->input('Variable.value', ['label' => __('Value') . ':', 'title' => __('Value of variable'),
		'type' => 'text', 'autocomplete' => 'off',
		'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies)
	]);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
