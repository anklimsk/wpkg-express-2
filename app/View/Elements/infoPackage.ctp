<?php
/**
 * This file is the view file of the application. Used to render
 *  information about package.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.View.Elements
 */

if (!isset($package)) {
	$package = [];
}

if (!isset($bindLimit)) {
	$bindLimit = 0;
}

if (!isset($autoVarRevision)) {
	$autoVarRevision = false;
}

if (empty($package)) {
	return;
}
?>
<div class="container-fluid top-buffer">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#basicInfo" aria-controls="basicInfo" role="tab" data-toggle="tab"><?php echo __('Basic information'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#extendInfo" aria-controls="extendInfo" role="tab" data-toggle="tab"><?php echo __('Extended information'); ?></a></li>
	</ul>
	<div class="tab-content top-buffer">
		<div role="tabpanel" class="tab-pane active" id="basicInfo">
			<div class="row">
				<div class="col-sm-6 col-xs-12">
					<dl class="dl-horizontal dl-wt-bottom-margin break-long-words">
<?php
	echo $this->Html->tag('dt', __('Enabled') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($package['Package']['enabled']),
		['controller' => 'packages', 'action' => 'enabled', $package['Package']['id'], !$package['Package']['enabled']],
		['title' => ($package['Package']['enabled'] ? __('Disable package') : __('Enable package'))]
	));
	echo $this->Html->tag('dt', __('Package ID') . ':');
	echo $this->Html->tag('dd', h($package['Package']['id_text']) .
		$this->Indicator->createIndicator($package['Package']['template'], __('template'), __('Use this package as a template')));
	echo $this->Html->tag('dt', __('Name') . ':');
	echo $this->Html->tag('dd', h($package['Package']['name']));
?>
					</dl>
				</div>
				<div class="col-sm-6 col-xs-12">
					<dl class="dl-horizontal break-long-words">
<?php
	echo $this->Html->tag('dt', __('Revision') . ':');
	echo $this->Html->tag('dd', h($package['Package']['revision']) .
		$this->Indicator->createIndicator($autoVarRevision, '%Revision%', __('The automatic creation of the variable %%%s%% based on the package version is enabled', VARIABLE_AUTO_REVISION_NAME)));
	echo $this->Html->tag('dt', __('Priority') . ':');
	echo $this->Html->tag('dd', $this->Number->format(
		$package['Package']['priority'],
		['thousands' => ' ', 'before' => '', 'places' => 0]) .
		(!empty($package['PackagePriority']['name']) ? ' (' . __d('package_priority', h($package['PackagePriority']['name'])) . ')' : '')
	);
	echo $this->Html->tag('dt', __('Notes') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->showEmpty(h($package['Package']['notes'])));
?>
					</dl>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="extendInfo">
			<div class="row">
				<div class="col-sm-6 col-xs-12">
					<dl class="dl-horizontal dl-wt-bottom-margin">
