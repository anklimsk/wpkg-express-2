/**
 * This file use for notify user with WebNotifications
 *
 * @file    Main file for WebNotifications
 * @version 0.3.0
 * @copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.WebNotifications = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         *  Notify user with WebNotifications
         *
         * @version   0.3.0
         * @author    Andrey Klimov
         * @namespace WebNotifications
         */
        var WebNotifications = {};

        /**
         * Object of JS Storage
         *
         * @member {object} _storageObj
         *
         * @see {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         */
        var storageObj = null;

        /**
         * Object of jQuery Server-Sent Events
         *
         * @member {object} _sseObj
         *
         * @see {@link https://github.com/byjg/jquery-sse} jQuery Server-Sent Events
         */
        var _sseObj = null;

        /**
         * Time of delay for request Server-Sent Events in miliseconds
         *
         * @constant {integer} _retrySse
         *
         * @see {@link https://github.com/byjg/jquery-sse} jQuery Server-Sent Events
         */
        var _retrySse = 30000;

        /**
         * Specify the url to fetch SSE data
         *
         * @constant {string} _SSEdataUrl
         */
        var _SSEdataUrl = '/cake_notify/notifications/message';

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
         * Return state of need update notifications
         *
         * @function _checkNeedUpdate
         *
         * @returns {boolean}
         */
        function _checkNeedUpdate()
        {
            var now        = Date.now();
            var lastUpdate = _getTimeLastUpdate();
            var retrySse   = _getTimeRetrySse();
            if (lastUpdate === 0) {
                return true;
            }

            if ((now - lastUpdate) <= (retrySse * 2)) {
                return false;
            }

            return true;
        }

        /**
         * Saving in storage the timestamp of last update notifications.
         *
         * @function _setTimeLastUpdate
         *
         * @returns {null}
         */
        function _setTimeLastUpdate()
        {
            var storageKey = 'TimeLastUpdate';
            if (!_isStorageUse()) {
                return;
            }

            var now = Date.now();
            _storageObj.set(storageKey, now);
        }

        /**
         * Retrieving from storage the timestamp of last update notifications.
         *
         * @function _getTimeLastUpdate
         *
         * @returns {integer} Timestamp of last update notifications
         */
        function _getTimeLastUpdate()
        {
            var storageKey = 'TimeLastUpdate';
            if (!_isStorageUse() || !_storageObj.isSet(storageKey)) {
                return 0;
            }

            var stored = _storageObj.get(storageKey);
            return stored;
        }

        /**
         * Saving in storage the time of delay for request Server-Sent Events.
         *
         * @param {integer} retry Time of delay for request SSE
         *
         * @function _setTimeRetrySse
         *
         * @returns {null}
         */
        function _setTimeRetrySse(retry)
        {
            var storageKey = 'SSEretry';
            if (!_isStorageUse()) {
                return;
            }

            _storageObj.set(storageKey, retry);
            return;
        }

        /**
         * Retrieving from storage the time of delay for request Server-Sent Events.
         *
         * @function _getTimeRetrySse
         *
         * @returns {integer} Time of delay for request SSE
         */
        function _getTimeRetrySse()
        {
            var storageKey = 'SSEretry';
            if (!_isStorageUse() || !_storageObj.isSet(storageKey)) {
                return _retrySse;
            }

            var stored = _storageObj.get(storageKey);
            return stored;
        }

        /**
         * This function used as callback for event in SSE for
         *  show/update WEB Notification.
         *
         * @param {object} e Event object
         *
         * @callback _processSSE
         *
         * @returns {null}
         */
        function _processSSE(e)
        {
            var data = $.parseJSON(e.data);
            if (data.retry && (data.retry > 5000)) {
                _setTimeRetrySse(data.retry);
            }

            _setTimeLastUpdate();
            if (!data.result || (data.messages.length === 0)) {
                return;
            }

            $.each(
                data.messages,
                function (index, dataItem) {
                    WebNotifications.showNotification(dataItem);
                }
            );
        }

        /**
         * Initializing the JS Storage Plugin
         *
         * @function initStorage
         * @memberof WebNotifications
         * @requires Storages
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         *
         * @returns {boolean}
         */
        WebNotifications.initStorage = function () {
            if (typeof Storages === 'undefined') {
                return false;
            }

            _storageObj = Storages.initNamespaceStorage('web_notifications').localStorage;
            return true;
        };

        /**
         * Initializing the Server-Sent Events Plugin: createon SSE object and bind
         *  callback function on event type.
         *
         * @function initSSE
         * @memberof WebNotifications
         * @requires jQuery.SSE
         * @see      {@link https://github.com/byjg/jquery-sse} jQuery Server-Sent Events
         *
         * @returns {boolean}
         */
        WebNotifications.initSSE = function () {
            if (!jQuery.SSE) {
                return false;
            }

            var url = _SSEdataUrl + '.sse';
            _sseObj = $.SSE(
                url,
                {
                    options: {
                        forceAjax: false
                    },
                    events: {
                        webNotification: _processSSE
                    },
                    onError: function (e) {
                        _setTimeLastUpdate();
                        if (WebNotifications.initSSE()) {
                            WebNotifications.startSSE();
                        }
                    }
                }
            );
            return true;
        };

        /**
         * Start the EventSource communication.
         *
         * @function startSSE
         * @memberof WebNotifications
         *
         * @returns {null}
         */
        WebNotifications.startSSE = function () {
            if (_checkNeedUpdate() || !_isStorageUse()) {
                _sseObj.start();
            } else {
                var retrySse = _getTimeRetrySse();
                setTimeout(WebNotifications.startSSE, (retrySse * 3));
            }
        };

        /**
         * This function used for createon and show
         *  notification message.
         *
         * @param {object} data Data for notification message
         *
         * @function showNotification
         *
         * @memberof WebNotifications
         *
         * @requires Web Notifications API support
         *
         * @returns {boolean}
        */
        WebNotifications.showNotification = function (data) {
            if (!('Notification' in window)) {
                return false;
            }

            var notification = null;
            var defaultData  = {
                title: '',
                body: '',
                data: {}
            };
            data             = $.extend({}, defaultData, data);
            var title        = data.title;
            delete data.title;
            var options = data;
            if (!title || !options.body) {
                return false;
            }

            if (Notification.permission === 'granted') {
                notification = new Notification(title, options);
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission(
                    function (permission) {
                        if (permission === 'granted') {
                            notification = new Notification(title, options);
                        }
                    }
                );
            }

            if (!notification) {
                return false;
            }

            if (options.data && options.data.url) {
                notification.onclick = function (event) {
                    event.preventDefault();
                    window.open(options.data.url, '_blank');
                };
            }

            return notification;
        };

        return WebNotifications;
    }
);


/**
 * Initializing JS Store and Web Notifications plugin.
 *
 * @function ready
 *
 * @returns {null}
 */
$(
    function () {
        if (!('Notification' in window)) {
            return;
        }

        WebNotifications.initStorage();
        if (WebNotifications.initSSE()) {
            WebNotifications.startSSE();
        }
    }
);
