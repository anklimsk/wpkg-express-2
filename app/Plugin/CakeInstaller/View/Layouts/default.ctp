<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of page as `installer` style.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php
	echo $this->Html->charset();
?>
	<title><?php echo $this->fetch('title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="ak">
<?php
	echo $this->Html->meta('icon');

	echo $this->Html->css([
		'CakeInstaller.bootstrap/bootstrap.min.css?v=3.3.7',
		'CakeInstaller.Installer.min.css?v=0.8.0'
		]);

	echo $this->Html->script([
		'CakeInstaller.jquery/jquery.min.js?v=1.12.3',
		'CakeInstaller.bootstrap/bootstrap.min.js?v=3.3.7',
		'CakeInstaller.font-awesome/fa-solid.min.js?v=5.1.0',
		'CakeInstaller.font-awesome/fa-regular.min.js?v=5.1.0',
		'CakeInstaller.font-awesome/fontawesome.min.js?v=5.1.0',
		'CakeInstaller.layout.installer.min.js?v=0.8.0'
		]);
?>  
</head>
<body>
	<div id="container">
		<div id="header">
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
<?php
	echo $this->Html->tag('span', __d('cake_installer', 'Installation of application'), ['class' => 'navbar-brand']);
?>
					</div>
				</div>
			</nav>
		</div>
		<div id="content">
<?php echo $this->fetch('content'); ?>
		</div>
	</div>
</body>
</html>