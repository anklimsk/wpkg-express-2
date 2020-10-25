<?php
/**
 * This file is the view file of the application. Used for render
 *  information of disabled unused hosts and profiles in e-mail
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

if (!isset($data)) {
	$data = [];
}

if (!isset($created)) {
	$created = null;
}

if (!isset($useNestedList)) {
	$useNestedList = true;
}

if (empty($data)) {
	return;
}
?>
<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', __('Created'));
	echo $this->Html->tag('dd', $this->Time->i18nFormat($created, '%x %X'));

	foreach ($data as $label => $info) {
		if (empty($info)) {
			continue;
		}
		if (is_array($info)) {
			if ($useNestedList) {
				$text = $this->Html->nestedList($info, [], [], 'ol');
			} else {
				$text = implode(', ', $info) . '.';
			}
		} else {
			$text = $info;
		}
		echo $this->Html->tag('dt', h($label));
		echo $this->Html->tag('dd', $text);
	}
?>
</dl>
