/**
 * This file use for Ajax show Flash Message
 *
 * @file    Main file for AjaxFlashMessage
 * @version 0.9.0
 * @copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.AjaxFlashMessage = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         * @version 0.9.0
         * @namespace AjaxFlashMessage
         */
        var AjaxFlashMessage = {};

        /**
         * Specify the url to fetch the list of messages
         *
         * @constant {string} _flashMsgUrl
         */
        var _flashMsgUrl = '/cake_theme/flash/flashmsg.json';

        /**
         * Specify the url to fetch the configurations for flash messages
         *
         * @constant {string} _flashCfgUrl
         */
        var _flashCfgUrl = '/cake_theme/flash/flashcfg.json';

        /**
         * Settings of the script
         *
         * @member {array} _settings
         */
        var _settings = {
            'timeOut': 30,
            'theme': 'mint',
            'layout': 'top',
            'open': 'animated flipInX',
            'close': 'animated flipOutX',
            'delayDeleteFlash': 5,
            'globalAjaxComplete': false
        };

        /**
         * Object of JS Storage
         *
         * @member {object} _storageObj
         * @see    {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         */
        var _storageObj = null;

        /**
         * Return state of usage storage
         *
         * @function _isStorageUse
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
         * Callback function for ajax POST request of Flash messages
         *  used for display a message
         *
         * @param {*} data Data of flash messages
         * @param {string} textStatus string Status of result.
         * @param {object} jqXHR object Object, which is a superset
         *  of the XMLHTTPRequest object.
         *
         * @callback _parseFlashData
         *
         * @returns {null}
         */
        function _parseFlashData(data, textStatus, jqXHR)
        {
            var showIcon        = true;
            var iconTag         = '';
            var flashClass      = 'alert-info';
            var flashClassPlain = 'flash-information';
            var iconClass       = 'glyphicon-info-sign';
            var notyType        = 'alert';
            var notyTimeout     = false;
            var message         = '';
            var messageBox      = null;
            var keysDelete      = [];
            $.each(
                data,
                function (i, dataItem) {
                    if (!dataItem.result || !dataItem.key || !dataItem.messages) {
                        return;
                    }

                    $.each(
                        dataItem.messages,
                        function (i, messageInfo) {
                            if (!messageInfo.message) {
                                return;
                            }

                            switch (messageInfo.element) {
                                case 'Flash/success':
                                    flashClass      = 'alert-success';
                                    flashClassPlain = 'flash-success';
                                    iconClass       = 'glyphicon-ok';
                                    notyType        = 'success';
                                    notyTimeout     = (_settings.timeOut * 1000);
                                break;

                                case 'Flash/notification':
                                    flashClass      = 'alert-info';
                                    flashClassPlain = 'flash-information';
                                    iconClass       = 'glyphicon-info-sign';
                                    notyType        = 'alert';
                                    notyTimeout     = (_settings.timeOut * 1000);
                                break;

                                case 'Flash/error':
                                    flashClass      = 'alert-danger';
                                    flashClassPlain = 'flash-error';
                                    iconClass       = 'glyphicon-warning-sign';
                                    notyType        = 'error';
                                break;

                                case 'Flash/warning':
                                    flashClass      = 'alert-warning';
                                    flashClassPlain = 'flash-warning';
                                    iconClass       = 'glyphicon-exclamation-sign';
                                    notyType        = 'warning';
                                break;

                                case 'Flash/information':
                                default:
                                    flashClass      = 'alert-info';
                                    flashClassPlain = 'flash-information';
                                    iconClass       = 'glyphicon-info-sign';
                                    notyType        = 'information';
                                    notyTimeout     = (_settings.timeOut * 1000);
                                break;
                            }//end switch

                            showIcon = !messageInfo.params.hideMsgIcon;
                            message  = messageInfo.message;
                            iconTag  = '';
                            if (showIcon) {
                                iconTag = '<span class="glyphicon ' + iconClass + '"></span>';
                            }

                            if (messageInfo.params.code) {
                                message = '<strong class="ajax-flash-msg-code"><samp>' + messageInfo.params.code + '</strong></samp>' + message;
                            }

                            // ! Check 'jQuery.noty' exist
                            if (typeof Noty === 'undefined') {
                                // ! Check Twitter bootstrap exist
                                if (typeof $().modal === 'function') {
                                    messageBox = $(
                                        '<div class="alert ' + flashClass + ' alert-dismissible fade in ajax-flash-body" role="alert">\
                                            <div class="ajax-flash-icon">' + iconTag + '</div>\
                                            <div class="ajax-flash-msg">\
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">\
                                                    <span aria-hidden="true">&times;</span>\
                                                </button>' + message + '</div>\
                                        </div>'
                                    );
                                    if ($('#content .breadcrumb').length > 0) {
                                        messageBox.insertAfter('#content .breadcrumb:eq(0)');
                                    } else {
                                        $('#content').prepend(messageBox);
                                    }
                                } else {
                                    $('#content').prepend(
                                        '<div class="ajax-flash-body flash ' + flashClassPlain + '">\
                                            <div class="ajax-flash-icon">' + iconTag + '</div>\
                                            <div class="ajax-flash-msg">' + message + '</div>\
                                        </div>'
                                    );
                                    $('.ajax-flash-body').off('click.AjaxFlash').on(
                                        'click.AjaxFlash',
                                        function () {
                                            $(this).fadeOut(400);
                                        }
                                    );
                                }//end if

                                $('html, body').animate(
                                    {
                                        scrollTop: 0
                                    },
                                    800
                                );
                            } else {
                                var notyText = '<div class="ajax-flash-body' + flashClassPlain + '">\
                                    <div class="ajax-flash-icon">' + iconTag + '</div>\
                                    <div class="ajax-flash-msg">' + message + '</div>\
                                </div>';
                                var objNoty  = new Noty(
                                    {
                                        text: notyText,
                                        type: notyType,
                                        timeout: notyTimeout,
                                        closeWith: ['click'],
                                        layout: _settings.layout,
                                        theme: _settings.theme,
                                        animation: {
                                            open: _settings.open,
                                            close: _settings.close
                                        }
                                      }
                                ).show();
                            }//end if
                        }
                    );

                    keysDelete.push(dataItem.key);
                }
            );
            if (keysDelete.length > 0) {
                if (_settings.delayDeleteFlash < 1) {
                    _settings.delayDeleteFlash = 5;
                }

                var delayDeleteFlash = (_settings.delayDeleteFlash * 1000);
                setTimeout(_deleteFlashMessage, delayDeleteFlash, keysDelete);
            }
        }

        /**
         * Delete a message for the appropriate key
         *
         * @param {array} keys Array data of the session key
         *  for delete message.
         *
         * @function _deleteFlashMessage
         *
         * @returns {null}
         */
        function _deleteFlashMessage(keys)
        {
            if (!keys || !$.isArray(keys) || (keys.length === 0)) {
                return;
            }

            var postData = {
                'data': {
                    'keys': keys,
                    'delete': 1
                }
            };
            $.ajax(
                {
                    async: true,
                    url: _flashMsgUrl,
                    data: postData,
                    dataType: 'json',
                    global: false,
                    method: 'POST'
                }
            );
        }

        /**
    * Initializing the JS Storage Plugin
     *
    * @function initStorage
    * @memberof AjaxFlashMessage
    *
    * @returns {boolean}
    */
        AjaxFlashMessage.initStorage = function () {
            if (typeof Storages === 'undefined') {
                return false;
            }

            _storageObj = Storages.initNamespaceStorage('ajax_flash').sessionStorage;
            return true;
        };

        /**
         * Getting and initializing the settings of the script
         *
         * @function initSettings
         * @memberof AjaxFlashMessage
         * @requires Storages
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         *
         * @returns {null}
         */
        AjaxFlashMessage.initSettings = function () {
            var storageKey = 'Config';

            if (_isStorageUse() && _storageObj.isSet(storageKey)) {
                var stored = _storageObj.get(storageKey);
                if (stored) {
                    _settings = stored;
                    return;
                }
            }

            $.ajax(
                {
                    async: false,
                    url: _flashCfgUrl,
                    dataType: 'json',
                    global: false,
                    method: 'POST',
                    success: function (data) {
                        if (data) {
                            _settings = $.extend({}, _settings, data);
                        }

                        if (_isStorageUse()) {
                            _storageObj.set(storageKey, _settings);
                        }
                    }
                }
            );
        };

        /**
         * Callback function for global Ajax callback complete()
         *
         * @param {string} textStatus string Status of result.
         * @param {object} jqXHR object Object, which is a superset
         *  of the XMLHTTPRequest object.
         *
         * @callback callbackAjaxFlashMessage
         * @memberof AjaxFlashMessage
         *
         * @returns {null}
         */
        AjaxFlashMessage.callbackAjaxFlashMessage = function (jqXHR, textStatus) {
            if (textStatus !== 'success') {
                return;
            }

            if (!jqXHR.responseText) {
                return;
            }

            if (jqXHR.responseJSON || jqXHR.responseXML) {
                return;
            }

            if (/<[a-z][\s\S]*>/i.test(jqXHR.responseText)) {
                AjaxFlashMessage.ajaxFlashMessage();
            }
        };

        /**
         * Register global ajax callback complete()
         *
         * @function registerGlobalAjaxComplete
         * @memberof AjaxFlashMessage
         *
         * @returns {null}
         */
        AjaxFlashMessage.registerGlobalAjaxComplete = function () {
            if (!_settings.globalAjaxComplete) {
                return;
            }

            $.ajaxSetup(
                {
                    complete: function (jqXHR, textStatus) {
                        callbackAjaxFlashMessage(jqXHR, textStatus);
                    }
                }
            );
        };

        /**
         * Display a message for all pre-installed keys
         *
         * @function ajaxFlashMessage
         * @memberof AjaxFlashMessage
         *
         * @returns {null}
         */
        AjaxFlashMessage.ajaxFlashMessage = function () {
            var keys          = _settings.flashKeys;
            var postDataItems = [];

            if (!keys || !$.isArray(keys) || (keys.length === 0)) {
                return;
            }

            var postData = {
                'data': {
                    'keys': keys,
                    'delete': 0
                }
            };
            $.ajax(
                {
                    async: true,
                    url: _flashMsgUrl,
                    data: postData,
                    dataType: 'json',
                    global: false,
                    method: 'POST',
                    success: _parseFlashData
                }
            );
        };

        return AjaxFlashMessage;
    }
);

/**
 * Initializing the settings of the script and JS Store,
 *  registration is global ajax handler.
 *  Registration handler of event `MainAppScripts:update`
 *  for display a message for all pre-installed keys.
 *
 * @function ready
 *
 * @returns {null}
 */
$(
    function () {
        AjaxFlashMessage.initStorage();
        AjaxFlashMessage.initSettings();
        AjaxFlashMessage.registerGlobalAjaxComplete();

        $(document).off('MainAppScripts:update.AjaxFlashMessage').on(
            'MainAppScripts:update.AjaxFlashMessage',
            function () {
                AjaxFlashMessage.ajaxFlashMessage();
            }
        );
    }
);
