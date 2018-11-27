<?php
/**
 * Routes configuration
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

	Router::connect(
		'/users/login',
		['controller' => 'users', 'action' => 'login', 'plugin' => 'cake_ldap']
	);
	Router::connect(
		'/users/logout',
		['controller' => 'users', 'action' => 'logout', 'plugin' => 'cake_ldap']
	);
	Router::connect(
		'/users',
		['controller' => 'employees', 'action' => 'index', 'plugin' => 'cake_ldap']
	);
	Router::connect(
		'/users/:action/*',
		['controller' => 'employees', 'plugin' => 'cake_ldap']
	);

	Router::parseExtensions();
	Router::setExtensions('json');
