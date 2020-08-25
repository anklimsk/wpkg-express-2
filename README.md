# WPKG Express 2
[![Build Status](https://travis-ci.com/anklimsk/wpkg-express-2.svg?branch=master)](https://travis-ci.com/anklimsk/wpkg-express-2)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/wpkg-express-2/v/stable)](https://packagist.org/packages/anklimsk/wpkg-express-2)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A web-based frontend to [WPKG](https://wpkg.org) ([presentation](https://anklimsk.github.io/wpkg-express-2/presentation/))

WPKG Express 2 based on [wpkgExpress](https://code.google.com/archive/p/wpkgexpress)
by Brian White &copy;2009.

[WPKG](https://wpkg.org/WPKG_overview) is an automated software deployment, upgrade and removal program for Windows.

## This frontend to WPKG provides next features:

- User authentication by username and password and LDAP security group membership;
- Generates XML configuration files for WPKG:
  * `packages.xml` - Defines software packages (commands for WPKG to 
    install/uninstall programs, etc.);
  * `profiles.xml` - Specifies which packages will be installed/executed
    for each WPKG profile;
  * `hosts.xml` - Mappings between machine names and profile names;
  * `config.xml` - Configuration settings for runtime behavior of wpkg.js.
- Generates configuration files for [Windows Post-Install Wizard](https://msfn.org/board/topic/158274-windows-post-install-wizard-main-thread):
  * `config.js` - Configuration file for WPI;
  * `profiles.xml` - Specifies which packages will be installed/executed
    for each WPKG profile;
  * `hosts.xml` - Mappings between machine names and profile names;
  * `config.xml` - Configuration settings for runtime behavior of wpkg.js.
- Creates a package, profile and host based on a template;
- Creates a profile and host based on a template and list of computers from LDAP;
- Creates copy of a package, profile and host;
- Preview XML configuration files for WPKG with validating XML schema;
- Download and upload XML configuration files for WPKG with validating XML schema;
- Creates an XML configuration for WPKG using an editor with validating XML schema;
- Build a relationship graph for package, profile, and host;
- Build a relationship graph for host by name;
- Maintaining archive version of the package with the ability to switch to version;
- Disable unused profile and host based on list of computers from LDAP;
- Parsing WPKG log files with sending report to administrators E-mail;
- Parsing WPKG report and client database files;
- Viewing the version chart of the installed package;
- Recycle Bin with the ability to recover deleted data of package, profile or host.

## Requirements

- Apache module `mod_rewrite`;
- PHP 5.4.0 or greater (up to 7.4) (with extensions: `pdo ldap bz2 xml openssl`);
- [GraphViz](https://www.graphviz.org);
- smbclient
- java
- [composer](https://getcomposer.org/download/)
- A ldap server (Windows Server, samba, openLDAP, ...) for authentication
- A database server (MySQL/Postgres/Sqlite/SQLserver)

## Installation

1. Install WPKG Express 2 using composer:
  `composer create-project anklimsk/wpkg-express-2 /var/www/html/wpkg`.
2. If you are using OPcache you should set the [opcache.blacklist_filename](https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.blacklist-filename)
  configuration value with a file path to your blacklist (View cache):
  - For example, create a new file:
    `/etc/php5/apache2/opcache-blacklist.txt`;
  - Specify the path for excluding files, e.g.: `/var/www/wpkg/app/tmp/cache/views/wpkg_*.php`;
  - Add the blacklist file path to your `php.ini` file:
    `opcache.blacklist_filename=/etc/php5/apache2/opcache-blacklist.txt`;
  - Reload apache configuration: `sudo service apache2 reload`.
3. wpkg Express 2 uses rewrite with a .htaccess to handle the requests. In apache2 the .htaccess is disabled by default, enable it with:
```
        <Directory /var/www/html>
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
        </Directory>
```
4. Navigate to the directory `app` application (`/var/www/html/wpkg/app`),
  and run the following command: `sudo ./Console/cake CakeInstaller`
  to start interactive shell of installer.
  If you have no clue about ldap and want to use a samba server or windows server of authentication, use `cn=username,cn=Users,dc=domain,dc=de'` as login name and `cn=Users,dc=domain,dc=de` as basedn. See #16 for more information.
5. After the installation process is complete, in your browser go to the link
  `http://wpkg.fabrikam.com/wpkg/settings` to change settings of application,
  where `http://wpkg.fabrikam.com/wpkg` - base URL of installited WPKG Express 2.
6. Fill in the fields in the `Authentication` group settings and click the `Save` button.

## Using

[Using this frontend to WPKG](docs/using.md)

## Links

- [List of silent install, upgrade and uninstall configurations for many programs](https://wpkg.org/Category:Silent_Installers);
- [Running WPKG as a Group Policy Extension (forked from cleitet/wpkg-gp)](https://github.com/sonicnkt/wpkg-gp);
- [GUI for wpkg-gp](https://github.com/sonicnkt/wpkg-gp-client).

## Project icon

Part of: [WPKG logo was contributed by Eric Le Henaff](http://wpkg.org/wpkg.png)

## License

GNU GENERAL PUBLIC LICENSE Version 3
