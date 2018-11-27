<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  table information of employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	$this->loadHelper('CakeTheme.Filter');

if (!isset($employees)) {
	$employees = [];
}

if (!isset($filterOptions)) {
	$filterOptions = [];
}

if (!isset($fieldsConfig)) {
	$fieldsConfig = [];
}

	$useExtendAction = false;
if ($this->elementExists('actionTableEmployee')) {
	$useExtendAction = true;
}
?>
	<div class="table-responsive table-filter">
<?php
if (!empty($filterOptions)) {
	echo $this->Filter->openFilterForm();
}

?>  
		<table class="table table-hover table-striped table-condensed">
			<thead>
<?php
if (!empty($filterOptions)) {
	echo $this->Filter->createFilterForm($filterOptions);
}
?>
			</thead>
			<tbody> 
<?php
foreach ($employees as $employee) {
	$action = '';
	$attrRow = [];
	if (isset($employee['Employee']['block']) && $employee['Employee']['block']) {
		$attrRow['class'] = 'danger';
	}
	if (isset($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID])) {
		$action = $this->ViewExtension->buttonLink(
			'fas fa-sync-alt',
			'btn-primary',
			['controller' => 'employees', 'action' => 'sync', $employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID]],
			[
				'title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server'),
				'data-toggle' => 'request-only'
			]
		);
	}
	if ($useExtendAction) {
		$action .= (!empty($action) ? '&nbsp;' : '') . $this->element('actionTableEmployee', compact('employee'));
	}
	$tableRow = $this->EmployeeInfo->getInfo($employee, $filterOptions, $fieldsConfig, [], true);
	$tableRow[] = [$action, ['class' => 'action text-center']];

	echo $this->Html->tableCells($tableRow, $attrRow, $attrRow);
}
?>
			</tbody>
		</table>
<?php
if (!empty($filterOptions)) {
	echo $this->Filter->closeFilterForm();
}
?>
	</div>
<?php
	echo $this->ViewExtension->buttonsPaging();
