<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  response of Server-Sent Event.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/byjg/jquery-sse
 * @package plugin.View.Elements
 */

if (!isset($data)) {
	$data = null;
}

if (!isset($event)) {
	$event = null;
}

if (!isset($retry)) {
	$retry = null;
}

if (empty($data)) {
	return;
}

if (!empty($event)) {
	echo "event: " . $event . "\n";
}
if (!empty($retry)) {
	echo "retry: " . (int)$retry . "\n";
}
echo "data: " . $data . "\n\n";
