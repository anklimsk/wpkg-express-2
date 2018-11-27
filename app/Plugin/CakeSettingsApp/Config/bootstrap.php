<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin and configuration cache.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * The path to the file marker.
 *
 * Used for indicate that the application is configured correctly
 *  Default value `APP/tmp/settings/configured.txt`
 */
if (!defined('CAKE_SETTINGS_APP_SETTINGS_MARKER_FILE')) {
	define('CAKE_SETTINGS_APP_SETTINGS_MARKER_FILE', TMP . 'settings' . DS . 'configured.txt');
}

/**
 * LDAP field `Distinguished Name`
 *
 * Used as primary key on LDAP Data source. Default value `dn`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/aa366101%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/aa366101%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME')) {
	define('CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME', 'dn');
}

/**
 * LDAP attribute `distinguishedName`
 *
 * The distinguished name of the employee.
 * Used as field `distinguished name` on LDAP Data source. Default value `distinguishedname`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675516%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675516%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME')) {
	define('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME', 'distinguishedname');
}

/**
 * LDAP attribute `objectGUID`
 *
 * The unique identifier for an object.
 * Used as field `objectguid` on LDAP Data source. Default value `objectguid`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679021%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679021%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID')) {
	define('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID', 'objectguid');
}

/**
 * LDAP attribute `name`
 *
 * The Relative Distinguished Name (RDN) of an object.
 * Used as field `name` on LDAP Data source. Default value `name`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms678697%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms678697%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME')) {
	define('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME', 'name');
}

/**
 * LDAP attribute `mail`
 *
 * The list of email addresses for a contact.
 * Used as field `mail` on LDAP Data source. Default value `mail`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms676855%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms676855%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL')) {
	define('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL', 'mail');
}

/**
 * LDAP attribute `memberOf`
 *
 * The distinguished name of the groups to which this object belongs.
 * Used as field `memberof` on LDAP Data source. Default value `memberof`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms677099%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms677099%28v=vs.85%29.aspx
 */
if (!defined('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF')) {
	define('CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF', 'memberof');
}

/**
 * Delimiter for E-mail list
 *
 * Used to separate E-mail distribution list. Default value `,`
 */
if (!defined('CAKE_SETTINGS_APP_SMTP_EMAIL_DELIM')) {
	define('CAKE_SETTINGS_APP_SMTP_EMAIL_DELIM', ',');
}

/**
 * Time limit for connection to SMTP server
 *
 * Used for set time limit for connection to SMTP server for
 *  validation or send mail. Default value `30`
 */
if (!defined('CAKE_SETTINGS_APP_SMTP_TIME_LIMIT')) {
	define('CAKE_SETTINGS_APP_SMTP_TIME_LIMIT', 30);
}

/**
 * Port for connection to SMTP server
 *
 * Used for set port for connection to SMTP server if needed.
 *  Default value `25`
 */
if (!defined('CAKE_SETTINGS_APP_SMTP_DEFAULT_PORT')) {
	define('CAKE_SETTINGS_APP_SMTP_DEFAULT_PORT', 25);
}

/**
 * Cache configuration for store list AD security groups
 *
 * Used for access to cached data of list AD security groups.
 *  Default value `cake_settings_app_ad_groups`
 */
if (!defined('CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS')) {
	define('CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS', 'cake_settings_app_ad_groups');
}

/**
 * Cache configuration for store list email of members AD security groups
 *
 * Used for access to cached data list email of members AD security groups.
 *  Default value `cake_settings_app_ad_group_memb_mail`
 */
if (!defined('CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL')) {
	define('CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL', 'cake_settings_app_ad_group_memb_email');
}

/**
 * Time limit for list of top level containers
 *
 * Used for set limit for list of top level containers.
 *  Default value `20`
 */
if (!defined('CAKE_SETTINGS_APP_TOP_LEVEL_UNITS_LIST_LIMIT')) {
	define('CAKE_SETTINGS_APP_TOP_LEVEL_UNITS_LIST_LIMIT', 20);
}

$prefix = Inflector::slug(App::pluginPath('CakeSettingsApp'));
/**
* Configuration the cache for store list AD security groups
*
*/
Cache::config(CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS . DS,
]);

/**
* Configuration the cache for store list email of members AD security groups
*
*/
Cache::config(CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL . DS,
]);
