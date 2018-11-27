<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of email as `default` style.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Layouts
 */

	$replace = [
		'</dt>' => " ",
		'</dd>' => "\n",
		'</th>' => " | ",
		'</td>' => " | ",
		'</tr>' => "\n",
		'<br />' => "\n",
		'<br/>' => "\n",
		'<br>' => "\n",
	];
	$this->startIfEmpty('footer');
	if ($this->elementExists('mailFooter')) {
		echo $this->element('mailFooter');
	} else {
		echo $this->element('CakeNotify.mailFooter');
	}
	$this->end();

	$content = $this->fetch('content');
	$footer = $this->fetch('footer');
	echo html_entity_decode(strip_tags($this->Text->stripLinks(strtr($content, $replace)))) . "\n";
	echo html_entity_decode(strip_tags($this->Text->stripLinks(strtr($footer, $replace))));
