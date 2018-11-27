<?php
/**
 * This file is the view file of the application. Used to render
 *  XML information.
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

if (!isset($outXML)) {
	$outXML = '';
}

if (!isset($selLine)) {
	$selLine = [];
}

if (!isset($errorMsg)) {
	$errorMsg = null;
}

if (!isset($fullName)) {
	$fullName = null;
}

if (empty($outXML)) {
	return;
}
?>
<dl class="dl-horizontal dl-popup-modal">
<?php
	if (!empty($fullName)):
?>
	<dt><?php echo __('Preview type') . ':'; ?></dt>
	<dd><?php echo h($fullName); ?></dd>
<?php endif; ?>
	<dt><?php echo __('Data') . ':'; ?></dt>
	<dd>
<?php
if (!empty($errorMsg)) {
	echo $this->Html->div(
		'alert alert-danger alert-dismissible',
		$this->Html->tag(
			'button',
			$this->Html->tag('span', '&times;', ['aria-hidden' => 'true']),
			['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']
		) .
		$errorMsg,
		['role' => 'alert']
	);
}
	echo $this->GeshiExt->highlightText($outXML, 'XML', false, $selLine);
?>
	</dd>
</dl>
