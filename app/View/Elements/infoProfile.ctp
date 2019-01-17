<?php
/**
 * This file is the view file of the application. Used to render
 *  information about profile.
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

if (!isset($profile)) {
	$profile = [];
}

if (!isset($bindLimit)) {
	$bindLimit = null;
}

if (empty($profile)) {
	return;
}

$profileName = h($profile['Profile']['id_text']);
?>
<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', __('Enabled') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($profile['Profile']['enabled']),
		['controller' => 'profiles', 'action' => 'enabled', $profile['Profile']['id'], !$profile['Profile']['enabled']],
		['title' => ($profile['Profile']['enabled'] ? __('Disable profile') : __('Enable profile'))]
	));
	echo $this->Html->tag('dt', __('Template') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($profile['Profile']['template']),
		['controller' => 'profiles', 'action' => 'template', $profile['Profile']['id'], !$profile['Profile']['template']],
		['title' => ($profile['Profile']['template'] ? __('Don\'t use as template') : __('Use as template'))]
	));
	echo $this->Html->tag('dt', __('Profile ID') . ':');
	echo $this->Html->tag('dd', $profileName .
		$this->Indicator->createIndicator($profile['Profile']['template'], __('template'), __('Use this profile as a template')));
	echo $this->Html->tag('dt', __('Notes') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->showEmpty(h($profile['Profile']['notes'])));
	echo $this->Html->tag('dt', __('Last modified') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->timeAgo($profile['Profile']['modified']));
?>
</dl>
<div class="container-fluid top-buffer">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#packages" aria-controls="packages" role="tab" data-toggle="tab"><?php echo __('Associated packages'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#dependencies" aria-controls="dependencies" role="tab" data-toggle="tab"><?php echo __('Depends on profiles'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#variables" aria-controls="variables" role="tab" data-toggle="tab"><?php echo __('Variables'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#hosts" aria-controls="hosts" role="tab" data-toggle="tab"><?php echo __('Exists in hosts'); ?></a></li>
	</ul>
	<div class="tab-content top-buffer">
		<div role="tabpanel" class="tab-pane active" id="packages">
<?php
	$btnModifyAssociatedPackages = $this->ViewExtension->buttonLink(
		'fas fa-edit',
		'btn-warning',
		['controller' => 'profiles', 'action' => 'packages', $profile['Profile']['id']],
		[
			'title' => __('Modify list of associated packages'),
			'action-type' => 'modal',
			'data-modal-size' => 'lg'
		]
	);
	echo $this->element('infoDependency', ['dependencies' => $profile['PackagesProfile'], 'controllerName' => 'packages',
		'modelName' => 'Package', 'btnActions' => $btnModifyAssociatedPackages,
		'attrRefType' => ATTRIBUTE_TYPE_PROFILE, 'attrRefNode' => ATTRIBUTE_NODE_PACKAGE,
		'checkRefType' => CHECK_PARENT_TYPE_PROFILE, 'bindLimit' => $bindLimit,
		'extInfoElement' => 'infoProfilePackageAttributes', 'extBtnElement' => 'buttonEditProfilePackageAttributes',
		'includeIdText' => true
		]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="dependencies">
			<div class="row">
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $profile['ProfilesProfile'], 'controllerName' => 'profiles',
		'label' => __('Depends on profiles'), 'modelName' => 'ProfileDependency', 'bindLimit' => $bindLimit,
		'attrRefType' => ATTRIBUTE_TYPE_PROFILE, 'attrRefNode' => ATTRIBUTE_NODE_DEPENDS]);
?>
				</div>
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $profile['InDependencies'], 'controllerName' => 'profiles',
		'label' => __('Exists in dependencies'), 'bindLimit' => $bindLimit]);
?>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="variables">
<?php
	echo $this->element('infoVariables', ['variables' => $profile['Variable'], 'refType' => VARIABLE_TYPE_PROFILE, 'refId' => $profile['Profile']['id'],
		'showShort' => true, 'showBtnExpand' => true]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="hosts">
			<div class="row">
				<div class="col-md-6 col-xs-12">
<?php
	$btnActionsExistsProfiles = $this->ViewExtension->buttonLink(
		'fas fa-pencil-alt',
		'btn-warning',
		['controller' => 'profiles', 'action' => 'hosts', $profile['Profile']['id']],
		[
			'title' => __('Edit existing profile in hosts'),
			'action-type' => 'modal',
		]
	);
	echo $this->element('infoDependency', ['dependencies' => $profile['Host'], 'controllerName' => 'hosts',
		'label' => __('Associated profiles'), 'bindLimit' => $bindLimit, 'btnActions' => $btnActionsExistsProfiles]);
?>
				</div>
				<div class="col-md-6 col-xs-12">
<?php
	echo $this->element('infoDependency', ['dependencies' => $profile['HostMainProfiles'], 'controllerName' => 'profiles',
		'label' => __('Main profiles'), 'bindLimit' => $bindLimit]);
?>
				</div>
			</div>
		</div>
	</div>
</div>