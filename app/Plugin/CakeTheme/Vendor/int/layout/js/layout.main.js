/**
 * @file Main file for layout Main
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
            $(document).off('MainAppScripts:update.MainLayout').on(
                'MainAppScripts:update.MainLayout',
                function () {
                    MainAppScripts.setUIReadyCounter();
                    MainAppScripts.setInputFocus();
                    MainAppScripts.setTextareaLinesLimit();
                    MainAppScripts.updateBodyClass();
                    MainAppScripts.updateFontAwesome();
                    MainAppScripts.updateNotUsedLinks();
                    MainAppScripts.updateLightboxLinks();
                    MainAppScripts.updateModalLinks();
                    MainAppScripts.updateAjaxLinks();
                    MainAppScripts.updatePjaxLinks();
                    MainAppScripts.updatePrintLinks();
                    MainAppScripts.updateLoadMoreLinks();
                    MainAppScripts.updateMoveLinks();
                    MainAppScripts.updateRequestOnlyLinks();
                    MainAppScripts.updatePostLinks();
                    MainAppScripts.updateConfirmActionLinks();
                    MainAppScripts.updateAjaxForm();
                    MainAppScripts.updatePjaxForm();
                    MainAppScripts.updateTimeago();
                    MainAppScripts.updateCheckbox();
                    MainAppScripts.updateTreeview();
                    MainAppScripts.updateBonsaiTreeview();
                    MainAppScripts.updateSelect();
                    MainAppScripts.updateButtons();
                    MainAppScripts.updateDropdowns();
                    MainAppScripts.updateTabs();
                    MainAppScripts.updateDatePicker();
                    MainAppScripts.updateFileUpload();
                    MainAppScripts.updateSpin();
                    MainAppScripts.updatePassField();
                    MainAppScripts.updateInputMask();
                    MainAppScripts.updateFlagSelect();
                    MainAppScripts.updateCloneDOMelements();
                    MainAppScripts.updateAutocomplete();
                    MainAppScripts.updateTextcomplete();
                    MainAppScripts.updatePopover();
                    MainAppScripts.updateExpandTruncatedText();
                    MainAppScripts.updateSortable();
                    MainAppScripts.updateProgressSSEtasks();
                    MainAppScripts.updateInputsFilledProgressBar();
                    MainAppScripts.updateRequiredInputsForm();
                    MainAppScripts.updatePaginationLinks();
                    MainAppScripts.updateDisabledLinks();
                    MainAppScripts.clearInputBtn();
                    MainAppScripts.scrollUpBtn();
                    MainAppScripts.updateRepeat();
                    MainAppScripts.updateTooltips();
                    MainAppScripts.updateToggleIcons();
                    MainAppScripts.processUIReadyCounter();
                }
            );

            MainAppScripts.configPjax();
            MainAppScripts.ajaxIndicator();
            MainAppScripts.updateF5key();
            MainAppScripts.disableTransition();
            MainAppScripts.update();
        }
    );
