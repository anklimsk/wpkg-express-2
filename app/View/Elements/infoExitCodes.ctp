<?php
/**
 * This file is the view file of the application. Used to render
 *  information about exit codes.
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

if (!isset($exitCodes)) {
	$exitCodes = [];
}

if (!isset($packageActionId)) {
	$packageActionId = null;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($showShort)) {
	$showShort = false;
}

$list = [];
foreach ($exitCodes as $exitCode) {
	if (isset($exitCode['ExitCode'])) {
		$exitCode = array_merge($exitCode, $exitCode['ExitCode']);
		unset($exitCode['ExitCode']);
	}

	$exitCodeValue = h($exitCode['code']);
	$exitCodeName = $exitCodeValue;
	if (isset($exitCode['ExitCodeDirectory'][0]['description']) &&
		!empty($exitCode['ExitCodeDirectory'][0]['description'])) {
		$exitCodeName = $this->Html->tag(
			'abbr',
			$exitCodeValue,
			[
				'title' => $exitCode['ExitCodeDirectory'][0]['description'],
				'data-toggle' => 'tooltip'
			]
		);
	}
	$exitCodeDisplayName = $this->Html->tag('strong', $exitCodeName) . ' - ' .
		$this->Html->tag('var', mb_ucfirst(__d('exit_code_reboot', h($exitCode['ExitcodeRebootType']['name']))));

	$actions = '';
	if (!$showShort) {
		$actions = '&nbsp;' . $this->Html->tag('span',
			$this->ViewExtension->buttonLink(
				'fas fa-pencil-alt',
				'btn-warning',
				['controller' => 'exit_codes', 'action' => 'edit', $exitCode['id']],
				[
					'title' => __('Edit exit code'),
					'action-type' => 'modal',
				]
			) .
			$this->ViewExtension->buttonLink(
				'fas fa-trash-alt',
				'btn-danger',
				['controller' => 'exit_codes', 'action' => 'delete', $exitCode['id']],
				[
					'title' => __('Delete exit code'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete exit code \'%s\'?', $exitCodeValue),
					'data-update-modal-content' => true,
				]
			),
			['class' => 'action hide-popup']
		);
	}
	$list[] = $this->Html->tag('span', $exitCodeDisplayName, ['class' => 'text-nowrap']) . $actions;
}
$infoExitCodes = $this->ViewExtension->showEmpty($list, $this->Html->nestedList($list, ['class' => 'list-unstyled'], [], 'ul'));

if ($showShort):
	echo $infoExitCodes;
else: ?>
	<dl class="dl-horizontal dl-popup-modal">
	<?php if (!empty($fullName)): ?>
		<dt><?php echo __('Exit code type') . ':'; ?></dt>
		<dd><?php echo h($fullName); ?></dd>
	<?php endif; ?>
		<dt><?php echo __('Exit codes') . ':'; ?></dt>
		<dd>
	<?php
		if (!empty($packageActionId)) {
			$actions = $this->ViewExtension->buttonLink(
				'fas fa-plus',
				'btn-success',
				['controller' => 'exitcodes', 'action' => 'add', $packageActionId],
				[
					'title' => __('Add variable'),
					'action-type' => 'modal',
				]
			);
			echo $this->Html->div('action pull-right hide-popup', $actions);
		}
		echo $this->Html->div('pull-left', $infoExitCodes); ?>
		</dd>
	</dl>
	<?php
	echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
endif;
