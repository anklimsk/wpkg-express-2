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

## Exit code directory

- See [Win32 Error Codes](https://docs.microsoft.com/en-us/openspecs/windows_protocols/ms-erref/18d8fbe8-a967-4f1c-ae50-99ca8e491d2d);
- Create a XML file with record for the exit code directory:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <directory xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <record code="3010">
      <hexadecimal>0x00000BC2</hexadecimal>
      <constant>ERROR_SUCCESS_REBOOT_REQUIRED</constant>
      <description>The requested operation is successful. Changes will not be effective until the system is rebooted.</description>
    </record>
  </directory>
  ```

- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Uploading XML`;
- Upload created XML file or simply navigate to menu `Application settings` ->
  `Creating XML`, paste this XML into the editor and click the `Create` button.
