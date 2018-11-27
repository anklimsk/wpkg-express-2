<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Time of delay for request Server-Sent Events in miliseconds.
 *
 * Used for delay next request Server-Sent Events. Default value `30000`
 */
if (!defined('CAKE_NOTIFY_SSE_RETRY')) {
	define('CAKE_NOTIFY_SSE_RETRY', 30000);
}

/**
 * Time of expire notification in hours.
 *
 * Used to clearing expired notifications. Default value `6`
 */
if (!defined('CAKE_NOTIFY_EXPIRES_HOUR')) {
	define('CAKE_NOTIFY_EXPIRES_HOUR', 6);
}

/**
 * The task of the shell `cron` used for clearing expired notifications
 *
 * Used for set name of command. Default value `clear`
 */
if (!defined('CAKE_NOTIFY_CRON_TASK_CLEAR_NOTIFICATIONS')) {
	define('CAKE_NOTIFY_CRON_TASK_CLEAR_NOTIFICATIONS', 'clear');
}
