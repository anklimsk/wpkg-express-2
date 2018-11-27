<?php
/**
 * This file is the view file of the plugin. Used for render login form.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	echo $this->Form->create('CakeLdap.User', ['role' => 'form']);
	echo $this->Form->inputs(
		[
			'legend' => false,
			'username' => ['label' => __d('cake_ldap', 'Username') . ':', 'title' => __d('cake_ldap', 'Username in user principal name format, e.g.: %s', 'user@fabrikam.com'), 'autocomplete' => 'off',
				'data-toggle' => 'tooltip', 'type' => 'text', 'autofocus' => true],
			'password' => ['label' => __d('cake_ldap', 'Password') . ':', 'autocomplete' => 'off',
				'type' => 'password', 'data-content' => __d('cake_ldap', 'Caps Lock on')]
		]
	);
	echo $this->Form->submit(__d('cake_ldap', 'Login'), ['class' => 'btn btn-success  btn-md', 'div' => 'form-group text-center']);
	echo $this->Form->end();
