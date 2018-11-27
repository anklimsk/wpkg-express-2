/**
 * @file Main file for layout Login
 * @version 0.1
 * @copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Registration handler of event `MainAppScripts:update`.
 *  Used for styling UI and trigger this event.
 *
 * @function ready
 * @returns  {null}
 */
    $(
        function () {
            $(document).off('MainAppScripts:update.LoginLayout').on(
                'MainAppScripts:update.LoginLayout',
                function () {
                    MainAppScripts.setUIReadyCounter();
                    MainAppScripts.setInputFocus();
                    MainAppScripts.updateBodyClass();
                    MainAppScripts.updateFontAwesome();
                    MainAppScripts.updateButtons();
                    MainAppScripts.updatePassField();
                    MainAppScripts.updateTooltips();
                    MainAppScripts.processUIReadyCounter();
                }
            );

            MainAppScripts.update();
        }
    );
