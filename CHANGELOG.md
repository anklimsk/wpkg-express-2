# Changelog


## v1.0.0-rc.1 (2019-01-29)

### New

* Added feature to view the version chart of the installed package. [Andrey Klimov]

* Added feature to parse report files containing the console output of the command `cscript.exe wpkg.js //NoLogo /query:iml` [Andrey Klimov]

### Changes

* Updated the feature to use WPI to quickly test WPKG packages. [Andrey Klimov]

### Fix

* Fixed the Controller name in the list of hosts containing the current profile as the main profile. [Andrey Klimov]

* Fixed sorting direction when paginating `Logs` data. [Andrey Klimov]


## v1.0.0-beta.4 (2018-12-28)

### New

* Added feature to change, import and export package attributes `precheck-*` [Andrey Klimov]

* Added feature changing installation date of package in profile. [Andrey Klimov]

### Changes

* Removed default value of package attributes `notify` and `execute` from XML. [Andrey Klimov]

* Added auto-complete computer name from LDAP to generate graph of this computer. [Andrey Klimov]

* Updated tooltip for form input items. [Andrey Klimov]

* Added parent change on drag and drop check item. [Andrey Klimov]

* Added input mask for the check condition of the `File date` form. [Andrey Klimov]

* Added a list of check values for the host: `OS`, `Architecture`, `Language ID` and `Language ID OS` [Andrey Klimov]

* Updated autocomplete for checks, variables, and package actions. [Andrey Klimov]

### Fix

* Fixed attribute `expandURL` for the package action `Download` [Andrey Klimov]

* Fixed display empty timeout of command. [Andrey Klimov]

* Fixed case sensitively for ID of package, profile and host. [Andrey Klimov]

* Fixed clearing value of group action in filter form. [Andrey Klimov]

* Fixed disabling browser cache on displaying XML configurations. [Andrey Klimov]

* Fixed clearing of the View files cache after saving application settings. [Andrey Klimov]

* Fixed XML configuration on disabled XML output. [Andrey Klimov]


## v1.0.0-beta.3 (2018-12-04)

### New

* Added feature to disable unused hosts and profiles. [Andrey Klimov]

### Changes

* Added translation for message type from task queue. [Andrey Klimov]

### Fix

* Fixed exporting package note for WPI. [Andrey Klimov]

* Fixed checking the ability to perform operations `delete` or `disable` [Andrey Klimov]

* Fixed internal authentication. [Andrey Klimov]


## v1.0.0-beta.2 (2018-11-30)

### Changes

* Added static text describing the valid type of XML file in the form of uploading XML files. [Andrey Klimov]

### Fix

* Fixed parsing logs for open files. [Andrey Klimov]

* Fixed importing XML for new records. [Andrey Klimov]

* Fixed clearing the cache when a transaction is rolled back when importing a package and client database. [Andrey Klimov]

* Fixed determination of the client database XML file for the `checkResults` element when importing XML. [Andrey Klimov]

* Fixed getting the ID of the last processed entry when importing XML and log files. [Andrey Klimov]


## v1.0.0-beta (2018-11-29)

### Fix

* Fixed deletion of related attributes along with global variables. [Andrey Klimov]


