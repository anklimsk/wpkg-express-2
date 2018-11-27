# Authenticating users by membership in the LDAP security group

## Using LDAP Authentication

1. Include `Auth` and `UserInfo` (not necessary) components in your `AppController`:

   ```php
   /**
    * Array containing the names of components this controller uses. Component names
    * should not contain the "Component" portion of the class name.
    *
    * @var array
    * @link http://book.cakephp.org/2.0/en/controllers/components.html
    */
   public $components = [
       'Auth',
       'CakeLdap.UserInfo',
   ];
   ```

2. Include `Setting` model in your `AppController` (not necessary):

   ```php
   /**
    * An array containing the class names of models this controller uses.
    *
    * @var mixed
    * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
    */
   public $uses = [
       'CakeSettingsApp.Setting',
   ];
   ```

2. Configure `Auth` component in method `beforeFilter()`, e.g.:

   ```php
   /**
    * Called before the controller action. You can use this method to configure and customize components
    * or perform logic that needs to happen before each controller action.
    *
    * Actions:
    *  - Configure components.
    *
    * @return void
    * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
    */
   public function beforeFilter() {
       $authGroups = [
           USER_ROLE_USER => 'default'
       ];
       $authGroupsList = $this->Setting->getAuthGroupsList();
       $authPrefixes = $this->Setting->getAuthPrefixesList();
       foreach ($authGroupsList as $userRole => $fieldName) {
           $userGroup = Configure::read(PROJECT_CONFIG_NAME . '.' . $fieldName);
           if (!empty($userGroup)) {
               $authGroups[$userRole] = $userGroup;
           }
       }

       $isExternalAuth = false;
       if ((bool)Configure::read(PROJECT_CONFIG_NAME . '.ExternalAuth') == true) {
           $isExternalAuth = $this->UserInfo->isExternalAuth();
       }

       $this->Auth->authenticate = [
           'CakeLdap.Ldap' => [
               // Flag of using external authentication
               'externalAuth' => $isExternalAuth,
               // List of user groups in format:
               //  key - bit mask of the user role
               //  value - security group name
               'groups' => $authGroups,
               // List of user role prefixes:
               //  key - bit mask of the user role
               //  value - prefix of user role
               'prefixes' => $authPrefixes,
               // List of LDAP fields for including in result
               'includeFields' => CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
               // List of LDAP fields for binding information with database information
               'bindFields' => [
                   CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID
               ]
               // Name of user model
               //'userModel' => 'CakeLdap.User'
           ]
       ];
       $this->Auth->authorize = ['Controller'];
       $this->Auth->flash = [
           'element' => 'warning',
           'key' => 'auth',
           'params' => []
       ];
       $this->Auth->loginAction = '/users/login';

       parent::beforeFilter();
   }
   ```

## Using `UserInfo` library

Create instance of `UserInfo` library:

   ```php
   App::uses('UserInfo', 'CakeLdap.Utility');

   $objUserInfo = new UserInfo();
   ```
### Getting value of field from user authentication information

Example:

```php
$objUserInfo->getUserField($field);
```
Where:
- `$field` - Field to retrieve`

### Checking user for compliance with roles

Example:

```php
if ($objUserInfo->checkUserRole($roles, $logicalOr, $userInfo)) {
    echo 'Allow';
}
```

Where:
- `$roles` - Bit mask of user role for checking or array of bit masks.
- `$logicalOr` If True, used logical OR for checking several bit masks.
  Used logical AND otherwise.
- `$userInfo` - Array of information about authenticated user

## Using `UserInfo` component

1. Include `UserInfo` component in your `AppController`:

   ```php
   public $components = [
       'CakeLdap.UserInfo'
       ...,
   ];
   ```

2. See `UserInfo` library:
   - [Getting value of field from user authentication information](#getting-value-of-field-from-user-authentication-information)
   - [Checking user for compliance with roles](#checking-user-for-compliance-with-roles)
3. Checking the requested controller action on the user's access by prefix role
   Example:

   ```php
   if ($this->UserInfo->isAuthorized($user)) {
       echo 'Authorized';
   }
   ```

   Where:
   - `$user` - Array of information about authenticated user
4. Checking the request is use external authentication (e.g. Kerberos)
   Example:

   ```php
   if ($this->UserInfo->isExternalAuth()) {
       echo 'The request uses external authentication';
   }
   ```

## Using `UserInfo` helper

1. Include `UserInfo` helper in your `AppController`:

   ```php
   public $helpers = [
       'CakeLdap.UserInfo'
       ...,
   ];
   ```

2. See `UserInfo` library:
   - [Getting value of field from user authentication information](#getting-value-of-field-from-user-authentication-information)
   - [Checking user for compliance with roles](#checking-user-for-compliance-with-roles)
