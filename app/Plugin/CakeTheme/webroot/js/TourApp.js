/**
 * This file use for tour application
 *
 * @file    Main file for TourApplication
 * @version 0.3.0
 * @copyright 2017-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.TourApplication = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         * @version 0.3.0
         * @namespace TourApplication
         */
        var TourApplication = {};

        /**
         * Set a expiration time for the steps. When the step expires,
         *  the next step is automatically shown.
         *  The value is specified in milliseconds
         *
         * @member {boolean|integer} _duration
         */
        var _duration = 10000;

        /**
         * Specify the url to fetch the list of steps for tour
         *
         * @member {string} _tourStepsUrl
         */
        var _tourStepsUrl = '/cake_theme/tours/steps.json';

        /**
         * Object of Bootstrap Tour plugin
         *
         * @member {object} _tourObj
         * @see    {@link http://bootstraptour.com} Bootstrap Tour
         */
        var _tourObj = null;

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
         * @returns  {boolean}
         */
        function _isStorageUse()
        {
            if (_storageObj) {
                return true;
            }

            return false;
        }

        /**
         * Convert next string options of step: `onShow`, `onShown`, `onHide`,
         *  `onHidden`, `onNext`, `onPrev` into function and adding option `pageNum`.
         *
         * @param {object} steps Array of tour steps
         *
         * @function _prepareSteps
         *
         * @returns {object} Return array of steps.
         */
        function _prepareSteps(steps)
        {
            if (!steps) {
                return steps;
            }

            var funcProp = ['onShow', 'onShown', 'onHide', 'onHidden', 'onNext', 'onPrev'];
            var numSteps = steps.length;
            $.each(
                steps,
                function (index, dataItem) {
                    dataItem.pageNum = (index + 1) + '/' + numSteps;
                    $.each(
                        funcProp,
                        function (index, funcPropItem) {
                            if (dataItem.hasOwnProperty(funcPropItem)) {
                                dataItem[funcPropItem] = new Function('tour', dataItem[funcPropItem]);
                            }
                        }
                    );
                }
            );

            return steps;
        }

        /**
         * Return array of tour steps from server.
         *
         * @param {boolean} useRemoteConfig If True, return steps from server.
         *  Otherwise, return from storage.
         *
         * @function _getTourSteps
         *
         * @returns {object}
         */
        function _getTourSteps(useRemoteConfig)
        {
            var storageKey = 'Steps';
            var result     = [];

            if (_isStorageUse() && _storageObj.isSet(storageKey)) {
                var stored = _storageObj.get(storageKey);
                if (stored) {
                    result = stored;
                }
            } else if (useRemoteConfig || (!useRemoteConfig && !_isStorageUse())) {
                $.ajax(
                    {
                        async: false,
                        url: _tourStepsUrl,
                        dataType: 'json',
                        method: 'POST',
                        success: function (data) {
                            if (!data) {
                                return;
                            }

                            result = data;
                            if (_isStorageUse()) {
                                _storageObj.set(storageKey, result);
                            }
                        }
                    }
                );
            } else {
                return result;
            }//end if

            return result;
        }

        /**
         * Start tour from the beginning.
         *
         * @function _startTourApp
         *
         * @returns {null}
         */
        function _startTourApp()
        {
            _tourObj.goTo(0);
            if (_tourObj.ended()) {
                _tourObj.restart();
            } else {
                _tourObj.start(true);
            }
        }

        /**
         * Initializing the JS Storage Plugin
         *
         * @function initStorage
         * @memberof TourApplication
         * @requires Storages
         * @see      {@link https://github.com/julien-maurel/js-storage} JS Storage Plugin
         *
         * @returns {boolean}
         */
        TourApplication.initStorage = function () {
            if (typeof Storages === 'undefined') {
                return false;
            }

            _storageObj = Storages.initNamespaceStorage('tour_app').sessionStorage;
            return true;
        };

        /**
         * Initializing the Bootstrap Tour Plugin
         *
         * @param {boolean} useRemoteConfig If True, return steps from server.
         *  Otherwise, return from storage.
         *
         * @function initTourApp
         * @memberof TourApplication
         *
         * @returns {boolean}
         */
        TourApplication.initTourApp = function (useRemoteConfig) {
            if (typeof Tour === 'undefined') {
                return false;
            }

            if (_tourObj) {
                return true;
            }

            var steps = _getTourSteps(useRemoteConfig);
            if (steps.length === 0) {
                return false;
            }

            steps    = _prepareSteps(steps);
            _tourObj = new Tour(
                {
                    steps: steps,
                    duration: _duration,
                    template: function (i, step) {
                        var pageNum  = step.pageNum;
                        var pageInfo = '';
                        if (pageNum) {
                            pageInfo = '<samp>[' + pageNum + ']</samp>';
                        }

                        var templ = '<div class="popover" role="tooltip">\
                            <div class="arrow"></div>\
                            <h3 class="popover-title"></h3>\
                            <div class="popover-content"></div>\
                            <div class="popover-navigation text-center">\
                                <div class="btn-group pull-left">\
                                    <button class="btn btn-sm btn-default" data-role="prev">\
                                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>\
                                    </button>\
                                    <button class="btn btn-sm btn-default" data-role="next">\
                                        <span class="glyphicon glyphicon-chevron-right"></span>\
                                    </button>\
                                    <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-class="glyphicon glyphicon-pause" data-resume-class="glyphicon glyphicon-play">\
                                        <span class="glyphicon glyphicon-pause"></span>\
                                    </button>\
                                </div>\
                                <span class="tour-info">' + pageInfo + '</span>\
                                <button class="btn btn-sm btn-default" data-role="end">\
                                    <span class="glyphicon glyphicon-remove"></span>\
                                </button>\
                            </div>\
                        </div>'
                        return templ;
                    },
                    onShown: function (tour) {
                        var title  = this.title;
                        var id     = this.id;
                        var target = $('#' + id);
                        target.find('h3.popover-title').html(title);
                    },
                    onPause: function (tour) {
                        var id          = this.id;
                        var target      = $('#' + id);
                        var btnPause    = target.find('button[data-role="pause-resume"]');
                        var classResume = btnPause.data('resume-class');
                        btnPause.find('span').attr('class', classResume);
                    },
                    onResume: function (tour) {
                        var id         = this.id;
                        var target     = $('#' + id);
                        var btnPause   = target.find('button[data-role="pause-resume"]');
                        var classPause = btnPause.data('pause-class');
                        btnPause.find('span').attr('class', classPause);
                    }
                }
            );

            _tourObj.init();
            return true;
        };

        /**
         * This function used for start tour of application on click event.
         * Selector: `[data-toggle="start-app-tour"]`.
         *
         * @function updateTourApplication
         * @memberof TourApplication
         *
         * @returns {null}
         */
        TourApplication.updateTourApplication = function () {
            $('[data-toggle="start-app-tour"]').off('click.TourApplication').on(
                'click.TourApplication',
                function (e) {
                    e.preventDefault();
                    if (!TourApplication.initTourApp(true)) {
                        return;
                    }

                    _startTourApp();
                }
            );
        };

        return TourApplication;
    }
);

/**
 * Initializing JS Store and Bootstrap Tour plugin.
 *  Registration handler of event `MainAppScripts:update`
 *  for bind click event handler to start tour.
 *
 * @function ready
 *
 * @returns {null}
 */
$(
    function () {
        TourApplication.initStorage();
        TourApplication.initTourApp(false);

        $(document).off('MainAppScripts:update.TourApplication').on(
            'MainAppScripts:update.TourApplication',
            function () {
                TourApplication.updateTourApplication();
            }
        );
    }
);
