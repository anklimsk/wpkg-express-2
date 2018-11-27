<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  list or tree of subordinate employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	$this->loadHelper('CakeTheme.ViewExtension');

if (!isset($data)) {
	$data = [];
}

	$result = $this->ViewExtension->showEmpty('');
if (empty($data)) {
	echo $result;

	return;
}

	$result = $this->element('CakeLdap.infoSubordinate', ['subordinate' => $data, 'draggable' => false]);
	echo $result;
