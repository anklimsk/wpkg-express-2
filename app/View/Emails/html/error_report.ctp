<?php
/**
 * This file is the view file of the application. Used to render
 *  e-mail content about log records in HTML format.
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
 * @package app.View.Emails.html
 */
?>
<div class="container">
<?php
	echo $this->Html->div('page-header', $this->Html->tag('h2', __('WPKG Error report')));
	echo $this->element('tableLog', compact('logs', 'shortInfo', 'created'));
	echo $this->Html->tag('br', '');
	echo $this->Html->para('text-right', __(
		'To view the full list of errors, click \'%s\'',
		$this->Html->link(__('here'), $logsUrl)
	));
?>
</div>
