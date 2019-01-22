<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing package.
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

if (!isset($listReboot)) {
	$listReboot = [];
}

if (!isset($listExecute)) {
	$listExecute = [];
}

if (!isset($listNotify)) {
	$listNotify = [];
}

if (!isset($listPrecheck)) {
	$listPrecheck = [];
}

if (!isset($listPriority)) {
	$listPriority = [];
}

if (!isset($packageDependencies)) {
	$packageDependencies = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}

	$listPriorityValues = [];
	foreach ($listPriority as $priorityValue => $priorityText) {
		$listPriorityValues[] = $this->Html->link(h($priorityText), '#', ['data-priority-value' => $priorityValue]);
	}

	$modelName = 'Package';
	$legend = __('Package');
	$inputList = [
		$modelName . '.id' => ['type' => 'hidden'],
		$modelName . '.enabled' => ['label' => [__('Enabled'),
			__('If enabled, this package will appear in packages (xml) lists.'), ':'],
			'type' => 'checkbox', 'autocomplete' => 'off'],
		$modelName . '.template' => ['label' => [__('Template'),
			__('If enabled, this package is used as a template.'), ':'],
			'type' => 'checkbox', 'autocomplete' => 'off'],
		$modelName . '.id_text' => ['label' => __('Package ID') . ':', 'title' => __('Unique ID containing only letters, numbers, underscores and hyphens (e.g. fooBarBaz_ultra OR foo-bar-baz-ultra).'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
			'data-inputmask-regex' => '^[a-zA-Z0-9]{1}[a-zA-Z0-9_\-]+',
			'beforeInput' => '<div class="input-group">',
			'afterInput' => $this->Html->div('input-group-btn',
				$this->ViewExtension->button('fas fa-sync-alt', 'btn btn-default',
				['id' => 'btnGenerateId', 'title' => __('Create a package ID based on his name. Repeated click changes the strategy for creating an ID.'), 'data-toggle' => 'title'])
			) . '</div>'],
		$modelName . '.name' => ['label' => __('Name') . ':', 'title' => __('Human-readable name that identifies this package (e.g. Foo Bar Baz Ultra Edition).'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
			'autofocus' => true],
		$modelName . '.revision' => ['label' => __('Revision') . ':', 'title' => __('An positive integer or positive decimal value that denotes the version/revision of this package (e.g. 1 OR 1.0.3). If you have periods in the revision, you must have at least one digit after each period.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.priority' => ['label' => __('Priority') . ':', 'title' => __('An integer value (any positive non-decimal number) that indicates this package\'s priority. Higher priorities take precedence over lower priorities (e.g. 10).'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off',
			'data-inputmask-mask' => '9{1,}',
			'beforeInput' => '<div class="input-group">',
			'afterInput' => $this->Html->div('input-group-btn',
				$this->ViewExtension->button(__('Priorities') . ' ' .
					$this->Html->tag('span', '', ['class' => 'caret']),
					'btn btn-default dropdown-toggle',
					[
						'title' => __('List of priorities.'),
						'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'
					]
				) .
				$this->Html->nestedList($listPriorityValues, ['class' => 'dropdown-menu dropdown-menu-right', 'id' => 'DropdownPackagePriorities'], [], 'ul') .
				$this->ViewExtension->buttonLink(
					'fas fa-expand-arrows-alt',
					'btn btn-info',
					['controller' => 'package_priorities', 'action' => 'index'],
					[
						'title' => __('Open in new window'),
						'action-type' => 'modal'
					]
				)
			) . '</div>'],
		$modelName . '.reboot_id' => ['label' => [__('Reboot'), nl2br(__("Specifies if and how the system reboots when installing, removing or upgrading a given package:\n- 'yes': always reboot after processing this package;\n- 'no': do not cause a reboot because of this package. Other packages may still cause a reboot.\n- 'postponed': reboot after WPKG has finished processing all packages, if the package checks are successful after the action.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listReboot, 'autocomplete' => 'off'],
		$modelName . '.execute_id' => ['label' => [__('Execute'), nl2br(__("Specify how the package should be executed:\n- 'always': execute on each synchronization - regardless of the current install state or the result of any defined checks.\n- 'changed': execute on each synchronization but only if there are other changes done to the system.\n- 'once': execute only once. No checks will be executed on following synchronization requests unless the package version on the server side is changed.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listExecute, 'autocomplete' => 'off'],
		$modelName . '.notify_id' => ['label' => [__('Notify'), __('Specify if the user should be notified about the installation of packages due to this package.'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listNotify, 'autocomplete' => 'off'],
		$modelName . '.precheck_install_id' => ['label' => [__('Precheck install'), nl2br(__("Specify how package checks are used during package installation:\n- 'always' (default): When a package is new to the host\nthen first the checks are run in order to verify whether the package is already installed. If the checks succeed then it is assumed that no further installation is needed. The package is silently added to the host without executing any commands.\n- 'never': When a package is new to the host then the install commands are run in any case (without doing checks first). Note: Checks will still be done after package installation to verify whether installation was successful.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listPrecheck, 'autocomplete' => 'off'],
		$modelName . '.precheck_remove_id' => ['label' => [__('Precheck remove'), nl2br(__("Specify how package checks are used during package removal:\n- 'always': When a package is removed from a host then the checks will be executed before removal is processes. If the checks fail this potentially means that the package has been removed already. In such case the package remove commands will be skipped.\n- 'never' (default): When a package is about to be removed from the host then WPKG will execute the remove commands in any case without executing the checks first. Note: Checks will still be done after package removal to verify whether the removal was successful.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listPrecheck, 'autocomplete' => 'off'],
		$modelName . '.precheck_upgrade_id' => ['label' => [__('Precheck upgrade'), nl2br(__("Specify how package checks are used during package upgrade:\n- 'always': When a package is upgraded the checks specified will be be executed before the upgrade takes place. If checks succeed, then the upgrade will not be performed (WPKG just assumes that the new version is already applied correctly. Please note that your checks shall verify a specific software version and not just a generic check which is true for all versions. If your checks are true for the old version too then WPKG would never perform the upgrade in this mode.\n- 'never' (default): When a package is about to be upgraded then WPKG will execute the upgrade commands in any case without executing the checks first. This is the recommended behavior. Note: Checks will still be done after package upgrade to verify whether the upgrade was successful.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listPrecheck, 'autocomplete' => 'off'],
		$modelName . '.precheck_downgrade_id' => ['label' => [__('Precheck downgrade'), nl2br(__("Specify how package checks are used during package downgrade:\n - 'always': When a package is downgraded the checks specified will be be executed before the downgrade takes place. If checks succeed, then the downgrade will not be performed (WPKG just assumes that the old version is already applied correctly. Please note that your checks shall verify a specific software version and not just a generic check which is true for all versions. If your checks are true for the new/current version too then WPKG would never perform the downgrade in this mode.\n- 'never' (default): When a package is about to be downgraded then WPKG will execute the downgrade commands in any case without executing the checks first. This is the recommended behavior. Note: Checks will still be done after package downgrade to verify whether the downgrade was successful.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listPrecheck, 'autocomplete' => 'off'],
		$modelName . '.notes' => ['label' => __('Notes') . ':', 'title' => __('Additional notes about this package. Not used by WPKG.'),
			'type' => 'textarea', 'escape' => false, 'data-toggle' => 'tooltip', 'rows' => '3', 'autocomplete' => 'off'],
		'DependsOn' => ['label' => [__('Depends on packages'), __('By using <i>depend</i> you make a package depending on another package, meaning that this package needs the other package for correct functionality. This dependency can already be needed during the installation or upgrade, therefore a dependency is always installed right <u>before</u> the current package independently of the priority of the packages.'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $packageDependencies, 'multiple' => true,
			'actions-box' => 'true', 'autocomplete' => 'off', 'class' => 'dependency-select'],
		'Includes' => ['label' => [__('Includes packages'), __('By using <i>include</i> you include a package to the list of packages, meaning that if you install this package the included package will also be installed. This included package will be installed <u>based on its priority</u>. So if the included package has high priority it will be installed early during synchronization. If the included package has low priority it will be installed late. If you would like to enforce a certain installation order please consider to specify a <i>dependency</i>.'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $packageDependencies, 'multiple' => true,
			'actions-box' => 'true', 'autocomplete' => 'off', 'class' => 'dependency-select'],
		'Chains' => ['label' => [__('Chains packages'), nl2br(__("By using <i>chain</i> you chain a package to the current package, meaning that if this package is installed, the other package also has to be installed but right <u>after</u> the current package.\n<u>Attention</u>: a chained package might also be installed before the package chaining it. This might happen if the chained package is either already installed (possible by another dependency) or has higher priority than the package which references it by chain. If you need to ensure that a package is installed before another one please specify a <i>dependency</i>.")), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $packageDependencies, 'multiple' => true,
			'actions-box' => 'true', 'autocomplete' => 'off', 'class' => 'dependency-select'],
	];
	$inputStatic = [];
	$tabsList = [
		__('Package ID, revision, ...') => [
			$modelName . '.enabled',
			$modelName . '.template',
			$modelName . '.name',
			$modelName . '.id_text',
			$modelName . '.revision',
		],
		__('Priority, reboot, ...') => [
			$modelName . '.priority',
			$modelName . '.reboot_id',
			$modelName . '.execute_id',
			$modelName . '.notify_id',
		],
		__('Prechecks') => [
			$modelName . '.precheck_install_id',
			$modelName . '.precheck_remove_id',
			$modelName . '.precheck_upgrade_id',
			$modelName . '.precheck_downgrade_id',
		],
		__('Notes') => [
			$modelName . '.notes',
		],
		__('Dependencies') => [
			'DependsOn',
			'Includes',
			'Chains',
		]
	];
	if (!$isAddAction) {
		array_unshift($tabsList[__('Package ID, revision, ...')], $modelName . '.id');
	}
	echo $this->Form->createFormTabs($inputList, $inputStatic, $tabsList, $legend, $modelName);
