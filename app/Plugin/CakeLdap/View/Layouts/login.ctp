<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of page as `login` style.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */

if (!isset($uiLcid2) || empty($uiLcid2)) {
	$uiLcid2 = 'en';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $uiLcid2; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php
	echo $this->Html->charset();
if (isset($pageTitlePrefix)) {
	$this->prepend('title', $pageTitlePrefix);
}
if (isset($pageTitlePostfix)) {
	$this->append('title', $pageTitlePostfix);
}
?>
	<title><?php echo $this->fetch('title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="ak">
<?php
	echo $this->Html->meta('icon');

	echo $this->AssetCompress->css('CakeTheme.libs');
	echo $this->AssetCompress->css('CakeTheme.noty');
	echo $this->AssetCompress->css('CakeTheme.login');

	echo $this->AssetCompress->script('CakeTheme.libs');
	echo $this->AssetCompress->script('CakeTheme.noty');
	echo $this->AssetCompress->script('CakeTheme.login');

	echo $this->fetch('css');
	echo $this->AssetCompress->css('CakeTheme.login-plugins');
	echo $this->fetch('script');
	echo $this->AssetCompress->script('CakeTheme.login-plugins');
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
	<div id="container">
		<div id="content">
<?php echo $this->fetch('content'); ?>
		</div>
	</div>
</body>
</html>