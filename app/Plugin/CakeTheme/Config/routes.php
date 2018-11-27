<?php
/**
 * Routes configuration
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

	Router::parseExtensions();
	Router::setExtensions(['json', 'sse', 'pop', 'mod', 'prt']);
