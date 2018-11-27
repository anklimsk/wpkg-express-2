<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin and configuration cache.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Global Query limit
 *
 * Used for set global find limit, if needed. Default value `5000`
 */
if (!defined('CAKE_LDAP_GLOBAL_QUERY_LIMIT')) {
	define('CAKE_LDAP_GLOBAL_QUERY_LIMIT', 5000);
}

/**
 * Limit of deep for tree of employee
 *
 * Used for set level of deep tree employee for saveing records on synchronize. Default value `50`
 */
if (!defined('CAKE_LDAP_TREE_EMPLOYEE_DEEP_LIMIT')) {
	define('CAKE_LDAP_TREE_EMPLOYEE_DEEP_LIMIT', 50);
}

/**
 * Limit the number of records to synchronize
 *
 * Used for set the number of records to synchronize. Default value `5000`
 */
if (!defined('CAKE_LDAP_SYNC_AD_LIMIT')) {
	define('CAKE_LDAP_SYNC_AD_LIMIT', 5000);
}

/**
 * Time limit for synchronize records
 *
 * Used for set time limit of synchronize records. Default value `60`
 */
if (!defined('SYNC_EMPLOYEE_TIME_LIMIT')) {
	define('SYNC_EMPLOYEE_TIME_LIMIT', 180);
}

/**
 * Time limit for synchronize tree of employee
 *
 * Used for set time limit of synchronize tree records. Default value `60`
 */
if (!defined('SYNC_TREE_EMPLOYEE_TIME_LIMIT')) {
	define('SYNC_TREE_EMPLOYEE_TIME_LIMIT', 180);
}

/**
 * Time limit for reorder tree of employee
 *
 * Used for set time limit of reorder tree of employee. Default value `180`
 */
if (!defined('REORDER_TREE_EMPLOYEE_TIME_LIMIT')) {
	define('REORDER_TREE_EMPLOYEE_TIME_LIMIT', 180);
}

/**
 * Time limit for recover tree of employee
 *
 * Used for set time limit of recover tree of employee. Default value `180`
 */
if (!defined('RECOVER_TREE_EMPLOYEE_TIME_LIMIT')) {
	define('RECOVER_TREE_EMPLOYEE_TIME_LIMIT', 180);
}

/**
 * Time limit for check state tree of employee
 *
 * Used for set time limit of check state tree of employee. Default value `60`
 */
if (!defined('CHECK_TREE_EMPLOYEE_TIME_LIMIT')) {
	define('CHECK_TREE_EMPLOYEE_TIME_LIMIT', 60);
}

/**
 * The task of the shell `cron` used for synchronize employees with
 *  Active Directory
 *
 * Used for set name of command. Default value `sync`
 */
if (!defined('CAKE_LDAP_SHELL_CRON_TASK_SYNC')) {
	define('CAKE_LDAP_SHELL_CRON_TASK_SYNC', 'sync');
}

/**
 * Maximum length of text in table information of employee
 *
 * Is used to set the limit text in table information of employee. If the
 *  text length is exceeded the text will be truncated and at the end,
 *  of the line will be added to the characters '...'.
 *  Default value `25`
 */
if (!defined('CAKE_LDAP_EMPLOYEE_TABLE_TEXT_MAX_LENGTH')) {
	define('CAKE_LDAP_EMPLOYEE_TABLE_TEXT_MAX_LENGTH', 25);
}

/**
 * Maximum length of text in item of element information of employee
 *
 * Is used to set the limit text in item of element information of employee. If the
 *  text length is exceeded the text will be truncated and at the end,
 *  of the line will be added to the characters '...'.
 *  Default value `50`
 */
if (!defined('CAKE_LDAP_EMPLOYEE_ITEM_TEXT_MAX_LENGTH')) {
	define('CAKE_LDAP_EMPLOYEE_ITEM_TEXT_MAX_LENGTH', 50);
}

/**
 * Width of small employee photo
 *
 * Used for set width of uploaded photo, and width of rendered photo.
 *  Default value `64` px
 */
if (!defined('CAKE_LDAP_PHOTO_SIZE_SMALL')) {
	define('CAKE_LDAP_PHOTO_SIZE_SMALL', 64);
}

/**
 * Width of large employee photo
 *
 * Used for set width of uploaded photo, and width of rendered photo.
 *  Default value `200` px
 */
if (!defined('CAKE_LDAP_PHOTO_SIZE_LARGE')) {
	define('CAKE_LDAP_PHOTO_SIZE_LARGE', 200);
}

