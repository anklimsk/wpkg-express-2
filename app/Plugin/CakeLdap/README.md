# CakePHP 2.x Synchronizing information and authenticating users by LDAP
[![Build Status](https://travis-ci.com/anklimsk/cakephp-ldap-sync.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-ldap-sync)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-ldap-sync/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-ldap-sync)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-ldap-sync/version)](https://packagist.org/packages/anklimsk/cakephp-ldap-sync)
[![License](https://poser.pugx.org/anklimsk/cakephp-ldap-sync/license)](https://packagist.org/packages/anklimsk/cakephp-ldap-sync)

Synchronizing information with LDAP and authenticating users by membership in the LDAP security group

## This plugin provides next features:

- Authenticating users by membership in the LDAP security group;
- Support external authentication, e.g. `kerberos`;
- Checking user for compliance with roles;
- Synchronizing information from LDAP to database;
- Synchronizing information as a subordination tree of employees;
- Support for a customizable list of fields for synchronization;
- Support for customize binded information for models `Employee` and `Department`;
- Support for customize displaying information of employees.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-ldap-sync`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeLdap', ['bootstrap' => true, 'routes' => true]);
   ```

3. Open file `app/Config/database.php` and add connection `ldap`, e.g.:

   ```php
       public $ldap = [
           'datasource' => 'CakeLdap.LdapExtSource',
           'persistent' => false,
           'host' => ['ldapsrv01', 'ldapsrv02'],
           'port' => 389,
           'login' => 'user@fabrikam.com',
           'password' => 'pas$w0rd',
           'database' => '',
           'basedn' => 'dc=fabrikam,dc=com',
           'type' => 'ActiveDirectory',
           'tls' => false,
           'version' => 3,
       ];
   ```

4. Copy configuration file from `app/Plugin/CakeLdap/Config/cakeldap.php` to `app/Config`.
5. Edit config file and configure plugin [See `Example of configuration file`](docs/EXAMPLE_CFG_FILE.md)
6. Create database tables of plugin using the CakePHP console, run the command:
   `Console/cake schema create -p CakeLdap`
7. In your file `app\Config\core.php` uncomment modify next line: `Configure::write('Routing.prefixes', array('admin'));`
8. Copy translation files from `app/Plugin/CakeLdap/Locale/rus/LC_MESSAGES/` to
   `app/Locale/rus/LC_MESSAGES`:
- `cake_ldap_field_name.*`;
- `cake_ldap_validation_errors.*`.
9. Get the name of the user that is running the web server, run the command:
   `ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`.
10. Configure scripts to run schedule, run the command `crontab -u www-data -e` where
   `www-data` - user name for web server.
11. Add the following line to the list of cron jobs:

   ```
   #
   # In this example, run the synchronizing script
   #  will be made every day on 7:10 AM 
   10 7 * * * cd /var/www/paht_to_app/app && Console/cake CakeLdap.cron sync -q
   ```

## Using

[Using this plugin](docs/USING.md)
