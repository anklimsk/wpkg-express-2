<!DOCTYPE html>
<html>
<head>
	<title><?php echo $this->fetch('title');?></title>
	<style type="text/css"><?php echo $this->Style->getStyle(); ?></style>
</head>
<body>
	<div id="container">
		<div id="content">
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
</body>
</html>