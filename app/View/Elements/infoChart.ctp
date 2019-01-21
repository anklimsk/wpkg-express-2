<?php
/**
 * This file is the view file of the application. Used to render
 *  information about chart.
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

if (!isset($chartType)) {
	$chartType = null;
}

if (!isset($chartTitle)) {
	$chartTitle = null;
}

if (!isset($chartClickUrl)) {
	$chartClickUrl = null;
}

if (!isset($refType)) {
	$refType = null;
}

if (!isset($refId)) {
	$refId = null;
}

$chartUrl = [
	'controller' => 'charts',
	'action' => 'dataset',
	'ext' => 'json'
];
?>
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="chartListMessages">
<?php
	echo $this->Html->div(
		'alert alert-warning',
		__('No data to display'),
		['role' => 'alert', 'id' => 'msgChartNoData']
	) .
	$this->Html->div(
		'alert alert-info hidden',
		__('Please wait. Creating a chart in progress...'),
		['role' => 'alert', 'id' => 'msgChartProgress']
	) .
	$this->Html->div(
		'alert alert-danger hidden',
		__('Error on creating chart'),
		['role' => 'alert', 'id' => 'msgChartError']
	);
?>
			</div>
			<div class="text-center">
<?php
	$attributes = [
		'escape' => false,
		'id' => 'chart-wpkg',
		'data-chart-type' => $chartType,
		'data-chart-title' => $chartTitle,
		'data-chart-ref-type' => $refType,
		'data-chart-ref-id' => $refId,
		'data-chart-url' => $this->Html->url($chartUrl),
		'data-chart-click-url' => $this->Html->url($chartClickUrl)
	];
	echo $this->Html->tag('canvas', '', $attributes);
?>
			</div>
		</div>
	</div>