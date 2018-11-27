<?php
/**
 * This file is the view file of the application. Used to render
 *  extended settings form.
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

if (!isset($varsExt) || !is_array($varsExt)) {
	$varsExt = [];
}

if (!isset($countryCodePhoneLib)) {
	$countryCodePhoneLib = [];
}

if (!isset($numberFormatList)) {
	$numberFormatList = [];
}

if (!isset($containerList)) {
	$containerList = [];
}

if (!empty($varsExt)) {
	extract($varsExt);
}

	echo $this->Form->inputs([
		'legend' => __('Internal authentication'),
		'Setting.IntAuthUser' => ['label' => __d('cake_settings_app', 'Username') . ':', 'title' => __('Username for internal authentication.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.IntAuthPassword' => ['label' => __d('cake_settings_app', 'Password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on'),
			'before' => '<input type="text" style="display:none"><input type="password" style="display:none">'],
		'Setting.IntAuthPassword_confirm' => ['label' => __d('cake_settings_app', 'Confirm password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on')]
	]);

	echo $this->Form->inputs([
		'legend' => __('XML output'),
		'Setting.ProtectXml' => ['label' => [__('Protect XML output'),
			__('Use authorization for the XML output.'), ':'],
			'type' => 'checkbox'],
		'Setting.ExportDisable' => ['label' => [__('Disable XML output'),
			__('Disable exporting XML and WPI configuration files.'), ':'],
			'type' => 'checkbox'],
		'Setting.FormatXml' => ['label' => [__('Format XML output'),
			__('Only useful for debugging purposes.'), ':'],
			'type' => 'checkbox'],
		'Setting.ExportNotes' => ['label' => [__('Export notes items'),
			__('Notes of items will only appear as XML comments.'), ':'],
			'type' => 'checkbox'],
		'Setting.ExportDisabled' => ['label' => [__('Export disabled items'),
			__('Disabled items will only appear as XML comments.'), ':'],
			'type' => 'checkbox'],
	]);

	echo $this->Form->inputs([
		'legend' => __('System'),
		'Setting.AutoVarRevision' => ['label' => [__('Auto variable %%%s%%', VARIABLE_AUTO_REVISION_NAME),
			__('Create auto variable %%%s%% in packages.', VARIABLE_AUTO_REVISION_NAME), ':'],
			'type' => 'checkbox'],
	]);

	echo $this->Form->inputs([
		'legend' => __('Search information'),
		'Setting.DefaultSearchAnyPart' => ['label' => [__('Search in any part string'),
			__('Default value for flag of search in any part string'), ':'],
			'type' => 'checkbox'],
	]);

	echo $this->Form->inputs([
		'legend' => __('Search on LDAP server'),
			'Setting.SearchBaseUser' => ['label' => __('Search base of users') . ':',
				'title' => __('Distinguished name of the search base object for search employee on LDAP server (e.g. CN=Users,DC=fabrikam,DC=com)'), 'type' => 'text',
				'data-toggle' => 'autocomplete', 'data-autocomplete-local' => json_encode($containerList),
				'data-autocomplete-min-length' => 1],
			'Setting.SearchBaseComp' => ['label' => __('Search base of computers') . ':',
				'title' => __('Distinguished name of the search base object for search computers on LDAP server (e.g. CN=Computers,DC=fabrikam,DC=com)'), 'type' => 'text',
				'data-toggle' => 'autocomplete', 'data-autocomplete-local' => json_encode($containerList),
				'data-autocomplete-min-length' => 1],
	]);