<?php
/**
 * This file is the view file of the application. Used to render
 *  information about checks with buttons.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.View.Elements
 */

if (!isset($checks)) {
	$checks = [];
}

if (!isset($nest)) {
	$nest = false;
}

if (!isset($expandAll)) {
	$expandAll = false;
}

if (!isset($draggable)) {
	$draggable = false;
}

if (!isset($displayItalics)) {
	$displayItalics = false;
}

if (!isset($checkRefType)) {
	$checkRefType = null;
}

if (!isset($checkRefId)) {
	$checkRefId = null;
}

if (!isset($showShort)) {
	$showShort = false;
}

if (!isset($showBtnExpand)) {
	$showBtnExpand = false;
}

	$dropUrl = $this->Html->url(['controller' => 'checks', 'action' => 'drop', 'ext' => 'json']);
	$infoChecksFull = '';
	if (!empty($checkRefType) && !empty($checkRefId)) {
		$actions = $this->ViewExtension->buttonLink(
			'fas fa-plus',
			'btn-success',
			['controller' => 'checks', 'action' => 'add', $checkRefType, $checkRefId],
			[
				'title' => __('Add checks'),
				'action-type' => 'modal',
				'data-modal-size' => 'lg'
			]
		);
		if (!empty($checks)) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-clipboard-check',
				'btn-info',
				['controller' => 'checks', 'action' => 'verify', $checkRefType, $checkRefId],
				[
					'title' => __('Verify state of tree checks'),
					'action-type' => 'modal',
				]
			);
		}
		if ($showBtnExpand) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-expand-arrows-alt',
				'btn-info',
				['controller' => 'checks', 'action' => 'view', $checkRefType, $checkRefId],
				[
					'title' => __('Open in new window'),
					'action-type' => 'modal',
					'data-modal-size' => 'lg'
				]
			);
		}
		$infoChecksFull .= $this->Html->div('action pull-right hide-popup', $actions);
	}
	$infoChecksFull .= $this->Html->div('pull-left', $this->element('infoChecks', compact('checks', 'nest', 'expandAll', 'draggable', 'dropUrl', 'displayItalics')));

	if ($showShort) {
		echo $infoChecksFull;
		return;
	} else {
		echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
	}
?>
<dl class="dl-horizontal dl-popup-modal">
<?php
	if (!empty($fullName)):
?>
	<dt><?php echo __('Check type') . ':'; ?></dt>
	<dd><?php echo h($fullName); ?></dd>
<?php
	endif;
?>
	<dt><?php echo __('Check') . ':'; ?></dt>
	<dd>
<?php
	echo $infoChecksFull;
?>
	</dd>
</dl>
