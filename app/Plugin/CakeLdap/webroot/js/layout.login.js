/**
 * @file Main file for layout Login
 * @version 0.1
 * @copyright 2017-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Registration handler of event `MainAppScripts:update`.
 *  Used for styling UI and trigger this event.
 *
 * @function ready
 *
 * @returns {null}
 */
    $(
        function () {
            $(document).off('MainAppScripts:update.LoginLayout').on(
                'MainAppScripts:update.LoginLayout',
                function () {
                    MainAppScripts.setInputFocus();
                    MainAppScripts.updateButtons();
                    MainAppScripts.updateTooltips();
                    MainAppScripts.updatePassField();
                    MainAppScripts.processUIReadyCounter();
                }
            );

            MainAppScripts.update();
        }
    );
