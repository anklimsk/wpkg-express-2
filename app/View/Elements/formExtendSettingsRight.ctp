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

if (!isset($extendViewFieldsList)) {
	$extendViewFieldsList = [];
}

if (!isset($readOnlyFieldsList)) {
	$readOnlyFieldsList = [];
}

if (!empty($varsExt)) {
	extract($varsExt);
}

	echo $this->Form->inputs([
		'legend' => __('XML output authentication'),
		'Setting.XmlAuthUser' => ['label' => __d('cake_settings_app', 'Username') . ':', 'title' => __('Username for the XML protect account.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.XmlAuthPassword' => ['label' => __d('cake_settings_app', 'Password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on'),
			'before' => '<input type="text" style="display:none"><input type="password" style="display:none">'],
		'Setting.XmlAuthPassword_confirm' => ['label' => __d('cake_settings_app', 'Confirm password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on')]
	]);

	echo $this->Form->inputs([
		'legend' => __('Parsing logs and client databases'),
		'Setting.SmbAuthUser' => ['label' => __d('cake_settings_app', 'Username') . ':', 'title' => __('Username to connect to the SMB share.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.SmbAuthPassword' => ['label' => __d('cake_settings_app', 'Password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on'),
			'before' => '<input type="text" style="display:none"><input type="password" style="display:none">'],
		'Setting.SmbAuthPassword_confirm' => ['label' => __d('cake_settings_app', 'Confirm password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on')],
		'Setting.SmbWorkgroup' => ['label' => __('Workgroup or domain') . ':', 'title' => __('Workgroup or domain.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.SmbServer' => ['label' => __('Server name') . ':', 'title' => __('Server name.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.SmbLogShare' => ['label' => __('Share name containing logs') . ':', 'title' => __('The name of the SMB share containing the log files, e.g.: wpkg/log.'),
			'type' => 'text', 'data-toggle' => 'tooltip'],
		'Setting.SmbDbShare' => ['label' => __('Share name containing client databases') . ':', 'title' => nl2br(__("The name of the SMB share containing the client database files, e.g.: wpkg/db.\n<b>Not necessary.</b> The report files containing the console output of the '<i>%s</i>' command, put in the '%s' subdirectory. File name format: <i>%%COMPUTERNAME%%.log</i>", 'cscript.exe wpkg.js //NoLogo /query:iml', REPORT_SHARE_SUBDIR)),
			'type' => 'text', 'data-toggle' => 'tooltip'],
	]);