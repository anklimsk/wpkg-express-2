# Customization phonebook

## Change settings of LDAP fields

- Open configuration file `app/Config/cakeldap.php`
- Comment or delete unused LDAP fields;
- Customize using LDAP fields:
   * `label` - Full label of field (used e.g. in view);
   * `altLabel` - Short label of field (used e.g. in table);
   * `priority` - Priority (used as an order in the header of the table);
   * `truncate` - Truncate long text in a table;
   * `rules` - Validation rules (used when saving data);
   * `default` - Default vale (used when saving data);
   * `inputmask` - [Input mask](https://github.com/RobinHerbots/Inputmask);
   * `tooltip` - Tooltip of input form.
- After editing the file, select the menu item `Application settings` ->
  `Application settings` and click the `Save` button to clear the application cache.
- If you comment or delete unused LDAP fields, go to the OS console,
  navigate to the directory `app` application, and run the following commands:
  `sudo ./Console/cake CakeInstaller createdb` to update database.

## Change the logo of the organization on the title page PDF

Place your logo file in `app/webroot/img/project-logo.png` - size: 200x200 px,
  or remove this file.

## Change the order of departments in the exported files

- Select the menu item `Informations of departments` -> `Index of departments`.
- Use the buttons on the right side of the line to change the position
  of the department.

## Filter employees to sync with phonebook

Employees are filtered for synchronization with the phonebook according to the
  LDAP attribute value of the `company`, specified in the application settings.
  You can edit the value of this field through the phonebook until the company name
  is specified in the application settings.

## Changing the synchronization frequency with LDAP and updating exported files.

Configure scripts to run schedule, run the command `sudo crontab -u www-data -e` where
  `www-data` - user name for web server.
  Example of cron jobs:

   ```
   #
   # In this example, run the synchronizing script will be made every day on 7:10 AM
   10 7 * * * cd /var/www/paht_to_app/app && Console/cake CakeLdap.cron sync -q
   #
   # In this example, at 7:00 on Monday, the script for the PDF and Excel
   # files builder will be launched.
   0 7 * * mon cd /var/www/paht_to_app/app/ && Console/cake generate all all -q
   #
   # In this example, every 15 minutes, the script to check for new deferred saves
   # will be launched.
   */15 * * * * cd /var/www/paht_to_app/app/ && Console/cake cron deferred -q
   ```
