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
}

if (!isset($projectAuthor)) {
	$projectAuthor = null;
}

if (empty($projectVersion) && empty($projectAuthor)) {
	return;
}

$elementName = 'CakeTheme.footerPage';
if ($this->elementExists('footerPage')) {
	$elementName = 'footerPage';
}

echo $this->element($elementName, compact('projectVersion', 'projectAuthor'));