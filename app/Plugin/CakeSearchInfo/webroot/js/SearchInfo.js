/**
 * This file use for search info
 *
 * @file    Main file for WebNotifications
 * @version 0.9.0
 * copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.SearchInfo = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         * @version 0.9.0
         * @namespace SearchInfo
         */
        var SearchInfo = {};

        /**
         * This function used for bind Twitter Bootstrap Typeahead
         * Selector: `[data-toggle="autocomplete-search"]`.
         * Attributes:
         *  `data-autocomplete-url` - URL for autocomplete;
         *  `data-autocomplete-min-length` - minimal length of query string.
         *
         * @function updateAutocomplete
         * @memberof SearchInfo
         * @requires Bootstrap.Typeahead
         * @see      {@link https://github.com/corejavascript/typeahead.js} corejs-typeahead
         * @returns  {null}
         */
        SearchInfo.updateAutocomplete = function () {
            if (!jQuery.fn.typeahead) {
                return false;
            }

            $('.search-form [data-toggle="autocomplete-search"]:not(.tt-hint,.tt-input)').each(
                function (i) {
                    var url       = $(this).data('autocomplete-url');
                    var minLength = $(this).data('autocomplete-min-length');
                    if (!url) {
                        return;
                    }

                    if (!minLength) {
                        minLength = 2;
                    }

                    var source = new Bloodhound(
                        {
                            datumTokenizer: Bloodhound.tokenizers.whitespace,
                            queryTokenizer: Bloodhound.tokenizers.whitespace,
                            limit: 10,
                            remote: {
                                url: url,
                                rateLimitBy: 'debounce',
                                rateLimitWait: 750,
                                prepare: function (query, settings) {
                                    var target    = $('#SearchTarget').val();
                                    settings.type = 'POST';
                                    settings.data = { query: query, target: target };
                                    return settings;
                                }
                            }
                        }
                    );

                    $(this).typeahead('destroy').typeahead(
                        {
                            minLength: Number(minLength),
                            highlight: true,
                            hint: true
                        },
                        {
                            name: 'autocomplete-search-data',
                            source: source
                        }
                    );
                    $(this).off('typeahead:select.SearchInfo').on(
                        'typeahead:select.SearchInfo',
                        function (e, suggestion) {
                            $(e.target.form).trigger('submit');
                        }
                    );
                }
            );
            return true;
        };

        /**
         * This function used for bind ajax submiting form.
         * Selector: `[data-toggle="ajax-form"]`.
         *
         * @function updateSearchForm
         * @memberof SearchInfo
         * @requires jQuery.Form
         * @see      {@link http://malsup.com/jquery/form} jQuery.Form
         * @returns  {boolean}
         */
        SearchInfo.updateSearchForm = function () {
            SearchInfo.updateAutocomplete();

            return true;
        };

        return SearchInfo;
    }
);

/**
 * Registration handler of event `MainAppScripts:update`.
 *  Used for bind event handler on search form.
 *
 * @function ready
 *
 * @returns {null}
 */
$(
    function () {
        $(document).off('MainAppScripts:update.SearchInfo').on(
            'MainAppScripts:update.SearchInfo',
            function () {
                SearchInfo.updateSearchForm();
            }
        );
    }
);
