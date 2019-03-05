<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of page as `main` style.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */

if (!isset($isExternalAuth)) {
	$isExternalAuth = false;
}

if (!isset($emailContact)) {
	$emailContact = '';
}

if (!isset($emailSubject)) {
	$emailSubject = '';
}

if (!isset($projectVersion)) {
	$projectVersion = null;
}

if (!isset($projectAuthor)) {
	$projectAuthor = null;
}

if (!isset($showBreadcrumb)) {
	$showBreadcrumb = true;
}

if (!isset($showFooter)) {
	$showFooter = true;
}

if (!isset($uiLcid2) || empty($uiLcid2)) {
	$uiLcid2 = 'en';
}

if (isset($pageTitlePrefix) && !empty($pageTitlePrefix)) {
	$this->prepend('title', $pageTitlePrefix);
}

if (isset($pageTitlePostfix) && !empty($pageTitlePostfix)) {
	$this->append('title', $pageTitlePostfix);
}

	$this->ActionScript->css(['block' => 'css'], $uiLcid3);
	$this->ActionScript->script(['block' => 'script'], $uiLcid3);
?>
<!DOCTYPE html>
<html lang="<?php echo $uiLcid2; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->fetch('title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	echo $this->Html->meta('icon');

	echo $this->AssetCompress->css('CakeTheme.libs');
	echo $this->AssetCompress->css('CakeTheme.main');
	echo $this->AssetCompress->script('CakeTheme.libs');
	echo $this->AssetCompress->script('CakeTheme.libs-min');
	echo $this->AssetCompress->script('CakeTheme.main');
	echo $this->AssetCompress->script('CakeTheme.main-min');
	echo $this->AssetCompress->script('CakeTheme.main-i18n-' . $uiLcid2);
?>
<!--[if gt IE 9]><!-->
<?php
	echo $this->AssetCompress->css('CakeTheme.noty');
	echo $this->AssetCompress->script('CakeTheme.noty');
?>
<!--<![endif]-->
<?php
	echo $this->AssetCompress->css('CakeTheme.main-plugins');
if (isset($additionalCssFiles) && !empty($additionalCssFiles)) {
	foreach ($additionalCssFiles as $additionalCssFile) {
		echo $this->AssetCompress->css($additionalCssFile);
	}
}
	echo $this->fetch('css');
	echo $this->AssetCompress->script('CakeTheme.main-plugins');
if (isset($additionalJsFiles) && !empty($additionalJsFiles)) {
	foreach ($additionalJsFiles as $additionalJsFile) {
		echo $this->AssetCompress->script($additionalJsFile);
	}
}
	echo $this->fetch('script');
	echo $this->AssetCompress->script('CakeTheme.main-layout');
?>
<!--[if (gte IE 6)&(lte IE 8)]>
<?php
	echo $this->AssetCompress->css('CakeTheme.ie8supp');
	echo $this->AssetCompress->script('CakeTheme.ie8supp');
	echo $this->AssetCompress->script('CakeTheme.ie8supp-min');
?>
<![endif]-->
</head>
<body>
	<div class="mainappscripts-ds-overlay-higher"></div>
	<div id="container">
		<div id="header">
<?php
	echo $this->element('barNav', compact('isExternalAuth', 'emailContact', 'emailSubject'));
if ($showBreadcrumb) {
	echo $this->Html->div(
		'breadcrumb hidden-print', $this->Html->getCrumbs(
			'&nbsp;' .
			$this->Html->tag('span', '', ['class' => 'fas fa-angle-right']) . '&nbsp;',
			[
				'text' => $this->Html->tag('span', '', ['class' => 'fas fa-home']),
				'url' => '/',
				'escape' => false
			]
		),
		['data-toggle' => 'pjax']
	);
}
?>
		</div>
		<div id="content">
<?php
	echo $this->fetch('content');
?>
		</div>
		<div id="footer">
<?php
	if ($showFooter) {
		echo $this->element('CakeTheme.footerPageBase', compact('projectVersion', 'projectAuthor'));
	}
?>
		</div>
	</div>
<?php echo $this->element('CakeTheme.sql_dump'); ?>
</body>
</html>