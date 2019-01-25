# Update frontend to WPKG

1. Make backup the following files:
- `app/Config/config.php`;
- `app/Config/core.php`;
- `app/Config/database.php`.
2. Download XML configuration file for WPKG.
3. Install new WPKG Express 2 using composer:
  `composer create-project anklimsk/wpkg-express-2 /path/to/wpkg --stability beta`.
4. Restore from backup files to path `/path/to/wpkg/app/Config`.
5. Navigate to the directory `app` application (/path/to/wpkg/app),
  and run the following command: `sudo ./Console/cake CakeInstaller install`
  for re-install frontend to WPKG.
6. Upload saved XML configuration file for WPKG.
