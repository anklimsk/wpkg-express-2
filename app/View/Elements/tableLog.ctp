<?php
/**
 * This file is the view file of the application. Used to render
 *  table of log records.
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

if (!isset($logs)) {
	$logs = [];
}

if (!isset($groupActions)) {
	$groupActions = [];
}

if (!isset($usePost)) {
	$usePost = true;
}

if (!isset($listTypes)) {
	$listTypes = [];
}

if (!isset($shortInfo)) {
	$shortInfo = false;
}

if (!isset($moreRecords)) {
	$moreRecords = 0;
}

if (!isset($created)) {
	$created = null;
}
?>
<div class="table-responsive table-filter">
<?php
	if (!$shortInfo) {
		echo $this->Filter->openFilterForm($usePost);
	}
?>
	<table class="table table-hover table-striped table-condensed">
<?php
	if (!empty($created)) {
		echo $this->Html->tag('caption', __('Created: %s', $this->Time->i18nFormat($created, '%x %X')));
	}
?>
		<thead>
<?php
	$formInputs = [
		'Log.id' => [
			'label' => 'ID',
			'class-header' => 'action',
			'not-use-input' => true
		],
		'LogHost.name' => [
			'label' => __('Host name'),
			'class-header' => 'action',
		],
		'Log.type_id' => [
			'label' => __('Type'),
			'options' => $listTypes,
			'class-header' => 'action',
		],
		'Log.message' => [
			'label' => __('Message'),
		],
		'Log.date' => [
			'label' => __('Date of event'),
			'class-header' => 'action',
		],
	];
	if (!$shortInfo) {
		echo $this->Filter->createFilterForm($formInputs);
	} else {
		$tableHeaders = [
			[__('Host') => ['class' => 'action']],
			[__('Type') => ['class' => 'action']],
			__('Message'),
			[__('Date') => ['class' => 'action']],
		];
		echo $this->Html->tableHeaders($tableHeaders);
	}
?>
		</thead>
<?php if (!$shortInfo && !empty($logs) && $usePost) : ?>
		<tfoot>
<?php
	echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
		</tfoot>
<?php endif; ?>
		<tbody>
<?php
$prevHost = '';
foreach ($logs as $log) {
	if ($log['LogHost']['name'] !== $prevHost) {
		$prevHost = $log['LogHost']['name'];
		$tableSubHeaderAttrRow = ['class' => 'warning'];
		$subHeaderText = $this->Html->tag('em', $log['LogHost']['name']);
		$actions = '';
		$colspan = 3;
		if (!$shortInfo) {
			$actions = $this->ViewExtension->buttonLink(
				'far fa-trash-alt',
				'btn-danger',
				['controller' => 'logs', 'action' => 'clear', $log['Log']['host_id']],
				[
					'title' => __('Clear logs of host'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear logs of host \'%s\'?', h($log['LogHost']['name'])),
				]
			);
			$colspan = 5;
		}
		$tableSubHeaderRow = [
			[
				[$subHeaderText, ['colspan' => $colspan, 'class' => 'text-center']],
				[$actions, ['class' => 'action text-right']]
			]];
		echo $this->Html->tableCells($tableSubHeaderRow, $tableSubHeaderAttrRow, $tableSubHeaderAttrRow);
	}

	$tableRow = [];
	$attrRow = [];
	$actions = '';
	if (!$shortInfo) {
		$actions = $this->ViewExtension->buttonLink(
				'fas fa-trash-alt',
				'btn-danger',
				['controller' => 'logs', 'action' => 'delete', $log['Log']['id']],
				[
					'title' => __('Delete log record'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this log record?'),
				]
			);

		$tableRow[] = [
			$this->Filter->createFilterRowCheckbox('Log.id', $log['Log']['id']),
			['class' => 'action text-center']
		];
	}
	$tableRow[] = [
		__d('log', h($log['LogType']['name'])),
		['class' => 'action text-center', 'colspan' => 2]
	];
	$tableRow[] = $log['Log']['message'];
	$tableRow[] = [
		(!$shortInfo ? $this->ViewExtension->timeAgo($log['Log']['date']) : $this->Time->i18nFormat($log['Log']['date'], '%x %X')),
		['class' => 'text-nowrap']
	];
	if (!$shortInfo) {
		$tableRow[] = [$actions, ['class' => 'action text-right']];
	}

	echo $this->Html->tableCells([$tableRow], $attrRow, $attrRow);
}
?>
		</tbody>
	</table>
<?php
	if (!$shortInfo) {
		echo $this->Filter->closeFilterForm();
		echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
	}
?>
</div>
<?php
	if (!$shortInfo) {
		echo $this->ViewExtension->buttonsPaging();
	} elseif ($moreRecords > 0) {
		echo $this->Html->para(
			'text-right',
			$this->Html->tag('i',
				__(
					'...And %s more %s',
					$this->Number->format($moreRecords, ['thousands' => ' ', 'before' => '', 'places' => 0]),
					__n('record', 'records', $moreRecords)
				)
			)
		);
	}
