# WPKG Express 2
[![Build Status](https://travis-ci.com/anklimsk/wpkg-express-2.svg?branch=master)](https://travis-ci.com/anklimsk/wpkg-express-2)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/wpkg-express-2/v/stable)](https://packagist.org/packages/anklimsk/wpkg-express-2)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A web-based frontend to [WPKG](https://wpkg.org)

WPKG Express 2 based on [wpkgExpress](https://code.google.com/archive/p/wpkgexpress)
by Brian White &copy;2009.

[WPKG](https://wpkg.org/WPKG_overview) is an automated software deployment, upgrade and removal program for Windows.

## WPKG Express 2 UI

![WPKG Express 2 UI](https://anklimsk.github.io/wpkg-express-2/img/slideshow.gif)

See the project [presentation](https://anklimsk.github.io/wpkg-express-2/presentation/).

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
- Creates and edit XML configuration for WPKG using an editor with validating XML schema and
  autocompletion based on XML schema;
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
- PHP `5.4` or greater (up to `7.4`);
- PHP Extensions: `pdo`, `ldap`, `bz2`, `xml` and `openssl`;
- Ldap server (`Active Directory`, `Samba` or `OpenLDAP`) to authenticate
  (only `Active Directory`) and get a list of computers to create `Profiles` and `Hosts`
  based on a template;
- Database server (`MySQL` or `Postgres`).

### Not necessary

- [Composer](https://getcomposer.org/download/) to install the application;
- [GraphViz](https://www.graphviz.org) to create dependency graph of `Packages`, `Profiles`
  and `Hosts`;
- [smbclient](https://www.samba.org/samba/docs/current/man-html/smbclient.1.html) to access
  log files and databases of client computers for parsing content;
- SMTP Server to send mail notifications to the administrator.

## Installation

1. Install WPKG Express 2 using composer:
  `composer create-project anklimsk/wpkg-express-2 /var/www/wpkg`,
  where `/var/www/wpkg` is Document Root directory.
  Or just download the [latest release](https://github.com/anklimsk/wpkg-express-2/releases/latest)
  from [releases](https://github.com/anklimsk/wpkg-express-2/releases) and extract
  the archive to the Document Root directory.
2. Set the [DocumentRoot](https://httpd.apache.org/docs/trunk/mod/core.html#documentroot)
  directive for the domain to [`/var/www/wpkg/app/webroot`](https://book.cakephp.org/2/en/installation.html#production).
3. Make sure that an `.htaccess` [override is allowed](https://book.cakephp.org/2/en/installation/url-rewriting.html#apache-and-mod-rewrite-and-htaccess)
  and that `AllowOverride` is set to `All` for the correct DocumentRoot. For users having apache
  2.4 and above, you need to modify the configuration file for your `httpd.conf`
  or virtual host configuration to look like the following:
  ```text
  DocumentRoot /var/www/wpkg/app/webroot
  
  <Directory /var/www/wpkg>
       Options FollowSymLinks
       AllowOverride All
       Require all granted
  </Directory>
  ```
4. If you are using OPcache you should set the [opcache.blacklist_filename](https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.blacklist-filename)
  configuration value with a file path to your blacklist (View cache):
  - For example, create a new file:
    `/etc/php/7.4/apache2/opcache-blacklist.txt`;
  - Specify the path for excluding files, e.g.: `/var/www/wpkg/app/tmp/cache/views/wpkg_*.php`;
  - Add the blacklist file path to your `php.ini` file:
    `opcache.blacklist_filename=/etc/php/7.4/apache2/opcache-blacklist.txt`;
  - Reload apache configuration: `sudo service apache2 reload`.
5. Navigate to the directory `app` application (`/var/www/wpkg/app`),
  and run the following command: `sudo ./Console/cake CakeInstaller`
  to start interactive shell of installer.
6. After the installation process is complete, in your browser go to the link
  `https://wpkg.fabrikam.com/settings` to change settings of application,
  where `https://wpkg.fabrikam.com` - base URL of installited WPKG Express 2.
7. Fill in the fields in the `Authentication` group settings (if required)
  and click the `Save` button.

## Update

[Update frontend to WPKG](update.md)

## Using

[Using this frontend to WPKG](using.md)

## Links

- [List of silent install, upgrade and uninstall configurations for many programs](https://wpkg.org/Category:Silent_Installers);
- [Running WPKG as a Group Policy Extension (forked from cleitet/wpkg-gp)](https://github.com/sonicnkt/wpkg-gp);
- [GUI for wpkg-gp](https://github.com/sonicnkt/wpkg-gp-client).

## Project icon

Part of: [WPKG logo was contributed by Eric Le Henaff](http://wpkg.org/wpkg.png)

## License

GNU GENERAL PUBLIC LICENSE Version 3
