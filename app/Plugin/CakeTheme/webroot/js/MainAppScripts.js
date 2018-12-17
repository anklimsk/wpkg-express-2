/**
 * This file use for application UI
 *
 * @file    Main file for MainAppScripts
 * @version 1.1.0
 * @copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.MainAppScripts = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         * @version 0.20.7
         * @namespace MainAppScripts
         */
        var MainAppScripts = {};
        /**
         * Content container for AJAX requset
         *
         * @constant {string} _contentContainer
         * @memberof MainAppScripts
         */
        var _contentContainer = '#content';

        /**
         * Content container for PAJAX requset
         *
         * @constant {string} _contentContainerPjax
         * @memberof MainAppScripts
         */
        var _contentContainerPjax = '#container';

        /**
         * Specify the url to fetch the settings of SSE object
         *
         * @constant {string} _SSEcfgUrl
         * @memberof MainAppScripts
         */
        var _SSEcfgUrl = '/cake_theme/events/ssecfg.json';

        /**
         * Specify the url to fetch the list of tasks for SSE
         *
         * @constant {string} _SSEtasksUrl
         * @memberof MainAppScripts
         */
        var _SSEtasksUrl = '/cake_theme/events/tasks.json';

        /**
         * Specify the url to fetch SSE data
         *
         * @constant {string} _SSEdataUrl
         * @memberof MainAppScripts
         */
        var _SSEdataUrl = '/cake_theme/events/queue';

        /**
         * Object of timer AJAX updater for function refresh page
         *
         * @member   {object} _timerRepeat
         * @memberof MainAppScripts
         */
        var _timerRepeat = null;

        /**
         * Object of JS Storage
         *
         * @member   {object} _storageObj
         * @memberof MainAppScripts
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         */
        var _storageObj = null;

        /**
         * Random number
         *
         * @member   {integer} _randomNum
         * @memberof MainAppScripts
         */
        var _randomNum = 0;

        /**
         * Delay to delete SSE tasks
         *
         * @member   {integer} _delayDeleteSSEtasks
         * @memberof MainAppScripts
         */
        var _delayDeleteSSEtasks = 5000;

        /**
         * Array of settings for Server-Sent Events object
         *
         * @member   {object} _SSE
         * @memberof MainAppScripts
         */
        var _SSE = {
            obj: {},
            retries: {},
            noty: {},
            config: {},
            progress: {}
        };

        /**
         * Options for PAJAX requset
         *
         * @constant {object} _optionsPjax
         * @memberof MainAppScripts
         */
        var _optionsPjax = {
            timeout: 5000,
            container: _contentContainerPjax
        };

        /**
         * Array of data for jQuery Sortable
         *
         * @member   {array} _sortableData
         * @memberof MainAppScripts
         */
        var _sortableData = {
            parentIdStart: null,
            backupElement: null
        };

        /**
         * Function for creating hash from string.
         *
         * @param {string} d Target data
         *
         * @function hashCode
         * @memberof MainAppScripts
         * @link     https://stackoverflow.com/a/7616484
         *
         * @returns {integer}
         */
        function _hashCode(d)
        {
            var hash   = 0, i, chr;
            var length = d.length;
            if (length === 0) {
                return hash;
            }

            for (i = 0; i < length; i++) {
                chr  = d.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash = (hash | 0);
                // ! Convert to 32bit integer
            }

            return hash;
        }

        /**
         * Return random number
         *
         * @function _getRandomNumber
         * @memberof MainAppScripts
         *
         * @returns {integer}
         */
        function _getRandomNumber()
        {
            var randNum = Math.floor(Math.random() * 50) + Date.now();

            return randNum;
        }

        /**
         * Return unique ID
         *
         * @param {string} prefix Prefix of unique ID
         *
         * @function _getUniqueID
         * @memberof MainAppScripts
         *
         * @returns {string}
         */
        function _getUniqueID(prefix)
        {
            var result = '';

            if (prefix) {
                result = prefix + '_';
            }

            if (_randomNum === 0) {
                _randomNum = _getRandomNumber();
            }

            result += _randomNum;
            _randomNum++;

            return result;
        }

        /**
         * Return state of usage storage
         *
         * @function _isStorageUse
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _isStorageUse()
        {
            if (_storageObj) {
                return true;
            }

            return false;
        }

        /**
         * Callback function for AJAX form submit. Called after the form has been
         *  successfully submitted
         *
         * @param {*} responseText Value (depending on the value of the dataType option)
         * @param {string} statusText
         * @param {object} xhr
         * @param {object} $form jQuery-wrapped form element
         *
         * @callback _ajaxFormResponse
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _ajaxFormResponse(responseText, statusText, xhr, $form)
        {
            var fadeBackground     = $form.data('fade-background');
            var fullFadeBackground = false;

            if ($form.attr('fade-page')) {
                fullFadeBackground = true;
            }

            if (statusText !== 'success') {
                MainAppScripts.loadIndicatorOff(fadeBackground, fullFadeBackground);
                return;
            }

            var objModalWindow = $form.parents('.main-app-scripts-modal');
            if (objModalWindow.length === 1) {
                var currentUrl         = $form.attr('action');
                var objForm            = $(responseText).find('form[action="' + currentUrl + '"]');
                var updateModalContent = $form.data('update-modal-content');
                if (objForm.length > 0) {
                    var modalContent = _prepareModalContent(responseText, objModalWindow);
                    objModalWindow.find('.modal-body').html(modalContent);
                    MainAppScripts.update();
                    MainAppScripts.loadIndicatorOff(fadeBackground);
                } else {
                    $(_contentContainer).data('update-page-after-close', true);
                    objModalWindow.data('update-modal-content', updateModalContent);
                    objModalWindow.modal('hide');
                    MainAppScripts.loadIndicatorOff(fadeBackground);
                }
            } else {
                $(_contentContainer).html(responseText);
                MainAppScripts.update();
                MainAppScripts.loadIndicatorOff(fadeBackground);
            }//end if
        }

        /**
         * Callback function for AJAX form submit error. Called invoked upon error
         *
         * @param {object} xhr
         * @param {string} status
         * @param {string} error
         * @param {object} $form jQuery-wrapped form element
         *
         * @callback _ajaxFormError
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _ajaxFormError(xhr, status, error, $form)
        {
            var fadeBackground     = $form.data('fade-background');
            var fullFadeBackground = false;

            if ($form.attr('fade-page')) {
                fullFadeBackground = true;
            }

            MainAppScripts.loadIndicatorOff(fadeBackground, fullFadeBackground);
            if (xhr.status === 403) {
                location.reload();
            }
        }

        /**
         * Function for submitting post form used on postLinks. Required attribute
         *  `data-onclick` contains callback function.
         *
         * @param {object} el Target element
         *
         * @function _submitPostForm
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _submitPostForm(el)
        {
            if (!el) {
                return false;
            }

            var target = $(el);
            if (!target.attr('data-onclick')) {
                return false;
            }

            var onClick = target.attr('data-onclick');
            if (!onClick) {
                return false;
            }

            var formId = /document\.(post_[0-9a-fA-F]+)\.submit\(\).+/.exec(onClick)[1];
            if (!formId) {
                return false;
            }

            var objForm = $('#' + formId);
            if (objForm.length !== 1) {
                return false;
            }

            if (jQuery.fn.ajaxSubmit) {
                objForm.attr('data-toggle', 'ajax-form');
                MainAppScripts.updateAjaxForm();
            }

            var updateModalContent = target.data('update-modal-content');
            objForm.data('update-modal-content', updateModalContent);

            return objForm.submit();
        }

        /**
         * This function used for fade background.
         *
         * @param {boolean} fullFadeBackground If True - full fade background.
         *  Default - False.
         *
         * @function _fadeBackgroundOn
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _fadeBackgroundOn(fullFadeBackground)
        {
            if (typeof fullFadeBackground === 'undefined') {
                fullFadeBackground = false;
            }

            var opacity = 0.8;
            if (fullFadeBackground) {
                opacity = 1;
            }

            if ($('div.mainappscripts-ds-overlay').length > 0) {
                return;
            }

            $('<div class="mainappscripts-ds-overlay"></div>').appendTo('body').fadeTo('fast', opacity);
        }

        /**
         * This function used for removing fade background.
         *
         * @param {boolean} fullFadeBackground If True - full fade background with higher
         *  z-index. Default - False.
         *
         * @function _fadeBackgroundOff
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _fadeBackgroundOff(fullFadeBackground)
        {
            if (typeof fullFadeBackground === 'undefined') {
                fullFadeBackground = false;
            }

            var classOverlay = 'mainappscripts-ds-overlay';
            if (fullFadeBackground) {
                classOverlay += '-higher';
            }

            $('div.' + classOverlay).fadeTo(
                'fast',
                0,
                function () {
                    $('div.' + classOverlay).detach();
                }
            );
        }

        /**
         * This function used for AJAX update contenet of page.
         *
         * @param {string} url URL for update
         * @param {integer} timeOut Delay for update
         * @param {string} target Selector of target element
         *
         * @function _loadRepeatData
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _loadRepeatData(url, timeOut, target)
        {
            if ((!url) || (timeOut < 10000) || !target) {
                return;
            }

            $.ajax(
                {
                    url: url,
                    async: false,
                    dataType: 'html',
                    success: function (data, textStatus, jqXHR) {
                        var targetObj = $(target);
                        if (targetObj.length === 0) {
                            return;
                        }

                        var htmlData = $(data).find(target).addBack(target);
                        if (htmlData.length !== 1) {
                            return;
                        }

                        targetObj.html(htmlData.html());
                        MainAppScripts.update();
                    }
                }
            );
        }

        /**
         * This function used for AJAX get list of countrys.
         *
         * @param {string} url URL for getting list
         * @param {string} id ID of input. Use as part of cache key
         *
         * @function _getCountrysList
         * @memberof MainAppScripts
         *
         * @returns {array}
         */
        function _getCountrysList(url, id)
        {
            var result = {};
            if (!url) {
                return result;
            }

            if (!id) {
                id = _hashCode(url);
            }

            var storageKey = 'inputCountrysList' + id;
            if (_isStorageUse() && _storageObj.isSet(storageKey)) {
                var stored = _storageObj.get(storageKey);
                if (stored) {
                    return stored;
                }
            }

            $.ajax(
                {
                    url: url,
                    async: false,
                    method: 'POST',
                    dataType: 'json',
                    global: false,
                    success: function (response) {
                        if (!response) {
                            return;
                        }

                        result = response;
                        if (_isStorageUse()) {
                            _storageObj.set(storageKey, result);
                        }
                    }
                }
            );

            return result;
        }

        /**
         * Getting and initializing the settings of SSE object
         *
         * @function _initSSEConfig
         * @memberof MainAppScripts
         * @requires Storages
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         *
         * @returns {null}
         */
        function _initSSEConfig()
        {
            if (_SSE.config.length > 0) {
                return;
            }

            var storageKey = 'SSEobjCfg';
            var config     = {
                text: 'Waiting to run task',
                label: {
                    task: 'Task',
                    completed: 'completed'
                },
                retries: 100,
                delayDeleteTask: 5
            };

            if (_isStorageUse() && _storageObj.isSet(storageKey)) {
                var stored = _storageObj.get(storageKey);
                if (stored) {
                    _SSE.config = stored;
                    return;
                }
            }

            $.ajax(
                {
                    async: false,
                    url: _SSEcfgUrl,
                    dataType: 'json',
                    global: false,
                    method: 'POST',
                    success: function (data) {
                        if (data) {
                            config = $.extend({}, config, data);
                        }

                        if (_isStorageUse()) {
                            _storageObj.set(storageKey, config);
                        }
                    }
                }
            );
            _SSE.config = config;
        }

        /**
         * Create noty object
         *
         * @function _createNoty
         * @memberof MainAppScripts
         * @see      {@link https://github.com/needim/noty noty
         *
         * @returns {object} Object of noty
         */
        function _createNoty(defaultText)
        {
            var result = null;
            if (typeof Noty === 'undefined') {
                return result;
            }

            if (!defaultText) {
                defaultText = 'Waiting...';
            }

            var _settings = {
                text: defaultText,
                type: 'information',
                timeout: false,
                force: true,
                closeWith: ['click'],
                layout: 'topRight',
                theme: 'bootstrap-v3',
                animation: {
                    open: 'animated bounceInRight',
                    close: 'animated bounceOutRight'
                }
            };

            var objNoty = new Noty(_settings);
            return objNoty;
        }

        /**
         * This function used as callback for event in SSE for
         *  show/hide/set NProgress bar.
         *
         * @param {object} e Event object
         *
         * @callback _setProgressSSE
         * @memberof MainAppScripts
         * @requires NProgress
         * @see      {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {null}
         */
        function _setProgressSSE(e)
        {
            var data         = $.parseJSON(e.data);
            var type         = data.type;
            var progress     = parseFloat(data.progress);
            var msg          = data.msg;
            var delayHide    = 1000;
            var useNProgress = true;
            var notyMsg      = '';
            if (isNaN(progress)) {
                progress = 0;
            }

            if (typeof NProgress === 'undefined') {
                useNProgress = false;
            }

            if (msg && (typeof msg === 'string')) {
                msg = msg.replace(/\n/g, '<br />');
            }

            if (data.result === false) {
                --_SSE.retries[type];
                if (_SSE.retries[type] > 0) {
                    if (_SSE.noty[type]) {
                        notyMsg = _SSE.config.text + ' "<b>' + type + '</b>" - [<i>' + _SSE.retries[type] + '</i>]';
                        _SSE.noty[type].setText(notyMsg, true);
                        if ((!_SSE.noty[type].showing && !_SSE.noty[type].shown) || _SSE.noty[type].closed) {
                            _SSE.noty[type].show();
                        }
                    }
                } else {
                    if (_SSE.obj[type]) {
                        _SSE.obj[type].stop();
                    }

                    if (_SSE.noty[type]) {
                        _SSE.noty[type].close();
                    }
                }
            } else {
                if (data.result === true) {
                    if (useNProgress) {
                        if (NProgress.status !== null) {
                            NProgress.done();
                            setTimeout(
                                function () {
                                    NProgress.remove();
                                },
                                delayHide
                            );
                        }
                    }

                    if (_SSE.noty[type]) {
                        if (progress < 1) {
                            _SSE.noty[type].setType('error', true);
                        } else if (msg) {
                            _SSE.noty[type].setType('warning', true);
                        } else {
                            _SSE.noty[type].setType('success', true);
                            _SSE.noty[type].setTimeout(30000);
                        }
                    }

                    if (_SSE.obj[type]) {
                        _SSE.obj[type].stop();
                    }
                } else {
                    if (useNProgress) {
                        if (NProgress.status === null) {
                            NProgress.start();
                        }

                        if (_SSE.progress[type]) {
                            NProgress.set(progress);
                        } else {
                            NProgress.inc();
                        }
                    }
                }//end if

                if (_SSE.noty[type]) {
                    notyMsg = _SSE.config.label.task + ': <b>' + type + '</b>; ' + _SSE.config.label.completed + ': <i>' + parseInt((progress * 100), 10) + '%</i>.';
                    if (msg) {
                        notyMsg += '<br /><div class="sse-message">' + _SSE.config.label.message + ': <small>' + msg + '.</small></div>';
                    }

                    _SSE.noty[type].setText(notyMsg, true);
                    if (!_SSE.noty[type].showing && !_SSE.noty[type].shown) {
                        _SSE.noty[type].show();
                    }
                }
            }//end if
        }

        /**
         * This function used as callback for event to move
         *  Tooltip or Popover in modal.
         *
         * @param {object} e Event object
         *
         * @callback _moveTooltip
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _moveTooltip(e)
        {
            var target = $(e.target);
            var modal  = target.parents('.modal');
            if (modal.length === 0) {
                return;
            }

            var targetPopover = $('[role="tooltip"]');
            targetPopover.detach();
            targetPopover.appendTo(modal);
        }

        /**
         * This function used for bind Twitter Bootstrap Tooltips.
         *
         * @param {string} target Selector to target element
         *
         * @function _updateTooltips
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Tooltips
         * @see      {@link http://getbootstrap.com/javascript/#tooltips} Twitter Bootstrap Tooltips
         *
         * @returns {boolean}
         */
        function _updateTooltips(target)
        {
            if (typeof $().tooltip !== 'function') {
                return false;
            }

            if (!target) {
                return false;
            }

            var activeToltip = $('div.tooltip.in[role="tooltip"]');
            if (activeToltip.length > 0) {
                activeToltip.removeClass('in');
            }

            var targetBlock = $(target);
            if (targetBlock.length === 0) {
                return true;
            }

            var targetItem    = null;
            var originalTitle = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap tooltip is binded
                    if (targetItem.data('bs.tooltip')) {
                        return true;
                    }

                    // ! Restore title for history back action
                    originalTitle = targetItem.attr('data-original-title');
                    if (originalTitle) {
                        targetItem.attr('title', originalTitle);
                        targetItem.removeAttr('data-original-title');
                    }

                    targetItem.tooltip(
                        {
                            trigger: 'hover',
                            container: _contentContainer,
                            placement: 'auto',
                            html: true
                        }
                    ).off('inserted.bs.tooltip').on('inserted.bs.tooltip', _moveTooltip);
                }
            );

            return true;
        }

        /**
         * This function used for AJAX get data for tree view.
         *
         * @param {string} url URL for getting data
         *
         * @function _getTreeData
         * @memberof MainAppScripts
         *
         * @returns {array}
         */
        function _getTreeData(url)
        {
            var result = {};
            if (!url) {
                return result;
            }

            $.ajax(
                {
                    url: url,
                    async: false,
                    method: 'POST',
                    dataType: 'json',
                    global: false,
                    success: function (response) {
                        if (!response) {
                            return;
                        }

                        result = response;
                    }
                }
            );

            return result;
        }

        /**
         * This function used as callback for change event for
         *  render progress bar of filling form inputs.
         *
         * @param {object} e Event object
         *
         * @memberof MainAppScripts
         * @callback _setProgressFillInputs
         *
         * @returns {null}
         */
        function _setProgressFillInputs(e)
        {
            var target           = $(this);
            var progressBar      = target.parents('form[progressfill!=""][progressfill]').find('[role="progressbar"].inputs-filled-progress');
            var progressBarClass = '';
            if (!progressBar) {
                return;
            }

            var val                  = target.val();
            var totalInputs          = progressBar.data('total-inputs');
            var totalRequiredInputs  = progressBar.data('total-required-inputs');
            var filledInputs         = progressBar.data('filled-inputs');
            var filledRequiredInputs = progressBar.data('filled-required-inputs');
            if (!filledInputs) {
                filledInputs = [];
            }

            if (!filledRequiredInputs) {
                filledRequiredInputs = [];
            }

            var id = target.attr('id');
            if (!id) {
                return;
            }

            var required    = target.attr('required');
            var posFilled   = $.inArray(id, filledInputs);
            var posRequired = $.inArray(id, filledRequiredInputs);
            if (!val || (val.length === 0)) {
                if (posFilled !== -1) {
                    filledInputs.splice(posFilled, 1);
                }

                if (required && (posRequired !== -1)) {
                    filledRequiredInputs.splice(posRequired, 1);
                }
            } else {
                if (posFilled === -1) {
                    filledInputs.push(id);
                }

                if (required && (posRequired === -1)) {
                    filledRequiredInputs.push(id);
                }
            }

            progressBar.data('filled-inputs', filledInputs);
            progressBar.data('filled-required-inputs', filledRequiredInputs);
            var filledInputsCount         = filledInputs.length;
            var filledRequiredInputsCount = filledRequiredInputs.length;

            var perc = 0;
            if (totalInputs > 0) {
                perc = Math.round(filledInputsCount / totalInputs * 100);
            }

            progressBar.css(
                {
                    'width': perc + '%'
                }
            );
            progressBar.text(filledInputsCount + ' / ' + totalInputs);
            if (totalRequiredInputs > 0) {
                if (filledRequiredInputsCount < totalRequiredInputs) {
                    progressBarClass = 'progress-bar-danger';
                } else {
                    if ((totalInputs - totalRequiredInputs) > 0) {
                        if (filledInputsCount < totalInputs) {
                            progressBarClass = 'progress-bar-warning';
                        } else {
                            progressBarClass = 'progress-bar-success';
                        }
                    } else {
                        progressBarClass = 'progress-bar-success';
                    }
                }
            } else {
                if (filledInputsCount < totalInputs) {
                    progressBarClass = 'progress-bar-warning';
                } else {
                    progressBarClass = 'progress-bar-success';
                }
            }//end if

            progressBarClass = 'progress-bar ' + progressBarClass + ' inputs-filled-progress';
            progressBar.attr('class', progressBarClass);
        }

        /**
         * This function used as callback for change event for
         *  checking required form inputs and display error
         *  message if input is empty.
         *
         * @param {object} e Event object
         *
         * @callback _setRequiredInputsMessage
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _setRequiredInputsMessage(e)
        {
            var tabId                 = null;
            var tabLink               = null;
            var exclamationIcon       = null;
            var exclamationIconParent = null;
            var targetInput           = $(this);
            var parentGroupEl         = targetInput.parents('.form-group');
            var msgWrapId             = 'RequiredInputEmptyMsg';
            var msgText               = 'Required input is empty';
            var emptyInputMsg         = targetInput.parents('form[requiredcheck!=""][requiredcheck]').data('required-msg');
            if (emptyInputMsg) {
                msgText = emptyInputMsg;
            }

            if (parentGroupEl.length === 0) {
                return;
            }

            var tabObj = targetInput.parents('.tab-pane:eq(0)');
            if (tabObj.length === 1) {
                tabId = tabObj.attr('id');
                if (tabId) {
                    tabLink = targetInput.parents('.tabbable').find('ul.nav li a[href="#' + tabId + '"]');
                }
            }

            if (targetInput.val()) {
                parentGroupEl.find('span#' + msgWrapId).remove();
                if (parentGroupEl.find('.help-block').length === 0) {
                    parentGroupEl.removeClass('has-error');
                }

                if (tabLink && (tabObj.find('.has-error').length === 0)) {
                    exclamationIcon = tabLink.find('.required-input-tab-icon');
                    if (exclamationIcon.length > 0) {
                        exclamationIconParent = exclamationIcon.parent();
                        exclamationIconParent.children('.required-input-tab-icon').remove();
                    }
                }
            } else {
                parentGroupEl.addClass('has-error');
                if (parentGroupEl.find('span#' + msgWrapId).length === 0) {
                    parentGroupEl.append('<span id="' + msgWrapId + '" class="help-block">' + msgText + '</span>');
                }

                if (tabLink && (tabLink.find('.required-input-tab-icon').length === 0)) {
                    tabLink.append('<span class="required-input-tab-icon">&nbsp;<span class="fas fa-exclamation-triangle fa-lg"></span></span>');
                }
            }//end if
        }

        /**
         * This function used as callback for submit event for
         *  checking required form inputs.
         *
         * @param {object} e Event object
         *
         * @callback _submitFormWithRequiredInputs
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _submitFormWithRequiredInputs(e)
        {
            var requiredInputs = $(this).parents('form[requiredcheck!=""][requiredcheck]').find(':input[name^="data["][required]');
            if (requiredInputs.length === 0) {
                return true;
            }

            var emptyInput = requiredInputs.filter(
                function () {
                    return !this.value;
                }
            );
            if (emptyInput.length === 0) {
                return true;
            }

            emptyInput.trigger('change.MainAppScripts.RequirInput');
            var targetInput = emptyInput.eq(0);
            var tabObj      = targetInput.closest('.tab-pane');
            if (tabObj.length === 1) {
                var tabId = tabObj.attr('id');
                if (tabId) {
                    targetInput.parents('.tabbable').find('ul.nav li a[href="#' + tabId + '"]').tab('show');
                }
            }

            return false;
        }

        /**
         * Callback function for move parent element of `Move` links
         *
         * @param {object} data Response data of move action
         *
         * @callback _moveLinksAjaxSuccess
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _moveLinksAjaxSuccess(data)
        {
            if (!data.result) {
                return;
            }

            var parentElem = this.parents('li:eq(0)');
            if (parentElem.length === 0) {
                parentElem = this.parents('tr:eq(0)');
                if (parentElem.length === 0) {
                    return;
                }
            }

            var direct    = data.direct;
            var currIndex = parentElem.index();
            var newIndex  = null;
            var delta     = 0;
            if (typeof(data.delta) !== 'boolean') {
                delta = parseInt(data.delta, 10);
                if (isNaN(delta)) {
                    delta = 0;
                }
            }

            switch (direct) {
                case 'top':
                    newIndex = 0;
                case 'up':
                    if (newIndex === null) {
                        newIndex = currIndex - delta;
                    }

                    if (parentElem.is(':first-child')) {
                        return;
                    }

                    if (typeof $().tooltip === 'function') {
                        this.tooltip('hide');
                    }

                    parentElem.insertBefore(parentElem.siblings(':eq(' + newIndex + ')'));
                break;

                case 'bottom':
                    newIndex = -1;
                case 'down':
                    if (newIndex === null) {
                        newIndex = currIndex + delta - 1;
                    }

                    if (parentElem.is(':last-child')) {
                        return;
                    }

                    if (typeof $().tooltip === 'function') {
                        this.tooltip('hide');
                    }

                    parentElem.insertAfter(parentElem.siblings(':eq(' + newIndex + ')'));
                break;

                default:
                return;
            }//end switch

            return;
        }

        /**
         * Callback function for add new data to page
         *
         * @param {string} data Response HTML data from server
         *
         * @callback _loadMoreAjaxSuccess
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _loadMoreAjaxSuccess(data)
        {
            var fadeBackground = this.fadeBackground;
            if (!data) {
                MainAppScripts.loadIndicatorOff(fadeBackground);
                return;
            }

            if (typeof $().tooltip === 'function') {
                this.target.tooltip('hide');
            }

            var targetSelector     = this.target.data('target-selector');
            var dataSelector       = '';
            var contentSelector    = _contentContainer;
            var objData            = null;
            var objConfirmFormData = null;
            if (targetSelector) {
                dataSelector       = targetSelector + ' > *';
                contentSelector   += ' ' + targetSelector;
                objData            = $(dataSelector, data);
                objConfirmFormData = $('.confirm-form-block > *', data);
            } else {
                objData = $(data);
            }

            if (objData.length > 0) {
                objData.hide().appendTo($(contentSelector)).fadeIn('slow');
            }

            if (objConfirmFormData.length > 0) {
                objConfirmFormData.appendTo($('.confirm-form-block'));
            }

            if ($(_contentContainer + ' .load-more').length > 0) {
                $(_contentContainer + ' .load-more').replaceWith($('.load-more', data));
            } else {
                $(_contentContainer + ' a[data-toggle="load-more"]').replaceWith($('a[data-toggle="load-more"]', data));
            }

            MainAppScripts.update();
            MainAppScripts.loadIndicatorOff(fadeBackground);
            return;
        }

        /**
         * Callback function for drop element and restore state on failure
         *
         * @param {object} $item jQuery object of dragged element
         * @param {object} container Container object of dragged element
         * @param {callback} _super Callback function from parent widget
         *
         * @callback _sortableOnDrop
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _sortableOnDrop($item, container, _super)
        {
            _super($item, container);
            var itemSelector = container.group.options.itemSelector;
            var group        = container.rootGroup.options.group;
            var groupClass   = '.' + group;
            var url          = $item.parents('[data-toggle="draggable"]').data('url');
            var targetId     = $item.data('id');
            var targetObj    = container.el;
            var dataTree     = targetObj.sortable('serialize').get();
            var parentId     = targetObj.parent(itemSelector).data('id');
            if (typeof parentId === 'undefined') {
                parentId = null;
            }

            var dataPost = {
                target: targetId,
                parent: parentId,
                parentStart: _sortableData.parentIdStart,
                tree: JSON.stringify(dataTree)
            };
            var result   = false;
            if (url) {
                $.ajax(
                    {
                        async: false,
                        url: url,
                        data: dataPost,
                        dataType: 'json',
                        method: 'POST',
                        success: function (data) {
                            result = data.result;
                        }
                    }
                );
            }

            if (!result) {
                $(groupClass).replaceWith(_sortableData.backupElement);
                MainAppScripts.update();
            }
        }

        /**
         * Callback function for start drop element and store state, parent ID.
         *
         * @param {object} $item jQuery object of dragged element
         * @param {object} container Container object of dragged element
         * @param {callback} _super Callback function from parent widget
         *
         * @callback _sortableOnDragStart
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _sortableOnDragStart($item, container, _super)
        {
            var itemSelector            = container.group.options.itemSelector;
            var group                   = container.rootGroup.options.group;
            _sortableData.parentIdStart = $item.parents(itemSelector).data('id');
            _sortableData.backupElement = $('.' + group).clone(false);
            _super($item, container);
        }

        /**
         * Base function for update page by parameters.
         *
         * @param {object} target Target object
         * @param {string} param Name of request parameter
         * @param {integer} defaultValue Default value of
         *  request parameter
         *
         * @function _updatePageByParam
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _updatePageByParam(target, param, defaultValue)
        {
            if ((typeof target !== 'object')
                || (target.length === 0)
                || !param
            ) {
                return false;
            }

            var url = target.data('url');
            if (!url) {
                return false;
            }

            url       = url.replace(/&amp;/g, '&');
            var value = parseInt(target.val(), 10);
            if (isNaN(value)) {
                value = defaultValue;
            }

            var currValue = parseInt(target.data('curr-value'), 10);
            if (isNaN(currValue)) {
                currValue = defaultValue;
            }

            if (value === currValue) {
                return false;
            }

            var pattern = param + ':\\d+';
            var objRE   = new RegExp(pattern);
            if (url.search(objRE) === -1) {
                return false;
            }

            pattern       = '(' + param + ':)\\d+';
            objRE         = new RegExp(pattern, 'g');
            var urlUpdate = url.replace(objRE, '$1' + value);
            MainAppScripts.updatePage(urlUpdate);

            return true;
        }

        /**
         * Callback function for change number lines.
         *
         * @param {object} e Event object
         *
         * @callback _changeNumLines
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _changeNumLines(e)
        {
            var target = $(this);
            _updatePageByParam(target, 'limit', 20);

            return false;
        }

        /**
         * Callback function for go to the page on click event from button `Go`
         *  located inside spinner.
         *
         * @param {object} e Event object
         *
         * @callback _gotoPage
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _gotoPage(e)
        {
            e.preventDefault();
            var target = $(this).siblings(':text');
            _updatePageByParam(target, 'page', 1);

            return true;
        }

        /**
         * Return content for popover on click event from link.
         *
         * @callback _getPopoverContent
         * @memberof MainAppScripts
         *
         * @returns {string} Content for popover
         */
        function _getPopoverContent()
        {
            var result         = '';
            var el             = $(this);
            var url            = $(el).attr('href');
            var urlData        = $(el).data('popover-url');
            var contentCached  = $(el).data('cached');
            var fadeBackground = false;

            if (contentCached) {
                return contentCached;
            }

            if (!url) {
                if (!urlData) {
                    return result;
                }

                url = urlData;
            }

            if (!url.match(/\.pop$/)) {
                url += '.pop';
            }

            $.ajax(
                {
                    url: url,
                    dataType: 'html',
                    async: false,
                    success: function (data) {
                        $(el).data('cached', data);
                        result = data;
                    }
                }
            );

            return result;
        }

        /**
         * Return template for popover by size.
         *
         * @param {string} size Size of popover
         *
         * @callback _getPopoverTemplate
         * @memberof MainAppScripts
         *
         * @returns {string} Template for popover
         */
        function _getPopoverTemplate(size)
        {
            var templateNormal = '<div class="popover" role="tooltip">\
                    <div class="arrow"></div>\
                    <h3 class="popover-title"></h3>\
                    <div class="popover-content"></div>\
                </div>';
            var templateLarge = '<div class="popover popover-lg" role="tooltip">\
                    <div class="arrow"></div>\
                    <h3 class="popover-title"></h3>\
                    <div class="popover-content"></div>\
                </div>';

            if (!size) {
                return templateNormal;
            }

            size = size.toLowerCase();
            if (size === 'lg') {
                return templateLarge;
            }

            return templateNormal;
        }

        /**
         * Create template of modal window
         *
         * @function _createTemplateModalWindow
         * @memberof MainAppScripts
         *
         * @returns {string} ID of modal window
         */
        function _createTemplateModalWindow()
        {
            var modalId = _getUniqueID('MainAppScriptsModal');
            $(_contentContainer).append(
                '<div class="main-app-scripts-modal modal fade" id="' + modalId + '" tabindex="-1" role="dialog" aria-labelledby="ModalFormLabel">\
                    <div class="modal-dialog" role="document">\
                        <div class="modal-content">\
                            <div class="modal-header">\
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title text-center">&nbsp;</h4>\
                            </div>\
                            <div class="modal-body">\
                            </div>\
                        </div>\
                    </div>\
                </div>'
            );
            return modalId;
        }

        /**
         * This function used for prepare contenet of modal window:
         *  - Add attribute `modal` to links of page menu;
         *  - Add page menu in modal header;
         *  - Make form in modal window AJAX.
         *
         * @param {string} content Content for preraring
         * @param {object} modalWindow jQuery object of modal window
         *
         * @function _prepareModalContent
         * @memberof MainAppScripts
         *
         * @returns {string} Prepared contenet
         */
        function _prepareModalContent(content, modalWindow)
        {
            if (!content || !modalWindow) {
                return content;
            }

            var objModalContent = $(content);
            var objPageHeader   = objModalContent.find('.page-header').detach();
            if (objPageHeader.length > 0) {
                var objPageHeaderTag = objPageHeader.find('.header');
                if (objPageHeaderTag.length > 0) {
                    objPageHeaderTag.find('#pageHeaderMenu').addClass('btn-sm');
                    objPageHeaderTag.find('.page-header-menu a:not([role="post-link"],[data-toggle="ajax"],[data-toggle="request-only"],[skip-modal][skip-modal!=""])').attr('data-toggle', 'modal');
                    modalWindow.find('.modal-header .modal-title').html(objPageHeaderTag.html());
                }
            }

            objModalContent.find('form:not([skip-modal])').attr('data-toggle', 'ajax-form');
            var result = $('<div />').append(objModalContent).html();
            return result;
        }

        /**
         * Add information of modal window to stack.
         *
         * @param {object} info Information of modal window
         *
         * @function _pushModalWindowInfo
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _pushModalWindowInfo(info)
        {
            if ($.isEmptyObject(info)) {
                return;
            }

            var objContentContainer = $(_contentContainer);
            if (objContentContainer.length === 0) {
                return;
            }

            var stack = objContentContainer.data('stack-modal-window-info');
            if (!$.isArray(stack)) {
                stack = [];
            }

            var defaultInfo = {
                'url': '',
                'title': '',
                'size': '',
                'method': ''
            };
            info            = $.extend({}, defaultInfo, info);
            stack.push(info);
            objContentContainer.data('stack-modal-window-info', stack);
        }

        /**
         * Get information of modal window from stack.
         *
         * @function _popModalWindowInfo
         * @memberof MainAppScripts
         *
         * @returns {object} Information of modal window
         */
        function _popModalWindowInfo()
        {
            var info                = {};
            var objContentContainer = $(_contentContainer);
            if (objContentContainer.length === 0) {
                return info;
            }

            var stack = objContentContainer.data('stack-modal-window-info');
            if (!$.isArray(stack) || (stack.length === 0)) {
                return info;
            }

            info = stack.pop();
            objContentContainer.data('stack-modal-window-info', stack);
            return info;
        }

        /**
         * This function used for AJAX load and showing modal window.
         *
         * @param {string} url URL to update content of modal window
         * @param {string} title Title of modal window
         * @param {string} size Size of modal window: `sm` or `lg`
         * @param {string} method Type of request: GET or POST
         * @param {boolean} disableUseStack Disabling use stack of madal window
         *
         * @memberof MainAppScripts
         * @function _showModalWindow
         *
         * @returns {null}
         */
        function _showModalWindow(url, title, size, method, disableUseStack)
        {
            if (!url) {
                return;
            }

            var objModalWindows = $('.main-app-scripts-modal');
            if (objModalWindows.length > 0) {
                objModalWindows.data('disable-use-stack', true);
                objModalWindows.modal('hide');
            }

            var modalId        = _createTemplateModalWindow();
            var fadeBackground = true;
            var modalWindow    = $('#' + modalId);
            var modalClass     = '';

            if (!url.match(/\.mod$/)) {
                url += '.mod';
            }

            if (!method) {
                method = 'GET';
            }

            method = method.toUpperCase();
            if (title) {
                modalWindow.find('.modal-header h4').text(title);
            }

            if (size) {
                size = size.toLowerCase();
                switch (size) {
                    case 'sm':
                        modalClass = 'modal-sm';
                    break;

                    case 'lg':
                        modalClass = 'modal-lg';
                    break;

                    default:
                        modalClass = '';
                    break;
                }

                if (modalClass) {
                    modalWindow.find('.modal-dialog').addClass(modalClass);
                }
            }

            if (typeof disableUseStack === 'undefined') {
                disableUseStack = false;
            }

            if (!modalWindow.data('disable-use-stack')) {
                modalWindow.data('disable-use-stack', disableUseStack);
            }

            MainAppScripts.loadIndicatorOn(fadeBackground);
            $.ajax(
                {
                    url: url,
                    method: method,
                    async: true,
                    dataType: 'html',
                    success: function (data, textStatus, jqXHR) {
                        var modalWindowInfo = {
                            'url': url,
                            'title': title,
                            'size': size,
                            'method': method
                        };
                        _pushModalWindowInfo(modalWindowInfo);
                        var modalContent = _prepareModalContent(data, modalWindow);
                        modalWindow.find('.modal-body').html(modalContent);
                        MainAppScripts.update();
                        MainAppScripts.loadIndicatorOff(fadeBackground);
                        modalWindow.modal('show');
                        modalWindow.off('shown.bs.modal').on(
                            'shown.bs.modal',
                            function () {
                                MainAppScripts.setInputFocus();
                            }
                        );
                        modalWindow.off('hidden.bs.modal').on(
                            'hidden.bs.modal',
                            function (e) {
                                var objModal             = $(this);
                                var objContentContainer  = $(_contentContainer);
                                var disableUseStack      = objModal.data('disable-use-stack');
                                var updatePageAfterClose = objContentContainer.data('update-page-after-close');
                                var updateModalContent   = objModal.data('update-modal-content');
                                objModal.remove();
                                if (!disableUseStack) {
                                    var info = _popModalWindowInfo();
                                    if (!updateModalContent) {
                                        info = _popModalWindowInfo();
                                    }

                                    if (!$.isEmptyObject(info)) {
                                        updatePageAfterClose = false;
                                        _showModalWindow(info.url, info.title, info.size, info.method, disableUseStack);
                                    }
                                } else {
                                    updatePageAfterClose = false;
                                }

                                if (updatePageAfterClose) {
                                    objContentContainer.data('update-page-after-close', false);
                                    MainAppScripts.updatePage();
                                }
                            }
                        );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        MainAppScripts.loadIndicatorOff(fadeBackground);
                    }
                }
            );
        }

        /**
         * This function used as callback for click event from link
         *  for AJAX load and showing modal window.
         *
         * @param {object} e Event object
         *
         * @callback _showModalWindowClick
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        function _showModalWindowClick(e)
        {
            e.preventDefault();

            var el              = $(this);
            var url             = $(el).attr('href');
            var title           = el.data('modal-title');
            var size            = el.data('modal-size');
            var method          = el.data('modal-method');
            var disableUseStack = false;
            if ((el.attr('data-toggle') === 'modal')
                || (el.attr('data-toggle') === 'modal-popover')
            ) {
                disableUseStack = el.data('disable-use-stack');
            } else {
                var wrapBlock = el.parents('[data-toggle="modal"]');
                if (wrapBlock.length > 0) {
                    disableUseStack = wrapBlock.data('disable-use-stack');
                }
            }

            _showModalWindow(url, title, size, method, disableUseStack);

            return false;
        }

        /**
         * This function used for prepare POST link.
         *
         * @function _preparePostLink
         * @memberof MainAppScripts
         * @requires jQuery.Form
         * @see      {@link http://malsup.com/jquery/form} jQuery.Form
         *
         * @returns {null}
         */
        function _preparePostLink()
        {
            var targetBlock       = $('a[role="post-link"][onclick]');
            var targetBlockLength = targetBlock.length;

            if (targetBlockLength > 0) {
                var targetItem = null;
                targetBlock.each(
                    function (i, el) {
                        targetItem = $(el);
                        targetItem.attr('data-onclick', targetItem.attr('onclick')).removeAttr('onclick');
                    }
                );
            }
        }

        /**
         * This function used for checking ready Font Awesome.
         *
         * @function _checkReadyFontAwesome
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        function _checkReadyFontAwesome()
        {
            if (!$('html').hasClass('fontawesome-i2svg-active')) {
                setTimeout(_checkReadyFontAwesome, 100);
                return;
            }

            MainAppScripts.processUIReadyCounter('FontAwesome');
        }

        /**
         * Delete a list of SSE tasks on server
         *
         * @param {array} keys Array data of the session key
         *  for delete message.
         *
         * @function _deleteSSEtasks
         *
         * @returns {null}
         */
        function _deleteSSEtasks(keys)
        {
            if (!keys || !$.isArray(keys) || (keys.length === 0)) {
                return;
            }

            var postData = {
                'data': {
                    'tasks': keys,
                    'delete': 1
                }
            };
            $.ajax(
                {
                    async: true,
                    url: _SSEtasksUrl,
                    data: postData,
                    dataType: 'json',
                    global: false,
                    method: 'POST',
                    success: postData
                }
            );
        }

        /**
         * Initializing the JS Storage Plugin
         *
         * @function initStorage
         * @memberof MainAppScripts
         * @requires Storages
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         *
         * @returns {boolean}
         */
        MainAppScripts.initStorage = function () {
            if (typeof Storages === 'undefined') {
                return false;
            }

            _storageObj = Storages.initNamespaceStorage('main_app_srt').sessionStorage;

            return true;
        };

        /**
         * This function used for configuration `PJAX`.
         *
         * @function configPjax
         * @memberof MainAppScripts
         * @requires jQuery.Pjax,NProgress
         * @see      {@link https://github.com/defunkt/jquery-pjax} PJAX,
         *  {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {boolean}
         */
        MainAppScripts.configPjax = function () {
            if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                return false;
            }

            var fadeBackground = true;
            var needUpdate     = false;
            $.pjax.defaults    = $.extend({}, $.pjax.defaults, _optionsPjax);
            $(document).off('pjax:success').on(
                'pjax:success',
                function () {
                    MainAppScripts.update();
                }
            );
            $(document).off('pjax:popstate').on(
                'pjax:popstate',
                function () {
                    needUpdate = true;
                }
            );
            $(document).off('pjax:start').on(
                'pjax:start',
                function () {
                    MainAppScripts.loadIndicatorOn(fadeBackground);
                }
            );
            $(document).off('pjax:end').on(
                'pjax:end',
                function () {
                    if (needUpdate) {
                        needUpdate = false;
                        MainAppScripts.update();
                    }

                    MainAppScripts.loadIndicatorOff(fadeBackground);
                }
            );

            return true;
        };

        /**
         * This function used for bind jQuery plugin `Timeago`.
         * Selector: `[data-toggle="timeago"]`.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateTimeago
         * @memberof MainAppScripts
         * @requires jQuery.Timeago
         * @see      {@link http://timeago.yarp.com} Timeago
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateTimeago = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.timeago) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="timeago"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var targetItem = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);

                    targetItem.timeago();
                    MainAppScripts.processUIReadyCounter('Timeago');
                }
            );
            _updateTooltips('[data-toggle="timeago"][title]');

            return true;
        };

        /**
         * This function used for AJAX repeat update content.
         * Selector: `[data-toggle="repeat"]`.
         * Attributes:
         *  `data-repeat-time` - Repeat time in seconds;
         *
         * @function updateRepeat
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        MainAppScripts.updateRepeat = function () {
            var url         = window.location.href;
            var selector    = '[data-toggle="repeat"]';
            var target      = $(selector);
            var timeOutData = target.data('repeat-time');
            var timeOut     = 60;

            if (target.length !== 1) {
                return false;
            }

            if (timeOutData) {
                timeOutData = parseInt(timeOutData, 10);
                if (!isNaN(timeOutData)) {
                    timeOut = timeOutData;
                }
            }

            timeOut *= 1000;
            clearTimeout(_timerRepeat);
            _timerRepeat = setTimeout(_loadRepeatData, timeOut, url, timeOut, selector);

            return true;
        };

        /**
         * This function is used to disabling transitions.
         *
         * @function disableTransition
         * @memberof MainAppScripts
         * @see      {@link http://getbootstrap.com/javascript/#transitions} Disabling transitions
         *
         * @returns {null}
         */
        MainAppScripts.disableTransition = function () {
            $.support.transition = false;
        };

        /**
         * This function used for PJAX update page.
         *
         * @param {string} url URL for update page
         *
         * @function updatePage
         * @memberof MainAppScripts
         * @requires jQuery.Pjax
         * @see      {@link https://github.com/defunkt/jquery-pjax} PJAX
         *
         * @returns {null}
         */
        MainAppScripts.updatePage = function (url) {
            if (!url) {
                url = window.location.href;
            }

            if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                window.location.href = url;
                return true;
            }

            var pjaxOptions     = {
                url: url,
                container: _contentContainerPjax
            };
            var pjaxOptionsFull = $.extend({}, pjaxOptions, _optionsPjax);
            $('[data-original-title]').tooltip('hide');
            var objModal = $('.modal');
            if (objModal.length > 0) {
                objModal.data('disable-use-stack', true);
                objModal.modal('hide');
            }

            $.pjax(pjaxOptionsFull);

            return true;
        };

        /**
         * This function is used to bind an AJAX update the contents of the
         *  page when you press `F5`.
         *
         * @function updateF5key
         * @memberof MainAppScripts
         * @requires jQuery.Pjax
         * @see      {@link https://github.com/defunkt/jquery-pjax} PJAX
         *
         * @returns {boolean}
         */
        MainAppScripts.updateF5key = function () {
            if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                return false;
            }

            $(document).off('keydown.MainAppScripts').on(
                'keydown.MainAppScripts',
                function (e) {
                    if (e.keyCode === 116) {
                        e.preventDefault();

                        MainAppScripts.updatePage();
                    }
                }
            );

            return true;
        };

        /**
         * This function used for disable click event on disabled links.
         * Selector: `.disabled a, a.disabled`.
         *
         * @function updateDisabledLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateDisabledLinks = function () {
            $('.disabled a, a.disabled').off('click').on(
                'click.MainAppScripts',
                function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            );
        };

        /**
         * This function used for disable click event on not used links.
         * Selector: `a[role="button"]`.
         *
         * @function updateNotUsedLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateNotUsedLinks = function () {
            $('a[role="button"]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                }
            );
        };

        /**
         * This function used for AJAX update the contents of the page on click event.
         * Selector: `[data-toggle="lightbox"]`.
         *
         * @function updateLightboxLinks
         * @memberof MainAppScripts
         * @requires Lightbox for Bootstrap
         * @see      {@link http://ashleydw.github.io/lightbox/} Lightbox for Bootstrap
         *
         * @returns {boolean}
         */
        MainAppScripts.updateLightboxLinks = function () {
            if (!jQuery.fn.ekkoLightbox) {
                return false;
            }

            $(document).off('click.MainAppScripts').on(
                'click.MainAppScripts',
                '[data-toggle="lightbox"]',
                function (e) {
                    e.preventDefault();
                    var target = $(this);
                    target.ekkoLightbox();
                }
            );

            return true;
        };

        /**
         * This function used for AJAX update the contents of the page on click event.
         * Selector: `a[data-toggle="ajax"], [data-toggle="ajax"] a`.
         *
         * @function updateAjaxLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateAjaxLinks = function () {
            $('a[data-toggle="ajax"], [data-toggle="ajax"] a').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var fadeBackground = true;
                    var url            = $(this).attr('href');
                    if (!url) {
                        return false;
                    } else if (url === '#') {
                        return true;
                    }

                    MainAppScripts.loadIndicatorOn(fadeBackground);
                    $(_contentContainer).load(
                        url,
                        function (responseText, textStatus, jqXHR) {
                            if (textStatus !== 'success') {
                                MainAppScripts.loadIndicatorOff(fadeBackground);
                                return;
                            }

                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                            MainAppScripts.update();
                            MainAppScripts.loadIndicatorOff(fadeBackground);
                        }
                    );
                }
            );
        };

        /**
         * This function used for PAJAX update the contents of the page on click event.
         * Selector: `a[data-toggle="pjax"], [data-toggle="pjax"] a`.
         *
         * @function updatePjaxLinks
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        MainAppScripts.updatePjaxLinks = function () {
            if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                return false;
            }

            $('a[data-toggle="pjax"], [data-toggle="pjax"] a').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    if (!url) {
                        return false;
                    } else if (url === '#') {
                        return true;
                    }

                    MainAppScripts.updatePage(url);
                    return false;
                }
            );

            return true;
        };

        /**
         * This function used for prints the contents of the current window on click event.
         * Selector: `[data-toggle="print"]`.
         *
         * @function updatePrintLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updatePrintLinks = function () {
            $('[data-toggle="print"]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    window.print();
                }
            );
        };

        /**
         * This function used for show progress bar of task from queue on click event.
         * Selector: `a[data-toggle="progress-sse"], [data-toggle="progress-sse"] a`.
         * Attributes:
         *  `data-task-type` - Type of task.
         *  `data-task-use-progress` - Use progress bar for this task: `True` or `False`
         *   (default - `True`).
         *
         * @function updateProgressSSELinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateProgressSSElinks = function () {
            $('a[data-toggle="progress-sse"], [data-toggle="progress-sse"] a').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var url            = $(this).attr('href');
                    var fadeBackground = true;
                    var type           = $(this).data('task-type');
                    var useProgress    = $(this).data('task-use-progress');
                    if (typeof useProgress === 'undefined') {
                        useProgress = true;
                    }

                    if (!url) {
                        return false;
                    } else if (url === '#') {
                        return true;
                    }

                    MainAppScripts.loadIndicatorOn(fadeBackground);
                    $(_contentContainer).load(
                        url,
                        function (responseText, textStatus, jqXHR) {
                            if (textStatus !== 'success') {
                                MainAppScripts.loadIndicatorOff(fadeBackground);
                                return;
                            }

                            MainAppScripts.update();
                            MainAppScripts.loadIndicatorOff(fadeBackground);
                            MainAppScripts.showProgressSSE(type, useProgress);
                        }
                    );
                }
            );
        };

        /**
         * This function used for show progress bar of task from queue.
         *
         * @function updateProgressSSEtasks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateProgressSSEtasks = function () {
            $.ajax(
                {
                    async: true,
                    url: _SSEtasksUrl,
                    dataType: 'json',
                    global: false,
                    method: 'POST',
                    success: function (data) {
                        if (!data.result || !data.tasks) {
                            return;
                        }

                        var keysDelete = [];
                        $.each(
                            data.tasks,
                            function (i, task) {
                                MainAppScripts.showProgressSSE(task, true);
                                keysDelete.push(task);
                            }
                        );
                        if (keysDelete.length > 0) {
                            _initSSEConfig();
                            var delayDeleteTask = (_SSE.config.delayDeleteTask * 1000);
                            setTimeout(_deleteSSEtasks, delayDeleteTask, keysDelete);
                        }
                    }
                }
            );
        };

        /**
         * This function used for AJAX move element of list or table on
         *  click event of action link.
         * Selector: `a[data-toggle="move"]`.
         *
         * @function updateMoveLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateMoveLinks = function () {
            $('a[data-toggle="move"]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var target = $(this);
                    var url    = target.attr('href');
                    if (!url) {
                        return false;
                    }

                    url += '.json';
                    $.ajax(
                        {
                            async: true,
                            url: url,
                            dataType: 'json',
                            method: 'POST',
                            context: target,
                            success: _moveLinksAjaxSuccess
                        }
                    );
                }
            );
        };

        /**
         * This function used for AJAX add data on
         *  click event of action link `Load more`.
         * Selector: `a[data-toggle="load-more"]`.
         * Attributes:
         *  `data-target-selector` - jQuery selector to select data
         *   from server response and selection of the element on the
         *   page to add new data.
         *
         * @function updateLoadMoreLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateLoadMoreLinks = function () {
            $('a[data-toggle="load-more"]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var fadeBackground = true;
                    var target         = $(this);
                    var url            = target.attr('href');
                    if (!url) {
                        return false;
                    }

                    $.ajax(
                        {
                            async: true,
                            url: url,
                            dataType: 'html',
                            global: false,
                            context: {
                                target: target,
                                fadeBackground: fadeBackground
                            },
                            beforeSend: function () {
                                MainAppScripts.loadIndicatorOn(fadeBackground);
                            },
                            success: _loadMoreAjaxSuccess,
                            error: function () {
                                MainAppScripts.loadIndicatorOff(fadeBackground);
                            }
                        }
                    );
                }
            );
        };

        /**
         * This function used for show spinner for go to the page on click event.
         *
         * @function updatePaginationLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updatePaginationLinks = function () {
            $('#numlinesbar').off('change.MainAppScripts').on('change.MainAppScripts', _changeNumLines);
            $('.btn-go-to-page').off('click.MainAppScripts').on('click.MainAppScripts', _gotoPage);
            $('#gotopagebar').off('keyup.MainAppScripts').on(
                'keyup.MainAppScripts',
                function (e) {
                    if (e.keyCode !== 13) {
                        return;
                    }

                    $('.btn-go-to-page').trigger('click.MainAppScripts');
                }
            );
        };

        /**
         * This function used for bind Twitter Bootstrap Tooltips.
         * Selector: `[data-toggle="tooltip"], [data-toggle][title], [data-tooltip-text]`.
         * Attributes:
         *  `data-tooltip-text` - Default title value if title attribute isn't present.
         *
         * @function updateTooltips
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Tooltips
         * @see      {@link http://getbootstrap.com/javascript/#tooltips} Twitter Bootstrap Tooltips
         *
         * @returns {boolean}
         */
        MainAppScripts.updateTooltips = function () {
            return _updateTooltips('[data-toggle="tooltip"], [data-toggle][title], [data-tooltip-text]');
        };

        /**
         * This function used for prepare checkbox and radio input for use
         *  Awesome Bootstrap Checkbox.
         * Selector: `label [type=checkbox], label [type=radio]`.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateCheckbox
         * @memberof MainAppScripts
         * @see      {@link https://github.com/flatlogic/awesome-bootstrap-checkbox} Awesome Bootstrap Checkbox
         *
         * @returns {boolean}
         */
        MainAppScripts.updateCheckbox = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            var targetBlockNext        = null;
            var targetBlockChild       = null;
            var targetBlockNextLength  = null;
            var targetBlockChildLength = null;
            var targetItem             = null;
            var parentEl               = null;
            var prevEl                 = null;
            var moveEl                 = null;

            targetBlockNext        = $('label + .checkbox');
            targetBlockChild       = $('label [type=checkbox], label [type=radio]');
            targetBlockNextLength  = targetBlockNext.length;
            targetBlockChildLength = targetBlockChild.length;

            if (returnCount) {
                return targetBlockNextLength + targetBlockChildLength;
            }

            if (targetBlockNextLength > 0) {
                targetBlockNext.each(
                    function (i, el) {
                        targetItem = $(el);
                        prevEl     = targetItem.prev();
                        moveEl     = prevEl.detach();
                        moveEl.appendTo(targetItem);
                        MainAppScripts.processUIReadyCounter('Checkbox');
                    }
                );
            }

            if (targetBlockChildLength > 0) {
                targetBlockChild.each(
                    function (i, el) {
                        targetItem = $(el);
                        parentEl   = targetItem.parent();
                        moveEl     = targetItem.detach();
                        moveEl.insertBefore(parentEl);
                        MainAppScripts.processUIReadyCounter('Checkbox');
                    }
                );
            }

            return true;
        };

        /**
         * This function used for bind Twitter Bootstrap Tree View.
         * Selector: `[data-toggle="treeview"]`.
         * Attributes:
         *  `data-treeview-url` - URL for loading data;
         *  `data-treeview-data` - data for build tree;
         *  `data-treeview-enablelinks` - whether or not to present
         *   node text as a hyperlink;
         *  `data-treeview-showtags` - whether or not to display
         *   tags to the right of each node;
         *  `data-treeview-levels` - sets the number of hierarchical
         *   levels deep the tree will be expanded to by default.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateTreeview
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Tree View
         * @see      {@link https://github.com/jonmiles/bootstrap-treeview} Tree View for Twitter Bootstrap
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateTreeview = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.treeview) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="treeview"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlock.length === 0) {
                return true;
            }

            var targetItem      = null;
            var urlData         = null;
            var enableLinksData = null;
            var showTagsData    = null;
            var levelsData      = null;
            var treeData        = null;
            var enableLinks     = false;
            var showTags        = false;
            var levels          = 1;
            targetBlock.each(
                function (i, el) {
                    targetItem  = $(el);
                    enableLinks = false;
                    showTags    = false;
                    levels      = 1;

                    treeData = $(targetItem).data('treeview-data');
                    if (!treeData) {
                        urlData = $(targetItem).data('treeview-url');
                        if (urlData) {
                            treeData = _getTreeData(urlData);
                        }
                    }

                    if (!treeData || (typeof treeData !== 'object')
                        || (treeData.length === 0)
                    ) {
                        return true;
                    }

                    enableLinksData = $(targetItem).data('treeview-enablelinks');
                    if (enableLinksData) {
                        enableLinks = true;
                    }

                    showTagsData = $(targetItem).data('treeview-showtags');
                    if (showTagsData) {
                        showTags = true;
                    }

                    levelsData = $(targetItem).data('treeview-levels');
                    if (levelsData) {
                        levels = parseInt(levelsData, 10);
                        if (isNaN(levels)) {
                            levels = 1;
                        }
                    }

                    targetItem.treeview(
                        {
                            data: treeData,
                            enableLinks: enableLinks,
                            showBorder: false,
                            showTags: showTags,
                            levels: levels,
                            selectable: true,
                            highlightSelected: false
                        }
                    );
                    MainAppScripts.processUIReadyCounter('Treeview');
                }
            );

            return true;
        };

        /**
         * This function used for bind Tree View jQuery Bonsai.
         * Selector: `.bonsai-treeview, [data-toggle="bonsai-treeview"]`.
         * Attributes:
         *  `data-id` - ID of tree iteml.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateBonsaiTreeview
         * @memberof MainAppScripts
         * @requires Tree View jQuery Bonsai
         * @see      {@link https://github.com/aexmachina/jquery-bonsai} Tree View jQuery Bonsai
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateBonsaiTreeview = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.bonsai) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('.bonsai-treeview, [data-toggle="bonsai-treeview"] > ul, [data-toggle="bonsai-treeview"] > ol');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlock.length === 0) {
                return true;
            }

            var targetItem = null;
            var bonsai     = null;
            var expandAll  = true;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    expandAll  = false;
                    if ((targetItem.parent('[data-toggle="bonsai-treeview"][data-bonsai-expand-all]').length > 0)
                        || (targetItem.hasClass('bonsai-expand-all'))
                    ) {
                          expandAll = true;
                    }

                    bonsai = targetItem.bonsai(
                        {
                            idAttribute: 'data-id',
                            expandAll: expandAll
                        }
                    ).data('init', true).data('bonsai');
                    MainAppScripts.processUIReadyCounter('BonsaiTreeview');
                }
            );

            return true;
        };

        /**
         * This function used for bind jQuery Sortable for drag and drop items.
         * Selector: `[data-toggle="draggable"] > ul, [data-toggle="draggable"] > ol,
         *  [data-toggle="draggable"] > table`.
         * Attributes:
         *  `data-vertical` - If true, the items are assumed to be arranged vertically;
         *  `data-nested` - If true, search for nested containers within an item.
         *    If you nest containers, either the original selector with which you call
         *    the plugin must only match the top containers, or you need to specify a group.
         *  `data-pull-placeholder` - If true, the position of the placeholder is
         *    calculated on every mousemove. If false, it is only calculated when the
         *  `data-change-parent` - If true, allowed change parent. Otherwise it is forbidden.
         *
         * @function updateSortable
         * @memberof MainAppScripts
         * @requires jQuery Sortable
         * @see      {@link https://github.com/johnny/jquery-sortable} jQuery Sortable
         *
         * @returns {boolean}
         */
        MainAppScripts.updateSortable = function () {
            if (!jQuery.fn.sortable) {
                return false;
            }

            var targetBlock = $('[data-toggle="draggable"] > ul, [data-toggle="draggable"] > ol, [data-toggle="draggable"] > table');
            if (targetBlock.length === 0) {
                return true;
            }

            var containerSelector  = '';
            var itemPath           = '';
            var itemSelector       = '';
            var placeholder        = '';
            var verticalDef        = true;
            var nestedDef          = true;
            var pullPlaceholderDef = true;
            var changeParentDef    = true;
            var groupClass         = '';
            var opt                = {
                handle: '[data-toggle="drag"]',
                delay: 500,
                tolerance: 0,
                distance: 0,
                onDrop: _sortableOnDrop,
                onDragStart: _sortableOnDragStart
            };
            var optFull            = {};
            var optElem            = {};
            var isTable            = false;

            var targetItem = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    groupClass = _getUniqueID('main-app-scripts-draggable');

                    var vertical = targetItem.parent().data('vertical');
                    if (typeof(vertical) === 'undefined') {
                        vertical = verticalDef;
                    }

                    var nested = targetItem.parent().data('nested');
                    if (typeof(nested) === 'undefined') {
                        nested = nestedDef;
                    }

                    var pullPlaceholder = targetItem.parent().data('pull-placeholder');
                    if (typeof(pullPlaceholder) === 'undefined') {
                        pullPlaceholder = pullPlaceholderDef;
                    }

                    var changeParent = targetItem.parent().data('change-parent');
                    if (typeof(changeParent) === 'undefined') {
                        changeParent = changeParentDef;
                    }

                    isTable = false;
                    if (targetItem.prop('tagName').toLowerCase() === 'table') {
                        isTable = true;
                    }

                    if (isTable) {
                        containerSelector = 'table';
                        itemPath          = '> tbody';
                        itemSelector      = 'tr';
                        placeholder       = '<tr class="placeholder"/>';
                        nested            = false;
                    } else {
                        containerSelector = 'ol, ul';
                        itemPath          = '';
                        itemSelector      = 'li';
                        placeholder       = '<li class="placeholder"></li>';
                    }

                    optElem = {
                        vertical: vertical,
                        nested: nested,
                        group: groupClass,
                        pullPlaceholder: pullPlaceholder,
                        containerSelector: containerSelector,
                        itemPath: itemPath,
                        itemSelector: itemSelector,
                        placeholder: placeholder,
                        isValidTarget: function ($item, container) {
                            if (changeParent) {
                                return true;
                            }

                            var parentDom = $item.parent().get(0);
                            var containerDom = container.el.get(0);
                            if (parentDom === containerDom) {
                                return true;
                            }

                            return false;
                        }
                    };
                    optFull = $.extend({}, opt, optElem);
                    targetItem.attr('class', targetItem.attr('class').replace(/\bdraggable_\d+\b/g, ''));
                    targetItem.addClass('draggable');
                    targetItem.addClass(groupClass);
                    targetItem.sortable('destroy').sortable(optFull);
                }
            );

            return true;
        };

        /**
         * This function used for bind Twitter Bootstrap Dropdowns.
         * Selector: `.dropdown-toggle`.
         *
         * @function updateDropdowns
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Dropdowns
         * @see      {@link http://getbootstrap.com/javascript/#dropdowns} Twitter Bootstrap Dropdowns
         *
         * @returns {boolean}
         */
        MainAppScripts.updateDropdowns = function () {
            if (typeof $().dropdown !== 'function') {
                return false;
            }

            $('.dropdown-toggle').dropdown();

            return true;
        };

        /**
         * This function used for bind Twitter Bootstrap Togglable tabs.
         * Selector: `[data-toggle="tab"]`.
         *
         * @function updateTabs
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Togglable tabs
         * @see      {@link http://getbootstrap.com/javascript/#tabs} Twitter Bootstrap Togglable tabs
         *
         * @returns {boolean}
         */
        MainAppScripts.updateTabs = function () {
            if (typeof $().tab !== 'function') {
                return false;
            }

            var target = $('[data-toggle="tab"]');
            if (target.length === 0) {
                return true;
            }

            target.tab();
            target.off('show.bs.tab').on(
                'show.bs.tab',
                function (e) {
                    var href = $(e.target).attr('href');
                    if (href) {
                        $('html').data('active-tab', href);
                    }
                }
            );
            target.off('shown.bs.tab').on(
                'shown.bs.tab',
                function (e) {
                    MainAppScripts.setInputFocus();
                }
            );
            var activeTab = $('html').data('active-tab');
            if (activeTab) {
                $('a[data-toggle="tab"][href="' + activeTab + '"]').tab('show');
            }

            return true;
        };

        /**
         * This function used for bind Bootstrap select.
         * Selector: `[data-toggle="select"]`.
         * Attributes:
         *  `data-placeholder` - The default title for the selectpicker;
         *  `data-abs-ajax-url` - URL for loading select options;
         *  `data-abs-min-length` - Invoke a request for empty search values.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateSelect
         * @memberof MainAppScripts
         * @requires Bootstrap select,Ajax Bootstrap Select
         * @see      {@link https://github.com/silviomoreto/bootstrap-select} Bootstrap select,
         *  {@link https://github.com/truckingsim/Ajax-Bootstrap-Select} Ajax Bootstrap Select
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateSelect = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.selectpicker) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="select"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var targetItem         = null;
            var wrapElem           = null;
            var moveEl             = null;
            var title              = null;
            var style              = null;
            var size               = null;
            var liveSearch         = null;
            var showTick           = null;
            var selectedTextFormat = null;
            var placeholder        = null;
            var defaultSize        = 8;
            var multiple           = false;
            var dropdownAlignRight = 'auto';
            var absAjaxUrl         = null;
            var minLength          = null;
            var defaultMinLength   = 0;
            var useAjaxSelect      = true;
            if (!jQuery.fn.ajaxSelectPicker) {
                useAjaxSelect = false;
            }

            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap select is binded
                    if (targetItem.data('selectpicker')) {
                        MainAppScripts.processUIReadyCounter('Select');
                        return true;
                    }

                    // ! Remove wrap DIV for history back action
                    wrapElem = targetItem.closest('div.bootstrap-select');
                    if (wrapElem.length === 1) {
                        moveEl = targetItem.detach();
                        moveEl.insertAfter(wrapElem);
                        wrapElem.remove();
                    }

                    style              = targetItem.data('style');
                    size               = targetItem.data('size');
                    liveSearch         = targetItem.data('live-search');
                    showTick           = targetItem.data('show-tick');
                    selectedTextFormat = targetItem.data('selected-text-format');
                    absAjaxUrl         = targetItem.data('abs-ajax-url');
                    minLength          = targetItem.data('abs-min-length');
                    multiple           = false;
                    if (targetItem.attr('multiple') === 'multiple') {
                        multiple = true;
                    }

                    if (typeof liveSearch === 'undefined') {
                        liveSearch = false;
                        if (targetItem.find('option').length > defaultSize) {
                            liveSearch = true;
                        }
                    }

                    if (absAjaxUrl && useAjaxSelect) {
                        liveSearch = true;
                    }

                    if (typeof showTick === 'undefined') {
                        showTick = true;
                    }

                    if (!selectedTextFormat) {
                        if (multiple) {
                            selectedTextFormat = 'count > 10';
                        } else {
                            selectedTextFormat = 'value';
                        }
                    }

                    if (!size) {
                        size = defaultSize;
                    }

                    if (!style) {
                        style = 'btn-default';
                    }

                    if (multiple) {
                        style += ' multiple-value-select';
                    }

                    title = targetItem.attr('title');
                    if (title) {
                        targetItem.removeAttr('title');
                    }

                    if (minLength) {
                        targetItem.removeAttr('data-abs-min-length');
                    } else {
                        minLength = defaultMinLength;
                    }

                    placeholder = targetItem.data('placeholder');
                    targetItem.selectpicker('destroy').selectpicker(
                        {
                            title: placeholder,
                            style: style,
                            size: size,
                            liveSearch: liveSearch,
                            showTick: showTick,
                            selectedTextFormat: selectedTextFormat,
                            dropdownAlignRight: dropdownAlignRight
                        }
                    ).off('loaded.bs.select').on(
                        'loaded.bs.select',
                        function (e) {
                            MainAppScripts.processUIReadyCounter('Select');
                        }
                    ).off('changed.bs.select').on(
                        'changed.bs.select',
                        function (e) {
                            var btnSelect = $(e.target).siblings('button');
                            if (btnSelect.length !== 1) {
                                return;
                            }

                            var title = btnSelect.attr('title');
                            if (!title) {
                                return;
                            }

                            btnSelect.attr('data-original-title', title);
                            btnSelect.tooltip('fixTitle');
                        }
                    );
                    if (title) {
                        targetItem.siblings('button[data-toggle="dropdown"]').attr('title', title);
                    }

                    if (absAjaxUrl && useAjaxSelect) {
                        targetItem.ajaxSelectPicker({ minLength: minLength });
                    }
                }
            );

            return true;
        };

        /**
         * This function used build progress bar of filling form inputs.
         * Selector: `form[progressfill!=""][progressfill]`.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateInputsFilledProgressBar
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Progress bars
         * @see      {@link http://getbootstrap.com/components/#progress} Twitter Bootstrap Progress bars
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateInputsFilledProgressBar = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            var progressTemplate  = '<div class="progress">\
                    <div class="progress-bar inputs-filled-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>\
                </div>';
            var targetBlock       = $('form[progressfill!=""][progressfill]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            var targetItem         = null;
            var inputObj           = null;
            var inputCount         = 0;
            var inputRequiredCount = 0;
            var progressObj        = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap progress bar is binded
                    if (targetItem.data('inputsFilledProgress')) {
                        MainAppScripts.processUIReadyCounter('InputsFilledProgressBar');
                        return true;
                    }

                    inputObj           = targetItem.find(':input:not(:button,[type=hidden],[type=checkbox],[type=radio],[readonly])').filter('[name^="data["]');
                    inputCount         = inputObj.length;
                    inputRequiredCount = inputObj.filter('[required]').length;
                    progressObj        = targetItem.find('[role="progressbar"].inputs-filled-progress');
                    if (progressObj.length === 0) {
                        progressObj = targetItem.prepend(progressTemplate).find('[role="progressbar"].inputs-filled-progress');
                    }

                    progressObj.data('total-inputs', inputCount);
                    progressObj.data('total-required-inputs', inputRequiredCount);
                    inputObj.off('change.MainAppScripts.FillProgr').on('change.MainAppScripts.FillProgr', _setProgressFillInputs);
                    inputObj.trigger('change.MainAppScripts.FillProgr');
                    targetItem.data('inputsFilledProgress', true);
                    MainAppScripts.processUIReadyCounter('InputsFilledProgressBar');
                }
            );

            return true;
        };

        /**
         * This function used for checking required form inputs
         *  and display error message if input is empty.
         * Selector: `form[requiredcheck!=""][requiredcheck]`.
         *
         * @function updateRequiredInputsForm
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        MainAppScripts.updateRequiredInputsForm = function () {
            var targetBlock = $('form[requiredcheck!=""][requiredcheck]');
            if (targetBlock.length === 0) {
                return false;
            }

            var targetItem = null;
            var inputObj   = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    inputObj   = targetItem.find(':input[name^="data["][required]');
                    inputObj.off('change.MainAppScripts.RequirInput').on('change.MainAppScripts.RequirInput', _setRequiredInputsMessage);
                    targetItem.find(':submit').off('click.MainAppScripts.RequirInput').on('click.MainAppScripts.RequirInput', _submitFormWithRequiredInputs);
                }
            );

            return true;
        };

        /**
         * This function used for bind Twitter Bootstrap Popovers.
         * Selector: `[data-toggle="popover"],[data-toggle="modal-popover"]`.
         * Attributes:
         *  `data-popover-url` - URL for loading content;
         *  `data-popover-placement` - position of popover;
         *  `data-popover-title` - title of popover.
         *  `data-popover-size` - size of popover: `lg`.
         *
         * @function updatePopover
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Popovers
         * @see      {@link http://getbootstrap.com/javascript/#popovers} Twitter Bootstrap Popovers
         *
         * @returns {boolean}
         */
        MainAppScripts.updatePopover = function () {
            if (typeof $().popover !== 'function') {
                return false;
            }

            var targetBlock = $('[data-toggle="popover"],[data-toggle="modal-popover"]');
            if (targetBlock.length === 0) {
                return true;
            }

            var targetItem = null;
            var title      = null;
            var size       = null;
            var placement  = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    title      = $(this).data('popover-title');
                    size       = $(this).data('popover-size');
                    placement  = $(this).data('popover-placement');
                    if (!placement) {
                        placement = 'right';
                    }
                    targetItem.popover(
                        {
                            content: _getPopoverContent,
                            delay: {
                                'show': 1000,
                                'hide': 100
                            },
                            html: true,
                            placement: placement,
                            container: _contentContainer,
                            template: _getPopoverTemplate(size),
                            title: title,
                            trigger: 'hover'
                        }
                    ).off('inserted.bs.popover').on(
                        'inserted.bs.popover',
                        function (e) {
                            _moveTooltip(e);
                            MainAppScripts.update();
                        }
                    );
                }
            );

            return true;
        };

        /**
         * This function used for bind Twitter Bootstrap Modals.
         * Selector: `a[data-toggle="modal"], a[data-toggle="modal-popover"], [data-toggle="modal"] a`.
         * Attributes:
         *  `data-modal-title` - title of modal window;
         *  `data-modal-size` - `sm`|`lg` - size of modal window;
         *  `data-modal-method` - `get`|`post` - method for request: GET or POST;
         *  `data-disable-use-stack` - disabling use stack of madal window.
         *
         * @function updateModalLinks
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap Modals
         * @see      {@link http://getbootstrap.com/javascript/#modals} Twitter Bootstrap Modals
         *
         * @returns {boolean}
         */
        MainAppScripts.updateModalLinks = function () {
            if (typeof $().modal !== 'function') {
                return false;
            }

            $('a[data-toggle="modal"], a[data-toggle="modal-popover"], [data-toggle="modal"] a').off('click.MainAppScripts.modal').on('click.MainAppScripts.modal', _showModalWindowClick);

            return true;
        };

        /**
         * This function used for bind Caps lock check jQuery plugin.
         * Selector: `:password`.
         *
         * @function updatePassField
         * @memberof MainAppScripts
         * @requires jQuery.CapsLockAlert
         *
         * @returns {boolean}
         */
        MainAppScripts.updatePassField = function () {
            if (!jQuery.fn.CapsLockAlert) {
                return false;
            }

            $(':password').CapsLockAlert();

            return true;
        };

        /**
         * This function is used to bind mouseup event to button for
         *  reset focus.
         *
         * @function updateButtons
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateButtons = function () {
            $('.btn').off('mouseup.MainAppScripts').on(
                'mouseup.MainAppScripts',
                function () {
                    $(this).blur();
                }
            );
        };

        /**
         * This function used for bind Twitter Bootstrap TouchSpin
         * Selector: `[data-toggle="spin"]`.
         * Attributes:
         *  `data-spin-initval` - Applied when no explicit value is set on the
         *  input with the value attribute. Empty string means that the value
         *  remains empty on initialization.
         *  `data-spin-min` - Minimum value;
         *  `data-spin-max` - Maximum value;
         *  `data-spin-step` - Incremental/decremental step on up/down change;
         *  `data-spin-decimals` - Number of decimal points;
         *  `data-spin-maxboostedstep` - Maximum step when boosted;
         *  `data-spin-verticalbuttons` -      Enables the traditional up/down buttons;
         *  `data-spin-prefix` - Text before the input;
         *  `data-spin-prefix_extraclass` - Extra class(es) for prefix;
         *  `data-spin-postfix` - Text after the input;
         *  `data-spin-postfix_extraclass` - Extra class(es) for postfix.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateSpin
         * @memberof MainAppScripts
         * @requires Bootstrap.TouchSpin
         * @see      {@link http://www.virtuosoft.eu/code/bootstrap-touchspin} TouchSpin
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateSpin = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.TouchSpin) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="spin"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var verticalbuttonsDef = true;
            var targetItem         = null;
            var wrapElem           = null;
            var moveEl             = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap TouchSpin is binded
                    if (targetItem.data('spinnerid')) {
                        MainAppScripts.processUIReadyCounter('Spin');
                        return true;
                    }

                    // ! Remove wrap DIV for history back action
                    wrapElem = targetItem.closest('div.bootstrap-touchspin');
                    if (wrapElem.length === 1) {
                        moveEl = targetItem.detach();
                        moveEl.insertAfter(wrapElem);
                        wrapElem.remove();
                    }

                    var initval = targetItem.data('spin-initval');
                    if (typeof initval === 'undefined') {
                        initval = '';
                    }

                    var min = targetItem.data('spin-min');
                    if (!min) {
                        min = 0;
                    }

                    var max = targetItem.data('spin-max');
                    if (!max) {
                        max = 100;
                    }

                    var step = targetItem.data('spin-step');
                    if (!step) {
                        step = 1;
                    }

                    var decimals = targetItem.data('spin-decimals');
                    if (!decimals) {
                        decimals = 0;
                    }

                    var maxboostedstep = targetItem.data('spin-maxboostedstep');
                    if (!maxboostedstep) {
                        maxboostedstep = 10;
                    }

                    var verticalbuttons = targetItem.data('spin-verticalbuttons');
                    if (typeof verticalbuttons === 'undefined') {
                        verticalbuttons = verticalbuttonsDef;
                    }

                    var prefix = targetItem.data('spin-prefix');
                    if (typeof prefix === 'undefined') {
                        prefix = '';
                    }

                    var prefix_extraclass = targetItem.data('spin-prefix_extraclass');
                    if (typeof prefix_extraclass === 'undefined') {
                        prefix_extraclass = '';
                    }

                    var postfix = targetItem.data('spin-postfix');
                    if (typeof postfix === 'undefined') {
                        postfix = '';
                    }

                    var postfix_extraclass = targetItem.data('spin-postfix_extraclass');
                    if (typeof postfix_extraclass === 'undefined') {
                        postfix_extraclass = '';
                    }

                    targetItem.TouchSpin('destroy');
                    targetItem.TouchSpin(
                        {
                            initval: initval,
                            min: min,
                            max: max,
                            step: step,
                            decimals: decimals,
                            maxboostedstep: maxboostedstep,
                            verticalbuttons: verticalbuttons,
                            prefix: prefix,
                            prefix_extraclass: prefix_extraclass,
                            postfix: postfix,
                            postfix_extraclass: postfix_extraclass
                        }
                    );
                    MainAppScripts.processUIReadyCounter('Spin');
                }
            );

            return true;
        };

        /**
         * This function used for bind ajax submiting form.
         * Selector: `[data-toggle="ajax-form"]`.
         * Attributes:
         *  `fade-page` - full fade page before submit form.
         *
         * @function updateAjaxForm
         * @memberof MainAppScripts
         * @requires jQuery.Form
         * @see      {@link http://malsup.com/jquery/form} jQuery.Form
         *
         * @returns {boolean}
         */
        MainAppScripts.updateAjaxForm = function () {
            if (!jQuery.fn.ajaxSubmit) {
                return false;
            }

            $('[data-toggle="ajax-form"]').off('submit.MainAppScripts').on(
                'submit.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    $(this).ajaxSubmit(
                        {
                            beforeSubmit: function (arr, $form, options) {
                                var fadeBackground     = true;
                                var fullFadeBackground = false;
                                if ($form.attr('fade-page')) {
                                    fullFadeBackground = true;
                                }

                                $form.data('fade-background', fadeBackground);
                                MainAppScripts.loadIndicatorOn(fadeBackground, fullFadeBackground);
                            },
                            success: _ajaxFormResponse,
                            error: _ajaxFormError
                        }
                    );
                }
            );

            return true;
        };

        /**
         * This function used for bind pjax submiting form.
         * Selector: `[data-toggle="pjax-form"]`.
         * Attributes:
         *  `fade-page` - full fade page before submit form.
         *
         * @function updatePjaxForm
         * @memberof MainAppScripts
         * @requires jQuery.Pjax
         * @see      {@link https://github.com/defunkt/jquery-pjax} PJAX
         *
         * @returns {boolean}
         */
        MainAppScripts.updatePjaxForm = function () {
            if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                return false;
            }

            $(document).off('submit.MainAppScripts').on(
                'submit.MainAppScripts',
                '[data-toggle="pjax-form"]',
                function (e) {
                    var fadeBackground     = true;
                    var fullFadeBackground = false;
                    var form               = $(this);
                    var options            = {};
                    if (form.attr('fade-page')) {
                        fullFadeBackground = true;
                    }

                    // ! CLX6-215 filter all empty <input>&<selects>
                    if (this.method.toUpperCase() === 'GET') {
                        var action = form.attr('action');
                        if (action) {
                            form.attr('action', action.replace(/\?.*/, ''));
                        }

                        options.data = form.find(':input').filter(
                            function () {
                                return $(this).val() !== '';
                            }
                        ).serializeArray();
                    }

                    MainAppScripts.loadIndicatorOn(fadeBackground, fullFadeBackground);
                    $.pjax.submit(e, _contentContainerPjax, options);
                }
            );

            return true;
        };

        /**
         * This function used for bind input mask.
         * Selectors:
         *  `[data-toggle="input-mask-mask"],[data-inputmask-mask],[data-inputmask-alias]` -
         *   default Inputmask;
         *  `[data-toggle="input-mask-regex"],[data-inputmask-regex]` - Inputmask regex.
         *
         * @function updateInputMask
         * @memberof MainAppScripts
         * @requires jQuery.Inputmask
         * @see      {@link https://github.com/RobinHerbots/jquery.inputmask} jQuery.Inputmask
         *
         * @returns {boolean}
         */
        MainAppScripts.updateInputMask = function () {
            if (!jQuery.fn.inputmask) {
                return false;
            }

            var extendAliases = {
                'integer': {
                    rightAlign: false
                }
            };
            Inputmask.extendDefaults(
                {
                    clearIncomplete: true
                }
            );
            Inputmask.extendAliases(extendAliases);

            $('[data-toggle="input-mask-mask"],[data-inputmask-mask],[data-inputmask-alias]').inputmask();
            $('[data-toggle="input-mask-regex"],[data-inputmask-regex]').inputmask('Regex');

            return true;
        };

        /**
         * This function used to clone DOM element.
         * Selector: `[data-toggle="clone-target"], .clone-wrapper`.
         * Attributes:
         *  `max-clone` - The maximum number of clones allowed;
         *  `one-clone-button` - Use only one button `Add` and `Remove`.
         * Additional selector:
         *  `[data-toggle="clone-source"]` - Element that'll be cloned. Must be
         *   a child of the element that CloneYa was instantiated on.;
         *  `[data-toggle="btn-action-clone"]` - Element that triggers cloning. Must be
         *   a child of the `[data-toggle="clone-source"]` selector;
         *  `[data-toggle="btn-action-delete"]` - Element that triggers clone deletion.
         *   Must be a child of the `[data-toggle="clone-source"]` selector;
         *  `.exclude-clone` - Elements that will not be copied (i.e. ignored) into the new clone.
         *
         * @function updateCloneDOMelements
         * @memberof MainAppScripts
         * @requires jQuery.Cloneya
         * @see      {@link https://github.com/Yapapaya/jquery-cloneya} jQuery.Cloneya
         *
         * @returns {boolean}
         */
        MainAppScripts.updateCloneDOMelements = function () {
            if (!jQuery.fn.cloneya) {
                return false;
            }

            var targetBlock = $('[data-toggle="clone-target"], .clone-wrapper');
            if (targetBlock.length === 0) {
                return true;
            }

            var targetItem     = null;
            var oneCloneButton = false;
            var maxClone       = 1;
            var clonePosition  = '';
            var fadeBackground = true;
            targetBlock.each(
                function (i, el) {
                    targetItem     = $(el);
                    clonePosition  = 'after';
                    oneCloneButton = targetItem.data('one-clone-button');
                    if (oneCloneButton) {
                        targetItem.find('[data-toggle="btn-action-clone"]:first').removeClass('hidden');
                        targetItem.find('[data-toggle="btn-action-clone"]:not(:first)').addClass('hidden');
                        targetItem.find('[data-toggle="btn-action-delete"]:first').addClass('hidden');
                        targetItem.find('[data-toggle="btn-action-delete"]:not(:first)').removeClass('hidden');
                        clonePosition = 'before';
                    }

                    maxClone = parseInt(targetItem.data('max-clone'), 10);
                    if (isNaN(maxClone)) {
                        maxClone = 8;
                    }

                    targetItem.cloneya(
                        {
                            minimum: 1,
                            maximum: maxClone,
                            cloneThis: '[data-toggle="clone-source"]',
                            valueClone: false,
                            dataClone: false,
                            deepClone: false,
                            cloneButton: '[data-toggle="btn-action-clone"]',
                            deleteButton: '[data-toggle="btn-action-delete"]',
                            clonePosition: clonePosition,
                            serializeID: true,
                            serializeIndex: true,
                            ignore: '.exclude-clone',
                            preserveChildCount: false
                        }
                    ).off('before_append.cloneya.MainAppScripts').on(
                        'before_append.cloneya.MainAppScripts',
                        function (event, toclone, newclone) {
                            $(newclone).find('[data-toggle="btn-action-delete"]').removeClass('hidden');
                        }
                    ).off('after_append.cloneya.MainAppScripts').on(
                        'after_append.cloneya.MainAppScripts',
                        function (event, toclone, newclone) {
                            MainAppScripts.update();
                        }
                    ).off('before_delete.cloneya.MainAppScripts').on(
                        'before_delete.cloneya.MainAppScripts',
                        function (event, toDelete, cloneCount) {
                            if (typeof $().tooltip !== 'function') {
                                return;
                            }

                            $(toDelete).find('[data-toggle="btn-action-delete"]').tooltip('destroy');
                        }
                    ).off('after_delete.cloneya.MainAppScripts').on(
                        'after_delete.cloneya.MainAppScripts',
                        function (event, toDelete, cloneCount) {
                            MainAppScripts.update();
                        }
                    );
                }
            );

            return true;
        };

        /**
         * This function used for bind click event to buttons
         *  `expand` and `roll up` text.
         * Selector: `[data-toggle="collapse-text-expand"], [data-toggle="collapse-text-roll-up"]`.
         *
         * @function updateExpandTruncatedText
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        MainAppScripts.updateExpandTruncatedText = function () {
            var targetExpandBtn = $('[data-toggle="collapse-text-expand"], [data-toggle="collapse-text-roll-up"]');
            if (targetExpandBtn.length === 0) {
                return true;
            }

            var targetExpandBlock = $('.collapse-text-expanded:has([data-toggle="collapse-text-expand"], [data-toggle="collapse-text-roll-up"])');
            if (targetExpandBlock.length > 0) {
                var blockWidth = 0;
                targetExpandBlock.each(
                    function (i, el) {
                        blockWidth = $(el).outerWidth();
                        $(el).css({ 'max-width': blockWidth });
                    }
                );
            }

            targetExpandBtn.off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var target = $(e.currentTarget);
                    var parent = target.parents('.collapse-text-expanded');
                    if (parent.length === 0) {
                        return false;
                    }

                    var toggle  = target.attr('data-toggle');
                    var hideSel = '';
                    var showSel = '';
                    switch (toggle) {
                        case 'collapse-text-expand':
                            hideSel = '.collapse-text-truncated';
                            showSel = '.collapse-text-original';
                        break;

                        case 'collapse-text-roll-up':
                            hideSel = '.collapse-text-original';
                            showSel = '.collapse-text-truncated';
                        break;

                        default:
                        return false;
                    }

                    parent.children(showSel).show();
                    parent.children(hideSel).hide();
                    return false;
                }
            );

            return true;
        };

        /**
         * This function used for showing modal window.
         *
         * @param {string} url URL for contenet modal window
         * @param {string} title Title of modal window
         * @param {string} size Size of modal window: `sm` or `lg`
         * @param {string} method Method for request: GET or POST
         *  (default GET)
         * @param {boolean} disableUseStack Disabling use stack of madal window
         *
         * @function openModalWindow
         * @memberof MainAppScripts
         *
         * @returns {boolean}
         */
        MainAppScripts.openModalWindow = function (url, title, size, method, disableUseStack) {
            if (typeof $().modal !== 'function') {
                return false;
            }

            _showModalWindow(url, title, size, method, disableUseStack);

            return true;
        };

        /**
         * This function used for display loading indicator or progress bar.
         *
         * @param {boolean} fadeBackground If True - fade background.
         *  Default - False.
         * @param {boolean} fullFadeBackground If True - full fade background.
         *  Default - False.
         *
         * @function loadIndicatorOn
         * @memberof MainAppScripts
         * @requires NProgress
         * @see      {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {null}
         */
        MainAppScripts.loadIndicatorOn = function (fadeBackground, fullFadeBackground) {
            if (typeof fadeBackground === 'undefined') {
                fadeBackground = false;
            }

            if (typeof fullFadeBackground === 'undefined') {
                fullFadeBackground = false;
            }

            if (typeof NProgress === 'undefined') {
                if ($('div.mainappscripts-ds-loading').length > 0) {
                    return;
                }

                $('body').append('<div class="mainappscripts-ds-loading"></div>');
            } else {
                if (NProgress.status === null) {
                    NProgress.configure(
                        {
                            trickle: true,
                            showSpinner: false,
                            minimum: 0.01
                        }
                    );
                    NProgress.start();
                    NProgress.inc();
                }
            }

            if (fadeBackground) {
                _fadeBackgroundOn(fullFadeBackground);
            }
        };

        /**
         * This function used for hide loading indicator or progress bar.
         *
         * @param {boolean} fadeBackground If True - remove fade background.
         *  Default - False.
         *
         * @function loadIndicatorOff
         * @memberof MainAppScripts
         * @requires NProgress
         * @see      {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {null}
         */
        MainAppScripts.loadIndicatorOff = function (fadeBackground) {
            if (typeof fadeBackground === 'undefined') {
                fadeBackground = false;
            }

            if (typeof NProgress === 'undefined') {
                $('div.mainappscripts-ds-loading').detach();
            } else {
                if (NProgress.status !== null) {
                    NProgress.done();
                }
            }

            if (fadeBackground) {
                _fadeBackgroundOff();
            }
        };

        /**
         * This function used for register loading indicator or progress bar
         *  for AJAX requset.
         *
         * @param {boolean} fadeBackground If True - fade background.
         *  Default - False.
         *
         * @function ajaxIndicator
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.ajaxIndicator = function (fadeBackground) {
            if (typeof fadeBackground === 'undefined') {
                fadeBackground = false;
            }

            $(document).ajaxStart(
                function () {
                    MainAppScripts.loadIndicatorOn(fadeBackground);
                }
            );
            $(document).ajaxStop(
                function () {
                    MainAppScripts.loadIndicatorOff(fadeBackground);
                }
            );
        };

        /**
         * This function used for confirm action click.
         * Selector: `a[data-confirm-msg!=""][data-confirm-msg]`.
         * Attributes:
         *  `data-confirm-msg` - Message for confirmation dialog;
         *  `data-confirm-btn-ok` - label for `Ok` button;
         *  `data-confirm-btn-cancel` - label for `Cancel` button.
         *
         * @function updateConfirmActionLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateConfirmActionLinks = function () {
            _preparePostLink();
            $('a[data-confirm-msg!=""][data-confirm-msg]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    MainAppScripts.confirmAction(this);
                    return true;
                }
            );
        };

        /**
         * This function used for POST requset on click link.
         * Selector: `a[role="post-link"]:not([data-confirm-msg])`.
         *
         * @function updatePostLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updatePostLinks = function () {
            _preparePostLink();
            $('a[role="post-link"]:not([data-confirm-msg])').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    _submitPostForm(this);
                    return false;
                }
            );
        };

        /**
         * This function used for AJAX request on server
         * Selector: `a[data-toggle="request-only"]`.
         * Attributes:
         *  `data-use-post-request` - If True, use POST request.
         *
         * @function updateRequestOnlyLinks
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateRequestOnlyLinks = function () {
            $('a[data-toggle="request-only"]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    e.preventDefault();
                    var target         = $(this);
                    var url            = target.attr('href');
                    var method         = 'GET';
                    var fadeBackground = true;
                    if (!url) {
                        return false;
                    }

                    if (target.data('use-post-request')) {
                        method = 'POST';
                    }

                    $.ajax(
                        {
                            async: true,
                            url: url,
                            method: method,
                            global: false,
                            beforeSend: function () {
                                MainAppScripts.loadIndicatorOn(fadeBackground);
                            },
                            success: function () {
                                MainAppScripts.update();
                            },
                            complete: function () {
                                MainAppScripts.loadIndicatorOff(fadeBackground);
                            }
                        }
                    );
                }
            );
        };

        /**
         * This function used for show confirm dialog.
         * Attributes:
         *  `data-confirm-msg` - Message for confirmation dialog;
         *  `data-confirm-btn-ok` - label for `Ok` button;
         *  `data-confirm-btn-cancel` - label for `Cancel` button.
         *
         * @param {object} el Target element
         * @param {requestCallback} action Callback function
         *  for submitting action
         *
         * @function confirmAction
         * @memberof MainAppScripts
         * @see      {@link http://bootboxjs.com} Bootbox,
         *  {@link https://github.com/needim/noty noty
         *
         * @returns {boolean}
         */
        MainAppScripts.confirmAction = function (el, action) {
            var msg            = $(el).data('confirm-msg');
            var labelBtnOk     = $(el).data('confirm-btn-ok');
            var labelBtnCancel = $(el).data('confirm-btn-cancel');
            if (!msg) {
                msg = 'Are you sure?';
            }

            var locale = $('html').attr('lang');
            if (!locale) {
                locale = 'en';
            }

            var callbackFunc = function (result) {
                if (!result) {
                    return false;
                }

                if (action && $.isFunction(action)) {
                    return action(el);
                }

                if (!_submitPostForm(el) && $(el).attr('href')) {
                    var url = $(el).attr('href');
                    if (url === '#') {
                        return false;
                    }

                    $(_contentContainer).load(
                        url,
                        function (responseText, textStatus, jqXHR) {
                            if (textStatus === 'success') {
                                MainAppScripts.update();
                            }
                        }
                    );
                    return false;
                }

                return false;
            };

            if (typeof Noty !== 'undefined') {
                var objNoty = new Noty(
                    {
                        text: msg,
                        closeWith: ['button'],
                        force: true,
                        modal: true,
                        layout: 'center',
                        theme: 'bootstrap-v3',
                        type: 'alert',
                        buttons: [
                            Noty.button(
                                labelBtnOk,
                                'btn btn-success',
                                function () {
                                    objNoty.close();
                                    callbackFunc(true);
                                },
                                {
                                    id: 'buttonOk',
                                    'data-status': 'ok'
                                }
                            ),
                            Noty.button(
                                labelBtnCancel,
                                'btn btn-danger',
                                function () {
                                    objNoty.close();
                                },
                                {
                                    id: 'buttonCancel',
                                    'data-status': 'cancel'
                                }
                            )
                        ],
                    callbacks: {
                        onShow: function () {
                            $('div.noty_buttons #buttonCancel', $(this.barDom)).focus();
                        },
                        onTemplate: function () {
                            $('div.noty_buttons', $(this.barDom)).addClass('text-center').css('border-top', '1px solid #e7e7e7');
                            $('div.noty_buttons button', $(this.barDom)).css('margin', '0 2px');
                        }
                        }
                    }
                ).show();
            } else if (typeof bootbox !== 'undefined') {
                bootbox.setLocale(locale);
                bootbox.confirm(
                    {
                        size: 'small',
                        message: msg,
                        buttons: {
                            cancel: {
                                label: labelBtnCancel,
                                className: 'btn-danger',
                                callback: function () {}
                            },
                            confirm: {
                                label: labelBtnOk,
                                className: 'btn-success',
                                callback: function () {}
                            }
                        },
                        callback: callbackFunc
                    }
                );
            } else {
                if (confirm(msg)) {
                    callbackFunc(true);
                }
            }//end if
        };

        /**
         * This function used for decrement counter of ready UI elements. If
         *  counter of processed UI equal counter of need  processe UI -
         *  remove fading of background and hide NProgress bar.
         *
         * @param {string} target Target element
         *
         * @function processUIReadyCounter
         * @memberof MainAppScripts
         * @requires NProgress
         * @see      {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {null}
         */
        MainAppScripts.processUIReadyCounter = function (target) {
            var controls = $('body').data('ui-ready-controls');
            if (!controls) {
                controls = {
                    'Default': 1
                };
            }

            if (!target) {
                target = 'Default';
            }

            if (!controls.hasOwnProperty(target)) {
                return;
            }

            if (controls[target] === 0) {
                return;
            }

            controls[target]--;
            var controlsCount     = 0;
            var controlsProcessed = 0;
            $.each(
                controls,
                function (controlName, controlCount) {
                    controlsCount++;
                    if (controlCount === 0) {
                        controlsProcessed++;
                    }
                }
            );

            if ((controlsCount === 0) || (controlsCount === controlsProcessed)) {
                _fadeBackgroundOff(true);
                if (typeof NProgress === 'undefined') {
                    return;
                }

                NProgress.done();
                setTimeout(
                    function () {
                        NProgress.remove();
                    },
                    500
                );
            } else {
                if (typeof NProgress !== 'undefined') {
                    NProgress.set(controlsProcessed / controlsCount);
                }
            }
        };

        /**
         * This function used for set counter of ready UI elements and
         * show NProgress bar.
         *
         * @function setUIReadyCounter
         * @memberof MainAppScripts
         * @requires NProgress
         * @see      {@link http://ricostacruz.com/nprogress} NProgress
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.setUIReadyCounter = function () {
            var controls     = {
                'Default': 1
            };
            var listControls = [
                'BodyClass',
                'Timeago',
                'Checkbox',
                'Treeview',
                'BonsaiTreeview',
                'Select',
                'InputsFilledProgressBar',
                'DatePicker',
                'FlagSelect',
                'Spin',
                'FileUpload',
                'FontAwesome'
            ];

            $.each(
                listControls,
                function (i, controlType) {
                    controls[controlType] = window['MainAppScripts']['update' + controlType](true);
                }
            );

            $('body').data('ui-ready-controls', controls);
            if ((typeof NProgress !== 'undefined') && (NProgress.status === null)) {
                NProgress.configure(
                    {
                        trickle: false,
                        showSpinner: false,
                        minimum: 0.01
                    }
                );
                NProgress.start();
            }

            return true;
        };

        /**
         * This function used for set focus on first form input with
         *  `autofocus` attribute.
         * Selector: `input[autofocus]:visible:enabled:first`.
         *
         * @function setInputFocus
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.setInputFocus = function () {
            $('input[autofocus]:visible:enabled:first').focus();
        };

        /**
         * This function used for adding button `clear` to input element and
         *  bind it to click event.
         * Selector: `input:text.clear-btn`.
         *
         * @function clearInputBtn
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.clearInputBtn = function () {
            var targetBlock = $('input:text.clear-btn');
            if (targetBlock.length === 0) {
                return;
            }

            var targetItem = null;
            var btnClear   = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);

                    if (targetItem.siblings('.mainappscripts-btn-input-clear').length === 0) {
                        $('<span class="mainappscripts-btn-input-clear"><span class="fas fa-times"></span></span>').insertAfter(targetItem);
                    }

                    btnClear = targetItem.siblings('.mainappscripts-btn-input-clear');
                    if (btnClear.length === 0) {
                        return true;
                    }

                    targetItem.keyup(
                        function () {
                            $(this).siblings('.mainappscripts-btn-input-clear').toggle(Boolean($(this).val()));
                        }
                    );
                    btnClear.toggle(Boolean(targetItem.val()));
                    btnClear.off('click.MainAppScripts').on(
                        'click.MainAppScripts',
                        function () {
                            var inputEl = $(this).siblings(':text');
                            if (inputEl.length === 0) {
                                return false;
                            }

                            if (jQuery.fn.typeahead) {
                                inputEl.typeahead('val', '');
                            } else {
                                inputEl.val('');
                            }

                            inputEl.last().focus();
                            $(this).hide();
                            return false;
                        }
                    );
                }
            );
        };

        /**
         * This function used for set textarea lines limit.
         * Attribute: `data-lines-limit` - Lines limit for input.
         *
         * @function setTextareaLinesLimit
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.setTextareaLinesLimit = function () {
            $('textarea').keydown(
                function (e) {
                    var linesLimit = $(this).data('lines-limit');
                    if (!linesLimit) {
                        return true;
                    }

                    var lines = $(this).val().split("\n").length;
                    if (e.keyCode === 13 && lines >= linesLimit) {
                        return false;
                    }

                    return true;
                }
            );
        };

        /**
         * This function used for bind Twitter Bootstrap 3 Date/Time Picker.
         * Selector: `[data-toggle="datetimepicker"]`.
         * Attributes:
         *  `data-date-format` - Date format, in js moment format;
         *  `data-date-locale` - Current locale, e.g. `en`;
         *  `data-icon-type` - Icon for button, e.g. `date` or `time`;
         *  `data-widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
         *  `data-widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
         *   Set to false form disable button.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateDatePicker
         * @memberof MainAppScripts
         * @requires Twitter Bootstrap 3 Date/Time Picker
         * @see      {@link https://github.com/Eonasdan/bootstrap-datetimepicker} Twitter Bootstrap 3 Date/Time Picker,
         *  {@link http://momentjs.com/docs/#/displaying/format/} JS Moment date and time format,
         *  {@link https://github.com/moment/moment/tree/develop/locale} JS Moment locale
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateDatePicker = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.datetimepicker) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="datetimepicker"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var targetItem          = null;
            var iconTypeDef         = 'date';
            var inputGroup          = null;
            var inputGroupAddon     = null;
            var datetimepicker      = null;
            var bindTarget          = null;
            var iconBtn             = null;
            var format              = null;
            var locale              = null;
            var iconType            = null;
            var widgetPositioning   = null;
            var horizontalPositions = ['auto', 'left', 'right'];
            var verticalPositions   = ['auto', 'top', 'bottom'];
            var horizontalPosition  = null;
            var verticalPosition    = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap Date/Time Picker is binded
                    if (targetItem.data('DateTimePicker')) {
                        MainAppScripts.processUIReadyCounter('DatePicker');
                        return true;
                    }

                    bindTarget = targetItem;
                    format     = targetItem.data('date-format');
                    if (!format) {
                        format = 'YYYY-MM-DD';
                    }

                    locale = targetItem.data('date-locale');
                    if (!locale) {
                        locale = 'en';
                    }

                    iconType = targetItem.data('icon-type');
                    if (typeof iconType === 'undefined') {
                        iconType = iconTypeDef;
                    }

                    widgetPositioning  = {
                        horizontal: 'auto',
                        vertical: 'auto'
                    };
                    horizontalPosition = targetItem.data('widget-position-horizontal');
                    verticalPosition   = targetItem.data('widget-position-vertical');
                    if (horizontalPosition) {
                        horizontalPosition = horizontalPosition.toLowerCase();
                        if ($.inArray(horizontalPosition, horizontalPositions) !== -1) {
                            widgetPositioning.horizontal = horizontalPosition;
                        }
                    }

                    if (verticalPosition) {
                        verticalPosition = verticalPosition.toLowerCase();
                        if ($.inArray(verticalPosition, verticalPositions) !== -1) {
                            widgetPositioning.vertical = verticalPosition;
                        }
                    }

                    if (iconType) {
                        iconType = iconType.toLowerCase();
                        switch (iconType) {
                            case 'time':
                                iconBtn = 'fa-clock';
                            break;

                            case 'date':
                            default:
                                iconBtn = 'fa-calendar-alt';
                            break;
                        }

                        inputGroup = targetItem.parents('.input-group');
                        if (inputGroup.length === 0) {
                            targetItem.wrap('<div class="input-group date"></div>');
                        } else {
                            inputGroup.addClass('date');
                        }

                        inputGroupAddon = targetItem.siblings('.input-group-addon.datepickerbutton');
                        if (inputGroupAddon.length === 0) {
                            inputGroupAddon = $('<span class="input-group-addon datepickerbutton"></span>');
                            inputGroupAddon.append('<span class="far ' + iconBtn + '"></span>');
                        } else {
                            if (inputGroupAddon.children('.' + iconBtn + '[data-fa-i2svg],span.' + iconBtn).length === 0) {
                                inputGroupAddon.append('<span class="far ' + iconBtn + '"></span>');
                            }
                        }

                        inputGroupAddon.insertAfter(targetItem);
                        bindTarget = targetItem.parents('.date');
                    }//end if

                    datetimepicker = bindTarget.datetimepicker(
                        {
                            format: format,
                            locale: locale,
                            showTodayButton: true,
                            showClear: true,
                            showClose: true,
                            widgetPositioning: widgetPositioning
                        }
                    );
                    datetimepicker.off('dp.show').on(
                        'dp.show',
                        function (e) {
                            if (typeof $().tooltip !== 'function') {
                                return;
                            }

                            $(e.target).children('[data-toggle="datetimepicker"]').tooltip('hide');
                        }
                    );
                    MainAppScripts.processUIReadyCounter('DatePicker');
                }
            );

            return true;
        };

        /**
         * This function used for bind jQuery plugin `Flagstrap`.
         * Selector: `[data-toggle="flag-select"]`.
         * Attributes:
         *  `data-country-url` - URL to list of countries.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateFlagSelect
         * @memberof MainAppScripts
         * @requires jQuery.fn.flagStrap
         * @see      {@link https://github.com/blazeworx/flagstrap} Flagstrap
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateFlagSelect = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.flagStrap) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="flag-select"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var url        = null;
            var id         = null;
            var opt        = {
                placeholder: false
            };
            var countries  = {};
            var targetItem = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Flagstrap is binded
                    if (targetItem.data('flagStrap')) {
                        MainAppScripts.processUIReadyCounter('FlagSelect');
                        return true;
                    }

                    id  = targetItem.attr('id');
                    url = targetItem.data('country-url');
                    if (url) {
                        opt.countries = _getCountrysList(url, id);
                    }

                    targetItem.empty();
                    targetItem.flagStrap(opt);
                    MainAppScripts.processUIReadyCounter('FlagSelect');
                }
            );

            return true;
        };

        /**
         * This function used for createon SSE object and bind
         *  callback function on event type.
         *
         * @param {string} type Type of task
         * @param {boolean} useProgress Use progress bar for this task
         * @param {_setProgressSSE} callbackEvent Callback function for event
         *
         * @function processSSE
         * @memberof MainAppScripts
         * @requires jQuery.SSE
         * @see      {@link https://github.com/byjg/jquery-sse} jQuery Server-Sent Events
         *
         * @returns {boolean}
         */
        MainAppScripts.processSSE = function (type, useProgress, callbackEvent) {
            if (!type || !callbackEvent) {
                return false;
            }

            if (!jQuery.SSE) {
                return false;
            }

            var url = _SSEdataUrl + '/' + type + '.sse';
            if (_SSE.obj[type]) {
                return true;
            }

            _initSSEConfig();
            _SSE.obj[type] = $.SSE(
                url,
                {
                    options: {
                        forceAjax: false
                    },
                    events: {
                        progressBar: callbackEvent
                    },
                    onEnd: function (e) {
                        _SSE.obj[type] = null;
                    },
                    onError: function (e) {
                        _SSE.obj[type] = null;
                    },
                    onMessage: function (e) {}
                }
            );

            _SSE.retries[type]  = _SSE.config.retries;
            var defaultText     = _SSE.config.text + '...';
            _SSE.noty[type]     = _createNoty(defaultText);
            _SSE.progress[type] = useProgress;
            _SSE.obj[type].start();
            if (typeof NProgress !== 'undefined') {
                NProgress.configure(
                    {
                        trickle: false,
                        showSpinner: false,
                        minimum: 0.01
                    }
                );
            }

            return true;
        };

        /**
         * This function used for show progress bar of
         *  task from queue.
         *
         * @param {string} type Type of task
         * @param {boolean} useProgress Use progress bar for this task
         *
         * @function showProgressSSE
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.showProgressSSE = function (type, useProgress) {
            MainAppScripts.processSSE(type, useProgress, _setProgressSSE);
        };

        /**
         * This function used for bind Twitter Bootstrap Typeahead
         * Selector: `[data-toggle="autocomplete"]`.
         * Attributes:
         *  `data-autocomplete-type` - Type for autocomplete suggestions, e.g. Model.Field;
         *  `data-autocomplete-plugin` - Plugin name for autocomplete field;
         *  `data-autocomplete-url` - URL for autocomplete;
         *  `data-autocomplete-local` - Local data for autocomplete;
         *  `data-autocomplete-min-length` - minimal length of query string.
         *
         * @function updateAutocomplete
         * @memberof MainAppScripts
         * @requires Bootstrap.Typeahead
         * @see      {@link https://github.com/bassjobsen/Bootstrap-3-Typeahead} Bootstrap 3 Typeahead
         *
         * @returns {null}
         */
        MainAppScripts.updateAutocomplete = function () {
            if (!jQuery.fn.typeahead) {
                return false;
            }

            var targetBlock       = $('[data-toggle="autocomplete"]');
            var targetBlockLength = targetBlock.length;
            if (targetBlockLength === 0) {
                return true;
            }

            var targetItem    = null;
            var wrapElem      = null;
            var moveEl        = null;
            var type          = null;
            var pluginName    = null;
            var url           = null;
            var minLength     = null;
            var local         = null;
            var source        = null;
            var bloodhoundOpt = null;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap Typeahead is binded
                    if (targetItem.data('ttTypeahead')) {
                        return true;
                    }

                    // ! Remove wrap SPAN for history back action
                    wrapElem = targetItem.closest('span.twitter-typeahead');
                    if (wrapElem.length === 1) {
                        moveEl     = wrapElem.find('.tt-input').removeClass('tt-input').detach();
                        targetItem = moveEl.insertAfter(wrapElem);
                        wrapElem.remove();
                    }

                    type       = targetItem.data('autocomplete-type');
                    pluginName = targetItem.data('autocomplete-plugin');
                    url        = targetItem.data('autocomplete-url');
                    local      = targetItem.data('autocomplete-local');
                    minLength  = parseInt(targetItem.data('autocomplete-min-length'), 10);
                    if (!url && !local) {
                        return true;
                    }

                    if (isNaN(minLength)) {
                        minLength = 2;
                    }

                    bloodhoundOpt = {
                        datumTokenizer: Bloodhound.tokenizers.whitespace,
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        limit: 10
                    };
                    if (local) {
                        bloodhoundOpt.local = local;
                    } else {
                        bloodhoundOpt.remote = {
                            url: url,
                            rateLimitBy: 'debounce',
                            rateLimitWait: 750,
                            prepare: function (query, settings) {
                                var type        = $('[data-toggle="autocomplete"]:focus').data('autocomplete-type');
                                settings.method = 'POST';
                                settings.data   = {
                                    query: query,
                                    type: type,
                                    plugin: pluginName
                                };

                                return settings;
                            }
                        };
                    }

                    source = new Bloodhound(bloodhoundOpt);
                    targetItem.typeahead(
                        {
                            minLength: minLength,
                            highlight: true,
                            hint: true
                        },
                        {
                            name: 'autocomplete-data',
                            source: source
                        }
                    );
                }
            );

            return true;
        };

        /**
         * This function used for bind Autocomplete for textarea elements
         * Selector: `[data-toggle="textcomplete"]`.
         * Attributes:
         *  `data-textcomplete-strategies` - Array of strategies, e.g.:
         *   - `match`, `replace`.
         *   - `ajaxOptions`: A set of key/value pairs that configure the Ajax request.
         *
         * @function updateTextcomplete
         * @memberof MainAppScripts
         * @see      {@link https://github.com/yuku/textcomplete} Autocomplete for textarea elements
         *
         * @returns {null}
         */
        MainAppScripts.updateTextcomplete = function () {
            if (typeof Textcomplete === 'undefined') {
                return false;
            }

            var targetBlock       = $('[data-toggle="textcomplete"]');
            var targetBlockLength = targetBlock.length;
            if (targetBlockLength === 0) {
                return true;
            }

            var Textarea = Textcomplete.editors.Textarea;
            var targetItem   = null;
            var editor       = null;
            var textcomplete = null;
            var strategies   = [];
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    strategies = targetItem.data('textcomplete-strategies');
                    if (!strategies || (typeof strategies !== 'object')) {
                        return true;
                    }

                    $.each(strategies,
                        function (i, strategy) {
                            $.each(strategy,
                                function (prop, val) {
                                    switch (prop) {
                                        case 'match':
                                            strategy[prop] = new RegExp(val, 'i');
                                        break;

                                        case 'replace':
                                            strategy[prop] = new Function('value', val);
                                        break;

                                        case 'search':
                                            strategy[prop] = new Function('term', 'callback', val);
                                        break;

                                        case 'ajaxOptions':
                                            if (!val || (typeof val !== 'object') ||
                                                !val.hasOwnProperty('url') || !val.hasOwnProperty('data') ||
                                                (typeof val.data !== 'object')) {
                                                    return true;
                                            }
                                            if (!val.hasOwnProperty('method')) {
                                                val.method = 'POST';
                                            }
                                            strategy['search'] = function (term, callback) {
                                                val.data.query = term;
                                                val.dataType = 'json';
                                                $.ajax(val).done(function (resp) { callback(resp); }).fail(function () { callback([]); });
                                            };
                                            delete strategy[prop];
                                        break;
                                    }
                                }
                            );
                            if (!strategy.hasOwnProperty('cache')) {
                                strategy.cache = true;
                            }
                        }
                    );
                    editor = new Textarea(el);
                    textcomplete = new Textcomplete(editor);
                    textcomplete.register(
                        strategies
                    );
                }
            );

            return true;
        };

        /**
         * This function used for bind jQuery plugin `File Upload`.
         * Selector: `[data-toggle="fileupload"]`.
         * Attributes:
         *  `data-fileupload-url` - URL to which the request is sent;
         *  `data-fileupload-maxfilesize` - The maximum allowed file size in bytes;
         *  `data-fileupload-acceptfiletypes` - The regular expression for allowed file;
         *  `data-fileupload-btntext-upload` - The text on the button "Upload";
         *  `data-fileupload-btntext-abort` - The text on the button "Abort";
         *  `data-fileupload-msgtext-processing` - The text for the message "processing";
         *  `data-fileupload-msgtext-error` - The text for the message "error".
         *  types, matches against either file type or file name as only browsers with
         *  support for the File API report the file type.
         *  `data-fileupload-redirecturl` - URL for redirect on success upload.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateFileUpload
         * @memberof MainAppScripts
         * @requires jQuery.FileUpload,jQuery.fileupload-process,jQuery.fileupload-validate.lng
         * @see      {@link https://blueimp.github.io/jQuery-File-Upload} File Upload
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateFileUpload = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (!jQuery.fn.fileupload) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            var targetBlock       = $('[data-toggle="fileupload"]');
            var targetBlockLength = targetBlock.length;
            if (returnCount) {
                return targetBlockLength;
            }

            if (targetBlockLength === 0) {
                return true;
            }

            var targetItem        = null;
            var wrapElem          = null;
            var urlFileupload     = null;
            var acceptFileTypes   = null;
            var btnTextUpload     = null;
            var btnTextAbort      = null;
            var msgTextProcessing = null;
            var msgTextError      = null;
            var parentClass       = null;
            var maxfilesize       = null;
            var uploadButton      = null;
            var fadeBackground    = true;
            targetBlock.each(
                function (i, el) {
                    targetItem = $(el);
                    // ! Checking Bootstrap tooltip is binded
                    if (targetItem.data('blueimpFileupload')) {
                        MainAppScripts.processUIReadyCounter('FileUpload');
                        return true;
                    }

                    // ! Remove list of files for history back action
                    wrapElem = targetItem.closest('span.btn');
                    if (wrapElem.length === 1) {
                        wrapElem.siblings('.files').empty();
                        wrapElem.siblings('.progress').children('.progress-bar').css('width', 0);
                    }

                    urlFileupload = targetItem.data('fileupload-url');
                    if (!urlFileupload) {
                        MainAppScripts.processUIReadyCounter('FileUpload');
                        return true;
                    }

                    acceptFileTypes = targetItem.data('fileupload-acceptfiletypes');
                    if (acceptFileTypes) {
                        acceptFileTypes = new RegExp(acceptFileTypes, 'i');
                    }

                    btnTextUpload = targetItem.data('fileupload-btntext-upload');
                    if (!btnTextUpload) {
                        btnTextUpload = 'Upload';
                    }

                    btnTextAbort = targetItem.data('fileupload-btntext-abort');
                    if (!btnTextAbort) {
                        btnTextAbort = 'Abort';
                    }

                    msgTextProcessing = targetItem.data('fileupload-msgtext-processing');
                    if (!msgTextProcessing) {
                        msgTextProcessing = 'Processing...';
                    }

                    msgTextError = targetItem.data('fileupload-msgtext-error');
                    if (!msgTextError) {
                        msgTextError = 'File upload failed.';
                    }

                    parentClass = null;
                    if (!$.support.fileInput) {
                        parentClass = 'disabled';
                    }

                    maxfilesize = parseInt(targetItem.data('fileupload-maxfilesize'), 10);
                    if (isNaN(maxfilesize)) {
                        maxfilesize = null;
                    }

                    uploadButton = $('<button/>')
                        .addClass('btn btn-primary')
                        .prop('disabled', true)
                        .text(msgTextProcessing)
                        .on(
                            'click.MainAppScripts',
                            function () {
                                var $this = $(this),
                                data      = $this.data();
                                $this
                                .off('click.MainAppScripts')
                                .text(btnTextAbort)
                                .on(
                                    'click.MainAppScripts',
                                    function () {
                                        $this.remove();
                                        data.abort();
                                    }
                                );
                                data.submit().always(
                                    function () {
                                        $this.remove();
                                    }
                                );
                                return false;
                            }
                        );
                    targetItem.fileupload(
                        {
                            url: urlFileupload,
                            dataType: 'json',
                            acceptFileTypes: acceptFileTypes,
                            maxFileSize: maxfilesize,
                            autoUpload: false,
                            previewMaxWidth: 100,
                            previewMaxHeight: 100,
                            previewCrop: true
                        }
                    ).on(
                        'fileuploadsubmit',
                        function (e, data) {
                            MainAppScripts.loadIndicatorOn(fadeBackground);
                        }
                    ).on(
                        'fileuploadadd',
                        function (e, data) {
                            var filesId = $(e.target).data('files-id');
                            if (filesId) {
                                data.context = $('#' + filesId).html('<div/>');
                            } else {
                                data.context = $('div.files:eq(0)').html('<div/>');
                            }

                            var node = null;
                            $.each(
                                data.files,
                                function (index, file) {
                                    node = $('<p/>').append($('<span/>').text(file.name));
                                    if (!index) {
                                        node
                                        .append('<br>')
                                        .append(uploadButton.clone(true).data(data));
                                    }

                                    node.appendTo(data.context);
                                }
                            );
                        }
                    ).on(
                        'fileuploadprocessalways',
                        function (e, data) {
                            var index = data.index,
                            file      = data.files[index],
                            node      = $(data.context.children()[index]);
                            if (file.preview) {
                                node
                                .prepend('<br>')
                                .prepend(file.preview);
                            }

                            if (file.error) {
                                node
                                .append('<br>')
                                .append($('<span class="text-danger"/>').text(file.error));
                            }

                            if (index + 1 === data.files.length) {
                                data.context.find('button')
                                .text(btnTextUpload)
                                .prop('disabled', !!data.files.error);
                            }
                        }
                    ).on(
                        'fileuploadprogressall',
                        function (e, data) {
                            var progress = parseInt((data.loaded / data.total * 100), 10);
                            if (isNaN(progress)) {
                                progress = 0;
                            }

                            var progressId = $(e.target).data('progress-id');
                            if (progressId) {
                                $('#' + progressId + ' .progress-bar').css(
                                    'width',
                                    progress + '%'
                                );
                            }
                        }
                    ).on(
                        'fileuploaddone',
                        function (e, data) {
                            var result = true;
                            $.each(
                                data.result.files,
                                function (index, file) {
                                    if (file.error) {
                                        result = false;
                                    }

                                    if (file.url) {
                                        var link = $('<a>')
                                        .attr('target', '_blank')
                                        .prop('href', file.url);
                                        $(data.context.children()[index])
                                        .wrap(link);
                                    } else if (file.error) {
                                        var error = $('<span class="text-danger"/>').text(file.error);
                                        $(data.context.children()[index])
                                        .append('<br>')
                                        .append(error);
                                    }
                                }
                            );
                            if (result) {
                                var urlRedirect = targetItem.data('fileupload-redirecturl');
                                if (!urlRedirect) {
                                    urlRedirect = window.location.href;
                                }

                                MainAppScripts.updatePage(urlRedirect);
                            }

                            MainAppScripts.loadIndicatorOff(fadeBackground);
                        }
                    ).on(
                        'fileuploadfail',
                        function (e, data) {
                            $.each(
                                data.files,
                                function (index) {
                                        var error = $('<span class="text-danger"/>').text(msgTextError);
                                        $(data.context.children()[index])
                                        .append('<br>')
                                        .append(error);
                                }
                            );
                            MainAppScripts.loadIndicatorOff(fadeBackground);
                        }
                    ).prop('disabled', !$.support.fileInput)
                    .parent().addClass(parentClass);
                    MainAppScripts.processUIReadyCounter('FileUpload');
                }
            );

            return true;
        };

        /**
         * This function used for process ready Font Awesome.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateFontAwesome
         * @memberof MainAppScripts
         * @requires Font Awesome
         * @see      {@link https://fontawesome.com/} Font Awesome
         *
         * @returns {integer|boolean}
         */
        MainAppScripts.updateFontAwesome = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            if (typeof FontAwesome === 'undefined') {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            if (returnCount) {
                return 1;
            }

            _checkReadyFontAwesome();

            return true;
        };

        /**
         * This function used for toggle icon on click event
         * Selector: `[data-toggle-icons!=""][data-toggle-icons]`.
         * Attributes:
         *  `data-toggle-icons` - List of 2 classes of comma-separated
         *   for toggle icons
         *
         * @function updateToggleIcons
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateToggleIcons = function () {
            $('[data-toggle-icons!=""][data-toggle-icons]').off('click.MainAppScripts').on(
                'click.MainAppScripts',
                function (e) {
                    var target      = $(e.target);
                    var iconClasses = target.data('toggle-icons').split(',');
                    if (iconClasses.length !== 2) {
                        return true;
                    }

                    target.find('[data-fa-i2svg]').toggleClass(iconClasses[0]).toggleClass(iconClasses[1]);

                    return true;
                }
            );
        };

        /**
         * This function used for adding class to tag BODY for
         *  fixing padding on top.
         *
         * @param {boolean} returnCount If True - return count of elements for bind,
         *  boolean otherwise. Default - False.
         *
         * @function updateBodyClass
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.updateBodyClass = function (returnCount) {
            if (typeof returnCount === 'undefined') {
                returnCount = false;
            }

            var objBody = $('body');
            if (objBody.length === 0) {
                if (returnCount) {
                    return 0;
                } else {
                    return false;
                }
            }

            if (returnCount) {
                return 1;
            }

            var classBody = '';
            var objHeader = $('#header');
            if (objHeader.length === 0) {
                return true;
            }

            if (objHeader.find('[role="navigation"]').length === 1) {
                classBody = 'use-navbar-only';
                if (objHeader.find('.breadcrumb').length === 1) {
                    classBody = 'use-navbar-breadcrumb';
                }
            } else if (objHeader.find('.breadcrumb').length === 1) {
                classBody = 'use-breadcrumb-only';
            }

            if (classBody) {
                objBody.addClass(classBody);
            }

            MainAppScripts.processUIReadyCounter('BodyClass');

            return true;
        };

        /**
         * This function used for adding back to top button.
         *
         * @function scrollUpBtn
         * @memberof MainAppScripts
         * @requires scrollUp
         * @see      {@link http://markgoodyear.com/labs/scrollup} scrollUp
         *
         * @returns {boolean}
         */
        MainAppScripts.scrollUpBtn = function () {
            if (!jQuery.fn.scrollUp) {
                return false;
            }

            $.scrollUp(
                {
                    animation: 'fade',
                    scrollImg: true
                }
            );

            return true;
        };

        /**
         * This function is called after updating the content page.
         * Initializes and bind jquery and Twitter Bootstrap UI elements.
         * Initiates internal event `MainAppScripts:update`.
         * Requires the creation of event handler `MainAppScripts:update`.
         * Call from document ready event handler.
         *
         * @example
         *    $(function() {
         *        $(document).off('MainAppScripts:update.LoginLayout').on('MainAppScripts:update.LoginLayout', function() {
         *            MainAppScripts.updateButtons();
         *            MainAppScripts.updatePassField();
         *            MainAppScripts.updateTooltips();
         *            MainAppScripts.setInputFocus();
         *            MainAppScripts.processUIReadyCounter();
         *        });
         *
         *        MainAppScripts.update();
         *    });
         * @memberof MainAppScripts
         *
         * @returns {null}
         */
        MainAppScripts.update = function () {
            $(document).trigger('MainAppScripts:update');
        };

        return MainAppScripts;
    }
);

/**
 * Initializing JS Store
 *
 * @function ready
 * @returns  {null}
 */
$(
    function () {
        MainAppScripts.initStorage();
    }
);
