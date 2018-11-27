<?php
/**
 * Routes configuration
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

	Router::connect(
		'/installer',
		['controller' => 'installer', 'action' => 'index', 'plugin' => 'cake_installer']
	);
