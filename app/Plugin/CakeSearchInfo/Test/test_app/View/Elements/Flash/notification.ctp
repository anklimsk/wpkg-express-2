<?php
/**
 * This file is the view file of the application. Used for render
 *  Flash message.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */
?>
<div id="<?php echo $key; ?>Message" class="alert alert-info alert-dismissible<?php echo !empty($params['class']) ? ' ' . $params['class'] : ''; ?>" role="alert">
	<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
	<span class="sr-only">Notification:</span>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>	
<?php echo '&nbsp;' . $message; ?>
</div>
