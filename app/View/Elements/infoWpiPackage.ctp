<?php
/**
 * This file is the view file of the application. Used to render
 *  information about WPI package.
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

if (!isset($wpiPackage)) {
	$wpiPackage = [];
}

if (empty($wpiPackage)) {
	return;
}
?>
<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', __('WPI package') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->popupModalLink(
		$wpiPackage['Package']['id_text'],
		['controller' => 'packages', 'action' => 'view', $wpiPackage['Package']['id']],
		[
			'data-modal-size' => 'lg',
			'data-popover-size' => 'lg'
		]
	));
	echo $this->Html->tag('dt', __('Category') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->showEmpty(h($wpiPackage['WpiCategory']['name'])));
	echo $this->Html->tag('dt', __('Default') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($wpiPackage['Wpi']['default']),
			['controller' => 'wpi', 'action' => 'default', $wpiPackage['Wpi']['id'], !$wpiPackage['Wpi']['default']],
			['title' => ($wpiPackage['Wpi']['default'] ? __('Don\'t selected by default') : __('Selected by default'))]
		)
	);
	echo $this->Html->tag('dt', __('Forcibly') . ':');
	echo $this->Html->tag('dd', $this->ViewExtension->ajaxLink(
			$this->ViewExtension->yesNo($wpiPackage['Wpi']['force']),
			['controller' => 'wpi', 'action' => 'force', $wpiPackage['Wpi']['id'], !$wpiPackage['Wpi']['force']],
			['title' => ($wpiPackage['Wpi']['force'] ? __('Don\'t selected forcibly') : __('Forcibly selected'))]
		)
	);
?>
</dl>
