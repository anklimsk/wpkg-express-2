<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  short informations of employee (without photo and subordinate employees).
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

if (!isset($employee)) {
	$employee = [];
}

if (!isset($employee['Employee'])) {
	return;
}

	$this->loadHelper('CakeTheme.ViewExtension');
	$this->loadHelper('Text');
	$truncateOpt = [
		'ellipsis' => '...',
		'exact' => false,
		'html' => false
	];
	$employeeInfo = $this->ViewExtension->popupModalLink(
		h($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_NAME]),
		['controller' => 'employees', 'action' => 'view', $employee['Employee']['id']]
	);
	if (isset($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_TITLE]) && !empty($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_TITLE])) {
		$employeeInfo .= ' - ' . $this->Text->truncate(h($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_TITLE]), CAKE_LDAP_EMPLOYEE_ITEM_TEXT_MAX_LENGTH, $truncateOpt);
	}
	if (isset($employee['Employee']['block']) && $employee['Employee']['block']) {
		$employeeInfo = $this->Html->tag('s', $employeeInfo);
	}
	echo $employeeInfo;
