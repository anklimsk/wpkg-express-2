<?php
/**
 * Routes configuration
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

	Router::connect(
		'/settings',
		['controller' => 'settings', 'action' => 'index', 'plugin' => 'cake_settings_app']
	);
	Router::connect(
		'/queues',
		['controller' => 'queues', 'action' => 'index', 'plugin' => 'cake_settings_app']
	);
	Router::connect(
		'/settings/:action/*',
		['controller' => 'settings', 'plugin' => 'cake_settings_app']
	);
	Router::connect(
		'/queues/:action/*',
		['controller' => 'queues', 'plugin' => 'cake_settings_app']
	);

	Router::parseExtensions();
	Router::setExtensions('json');
