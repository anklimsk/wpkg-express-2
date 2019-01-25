# Preparing of WPKG Express 2

## Creating a domain user for WPKG

- Create a domain user, for the `WpkgServer` service, e.g. `WpkgUser`.

## Creating a share to store client logs, databases, and reports

- Create a share to store:
  * Client logs, e.g. `\\fabrikam.com\system\wpkg\log`;
  * Client databases and reports, e.g. `\\fabrikam.com\system\wpkg\db`.
    If you are using reports from a client, create a `reports` subdirectory.
- Allow the user `WpkgUser` to write to this shares.

## Configuration WPKG Express 2 settings

- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Application settings`;
- Complete the settings and click the `Save` button.

## Configuration WPKG settings

- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Settings of WPKG`;
- In the page menu, click on the menu item `Edit settings`;
- Complete the settings and click the `Save` button.

## Configuration global variables

- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Global variables`;
- Edit the following variables:
  * `SOFTWARE` - Shared resource with distributives of programs available to
    the user `WpkgUser`;
  * `SOFTWARE_PUBLIC` - Shared resource with distributives of programs available
    to all users. Used, e.g., to install drivers.
  * `SOFTWARE_NETLOGON` - Shared resource with distributives of programs available
    to all user. Used for logon scripts.
