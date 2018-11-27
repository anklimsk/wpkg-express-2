<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin and configuration cache.
 *
 * CakeConfigPlugin: Initialize and obtain plugin configuration.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Cache configuration for store static part configuration of plugin
 *
 * Used for access to cached data from plugin.
 *  Default value `cake_config_plugin`
 */
if (!defined('CAKE_CONFIG_PLUGIN_CACHE_CFG')) {
	define('CAKE_CONFIG_PLUGIN_CACHE_CFG', 'cake_config_plugin');
}

/**
* Configuration the cache for store static part configuration
*
*/
Cache::config(CAKE_CONFIG_PLUGIN_CACHE_CFG, [
	'engine' => 'File',
	'prefix' => Inflector::slug(App::pluginPath('CakeConfigPlugin')),
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_CONFIG_PLUGIN_CACHE_CFG . DS,
]);
