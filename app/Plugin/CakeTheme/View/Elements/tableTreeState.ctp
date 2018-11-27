<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  table of errors of state tree.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @see TreeBehavior::verify()
 * @package plugin.View.Elements
 */

if (!isset($treeState)) {
	$treeState = true;
}

if (!isset($urlNode)) {
	$urlNode = null;
}

if ($treeState === true) {
	echo $this->Html->div(
		'alert alert-success alert-dismissible',
		$this->Html->tag(
			'button',
			$this->Html->tag('span', '&times;', ['aria-hidden' => 'true']),
			['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']
		) .
		__d('view_extension', 'Errors of state tree is not found'),
		['role' => 'alert']
	);

	return;
} elseif (!is_array($treeState)) {
	return;
}
?>
	<div class="table-responsive">
		<table class="table table-hover table-striped table-condensed">
			<thead>
<?php
	$tableHeader = [];
	$tableHeader[] = __d('view_extension', 'Type');
	$tableHeader[] = __d('view_extension', 'Value');
	$tableHeader[] = __d('view_extension', 'Message');
	echo $this->Html->tableHeaders($tableHeader);
?>
			</thead>
			<tbody>
<?php
foreach ($treeState as $treeStateItem) {
	$urlNodeItem = [];
	if (!empty($urlNode) && ($treeStateItem[0] === 'node') &&
		ctype_digit($treeStateItem[1])) {
		$urlNodeItem = $urlNode + [$treeStateItem[1]];
	}
	$tableRow = [];
	foreach ($treeStateItem as $i => $treeStateItemText) {
		$treeStateItemTextDispl = h($treeStateItemText);
		if (($i == 1) && (!empty($urlNodeItem))) {
			$treeStateItemTextDispl = $this->Html->link(
				$treeStateItemTextDispl,
				$urlNodeItem,
				['target' => '_blank']
			);
		}
		$tableRow[] = $treeStateItemTextDispl;
	}
	echo $this->Html->tableCells($tableRow);
}
?>
			</tbody>
		</table>
	</div>
