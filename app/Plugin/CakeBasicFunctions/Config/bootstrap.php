<?php
/**
 * This file is global util functions file of the application.
 *
 * CakeBasicFunctions: Basic global utilities for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Vendor
 */

App::import(
	'Lib/Utility',
	'CakeBasicFunctions.BasicFunctions',
	['file' => 'BasicFunctions.php']
);

/**
 * Cache key for store language code
 *
 * Used for access to cached data of language code.
 *  Default value `cake_basic_func_lang_code`
 */
if (!defined('CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE')) {
	define('CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE', 'cake_basic_func_lang_code');
}

$prefix = Inflector::slug(App::pluginPath('CakeBasicFunctions'));
/**
* Configuration the cache for store language code
*
*/
Cache::config(CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_BASIC_FUNC_CACHE_KEY_LANG_CODE . DS,
]);
