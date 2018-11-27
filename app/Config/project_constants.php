<?php
/**
 * This file is constants definition file of the application.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Config
 */

/**
 * Name of project
 *
 * Used for set project name in main menu and in E-mail template.
 *  Translate domain - `project`.
 */
if (!defined('PROJECT_NAME')) {
	define('PROJECT_NAME', 'WPKG Express II');
}

/**
 * Title of page
 *
 * Used for set title of page. Translate domain - `project`.
 *  Default value `Project title`
 */
if (!defined('PROJECT_PAGE_TITLE')) {
	define('PROJECT_PAGE_TITLE', 'WPKG Express II');
}

/**
 * Name of project without space char
 *
 * Used for set configure key. Default value `Project`
 */
if (!defined('PROJECT_CONFIG_NAME')) {
	define('PROJECT_CONFIG_NAME', 'WPKG');
}

/**
 * Name of image file organization logo
 *
 * Used in the main menu of application. Size `32` X `32` px.
 *  Default value `project-logo.png`
 */
if (!defined('PROJECT_LOGO_IMAGE_SMALL')) {
	define('PROJECT_LOGO_IMAGE_SMALL', 'project-logo.png');
}

/**
 * Global Query limit
 *
 * Used for set global find limit, if needed. Default value `1000`
 */
if (!defined('GLOBAL_QUERY_LIMIT')) {
	define('GLOBAL_QUERY_LIMIT', 5000);
}

/**
 * Bit mask for user role `User`
 *
 * Default role for authorized user.
 * Used for set user role. Default value `1`
 */
if (!defined('USER_ROLE_USER')) {
	define('USER_ROLE_USER', 1);
}

/**
 * Bit mask for user role `Admin`
 *
 * Role for Administrators
 * Used for set user role. Default value `8`
 */
if (!defined('USER_ROLE_ADMIN')) {
	define('USER_ROLE_ADMIN', 2);
}

/**
 * Version of script WPKG
 *
 * Used to display on preview of the WPKG script configuration.
 *  Default value `1.3.1`
 */
if (!defined('WPKG_INFO_SCRIPT_VER')) {
	define('WPKG_INFO_SCRIPT_VER', '1.3.1');
}

/**
 * Allowed extensions of files for upload (PCRE)
 *
 * Used for checking imported files on server. Default value `/\.(xml)$/i`
 */
if (!defined('UPLOAD_FILE_TYPES_SERVER')) {
	define('UPLOAD_FILE_TYPES_SERVER', '/\.(xml)$/i');
}

/**
 * Allowed extensions of files for upload (PCRE)
 *
 * Used for checking imported files on client. Default value `(\.|\/)(xml)$`
 */
if (!defined('UPLOAD_FILE_TYPES_CLIENT')) {
	define('UPLOAD_FILE_TYPES_CLIENT', '(\.|\/)(xml)$');
}

/**
 * Limit size of uploaded files
 *
 * Used for set limit size for uploaded files, bytes. Default value `1Mb`
 */
if (!defined('UPLOAD_FILE_SIZE_LIMIT')) {
	define('UPLOAD_FILE_SIZE_LIMIT', 1024 * 1024);
}

/**
 * Full path to directory for upload
 *
 * Used for store imported files. Default value `/tmp/import`
 */
if (!defined('UPLOAD_DIR')) {
	define('UPLOAD_DIR', TMP . 'import' . DS);
}

/**
 * Full path to directory for client database
 *
 * Used for store client database files. Default value `/tmp/clientBase/`
 */
if (!defined('REPORT_DIR')) {
	define('REPORT_DIR', TMP . 'clientBase' . DS);
}
/**
 * Full path to directory for logs
 *
 * Used for store log files. Default value `/tmp/clientLog/`
 */
if (!defined('LOG_DIR')) {
	define('LOG_DIR', TMP . 'clientLog' . DS);
}

/**
 * Full path to directory for graphs
 *
 * Used for store graphs files. Default value `/tmp/graph/`
 */
if (!defined('GRAPH_DIR')) {
	define('GRAPH_DIR', TMP . 'graph' . DS);
}

/**
 * Full path to the package XML schema file
 *
 * Used for validation XML document. Default value `/webroot/xsd/packages.xsd`
 */
if (!defined('XSD_PATH_PACKAGE')) {
	define('XSD_PATH_PACKAGE', APP . 'webroot' . DS . 'xsd' . DS . 'packages.xsd');
}

/**
 * Full path to the profile XML schema file
 *
 * Used for validation XML document. Default value `/webroot/xsd/profiles.xsd`
 */
if (!defined('XSD_PATH_PROFILE')) {
	define('XSD_PATH_PROFILE', APP . 'webroot' . DS . 'xsd' . DS . 'profiles.xsd');
}

/**
 * Full path to the host XML schema file
 *
 * Used for validation XML document. Default value `/webroot/xsd/hosts.xsd`
 */
if (!defined('XSD_PATH_HOST')) {
	define('XSD_PATH_HOST', APP . 'webroot' . DS . 'xsd' . DS . 'hosts.xsd');
}

/**
 * Full path to the config XML schema file
 *
 * Used for validation XML document. Default value `/webroot/xsd/config.xsd`
 */
if (!defined('XSD_PATH_CONFIG')) {
	define('XSD_PATH_CONFIG', APP . 'webroot' . DS . 'xsd' . DS . 'config.xsd');
}

/**
 * Full path to the client database XML schema file
 *
 * Used for validation XML document. Default value `/webroot/xsd/settings.xsd`
 */
if (!defined('XSD_PATH_DATABASE')) {
	define('XSD_PATH_DATABASE', APP . 'webroot' . DS . 'xsd' . DS . 'settings.xsd');
}

/**
 * XML tag name for disabled items
 *
 * Used for render disabled items as commented out. Default value `disabled`
 */
if (!defined('XML_SPECIFIC_TAG_DISABLED')) {
	define('XML_SPECIFIC_TAG_DISABLED', 'disabled');
}

/**
 * XML tag name for store notes of items
 *
 * Used for render notes of items as commented out. Default value `notes`
 */
if (!defined('XML_SPECIFIC_TAG_NOTES')) {
	define('XML_SPECIFIC_TAG_NOTES', 'notes');
}

/**
 * XML tag name for store template flag of items
 *
 * Used for render template flag of items as commented out. Default value `template`
 */
if (!defined('XML_SPECIFIC_TAG_TEMPLATE')) {
	define('XML_SPECIFIC_TAG_TEMPLATE', 'template');
}

/**
 * Prefix for notes of items
 *
 * Used for detect notes of items on import. Default value ` Notes: `
 */
if (!defined('XML_EXPORT_NOTES_COMMENTS_PREFIX')) {
	define('XML_EXPORT_NOTES_COMMENTS_PREFIX', ' Notes: ');
}

/**
 * Postfix for notes of items
 *
 * Used for detect notes of items on import. Default value ` `
 */
if (!defined('XML_EXPORT_NOTES_COMMENTS_POSTFIX')) {
	define('XML_EXPORT_NOTES_COMMENTS_POSTFIX', ' ');
}

/**
 * Prefix for template flag of items
 *
 * Used for detect template flag of items on import. Default value ` Template: `
 */
if (!defined('XML_EXPORT_TEMPLATE_PREFIX')) {
	define('XML_EXPORT_TEMPLATE_PREFIX', ' Template: ');
}

/**
 * Postfix for template flag of items
 *
 * Used for detect template flag of items on import. Default value ` `
 */
if (!defined('XML_EXPORT_TEMPLATE_POSTFIX')) {
	define('XML_EXPORT_TEMPLATE_POSTFIX', ' ');
}

/**
 * State `False` of package reboot flag
 *
 * Used for reboot system after installation package. Default value `1`
 */
if (!defined('PACKAGE_REBOOT_FALSE')) {
	define('PACKAGE_REBOOT_FALSE', 1);
}

/**
 * State `True` of package reboot flag
 *
 * Used for reboot system after installation package. Default value `2`
 */
if (!defined('PACKAGE_REBOOT_TRUE')) {
	define('PACKAGE_REBOOT_TRUE', 2);
}

/**
 * State `Postponed` of package reboot flag
 *
 * Used for reboot system after installation package. Default value `3`
 */
if (!defined('PACKAGE_REBOOT_POSTPONED')) {
	define('PACKAGE_REBOOT_POSTPONED', 3);
}

/**
 * State `Default` of package installation execute flag
 *
 * Used for set mode of execute installation package. Default value `1`
 */
if (!defined('PACKAGE_EXECUTE_DEFAULT')) {
	define('PACKAGE_EXECUTE_DEFAULT', 1);
}

/**
 * State `Once` of package installation execute flag
 *
 * Used for set mode of execute installation package. Default value `2`
 */
if (!defined('PACKAGE_EXECUTE_ONCE')) {
	define('PACKAGE_EXECUTE_ONCE', 2);
}

