<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing information about installation date of
 *  package in profile.
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

if (!isset($fullName)) {
	$fullName = null;
}

	echo $this->Form->create('PackagesProfile', $this->ViewExtension->getFormOptions());
?>
	<fieldset>
		<legend><?php echo __('Package of profile'); ?></legend>
<?php
	$hiddenFields = [
		'PackagesProfile.id',
		'PackagesProfile.profile_id',
		'PackagesProfile.package_id',
	];
	echo $this->Form->hiddenFields($hiddenFields);
	echo $this->Form->staticControl(__('Editing installation date') . ':', h($fullName));
	echo $this->Form->input('PackagesProfile.installdate', ['label' => __('Installation date') . ':', 'title' => __('Date from which the package should be installed (this date or later). Date has to be specified in ISO 8601 format, e.g.: 2018-12-27T15:30:42+03:00.'),
		'type' => 'dateTimeSelect', 'date-format' => 'YYYY-MM-DDTHH:mm:ssZ',
		'data-inputmask-alias' => '9999-99-99T99:99:99[+|-99:99]',
		'autocomplete' => 'off', 'autofocus' => true]);
	echo $this->Form->input('PackagesProfile.uninstalldate', ['label' => __('Uninstallation date') . ':', 'title' => __('Date from which the package should be removed (this date or later). Date has to be specified in ISO 8601 format, e.g.: 2018-12-27T15:30:42+03:00.'),
		'type' => 'dateTimeSelect', 'date-format' => 'YYYY-MM-DDTHH:mm:ssZ',
		'data-inputmask-alias' => '9999-99-99T99:99:99[+|-99:99]',
		'autocomplete' => 'off']);
?>
	</fieldset>
<?php
	echo $this->Form->submit(__('Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
