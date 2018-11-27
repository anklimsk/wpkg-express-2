<?php
/**
 * This file is the view file of the application. Used for settings application.
 *
 * @copyright Copyright 2014-2015, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.View.Settings
 */

	$this->assign('title', $pageHeader);
	$this->ViewExtension->addBreadCrumbs($breadCrumbs);
?>
<div class="container-fluid" data-toggle="repeat" data-repeat-time="300">
<?php
	echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
if (!empty($stateData)) {
	echo $this->Html->tag('h3', __d('cake_settings_app', 'State queue of tasks'), ['class' => 'text-center']);
	echo $this->ViewExtension->barState($stateData);
}
	echo $this->element('tableQueue', compact('queue', 'groupActions', 'taskStateList', 'usePost'));
?>
</div>