/**
 * State `Always` of package installation execute flag
 *
 * Used for set mode of execute installation package. Default value `3`
 */
if (!defined('PACKAGE_EXECUTE_ALWAYS')) {
	define('PACKAGE_EXECUTE_ALWAYS', 3);
}

/**
 * State `Changed` of package installation execute flag
 *
 * Used for set mode of execute installation package. Default value `4`
 */
if (!defined('PACKAGE_EXECUTE_CHANGED')) {
	define('PACKAGE_EXECUTE_CHANGED', 4);
}

/**
 * State `False` of user notification flag
 *
 * Used to notify the user about the package installation process. Default value `1`
 */
if (!defined('PACKAGE_NOTIFY_FALSE')) {
	define('PACKAGE_NOTIFY_FALSE', 1);
}

/**
 * State `False` of user notification flag
 *
 * Used to notify the user about the package installation process. Default value `2`
 */
if (!defined('PACKAGE_NOTIFY_TRUE')) {
	define('PACKAGE_NOTIFY_TRUE', 2);
}

/**
 * Preset package priority `Normal`
 *
 * Used as preset package priority from list. Default value `0`
 */
if (!defined('PACKAGE_PRIORITY_NORMAL')) {
	define('PACKAGE_PRIORITY_NORMAL', 0);
}

/**
 * Preset package priority `Priority soft`
 *
 * Used as preset package priority from list. Default value `100`
 */
if (!defined('PACKAGE_PRIORITY_PRIORITY_SOFT')) {
	define('PACKAGE_PRIORITY_PRIORITY_SOFT', 100);
}

/**
 * Preset package priority `Drivers`
 *
 * Used as preset package priority from list. Default value `600`
 */
if (!defined('PACKAGE_PRIORITY_DRIVERS')) {
	define('PACKAGE_PRIORITY_DRIVERS', 600);
}

/**
 * Preset package priority `Libraries`
 *
 * Used as preset package priority from list. Default value `800`
 */
if (!defined('PACKAGE_PRIORITY_LIBRARIES')) {
	define('PACKAGE_PRIORITY_LIBRARIES', 800);
}

/**
 * Preset package priority `Utilities`
 *
 * Used as preset package priority from list. Default value `1000`
 */
if (!defined('PACKAGE_PRIORITY_SYSTEM_UTILITIES')) {
	define('PACKAGE_PRIORITY_SYSTEM_UTILITIES', 1000);
}

/**
 * Preset package priority `WPKG`
 *
 * Used as preset package priority from list. Default value `10000`
 */
if (!defined('PACKAGE_PRIORITY_WPKG')) {
	define('PACKAGE_PRIORITY_WPKG', 10000);
}

/**
 * Package action `Install`
 *
 * Used as package action name. Default value `1`
 */
if (!defined('ACTION_TYPE_INSTALL')) {
	define('ACTION_TYPE_INSTALL', 1);
}

/**
 * Package action `Upgrade`
 *
 * Used as package action name. Default value `2`
 */
if (!defined('ACTION_TYPE_UPGRADE')) {
	define('ACTION_TYPE_UPGRADE', 2);
}

/**
 * Package action `Downgrade`
 *
 * Used as package action name. Default value `3`
 */
if (!defined('ACTION_TYPE_DOWNGRADE')) {
	define('ACTION_TYPE_DOWNGRADE', 3);
}

/**
 * Package action `Remove`
 *
 * Used as package action name. Default value `4`
 */
if (!defined('ACTION_TYPE_REMOVE')) {
	define('ACTION_TYPE_REMOVE', 4);
}

/**
 * Package action `Download`
 *
 * Used as package action name. Default value `5`
 */
if (!defined('ACTION_TYPE_DOWNLOAD')) {
	define('ACTION_TYPE_DOWNLOAD', 5);
}

/**
 * Package action command type `Command`
 *
 * Used for determine the type of package action command. Default value `1`
 */
if (!defined('ACTION_COMMAND_TYPE_COMMAND')) {
	define('ACTION_COMMAND_TYPE_COMMAND', 1);
}

/**
 * Package action command type `Include`
 *
 * Used for determine the type of package action command. Default value `2`
 */
if (!defined('ACTION_COMMAND_TYPE_INCLUDE')) {
	define('ACTION_COMMAND_TYPE_INCLUDE', 2);
}

/**
 * Default timeout of package action command
 *
 * Used for set default timeout of package action command. Default value `300`
 */
if (!defined('ACTION_COMMAND_DEFAULT_TIMEOUT')) {
	define('ACTION_COMMAND_DEFAULT_TIMEOUT', 300);
}

/**
 * Maximum timeout of package action command
 *
 * Used for set maximum timeout of package action command. Default value `3600`
 */
if (!defined('ACTION_COMMAND_MAX_TIMEOUT')) {
	define('ACTION_COMMAND_MAX_TIMEOUT', 3600);
}

/**
 * Package action exit code reboot action `False`
 *
 * Used as preset package action exit code reboot action from list.
 *  Default value `1`
 */
if (!defined('EXITCODE_REBOOT_FALSE')) {
	define('EXITCODE_REBOOT_FALSE', 1);
}

/**
 * Package action exit code reboot action `True`
 *
 * Used as preset package action exit code reboot action from list.
 *  Default value `1`
 */
if (!defined('EXITCODE_REBOOT_TRUE')) {
	define('EXITCODE_REBOOT_TRUE', 2);
}

/**
 * Package action exit code reboot action `Delayed`
 *
 * Used as preset package action exit code reboot action from list.
 *  Default value `1`
 */
if (!defined('EXITCODE_REBOOT_DELAYED')) {
	define('EXITCODE_REBOOT_DELAYED', 3);
}

/**
 * Package action exit code reboot action `Postponed`
 *
 * Used as preset package action exit code reboot action from list.
 *  Default value `1`
 */
if (!defined('EXITCODE_REBOOT_POSTPONED')) {
	define('EXITCODE_REBOOT_POSTPONED', 4);
}

/**
 * Package action exit code reboot action `Null`
 *
 * Used as preset package action exit code reboot action from list.
 *  Default value `1`
 */
if (!defined('EXITCODE_REBOOT_NULL')) {
	define('EXITCODE_REBOOT_NULL', 5);
}

/**
 * Type of object check `Package`
 *
 * Used for determine the type of object check. Default value `1`
 */
if (!defined('CHECK_PARENT_TYPE_PACKAGE')) {
	define('CHECK_PARENT_TYPE_PACKAGE', 1);
}


/**
 * Type of object check `Package action`
 *
 * Used for determine the type of object check. Default value `2`
 */
if (!defined('CHECK_PARENT_TYPE_ACTION')) {
	define('CHECK_PARENT_TYPE_ACTION', 2);
}

/**
 * Type of object check `Profile package`
 *
 * Used for determine the type of object check. Default value `3`
 */
if (!defined('CHECK_PARENT_TYPE_PROFILE')) {
	define('CHECK_PARENT_TYPE_PROFILE', 3);
}

/**
 * Type of object check `Variable`
 *
 * Used for determine the type of object check. Default value `4`
 */
if (!defined('CHECK_PARENT_TYPE_VARIABLE')) {
	define('CHECK_PARENT_TYPE_VARIABLE', 4);
}

/**
 * Type of check `Logical`
 *
 * Used for determine the type of check. Default value `1`
 */
if (!defined('CHECK_TYPE_LOGICAL')) {
	define('CHECK_TYPE_LOGICAL', 1);
}

/**
 * Type of check `Registry`
 *
 * Used for determine the type of check. Default value `2`
 */
if (!defined('CHECK_TYPE_REGISTRY')) {
	define('CHECK_TYPE_REGISTRY', 2);
}

/**
 * Type of check `File`
 *
 * Used for determine the type of check. Default value `3`
 */
if (!defined('CHECK_TYPE_FILE')) {
	define('CHECK_TYPE_FILE', 3);
}

/**
 * Type of check `Uninstall`
 *
 * Used for determine the type of check. Default value `4`
 */
if (!defined('CHECK_TYPE_UNINSTALL')) {
	define('CHECK_TYPE_UNINSTALL', 4);
}

/**
 * Type of check `Execute`
 *
 * Used for determine the type of check. Default value `5`
 */
if (!defined('CHECK_TYPE_EXECUTE')) {
	define('CHECK_TYPE_EXECUTE', 5);
}

/**
 * Type of check `Host`
 *
 * Used for determine the type of check. Default value `6`
 */
if (!defined('CHECK_TYPE_HOST')) {
	define('CHECK_TYPE_HOST', 6);
}

/**
 * Type of check condition `Logical not`
 *
 * Used for determine the type of check condition. Default value `1`
 */
if (!defined('CHECK_CONDITION_LOGICAL_NOT')) {
	define('CHECK_CONDITION_LOGICAL_NOT', 1);
}

