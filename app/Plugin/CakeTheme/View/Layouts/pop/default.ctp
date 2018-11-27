<?php
/**
 * This file is the layout file of view the plugin. Used for render
 *  popup window view.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts.pop
 */

	$this->loadHelper('Text');
?> 
<div id="content-popup">
<?php echo $this->Text->stripLinks($this->fetch('content')); ?>
</div>