<?php
/**
 * This file is the view file of the application. Used to render
 *  information about host.
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

if (!isset($host)) {
	$host = [];
}

if (!isset($bindLimit)) {
	$bindLimit = null;
}

if (empty($host)) {
	return;
}

$hostName = h($host['Host']['id_text']);
$mainProfileName = h($host['MainProfile']['id_text']);
if (!$host['MainProfile']['enabled']) {
	$mainProfileName = $this->Html->tag('s', $mainProfileName);
}
?>
<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', __('Position') . ':');
	echo $this->Html->tag('dd', $this->Number->format(
		($host['Host']['lft'] + 1) / 2,
		['thousands' => ' ', 'before' => '', 'places' => 0]
	));
	echo $this->Html->tag('dt', __('Enabled') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($host['Host']['enabled']),
		['controller' => 'hosts', 'action' => 'enabled', $host['Host']['id'], !$host['Host']['enabled']],
		['title' => ($host['Host']['enabled'] ? __('Disable host') : __('Enable host'))]
	));
	echo $this->Html->tag('dt', __('Template') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
		$this->ViewExtension->yesNo($host['Host']['template']),
		['controller' => 'hosts', 'action' => 'template', $host['Host']['id'], !$host['Host']['template']],
		['title' => ($host['Host']['template'] ? __('Don\'t use as template') : __('Use as template'))]
	));
	echo $this->Html->tag('dt', __('Host ID') . ':');
	echo $this->Html->tag('dd', $hostName .
		$this->Indicator->createIndicator($host['Host']['template'], __('template'), __('Use this host as a template')));
	echo $this->Html->tag('dt', __('Main profile') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->popupModalLink(
		$mainProfileName,
		['controller' => 'profiles', 'action' => 'view', $host['MainProfile']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	));
	echo $this->Html->tag('dt', __('Notes') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->showEmpty(h($host['Host']['notes'])));
	echo $this->Html->tag('dt', __('Last modified') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->timeAgo($host['Host']['modified']));
?>
</dl>
<div class="container-fluid top-buffer">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab"><?php echo __('Attributes'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#variables" aria-controls="variables" role="tab" data-toggle="tab"><?php echo __('Variables'); ?></a></li>
		<li role="presentation" class="hide-popup"><a href="#profiles" aria-controls="profiles" role="tab" data-toggle="tab"><?php echo __('Additional associated profiles'); ?></a></li>
	</ul>
	<div class="tab-content top-buffer">
		<div role="tabpanel" class="tab-pane active" id="attributes">
<?php
	$btnEditAttributes = $this->Html->div('pull-right hide-popup', $this->ViewExtension->buttonLink(
		'fas fa-tasks',
		'btn-success',
		['controller' => 'attributes', 'action' => 'modify', ATTRIBUTE_TYPE_HOST, ATTRIBUTE_NODE_HOST, $host['Host']['id']],
		[
			'title' => __('Edit attributes'),
			'action-type' => 'modal',
		]
	));
	$hostAttributes = $this->Html->div('pull-left',
		$this->ViewExtension->showEmpty($host['Attribute'], $this->element('infoAttributes', ['attributes' => $host['Attribute'], 'showShort' => true, 'displayInline' => false]))) . $btnEditAttributes;
	echo $hostAttributes;
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="variables">
<?php
	echo $this->element('infoVariables', ['variables' => $host['Variable'], 'refType' => VARIABLE_TYPE_HOST, 'refId' => $host['Host']['id'],
		'showShort' => true, 'showBtnExpand' => true]);
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="profiles">
<?php
	echo $this->element('infoDependency', ['dependencies' => $host['HostsProfile'], 'controllerName' => 'profiles',
		'modelName' => 'Profile', 'bindLimit' => $bindLimit,
		'attrRefType' => ATTRIBUTE_TYPE_HOST, 'attrRefNode' => ATTRIBUTE_NODE_PROFILE]);
?>
		</div>
	</div>
</div>
