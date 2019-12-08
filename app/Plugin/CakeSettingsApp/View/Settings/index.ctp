<?php
/**
 * This file is the view file of the plugin. Used for render
 *  interface of settings application.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Settings
 */

	echo $this->AssetCompress->css('CakeTheme.flagstrap', ['block' => 'css']);
	echo $this->AssetCompress->script('CakeTheme.flagstrap', ['block' => 'script']);

	$this->assign('title', $pageHeader);
	$this->ViewExtension->addBreadCrumbs($breadCrumbs);
?>
	<div class="container">
<?php
	echo $this->ViewExtension->headerPage($pageHeader);
	echo $this->element('CakeSettingsApp.formSettings', compact(
		'groupList',
		'configUIlangs',
		'configSMTP',
		'configAcLimit',
		'configADsearch',
		'configExtAuth',
		'authGroups',
		'varsExt'
	));
?>
	</div>
