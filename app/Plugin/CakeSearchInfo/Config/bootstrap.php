<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin and configuration cache.
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Minimal leingt of query employee search
 *
 * Used for set minimal leingt of query employee search. Default value `2`
 */
if (!defined('CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH')) {
	define('CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH', 2);
}

/**
 * Autocomplete limit
 *
 * Used for set autocomplete limit, if needed. Default value `20`
 */
if (!defined('CAKE_SEARCH_INFO_AUTOCOMPLETE_LIMIT')) {
	define('CAKE_SEARCH_INFO_AUTOCOMPLETE_LIMIT', 20);
}

/**
 * Autocomplete result truncate limit
 *
 * Used for set limit for truncate result of autocomplete, if needed.
 *  Default value `40`
 */
if (!defined('CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT')) {
	define('CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT', 40);
}

/**
 * Value of target field for input `any part` on search form
 *
 * Is used to define value of target field for store value
 *  of flag `any part` on form input .
 *  Default value `False`
 */
if (!defined('CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART')) {
	define('CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART', 'anyPart');
}

/**
 * Cache configuration for store query configuration
 *
 * Used for access to cached data of query configuration.
 *  Default value `cake_search_info_query_cfg`
 */
if (!defined('CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG')) {
	define('CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG', 'cake_search_info_query_cfg');
}

/**
 * Cache configuration for store result of query.
 *
 * Used for access to cached data of result query.
 *  Default value `cake_search_info_query_cfg`
 */
if (!defined('CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT')) {
	define('CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT', 'cake_search_info_query_res');
}

$prefix = Inflector::slug(App::pluginPath('CakeSearchInfo'));
/**
* Configuration the cache for store query configuration
*
*/
Cache::config(CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG . DS,
]);

/**
* Configuration the cache for store query result of autocomplete
*
*/
Cache::config(CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 day',
	'probability' => 100,
	'path' => CACHE . CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT . DS,
]);