/**
 * Type of check condition `Logical and`
 *
 * Used for determine the type of check condition. Default value `2`
 */
if (!defined('CHECK_CONDITION_LOGICAL_AND')) {
	define('CHECK_CONDITION_LOGICAL_AND', 2);
}

/**
 * Type of check condition `Logical or`
 *
 * Used for determine the type of check condition. Default value `3`
 */
if (!defined('CHECK_CONDITION_LOGICAL_OR')) {
	define('CHECK_CONDITION_LOGICAL_OR', 3);
}

/**
 * Type of check condition `Logical at least`
 *
 * Used for determine the type of check condition. Default value `4`
 */
if (!defined('CHECK_CONDITION_LOGICAL_AT_LEAST')) {
	define('CHECK_CONDITION_LOGICAL_AT_LEAST', 4);
}

/**
 * Type of check condition `Logical at most`
 *
 * Used for determine the type of check condition. Default value `5`
 */
if (!defined('CHECK_CONDITION_LOGICAL_AT_MOST')) {
	define('CHECK_CONDITION_LOGICAL_AT_MOST', 5);
}

/**
 * Type of check condition `Registry exists`
 *
 * Used for determine the type of check condition. Default value `6`
 */
if (!defined('CHECK_CONDITION_REGISTRY_EXISTS')) {
	define('CHECK_CONDITION_REGISTRY_EXISTS', 6);
}

/**
 * Type of check condition `Registry equals`
 *
 * Used for determine the type of check condition. Default value `7`
 */
if (!defined('CHECK_CONDITION_REGISTRY_EQUALS')) {
	define('CHECK_CONDITION_REGISTRY_EQUALS', 7);
}

/**
 * Type of check condition `File exists`
 *
 * Used for determine the type of check condition. Default value `8`
 */
if (!defined('CHECK_CONDITION_FILE_EXISTS')) {
	define('CHECK_CONDITION_FILE_EXISTS', 8);
}

/**
 * Type of check condition `File size equals`
 *
 * Used for determine the type of check condition. Default value `9`
 */
if (!defined('CHECK_CONDITION_FILE_SIZE_EQUALS')) {
	define('CHECK_CONDITION_FILE_SIZE_EQUALS', 9);
}

/**
 * Type of check condition `File version smaller than`
 *
 * Used for determine the type of check condition. Default value `10`
 */
if (!defined('CHECK_CONDITION_FILE_VERSION_SMALLER_THAN')) {
	define('CHECK_CONDITION_FILE_VERSION_SMALLER_THAN', 10);
}

/**
 * Type of check condition `File version less than or equal to`
 *
 * Used for determine the type of check condition. Default value `11`
 */
if (!defined('CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO', 11);
}

/**
 * Type of check condition `File version equal to`
 *
 * Used for determine the type of check condition. Default value `12`
 */
if (!defined('CHECK_CONDITION_FILE_VERSION_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_VERSION_EQUAL_TO', 12);
}

/**
 * Type of check condition `File version greater than`
 *
 * Used for determine the type of check condition. Default value `13`
 */
if (!defined('CHECK_CONDITION_FILE_VERSION_GREATER_THAN')) {
	define('CHECK_CONDITION_FILE_VERSION_GREATER_THAN', 13);
}

/**
 * Type of check condition `File version greater than or equal to`
 *
 * Used for determine the type of check condition. Default value `14`
 */
if (!defined('CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO', 14);
}

/**
 * Type of check condition `File date modify equal to`
 *
 * Used for determine the type of check condition. Default value `15`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO', 15);
}

/**
 * Type of check condition `File date modify newer than`
 *
 * Used for determine the type of check condition. Default value `16`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN', 16);
}

/**
 * Type of check condition `File date modify older than`
 *
 * Used for determine the type of check condition. Default value `17`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN', 17);
}

/**
 * Type of check condition `File date create equal to`
 *
 * Used for determine the type of check condition. Default value `18`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO', 18);
}

/**
 * Type of check condition `File date create newer than`
 *
 * Used for determine the type of check condition. Default value `19`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN', 19);
}

/**
 * Type of check condition `File date create older than`
 *
 * Used for determine the type of check condition. Default value `20`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN', 20);
}

/**
 * Type of check condition `File date access equal to`
 *
 * Used for determine the type of check condition. Default value `21`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO')) {
	define('CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO', 21);
}

/**
 * Type of check condition `File date access newer than`
 *
 * Used for determine the type of check condition. Default value `22`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN', 22);
}

/**
 * Type of check condition `File date access older than`
 *
 * Used for determine the type of check condition. Default value `23`
 */
if (!defined('CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN')) {
	define('CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN', 23);
}

/**
 * Type of check condition `Execute exit code smaller than`
 *
 * Used for determine the type of check condition. Default value `24`
 */
if (!defined('CHECK_CONDITION_EXECUTE_EXIT_CODE_SMALLER_THAN')) {
	define('CHECK_CONDITION_EXECUTE_EXIT_CODE_SMALLER_THAN', 24);
}

/**
 * Type of check condition `Execute exit code less than or equal to`
 *
 * Used for determine the type of check condition. Default value `25`
 */
if (!defined('CHECK_CONDITION_EXECUTE_EXIT_CODE_LESS_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_EXECUTE_EXIT_CODE_LESS_THAN_OR_EQUAL_TO', 25);
}

/**
 * Type of check condition `Execute exit code equal to`
 *
 * Used for determine the type of check condition. Default value `26`
 */
if (!defined('CHECK_CONDITION_EXECUTE_EXIT_CODE_EQUAL_TO')) {
	define('CHECK_CONDITION_EXECUTE_EXIT_CODE_EQUAL_TO', 26);
}

/**
 * Type of check condition `Execute exit code greater than`
 *
 * Used for determine the type of check condition. Default value `27`
 */
if (!defined('CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN')) {
	define('CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN', 27);
}

/**
 * Type of check condition `Execute exit code greater than or equal to`
 *
 * Used for determine the type of check condition. Default value `28`
 */
if (!defined('CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN_OR_EQUAL_TO', 28);
}

/**
 * Type of check condition `Uninstall exists`
 *
 * Used for determine the type of check condition. Default value `29`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_EXISTS')) {
	define('CHECK_CONDITION_UNINSTALL_EXISTS', 29);
}

/**
 * Type of check condition `Uninstall version smaller than`
 *
 * Used for determine the type of check condition. Default value `30`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN')) {
	define('CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN', 30);
}

/**
 * Type of check condition `Uninstall version less than or equal to`
 *
 * Used for determine the type of check condition. Default value `31`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO', 31);
}

/**
 * Type of check condition `Uninstall version equal to`
 *
 * Used for determine the type of check condition. Default value `32`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO')) {
	define('CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO', 32);
}

/**
 * Type of check condition `Uninstall version greater than`
 *
 * Used for determine the type of check condition. Default value `33`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN')) {
	define('CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN', 33);
}

/**
 * Type of check condition `Uninstall version greater than or equal to`
 *
 * Used for determine the type of check condition. Default value `34`
 */
if (!defined('CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO')) {
	define('CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO', 34);
}

/**
 * Type of check condition `Host name`
 *
 * Used for determine the type of check condition. Default value `35`
 */
if (!defined('CHECK_CONDITION_HOST_NAME')) {
	define('CHECK_CONDITION_HOST_NAME', 35);
}

/**
 * Type of check condition `Host OS`
 *
 * Used for determine the type of check condition. Default value `36`
 */
if (!defined('CHECK_CONDITION_HOST_OS')) {
	define('CHECK_CONDITION_HOST_OS', 36);
}

/**
 * Type of check condition `Host architecture`
 *
 * Used for determine the type of check condition. Default value `37`
 */
if (!defined('CHECK_CONDITION_HOST_ARCHITECTURE')) {
	define('CHECK_CONDITION_HOST_ARCHITECTURE', 37);
}

/**
 * Type of check condition `Host IP addresses`
 *
 * Used for determine the type of check condition. Default value `38`
 */
if (!defined('CHECK_CONDITION_HOST_IP_ADDRESSES')) {
	define('CHECK_CONDITION_HOST_IP_ADDRESSES', 38);
}

/**
 * Type of check condition `Host domain name`
 *
 * Used for determine the type of check condition. Default value `39`
 */
if (!defined('CHECK_CONDITION_HOST_DOMAIN_NAME')) {
	define('CHECK_CONDITION_HOST_DOMAIN_NAME', 39);
}

/**
 * Type of check condition `Host groups`
 *
 * Used for determine the type of check condition. Default value `40`
 */
if (!defined('CHECK_CONDITION_HOST_GROUPS')) {
	define('CHECK_CONDITION_HOST_GROUPS', 40);
}

/**
 * Type of check condition `Host language code ID`
 *
 * Used for determine the type of check condition. Default value `41`
 */
