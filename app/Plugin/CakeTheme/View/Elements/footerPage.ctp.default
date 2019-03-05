<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  footer of page.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

if (!isset($projectVersion)) {
	$projectVersion = null;
	if (defined('PROJECT_VERSION')) {
		$projectVersion = PROJECT_VERSION;
	}
}

if (!isset($projectAuthor)) {
	$projectAuthor = null;
	if (defined('PROJECT_AUTHOR')) {
		$projectAuthor = PROJECT_AUTHOR;
	}
}

if (empty($projectVersion) && empty($projectAuthor)) {
	return;
}
?>
	<div class="footer navbar-default navbar-fixed-bottom">
		<div class="container-fluid">
<?php
	if (!empty($projectVersion)):
?>
			<div class="pull-left">
				<small>
					<em>
<?php
	$projectVersionTag = $this->Html->tag('samp', $projectVersion);
	echo __d('view_extension', 'Version: %s', $projectVersionTag);
?>
					</em>
				</small>
			</div>
<?php
	endif;
	if (!empty($projectAuthor)):
?>
			<div class="pull-right">
				<small>
<?php
	echo $projectAuthor;
?>
				</small>
			</div>
<?php
	endif;
?>
		</div>
	</div>
