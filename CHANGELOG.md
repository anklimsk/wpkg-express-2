# Changelog


## v1.0.5 (2019-03-15)

### New

* Added feature to creates an XML configuration for WPKG using an editor with validating XML schema.

### Changes

* Improved display of tabular information.

### Fix

* Fixed display of long information about packages.


## v1.0.4 (2019-03-07)

### Fix

* Fixed XML package template file `PACKAGE_TEMPLATE.xml`

* Fixed clearing notes when creating data from a template.

* Fixed downloading XML configuration files for WPI.


## v1.0.3 (2019-03-05)

### New

* Added feature of exit code directory.

* Added feature to preview logs of host.


## v1.0.2 (2019-02-08)

### Changes

* Added validation of SMB application settings.


## v1.0.1 (2019-02-04)

### Fix

* Fixed parsing of logs and reports files without settings.

* Replacing OpenSSL instead of outdated mcrpyt extension.


## v1.0.0 (2019-02-01)

### New

* Added feature to remove an item from the list of dependencies when viewing information.

### Fix

* Fixed confirmation of actions.

* Fixed the filter row checkbox in the WPI package table.


## v1.0.0-rc.1 (2019-01-29)

### New

* Added feature to view the version chart of the installed package.

* Added feature to parse report files containing the console output of the command `cscript.exe wpkg.js //NoLogo /query:iml`

### Changes

* Updated the feature to use WPI to quickly test WPKG packages.

### Fix

* Fixed the Controller name in the list of hosts containing the current profile as the main profile.

* Fixed sorting direction when paginating `Logs` data.


## v1.0.0-beta.4 (2018-12-28)

### New

* Added feature to change, import and export package attributes `precheck-*`

* Added feature changing installation date of package in profile.

### Changes

* Removed default value of package attributes `notify` and `execute` from XML.

* Added auto-complete computer name from LDAP to generate graph of this computer.

* Updated tooltip for form input items.

* Added parent change on drag and drop check item.

* Added input mask for the check condition of the `File date` form.

* Added a list of check values for the host: `OS`, `Architecture`, `Language ID` and `Language ID OS`

* Updated autocomplete for checks, variables, and package actions.

### Fix

* Fixed attribute `expandURL` for the package action `Download`

* Fixed display empty timeout of command.

* Fixed case sensitively for ID of package, profile and host.

* Fixed clearing value of group action in filter form.

* Fixed disabling browser cache on displaying XML configurations.

* Fixed clearing of the View files cache after saving application settings.

* Fixed XML configuration on disabled XML output.


## v1.0.0-beta.3 (2018-12-04)

### New

* Added feature to disable unused hosts and profiles.

### Changes

* Added translation for message type from task queue.

### Fix

* Fixed exporting package note for WPI.

* Fixed checking the ability to perform operations `delete` or `disable`

* Fixed internal authentication.


## v1.0.0-beta.2 (2018-11-30)

### Changes

* Added static text describing the valid type of XML file in the form of uploading XML files.

### Fix

* Fixed parsing logs for open files.

* Fixed importing XML for new records.

* Fixed clearing the cache when a transaction is rolled back when importing a package and client database.

* Fixed determination of the client database XML file for the `checkResults` element when importing XML.

* Fixed getting the ID of the last processed entry when importing XML and log files.


## v1.0.0-beta (2018-11-29)

### Fix

* Fixed deletion of related attributes along with global variables.


