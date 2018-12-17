<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing check.
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

if (!isset($listParent)) {
	$listParent = [];
}

if (!isset($listType)) {
	$listType = [];
}

if (!isset($listCondition)) {
	$listCondition = [];
}

if (!isset($listValues)) {
	$listValues = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}

$checkType = $this->request->data('Check.type');
$checkCondition = $this->request->data('Check.condition');
$pathLabel = __('Path');
$valueLabel = __('Value');
$showPath = true;
$showValue = true;
$valueOptions = [];

switch ($checkType) {
	case CHECK_TYPE_LOGICAL:
		$showPath = false;
		$showValue = false;
		break;
	case CHECK_TYPE_UNINSTALL:
		$pathLabel = __('Add/remove name');
		if ($checkCondition >= CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN
			&& $checkCondition <= CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO) {
			$valueLabel = __('Version');
		} elseif ($checkCondition == CHECK_CONDITION_UNINSTALL_EXISTS) {
			$showValue = false;
		}
		break;
	case CHECK_TYPE_REGISTRY:
		if ($checkCondition == CHECK_CONDITION_REGISTRY_EXISTS) {
			$showValue = false;
		}
		break;
	case CHECK_TYPE_FILE:
		$pathLabel = __('File path');
		switch ($checkCondition) {
			case CHECK_CONDITION_FILE_VERSION_SMALLER_THAN:
			case CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO:
			case CHECK_CONDITION_FILE_VERSION_EQUAL_TO:
			case CHECK_CONDITION_FILE_VERSION_GREATER_THAN:
			case CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO:
				$valueLabel = __('Version');
				break;
			case CHECK_CONDITION_FILE_EXISTS:
				$showValue = false;
				break;
			case CHECK_CONDITION_FILE_SIZE_EQUALS:
				$valueLabel = __('Size (in bytes)');
				break;
			case CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO:
			case CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN:
			case CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN:
			case CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO:
			case CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN:
			case CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN:
			case CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO:
			case CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN:
			case CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN:
				$valueOptions = [
					//'type' => 'dateTimeSelect',
					'data-inputmask-regex' => '^@.+|^%.+|^[\+-]{1}[\d]+|(last-week|last-month|last-year|yesterday)|[\d]{4}-[\d]{2}-[\d]{2}(\s|T)[\d]{2}:[\d]{2}(:[\d]{2}\.[\d]{3}|:[\d]{2}|\+[\d]{2}:[\d]{2}|Z|)$'
				];
				$valueLabel = __('Date');
				break;
		}
		break;
	case CHECK_TYPE_EXECUTE:
		$pathLabel = __('Executable path');
		$valueLabel = __('Exit code');
		break;
	case CHECK_TYPE_HOST:
		$showPath = false;
		switch ($checkCondition) {
			case CHECK_CONDITION_HOST_NAME:
				$valueLabel = __('Host name');
				break;

			case CHECK_CONDITION_HOST_OS:
				$valueLabel = __('OS');
				$valueOptions = [
					'type' => 'select',
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_ARCHITECTURE:
				$valueLabel = __('Architecture');
				$valueOptions = [
					'type' => 'select',
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_IP_ADDRESSES:
				$valueLabel = __('IP addresses');
				break;

			case CHECK_CONDITION_HOST_DOMAIN_NAME:
				$valueLabel = __('Domain name');
				break;

			case CHECK_CONDITION_HOST_GROUPS:
				$valueLabel = __('Groups');
				break;

			case CHECK_CONDITION_HOST_LCID:
				$valueLabel = __('Language ID');
				$valueOptions = [
					'type' => 'select',
					'multiple' => true,
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_LCID_OS:
				$valueLabel = __('Language ID OS');
				$valueOptions = [
					'type' => 'select',
					'multiple' => true,
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_ENVIRONMENT:
				$valueLabel = __('Environment');
				break;
		}
		break;
}

	echo $this->Form->create('Check', $this->ViewExtension->getFormOptions(['class' => 'form-edit-check']));
?>
	<fieldset>
		<legend><?php echo __('Check'); ?></legend>
<?php
	$hiddenFields = [
		'Check.ref_id',
		'Check.ref_type',
	];
	if (!$isAddAction) {
		$hiddenFields[] = 'Check.id';
	}

	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Check type') . ':', h($fullName));
	echo $this->Form->input('Check.parent_id', ['label' => [__('Parent'), __('Determines where the check should be placed in the check hierarchy. All potential parents are listed in order.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listParent, 'autocomplete' => 'off']);
	echo $this->Form->input('Check.type', ['label' => [__('Type'), __('The kind of check to be performed.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listType, 'autocomplete' => 'off']);
	echo $this->Form->input('Check.condition', ['label' => [__('Condition'), __('Describes how the check is to be carried out for the selected type.'), ':'],
		'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listCondition, 'autocomplete' => 'off']);
	$strategies = [
		[
			'ajaxOptions' => [
				'url' => $this->Html->url(['controller' => 'variables', 'action' => 'autocomplete', 'ext' => 'json']),
				'data' => [
					'ref-type' => $this->request->data('Check.ref_type'),
					'ref-id' => $this->request->data('Check.ref_id'),
					'convert-ref' => 'check',
				]
			],
			'match' => '(%)(\w+)$',
			'replace' => 'return "$1" + value + "%";'
		]
	];
if ($showPath) {
	echo $this->Form->input('Check.path', ['label' => $pathLabel . ':', 'title' => __('The contents of this field vary depending on the check type and condition.'),
		'type' => 'text', 'autocomplete' => 'off',
		'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies)
	]);
}
if ($showValue) {
	echo $this->Form->input('Check.value', $valueOptions + ['label' => $valueLabel . ':', 'title' => __('The contents of this field vary depending on the check type and condition.'),
		'type' => 'text', 'autocomplete' => 'off',
		'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies)
	]);
}
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
