<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of email as `default` style.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */

$this->startIfEmpty('footer');
echo $this->element('CakeNotify.mailFooter');
$this->end();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $this->fetch('title'); ?></title>
	<style type="text/css"><?php echo $this->Style->getStyle(); ?></style>
</head>
<body>
	<div id="container">
		<div id="content">
			<?php echo $this->fetch('content'); ?>
		</div>  
		<div id="footer">
			<?php echo $this->fetch('footer'); ?>
		</div>
	</div>
</body>
</html>