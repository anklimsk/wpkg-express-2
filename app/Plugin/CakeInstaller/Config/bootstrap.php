<?php
/**
 * This file is the bootstrap file of the plugin.
 * Definition constants for plugin.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

/**
 * Path to marker file for checking application is installed.
 *
 * Used for fast checking application is installed. Default value `/tmp/installer/installed.txt`
 */
if (!defined('CAKE_INSTALLER_MARKER_FILE_INSTALLED')) {
	define('CAKE_INSTALLER_MARKER_FILE_INSTALLED', TMP . 'installer' . DS . 'installed.txt');
}

/**
 * Path to marker file for checking if need restart installation process.
 *
 * Used for fast checking if need restart installation process.
 *  Default value `/tmp/installer/restart.txt`
 */
if (!defined('CAKE_INSTALLER_MARKER_FILE_RESTART')) {
	define('CAKE_INSTALLER_MARKER_FILE_RESTART', TMP . 'installer' . DS . 'restart.txt');
}

/**
 * The task of the shell `installer` used for setting application UI language.
 *
 * Used for set name of command. Default value `setuilang`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETUILANG')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETUILANG', 'setuilang');
}

/**
 * The task of the shell `installer` used for Checking PHP
 *  environment.
 *
 * Used for set name of command. Default value `check`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK', 'check');
}

/**
 * The task of the shell `installer` used for set access
 *  rights to folders and application files.
 *
 * Used for set name of command. Default value `setdirpermiss`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETDIRPERMISS')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETDIRPERMISS', 'setdirpermiss');
}

/**
 * The task of the shell `installer` used for create and write
 *  security keys in the settings file.
 *
 * Used for set name of command. Default value `setsecurkey`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY', 'setsecurkey');
}

/**
 * The task of the shell `installer` used for create and write
 *  time zone in the settings file.
 *
 * Used for set name of command. Default value `settimezone`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETTIMEZONE')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETTIMEZONE', 'settimezone');
}

/**
 * The task of the shell `installer` used for create and write
 *  base URL of application in the settings file.
 *
 * Used for set name of command. Default value `setbaseurl`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETBASEURL')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETBASEURL', 'setbaseurl');
}

/**
 * The task of the shell `installer` used for check database
 *  connections.
 *
 * Used for set name of command. Default value `connectdb`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONNECT_DB')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONNECT_DB', 'connectdb');
}

/**
 * The task of the shell `installer` used for configure database
 *  connections.
 *
 * Used for set name of command. Default value `configdb`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB', 'configdb');
}

/**
 * The task of the shell `installer` used for deploying the database.
 *
 * Used for set name of command. Default value `createdb`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_DB')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_DB', 'createdb');
}

/**
 * The task of the shell `installer` used for create symlinks to files.
 *
 * Used for set name of command. Default value `createsymlinks`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_SYMLINKS')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_SYMLINKS', 'createsymlinks');
}

/**
 * The task of the shell `installer` used for create cron jobs.
 *
 * Used for set name of command. Default value `createcronjobs`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_CRONJOBS')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_CREATE_CRONJOBS', 'createcronjobs');
}

/**
 * The task of the shell `installer` used for start the
 *  application installation.
 *
 * Used for set name of command. Default value `install`
 */
if (!defined('CAKE_INSTALLER_SHELL_INSTALLER_TASK_INSTALL')) {
	define('CAKE_INSTALLER_SHELL_INSTALLER_TASK_INSTALL', 'install');
}
