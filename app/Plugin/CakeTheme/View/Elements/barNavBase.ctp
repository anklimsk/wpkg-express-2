<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  navigation bar of main menu.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	App::uses('Hash', 'Utility');
	$this->loadHelper('CakeTheme.ViewExtension');

if (!isset($showSearchForm)) {
	$showSearchForm = false;
}
if ($showSearchForm) {
	if (CakePlugin::loaded('CakeSearchInfo')) {
		$this->loadHelper('CakeSearchInfo.Search');
	} else {
		$showSearchForm = false;
	}
}

if (!isset($useNavbarContainerFluid)) {
	$useNavbarContainerFluid = false;
}

if (!isset($projectName)) {
	$projectName = '';
}

if (!isset($projectLogo)) {
	$projectLogo = '';
}

if (!isset($iconList)) {
	$iconList = [];
}

if (!isset($search_targetFields)) {
	$search_targetFields = [];
}

if (!isset($search_targetFieldsSelected)) {
	$search_targetFieldsSelected = [];
}

if (!isset($search_querySearchMinLength)) {
	$search_querySearchMinLength = 0;
}

if (!isset($search_targetDeep)) {
	$search_targetDeep = 0;
}

if (!isset($search_urlActionSearch)) {
	$search_urlActionSearch = null;
}

if (!empty($iconList)) {
	array_unshift($iconList, $this->ViewExtension->menuItemLink('fas fa-home fa-lg', __d('view_extension', 'Home page'), '/'));
}
?>
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="<?php echo ($showSearchForm && $useNavbarContainerFluid ? 'container-fluid' : 'container'); ?>">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
<?php
	$headerText = '';
if (!empty($projectName)) {
	$headerText = $this->Html->tag('span', $projectName, ['class' => ($showSearchForm ? 'hidden-sm hidden-md' : null)]);
}

	$projectLogoImg = '';
if (!empty($projectLogo)) {
	$projectLogoImg = $this->Html->tag('span', $this->Html->image($projectLogo, ['class' => 'brand-logo']));
}

if (!empty($projectLogo) || !empty($projectName)) {
	echo $this->Html->link($projectLogoImg . $headerText, '/', ['class' => 'navbar-brand text-nowrap', 'escape' => false]);
}
?>
			</div>
			<div id="mainNavbar" class="collapse navbar-collapse">
<?php
	$menuList = $this->ViewExtension->getMenuList($iconList);
if (!empty($menuList)) {
	echo $menuList;
}
if ($showSearchForm) {
	echo $this->Search->createFormSearch($search_targetFields, $search_targetFieldsSelected, $search_urlActionSearch, $search_targetDeep, $search_querySearchMinLength);
}
?>
			</div>
		</div>
	</div>
