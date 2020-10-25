# Update frontend to WPKG

1. Make backup the following files:
- `app/Config/config.php`;
- `app/Config/core.php`;
- `app/Config/database.php`.
2. Download XML configuration file for WPKG (`config.xml`): in your browser go to the link
  `https://wpkg.fabrikam.com/admin/configs`,
  where `https://wpkg.fabrikam.com` - base URL of installited WPKG Express 2.
3. Delete the contents of the Document Root Directory (e.g. `/var/www/wpkg`).
4. Install the latest WPKG Express 2 using composer:
  `composer create-project anklimsk/wpkg-express-2 /var/www/wpkg`,
  where `/var/www/wpkg` is Document Root directory.
  Or just download the [latest release](https://github.com/anklimsk/wpkg-express-2/releases/latest)
  from [releases](https://github.com/anklimsk/wpkg-express-2/releases) and extract
  the archive to the Document Root directory.
5. Restore from backup files to path `/var/www/wpkg/app/Config`.
6. Navigate to the directory `app` application (`/var/www/wpkg/app`),
  and run the following command: `sudo ./Console/cake CakeInstaller install`
  for re-install frontend to WPKG.
7. Answer `Yes` to the request to re-create the application database schema.
8. Answer `No` to the request to delete database tables.
9. Upload saved XML configuration file for WPKG.