/**
 * Cache configuration for store config of fields
 *
 * Used for access to cached data config of fields.
 *  Default value `cake_ldap_config`
 */
if (!defined('CAKE_LDAP_CACHE_KEY_CONFIG')) {
	define('CAKE_LDAP_CACHE_KEY_CONFIG', 'cake_ldap_config');
}

/**
 * Cache configuration for store config of database tables and fields
 *
 * Used for access to cached data config of database tables
 *  and fields. Default value `cake_ldap_db`
 */
if (!defined('CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB')) {
	define('CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB', 'cake_ldap_db');
}

/**
 * Cache configuration for store data tree of employees
 *
 * Used for access to cached data tree of employees.
 *  Default value `cake_ldap_tree`
 */
if (!defined('CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES')) {
	define('CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES', 'cake_ldap_tree');
}

/**
 * LDAP field `Distinguished Name`
 *
 * Used as primary key on LDAP Data source. Default value `dn`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/aa366101%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/aa366101%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_DISTINGUISHED_NAME')) {
	define('CAKE_LDAP_LDAP_DISTINGUISHED_NAME', 'dn');
}

/**
 * LDAP attribute `distinguishedName`
 *
 * The distinguished name of the employee.
 * Used as field `distinguished name` on LDAP Data source. Default value `distinguishedname`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675516%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675516%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME', 'distinguishedname');
}

/**
 * LDAP attribute `userPrincipalName`
 *
 * This attribute contains the UPN that is an Internet-style login name for a user based on
 *  the Internet standard RFC 822.
 * Used as field `userprincipalname` on LDAP Data source. Default value `userprincipalname`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms680857%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms680857%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME', 'userprincipalname');
}

/**
 * LDAP attribute `memberOf`
 *
 * The distinguished name of the groups to which this object belongs.
 * Used as field `memberof` on LDAP Data source. Default value `memberof`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms677099%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms677099%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF', 'memberof');
}

/**
 * LDAP attribute `name`
 *
 * The Relative Distinguished Name (RDN) of an object.
 * Used as field `name` on LDAP Data source. Default value `name`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms678697%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms678697%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_NAME', 'name');
}

/**
 * LDAP attribute `company`
 *
 * The user's company name.
 * Used as field `company` on LDAP Data source. Default value `company`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675457%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675457%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY', 'company');
}

/**
 * LDAP attribute `displayName`
 *
 * The display name for an object. This is usually the combination of the users first name,
 *  middle initial, and last name.
 * Used as field `displayname` on LDAP Data source. Default value `displayname`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675514%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675514%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME', 'displayname');
}

/**
 * LDAP attribute `initials`
 *
 * Contains the initials for parts of the user's full name.
 * Used as field `initials` on LDAP Data source. Default value `initials`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms676202%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms676202%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS', 'initials');
}

/**
 * LDAP attribute `sn`
 *
 * This attribute contains the family or last name for a user.
 * Used as field `sn` on LDAP Data source. Default value `sn`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679872%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679872%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME', 'sn');
}

/**
 * LDAP attribute `givenName`
 *
 * Contains the given name (first name) of the user.
 * Used as field `givenname` on LDAP Data source. Default value `givenname`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675719%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675719%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME', 'givenname');
}

/**
 * LDAP attribute `middleName`
 *
 * Additional names for a user. For example, middle name, patronymic, matronymic, or others.
 * Used as field `middlename` on LDAP Data source. Default value `middlename`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms677108%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms677108%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME', 'middlename');
}

/**
 * LDAP attribute `title`
 *
 * Contains the user's job title.
 * Used as field `title` on LDAP Data source. Default value `title`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms680037%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms680037%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_TITLE')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_TITLE', 'title');
}

/**
 * LDAP attribute `department`
 *
 * Contains the name for the department in which the user works.
 * Used as field `department` on LDAP Data source. Default value `department`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675490%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675490%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT', 'department');
}

/**
 * LDAP attribute `division`
 *
 * The user's division.
 * Used as field `division` on LDAP Data source. Default value `division`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675518%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675518%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION', 'division');
}

/**
 * LDAP attribute `telephoneNumber`
 *
 * The primary telephone number (internal telephone number).
 * Used as field `telephonenumber` on LDAP Data source. Default value `telephonenumber`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms680027%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms680027%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER', 'telephonenumber');
}

/**
 * LDAP attribute `otherTelephone`
 *
 * A list of alternate office phone numbers (Landline number)
 * Used as field `othertelephone` on LDAP Data source. Default value `othertelephone`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679094%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679094%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER', 'othertelephone');
}

/**
 * LDAP attribute `mobile`
 *
 * The primary mobile phone number.
 * Used as field `mobile` on LDAP Data source. Default value `mobile`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms677119%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms677119%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER', 'mobile');
}

/**
 * LDAP attribute `otherMobile`
 *
 * A list of alternate mobile phone numbers.
 * Used as field `othermobile` on LDAP Data source. Default value `othermobile`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679092%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679092%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER', 'othermobile');
}

/**
 * LDAP attribute `physicalDeliveryOfficeName`
 *
 * Contains the office location in the user's place of business.
 * Used as field `physicaldeliveryofficename` on LDAP Data source. Default value `physicaldeliveryofficename`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679117%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679117%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME', 'physicaldeliveryofficename');
}

/**
 * LDAP attribute `mail`
 *
 * The list of email addresses for a contact.
 * Used as field `mail` on LDAP Data source. Default value `mail`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms676855%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms676855%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_MAIL')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_MAIL', 'mail');
}

/**
 * LDAP attribute `manager`
 *
 * Contains the distinguished name of the user who is the user's manager.
 * Used as field `manager` on LDAP Data source. Default value `manager`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms676859%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms676859%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER', 'manager');
}

/**
 * LDAP attribute `thumbnailPhoto`
 *
 * An image of the user. A space-efficient format like JPEG or GIF is recommended.
 * Used as field `thumbnailphoto` on LDAP Data source. Default value `thumbnailphoto`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms680034%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms680034%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO', 'thumbnailphoto');
}

/**
 * LDAP attribute `wWWHomePage`
 *
 * The primary webpage (use as computer name).
 * Used as field `wwwhomepage` on LDAP Data source. Default value `wwwhomepage`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms680927%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms680927%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER', 'wwwhomepage');
}

/**
 * LDAP attribute `employeeID`
 *
 * The ID of an employee.
 * Used as field `employeeid` on LDAP Data source. Default value `employeeid`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms675662%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms675662%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID', 'employeeid');
}

/**
 * LDAP attribute `objectGUID`
 *
 * The unique identifier for an object.
 * Used as field `objectguid` on LDAP Data source. Default value `objectguid`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679021%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679021%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID', 'objectguid');
}

/**
 * LDAP attribute `pager`
 *
 * The primary pager number (use as date of birthday).
 * Used as field `pager` on LDAP Data source. Default value `pager`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms679102%28v=vs.85%29.aspx">MSDN</a>
 * @link https://msdn.microsoft.com/en-us/library/windows/desktop/ms679102%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY', 'pager');
}

/**
 * LDAP attribute `ipPhone`
 *
 * The TCP/IP address for the phone (use as SIP telephone number).
 * Used as field `ipphone` on LDAP Data source. Default value `ipphone`
 * <a href="https://msdn.microsoft.com/en-us/library/windows/desktop/ms676213%28v=vs.85%29.aspx">MSDN</a>
 * @https://msdn.microsoft.com/en-us/library/windows/desktop/ms676213%28v=vs.85%29.aspx
 */
if (!defined('CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE')) {
	define('CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE', 'ipphone');
}

/**
 * Default MIME type of photo employee
 *
 * Used for set MIME type for render employee photo from binary data.
 *  Default value `image/jpeg`
 */
if (!defined('CAKE_LDAP_PHOTO_DEFAULT_MIME_TYPE')) {
	define('CAKE_LDAP_PHOTO_DEFAULT_MIME_TYPE', 'image/jpeg');
}

$prefix = Inflector::slug(App::pluginPath('CakeLdap'));
/**
* Configuration the cache for store config of fields
*
*/
Cache::config(CAKE_LDAP_CACHE_KEY_CONFIG, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_LDAP_CACHE_KEY_CONFIG . DS,
]);

/**
* Configuration the cache for store config of database
*  tables and fields
*
*/
Cache::config(CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB . DS,
]);

/**
* Configuration the cache for store data tree of employees
*
*/
Cache::config(CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES, [
	'engine' => 'File',
	'prefix' => $prefix,
	'duration' => '+1 week',
	'probability' => 100,
	'path' => CACHE . CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES . DS,
]);