<?php
	echo $this->Html->tag('dt', __('Template') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($package['Package']['template']),
		['controller' => 'packages', 'action' => 'template', $package['Package']['id'], !$package['Package']['template']],
		['title' => ($package['Package']['template'] ? __('Don\'t use as template') : __('Use as template'))]
	));
	echo $this->Html->tag('dt', __('Reboot') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_reboot', h($package['PackageRebootType']['name']))));
	echo $this->Html->tag('dt', __('Execute') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_execute', h($package['PackageExecuteType']['name']))));
	echo $this->Html->tag('dt', __('Notify') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_notify', h($package['PackageNotifyType']['name']))));
	echo $this->Html->tag('dt', __('Last modified') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->timeAgo($package['Package']['modified']));
?>
					</dl>
				</div>
				<div class="col-sm-6 col-xs-12">
					<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', __('Prechecks') . ':');
?>
						<dd>
<?php
	echo $this->Html->tag('dt', __x('precheck', 'Install') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_precheck', h($package['PackagePrecheckTypeInstall']['name']))));
	echo $this->Html->tag('dt', __x('precheck', 'Remove') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_precheck', h($package['PackagePrecheckTypeRemove']['name']))));
	echo $this->Html->tag('dt', __x('precheck', 'Upgrade') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_precheck', h($package['PackagePrecheckTypeUpgrade']['name']))));
	echo $this->Html->tag('dt', __x('precheck', 'Downgrade') . ':');
	echo $this->Html->tag('dd', mb_ucfirst(__d('package_precheck', h($package['PackagePrecheckTypeDowngrade']['name']))));
?>
						</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid top-buffer">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#actions" aria-controls="actions" role="tab" data-toggle="tab"><?php echo __('Package actions'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#dependencies" aria-controls="dependencies" role="tab" data-toggle="tab"><?php echo __('Dependencies'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#inclusions" aria-controls="inclusions" role="tab" data-toggle="tab"><?php echo __('Inclusions'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#chains" aria-controls="chains" role="tab" data-toggle="tab"><?php echo __('Chains'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#variables" aria-controls="variables" role="tab" data-toggle="tab"><?php echo __('Variables'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#checks" aria-controls="checks" role="tab" data-toggle="tab"><?php echo __('Checks'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#profiles" aria-controls="profiles" role="tab" data-toggle="tab"><?php echo __('Exists in profiles'); ?></a></li>
	</ul>
	<div class="tab-content top-buffer">
		<div role="tabpanel" class="tab-pane active" id="actions">
<?php
	echo $this->element('tablePackageActions', ['packageActions' => $package['PackageAction'], 'refId' => $package['Package']['id'], 'showBtnExpand' => true]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="dependencies">
			<div class="row">
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['PackagesPackage'], 'controllerName' => 'packages',
		'label' => __('Depends on packages'), 'modelName' => 'PackageDependency', 'bindLimit' => $bindLimit,
		'attrRefType' => ATTRIBUTE_TYPE_PACKAGE, 'attrRefNode' => ATTRIBUTE_NODE_DEPENDS,
		'dependencyType' => 'PackagesPackage']);
?>
				</div>
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['InDependencies'], 'controllerName' => 'packages',
		'label' => __('Exists in dependencies'), 'bindLimit' => $bindLimit]);
?>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="inclusions">
			<div class="row">
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['PackagesInclude'], 'controllerName' => 'packages',
		'label' => __('Includes packages'), 'modelName' => 'PackageDependency', 'bindLimit' => $bindLimit,
		'attrRefType' => ATTRIBUTE_TYPE_PACKAGE, 'attrRefNode' => ATTRIBUTE_NODE_INCLUDE,
		'dependencyType' => 'PackagesInclude']);
?>
				</div>
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['InInclusions'], 'controllerName' => 'packages',
		'label' => __('Exists in inclusions'), 'bindLimit' => $bindLimit]);
?>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="chains">
			<div class="row">
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['PackagesChain'], 'controllerName' => 'packages',
		'label' => __('Chains packages'), 'modelName' => 'PackageDependency', 'bindLimit' => $bindLimit,
		'attrRefType' => ATTRIBUTE_TYPE_PACKAGE, 'attrRefNode' => ATTRIBUTE_NODE_CHAIN,
		'dependencyType' => 'PackagesChain']);
?>
				</div>
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $package['InChains'], 'controllerName' => 'packages',
		'label' => __('Exists in chains'), 'bindLimit' => $bindLimit]);
?>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="variables">
<?php
	echo $this->element('infoVariables', ['variables' => $package['Variable'], 'refType' => VARIABLE_TYPE_PACKAGE, 'refId' => $package['Package']['id'],
		'showShort' => true, 'showBtnExpand' => true]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="checks">
<?php
	echo $this->element('infoChecksControls', ['checks' => $package['Check'], 'nest' => true, 'expandAll' => true, 'draggable' => true,
		'checkRefType' => CHECK_PARENT_TYPE_PACKAGE, 'checkRefId' => $package['Package']['id'], 'showShort' => true, 'showBtnExpand' => true]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="profiles">
<?php
	$btnActionsExistsProfiles = $this->ViewExtension->buttonLink(
		'fas fa-pencil-alt',
		'btn-warning',
		['controller' => 'packages', 'action' => 'profiles', $package['Package']['id']],
		[
			'title' => __('Edit existing package in profiles'),
			'action-type' => 'modal',
		]
	);
	echo $this->element('infoDependency', ['dependencies' => $package['Profile'], 'controllerName' => 'profiles',
		'bindLimit' => $bindLimit, 'btnActions' => $btnActionsExistsProfiles]);
?>
		</div>
	</div>
</div>
<?php
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
