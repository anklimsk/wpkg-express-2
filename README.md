# wpkg-express-2
[![Build Status](https://travis-ci.com/anklimsk/wpkg-express-2.svg?branch=master)](https://travis-ci.com/anklimsk/wpkg-express-2)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A web-based frontend to [WPKG](https://wpkg.org)

WPKG Express 2 based on [wpkgExpress](https://code.google.com/archive/p/wpkgexpress) by Brian White &copy;2009.

## Requirements

- Apache module `mod_rewrite`;
- PHP 5.4.0 or greater;
- GraphViz;
- smbclient.

## Installation

1. Install WPKG Express 2 using composer:
  `composer create-project anklimsk/wpkg-express-2 /path/to/wpkg --stability beta`.
2. Copy applicaton files from `/path/to/wpkg`
  to VirtualHost document root directory, e.g.: `/var/www/wpkg`.
3. Navigate to the directory `app` application (`/var/www/wpkg/app`),
  and run the following command: `sudo ./Console/cake CakeInstaller`
  to start interactive shell of installer.
4. After the installation process is complete, in your browser go to the link
  `http://wpkg.fabrikam.com/settings` to change settings of application,
  where `http://wpkg.fabrikam.com` - base URL of installited WPKG Express 2.
5. Fill in the fields in the `Authentication` group settings and click the `Save` button.

## Project icon

Part of: [WPKG logo was contributed by Eric Le Henaff](http://wpkg.org/wpkg.png)

## License

GNU GENERAL PUBLIC LICENSE Version 3
