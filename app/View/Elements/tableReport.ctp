<?php
/**
 * This file is the view file of the application. Used to render
 *  table of report records.
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

if (!isset($reports)) {
	$reports = [];
}

if (!isset($listStates)) {
	$listStates = [];
}
?>
<div class="table-responsive table-filter">
<?php
	echo $this->Filter->openFilterForm(false);

?>
	<table class="table table-hover table-striped table-condensed">
		<thead>
<?php
	$formInputs = [
		'ReportHost.name' => [
			'label' => __('Host name'),
			'class-header' => 'action',
		],
		'ReportHost.date' => [
			'label' => __('Last update'),
			'class-header' => 'action',
		],
		'Report.state_id' => [
			'label' => __('State'),
			'options' => $listStates,
			'class-header' => 'action',
		],
		'Package.name' => [
			'label' => __('Package'),
		],
		'Report.revision' => [
			'label' => __('Revision of package'),
			'class-header' => 'action',
		],
	];
	echo $this->Filter->createFilterForm($formInputs);
?>
		</thead>
		<tbody>
<?php
$prevHost = null;
foreach ($reports as $report) {
	if ($report['Report']['host_id'] !== $prevHost) {
		$prevHost = $report['Report']['host_id'];
		$tableSubHeaderAttrRow = ['class' => 'warning'];
		$subHeaderText = $this->Html->tag('em', $report['ReportHost']['name']) . ' ' .
			$this->ViewExtension->timeAgo($report['ReportHost']['date']) . ' ' .
			$this->element('infoAttributes', ['attributes' => $report['ReportHost']['Attribute'], 'showShort' => true, 'displayInline' => true]);
		$actions = $this->ViewExtension->buttonLink(
			'fas fa-sync-alt',
			'btn-primary',
			['controller' => 'reports', 'action' => 'parse', $report['ReportHost']['name']],
			[
				'title' => __('Refresh report of host'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to refresh report of host \'%s\'?', h($report['ReportHost']['name'])),
			]
		) .
		$this->ViewExtension->buttonLink(
			'far fa-trash-alt',
			'btn-danger',
			['controller' => 'reports', 'action' => 'clear', $report['Report']['host_id']],
			[
				'title' => __('Clear reports of host'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to clear reports of host \'%s\'?', h($report['ReportHost']['name'])),
			]
		) .
		$this->ViewExtension->buttonLink(
			'fas fa-trash-alt',
			'btn-danger',
			['controller' => 'reports', 'action' => 'rename', $report['ReportHost']['name']],
			[
				'title' => __('Remove (rename) client database'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to remove (rename) client database of host \'%s\'?', h($report['ReportHost']['name'])),
			]
		);
		$tableSubHeaderRow = [
			[
				[$subHeaderText, ['colspan' => 5, 'class' => 'text-center']],
				[$actions, ['class' => 'action text-right']]
			]];
		echo $this->Html->tableCells($tableSubHeaderRow, $tableSubHeaderAttrRow, $tableSubHeaderAttrRow);
	}
	
	$tableRow = [];
	$attrRow = [];
	$packageState = $report['Package']['enabled'];
	$packageName = h($report['Package']['name']);
	if (!$packageState) {
		$packageName = $this->Html->tag('s', $packageName);
	}

	$revision = $report['Report']['revision'];
	switch ($report['Report']['state_id']) {
		case REPORT_STATE_OK_MANUAL:
			$attrRow['class'] = 'active';
		break;
		case REPORT_STATE_UPGRADE:
			$attrRow['class'] = 'info';
			$revision .= ' ' . $this->ViewExtension->iconTag('fas fa-long-arrow-alt-right') . ' ';
			$revision .= $report['Package']['revision'];
		break;
		case REPORT_STATE_DOWNGRADE:
			$attrRow['class'] = 'warning';
			$revision .= ' ' . $this->ViewExtension->iconTag('fas fa-long-arrow-alt-left') . ' ';
			$revision .= $report['Package']['revision'];
		break;
	}
	$tableRow[] = [
		__d('report_state', h($report['ReportState']['name'])),
		['class' => 'action text-center', 'colspan' => 3]
	];
	$tableRow[] = $this->ViewExtension->popupModalLink(
		$packageName,
		['controller' => 'packages', 'action' => 'view', $report['Package']['id']],
		['data-modal-size' => 'lg']
	);
	$tableRow[] = [$revision, ['class' => 'action text-nowrap', 'colspan' => 2]];

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
