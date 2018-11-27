<?php
/**
 * This file is the view file of the application. Used to render
 *  list of package priorities.
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

	if (!isset($packagePriorities)) {
		$packagePriorities = [];
	}

	$list = [];
	foreach ($packagePriorities as $packagePriority) {
		$packagePriorityName = h($packagePriority['PackagePriority']['name']) . ' (' .
			$packagePriority['PackagePriority']['value'] . ')';
		$actions = $this->Html->tag('span',
			$this->ViewExtension->buttonLink(
				'fas fa-pencil-alt',
				'btn-warning',
				['controller' => 'package_priorities', 'action' => 'edit', $packagePriority['PackagePriority']['id']],
				[
					'title' => __('Edit package priority'),
					'action-type' => 'modal',
				]
			) .
			$this->ViewExtension->buttonLink(
				'fas fa-trash-alt',
				'btn-danger',
				['controller' => 'package_priorities', 'action' => 'delete', $packagePriority['PackagePriority']['id']],
				[
					'title' => __('Delete package priority'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete package priority \'%s\'?', $packagePriorityName),
					'data-update-modal-content' => true,
				]
			),
			['class' => 'action hide-popup']
		);
		$list[] = $this->Html->tag('span', $packagePriorityName) . $actions;
	}
	$infoPackagePriorities = $this->ViewExtension->showEmpty($list, $this->Html->nestedList($list, ['class' => 'list-unstyled'], [], 'ul'));
?>
<dl class="dl-horizontal dl-popup-modal">
	<dt><?php echo __('Package priorities') . ':'; ?></dt>
	<dd>
<?php
	$actions = $this->ViewExtension->buttonLink(
		'fas fa-plus',
		'btn-success',
		['controller' => 'package_priorities', 'action' => 'add'],
		[
			'title' => __('Add package priority'),
			'action-type' => 'modal',
		]
	);
	echo $this->Html->div('action pull-right hide-popup', $actions);
	echo $this->Html->div('pull-left', $infoPackagePriorities);
?>
	</dd>
</dl>
<?php
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
	echo $this->ViewExtension->barPaging(true, false, false, false);