# Retrieving list of specific `CSS` or `JS` files for action of controller

1. Include helper `'CakeTheme.ActionScript'` in your `AppController`:

   ```php
   public $helpers = [
       'CakeTheme.ActionScript'
       ...
   ];
   ```

2. In your `layout` file add next string for load `CSS` and `JS` files for action:

   ```php
   $this->ActionScript->css(['block' => 'css'], $uiLcid3);
   $this->ActionScript->script(['block' => 'script'], $uiLcid3);
   ```

3. Create folder in `app/webroot/css/specific/CONTROLLER_NAME/ACTION_NAME` or 
   `app/webroot/js/specific/CONTROLLER_NAME/ACTION_NAME` and place files for 
   current controller and current action.
4. For redefine specific file name for controller action define in controller method
   `specificCSS` or `specificJS` variables and specify needed files name as string or array, e.g.:

   ```php
   $specificCSS = 'some_action_name';
   $specificJS = ['another_action_name', 'SomeController' . DS . 'some_action'];
   ```

5. For load specific files for the selected version of the browser, create a folder, e.g.:
   `app/webroot/css/specific/CONTROLLER_NAME/ACTION_NAME/ie/8` and 
   place `CSS` files for current controller and current action.

   In `View` for this action add next string:

   ```php
   $this->append('specificCss', '<!--[if lte IE 8]>' . $this->Html->css($this->ActionScript->getFilesForAction('css', array('ie', 8), true)) . '<![endif]-->');
   ```

6. Add the ending `.min` to the compressed files, e.g. `index.min.css`.

7. Example of specific JS file:

   ```javascript
   /**
    * File for action Index of controller Employees
    *
    * @file    File for action Index of controller Employees
    * @version 0.1
    */

   /**
    * @version 0.1
    * @namespace AppActionScriptsEmployeesIndex
    */
   var AppActionScriptsEmployeesIndex = AppActionScriptsEmployeesIndex || {};

   (function ($) {
       'use strict';

       /**
        * This function used as callback for keyup event for
        *  add text to the search query input field if there
        *  is no focus.
        *
        * @param {object} e Event object
        *
        * @callback setFocusSearchInput
        *
        * @returns {null}
        */
       function _setFocusSearchInput(e)
       {
           var searchInput = $('#SearchQuery');
           if (searchInput.is(':focus') || ($('input:focus').length !== 0)) {
               return;
           }

           var keyCode = e.keyCode;
           if (e.ctrlKey || e.altKey
               || (keyCode < 0x20)
               || ((keyCode >= 0x21) && (keyCode <= 0x28))
               || ((keyCode >= 0x2C) && (keyCode <= 0x2E))
               || (keyCode === 0x5B) || (keyCode === 0x5d)
               || ((keyCode >= 0x70) && (keyCode <= 0x7B))
               || (keyCode === 0x90) || (keyCode === 0x91)
           ) {
               return;
           }

           searchInput.focus();
           var currText = searchInput.val();
           currText    += e.key;
           searchInput.val('');
           searchInput.val(currText);
       }

       /**
        * This function is used to bind keyup event for
        *  add text to the search query input field if there
        *  is no focus.
        *
        * @function updateSearchInput
        * @memberof AppActionScriptsEmployeesIndex
        *
        * @returns {null}
        */
       AppActionScriptsEmployeesIndex.updateSearchInput = function () {
           $(document).off('keyup.AppActionScriptsEmployeesIndex').on('keyup.AppActionScriptsEmployeesIndex', _setFocusSearchInput);
       };

       return AppActionScriptsEmployeesIndex;
   })(jQuery);

   /**
    * Registration handler of event `MainAppScripts:update`
    *
    * @function ready
    *
    * @returns {null}
    */
   $(
       function () {
           $(document).off('MainAppScripts:update.AppActionScriptsEmployeesIndex').on(
               'MainAppScripts:update.AppActionScriptsEmployeesIndex',
               function () {
                   AppActionScriptsEmployeesIndex.updateSearchInput();
               }
           );
       }
   );
   ```
