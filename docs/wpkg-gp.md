# Preparing the WPKG-GP

## Preparing Group Policies

- [Configure via Group Policies](https://github.com/cleitet/wpkg-gp/wiki/Installation-Guide#configure-via-group-policies);
- [How to disable asynchronous execution of GP](https://github.com/cleitet/wpkg-gp/wiki/Installation-Guide#how-to-disable-asynchronous-execution-of-gp);
- [How to enable verbose startup and shutdown messages](https://github.com/cleitet/wpkg-gp/wiki/Installation-Guide#how-to-enable-verbose-startup-and-shutdown-messages);
- Download [ADMX](https://github.com/sonicnkt/wpkg-gp/tree/master/src/admx) files;
- Copy the `ADMX` and `ADML` files to the folder `%SystemRoot%\PolicyDefinitions` folder
  on the machine performing the group policy object editing. If you're using a central store,
  copy the `ADMX` and `ADML` files to the folder `\\fabrikam.com\SYSVOL\fabrikam.com\Policies\PolicyDefinitions`.
- Add the user `WpkgUser` to the local administrators group:
  * Open Group Policy Management Console;
  * Navigate to path `Computer Configuration` -> `Policies` -> `Windows Settings` ->
    `Security Settings` -> `Restricted Groups`;
  * Right click and choose `Add Group` and enter `Administrators`;
  * Add user. Note that any users that are currently in the local administrators group
    will be removed and replaced with the users from current list.
- Allow the user `WpkgUser` log on as a service:
  * Open Group Policy Management Console;
  * Navigate to path `Computer Configuration` -> `Policies` -> `Windows Settings` ->
    `Security Settings` -> `Local Policies` -> `User Rights Assignment` -> `Log on as a service`;
  * Add user. Note that any users that are currently in the Log on as a service list
    will be removed and replaced with the users from current list.
- Check WPKG-GP configuration via Group Policies:

  ```text
  Computer Configuration
  +-- Policies
      +-- Windows Settings
          |   +-- Scripts (Logon/Logoff)
          |   |   +-- Logon
          |   |       +-- install_wpkg.cmd (see below)
          |   +-- Security Settings
          |       +-- Restricted Groups
          |       |   +-- BUILTIN\Administrators: FABRIKAM\WpkgUser
          |       +-- Local Policies
          |           +-- User Rights Assignment
          |               +-- Log on as a service: FABRIKAM\WpkgUser
          +-- Administrative templates
              +-- System
              |   +-- Group Policy
              |   |   +-- Startup policy processing wait time: Enabled
              |   |       +-- Amount of time to wait (in seconds): 30
              |   +-- Logon
              |       +-- Always wait for the network at computer startup and logon: Enabled
              +-- WPKG-GP
                  +-- Enable WPKG-GP and configure base settings: Enabled
                  |   +-- WPKG command: "%ProgramFiles%\Wpkg\wpkg.js"
                  |   +-- Disable WPKG-GP at boot-up: Disabled
                  +-- Miscellanous
                  |   +-- Enable activity indicator: Enabled
                  |   +-- Allow local users to execute WPKG-GP: Enabled
                  |   +-- Allow non-admins to execute WPKG-GP: Disabled
                  |   +-- Configure reboot options: Enabled
                  |   |   +-- Ignore reboot requests sent by wpkg.js: Disabled
                  |   |   +-- Limit number of reboots during single startup session: 3
                  |   +-- Set logging verbosity: Enabled
                  +-- Network
                      +-- Set number and timing of SMB connection attempts: Disabled
                      +-- Use specific credentials for network connections: Disabled
                      +-- Perform TCP test before SMB logon attempts: Enabled
                          +-- Host to test (IP or hostname): wpkg.fabrikam.com
                          +-- TCP port: 80
                          +-- Number of attempts: 5
  ```

## Preparing a WPKG-GP configuration file

- Create a WPKG-GP configuration file `Wpkg-GP.ini`:

  ```ini
  [WpkgConfig]
  # If you want Wpkg-GP to run from local group policies, e.g. execute
  # without you configuring anything on the servers, set this to 1
  # Default: 1
  # Alternatives: 0 | 1
  EnableViaLGP = 0
  
  # If you want the settings configured in this config file to override
  # any settings set through the Group Policy administrative template for
  # Wpkg-GP, and deployed through Group Policies, set this to 1
  IgnoreGroupPolicy = 0
  
  # Do not execute Wpkg-GP at bootup. Other methods of executing will still work.
  # Default: 0
  # Alternatives: 0 | 1
  DisableAtBootUp = 0
  
  # The path to your wpkg.js here
  # This setting is required
  WpkgCommand = "%ProgramFiles%\Wpkg\wpkg.js"
  
  # The log level (a value between 0 and 3)
  WpkgVerbosity = 1
  
  # The user name WPKG will use for connecting to the network
  # Default: Not set
  # Example: CONTOSO\InstallUser
  WpkgNetworkUsername = 
  
  # The password WPKG will use when connecting to the network
  # The service will automatically convert a cleartext password to
  # an encrypted one the first time it is starting.
  # The password is unique to the computer and the user the service
  # is running as, so the encrypted password cannot be transferred to
  # other computers.
  # Default: Not set
  # Example: clear:P@$$w0rd
  # Example: crypt:AQAAANCMnd8BFdERjHoAwE/Cl+sBAAAAsq/aNBh+HEi94fU5pxkb+gAAAAAoAAAARQB4AGUAYwB1AHQAZQBVAHMAZQByAFAAYQBzAHMAdwBvAHIAZAAAAANmAACoAAAAEAAAAJ3Jmb/7KPeQxclXo9RDypkAAAAABIAAAKAAAAAQAAAA+2T8G/OxrjIa+FBC1p68VAgAAAB7G5ApMTstrRQAAACiljCkFZ2zS5oqlnLzlhyN1/Biyw==
  # Note: If your password contains hashes (#'s), the entire string should
  #       be enclosed in double quotes to avoid it being interpreted as a
  #       inline comment.
  #       Example: WpkgNetworkPassword = "clear:P@$$#0rd"
  WpkgNetworkPassword =
  
  # The maximum number of consecutive reboots allowed before skipping
  # execution of Wpkg-GP
  WpkgMaxReboots = 3
  
  # Configure whether Wpkg-GP should initialize a reboot when Wpkg.js requests it, or not.
  # Alternatives: force | ignore
  # Default: force
  WpkgRebootPolicy = force
  
  # Configure whether users not in local administrators group
  # should be able to execute Wpkg-GP. Enabling this means that users
  # on other computers that is not a member of the local administrators
  # group on this computer can execute Wpkg-GP. Users that are a member
  # of the local Administrators group can always execute Wpkg-GP regardless
  # of this setting.
  # Alternatives: 1 | 0
  # Default: 0
  WpkgExecuteByNonAdmins = 0
  
  # Configure whether all local users on the computer should be able to execute Wpkg-GP.
  # This is necessary for the users to initiate installation of software themselves if
  # the setting WpkgExecuteByNonAdmins = 0
  # Alternatives: 1 | 0
  # Default: 1
  WpkgExecuteByLocalUsers = 1
  
  # Configure whether to show an activity indicator when Wpkg-GP is executing
  # Alternatives: 1 | 0
  # Default: 1
  WpkgActivityIndicator = 1
  
  # Configure if you want WPKG-GP to test the network connection with a
  # simple TCP-connect before trying to mount the share
  # This helps reduce boot stall on mobile clients as the timeout for a
  # tcp connect is set pretty short.
  TestConnectionHost = wpkg.fabrikam.com
  
  # Configure the port to connect to
  # Default: 445 (standard port for MS shares - alternative might be 139)
  TestConnectionPort = 80
  
  # Number of retries - increase if you have clients that connect slowly
  # Each try takes approx 2 seconds
  # Default: 5
  # TestConnectionTries = 5
  
  # Number in seconds WPKG-GP sleeps before retrying to test network connection again
  # Default: 2
  # TestConnectionSleepBeforeRetry = 2
  
  # Number of retries to mount share before giving up
  # Default: 7
  # ConnectionTries = 7
  
  # Number in seconds WPKG-GP sleeps before retrying to connect to share
  # Default: 5
  # ConnectionSleepBeforeRetry = 5
  
  [EnvironmentVariables]
  # Specify environment variables you want Wpkg to have here
  
  # Example: SOFTWARE = \\file001\install\software
  ```

- Copy the file `Wpkg-GP.ini` to `%SOFTWARE_NETLOGON%\WPKG\Client\GP\Config\%Revision%` and
  `%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common`
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG configuration file, e.g.,
      the modification date: `2018.12.05`.

- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-GP-CONFIG" name="WPKG-GP Config" revision="2018.12.05" priority="10000" reboot="false">
      <!-- Notes: WPKG-GP configuration -->
      <variable name="ConfigFile" value="Wpkg-GP.ini"/>
      <variable name="ProgramDir" value="%ProgramFiles%\Wpkg-GP" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramW6432%\Wpkg-GP" architecture="x64"/>
      <variable name="Revision" value="2018.12.05"/>
      <variable name="RevI1" value="2018"/>
      <variable name="RevI2" value="12"/>
      <variable name="RevI3" value="05"/>
      <check type="file" condition="datemodifynewerthan" path="%ProgramDir%\%ConfigFile%" value="%RevI1%-%RevI2%-%RevI3% 00:00:00"/>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="%COMSPEC% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP\Config\%Revision%\%ConfigFile%&quot; &quot;%ProgramDir%\&quot; /E /Q /H /R /Y" timeout="60"/>
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

## Preparing translation files for WPKG-GP

- [Translation and Customisation](https://github.com/sonicnkt/wpkg-gp-client/wiki/Translation-and-Customisation)
- Get the latest [wpkg-gp.pot](https://github.com/sonicnkt/wpkg-gp/blob/master/locale/wpkg-gp.pot)
  which contains all text parts which can be translated and open it up in the translation editor.
- Translate every string (or only the ones you want to customize), save it as *.po file for future
  modifications and finally generate a catalog *.mo file.
- Copy the `wpkg-gp.mo` file to `%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common\locale\%COUNTRYCODE%\LC_MESSAGES`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%COUNTRYCODE%` - Look at the [gnuttext manual](https://www.gnu.org/software/gettext/manual/html_node/Country-Codes.html#Country-Codes)
      for the 2 character code of your country. 4 character codes seperated by "_" can also be
      used (e.g. de_at for German - Austria or en_GB for English - Britain).

## Preparing the WPKG-GP service

- Download and install [AutoIt](https://www.autoitscript.com/site/autoit/downloads);
- Create a AutoIt script file `ConfigService.au3`:

  ```autoit
  Const $WPKG_DIR = 'Wpkg-GP'
  Const $WPKG_GP_SERVICE = 'WpkgServer.exe'
  Const $WPKG_USER = 'FABRIKAM\WpkgUser'
  Const $WPKG_PSWD = 'passw0rd'
  
  Func main()
     Local $sProgramFiles = '', $sWpkgPath = '', $sWpkgServicePath = ''
     If @OSArch = 'x86' Then
        $sProgramFiles = EnvGet('ProgramFiles')
     Else
        $sProgramFiles = EnvGet('ProgramW6432')
     EndIf
  
     $sWpkgPath = $sProgramFiles & '\' & $WPKG_DIR
     $sWpkgServicePath = $sWpkgPath & '\' & $WPKG_GP_SERVICE
  
     If Not FileExists($sWpkgServicePath) Then
        Exit(2)
     EndIf
  
     RunWait($sWpkgServicePath & ' --username ' & $WPKG_USER & ' --password ' & $WPKG_PSWD & ' update', $sWpkgPath, @SW_HIDE)
     If @error Then
        Exit(1)
     EndIf
  
     RunWait($sWpkgServicePath & ' restart', $sWpkgPath, @SW_HIDE)
     If @error Then
        Exit(3)
     EndIf
  
     Exit(0)
  EndFunc
  
  main()
  ```

- Edit constants `$WPKG_USER` and `$WPKG_PSWD`;
- Compile and build script;
- Copy the `ConfigService.exe` file to `%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`.

## Preparing a WPKG-GP

- Download [WPKG-GP](https://github.com/sonicnkt/wpkg-gp/releases) for all architecture;
- Copy the `Wpkg-GP-%Revision%_w7_%ARCH%.exe` files to `%SOFTWARE_NETLOGON%\WPKG\GP\%Revision%\%ARCH%`,
  where:
    * `%SOFTWARE_NETLOGON%` - global variable containing the path to the WPKG script,
      e.g.: `\\fabrikam.com\NETLOGON`;
    * `%Revision%` - current version of the WPKG-GP, e.g.: `0.17.17`;
    * `%ARCH%` - architecture `x86` or `x64`.
- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="WPKG-GP" name="WPKG-GP" revision="0.17.17" priority="10000" reboot="false">
      <!-- Notes: WPKG-GP. -->
      <variable name="ConfigFile" value="Wpkg-GP.ini"/>
      <variable name="Domain" value="fabrikam.com"/>
      <variable name="Revision" value="0.17.17"/>
      <variable name="RevI1" value="0"/>
      <variable name="RevI2" value="17"/>
      <variable name="RevI3" value="17"/>
      <variable name="Installer" value="Wpkg-GP-%Revision%_w7_%ARCH%.exe"/>
      <variable name="ProgramDir" value="%ProgramFiles%\Wpkg-GP" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramW6432%\Wpkg-GP" architecture="x64"/>
      <variable name="WpkgCommand" value="%ProgramFiles%\Wpkg\wpkg.js" architecture="x86"/>
      <variable name="WpkgCommand" value="%ProgramW6432%\Wpkg\wpkg.js" architecture="x64"/>
      <variable name="Uninstaller" value="uninstall.exe"/>
      <depends package-id="WPKG-SCRIPT"/>
      <chain package-id="WPKG-GP-Client"/>
      <chain package-id="WPKG-GP-CONFIG"/>
      <check type="logical" condition="or">
        <check type="logical" condition="not">
          <check type="host" condition="domainname" value="%Domain%"/>
        </check>
        <check type="uninstall" condition="exists" path="Wpkg-GP %Revision%.*"/>
      </check>
      <commands>
        <command type="install" include="remove"/>
        <command type="install" cmd="&quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP\%Revision%\%ARCH%\%Installer%&quot; /S /INI &quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common\%ConfigFile%&quot; /WpkgCommand &quot;%WpkgCommand%&quot; /D=%ProgramDir%" timeout="120">
          <exit code="0" reboot="postponed"/>
        </command>
        <command type="install" cmd="%ComSpec% /C xcopy &quot;%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common\locale&quot; &quot;%ProgramDir%\locale&quot; /E /Q /H /R /Y" timeout="30"/>
        <command type="install" cmd="%SOFTWARE_NETLOGON%\WPKG\Client\GP\Common\ConfigService.exe" timeout="60"/>
        <command type="upgrade" include="install"/>
        <command type="downgrade" include="install"/>
        <command type="remove" cmd="%ProgramDir%\%Uninstaller% /S _?=%ProgramDir%" timeout="120">
          <condition>
            <check type="file" condition="exists" path="%ProgramDir%\%Uninstaller%"/>
          </condition>
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

## Preparing install WPKG-GP

- Create a command file `install_wpkg.cmd`:

  ```batchfile
  @Echo off
  
  Set _SOFTWARE_NETLOGON=\\fabrikam.com\NETLOGON
  Set _WPKG_SCRIPT_PATH=%_SOFTWARE_NETLOGON%\WPKG\Script\Logon
  
  cscript.exe //NoLogo %_WPKG_SCRIPT_PATH%\wpkg.js /synchronize /quiet:true /nonotify:true /noreboot:true /sendStatus:false /noremove:true /norunningstate:true
  ```

- Check the variables `_SOFTWARE_NETLOGON` and `_WPKG_SCRIPT_REVISION`;
- Copy the `install_wpkg.cmd` file to logon script location, e.g.
  `\\fabrikam.com\SYSVOL\fabrikam.com\Policies\{GUID}\Machine\Scripts\Startup`:
  * Open Group Policy Management Console;
  * Navigate to path `Computer Configuration` -> `Policies` -> `Windows Settings` ->
    `Scripts (Logon/Logoff)` and double click to `Logon` in the right-hand pane;
  * In the `Logon` properties window, click `Show Files... `;
  * Paste the logon script `install_wpkg.cmd` and close the window;
  * In the `Logon` properties window, click `Add`;
  * In  the `Add a Script` window, click `Browse`, select script file,
    click `Open` and `OK` buttons.

## `WPKG` directory structure:

The structure of the directory `WPKG` located in the %SOFTWARE_NETLOGON%

  ```text
  WPKG
  +-- Client
      +-- GP
          +-- 0.17.17
          |   +-- x64
          |   |   +-- Wpkg-GP-0.17.17_w7_x64.exe
          |   +-- x86
          |       +-- Wpkg-GP-0.17.17_w7_x86.exe
          +-- Common
          |   +-- locale
          |   |   +-- ru
          |   |       +-- LC_MESSAGES
          |   |           +-- wpkg-gp.mo
          |   +-- ConfigService.exe
          |   +-- Wpkg-GP.ini
          +-- Config
              +-- 2018.12.05
                  +-- Wpkg-GP.ini
  ```

## Graph of the package `WPKG-GP`

![Graph of the package](https://anklimsk.github.io/wpkg-express-2/img/Graph-WPKG-GP.svg)
