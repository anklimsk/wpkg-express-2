<?php

$config['Queue'] = [
	'sleeptime' => 10,
	'gcprop' => 10,
	'defaultworkertimeout' => 120,
	'defaultworkerretries' => 2,
	'workermaxruntime' => 600,
	'cleanuptimeout' => 2000,
	'exitwhennothingtodo' => false,
	'pidfilepath' => TMP . 'queue' . DS,
	'log' => false,
	'notify' => false // Set to false to disable (tmp = file in TMP dir)
];
