<?php
/**
 * This file is the view file of the plugin. Used for render settings form.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Settings
 */

if (!isset($configUIlangs)) {
	$configUIlangs = false;
}

if (!isset($configExtAuth)) {
	$configExtAuth = false;
}

if (!isset($configAcLimit)) {
	$configAcLimit = false;
}

if (!isset($configADsearch)) {
	$configADsearch = false;
}

if (!isset($configSMTP)) {
	$configSMTP = false;
}

if (!isset($languages)) {
	$languages = [];
}

if (!isset($authGroups)) {
	$authGroups = [];
}

if (!isset($groupList)) {
	$groupList = [];
}

if (!isset($containerList)) {
	$containerList = [];
}

if (!isset($varsExt)) {
	$varsExt = [];
}

	$useExtendSettingsLeft = $this->elementExists('formExtendSettingsLeft');
	$useExtendSettingsRight = $this->elementExists('formExtendSettingsRight');

	echo $this->Form->create(
		'Setting',
		$this->ViewExtension->getFormOptions(
			[
				'role' => 'form',
				'data-toggle' => 'ajax-form',
				'fade-page' => true,
				'autocomplete' => 'off'
			]
		)
	);
?>
	<div class="settings-app-content tabbable">
<?php
if ($useExtendSettingsLeft || $useExtendSettingsRight) :
?>
		<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#mainSettings" aria-controls="mainSettings" role="tab" data-toggle="tab"><?php echo __d('cake_settings_app', 'Main settings'); ?></a></li>
		<li role="presentation"><a href="#extendSettings" aria-controls="extendSettings" role="tab" data-toggle="tab"><?php echo __d('cake_settings_app', 'Extended settings'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="mainSettings">
<?php
endif;
?>
				<div class="row top-buffer">
					<div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-1">
<?php
if ($configUIlangs) {
	echo $this->Form->inputs([
		'legend' => __d('cake_settings_app', 'Interface'),
		'Setting.Language' => ['label' => [__d('cake_settings_app', 'Language'),
			__d('cake_settings_app', 'Language of user interface'), ':'],
			'options' => $languages, 'type' => 'flag']
	]);
}
	$authInputs = [];
if ($configExtAuth) {
	$authInputs['Setting.ExternalAuth'] = ['label' => [__d('cake_settings_app', 'Use external authentication'),
		__d('cake_settings_app', 'Use external authentication (e.g. Kerberos)'), ':'],
		'type' => 'checkbox'];
}
foreach ($authGroups as $userRole => $authInfo) {
	if (!isset($authInfo['field']) || empty($authInfo['field'])) {
		continue;
	}

	$fieldName = $authInfo['field'];
	if (isset($authInfo['name'])) {
		$roleName = $authInfo['name'];
	} else {
		$roleName = __d('cake_settings_app', 'user with role %s', $userRole);
	}

	$authInputs['Setting.' . $fieldName] = ['label' => [__d('cake_settings_app', 'LDAP group member for access with role %s', $roleName),
		__d('cake_settings_app', 'LDAP group for access with role %s', $roleName), ':'],
		'type' => 'select', 'options' => $groupList];
}
if (!empty($authInputs)) {
	echo $this->Form->inputs(['legend' => __d('cake_settings_app', 'Authentication')] + $authInputs);
}
if ($configADsearch) {
	echo $this->Form->inputs([
		'legend' => __d('cake_settings_app', 'Search on LDAP server'),
		'Setting.Company' => ['label' => __d('cake_settings_app', 'Company name') . ':',
			'title' => __d('cake_settings_app', 'Company name for search employee on LDAP server'), 'type' => 'text',
			'data-toggle' => 'tooltip'],
		'Setting.SearchBase' => ['label' => __d('cake_settings_app', 'Search base') . ':',
			'title' => __d('cake_settings_app', 'Distinguished name of the search base object for search employee on LDAP server (e.g. CN=Users,DC=fabrikam,DC=com)'), 'type' => 'autocomplete',
			'local' => $containerList, 'min-length' => 1],
	]);
}
if ($configAcLimit) {
	echo $this->Form->inputs([
		'legend' => __d('cake_settings_app', 'Autocomplete'),
		'Setting.AutocompleteLimit' => ['label' => __d('cake_settings_app', 'Autocomplete limit') . ':', 'title' => __d('cake_settings_app', 'Limit for autocomplete search query'),
			'type' => 'spin', 'min' => 5,
			'max' => 100, 'step' => 1,
			'maxboostedstep' => 5, 'verticalbuttons' => true,
		],
	]);
}
?>
					</div>
					<div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-2">
<?php
if ($configSMTP) {
	echo $this->Form->inputs([
		'legend' => __d('cake_settings_app', 'Notification'),
		'Setting.EmailNotifyUser' => ['label' => [__d('cake_settings_app', 'Notify user via E-mail'),
			__d('cake_settings_app', 'Use user notification via E-mail'), ':'],
			'type' => 'checkbox']
	]);
}
	echo $this->Form->inputs([
		'legend' => __d('cake_settings_app', 'E-mail'),
		'Setting.EmailContact' => ['label' => __d('cake_settings_app', 'E-mail for contact') . ':', 'title' => __d('cake_settings_app', 'E-mail for the contact with the administrator'), 'type' => 'email',
			'data-toggle' => 'tooltip'],
		'Setting.EmailSubject' => ['label' => __d('cake_settings_app', 'Subject for E-mail') . ':', 'title' => __d('cake_settings_app', 'Subject for e-mail to contact the administrator'), 'type' => 'text',
			'data-toggle' => 'tooltip'],
	]);
	if ($configSMTP) {
		echo $this->Form->inputs([
			'legend' => __d('cake_settings_app', 'SMTP Server'),
			'Setting.EmailSmtphost' => ['label' => __d('cake_settings_app', 'SMTP Server') . ':', 'title' => __d('cake_settings_app', 'Address of the SMTP server.'),
				'data-toggle' => 'tooltip'],
			'Setting.EmailSmtpport' => ['label' => __d('cake_settings_app', 'Port') . ':', 'title' => __d('cake_settings_app', 'Port of the SMTP server.'),
				'type' => 'integer'],
			'Setting.EmailSmtpuser' => ['label' => __d('cake_settings_app', 'Username') . ':', 'title' => __d('cake_settings_app', 'Username for authentication on SMTP server.'),
				'type' => 'text', 'data-toggle' => 'tooltip'],
			'Setting.EmailSmtppassword' => ['label' => __d('cake_settings_app', 'Password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on'),
				'before' => '<input type="text" style="display:none"><input type="password" style="display:none">'],
			'Setting.EmailSmtppassword_confirm' => ['label' => __d('cake_settings_app', 'Confirm password') . ':', 'type' => 'password', 'data-content' => __d('cake_settings_app', 'Caps Lock on')]
		]);
	}
?>
					</div>
				</div>  
<?php
if ($useExtendSettingsLeft || $useExtendSettingsRight) :
?>			  
			</div>
			<div role="tabpanel" class="tab-pane" id="extendSettings">
				<div class="row top-buffer">
					<div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-1">
<?php
if ($useExtendSettingsLeft) {
	echo $this->element('formExtendSettingsLeft', compact('varsExt'));
}
?>
					</div>
					<div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-2">
<?php
if ($useExtendSettingsRight) {
	echo $this->element('formExtendSettingsRight', compact('varsExt'));
}
?>
					</div>
				</div>
			</div>
		</div>
<?php
endif;
?>
				<div class="row top-buffer">
					<div class="col-lg-12">
<?php
	echo $this->Form->submit(__d('cake_settings_app', 'Save'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
?>
					</div>
				</div>
	</div>
<?php
	echo $this->Form->end();
