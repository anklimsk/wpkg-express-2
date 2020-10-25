<?php
/**
 * This file is the view file of the application. Used to render
 *  information about result of verifying list package actions.
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

if (!isset($actionsState)) {
	$actionsState = null;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($refId)) {
	$refId = null;
}
?>
	<dl class="dl-horizontal dl-popup-modal">
		<dt><?php echo __('Package actions type') . ':'; ?></dt>
		<dd><?php echo h($fullName); ?></dd>
	</dl>
<?php
	foreach ($actionsState as $actionState): ?>
	<hr />
	<dl class="dl-horizontal dl-popup-modal">
		<dt><?php echo __('Action type') . ':'; ?></dt>
		<dd>
	<?php
		if ($actionState['actionState'] !== true) {
			$actions = $this->ViewExtension->buttonLink(
				'fas fa-redo-alt',
				'btn-warning',
				['controller' => 'actions', 'action' => 'recover', $actionState['actionId'], $refId],
				[
					'title' => __('Recovery state of list package actions'),
					'data-toggle' => 'request-only'
				]
			);
			echo $this->Html->div('action pull-right hide-popup', $actions);
		}
		echo $this->Html->div('pull-left', mb_ucfirst(__d('package_action_type', h($actionState['actionName']))));
	?>
		</dd>
		<dt><?php echo __('Result of verifying') . ':'; ?></dt>
		<dd>
	<?php echo $this->element('CakeTheme.tableTreeState', ['treeState' => $actionState['actionState']]); ?>
		</dd>
	</dl>
	<?php
	endforeach;