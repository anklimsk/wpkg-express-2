<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  result of checking state of tree subordinate employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Employees
 */

	$this->assign('title', $pageHeader);
	$this->ViewExtension->addBreadCrumbs($breadCrumbs);
?>  
	<div class="container">
<?php
		echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
		$urlNode = [
			'controller' => 'employees',
			'action' => 'view',
		];
		echo $this->element('CakeTheme.tableTreeState', compact('treeState', 'urlNode'));
?>
	</div>
