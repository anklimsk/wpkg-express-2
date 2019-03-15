# Preparing to create a WPKG report

## Create package

- Create a XML package file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <packages:packages xmlns:packages="http://www.wpkg.org/packages" xmlns:wpkg="http://www.wpkg.org/wpkg" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/packages">
    <package id="zzWPKG-REPORT" name="WPKG Report" revision="2018.12.05" priority="0" reboot="false" execute="changed">
      <!-- Notes: Create WPKG Report -->
      <variable name="Revision" value="2018.12.05"/>
      <variable name="RevI1" value="2018"/>
      <variable name="RevI2" value="12"/>
      <variable name="RevI3" value="05"/>
      <variable name="ProgramDir" value="%ProgramFiles%\Wpkg" architecture="x86"/>
      <variable name="ProgramDir" value="%ProgramW6432%\Wpkg" architecture="x64"/>
      <variable name="ReportPathLocal" value="%TEMP%\wpkg-report.log"/>
      <variable name="ReportPathNet" value="\\fabrikam.com\system\wpkg\base\reports\%COMPUTERNAME%.log"/>
      <variable name="WpkgScript" value="wpkg.js"/>
      <commands>
        <command type="install" include="cleanup"/>
        <command type="install" cmd="%ComSpec% /C cscript.exe &quot;%ProgramDir%\%WpkgScript%&quot; //NoLogo /query:iml /queryMode:local /quiet:false &gt; %ReportPathLocal%" timeout="60">
          <condition>
            <check type="file" condition="exists" path="%ProgramDir%\%WpkgScript%"/>
          </condition>
        </command>
        <command type="install" cmd="%ComSpec% /C copy /Y &quot;%ReportPathLocal%&quot; &quot;%ReportPathNet%&quot;" timeout="30">
          <condition>
            <check type="file" condition="exists" path="%ReportPathLocal%"/>
          </condition>
        </command>
        <command type="cleanup" cmd="%ComSpec% /C del /F /Q &quot;%ReportPathLocal%&quot;" timeout="30">
          <condition>
            <check type="file" condition="exists" path="%ReportPathLocal%"/>
          </condition>
        </command>
      </commands>
    </package>
  </packages:packages>
  ```

- Update package revision;
- Update the variables `Revision`, `RevI1`, `RevI2` and `RevI3`.

## Create profile

- Create a XML profile file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <profiles:profiles xmlns:profiles="http://www.wpkg.org/profiles" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/profiles">
    <profile id="WPKG_Common">
      <!-- Notes: WPKG common profile. -->
      <package package-id="zzWPKG-REPORT"/>
    </profile>
  </profiles:profiles>
  ```

## Create host

- Create a XML host file:

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <hosts:wpkg xmlns:hosts="http://www.wpkg.org/hosts" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.wpkg.org/hosts">
    <host name=".+" profile-id="WPKG_Common">
      <!-- Notes: WPKG common host. -->
    </host>
  </hosts:wpkg>
  ```

## Upload created XML files

- Open WPKG Express 2 in web browser and navigate to menu `Application settings` ->
  `Uploading XML`;
- Upload created XML file or simply navigate to menu `Application settings` ->
  `Creating XML`, paste this XML into the editor and click the `Create` button.
