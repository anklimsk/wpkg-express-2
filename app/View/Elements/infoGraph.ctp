<?php
/**
 * This file is the view file of the application. Used to render
 *  information about graph.
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

if (!isset($useBuildGraph)) {
	$useBuildGraph = false;
}
?>
	<div class="well well-sm">
<?php
	$formUrl = ['controller' => 'graph', 'action' => 'generate'];
	$formOptions = ['class' => 'form-inline', 'url' => $formUrl, 'type' => 'post', 'skip-modal' => true];
	echo $this->Form->create('GraphViz', $formOptions);
?>
		<fieldset>
<?php
	echo $this->Form->hidden('GraphViz.ref_type');
	echo $this->Form->hidden('GraphViz.ref_id');
	if ($useBuildGraph) {
		echo $this->Form->input(
			'GraphViz.host_name',
			[
				'type' => 'text',
				'label' => __('Host name') . ':',
				'title' => __('Build graph for host by name. Supports autocomplete.'),
				'maxlength' => '50',
				'autocomplete' => 'off',
				'data-toggle' => 'autocomplete',
				'data-autocomplete-url' => $this->Html->url(['controller' => 'hosts', 'action' => 'autocomplete', 'ext' => 'json']),
				'data-autocomplete-min-length' => CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH,
				'beforeInput' => '<div class="input-group">',
				'afterInput' => $this->Html->div(
					'input-group-btn',
					$this->ViewExtension->button('fas fa-pencil-ruler', 'btn btn-default',
						['id' => 'btnGenerateId', 'title' => __('Build a graph.'), 'data-toggle' => 'title',
						'type' => 'submit'])
					) . '</div>'
			]
		);
	} else {
		echo $this->Form->hidden('GraphViz.host_name');
	}
	echo $this->Form->input(
		'GraphViz.full_graph',
		[
			'label' => [__('Show full information'),
			__('Show full information about relations.')],
			'type' => 'checkbox',
			'autocomplete' => 'off'
		]
	);
?>
		</fieldset>
<?php
	echo $this->Form->end();
?>
	</div>
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="graphListMessages">
<?php
	echo $this->Html->div(
		'alert alert-warning',
		__('No data to display'),
		['role' => 'alert', 'id' => 'msgGraphNoData']
	) .
	$this->Html->div(
		'alert alert-info hidden',
		__('Please wait. Creating a graph in progress...'),
		['role' => 'alert', 'id' => 'msgGraphProgress']
	) .
	$this->Html->div(
		'alert alert-danger hidden',
		__('Error on creating graph'),
		['role' => 'alert', 'id' => 'msgGraphError']
	);
?>
			</div>
			<div class="text-center" id="graph-svg" data-graph-init="<?php echo ($useBuildGraph ? 'true' : 'false'); ?>"></div>
		</div>
	</div>