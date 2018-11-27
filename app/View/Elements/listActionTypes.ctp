<?php
/**
 * This file is the view file of the application. Used to render
 *  list of package action types.
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

	if (!isset($actionTypes)) {
		$actionTypes = [];
	}

	$list = [];
	foreach ($actionTypes as $actionType) {
		$actionTypeName = mb_ucfirst(__d('package_action_type', h($actionType['PackageActionType']['name'])));
		if ($actionType['PackageActionType']['builtin']) {
			$actionTypeName = $this->Html->tag('samp', $actionTypeName);
		}
		$actions = '';
		if (!$actionType['PackageActionType']['builtin']) {
			$actions = '&nbsp;' . $this->Html->tag('span',
				$this->ViewExtension->buttonLink(
					'fas fa-pencil-alt',
					'btn-warning',
					['controller' => 'action_types', 'action' => 'edit', $actionType['PackageActionType']['id']],
					[
						'title' => __('Edit action type'),
						'action-type' => 'modal',
					]
				) .
				$this->ViewExtension->buttonLink(
					'fas fa-trash-alt',
					'btn-danger',
					['controller' => 'action_types', 'action' => 'delete', $actionType['PackageActionType']['id']],
					[
						'title' => __('Delete action type'), 'action-type' => 'confirm-post',
						'data-confirm-msg' => __('Are you sure you wish to delete action type \'%s\'?', $actionTypeName),
						'data-update-modal-content' => true,
					]
				),
				['class' => 'action hide-popup']
			);
		}
		$list[] = $this->Html->tag('span', $actionTypeName) . $actions;
	}
	$infoActionTypes = $this->ViewExtension->showEmpty($list, $this->Html->nestedList($list, ['class' => 'list-unstyled'], [], 'ul'));
?>
<dl class="dl-horizontal dl-popup-modal">
	<dt><?php echo __('Action types') . ':'; ?></dt>
	<dd>
<?php
	$actions = $this->ViewExtension->buttonLink(
		'fas fa-plus',
		'btn-success',
		['controller' => 'action_types', 'action' => 'add'],
		[
			'title' => __('Add action type'),
			'action-type' => 'modal',
		]
	);
	echo $this->Html->div('action pull-right hide-popup', $actions);
	echo $this->Html->div('pull-left', $infoActionTypes);
?>
	</dd>
</dl>
<?php
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
	echo $this->ViewExtension->barPaging(true, false, false, false);