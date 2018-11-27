# Updating libraries

1. Use `bower`, run commands:
   - Install packages:
     `cd app/Plugin/CakeTheme/Vendor/bower && bower install`
   - List local packages and possible updates:
     `cd app/Plugin/CakeTheme/Vendor/bower && bower list`
2. For installing library files use `bower-installer`:
   `cd app/Plugin/CakeTheme/Vendor/bower && bower-installer`
3. For update `JS` and `CSS` files use CakePHP console:
   - Clears all builds defined in the ini file:
     `Console/cake asset_compress clear`
   - Generate only build files defined in the ini file:
     `Console/cake asset_compress build_ini --force`
