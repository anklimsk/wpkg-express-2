# Creating main menu with search input

1. Copy `View` element file from `app/Plugin/CakeTheme/View/Elements/barNav.ctp.default` to `app/View/Elements/barNav.ctp`.
2. Edit element file [See `Example of navigation bar`](#example-of-navigation-bar)
3. Define next valiable in method `beforeFilter` your `AppController`, e.g.:

   ```php
   /**
    * Called before the controller action. You can use this method to configure and customize components
    * or perform logic that needs to happen before each controller action.
    *
    * Actions:
    *  - Set global variables for View.
    *
    * @return void
    * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
    */
   public function beforeFilter() {
       // If `false`, show the `Logout` menu item in the main menu in the navigation bar
       // (require plugin `anklimsk/cakephp-ldap-sync`)
       $isExternalAuth = false;

       // E-mail address and subject of letter for communication with the administrator
       $emailContact = '';
       $emailSubject = '';

       // If `true`, show search form in navigation bar (require plugin `cake-search-info`)
       $showSearchForm = true;

       // If `true`, show main menu in navigation bar
       $showMainMenu = true;

       // Name of project displayed in navigation bar
       $projectName = null;

       // The path to the project logo file is displayed in the navigation bar
       $projectLogo = null;

       $this->set(compact(
           'isExternalAuth',
           'emailContact',
           'emailSubject',
           'showSearchForm',
           'showMainMenu',
           'projectName',
           'projectLogo'
       ));
   }
   ```

## Creating menu items

### Creating top-level menu items

Example of creating top-level menu items:

```php
$iconList[] = [];
$iconList[] = $this->ViewExtension->menuItemLink($icon, $title, $url, $options, $badgeNumber);
$iconList[] = [$this->ViewExtension->menuItemLink($icon, $title, $url, $options, $badgeNumber) => $menuItems];
```

Where:
- `$icon` - class of icon
- `$title` - Title of menu label
- `$url` - Cake-relative URL or array of URL parameters, or external URL (starts with http://)
- `$options` - HTML options for button element
- `$badgeNumber` - The number for display to the right of the menu item
- `$menuItems` - Array of submenu items

### Creating submenu items

Example of creating submenu items:

```php
$menuItems = [
    $this->ViewExtension->menuActionLink($icon, $titleText, $url, $options),
    'divider'
];
```

Where:
- `$icon` - Class of icon
- `$titleText` - Title of menu label
- `$url` - Cake-relative URL or array of URL parameters, or external URL (starts with http://)
- `$options` HTML options for button element. List of values option `action-type`:
   * `confirm`: create link with confirmation action;
   * `confirm-post`: create link with confirmation action and `POST` request;
   * `post`: create link with POST request;
   * `modal`: create link with opening result in modal window.

## Example of navigation bar

```php
<?php
/**
 * This file is the view file of the application. Used for render
 *  navigation bar.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
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

if (!isset($showMainMenu)) {
    $showMainMenu = true;
}

if (!isset($projectName)) {
    $projectName = null;
    if (defined('PROJECT_NAME')) {
        $projectName = __d('project', PROJECT_NAME);
    }
}

$useUserInfo = false;
if (CakePlugin::loaded('CakeLdap')) {
    $useUserInfo = true;
}

$useNavbarContainerFluid = false;

if (!isset($projectLogo)) {
    $projectLogo = null;
    if (defined('PROJECT_LOGO_IMAGE_SMALL')) {
        $projectLogo = PROJECT_LOGO_IMAGE_SMALL;
    }
}

$iconList = [];

if (!$showMainMenu) {
    echo $this->element('CakeTheme.barNavBase', compact('showSearchForm', 'useNavbarContainerFluid', 'projectName', 'projectLogo', 'iconList'));

    return;
}

if (CakePlugin::loaded('CakeLdap')) {
    $menuItems = [
        $this->ViewExtension->menuActionLink(
            'fas fa-users fa-lg',
            __('Employees'),
            ['controller' => 'employees', 'action' => 'index', 'plugin' => 'cake_ldap', 'prefix' => false],
            ['title' => __('Manage information about employees')]
        ),
    ];

    if ($useUserInfo && $this->UserInfo->checkUserRole(USER_ROLE_ADMIN)) {
        $menuItems[] = 'divider';
        $menuItems[] = $this->ViewExtension->menuActionLink(
            'fas fa-sync-alt',
            __('Synchronizing information with AD'),
            ['controller' => 'employees', 'action' => 'sync', 'plugin' => 'cake_ldap', 'prefix' => false],
            ['title' => __('Synchronize information of employees with AD'),
                'data-toggle' => 'progress-sse',
                'data-task-type' => 'SyncEmployee']
        );
    }

    $iconList[] = [$this->ViewExtension->menuItemLink(
        'far fa-address-book fa-lg',
        __('Informations of employees'),
        null,
        ['class' => 'app-tour-main-menu-employees']
    ) => $menuItems];
}

if ($useUserInfo && $this->UserInfo->checkUserRole(USER_ROLE_ADMIN) && CakePlugin::loaded('CakeSettingsApp')) {
    $menuItems = [
        $this->ViewExtension->menuActionLink(
            'fas fa-cog',
            __('Application settings'),
            ['controller' => 'settings', 'action' => 'index', 'plugin' => 'cake_settings_app', 'prefix' => false],
            ['title' => __('Application settings')]
        )
    ];
    if (CakePlugin::loaded('Queue')) {
        $menuItems[] = $this->ViewExtension->menuActionLink(
            'fas fa-tasks',
            __('Queue of tasks'),
            ['controller' => 'queues', 'action' => 'index', 'plugin' => 'cake_settings_app', 'prefix' => false],
            ['title' => __('Task queue list')]
        );
    }
    $iconList[] = [$this->ViewExtension->menuItemLink(
        'fas fa-cogs fa-lg',
        __('Application settings'),
        null,
        ['class' => 'app-tour-main-menu-settings']
    ) => $menuItems];

}

if (!$isExternalAuth && CakePlugin::loaded('CakeLdap')) {
    $iconList[] = $this->ViewExtension->menuItemLink(
        'fas fa-sign-out-alt fa-lg',
        __('Logout'),
        ['controller' => 'users', 'action' => 'logout', 'plugin' => 'cake_ldap', 'prefix' => false]
    );
}

if ($this->request->here() === '/') {
    if (!empty($emailContact)) {
        $iconList[] = $this->ViewExtension->menuItemLink(
            'far fa-envelope fa-lg',
            __('Contact administrator'),
            sprintf('mailto:%s?subject=%s', h($emailContact), rawurlencode(h($emailSubject)))
        );
    }

    $iconList[] = $this->ViewExtension->menuItemLink(
        'fas fa-question fa-lg',
        __d('tour_app', 'Start the tour of the application'),
        '#',
        ['data-toggle' => 'start-app-tour']
    );
}

echo $this->element('CakeTheme.barNavBase', compact('showSearchForm', 'useNavbarContainerFluid', 'projectName', 'projectLogo', 'iconList'));
```
