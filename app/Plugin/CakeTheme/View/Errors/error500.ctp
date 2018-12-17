<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  error message 500.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @see ExportComponent::preview()
 * @package plugin.View.Elements
 */
?>
<div class="container">
	<div class="alert alert-danger top-buffer" role="alert">
		<p class="text-center">
			<i class="fas fa-exclamation-triangle fa-lg"></i>
			<strong><?php echo __d('view_extension', 'Error %s: Internal Server Error', $this->Html->tag('big', '500')); ?></strong>
		</p>
	</div>
	<h2 class="h2 header"><?php echo $message; ?></h2>
	<p>
		<strong><?php echo __d('cake', 'Error'); ?>: </strong>
		<samp><?php echo __d('cake', 'An Internal Error Has Occurred.'); ?></samp>
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