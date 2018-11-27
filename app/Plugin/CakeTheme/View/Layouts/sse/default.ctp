<?php
/**
 * This file is the layout file of view the plugin. Used for render
 *  response of Server-Sent Event.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/byjg/jquery-sse
 * @package plugin.View.Layouts.sse
 */

	header("Content-Type: text/event-stream");
	echo $this->fetch('content');
