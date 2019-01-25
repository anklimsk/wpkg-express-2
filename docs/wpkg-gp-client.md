# Preparing the WPKG-GP Client

## Preparing a WPKG-GP Client configuration file

- Create a WPKG-GP Client configuration file `wpkg-gp_client.ini`:

  ```ini
  ################################
  ## WPKG-GP Client CONFIG File ##
  ################################
  
  [General]
  # Inform user that wpkg wasn't run for a specific time, default is 14 days
  # Default: False
  # Alternatives: True | False
  check last update = True
  last update interval = 14
  # Add close button to tray
  # Default: False
  # Alternatives: True | False
  allow quit = False
  # Check LOG for errors from bootup wpkg-gp execution
  # Default: False
  # Alternatives: True | False
  check boot log = False
  # Check if VPN is connected (Only working with Cisco AnyConnect ATM installed in default folder)
  # Default: False
  # Alternatives: True | False
  check vpn = False
  # Specify the timeout in seconds after a which a shutdown/reboot will be initiated
  # Default: 30
  shutdown timeout = 30
  # Specify custom help file
  # Default: Default
  # Alternatives: Default | %RELATIVE_PATH_TO_HELPFILE_FROM_INSTALL_DIR%
  help file = Default
  
  [Update Check]
  # Select update check method
  # Default: False
  # Alternatives: wpkg-gp | updatefile | False
  method = wpkg-gp
  # Specify interval for automatic update checks in minutes, if False or 0 it wont check for updates automatically.
  interval = 30
  # URL to WPKG-GP Client update file, needs to be set for method = updatefile
  update url = https://YOUR_WEB.SERVER/packages.xml
  # Filter what pending tasks should be displayed (update, install, downgrade, remove), seperate by ";" without spaces.
  # Default: install;update;downgrade;remove
  # Example: install;update
  #          would only show new installs and updates
  filter = install;update
  # Blacklist what packages the user wont be informed about by package NAME, they will still install.
  # Entries can be only the first letters which will be compared to the packages, seperate by  ";" without spaces.
  # Default: Not Set
  # EXAMPLE: micro;fire
  #          would block the packages with the names Micosoft Office, Microsoft Visual Studio, Firefox , Firebird
  #          but not Mozilla Firefox
  blacklist = WPKG
  # Check after startup if updates are available.
  # Default: False
  # Alternatives: True | False
  start up = False
  ```

- Copy the file `wpkg-gp_client.ini` to `%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Config\%Revision%` and
  `%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Common`
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\netlogon`;
    * `%Revision%` - current version of the WPKG configuration file, e.g.,
      the modification date: `2018.12.05`.

- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-GP-Client-CONFIG" name="WPKG GP Client config" revision="2018.12.05" priority="10000" reboot="false">
      <!-- Notes: WPKG-GP Client configuration. -->
      <variable name="ConfigFile" value="wpkg-gp_client.ini"/>
      <variable name="ProgramDir" value="%ProgramFiles%\WPKG-GP-Client" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramFiles(x86)%\WPKG-GP-Client" architecture="x64"/>
      <variable name="Revision" value="2018.12.05"/>
      <variable name="RevI1" value="2018"/>
      <variable name="RevI2" value="12"/>
      <variable name="RevI3" value="05"/>
      <check type="file" condition="datemodifynewerthan" path="%ProgramDir%\%ConfigFile%" value="%RevI1%-%RevI2%-%RevI3% 00:00:00"/>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="%COMSPEC% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Config\%Revision%\%ConfigFile%&quot; &quot;%ProgramDir%\&quot; /E /Q /H /R /Y" timeout="60"/>
        <command type="upgrade" include="install"/>
        <command type="downgrade" include="install"/>
        <command type="remove" cmd="%COMSPEC% /C del /F /Q &quot;%ProgramDir%\%ConfigFile%&quot;" timeout="30">
          <condition>
            <check type="file" condition="exists" path="%ProgramDir%\%ConfigFile%"/>
          </condition>
        </command>
      </commands>
    </package>
  </packages:packages>
  ```

