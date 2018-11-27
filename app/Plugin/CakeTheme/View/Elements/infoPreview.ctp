<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  preview of document.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @see ExportComponent::preview()
 * @package plugin.View.Elements
 */

if (!isset($previewInfo)) {
	return;
}

if (!isset($downloadExt)) {
	$downloadExt = null;
}

if (!isset($id)) {
	$id = null;
}

	$this->loadHelper('CakeTheme.ViewExtension');
	$downloadIcon = $this->ViewExtension->getIconForExtension($downloadExt);
	$downloadExtUc = mb_strtoupper($downloadExt);
if (empty($downloadExtUc)) {
	$downloadExtUc = '?';
}
	$headerMenuActions = [
		$this->ViewExtension->menuActionLink(
			$downloadIcon,
			__d('view_extension', 'Download file %s', $downloadExtUc),
			['action' => 'download', $id, $previewInfo['download']['orig'], 'ext' => $downloadExt]
		)
	];
	if (!empty($previewInfo['download']['pdf'])) {
		$headerMenuActions[] = $this->ViewExtension->menuActionLink(
			'fas fa-file-pdf',
			__d('view_extension', 'Download file %s', 'PDF'),
			['action' => 'download', $id, $previewInfo['download']['pdf'], 'ext' => 'pdf']
		);
	}
?>
<div class="row">
	<div class="col-lg-12">
<?php
	echo $this->Html->div(
		'thumbnail text-center',
		$this->Html->tag(
			'h3',
			h($previewInfo['exportFileName']) . '&nbsp;' .
			$this->ViewExtension->menuHeaderPage($headerMenuActions),
			['class' => 'text-danger']
		)
	);
?>
	</div>
</div>
<?php
	$pages = count($previewInfo['preview']);
	$rows = array_chunk($previewInfo['preview'], 3, true);
foreach ($rows as $row) :
?>
<div class="row">
<?php
foreach ($row as $i => $previewItem) :
?>
	<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<div class="thumbnail">
<?php
	$pagesLabel = __d('view_extension', 'Page %d of %d', $i + 1, $pages);
	echo $this->Html->link(
		$this->Html->tag('img', null, ['src' => $previewItem]),
		$previewItem,
		[
			'data-toggle' => 'lightbox',
			'data-gallery' => 'preview-gallery',
			'data-type' => 'image',
			'data-title' => __d('view_extension', 'Preview file'),
			'data-footer' => $pagesLabel,
			'data-loading-message' => __d('view_extension', 'Loading...'),
			'title' => __d('view_extension', 'Click to preview'),
			'escape' => false
		]
	);
?>
			<div class="caption">
<?php echo $this->Html->tag('h5', $pagesLabel); ?>
			</div>
		</div>
	</div>
<?php
endforeach;
?>
</div>
<?php
endforeach;
