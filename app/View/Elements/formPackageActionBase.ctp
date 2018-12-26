<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing package action.
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

if (!isset($listActionType)) {
	$listActionType = [];
}

if (!isset($listCommandType)) {
	$listCommandType = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}

if (!isset($refId)) {
	$refId = null;
}

	$actionType = $this->request->data('PackageAction.action_type_id');
	$commandType = $this->request->data('PackageAction.command_type_id');
	$includeOptions = ['type' => 'hidden'];
	switch ($actionType) {
		case ACTION_TYPE_DOWNLOAD:
			$commandTypeOptions = ['type' => 'hidden', 'data-toggle' => null, 'autocomplete' => null];
			$commandOptions = ['label' => __('URL') . ':', 'title' => __('URL for download.')];
			$workDirOptions = ['label' => __('Target') . ':', 'title' => __('Path to file for saving.')];
			$timeoutOptions = ['label' => [__('Expand URL'), __('Defines whether the url attribute is expanded (ie. environment variables are replaced).'), ':'],
				'title' => __('Expansion of environment variables in download URLs.'),
				'type' => 'checkbox'];
		break;
		default:
			$commandTypeOptions = ['label' => [__('Command type'), __('The type of command to be executed.'), ':'],
				'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listCommandType, 'autocomplete' => 'off'];
			if ($commandType == ACTION_COMMAND_TYPE_INCLUDE) {
				$commandOptions = ['type' => 'hidden', 'autocomplete' => null, 'autofocus' => null,
				'data-toggle' => null];
				$workDirOptions = ['type' => 'hidden', 'data-toggle' => null, 'autocomplete' => null];
				$timeoutOptions = ['type' => 'hidden', 'data-toggle' => null, 'autocomplete' => null];
				$includeOptions = ['label' => [__('Command'), __('The command to be executed.'), ':'],
					'type' => 'select', 'options' => $listActionType, 'autocomplete' => 'off'];
			} else {
				$strategies = [
					[
						'ajaxOptions' => [
							'url' => $this->Html->url(['controller' => 'actions', 'action' => 'autocomplete', 'ext' => 'json']),
							'data' => [
								'type' => 'command',
							]
						],
						'match' => '(%ComSpec%\s+)(\/\w*)$',
						'replace' => 'return "$1" + value;'
					],
					[
						'ajaxOptions' => [
							'url' => $this->Html->url(['controller' => 'variables', 'action' => 'autocomplete', 'ext' => 'json']),
							'data' => [
								'ref-type' => VARIABLE_TYPE_PACKAGE,
								'ref-id' => $refId,
							]
						],
						'match' => '(%)(\w+)$',
						'replace' => 'return "$1" + value + "%";'
					],
					[
						'ajaxOptions' => [
							'url' => $this->Html->url(['controller' => 'actions', 'action' => 'autocomplete', 'ext' => 'json']),
							'data' => [
								'type' => 'switch',
							]
						],
						'match' => '(\s+)(\/\w*)$',
						'replace' => 'return "$1" + value;'
					]
				];
				$commandOptions = ['label' => __('Command') . ':', 'title' => __('The command to be executed. Autocompletion is available for global variables, package variables, console commands like "%ComSpec% /C" and installer command line switches.'),
					'type' => 'textarea', 'rows' => 4, 'autocorrect' => 'off', 'autocapitalize' => 'off', 'spellcheck' => 'false',
					'data-toggle' => 'textcomplete', 'data-textcomplete-strategies' => json_encode($strategies),
				];
				$workDirOptions = ['label' => __('Work directory') . ':', 'title' => __('The working directory to use when executing this action.')];
				$timeoutOptions = ['label' => __('Timeout') . ':', 'title' => __('The maximum number of seconds to wait for the action to execute.'),
					'type' => 'spin', 'max' => ACTION_COMMAND_MAX_TIMEOUT];
			}
	}

	echo $this->Form->create('PackageAction', $this->ViewExtension->getFormOptions(['class' => 'form-default form-edit-action']));
?>
	<fieldset>
		<legend><?php echo __('Package action'); ?></legend>
<?php
	$hiddenFields = [
		'PackageAction.package_id',
		'PackageAction.parent_id',
	];
	if (!$isAddAction) {
		$hiddenFieldsExt = [
			'PackageAction.id',
			'PackageAction.action_type_id',
			'PackageAction.lft',
			'PackageAction.rght',
		];
		$hiddenFields = array_merge($hiddenFields, $hiddenFieldsExt);
	}
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Package action type') . ':', h($fullName));
	if ($isAddAction) {
		echo $this->Form->input('PackageAction.action_type_id', ['label' => [__('Type'), __('The kind of action to be executed.'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listActionType, 'autocomplete' => 'off']);
	} else {
		$actionTypeName = __('Unknown');
		if (isset($listActionType[$actionType])) {
			$actionTypeName = $listActionType[$actionType];
		}
		echo $this->Form->staticControl(__('The kind of action to be executed') . ':', mb_ucfirst(h($actionTypeName)));
	}
	echo $this->Form->input('PackageAction.command_type_id', $commandTypeOptions);
	echo $this->Form->input('PackageAction.command', $commandOptions + ['type' => 'text', 'autocomplete' => 'off', 'autofocus' => true,
			'data-toggle' => 'tooltip']);
	echo $this->Form->input('PackageAction.workdir', $workDirOptions + ['type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off']);
	echo $this->Form->input('PackageAction.timeout', $timeoutOptions + ['data-toggle' => 'tooltip', 'autocomplete' => 'off']);
	echo $this->Form->input('PackageAction.include_action_id', $includeOptions);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
