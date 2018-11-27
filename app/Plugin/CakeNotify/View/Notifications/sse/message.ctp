<?php
/**
 * This file is the view file of the plugin. Used for showing
 *  a result of Server-Sent Event.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/byjg/jquery-sse
 * @package plugin.View.Events
 */

	echo $this->element('CakeTheme.infoSSE', compact('retry', 'data', 'event'));
