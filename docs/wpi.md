# Using Windows Post-Install Wizard

## Download WPI

- Download and extract [WPI](https://msfn.org/board/topic/175982-wpi-v872-v873-windows-10-support-modded).

# Preparing WPI to use

- Open WPKG Express 2 in web browser and navigate to menu `Windows Post-Install Wizard` ->
  `List of packages for WPI`;
- Download WPI configuration files. To do this in the page menu, click on the following items:
  * `Download WPI configuration file`;
  * `Download WPKG configuration file for WPI`;
  * `Profiles of WPKG for WPI`;
  * `Hosts of WPKG for WPI`.
- Download and extract [WPKG](https://wpkg.org/Download);
- Copy next files to `/path/to/WPI/Tools/Wpkg`:
  * `config.xml`;
  * `hosts.xml`;
  * `profiles.xml`;
  * `wpkg.js`.
- Copy main configuration file `config.js` to `/path/to/WPI/UserFiles`.
- Run `WPI.exe` and press `F2` to open the `Options Wizard`;
- Open the `Tools` tab and find groups `Execute before` and `Execute after`:
  * select the checkboxes `Script or program to execute before installation begins.`
    and `Script or program to execute after installation is complete.`;
  * insert the following line in the text inputs: `%ComSpec% /C del /F /Q "%TEMP%\wpkg-wpi.xml"`;
  * press the `Save` and `Exit` buttons.

## `WPI` directory structure:

The structure of the directory containing `WPI`

  ```text
  WPI.exe
  +-- Tools
  |   +-- Wpkg
  |       +-- config.xml
  |       +-- hosts.xml
  |       +-- profiles.xml
  |       +-- wpkg.js
  +-- UserFiles
      +-- config.js
  ```

# Adding packages to WPI

- Open WPKG Express 2 in web browser and navigate to menu `Windows Post-Install Wizard` ->
  `List of packages for WPI`;
- In the page menu, click on the menu item `Add package WPI`;
- Select a package and category from the list;
- If necessary, use the `Default` and `Forcibly` flags to install the default
  package or force the package to execute;
- Click the `Save` button.

# Adding categories to WPI

- Open WPKG Express 2 in web browser and navigate to menu `Windows Post-Install Wizard` ->
  `List of packages for WPI`;
- In the page menu, click on the menu item `Categories`;
- Click the `plus` button to add a new category;
- Enter the name of the new category and click the `Save` button.

# Viewing log files

- Change to the temporary directory, e.g.:
  `cd %TEMP%`;
- View log files by pattern `wpkg-wpi-%COMPUTERNAME%@*.log`.
