<?php
/**
 * This file is the view file of the application. Used to render
 *  table of exit code directory.
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

if (!isset($exitCodeDirectory)) {
	$exitCodeDirectory = [];
}
?>
<div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm(false); ?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'ExitCodeDirectory.code' => [
			'label' => __('Exit code'),
			'class-header' => 'fit',
			'style' => 'min-width: 60px'
		],
		'ExitCodeDirectory.hexadecimal' => [
			'label' => __('Hexadecimal'),
			'class-header' => 'fit',
			'style' => 'min-width: 150px'
		],
		'ExitCodeDirectory.constant' => [
			'label' => __('Constant'),
			'class-header' => 'fit',
			'style' => 'min-width: 230px'
		],
		'ExitCodeDirectory.description' => [
			'label' => __('Description'),
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
		<tbody>
<?php
foreach ($exitCodeDirectory as $exitCodeDirectoryRecord) {
	$tableRow = [];
	$exitCodeName = $this->Html->tag('samp', h($exitCodeDirectoryRecord['ExitCodeDirectory']['code']));
	$actions = $this->ViewExtension->buttonLink(
		'fas fa-pencil-alt',
		'btn-warning',
		['controller' => 'exit_code_directory', 'action' => 'edit', $exitCodeDirectoryRecord['ExitCodeDirectory']['id']],
		[
			'title' => __('Edit record'),
			'action-type' => 'modal',
		]
	) .
	$this->ViewExtension->buttonLink(
		'fas fa-trash-alt',
		'btn-danger',
		['controller' => 'exit_code_directory', 'action' => 'delete', $exitCodeDirectoryRecord['ExitCodeDirectory']['id']],
		[
			'title' => __('Delete record'), 'action-type' => 'confirm-post',
			'data-confirm-msg' => __('Are you sure you wish to delete exit code \'%s\'?', $exitCodeName),
		]
	);
	$tableRow[] = [
		$exitCodeName,
		['class' => 'text-center']
	];
	$tableRow[] = [
		$this->ViewExtension->showEmpty(h($exitCodeDirectoryRecord['ExitCodeDirectory']['hexadecimal'])),
		['class' => 'text-center']
	];
	$tableRow[] = $this->ViewExtension->showEmpty(h($exitCodeDirectoryRecord['ExitCodeDirectory']['constant']));
	$tableRow[] = $this->ViewExtension->showEmpty(
		$exitCodeDirectoryRecord['ExitCodeDirectory']['description'],
		$this->ViewExtension->truncateText(h($exitCodeDirectoryRecord['ExitCodeDirectory']['description']), 80)
	);
	$tableRow[] = [$actions, ['class' => 'action text-right']];

	echo $this->Html->tableCells([$tableRow]);
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