if (!defined('CHECK_CONDITION_HOST_LCID')) {
	define('CHECK_CONDITION_HOST_LCID', 41);
}

/**
 * Type of check condition `Host OS language code ID`
 *
 * Used for determine the type of check condition. Default value `42`
 */
if (!defined('CHECK_CONDITION_HOST_LCID_OS')) {
	define('CHECK_CONDITION_HOST_LCID_OS', 42);
}

/**
 * Type of check condition `Host environment`
 *
 * Used for determine the type of check condition. Default value `43`
 */
if (!defined('CHECK_CONDITION_HOST_ENVIRONMENT')) {
	define('CHECK_CONDITION_HOST_ENVIRONMENT', 43);
}

/**
 * Type of object variable `Package`
 *
 * Used for determine the type of object variable. Default value `1`
 */
if (!defined('VARIABLE_TYPE_PACKAGE')) {
	define('VARIABLE_TYPE_PACKAGE', 1);
}

/**
 * Type of object variable `Profile`
 *
 * Used for determine the type of object variable. Default value `2`
 */
if (!defined('VARIABLE_TYPE_PROFILE')) {
	define('VARIABLE_TYPE_PROFILE', 2);
}

/**
 * Type of object variable `Host`
 *
 * Used for determine the type of object variable. Default value `3`
 */
if (!defined('VARIABLE_TYPE_HOST')) {
	define('VARIABLE_TYPE_HOST', 3);
}

/**
 * Type of object variable `WPKG config (global)`
 *
 * Used for determine the type of object variable. Default value `4`
 */
if (!defined('VARIABLE_TYPE_CONFIG')) {
	define('VARIABLE_TYPE_CONFIG', 4);
}

/**
 * Auto variable `Revision name`
 *
 * Used to create an automatically updated package variable containing a revision.
 *  Default value `Revision`
 */
if (!defined('VARIABLE_AUTO_REVISION_NAME')) {
	define('VARIABLE_AUTO_REVISION_NAME', 'Revision');
}

/**
 * Auto variable `RevI`
 *
 * Used to create automatically updated package variable containing a revision item.
 *  Default value `RevI`
 */
if (!defined('VARIABLE_AUTO_REVISION_ITEM')) {
	define('VARIABLE_AUTO_REVISION_ITEM', 'RevI');
}

/**
 * Type of object attribute `Host`
 *
 * Used for determine the type of object attribute. Default value `1`
 */
if (!defined('ATTRIBUTE_TYPE_HOST')) {
	define('ATTRIBUTE_TYPE_HOST', 1);
}

/**
 * Type of object attribute `Profile`
 *
 * Used for determine the type of object attribute. Default value `2`
 */
if (!defined('ATTRIBUTE_TYPE_PROFILE')) {
	define('ATTRIBUTE_TYPE_PROFILE', 2);
}

/**
 * Type of object attribute `Package`
 *
 * Used for determine the type of object attribute. Default value `3`
 */
if (!defined('ATTRIBUTE_TYPE_PACKAGE')) {
	define('ATTRIBUTE_TYPE_PACKAGE', 3);
}

/**
 * Type of object attribute `Package action`
 *
 * Used for determine the type of object attribute. Default value `4`
 */
if (!defined('ATTRIBUTE_TYPE_ACTION')) {
	define('ATTRIBUTE_TYPE_ACTION', 4);
}

/**
 * Type of object attribute `Variable`
 *
 * Used for determine the type of object attribute. Default value `5`
 */
if (!defined('ATTRIBUTE_TYPE_VARIABLE')) {
	define('ATTRIBUTE_TYPE_VARIABLE', 5);
}

/**
 * Type of object attribute `WPKG config variable`
 *
 * Used for determine the type of object attribute. Default value `6`
 */
if (!defined('ATTRIBUTE_TYPE_CONFIG')) {
	define('ATTRIBUTE_TYPE_CONFIG', 6);
}

/**
 * Node of object attribute `Host`
 *
 * Used for determine the node of object attribute. Default value `1`
 */
if (!defined('ATTRIBUTE_NODE_HOST')) {
	define('ATTRIBUTE_NODE_HOST', 1);
}

/**
 * Node of object attribute `Package`
 *
 * Used for determine the node of object attribute. Default value `2`
 */
if (!defined('ATTRIBUTE_NODE_PACKAGE')) {
	define('ATTRIBUTE_NODE_PACKAGE', 2);
}

/**
 * Node of object attribute `Variable`
 *
 * Used for determine the node of object attribute. Default value `3`
 */
if (!defined('ATTRIBUTE_NODE_VARIABLE')) {
	define('ATTRIBUTE_NODE_VARIABLE', 3);
}

/**
 * Node of object attribute `Package depends`
 *
 * Used for determine the node of object attribute. Default value `4`
 */
if (!defined('ATTRIBUTE_NODE_DEPENDS')) {
	define('ATTRIBUTE_NODE_DEPENDS', 4);
}

/**
 * Node of object attribute `Package include`
 *
 * Used for determine the node of object attribute. Default value `5`
 */
if (!defined('ATTRIBUTE_NODE_INCLUDE')) {
	define('ATTRIBUTE_NODE_INCLUDE', 5);
}

/**
 * Node of object attribute `Package chain`
 *
 * Used for determine the node of object attribute. Default value `6`
 */
if (!defined('ATTRIBUTE_NODE_CHAIN')) {
	define('ATTRIBUTE_NODE_CHAIN', 6);
}

/**
 * Node of object attribute `Package action`
 *
 * Used for determine the node of object attribute. Default value `7`
 */
if (!defined('ATTRIBUTE_NODE_ACTION')) {
	define('ATTRIBUTE_NODE_ACTION', 7);
}

/**
 * Node of object attribute `Check`
 *
 * Used for determine the node of object attribute. Default value `8`
 */
if (!defined('ATTRIBUTE_NODE_CHECK')) {
	define('ATTRIBUTE_NODE_CHECK', 8);
}

/**
 * Node of object attribute `Host report`
 *
 * Used for determine the node of object attribute. Default value `9`
 */
if (!defined('ATTRIBUTE_NODE_REPORT')) {
	define('ATTRIBUTE_NODE_REPORT', 9);
}

/**
 * Type of attribute architecture `x86`
 *
 * Used for determine the type of attribute architecture. Default value `x86`
 */
if (!defined('ATTRIBUTE_ARCHITECTURE_X86')) {
	define('ATTRIBUTE_ARCHITECTURE_X86', 'x86');
}

/**
 * Type of attribute architecture `x86`
 *
 * Used for determine the type of attribute architecture. Default value `x64`
 */
if (!defined('ATTRIBUTE_ARCHITECTURE_X64')) {
	define('ATTRIBUTE_ARCHITECTURE_X64', 'x64');
}

/**
 * Type of attribute architecture `x86`
 *
 * Used for determine the type of attribute architecture. Default value `ia64`
 */
if (!defined('ATTRIBUTE_ARCHITECTURE_IA64')) {
	define('ATTRIBUTE_ARCHITECTURE_IA64', 'ia64');
}

/**
 * Type of attribute OS `Windows XP`
 *
 * Used for determine the type of attribute OS. Default value `5\.1\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_XP')) {
	define('ATTRIBUTE_OS_WINDOWS_XP', '5\.1\.\d{4}');
}

/**
 * Type of attribute OS `Windows Vista`
 *
 * Used for determine the type of attribute OS. Default value `6\.0\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_VISTA')) {
	define('ATTRIBUTE_OS_WINDOWS_VISTA', '6\.0\.\d{4}');
}

/**
 * Type of attribute OS `Windows 7`
 *
 * Used for determine the type of attribute OS. Default value `7.+6\.1\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_7')) {
	define('ATTRIBUTE_OS_WINDOWS_7', '7.+6\.1\.\d{4}');
}

/**
 * Type of attribute OS `Windows 8`
 *
 * Used for determine the type of attribute OS. Default value `6\.2\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_8')) {
	define('ATTRIBUTE_OS_WINDOWS_8', '6\.2\.\d{4}');
}

/**
 * Type of attribute OS `Windows 8.1`
 *
 * Used for determine the type of attribute OS. Default value `6\.3\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_8.1')) {
	define('ATTRIBUTE_OS_WINDOWS_8.1', '6\.3\.\d{4}');
}

/**
 * Type of attribute OS `Windows 10`
 *
 * Used for determine the type of attribute OS. Default value `10\.0\.\d{5}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_10')) {
	define('ATTRIBUTE_OS_WINDOWS_10', '10\.0\.\d{5}');
}

/**
 * Type of attribute OS `Windows Server 2003`
 *
 * Used for determine the type of attribute OS. Default value `server.+5\.2\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_SERVER_2003')) {
	define('ATTRIBUTE_OS_WINDOWS_SERVER_2003', 'server.+5\.2\.\d{4}');
}

/**
 * Type of attribute OS `Windows Server 2008`
 *
 * Used for determine the type of attribute OS. Default value `server.+6\.0\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_SERVER_2008')) {
	define('ATTRIBUTE_OS_WINDOWS_SERVER_2008', 'server.+6\.0\.\d{4}');
}

/**
 * Type of attribute OS `Windows Server 2008 R2`
 *
 * Used for determine the type of attribute OS. Default value `server.+6\.1\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_SERVER_2008_R2')) {
	define('ATTRIBUTE_OS_WINDOWS_SERVER_2008_R2', 'server.+6\.1\.\d{4}');
}

/**
 * Type of attribute OS `Windows Server 2012`
 *
 * Used for determine the type of attribute OS. Default value `server.+6\.2\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_SERVER_2012')) {
	define('ATTRIBUTE_OS_WINDOWS_SERVER_2012', 'server.+6\.2\.\d{4}');
}

/**
 * Type of attribute OS `Windows Server 2012 R2`
 *
 * Used for determine the type of attribute OS. Default value `server.+6\.3\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_SERVER_2012_R2')) {
	define('ATTRIBUTE_OS_WINDOWS_SERVER_2012_R2', 'server.+6\.3\.\d{4}');
}

/**
 * Type of attribute OS `Windows less Vista`
 *
 * Used for determine the type of attribute OS. Default value `[5]{1}\.\d{1}\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_LESS_VISTA')) {
	define('ATTRIBUTE_OS_WINDOWS_LESS_VISTA', '[5]{1}\.\d{1}\.\d{4}');
}

/**
 * Type of attribute OS `Windows Vista and greater`
 *
 * Used for determine the type of attribute OS. Default value `(?:[6-9]{1}|[1-9]\d{1})\.\d{1}\.\d{4}`
 */
