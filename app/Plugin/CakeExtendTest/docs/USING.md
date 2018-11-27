# Using this plugin

## Baking test case use cake console

1. Copy files:
   - from `app/Plugin/CakeExtendTest/Test/AppCakeTestCase.php` to `app/Test`
   - from `app/Plugin/CakeExtendTest/Test/AppControllerTestCase.php` to `app/Test`
   - from `app/Plugin/CakeExtendTest/Test/AllTestsTest.php` to `app/Test/Case`
2. If necessary, edit the `AppCake TestCase.php` and `AppController TestCase.php` files.
3. For baking test case use cake console command `cake CakeExtendTest.bake`. Analog 
    of command `cake bake test`.

## Testing a non-public method or property

1. Create a new object for testing:

   ```php
   $proxy = $this->createProxyObject($target);
   $proxy->someProtectedMethod();
   ```

   where: 
   - `$target` - target object;
   - `$proxy` - new object for testing.

## Testing the View with a CSS Selector

1. Create View test on controller test:

   ```php
   $opt = [
       'method' => 'GET',
       'return' => 'contents',
   ];
   $url = 'some_controller/some_action/some_param';
   $view = $this->testAction($url, $opt);
   $numTableRows = $this->getNumberItemsByCssSelector($view, 'div#content div.container table > tbody > tr');
   $expected = 2;
   $this->assertData($expected, $numTableRows);
   ```

## Applying testing configuration of application from file

1. Copy configuration file from `app/Plugin/CakeExtendTest/Test/TestConfig.php` to `app/Test`.
2. Edit config file and configure application for testing, e.g.:

   ```php
   $config['TestKey'] = ['SomeKey' => 'Some data...'];
   ```

## Applying testing information of logged-on user from array

1. Copy file from `app/Plugin/CakeExtendTest/Test/AppTestTrait.php` to `app/Test`.
2. Edit file `AppTestTrait.php`. e.g.:

   ```php
   /**
    * Information about the logged in user.
    *
    * @var array
    */
   protected $userInfo = [
       'user' => 'Хвощинский В.В.',
       'role' => USER_ROLE_USER,
       'prefix' => '',
       'id' => '7',
       'includedFields' => [
           CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '8c149661-7215-47de-b40e-35320a1ea508'
       ]
   ];
   ```

3. Add call `setDefaultUserInfo()` in method `setUp()` of test:

   ```php
   /**
    * setUp method
    *
    * @return void
    */
   public function setUp() {
       $this->setDefaultUserInfo($this->userInfo);
       parent::setUp();
       ....
   }
   ```

4. For change user role on fly:

   ```php
   $userInfo = [
       'role' => USER_ROLE_USER | USER_ROLE_ADMIN,
       'prefix' => 'admin',
   ];
   $this->applyUserInfo($userInfo);
   ```

5. Replace in files `AppCakeTestCase.php` and `AppControllerTestCase.php`:
   `App::uses('AppTestTrait', 'CakeExtendTest.Test');` to `App::uses('AppTestTrait', 'Test');`

## Testing Flash messages

1. In controller test method add:

   ```php
   $this->testAction('/some_controller/some_action', $opt);
   $this->checkFlashMessage('Action completed successfully');
   ```

## Testing method with arguments assertions and messages from array

1. In test method add:

```php
   $params = [
       [
           null, // $id
       ], // Params for step 1
       [
           100, // $id
       ], // Params for step 2
       [
           2, // $id
       ], // Params for step 3
   ];
   $expected = [
       false, // Result of step 1
       [], // Result of step 2
       [
           'some data'
       ], // Result of step 3
   ];
   $this->runClassMethodGroup('methodName', $params, $expected);
   ```
