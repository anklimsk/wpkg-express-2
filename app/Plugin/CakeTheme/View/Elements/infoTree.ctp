<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  response of Tree view.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/jonmiles/bootstrap-treeview
 * @package plugin.View.Elements
 */

if (!isset($url)) {
	$url = '';
}

if (!isset($viewurl)) {
	$viewurl = null;
}

if (!isset($enablelinks)) {
	$enablelinks = false;
}

if (!isset($showtags)) {
	$showtags = false;
}

if (!isset($levels)) {
	$levels = 1;
}

if (empty($url)) {
	return;
}

	echo $this->Html->div('treeview-sm', '', [
		'data-toggle' => 'treeview',
		'data-treeview-url' => $url,
		'data-treeview-viewurl' => $viewurl,
		'data-treeview-enablelinks' => $enablelinks,
		'data-treeview-showtags' => $showtags,
		'data-treeview-levels' => $levels,
	]);
