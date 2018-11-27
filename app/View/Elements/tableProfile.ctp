<?php
/**
 * This file is the view file of the application. Used to render
 *  table of profiles.
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

if (!isset($profiles)) {
	$profiles = [];
}

if (!isset($groupActions)) {
	$groupActions = [];
}

if (!isset($usePost)) {
	$usePost = true;
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm($usePost); ?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'Profile.id' => [
			'label' => 'ID',
			'disabled' => true,
			'class-header' => 'action',
			'not-use-input' => true
		],
		'Profile.enabled' => [
			'label' => __('Enabled'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Profile.template' => [
			'label' => __('Template'),
			'options' => $this->ViewExtension->yesNoList(),
			'class-header' => 'action',
		],
		'Profile.id_text' => [
			'label' => __('Profile ID'),
		],
		'Profile.notes' => [
			'label' => __('Notes'),
		],
		'Profile.modified' => [
			'label' => __('Last modified'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
<?php if (!empty($profiles) && $usePost) : ?>
		<tfoot>
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
foreach ($profiles as $profile) {
	$tableRow = [];
	$attrRow = [];
	$profileState = $profile['Profile']['enabled'];
	$profileName = h($profile['Profile']['id_text']);
	$actions = $this->ViewExtension->buttonLink(
			'far fa-file-code',
			'btn-info',
			['controller' => 'profiles', 'action' => 'preview', $profile['Profile']['id']],
			[
				'title' => __('Preview XML'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-file-download',
			'btn-info',
			['controller' => 'profiles', 'action' => 'download', $profile['Profile']['id'], 'ext' => 'xml'],
			[
				'title' => __('Download XML file'),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-project-diagram',
			'btn-info',
			['controller' => 'graph', 'action' => 'view', GRAPH_TYPE_PROFILE, $profile['Profile']['id']],
			[
				'title' => __('Graph of profile'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-terminal',
			'btn-success',
			['controller' => 'profiles', 'action' => 'packages', $profile['Profile']['id']],
			[
				'title' => __('Modify list of associated packages'),
				'action-type' => 'modal',
			]
		);
		if ($profile['Profile']['template']) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-plus-square',
				'btn-warning',
				['controller' => 'profiles', 'action' => 'create', $profile['Profile']['id']],
				[
					'title' => __('Create profile from this template'),
					'action-type' => 'modal',
				]
			);
		}
		$actions .= $this->ViewExtension->buttonLink(
			'fas fa-copy',
			'btn-warning',
			['controller' => 'profiles', 'action' => 'copy', $profile['Profile']['id']],
			[
				'title' => __('Copy profile'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to copy profile \'%s\'?', $profileName),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-pencil-alt',
			'btn-warning',
			['controller' => 'profiles', 'action' => 'edit', $profile['Profile']['id']],
			[
				'title' => __('Edit profile'),
				'action-type' => 'modal',
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'profiles', 'action' => 'delete', $profile['Profile']['id']],
			[
				'title' => __('Delete profile'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete profile \'%s\'?', $profileName),
			]
		);

	if (!$profileState) {
		$attrRow['class'] = 'warning';
	} elseif ($profile['Profile']['template']) {
		$attrRow['class'] = 'info';
	}
	$tableRow[] = [
		$this->Filter->createFilterRowCheckbox('Profile.id', $profile['Profile']['id']),
		['class' => 'action text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($profile['Profile']['enabled']),
			['controller' => 'profiles', 'action' => 'enabled', $profile['Profile']['id'], !$profile['Profile']['enabled']],
			['title' => ($profile['Profile']['enabled'] ? __('Disable profile') : __('Enable profile'))]
		),
		['class' => 'text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($profile['Profile']['template']),
			['controller' => 'profiles', 'action' => 'template', $profile['Profile']['id'], !$profile['Profile']['template']],
			['title' => ($profile['Profile']['template'] ? __('Don\'t use as template') : __('Use as template'))]
		),
		['class' => 'text-center']
	];
	if (!$profileState) {
		$profileName = $this->Html->tag('s', $profileName);
	}
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$profileName,
		['controller' => 'profiles', 'action' => 'view', $profile['Profile']['id']],
		['data-modal-size' => 'lg']
	);
	$tableRow[] = $this->ViewExtension->showEmpty(
		$profile['Profile']['notes'],
		$this->ViewExtension->truncateText(h($profile['Profile']['notes']), 50)
	);
	$tableRow[] = $this->ViewExtension->timeAgo($profile['Profile']['modified']);
	$tableRow[] = [$actions, ['class' => 'action text-right']];

	echo $this->Html->tableCells([$tableRow], $attrRow, $attrRow);
}
?>
		</tbody>
	</table>
<?php
	echo $this->Filter->closeFilterForm();
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
?>
</div>
<?php
	echo $this->ViewExtension->buttonsPaging();