if (!defined('ATTRIBUTE_OS_WINDOWS_VISTA_AND_GREATER')) {
	define('ATTRIBUTE_OS_WINDOWS_VISTA_AND_GREATER', '(?:[6-9]{1}|[1-9]\d{1})\.\d{1}\.\d{4}');
}

/**
 * Type of attribute language code ID `Russian`
 *
 * Used for determine the type of attribute OS. Default value `419`
 */
if (!defined('ATTRIBUTE_LCID_RUSSIAN')) {
	define('ATTRIBUTE_LCID_RUSSIAN', '419');
}


/**
 * Type of attribute language code ID `English`
 *
 * Used for determine the type of attribute OS. Default value `409`
 */
if (!defined('ATTRIBUTE_LCID_ENGLISH')) {
	define('ATTRIBUTE_LCID_ENGLISH', '409');
}

/**
 * Report package state `OK`
 *
 * Used as preset report package state from list.
 *  Default value `1`
 */
if (!defined('REPORT_STATE_OK')) {
	define('REPORT_STATE_OK', 1);
}

/**
 * Report package state `OK manual`
 *
 * Used as preset report package state from list.
 *  Default value `2`
 */
if (!defined('REPORT_STATE_OK_MANUAL')) {
	define('REPORT_STATE_OK_MANUAL', 2);
}

/**
 * Report package state `Upgrade`
 *
 * Used as preset report package state from list.
 *  Default value `3`
 */
if (!defined('REPORT_STATE_UPGRADE')) {
	define('REPORT_STATE_UPGRADE', 3);
}

/**
 * Report package state `Downgrade`
 *
 * Used as preset report package state from list.
 *  Default value `4`
 */
if (!defined('REPORT_STATE_DOWNGRADE')) {
	define('REPORT_STATE_DOWNGRADE', 4);
}

/**
 * Log record state `Success`
 *
 * Used as preset log record state from list.
 *  Default value `1`
 */
if (!defined('LOG_TYPE_SUCCESS')) {
	define('LOG_TYPE_SUCCESS', 1);
}

/**
 * Log record state `Info`
 *
 * Used as preset log record state from list.
 *  Default value `2`
 */
if (!defined('LOG_TYPE_INFORMATION')) {
	define('LOG_TYPE_INFORMATION', 2);
}

/**
 * Log record state `Debug`
 *
 * Used as preset log record state from list.
 *  Default value `3`
 */
if (!defined('LOG_TYPE_DEBUG')) {
	define('LOG_TYPE_DEBUG', 3);
}

/**
 * Log record state `Warning`
 *
 * Used as preset log record state from list.
 *  Default value `4`
 */
if (!defined('LOG_TYPE_WARNING')) {
	define('LOG_TYPE_WARNING', 4);
}

/**
 * Log record state `Error`
 *
 * Used as preset log record state from list.
 *  Default value `5`
 */
if (!defined('LOG_TYPE_ERROR')) {
	define('LOG_TYPE_ERROR', 5);
}

/**
 * PCRE pattern for parsing the log file name
 *
 * Used to extract information from the log file name.
 *  Default value `/^wpkg-([^\/\*\?\"\<\>\|@_]+)_?.*$/i`
 */
