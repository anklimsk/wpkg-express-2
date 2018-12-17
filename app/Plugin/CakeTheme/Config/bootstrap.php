<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants and configuration cache for plugin.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Specific CSS folder name
 *
 * Used for store specific CSS files for View. Default value `specific`
 */
if (!defined('CAKE_THEME_SPECIFIC_CSS_DIR')) {
	define('CAKE_THEME_SPECIFIC_CSS_DIR', 'specific');
}

/**
 * Specific JS folder name
 *
 * Used for store specific JavaScript files for View. Default value `specific`
 */
if (!defined('CAKE_THEME_SPECIFIC_JS_DIR')) {
	define('CAKE_THEME_SPECIFIC_JS_DIR', 'specific');
}

/**
 * Cache key for store specific JS and CSS files
 *
 * Used for access to cached data of specific JS and CSS files.
 *  Default value `cake_theme_specific_files`
 */
if (!defined('CAKE_THEME_CACHE_KEY_SPECIFIC_FILES')) {
	define('CAKE_THEME_CACHE_KEY_SPECIFIC_FILES', 'cake_theme_specific_files');
}

/**
 * Autocomplete limit
 *
 * Used for set autocomplete limit, if needed. Default value `10`
 */
if (!defined('CAKE_THEME_AUTOCOMPLETE_LIMIT')) {
	define('CAKE_THEME_AUTOCOMPLETE_LIMIT', 10);
}

/**
 * Breadcrumbs text limit
 *
 * Used for set breadcrumbs text limit, if needed. Default value `20`
 */
if (!defined('CAKE_THEME_BREADCRUMBS_TEXT_LIMIT')) {
	define('CAKE_THEME_BREADCRUMBS_TEXT_LIMIT', 30);
}

/**
 * Filter row limit
 *
 * Used for set limit for amount row of filter, if needed. Default value `8`
 */
if (!defined('CAKE_THEME_FILTER_ROW_LIMIT')) {
	define('CAKE_THEME_FILTER_ROW_LIMIT', 8);
}

/**
 * Print data limit
 *
 * Used for set print data limit on pagination, if needed. Default value `1000`
 */
if (!defined('CAKE_THEME_PRINT_DATA_LIMIT')) {
	define('CAKE_THEME_PRINT_DATA_LIMIT', 1000);
}

/**
 * Cache configuration for store for store information of helpers
 *
 * Used for access to cached data of for store information of helpers.
 *  Default value `cake_theme_helper`
 */
if (!defined('CAKE_THEME_CACHE_KEY_HELPERS')) {
	define('CAKE_THEME_CACHE_KEY_HELPERS', 'cake_theme_helper');
}

/**
 * Cache configuration for store for store label of condition button for table filter
 *
 * Used for access to cached data of for store label of condition button.
 *  Default value `cake_theme_btn_cond_label`
 */
if (!defined('CAKE_THEME_CACHE_KEY_BTN_COND_LABEL')) {
	define('CAKE_THEME_CACHE_KEY_BTN_COND_LABEL', 'cake_theme_btn_cond_label');
}

/**
 * Cache key for store language code
 *
 * Used for access to cached data of language code.
 *  Default value `cake_theme_lang_code`
 */
if (!defined('CAKE_THEME_CACHE_KEY_LANG_CODE')) {
	define('CAKE_THEME_CACHE_KEY_LANG_CODE', 'cake_theme_lang_code');
}

/**
 * Path to export directory
 *
 * Used for store exported files
 */
if (!defined('CAKE_THEME_EXPORT_DIR')) {
	define('CAKE_THEME_EXPORT_DIR', TMP . 'export' . DS);
}

/**
 * Path to upload directory
 *
 * Used for store upload files
 */
if (!defined('CAKE_THEME_UPLOAD_DIR')) {
	define('CAKE_THEME_UPLOAD_DIR', TMP . 'import' . DS);
}

/**
 * Path to preview directory
 *
 * Used for store preview image files
 */
if (!defined('CAKE_THEME_PREVIEW_DIR')) {
	define('CAKE_THEME_PREVIEW_DIR', 'img' . DS . 'preview' . DS);
}

$prefix = Inflector::slug(App::pluginPath('CakeTheme'));
/**
* Configuration the cache for store path to specific files
*
*/
Cache::config(CAKE_THEME_CACHE_KEY_SPECIFIC_FILES, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_THEME_CACHE_KEY_SPECIFIC_FILES . DS,
]);

/**
* Configuration the cache for store navigation menu
*
*/
Cache::config(CAKE_THEME_CACHE_KEY_HELPERS, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_THEME_CACHE_KEY_HELPERS . DS,
]);

/**
* Configuration the cache for store label of condition button
*
*/
Cache::config(CAKE_THEME_CACHE_KEY_BTN_COND_LABEL, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_THEME_CACHE_KEY_BTN_COND_LABEL . DS,
]);

/**
* Configuration the cache for store language code
*
*/
Cache::config(CAKE_THEME_CACHE_KEY_LANG_CODE, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_THEME_CACHE_KEY_LANG_CODE . DS,
]);

/**
 * Configuration the Exception renderer
 *
 */
Configure::write('Exception.renderer', 'CakeTheme.ExceptionRendererCakeTheme');
