<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  tree of subordinate employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Employees
 */

	echo $this->AssetCompress->css('CakeTheme.tree', ['block' => 'css']);
	echo $this->AssetCompress->script('CakeTheme.tree', ['block' => 'script']);

	$this->assign('title', $pageHeader);
	$this->ViewExtension->addBreadCrumbs($breadCrumbs);
?>
	<div class="container">
<?php
		echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
?>
		<div class="row bottom-buffer">
			<div class="col-xs-8 col-xs-offset-2 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
<?php
		echo $this->element('CakeLdap.infoSubordinate', [
			'subordinate' => $employees,
			'draggable' => $isTreeDraggable,
			'expandAll' => $expandAll,
		]);
?>
			</div>
		</div>
	</div>
