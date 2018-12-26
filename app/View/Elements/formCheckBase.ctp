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
$pathTooltip = $valueTooltip = '';
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
		$pathTooltip = __("Checking the Microsoft software installation registry keys (as displayed in Windows 'Add/Remove Programs' section) for the existence of a particular package. Microsoft maintains the list of applications installed and available for uninstallation in the 'HKLM\\Software\\Microsoft\\Windows\\CurrentVersion\\Uninstall' registry key. The 'DisplayName' registry key is used.");
		switch ($checkCondition) {
			case CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN:
			case CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO:
			case CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO:
			case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN:
			case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO:
				$valueLabel = __('Version');
				$valueTooltip = __("The 'DisplayVersion' registry key is used");
				break;

			case CHECK_CONDITION_UNINSTALL_EXISTS:
				$showValue = false;
				break;
		}
		break;
	case CHECK_TYPE_REGISTRY:
		$pathTooltip = __('Registry key.');
		switch ($checkCondition) {
			case CHECK_CONDITION_REGISTRY_EXISTS:
				$showValue = false;
				break;

			case CHECK_CONDITION_REGISTRY_EQUALS:
				$valueTooltip = __('Registry value. If you are checking the value of a DWORD, make sure to check the decimal and not the hexadecimal value.');
				break;
		}
		break;
	case CHECK_TYPE_FILE:
		$pathLabel = $pathTooltip = __('File path');
		switch ($checkCondition) {
			case CHECK_CONDITION_FILE_VERSION_SMALLER_THAN:
			case CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO:
			case CHECK_CONDITION_FILE_VERSION_EQUAL_TO:
			case CHECK_CONDITION_FILE_VERSION_GREATER_THAN:
			case CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO:
				$valueLabel = __('Version');
				$valueTooltip = __('File version');
				break;

			case CHECK_CONDITION_FILE_EXISTS:
				$showValue = false;
				break;

			case CHECK_CONDITION_FILE_SIZE_EQUALS:
				$valueLabel = __('Size');
				$valueTooltip = __('File size in bytes');
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
				$valueTooltip = '<div class="text-left">' .
__("Format:
- Relative timestamp (in minutes):
* -100 Means the file timestamp is compared to the timestamp 100 minutes ago.
* +50 Means the file timestamp is compared to the timestamp 50 minutes in the future.
- Absolute timestamp in ISO 8601 format:
* '2007-11-23 22:00' (22:00 local time).
* '2007-11-23T22:00' (Both, 'T' and space delimiter are allowed).
* '2007-11-23 22:00:00' (specifies seconds which default to 0 above).
* '2007-11-23 22:00:00.000' (specifies milliseconds which default to 0).
- You can specify the timezone as well:
'2007-11-23 22:00+01:00' (22:00 CET)
'2007-11-23 21:00Z' (21:00 UTC/GMT = 22:00 CET)
'2007-11-23 22:00+00:00' (21:00 UTC/GMT = 22:00 CET)
- File-Comparison:
Prefix your value with the '@' character in order to point to a file to which the timestamp of the file referred in path is compared.
Examples:
* @%SystemRoot%\\explorer.exe
* @c:\\myfile.txt
- Special terms:
* 'last-week' Check against timestamp of exactly one week ago (7 days).
* 'last-month' Check against timestamp of exactly one month ago (30 days).
* 'last-year' Check against timestamp of exactly one year ago (365 days).
* 'yesterday' Check against timestamp of yesterday (24 hours ago).") . '</div>';
				break;
		}
		break;
	case CHECK_TYPE_EXECUTE:
		$pathLabel = __('Executable path');
		$pathTooltip = __('Path to the script to be executed');
		$valueLabel = __('Exit code');
		$valueTooltip = __('Exit code for comparison');
		break;
	case CHECK_TYPE_HOST:
		$showPath = false;
		switch ($checkCondition) {
			case CHECK_CONDITION_HOST_NAME:
				$valueLabel = __('Host name');
				$valueTooltip = __('This contains the name of the system, which is also contained in the Windows environment variable %COMPUTERNAME%.');
				break;

			case CHECK_CONDITION_HOST_OS:
				$valueLabel = __('OS');
				$valueTooltip = __('This contains the full description of the operations system, which consists of the following parts: OS-caption; OS-description; CSD-version (usually the service pack); OS-version.');
				$valueOptions = [
					'type' => 'select',
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_ARCHITECTURE:
				$valueLabel = __('Architecture');
				$valueTooltip = __('This contains the processor architecture of the current operating system: x86, x64 or ia64.');
				$valueOptions = [
					'type' => 'select',
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_IP_ADDRESSES:
				$valueLabel = __('IP addresses');
				$valueTooltip = __('This contains all currently active IP addresses of the system or an empty string, if no network adapter is currently active.');
				break;

			case CHECK_CONDITION_HOST_DOMAIN_NAME:
				$valueLabel = __('Domain name');
				$valueTooltip = __('This contains the name of the domain the system belongs to or an empty string, if it is not a domain member.');
				break;

			case CHECK_CONDITION_HOST_GROUPS:
				$valueLabel = __('Groups');
				$valueTooltip = __('This contains the names of all groups the system belongs to or an empty string, if it is not a member of any group.');
				break;

			case CHECK_CONDITION_HOST_LCID:
				$valueLabel = __('Language ID');
				$valueTooltip = __('This contains the language identifier of the operating system (e.g. 407,c07,1407).');
				$valueOptions = [
					'type' => 'select',
					'multiple' => true,
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_LCID_OS:
				$valueLabel = __('Language ID OS');
				$valueTooltip = __('This contains the language identifier of the operating system INSTALL (e.g. 407,c07,1407).');
				$valueOptions = [
					'type' => 'select',
					'multiple' => true,
					'options' => $listValues
				];
				break;

			case CHECK_CONDITION_HOST_ENVIRONMENT:
				$valueLabel = __('Environment');
				$valueTooltip = __("This contains condition for checking environment variables, e.g.: 'PKG_VER=^$'.");
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
	echo $this->Form->input('Check.path', ['label' => [$pathLabel, nl2br($pathTooltip), ':'],
		'title' => (empty($pathTooltip) ? __('The contents of this field vary depending on the check type and condition.') : null),
		'type' => 'text', 'autocomplete' => 'off',
		'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies)
	]);
}
if ($showValue) {
	echo $this->Form->input('Check.value', $valueOptions + ['label' => [$valueLabel, nl2br($valueTooltip), ':'],
		'title' => (empty($valueTooltip) ? __('The contents of this field vary depending on the check type and condition.') : null),
		'type' => 'text', 'autocomplete' => 'off',
		'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies)
	]);
}
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
