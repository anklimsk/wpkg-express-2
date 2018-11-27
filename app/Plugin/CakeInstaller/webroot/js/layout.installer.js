/**
 * @file Main file for layout Installer
 * @version 0.1
 * @copyright 2016 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Bind Twitter Bootstrap Tooltips.
 *
 * @function ready
 *
 * @returns {null}
 */
    $(
        function () {
            $('[data-toggle="tooltip"]').tooltip(
                {
                    container: '#content',
                    placement: 'auto',
                    html: true
                }
            );
        }
    );
