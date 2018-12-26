<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing settings of WPKG.
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
 * @package app.View.Elements
 */

if (!isset($listQueryMode)) {
	$listQueryMode = [];
}

if (!isset($listlogLevel)) {
	$listlogLevel = [];
}

	$modelName = 'Config';
	$legend = __('Editing settings of WPKG');
	$formOptions = ['autocomplete' => 'off'];
	$inputList = [
		$modelName . '.force' => ['label' => [__('Do not consider wpkg.xml but check existence of packages'),
			__('Do not consider wpkg.xml but check existence of packages'), ':'],
			'type' => 'checkbox'],
		$modelName . '.forceInstall' => ['label' => [__('Force installation over existing packages'),
			__('Force installation over existing packages'), ':'],
			'type' => 'checkbox'],
		$modelName . '.quitonerror' => ['label' => [__('Force the script to immediately quit on error'),
			__('Force the script to immediately quit on error'), ':'],
			'type' => 'checkbox'],
		$modelName . '.debug' => ['label' => [__('Enable debug mode'),
			__('Enable debug mode. Prints lots of ugly debug messages to event log'), ':'],
			'type' => 'checkbox'],
		$modelName . '.dryrun' => ['label' => [__('Enable dryrun mode'),
			__('Enable dryrun mode. Does not apply any changes to the system. Enables debug output and disables reboot.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.quiet' => ['label' => [__('Disable all log messages printed to the console'),
			__('Disable all log messages printed to the console (cscript) or displayed as dialog boxes (wscript).'), ':'],
			'type' => 'checkbox'],
		$modelName . '.nonotify' => ['label' => [__('Disable user notification about WPKG actions'),
			__('Disable user notification about WPKG actions using windows messaging.'), ':'],
			'type' => 'checkbox', 'disabled' => true],
		$modelName . '.notificationDisplayTime' => ['label' => __('Defines how long a user notification is displayed to the user') . ':',
			'title' => __('Defines how long a user notification is displayed to the user. After timeout has been reached the message will be closed. Specify 0 in order to specify that messages are never closed automatically'),
			'type' => 'spin', 'data-toggle' => 'tooltip', 'min' => 0, 'max' => 30],
		$modelName . '.execTimeout' => ['label' => __('Default command execution timeout') . ':',
			'title' => __('This is the default timeout used when executing external commands. Each command which runs for longer than the defined amount of seconds will be be regarded as failed and WPKG will continue execution.'),
			'type' => 'spin', 'data-toggle' => 'tooltip', 'min' => 0, 'max' => 3600, 'maxboostedstep' => 60],
		$modelName . '.noreboot' => ['label' => [__('System does not reboot regardless of need'),
			__('System does not reboot regardless of need'), ':'],
			'type' => 'checkbox'],
		$modelName . '.noRunningState' => ['label' => [__('Disable export of running state to Windows registry'),
			__('Disable export of running state to Windows registry at HKCU\Software\WPKG\running'), ':'],
			'type' => 'checkbox'],
		$modelName . '.caseSensitivity' => ['label' => [__('Matching of package and profile IDs is case sensitive'),
			__('Matching of package and profile IDs is case sensitive'), ':'],
			'type' => 'checkbox'],
		$modelName . '.applyMultiple' => ['label' => [__('Match multiple host entries to a single host'),
			__('Match multiple host entries to a single host'), ':'],
			'type' => 'checkbox'],
		$modelName . '.noDownload' => ['label' => [__('Disable all downloads'),
			__('In this mode all download instructions will be simply ignored. Exactly the same way as if they were not specified in the XML at all. Use this option with caution as your install commands might require the files downloaded in download specifications.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.rebootCmd' => ['label' => __('Use the specified command for rebooting') . ':',
			'title' => __('Use the specified command for rebooting, either with full path or relative to the location of wpkg.js. Setting rebootCmd to "special" will use tools\psshutdown.exe'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		$modelName . '.settings_file_name' => ['label' => __('Filename of the local package database') . ':',
			'title' => __('Filename of the local package database (client-side) stored at %SystemRoot%\system32 by default (see settings_file_path).'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.settings_file_path' => ['label' => __('Path to the local package database') . ':',
			'title' => __('Path to the local package database (client-side). It is strongly suggested to NOT set this parameter at all if not required.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.noForcedRemove' => ['label' => [__('Disable forced removal of packages'),
			__('Disable forced removal of packages if they are removed from the profile AND the package database.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.noRemove' => ['label' => [__('Allows to disable removing of packages'),
			__('If used in conjunction with the /synchronize parameter it will just add packages but never remove them. Instead of removing a list of packages which would have been removed during that session is printed on exit. Packages are not removed from the local settings database (wpkg.xml). Therefore it	will contain a list of all packages ever installed.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.sendStatus' => ['label' => [__('Enable status output on STDOUT'),
			__('Controls weather WPKG prints some information about its progress to STDOUT. This output can be read by another program (GUI) to display some progress bar or additional status information to the user.'), ':'],
			'type' => 'checkbox', 'disabled' => true],
		$modelName . '.noUpgradeBeforeRemove' => ['label' => [__('Disables the upgrade-before-remove feature'),
			__('Usually WPKG upgrades a package to the latest available version before it removes the package. This allows administrators to fix bugs in the package and assure proper removal.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.settingsHostInfo' => ['label' => [__('Includes host information in local wpkg.xml attributes'),
			__('Allows to disable insert of host attributes to local settings DB. This is handy for testing as the current test-suite compares the local wpkg.xml database and the file will look different on all test machines if these attribute are present. This setting might be removed if all test-cases are adapted.'), ':'],
			'type' => 'checkbox'],
		$modelName . '.volatileReleaseMarker' => ['label' => __('Marks volatile releases') . ':',
			'title' => __('Marks volatile releases and "inverts" the algorithm that a longer version number is newer.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		$modelName . '.queryMode' => ['label' => [__('Allows to switch to remote mode'),
			__('Allows to switch to remote mode where package verification is skipped.'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listQueryMode],
		$modelName . '.logAppend' => ['label' => [__('Log files are appended instead of overwritten'),
			__('Specifies if the log file should be appended or overwritten.'), ':'],
			'type' => 'checkbox', 'disabled' => true],
		$modelName . '.logLevel' => ['label' => [__('Log level'),
			__('Log level'), ':'],
			'type' => 'select', 'data-toggle' => 'tooltip', 'options' => $listlogLevel,
			'multiple' => 'checkbox'],
		$modelName . '.log_file_path' => ['label' => __('Path where the logfiles are written to') . ':',
			'title' => __('This might be an UNC path on the network as well as a local path. Environment variables are expanded.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.logfilePattern' => ['label' => __('Pattern to generate the log file name') . ':',
			'title' => nl2br(__("Recognized patterns:\n[HOSTNAME] replaced by the executing hostname\n[PROFILE] replaced by the applying profile name\n[YYYY] replaced by year (4 digits)\n[MM] replaced by month number (2 digits)\n[DD] replaced by the day of the month (2 digits)\n[hh] replaced by hour of the day (24h format, 2 digits)\n[mm] replaced by minutes (2 digits)\n[ss] replaced by seconds (2 digits)")),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.packages_path' => ['label' => __('Define paths where WPKG looks for XML packages files') . ':',
			'title' => __('Multiple paths can be specified using the pipe symbol (|) as paths-separrator. If any of the paths are specified WPKG will ignore the built-in defaults.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.profiles_path' => ['label' => __('Define paths where WPKG looks for XML profiles files') . ':',
			'title' => __('Multiple paths can be specified using the pipe symbol (|) as paths-separrator. If any of the paths are specified WPKG will ignore the built-in defaults.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.hosts_path' => ['label' => __('Define paths where WPKG looks for XML hosts files') . ':',
			'title' => __('Multiple paths can be specified using the pipe symbol (|) as paths-separrator. If any of the paths are specified WPKG will ignore the built-in defaults.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.sRegPath' => ['label' => __('Registry path') . ':',
			'title' => __('Registry path'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
		$modelName . '.sRegWPKG_Running' => ['label' => __('Registry path') . ':',
			'title' => __('Registry path where WPKG stores its running state.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'disabled' => true],
	];
	$inputStatic = [];
	$tabsList = [
		__('Flags - part 1') => [
			$modelName . '.force',
			$modelName . '.forceInstall',
			$modelName . '.quitonerror',
			$modelName . '.debug',
			$modelName . '.dryrun',
			$modelName . '.quiet',
		],
		__('Flags - part 2') => [
			$modelName . '.nonotify',
			$modelName . '.noreboot',
			$modelName . '.noRunningState',
			$modelName . '.noDownload',
			$modelName . '.noForcedRemove',
			$modelName . '.noRemove',
			$modelName . '.noUpgradeBeforeRemove',
		],
		__('Flags - part 3') => [
			$modelName . '.caseSensitivity',
			$modelName . '.applyMultiple',
			$modelName . '.sendStatus',
		],
		__('Timeout, command for rebooting, ...') => [
			$modelName . '.notificationDisplayTime',
			$modelName . '.execTimeout',
			$modelName . '.rebootCmd',
			$modelName . '.volatileReleaseMarker',
			$modelName . '.queryMode',
		],
		__('Log') => [
			$modelName . '.logAppend',
			$modelName . '.logLevel',
			$modelName . '.log_file_path',
			$modelName . '.logfilePattern',
		],
		__('Database') => [
			$modelName . '.settingsHostInfo',
			$modelName . '.settings_file_name',
			$modelName . '.settings_file_path',
		],
		__('XML paths') => [
			$modelName . '.packages_path',
			$modelName . '.profiles_path',
			$modelName . '.hosts_path',
		],
		__('Registry paths') => [
			$modelName . '.sRegPath',
			$modelName . '.sRegWPKG_Running',
		],
	];

	echo $this->Form->createFormTabs($inputList, $inputStatic, $tabsList, $legend, $modelName, $formOptions);
