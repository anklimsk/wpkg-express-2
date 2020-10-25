<?php
/**
 * This file is the view file of the application. Used to render
 *  form for creating XML.
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
 * @copyright Copyright 2018-2020, Andrey Klimov.
 * @package app.View.Elements
 */

	if (!isset($selLine)) {
		$selLine = [];
	}

	if (!isset($listValidXmlTypes)) {
		$listValidXmlTypes = [];
	}

	if (!isset($warningMsg)) {
		$warningMsg = null;
	}

	if (!isset($errorMsg)) {
		$errorMsg = null;
	}

	if (!isset($maxfilesize)) {
		$maxfilesize = (int)UPLOAD_FILE_SIZE_LIMIT;
	}

	if (!isset($listXmlConfigUrl)) {
		$listXmlConfigUrl = XML_CREATE_LIST_XML_CONFIG_URL;
	}

	$name = __('XML text');
	if (!empty($fullName)) {
		$name = $fullName;
	}

	echo $this->Form->create('Create', $this->ViewExtension->getFormOptions(['class' => 'form-default form-create-xml']));
?>
	<fieldset>
		<legend><?php echo $name; ?></legend>
		<div class="row">
<?php if (!empty($listValidXmlTypes)): ?>
		<div class="tabbable">
			<ul class="nav nav-pills nav-stacked col-xs-12 col-sm-2 col-md-2 col-lg-2" role="tablist">
				<li role="presentation" class="active"><a href="#tabData" aria-controls="tabData" role="tab" data-toggle="tab"><?php echo __('Data'); ?></a></li>
				<li role="presentation"><a href="#tabInfo" aria-controls="tabInfo" role="tab" data-toggle="tab"><?php echo __('Information'); ?></a></li>
			</ul>
			<div class="tab-content col-xs-12 col-sm-10 col-md-10 col-lg-10">
				<div role="tabpanel" class="tab-pane active" id="tabData">
<?php else:
		$classRow = 'col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1';
		if ($this->request->is('modal')) {
			$classRow = 'col-md-12';
		}
		?>
			<div class="<?php echo $classRow; ?>">
<?php endif;
	echo $this->element('infoWarningMessages', compact('warningMsg'));
	echo $this->element('infoErrorMessages', compact('errorMsg'));
	echo $this->Form->input('Create.xml', ['label' => [__('XML'), __('Press Ctrl-J to jump to the tag that matches the one under the cursor.'), ':'],
		'type' => 'textarea', 'escape' => true,
		'rows' => '20', 'autocomplete' => 'off',
		'data-sel-lines' => json_encode($selLine)
	]);
	if (!empty($listValidXmlTypes)): ?>
				</div>
				<div role="tabpanel" class="tab-pane" id="tabInfo">
		<?php
			echo $this->Form->staticControl(__('Maximum XML size') . ':', $this->Number->toReadableSize($maxfilesize));
			echo $this->Form->staticControl(__('List of silent install, upgrade and uninstall configurations for many programs') . ':',
				$this->Html->link($listXmlConfigUrl, null, ['target' => '_blank']));
		?>
				</div>
			</div>
	<?php 
	endif; ?>
		</div>
		</div>
	</fieldset>
	<div class="form-group text-center">
<?php
	if (!empty($listValidXmlTypes)) {
		echo $this->Form->button(__('Clear'), ['type' => 'reset', 'class' => 'btn btn-warning btn-md']) . '&nbsp;' .
			$this->Form->button(__('Create'), ['type' => 'submit', 'class' => 'btn btn-success btn-md']);
	} else {
		echo $this->Form->button(__('Update'), ['type' => 'submit', 'class' => 'btn btn-success btn-md']);
	}
?>
	</div>
<?php
	echo $this->Form->end();
