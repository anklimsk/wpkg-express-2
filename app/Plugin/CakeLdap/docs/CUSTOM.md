# Customization this plugin

- Edit config file and configure plugin [See `Example of configuration file`](EXAMPLE_CFG_FILE.md)
- Comment or delete unused LDAP fields;
- Customize using LDAP fields:
   * `label` - Full label of field (used e.g. in view);
   * `altLabel` - Short label of field (used e.g. in table);
   * `priority` - Priority (used as an order in the header of the table);
   * `truncate` - Truncate long text in a table;
   * `rules` - Validation rules (used when saving data);
   * `default` - Default vale (used when saving data).
 
## Customization binded information for models `Employee` and `Department`

1. Copy models files from `app/Plugin/CakeLdap/Model/Employee.php.default` to `app/Model/Employee.php` or
   `app/Plugin/CakeLdap/Model/Department.php.default` to `app/Model/Department.php`
2. Edit that files

## Customization displaying information of employees

### Using helper `EmployeeInfo`

1. Copy helper file from `app/Plugin/CakeLdap/View/Helper/EmployeeInfoHelper.php.default` to `app/View/Helper/EmployeeInfoHelper.php` or
2. Edit that files
3. If you set the type of data as `element`:
   - In the method `Employee::getExtendFieldsConfig` set the type as `element` for the `SomeModel.field` field, e.g.:

      ```php
      /**
       * Return fields configuration for helper
       *
       * @return array Return array of information about extended
       *  fields in format:
       *   [
       *      'modelName' => [
       *          'type' => 'string',
       *          'truncate' => false,
       *      ]
       *  ],
       *  where:
       *   - modelName - is name of model, or Hash::path.
       *   - type - type of data. Can be one of:
       *   integer, biginteger, float, date, time, datetime,
       *   timestamp, boolean, guid, photo, mail, string, text,
       *   binary, employee, manager, subordinate, department or
       *   element.
       *   - truncate - truncate text.
       */
      public function getExtendFieldsConfig() {
          $result = parent::getExtendFieldsConfig();
          $resultExtend = [
              'SomeModel' => [
                  'type' => 'element',
                  'truncate' => true,
              ]
          ];
          $result += $resultExtend;

          return $result;
      }
      ```

   - Copy `View` element file from `app/Plugin/CakeLdap/View/Elements/infoEmployeeExtendTemplate.ctp.default` to `app/View/Elements/infoEmployeeSomeModel.ctp`
   - Edit that file

### Other customization

- If need add action links in table `Employees`, copy view element file from
  `app/Plugin/CakeLdap/View/Elements/actionTableEmployee.ctp.default` to `app/View/Elements/actionTableEmployee.ctp`,
   and modify it
- If need change view of employees tree item, copy view element file from
  `app/Plugin/CakeLdap/View/Elements/treeItemEmployeeFull.ctp` to `app/View/Elements/`,
  and modify it
- If need change view of draggable employees tree item, copy view element file from
  `app/Plugin/CakeLdap/View/Elements/treeItemEmployeeFullDraggable.ctp` to `app/View/Elements/`,
  and modify it
