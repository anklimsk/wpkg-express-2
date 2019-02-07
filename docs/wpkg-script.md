# Preparing the WPKG script

## Preparing a WPKG configuration file

- Configuration [WPKG settings](wpkg-express.md#configuration-wpkg-express-2-settings);
- Configuration [global variables](wpkg-express.md#configuration-global-variables);
- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Settings of WPKG`;
- In the page menu, click on the menu item `Download XML file`;
- Copy the file `config.xml` to `%SOFTWARE_NETLOGON%\WPKG\Config\%Revision%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG configuration file, e.g.,
      the modification date: `2018.12.05`.
- Open the file `config.xml` and change the following parameters:
  * `settings_file_path` and `log_file_path` to `%SystemRoot%\\Temp`;
  * `settings_file_name` to `wpkg-gpo.xml`;
  * `logfilePattern` to `wpkg-gpo-[HOSTNAME]@[DD]-[MM]-[YYYY]-[hh]-[mm]-[ss].log`;
  * `logLevel` to `0x13`;
  * `profiles_path` to `profiles.xml`;
  * `hosts_path` to `hosts.xml`.
- Copy the file `config.xml` to `%SOFTWARE_NETLOGON%\WPKG\Script\%Revision%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG configuration file, e.g.,
      the modification date: `2018.12.05`.
- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-CONFIG" name="WPKG Config" revision="2018.12.05" priority="10000" reboot="false">
      <!-- Notes: WPKG configuration -->
      <variable name="ConfigFile" value="config.xml"/>
      <variable name="ProgramDir" value="%ProgramFiles%\Wpkg" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramW6432%\Wpkg" architecture="x64"/>
      <variable name="Revision" value="2018.12.05"/>
      <variable name="RevI1" value="2018"/>
      <variable name="RevI2" value="12"/>
      <variable name="RevI3" value="05"/>
      <check type="file" condition="datemodifynewerthan" path="%ProgramDir%\%ConfigFile%" value="%RevI1%-%RevI2%-%RevI3% 00:00:00"/>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="%COMSPEC% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Config\%Revision%\%ConfigFile%&quot; &quot;%ProgramDir%\&quot; /E /Q /H /R /Y" timeout="60"/>
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

## Preparing the `profiles.xml` and `hosts.xml` files

- Create a XML profile file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <profiles:profiles xmlns:profiles="http://www.wpkg.org/profiles" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/profiles">
    <profile id="WPKG_Common">
      <!-- Notes: WPKG common profile. -->
      <package package-id="WPKG-GP"/>
    </profile>
  </profiles:profiles>
  ```

- Save as `profiles.xml`;
- Create a XML host file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <hosts:wpkg xmlns:hosts="http://www.wpkg.org/hosts" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/hosts">
    <host name=".+" profile-id="WPKG_Common">
      <!-- Notes: WPKG common host. -->
    </host>
  </hosts:wpkg>
  ```

- Save as `hosts.xml`;
- Copy the files `profiles.xml` and `hosts.xml` to `%SOFTWARE_NETLOGON%\WPKG\Script\%Revision%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG script, e.g.: `1.3.1`.

## Preparing a WPKG script file

- Download and extract [WPKG](https://wpkg.org/Download);
- Copy the `config.xml` and `wpkg.js` files to `%SOFTWARE_NETLOGON%\WPKG\Script\%Revision%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG script, e.g.: `1.3.1`.
- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-SCRIPT" name="WPKG Script" revision="1.3.1" priority="10000" reboot="false">
      <!-- Notes: WPKG script -->
      <variable name="ProgramDir" value="%ProgramFiles%\Wpkg" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramW6432%\Wpkg" architecture="x64"/>
      <variable name="Revision" value="1.3.1"/>
      <variable name="RevI1" value="1"/>
      <variable name="RevI2" value="3"/>
      <variable name="RevI3" value="1"/>
      <variable name="WpkgModifyDate" value="2014-05-14"/>
      <variable name="WpkgScript" value="wpkg.js"/>
      <chain package-id="WPKG-CONFIG"/>
      <check type="file" condition="datemodifynewerthan" path="%ProgramDir%\%WpkgScript%" value="%WpkgModifyDate% 00:00:00"/>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="%COMSPEC% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Script\%Revision%\%WpkgScript%&quot; &quot;%ProgramDir%\&quot; /E /Q /H /R /Y" timeout="60"/>
        <command type="upgrade" include="install"/>
        <command type="downgrade" include="install"/>
        <command type="remove" cmd="%COMSPEC% /C del /F /Q &quot;%ProgramDir%\%WpkgScript%&quot;" timeout="30">
          <condition>
            <check type="file" condition="exists" path="%ProgramDir%\%WpkgScript%"/>
          </condition>
        </command>
      </commands>
    </package>
  </packages:packages>
  ```

- Check package revision;
- Check the variables `Revision`, `RevI1`, `RevI2`, `RevI3` and `WpkgModifyDate`;
- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Upload XML files`;
- Upload created XML file.

## `WPKG` directory structure:

The structure of the directory `WPKG` located in the %SOFTWARE_NETLOGON%

  ```text
  WPKG
  +-- Config
  |   +-- 2018.12.05
  |       +-- config.xml
  +-- Script
      +-- 1.3.1
          +-- config.xml
          +-- hosts.xml
          +-- profiles.xml
          +-- wpkg.js
  ```
