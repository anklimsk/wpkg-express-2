# Synchronizing information

# Synchronizing information from LDAP to database

1. Fill in the `Company` parameter in your configuration file to synchronize
   the records with the filled in attribute `company`, e.g.:

   ```php
   // Company name for synchronization with LDAP
   'Company' => 'Some company'
   ```

2. If necessary, specify the search base for synchronization by filling the `SearchBase` parameter, for example:

   ```php
   // The distinguished name of the search base object for searching employees in LDAP.
   'SearchBase' => 'dc=fabrikam,dc=com'
   ```

3. To start serving synchronized information, go to the link `/cake_ldap/employees` or simply `/users`
4. To start the synchronization, select the page menu and click on the menu item `Synchronize information with the LDAP server`

## Synchronizing information as a subordination tree of employees

1. Configure parameters `TreeSubordinate` in your configuration file, e.g.:

   ```php
   // Tree of subordinate employee
   'TreeSubordinate' => [
       'Enable' => true,
   ],
   ```

2. If you want to edit the positions of the employee subordination tree,
   configure parameters `TreeSubordinate` in your configuration file, e.g.:

   ```php
   // Tree of subordinate employee
   'TreeSubordinate' => [
       'Draggable' => true,
   ],
   ```

3. Uncomment parameter `CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER` in your configuration file, e.g.:

   ```php
   'LdapFields' => [
       ...,
       CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER => [
           'label' => __d('cake_ldap_field_name', 'Manager'),
           'altLabel' => __d('cake_ldap_field_name', 'Manag.'),
           'priority' => 16,
           'truncate' => true,
           'rules' => [],
           'default' => null
       ],
   ]
   ```

4. Fill in the `manager` attribute in the LDAP entries
5. To start serving subordination tree information, go to the link `/cake_ldap/employees/tree` or simply `/users/tree`
