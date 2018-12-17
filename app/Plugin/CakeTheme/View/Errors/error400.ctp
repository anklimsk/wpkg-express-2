<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  error message 400.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @see ExportComponent::preview()
 * @package plugin.View.Elements
 */
?>
<div class="container">
	<div class="alert alert-warning top-buffer" role="alert">
		<p class="text-center">
			<i class="fas fa-exclamation-triangle fa-lg"></i>
			<strong><?php echo __d('view_extension', 'Error %s: Page not found', $this->Html->tag('big', '404')); ?></strong>
		</p>
	</div>
	<h2 class="h2 header"><?php echo $message; ?></h2>
	<p>
		<strong><?php echo __d('cake', 'Error'); ?>: </strong>
		<samp>
<?php 
	printf(
		__d('cake', 'The requested address %s was not found on this server.'),
		"<strong>'{$url}'</strong>"
	); ?>
		</samp>
	</p>
	<hr />
<?php
if (Configure::read('debug') > 0):
?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('view_extension', 'Stack trace');?></div>
		<div class="panel-body"><?php echo $this->element('CakeTheme.exception_stack_trace'); ?></div>
	</div>
<?php endif; ?>
</div>