<?php
/**
 * This file is the view file of the plugin. Used for rendering
 *  full informations of employee.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

//$this->loadHelper('CakeLdap.EmployeeInfo');

if (!isset($employee)) {
	$employee = [];
}

if (!isset($fieldsLabel)) {
	$fieldsLabel = [];
}

if (!isset($fieldsLabelExtend)) {
	$fieldsLabelExtend = [];
}

if (!isset($fieldsConfig)) {
	$fieldsConfig = [];
}

if (!isset($linkOpt)) {
	$linkOpt = [];
}

if (!isset($id)) {
	$id = null;
}

	$isModal = $this->request->is('modal');
	$isPopup = $this->request->is('popup');

	$excludeFieldsDescription = [
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME,
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
		'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
	];

	if (empty($employee)) {
		return;
	}

	$rows = [];
	$fullName = $this->EmployeeInfo->getFullName($employee['Employee']);
	if (!empty($fullName) && isset($employee['Employee']['block']) && $employee['Employee']['block']) {
		$fullName = $this->Html->tag('s', $fullName);
	}

	$photo = null;
	if (isset($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO])) {
		$photo = $this->EmployeeInfo->getPhotoImage($employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO], $isPopup);
	}

	$fieldsLabel = array_diff_key($fieldsLabel, array_flip($excludeFieldsDescription));
	$employeeInfo = $this->EmployeeInfo->getInfo($employee, $fieldsLabel, $fieldsConfig, $linkOpt, false);
	$employeeInfoExtend = $this->EmployeeInfo->getInfo($employee, $fieldsLabelExtend, $fieldsConfig, $linkOpt, false);
	$employeeInfo = array_merge($employeeInfo, $employeeInfoExtend);
	$descriptionList = '';
	$emptyText = $this->EmployeeInfo->getEmptyText();
	foreach ($employeeInfo as $label => $info) {
		if ($isPopup && ($emptyText === $info)) {
			continue;
		}
		$descriptionList .= $this->Html->tag('dt', $label . ':');
		$descriptionList .= $this->Html->tag('dd', $info);
	}

	if (empty($fullName) && empty($photo) && empty($descriptionList)) {
		return;
	}

	if (!empty($fullName)) :
?>
<div class="row">
	<div class="col-md-12"><?php echo $this->Html->div('text-center', $this->Html->tag('h3', $fullName)); ?></div>
</div>
<?php
	endif;
	$photoColClass = 'col-xs-12 col-sm-4 col-md-3 col-lg-offset-1 col-lg-3';
	$infoColClass = 'col-xs-12 col-sm-8 col-md-9 col-lg-8';
	if ($isModal || $isPopup || empty($photo)) {
		$photoColClass = 'col-md-12';
		$infoColClass = 'col-md-12';
	}
?>
<div class="row">
<?php
if (!empty($photo)) :
?>
<div class="<?php echo $photoColClass; ?>"><?php echo $this->Html->div('text-center', $photo); ?></div>
<?php
endif;
if (!empty($descriptionList)) :
?>
<div class="<?php echo $infoColClass; ?>"><?php echo $this->Html->tag('dl', $descriptionList, ['class' => 'dl-horizontal']); ?></div>
<?php
endif;
?>
</div>
