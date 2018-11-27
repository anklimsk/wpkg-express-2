<?php
/**
 * This file is the view file of the application. Used to render
 *  navigation bar.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016, Andrey Klimov.
 * @package plugin.View.Elements
 */

if (!isset($isExternalAuth)) {
	$isExternalAuth = false;
}

if (!isset($emailContact)) {
	$emailContact = '';
}

if (!isset($emailSubject)) {
	$emailSubject = '';
}

if (!isset($showSearchForm)) {
	$showSearchForm = true;
}

if (!isset($useNavbarContainerFluid)) {
	$useNavbarContainerFluid = $this->UserInfo->checkUserRole([USER_ROLE_ADMIN]);
}

if (!isset($showMainMenu)) {
	$showMainMenu = true;
}

if (!isset($projectName)) {
	$projectName = __d('project', PROJECT_NAME);
}

if (!isset($countLogErrors)) {
	$countLogErrors = 0;
}

$projectLogo = PROJECT_LOGO_IMAGE_SMALL;
$iconList = [];

if (!$showMainMenu) {
	echo $this->element('CakeTheme.barNavBase', compact('showSearchForm', 'useNavbarContainerFluid', 'projectName', 'projectLogo', 'iconList'));

	return;
}

$menuItems = [];
if ($this->UserInfo->checkUserRole(USER_ROLE_ADMIN)) {
	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('List of packages'),
			['controller' => 'packages', 'action' => 'index', 'plugin' => null],
			['title' => __('List of packages')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-plus',
			__('Add package'),
			['controller' => 'packages', 'action' => 'add', 'plugin' => null],
			['title' => __('Add new package'), 'data-toggle' => 'modal']
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-plus-square',
			__('Create from template'),
			['controller' => 'packages', 'action' => 'create', 'plugin' => null],
			['title' => __('Create package from template'), 'data-toggle' => 'modal']
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-archive',
			__('Archive'),
			['controller' => 'archives', 'action' => 'index', 'plugin' => null],
			['title' => __('Archive of packages')]
		),
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-terminal fa-lg',
		__('Packages'),
		null,
		['title' => __('Manage packages')]
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('List of profiles'),
			['controller' => 'profiles', 'action' => 'index', 'plugin' => null],
			['title' => __('List of profiles')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-plus',
			__('Add profile'),
			['controller' => 'profiles', 'action' => 'add', 'plugin' => null],
			['title' => __('Add new profile'), 'data-toggle' => 'modal']
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-plus-square',
			__('Create from template'),
			['controller' => 'profiles', 'action' => 'create', 'plugin' => null],
			['title' => __('Create profile from template'), 'data-toggle' => 'modal']
		)
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-list-ul fa-lg',
		__('Profiles'),
		null,
		['title' => __('Manage profiles')]
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('List of hosts'),
			['controller' => 'hosts', 'action' => 'index', 'plugin' => null],
			['title' => __('List of hosts')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-plus',
			__('Add host'),
			['controller' => 'hosts', 'action' => 'add', 'plugin' => null],
			['title' => __('Add new host'), 'data-toggle' => 'modal']
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-plus-square',
			__('Create from template'),
			['controller' => 'hosts', 'action' => 'create', 'plugin' => null],
			['title' => __('Create host from template'), 'data-toggle' => 'modal']
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-magic',
			__('Generate based on LDAP'),
			['controller' => 'hosts', 'action' => 'generate', 'plugin' => null],
			['title' => __('Generate from template based on LDAP'), 'data-toggle' => 'modal']
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-pencil-ruler',
			__('Build a graph'),
			['controller' => 'graph', 'action' => 'build'],
			['title' => __('Build a graph for host'), 'data-toggle' => 'modal',
			'data-modal-size' => 'lg']
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-clipboard-check',
			__('Verify state of list'),
			['controller' => 'hosts', 'action' => 'verify'],
			['title' => __('Verify state of list hosts'), 'data-toggle' => 'modal']
		)
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-desktop fa-lg',
		__('Hosts'),
		null,
		['title' => __('Manage hosts')]
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('Logs'),
			['controller' => 'logs', 'action' => 'index', 'plugin' => null],
			['title' => __('List of logs records')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-sync-alt',
			__('Refresh logs'),
			['controller' => 'logs', 'action' => 'parse'],
			['title' => __('Refresh logs'), 'data-toggle' => 'request-only']
		),
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'far fa-file-alt fa-lg',
		__('Logs'),
		null,
		[],
		$countLogErrors
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('Reports'),
			['controller' => 'reports', 'action' => 'index', 'plugin' => null],
			['title' => __('List of packages installation state')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-sync-alt',
			__('Refresh reports'),
			['controller' => 'reports', 'action' => 'parse'],
			['title' => __('Refresh reports'), 'data-toggle' => 'request-only']
		),
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-file-alt fa-lg',
		__('Reports'),
		null,
		[]
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-list-ol',
			__('List of packages for WPI'),
			['controller' => 'wpi', 'action' => 'index', 'plugin' => null],
			['title' => __('List of packages for WPI')]
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-list',
			__('Categories'),
			['controller' => 'wpi_categories', 'action' => 'index', 'plugin' => null],
			['title' => __('Categories of packages')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-file-download',
			__('Configuration of WPI'),
			['controller' => 'wpi', 'action' => 'download', 'config', 'ext' => 'js'],
			['title' => __('Download WPI configuration file')]
		)
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-globe-americas fa-lg',
		__('Windows Post-Install Wizard'),
		null,
		[]
	) => $menuItems];

	$menuItems = [
		$this->ViewExtension->menuActionLink(
			'fas fa-cog',
			__('Application settings'),
			['controller' => 'settings', 'action' => 'index', 'plugin' => 'cake_settings_app', 'prefix' => false],
			['title' => __('Application settings')]
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-file-code',
			__('Settings of WPKG'),
			['controller' => 'configs', 'action' => 'index', 'plugin' => null],
			['title' => __('Creating a WPKG configuration file')]
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-tasks',
			__('Queue of tasks'),
			['controller' => 'queues', 'action' => 'index', 'plugin' => 'cake_settings_app', 'prefix' => false],
			['title' => __('Task queue list')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-percentage',
			__('Global variables'),
			['controller' => 'variables', 'action' => 'global', 'plugin' => null],
			['title' => __('Variables of the global environment')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-file-upload',
			__('Uploading XML'),
			['controller' => 'uploads', 'action' => 'index', 'plugin' => null],
			['title' => __('Upload XML files')]
		),
		$this->ViewExtension->menuActionLink(
			'fas fa-file-download',
			__('Downloading XML'),
			['controller' => 'downloads', 'action' => 'index', 'plugin' => null],
			['title' => __('Download XML files')]
		),
		'divider',
		$this->ViewExtension->menuActionLink(
			'fas fa-trash-alt',
			__('Recycle bin'),
			['controller' => 'garbage', 'action' => 'index', 'plugin' => null],
			['title' => __('Manage removed data')]
		),
	];
	$iconList[] = [$this->ViewExtension->menuItemLink(
		'fas fa-cogs fa-lg',
		__('Application settings'),
		null,
		[]
	) => $menuItems];
}

if (!$isExternalAuth) {
	$iconList[] = $this->ViewExtension->menuItemLink(
		'fas fa-sign-out-alt fa-lg',
		__('Logout'),
		['controller' => 'users', 'action' => 'logout', 'plugin' => 'cake_ldap', 'prefix' => false]
	);
}

echo $this->element('CakeTheme.barNavBase', compact('showSearchForm', 'useNavbarContainerFluid', 'projectName', 'projectLogo', 'iconList'));