- Update package revision;
- Update the variables `Revision`, `RevI1`, `RevI2` and `RevI3`;
- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Upload XML files`;
- Upload created XML file.

## Preparing translation files for WPKG-GP Client

- [Translation and Customisation](https://github.com/sonicnkt/wpkg-gp-client/wiki/Translation-and-Customisation)
- Get the latest [WPKG-GP-Client.pot](https://github.com/sonicnkt/wpkg-gp-client/blob/master/WPKG-GP-Client.pot)
  which contains all text parts which can be translated and open it up in the translation editor.
- Translate every string (or only the ones you want to customize), save it as *.po file for future
  modifications and finally generate a catalog *.mo file.
- Copy the `wpkg-gp-client.mo` file to `%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Common\locale\%COUNTRYCODE%\LC_MESSAGES`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\netlogon`;
    * `%COUNTRYCODE%` - Look at the [gnuttext manual](https://www.gnu.org/software/gettext/manual/html_node/Country-Codes.html#Country-Codes)
      for the 2 character code of your country. 4 character codes seperated by "_" can also be
      used (e.g. de_at for German - Austria or en_GB for English - Britain).

## Preparing a WPKG-GP Client

- Download [WPKG-GP Client](https://github.com/sonicnkt/wpkg-gp-client/releases);
- Copy the `wpkg-gp-client_v%Revision%.exe` file to `%SOFTWARE_NETLOGON%\WPKG\GP-Client\%Revision%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\netlogon`;
    * `%Revision%` - current version of the WPKG-GP, e.g.: `0.9.7.4`.
- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-GP-Client" name="WPKG-GP Client" revision="0.9.7.4" priority="10000" reboot="false">
      <!-- Notes: WPKG-GP Client. -->
      <variable name="ConfigFile" value="wpkg-gp_client.ini"/>
      <variable name="DisplayName" value="WPKG-GP Client"/>
      <variable name="ProcessName" value="WPKG-GP-Client.exe"/>
      <variable name="ProgramDir" value="%ProgramFiles%\WPKG-GP-Client" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramFiles(x86)%\WPKG-GP-Client" architecture="x64"/>
      <variable name="Revision" value="0.9.7.4"/>
      <variable name="RevI1" value="0"/>
      <variable name="RevI2" value="9"/>
      <variable name="RevI3" value="7"/>
      <variable name="RevI4" value="4"/>
      <variable name="Installer" value="wpkg-gp-client_v%Revision%.exe"/>
      <variable name="Uninstaller" value="Uninstall.exe"/>
      <chain package-id="WPKG-GP-Client-CONFIG"/>
      <check type="uninstall" condition="versiongreaterorequal" path="%DisplayName%" value="%Revision%"/>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="&quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\%Revision%\%Installer%&quot; /SP- /VERYSILENT /SUPPRESSMSGBOXES /NORESTART /RESTARTEXITCODE=3010 /DIR=&quot;%ProgramDir%&quot; /LOG=&quot;%TEMP%\WPKG-GP-Client-install.log%&quot; /INI=&quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Common\%ConfigFile%&quot;" timeout="60">
          <exit code="3010" reboot="postponed"/>
        </command>
        <command type="install" cmd="%ComSpec% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP-Client\Common\locale&quot; &quot;%ProgramDir%\locale&quot; /E /Q /H /R /Y" timeout="30"/>
        <command type="upgrade" include="install"/>
        <command type="downgrade" include="install"/>
        <command type="remove" include="prepare"/>
        <command type="remove" cmd="&quot;%ProgramDir%\%Uninstaller%&quot; /VERYSILENT /SUPPRESSMSGBOXES /NORESTART" timeout="60">
          <condition>
            <check type="file" condition="exists" path="%ProgramDir%\%Uninstaller%"/>
          </condition>
        </command>
        <command type="prepare" cmd="%ComSpec% /C taskkill /F /IM &quot;%ProcessName%&quot;" timeout="30">
          <exit code="128" reboot="false"/>
        </command>
      </commands>
    </package>
  </packages:packages>
  ```

- Check package revision;
- Check the variables `Revision`, `RevI1`, `RevI2`, `RevI3` and `Domain`;
- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Upload XML files`;
- Upload created XML file.
- Upload the `WPKG-GP` package XML file to update the chain.

## `WPKG` directory structure:

The structure of the directory `WPKG` located in the %SOFTWARE_NETLOGON%

  ```text
  WPKG
  +-- Client
      +-- GP-Client
          +-- 0.9.7.4
          |   +-- wpkg-gp-client_v0.9.7.4.exe
          +-- Common
          |   +-- locale
          |   |   +-- ru
          |   |       +-- LC_MESSAGES
          |   |           +-- wpkg-gp-client.mo
          |   +-- wpkg-gp_client.ini
          +-- Config
              +-- 2018.12.05
                  +-- wpkg-gp_client.ini
  ```
