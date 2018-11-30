<?php
/**
 * This file is the view file of the application. Used to render
 *  form for uploading XML file.
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

if (!isset($maxfilesize)) {
	$maxfilesize = (int)UPLOAD_FILE_SIZE_LIMIT;
}

if (!isset($acceptfiletypes)) {
	$acceptfiletypes = UPLOAD_FILE_TYPES_CLIENT;
}

if (!isset($validxmltypes)) {
	$validxmltypes = $this->ViewExtension->showEmpty();
}
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
	echo $this->Form->createUploadForm('Upload');
?>
			<fieldset>
<?php
	echo $this->Form->staticControl(__('Valid XML file types') . ':', $validxmltypes);
	echo $this->Form->staticControl(__('Maximum XML file size') . ':', $this->Number->toReadableSize($maxfilesize));
	$url = $this->Html->url(['controller' => 'uploads', 'action' => 'upload', 'ext' => 'json']);
	$btnUploadTitle = $this->ViewExtension->iconTag('fas fa-file-upload fa-lg') . '&nbsp;' .
		$this->Html->tag('span', __('Upload XML file'));
	echo $this->Form->upload($url, $maxfilesize, $acceptfiletypes, null, $btnUploadTitle);
?>
			</fieldset>
<?php
	echo $this->Form->end();
?>
		</div>
	</div>
