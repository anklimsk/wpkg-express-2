<?php
	echo $this->Html->tag('h2', 'Test header');
	echo $this->Html->para('lead', $this->Html->link('Test link', ['controller' => 'style_test', 'action' => 'index']));
