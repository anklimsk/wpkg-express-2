# Creating a breadcrumbs navigation

1. In your `Model` add:
   - Include behavior `BreadCrumb`:

      ```php
      public $actsAs = [
          'CakeTheme.BreadCrumb',
          ...,
      ];
      ```

   - If needed, add functions:

      ```php
      /**
       * Return plugin name.
       *
       * @return string Return plugin name for breadcrumb.
       */
      public function getPluginName() {
          $pluginName = 'cake_ldap';

          return $pluginName;
      }

      /**
       * Return controller name.
       *
       * @return string Return controller name for breadcrumb.
       */
      public function getControllerName() {
          $controllerName = 'employees';

          return $controllerName;
      }

      /**
       * Return action name.
       *
       * @return string Return action name for breadcrumb.
       */
      public function getActionName() {
          $actionName = 'preview';

          return $actionName;
      }

      /**
       * Return name of group data.
       *
       * @return string Return name of group data
       */
      public function getGroupName() {
          $groupName = __('Employees');

          return $groupName;
      }

      /**
       * Return full name of data.
       *
       * @param int|string|array $id ID of record or array data
       *  for retrieving full name.
       * @return string|bool Return full name of data,
       *  or False on failure.
       */
      public function getFullName($id = null) {
          $name = $this->getName($id);
          if (empty($name)) {
              return false;
          }

          $result = __('Employee %s', $name);

          return $result;
      }
      ```

2. In your `Controller` add: 
   - Include the `ViewExtension` helper:

      ```php
      public $helpers = [
          ...,
          'CakeTheme.ViewExtension'
      ];
      ```

   - Inside your `Controller` action add variable for `View`, e.g.:

      ```php
      $breadCrumbs = $this->Model->getBreadcrumbInfo($id);
      $breadCrumbs[] = __('Viewing');

      $this->set(compact('breadCrumbs'));
      ```

3. In your `View` add:
   - Adds a link to the breadcrumbs array:

      ```php
      $this->ViewExtension->addBreadCrumbs($breadCrumbs);
      ```

      Where:
      * `$breadCrumbs` - array of information for creating a breadcrumbs.