if (!defined('LOG_PARSE_PCRE_FILE_NAME')) {
	define('LOG_PARSE_PCRE_FILE_NAME', '/^wpkg-([^\/\*\?\"\<\>\|@_]+)_?.*$/i');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract information from the log file content.
 *  Default value `/^(\d{4}-\d{2}-\d{2} \d{2}\:\d{2}:\d{2}),\s(\w+)\s+:\s(.+)$/im`
 */
if (!defined('LOG_PARSE_PCRE_CONTENT')) {
	define('LOG_PARSE_PCRE_CONTENT', '/^(\d{4}-\d{2}-\d{2} \d{2}\:\d{2}:\d{2}),\s(\w+)\s+:\s(.+)$/im');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract binded information from the log file content:
 *  package name from log record with type `Info`.
 */
if (!defined('LOG_PKG_PCRE_INFO_PACKAGE_NAME')) {
	define('LOG_PKG_PCRE_INFO_PACKAGE_NAME', '(?:Package\s\'.+\'\s\((.+)\)\:.+|Processing\s\(.+\)\sof\s(.+)\s\w+\.|[\w]+\s\'(.+)\'\s\(.+\)\.\.\.|' .
		'Installing\sreferences\s\(.+\)\sof\s\'(.+)\'\s\(.+\).+)');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract binded information from the log file content:
 *  package ID text from log record with type `Debug`.
 */
if (!defined('LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT')) {
	define('LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT', 'Adding\spackage\swith\sID\s\'([^)]+)\'.+');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract binded information from the log file content:
 *  package name and ID text from log record with type `Debug`.
 */
if (!defined('LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT_NAME')) {
	define('LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT_NAME', '(?:Package\s\'.+\'\s\((.+)\)\:.+|Adding\ssettings\snode\:\s\'.+\'\s\((.+)\).+|' .
		'Installation\sof\sreferences\s\(.+\)\sfor\s\'.+\'\s\((.+)\).+|Found\spackage\snode\s\'.+\'\s\((.+)\).+|' .
		'Adding\sreferenced\spackage\s\'.+\'\s\((.+)\)\sfor\spackage\s\'.+\'\s\((.+)\)|Referenced\spackage\s\'.+\'\s\((.+)\)\sfor\spackage\s\'.+\'\s\((.+)\).+|' .
		'Going\sto\sinstall\spackage\s\'.+\'\s\((.+)\).+|Command\sin\sinstallation\sof\s(.+)\sreturned|' .
		'Checking\sexistence\sof\spackage\:\s(.+)|Reading\svariables\sfrom\spackage\s\'(.+)\'\.)');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract binded information from the log file content:
 *  profile ID text from log record with type `Debug`.
 */
if (!defined('LOG_PKG_PCRE_DEBUG_PROFILE_ID_TEXT')) {
	define('LOG_PKG_PCRE_DEBUG_PROFILE_ID_TEXT', 'Profiles\sapplying\sto\sthe\scurrent\shost\:\|([^\|]+)\|Applying\sprofile:\s(.+)');
}

/**
 * PCRE pattern for parsing the log file content
 *
 * Used to extract binded information from the log file content:
 *  package name from log record with type `Error`.
 */
if (!defined('LOG_PKG_PCRE_ERROR_PACKAGE_NAME')) {
	define('LOG_PKG_PCRE_ERROR_PACKAGE_NAME', 'Could\snot\sprocess\s\(.+\)\spackage\s\'.+\'\s\((.+)\).+');
}

/**
 * Type of object garbage `Package`
 *
 * Used for determine the type of object garbage. Default value `1`
 */
if (!defined('GARBAGE_TYPE_PACKAGE')) {
	define('GARBAGE_TYPE_PACKAGE', 1);
}

/**
 * Type of object garbage `Profile`
 *
 * Used for determine the type of object garbage. Default value `2`
 */
if (!defined('GARBAGE_TYPE_PROFILE')) {
	define('GARBAGE_TYPE_PROFILE', 2);
}

/**
 * Type of object garbage `Host`
 *
 * Used for determine the type of object garbage. Default value `3`
 */
if (!defined('GARBAGE_TYPE_HOST')) {
	define('GARBAGE_TYPE_HOST', 3);
}

/**
 * Type of object graph `Package`
 *
 * Used for determine the type of object graph. Default value `1`
 */
if (!defined('GRAPH_TYPE_PACKAGE')) {
	define('GRAPH_TYPE_PACKAGE', 1);
}

/**
 * Type of object graph `Profile`
 *
 * Used for determine the type of object graph. Default value `2`
 */
if (!defined('GRAPH_TYPE_PROFILE')) {
	define('GRAPH_TYPE_PROFILE', 2);
}

/**
 * Type of object graph `Host`
 *
 * Used for determine the type of object graph. Default value `3`
 */
if (!defined('GRAPH_TYPE_HOST')) {
	define('GRAPH_TYPE_HOST', 3);
}

/**
 * Processed graph data limit
 *
 * Used to set the limit for processing graph data. Default value `1000`
 */
if (!defined('GRAPH_DATA_LIMIT')) {
	define('GRAPH_DATA_LIMIT', 1000);
}

/**
 * Limiting the recursion level for processing graph data
 *
 * Used for set limit the recursion level for processing graph data.
 *  Default value `50`
 */
if (!defined('GRAPH_DEEP_LIMIT')) {
	define('GRAPH_DEEP_LIMIT', 50);
}

/**
 * Output format for generate graph
 *
 * Used for set output format of generated graph. Default value `svg`
 * @link https://graphviz.gitlab.io/_pages/doc/info/output.html
 */
if (!defined('GRAPH_OUTPUT_FORMAT')) {
	define('GRAPH_OUTPUT_FORMAT', 'svg');
}

/**
 * Time limit for generate graph
 *
 * Used for set time limit of generate graph. Default value `60`
 */
if (!defined('GRAPH_GENERATE_TIME_LIMIT')) {
	define('GRAPH_GENERATE_TIME_LIMIT', 60);
}

/**
 * Maximum time of store graph files
 *
 * Used for set maximum time of store graph files. Default value `600`
 */
if (!defined('GRAPH_STORE_FILE_TIME_LIMIT')) {
	define('GRAPH_STORE_FILE_TIME_LIMIT', 600);
}

/**
 * The path to the wpkg.js script in the WPI command
 *
 * Used in installation command WPI. Default value `%WPIPATH%\Tools\wpkg\wpkg.js`
 */
if (!defined('WPI_WPKG_SCRIPT_PATH')) {
	define('WPI_WPKG_SCRIPT_PATH', '%WPIPATH%\Tools\wpkg\wpkg.js');
}

/**
 * WPI installation command
 *
 * Used for installation package from WPI.
 */
if (!defined('WPI_INSTALL_CMD_WPKG')) {
	define('WPI_INSTALL_CMD_WPKG', 'cscript.exe //NoLogo %s /install:%s /quiet:false /nonotify:true /noreboot:true /sendStatus:false /noremove:true /norunningstate:true /forceInstall:true /settings:%%TEMP%%\wpkg-wpi.xml /log_file_path:%%TEMP%%');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_1')) {
	define('SWITCH_MSI_1', '/i "%SOFTWARE%\package.msi"');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_2')) {
	define('SWITCH_MSI_2', '/x "%SOFTWARE%\package.msi"');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_3')) {
	define('SWITCH_MSI_3', '/x {GUID}');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_4')) {
	define('SWITCH_MSI_4', '/passive /norestart');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_5')) {
	define('SWITCH_MSI_5', '/passive /norestart /log "%TMP%\%PKG_NAME%.log" TRANSFORMS="%SOFTWARE%\APP\silent.mst"');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_6')) {
	define('SWITCH_MSI_6', '/passive /norestart /log "%TMP%\%PKG_NAME%.log" TARGETDIR="%PKG_DESTINATION%"');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_7')) {
	define('SWITCH_MSI_7', '/qb /norestart');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_8')) {
	define('SWITCH_MSI_8', '/qb /norestart /log "%TMP%\%PKG_NAME%.log" TRANSFORMS="%SOFTWARE%\APP\silent.mst"');
}

/**
 * Command line switch for MSI installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_MSI_9')) {
	define('SWITCH_MSI_9', '/qb /norestart /log "%TMP%\%PKG_NAME%.log" TARGETDIR="%PKG_DESTINATION%"');
}

/**
 * Command line switch for Nullsoft Scriptable Install System installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_NSIS_1')) {
	define('SWITCH_NSIS_1', '/S /D=%PKG_DESTINATION%');
}

/**
 * Command line switch for Nullsoft Scriptable Install System installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_NSIS_2')) {
	define('SWITCH_NSIS_2', '/S _?=%PKG_DESTINATION%');
}

/**
 * Command line switch for Inno Setup installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INNO_1')) {
	define('SWITCH_INNO_1', '/SP- /VERYSILENT /SUPPRESSMSGBOXES /NORESTART');
}

/**
 * Command line switch for Inno Setup installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INNO_2')) {
	define('SWITCH_INNO_2', '/SP- /VERYSILENT /SUPPRESSMSGBOXES /NORESTART /DIR="%PKG_DESTINATION%"');
}

/**
 * Command line switch for Inno Setup installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INNO_3')) {
	define('SWITCH_INNO_3', '/SP- /VERYSILENT /SUPPRESSMSGBOXES /NORESTART /DIR="%PKG_DESTINATION%" /LOG="%TMP%\%PKG_NAME%.log"');
}

/**
 * Command line switch for Inno Setup installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INNO_4')) {
	define('SWITCH_INNO_4', '/VERYSILENT /SUPPRESSMSGBOXES /NORESTART');
}

/**
 * Command line switch for Inno Setup installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INNO_5')) {
	define('SWITCH_INNO_5', '/VERYSILENT /SUPPRESSMSGBOXES /NORESTART /LOG="%TMP%\%PKG_NAME%.log"');
}

/**
 * Command line switch for InstallShield installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INSTALLSHIELD_1')) {
	define('SWITCH_INSTALLSHIELD_1', '/s /sms');
}

/**
 * Command line switch for InstallShield installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INSTALLSHIELD_2')) {
	define('SWITCH_INSTALLSHIELD_2', '/s /sms /f1"%SOFTWARE%\setup.iss"');
}

/**
 * Command line switch for InstallShield installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INSTALLSHIELD_3')) {
	define('SWITCH_INSTALLSHIELD_3', '/s /sms /f1"%SOFTWARE%\setup.iss" /f2"%TMP%\%PKG_NAME%.log"');
}

/**
 * Command line switch for InstallShield installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INSTALLSHIELD_4')) {
	define('SWITCH_INSTALLSHIELD_4', '/s /v"/qb /norestart"');
}

/**
 * Command line switch for InstallShield installer
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('SWITCH_INSTALLSHIELD_5')) {
	define('SWITCH_INSTALLSHIELD_5', '/s /v"/qb /norestart" /f2"%TMP%\%PKG_NAME%.log"');
}

/**
 * Console command `XCOPY` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_XCOPY')) {
	define('VARIABLE_COMMAND_COMSPEC_XCOPY', '/C xcopy "%SOFTWARE%\App" "%ProgramDir%\" /E /Q /H /R /Y');
}

/**
 * Console command `COPY` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_COPY')) {
	define('VARIABLE_COMMAND_COMSPEC_COPY', '/C copy /Y "%SOFTWARE%\setup.exe" "%ProgramDir%\"');
}

/**
 * Console command `MOVE` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_MOVE')) {
	define('VARIABLE_COMMAND_COMSPEC_MOVE', '/C move /Y "%TempDir%\config.ini" "%ProgramDir%\"');
}

/**
 * Console command `DEL` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_DEL')) {
	define('VARIABLE_COMMAND_COMSPEC_DEL', '/C del /F /Q "%ProgramDir%\app.exe"');
}

/**
 * Console command `RMDIR` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_RMDIR')) {
	define('VARIABLE_COMMAND_COMSPEC_RMDIR', '/C rmdir /S /Q "%TempDir%"');
}

/**
 * Console command `run script.cmd` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_CMD')) {
	define('VARIABLE_COMMAND_COMSPEC_CMD', '/C "%SOFTWARE%\script.cmd"');
}

/**
 * Console command `Service start` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_SERVICE_START')) {
	define('VARIABLE_COMMAND_COMSPEC_SERVICE_START', '/C net start "Service Name"');
}

/**
 * Console command `Service stop` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_SERVICE_STOP')) {
	define('VARIABLE_COMMAND_COMSPEC_SERVICE_STOP', '/C net stop "Service Name"');
}

/**
 * Console command `Service restart` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_SERVICE_RESTART')) {
	define('VARIABLE_COMMAND_COMSPEC_SERVICE_RESTART', '/C net stop "Service Name" && net start "Service Name"');
}

/**
 * Console command `Registry import using reg import` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_REGISTRY_IMPORT')) {
	define('VARIABLE_COMMAND_COMSPEC_REGISTRY_IMPORT', '/C reg import "%SOFTWARE%\settings.reg" /reg:64');
}

/**
 * Console command `Registry import using regedit` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_REGISTRY_IMPORT_REGEDIT')) {
	define('VARIABLE_COMMAND_COMSPEC_REGISTRY_IMPORT_REGEDIT', '%SystemRoot%\regedit.exe /s "%SOFTWARE%\settings.reg"');
}

/**
 * Console command `Registry add` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_REGISTRY_ADD')) {
	define('VARIABLE_COMMAND_COMSPEC_REGISTRY_ADD', '/C reg add "HKLM\Software\app" /v "param" /t REG_SZ /d "value" /f');
}

/**
 * Console command `Uninstall MSI package` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_UNINSTALL')) {
	define('VARIABLE_COMMAND_COMSPEC_UNINSTALL', '/C wmic product where "name like \'App Name%%\'" call uninstall /nointeractive');
}

/**
 * Console command `Delay` for using with %ComSpec%
 *
 * Used as information for autocomplete string in package action command.
 */
if (!defined('VARIABLE_COMMAND_COMSPEC_DELAY')) {
	define('VARIABLE_COMMAND_COMSPEC_DELAY', '/C ping -n 127.0.0.1');
}

/**
 * The task of the shell `cron` used for parsing log files
 *
 * Used for set name of command. Default value `parse_logs`
 */
if (!defined('SHELL_CRON_TASK_PARSE_LOGS')) {
	define('SHELL_CRON_TASK_PARSE_LOGS', 'parse_logs');
}

/**
 * The task of the shell `cron` used for parsing client database files
 *
 * Used for set name of command. Default value `parse_databases`
 */
if (!defined('SHELL_CRON_TASK_PARSE_DATABASES')) {
	define('SHELL_CRON_TASK_PARSE_DATABASES', 'parse_databases');
}

/**
 * Time limit for import XML data
 *
 * Used for set time limit of import XML data. Default value `180`
 */
if (!defined('IMPORT_TIME_LIMIT')) {
	define('IMPORT_TIME_LIMIT', 180);
}

/**
 * Limiting the recursion level for processing dependency data
 *
 * Used for set limit the recursion level for processing dependency data
 *  on build of a dependency list.
 *  Default value `25`
 */
if (!defined('IMPORT_DEPEND_DEEP_LIMIT')) {
	define('IMPORT_DEPEND_DEEP_LIMIT', 25);
}

/**
 * Time limit for parsing log files
 *
 * Used for set time limit of parsing log files. Default value `180`
 */
if (!defined('LOG_PARSE_TIME_LIMIT')) {
	define('LOG_PARSE_TIME_LIMIT', 180);
}

/**
 * Time limit for parsing client database files
 *
 * Used for set time limit of parsing client database files.
 *  Default value `180`
 */
if (!defined('CLIENT_DATABASE_PARSE_TIME_LIMIT')) {
	define('CLIENT_DATABASE_PARSE_TIME_LIMIT', 180);
}

/**
 * Time limit for check state tree
 *
 * Used for set time limit of check state tree. Default value `60`
 */
if (!defined('CHECK_TREE_TIME_LIMIT')) {
	define('CHECK_TREE_TIME_LIMIT', 60);
}

/**
 * Time limit for recovering tree
 *
 * Used for set time limit of recovering tree. Default value `120`
 */
if (!defined('TASK_RECOVERY_TREE_TIME_LIMIT')) {
	define('TASK_RECOVERY_TREE_TIME_LIMIT', 120);
}

/**
 * Time limit for import XML data for console task
 *
 * Used for set time limit of import XML data. Default value `180`
 */
if (!defined('TASK_IMPORT_XML_TIME_LIMIT')) {
	define('TASK_IMPORT_XML_TIME_LIMIT', 180);
}

/**
 * Autocomplete data limit for package action command
 *
 * Used to set the limit for autocomplete data. Default value `10`
 */
if (!defined('AUTOCOMPLETE_ARRAY_DATA_LIMIT')) {
	define('AUTOCOMPLETE_ARRAY_DATA_LIMIT', 10);
}

/**
 * The limit for displaying error report entries in email
 *
 * Used to set the limit for displaying error report entries. 
 *  Default value `10`
 */
if (!defined('EMAIL_REPORT_ERRORS_SHOW_RECORDS_LIMIT')) {
	define('EMAIL_REPORT_ERRORS_SHOW_RECORDS_LIMIT', 10);
}

/**
 * WPKG configuration parameter `Query mode` value `local`
 *
 * Used as WPKG configuration parameter `Query mode` value `local`.
 *  Default value `local`
 */
if (!defined('WPKG_CONFIG_QUERY_MODE_LOCAL')) {
	define('WPKG_CONFIG_QUERY_MODE_LOCAL', 'local');
}

/**
 * WPKG configuration parameter `Query mode` value `remote`
 *
 * Used as WPKG configuration parameter `Query mode` value `remote`.
 *  Default value `remote`
 */
if (!defined('WPKG_CONFIG_QUERY_MODE_REMOTE')) {
	define('WPKG_CONFIG_QUERY_MODE_REMOTE', 'remote');
}

/**
 * WPKG configuration parameter `Log level` value `Errors only`
 *
 * Used as WPKG configuration parameter `Log level` value `Errors only`.
 *  Default value `1`
 */
if (!defined('WPKG_CONFIG_LOG_LEVEL_ERRORS_ONLY')) {
	define('WPKG_CONFIG_LOG_LEVEL_ERRORS_ONLY', 1);
}

/**
 * WPKG configuration parameter `Log level` value `Warnings`
 *
 * Used as WPKG configuration parameter `Log level` value `Warnings`.
 *  Default value `2`
 */
if (!defined('WPKG_CONFIG_LOG_LEVEL_WARNINGS')) {
	define('WPKG_CONFIG_LOG_LEVEL_WARNINGS', 2);
}

/**
 * WPKG configuration parameter `Log level` value `Information`
 *
 * Used as WPKG configuration parameter `Log level` value `Information`.
 *  Default value `4`
 */
if (!defined('WPKG_CONFIG_LOG_LEVEL_INFORMATION')) {
	define('WPKG_CONFIG_LOG_LEVEL_INFORMATION', 4);
}

/**
 * WPKG configuration parameter `Log level` value `Audit success`
 *
 * Used as WPKG configuration parameter `Log level` value `Audit success`.
 *  Default value `8`
 */
if (!defined('WPKG_CONFIG_LOG_LEVEL_AUDIT_SUCCESS')) {
	define('WPKG_CONFIG_LOG_LEVEL_AUDIT_SUCCESS', 8);
}

/**
 * WPKG configuration parameter `Log level` value `Audit failure`
 *
 * Used as WPKG configuration parameter `Log level` value `Audit failure`.
 *  Default value `16`
 */
if (!defined('WPKG_CONFIG_LOG_LEVEL_AUDIT_FAILURE')) {
	define('WPKG_CONFIG_LOG_LEVEL_AUDIT_FAILURE', 16);
}

/**
 * Item of list of download XML files `Packages` value `packages`
 *
 * Used as item of list of download XML files `Packages` value `packages`.
 *  Default value `packages`
 */
if (!defined('DOWNLOAD_XML_LIST_ITEM_PACKAGES')) {
	define('DOWNLOAD_XML_LIST_ITEM_PACKAGES', 'packages');
}

/**
 * Item of list of download XML files `Profiles` value `profiles`
 *
 * Used as item of list of download XML files `Profiles` value `profiles`.
 *  Default value `profiles`
 */
if (!defined('DOWNLOAD_XML_LIST_ITEM_PROFILES')) {
	define('DOWNLOAD_XML_LIST_ITEM_PROFILES', 'profiles');
}

/**
 * Item of list of download XML files `Hosts` value `hosts`
 *
 * Used as item of list of download XML files `Hosts` value `hosts`.
 *  Default value `hosts`
 */
if (!defined('DOWNLOAD_XML_LIST_ITEM_HOSTS')) {
	define('DOWNLOAD_XML_LIST_ITEM_HOSTS', 'hosts');
}

/**
 * Item of list of download XML files `Settings of WPKG` value `configs`
 *
 * Used as item of list of download XML files `Settings of WPKG` value `configs`.
 *  Default value `configs`
 */
if (!defined('DOWNLOAD_XML_LIST_ITEM_SETTINGS_OF_WPKG')) {
	define('DOWNLOAD_XML_LIST_ITEM_SETTINGS_OF_WPKG', 'configs');
}

/**
 * Item of list of export XML files `Packages` value `packages`
 *
 * Used as item of list of export XML files `Packages` value `packages`.
 *  Default value `packages`
 */
if (!defined('EXPORT_XML_LIST_ITEM_PACKAGES')) {
	define('EXPORT_XML_LIST_ITEM_PACKAGES', 'packages');
}

/**
 * Item of list of export XML files `Profiles` value `profiles`
 *
 * Used as item of list of export XML files `Profiles` value `profiles`.
 *  Default value `profiles`
 */
if (!defined('EXPORT_XML_LIST_ITEM_PROFILES')) {
	define('EXPORT_XML_LIST_ITEM_PROFILES', 'profiles');
}

/**
 * Item of list of export XML files `Hosts` value `hosts`
 *
 * Used as item of list of export XML files `Hosts` value `hosts`.
 *  Default value `hosts`
 */
if (!defined('EXPORT_XML_LIST_ITEM_HOSTS')) {
	define('EXPORT_XML_LIST_ITEM_HOSTS', 'hosts');
}

/**
 * First category for WPI
 *
 * Used as the first category created for WPI. Default value `Applications`
 */
if (!defined('WPI_CATEGORY_APPLICATIONS')) {
	define('WPI_CATEGORY_APPLICATIONS', 'Applications');
}

/**
 * Cache configuration for store statistics information of model `Host`
 *
 * Used for access to cached data of for store statistics information.
 *  of model `Host`. Default value `statistics_info_statistics_info_host`.
 */
if (!defined('CACHE_KEY_STATISTICS_INFO_HOST')) {
	define('CACHE_KEY_STATISTICS_INFO_HOST', 'statistics_info_host');
}

/**
 * Cache configuration for store statistics information of model `Log`
 *
 * Used for access to cached data of for store statistics information.
 *  of model `Log`. Default value `statistics_info_log`.
 */
if (!defined('CACHE_KEY_STATISTICS_INFO_LOG')) {
	define('CACHE_KEY_STATISTICS_INFO_LOG', 'statistics_info_log');
}

/**
 * Cache configuration for store statistics information of model `Package`
 *
 * Used for access to cached data of for store statistics information.
 *  of model `Package`. Default value `statistics_info_package`.
 */
if (!defined('CACHE_KEY_STATISTICS_INFO_PACKAGE')) {
	define('CACHE_KEY_STATISTICS_INFO_PACKAGE', 'statistics_info_package');
}

/**
 * Cache configuration for store statistics information of model `Profile`
 *
 * Used for access to cached data of for store statistics information.
 *  of model `Profile`. Default value `statistics_info_profile`.
 */
if (!defined('CACHE_KEY_STATISTICS_INFO_PROFILE')) {
	define('CACHE_KEY_STATISTICS_INFO_PROFILE', 'statistics_info_profile');
}

/**
 * Cache configuration for store statistics information of model `Report`
 *
 * Used for access to cached data of for store statistics information.
 *  of model `Report`. Default value `statistics_info_report`.
 */
if (!defined('CACHE_KEY_STATISTICS_INFO_REPORT')) {
	define('CACHE_KEY_STATISTICS_INFO_REPORT', 'statistics_info_report');
}

/**
 * Cache configuration for store lists information of constants
 *
 * Used for access to cached data of for store lists information.
 *  of constants. Default value `lists_info_constant`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_CONSTANT')) {
	define('CACHE_KEY_LISTS_INFO_CONSTANT', 'lists_info_constant');
}

/**
 * Cache configuration for store lists information of model `PackagePriority`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackagePriority`. Default value `lists_info_package_priority`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_PRIORITY', 'lists_info_package_priority');
}

/**
 * Cache configuration for store lists information of model `Attribute`
 *
 * Used for access to cached data of for store lists information
 *  of model `Attribute`. Default value `lists_info_attribute`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_ATTRIBUTE')) {
	define('CACHE_KEY_LISTS_INFO_ATTRIBUTE', 'lists_info_attribute');
}

/**
 * Cache configuration for store lists information of model `Check`
 *
 * Used for access to cached data of for store lists information
 *  of model `Check`. Default value `lists_info_check`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_CHECK')) {
	define('CACHE_KEY_LISTS_INFO_CHECK', 'lists_info_check');
}

/**
 * Cache configuration for store lists information of model `ExitcodeRebootType`
 *
 * Used for access to cached data of for store lists information
 *  of model `ExitcodeRebootType`. Default value `lists_info_exitcode_reboot_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_EXITCODE_REBOOT_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_EXITCODE_REBOOT_TYPE', 'lists_info_exitcode_reboot_type');
}

/**
 * Cache configuration for store lists information of model `GarbageType`
 *
 * Used for access to cached data of for store lists information
 *  of model `GarbageType`. Default value `lists_info_garbage_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_GARBAGE_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_GARBAGE_TYPE', 'lists_info_garbage_type');
}

/**
 * Cache configuration for store lists information of model `Host`
 *
 * Used for access to cached data of for store lists information
 *  of model `Host`. Default value `lists_info_host`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_HOST')) {
	define('CACHE_KEY_LISTS_INFO_HOST', 'lists_info_host');
}

/**
 * Cache configuration for store lists information of model `LogHost`
 *
 * Used for access to cached data of for store lists information
 *  of model `LogHost`. Default value `lists_info_log_host`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_LOG_HOST')) {
	define('CACHE_KEY_LISTS_INFO_LOG_HOST', 'lists_info_log_host');
}

/**
 * Cache configuration for store lists information of model `LogType`
 *
 * Used for access to cached data of for store lists information
 *  of model `LogType`. Default value `lists_info_log_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_LOG_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_LOG_TYPE', 'lists_info_log_type');
}

/**
 * Cache configuration for store lists information of model `Package`
 *
 * Used for access to cached data of for store lists information
 *  of model `Package`. Default value `lists_info_package`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE', 'lists_info_package');
}

/**
 * Cache configuration for store lists information of model `PackageAction`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackageAction`. Default value `lists_info_package_action`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_ACTION')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_ACTION', 'lists_info_package_action');
}

/**
 * Cache configuration for store lists information of model `PackageActionType`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackageActionType`. Default value `lists_info_package_action_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_ACTION_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_ACTION_TYPE', 'lists_info_package_action_type');
}

/**
 * Cache configuration for store lists information of model `PackageExecuteType`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackageExecuteType`. Default value `lists_info_package_execute_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_EXECUTE_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_EXECUTE_TYPE', 'lists_info_package_execute_type');
}

/**
 * Cache configuration for store lists information of model `PackageNotifyType`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackageNotifyType`. Default value `lists_info_package_notify_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_NOTIFY_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_NOTIFY_TYPE', 'lists_info_package_notify_type');
}

/**
 * Cache configuration for store lists information of model `PackageRebootType`
 *
 * Used for access to cached data of for store lists information
 *  of model `PackageRebootType`. Default value `lists_info_package_reboot_type`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PACKAGE_REBOOT_TYPE')) {
	define('CACHE_KEY_LISTS_INFO_PACKAGE_REBOOT_TYPE', 'lists_info_package_reboot_type');
}

/**
 * Cache configuration for store lists information of model `Profile`
 *
 * Used for access to cached data of for store lists information
 *  of model `Profile`. Default value `lists_info_profile`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_PROFILE')) {
	define('CACHE_KEY_LISTS_INFO_PROFILE', 'lists_info_profile');
}

/**
 * Cache configuration for store lists information of model `ReportHost`
 *
 * Used for access to cached data of for store lists information
 *  of model `ReportHost`. Default value `lists_info_report_host`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_REPORT_HOST')) {
	define('CACHE_KEY_LISTS_INFO_REPORT_HOST', 'lists_info_report_host');
}

/**
 * Cache configuration for store lists information of model `ReportState`
 *
 * Used for access to cached data of for store lists information
 *  of model `ReportState`. Default value `lists_info_report_state`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_REPORT_STATE')) {
	define('CACHE_KEY_LISTS_INFO_REPORT_STATE', 'lists_info_report_state');
}

/**
 * Cache configuration for store lists information of model `Wpi`
 *
 * Used for access to cached data of for store lists information
 *  of model `Wpi`. Default value `lists_info_wpi`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_WPI')) {
	define('CACHE_KEY_LISTS_INFO_WPI', 'lists_info_wpi');
}

/**
 * Cache configuration for store lists information of model `WpiCategory`
 *
 * Used for access to cached data of for store lists information
 *  of model `WpiCategory`. Default value `lists_info_wpi_category`.
 */
if (!defined('CACHE_KEY_LISTS_INFO_WPI_CATEGORY')) {
	define('CACHE_KEY_LISTS_INFO_WPI_CATEGORY', 'lists_info_wpi_category');
}

/**
 * Cache configuration for store model configuration information
 *
 * Used for access to cached data of for store model configuration
 *  information. Default value `model_cfg_info`.
 */
if (!defined('CACHE_KEY_MODEL_CFG_INFO')) {
	define('CACHE_KEY_MODEL_CFG_INFO', 'model_cfg_info');
}
