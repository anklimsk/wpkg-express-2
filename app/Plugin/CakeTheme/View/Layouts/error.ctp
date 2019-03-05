<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of page as `error` style.
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

	$showSearchForm = false;
	$showMainMenu = false;

if (!isset($uiLcid2) || empty($uiLcid2)) {
	$uiLcid2 = 'en';
}

if (isset($pageTitlePrefix) && !empty($pageTitlePrefix)) {
	$this->prepend('title', $pageTitlePrefix);
}

if (isset($pageTitlePostfix) && !empty($pageTitlePostfix)) {
	$this->append('title', $pageTitlePostfix);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $uiLcid2; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->fetch('title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="ak">
<?php
	echo $this->Html->meta('icon');

	echo $this->AssetCompress->css('CakeTheme.libs');
	echo $this->AssetCompress->css('CakeTheme.error');
	echo $this->AssetCompress->script('CakeTheme.libs');
	echo $this->AssetCompress->script('CakeTheme.libs-min');

	echo $this->fetch('css');
	echo $this->AssetCompress->css('CakeTheme.error-plugins');
	echo $this->AssetCompress->script('CakeTheme.error-plugins');
	echo $this->fetch('script');
	echo $this->AssetCompress->script('CakeTheme.error-layout');
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
	echo $this->element('barNav', compact('isExternalAuth', 'emailContact', 'emailSubject', 'showSearchForm', 'showMainMenu'));
?>
		</div>
		<div id="content">
<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('CakeTheme.sql_dump'); ?>
</body>
</html>