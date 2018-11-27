<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  table of employees.
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
<div class="container-fluid">
<?php
	echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
	echo $this->element('CakeLdap.tableEmployee', compact('employees', 'filterOptions', 'fieldsConfig'));
?>
</div>
