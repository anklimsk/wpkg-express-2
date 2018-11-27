/**
 * This file use for table filter
 *
 * @file    Main file for ViewExtension
 * @version 0.13.0
 * @copyright 2016-2018 Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.ViewExtension = factory();
    }
})(
    this,
    function () {
        'use strict';

        /**
         * @version 0.13.0
         * @namespace ViewExtension
         */
        var ViewExtension = {};

        /**
         * Limit of items in multiple value of input
         *
         * @constant {integer} _truncateMultipleLimit
         */
        var _truncateMultipleLimit = 10;

        /**
         * Check array contains value
         *
         * @param {array} a Target array
         * @param {*} v Value for checking
         *
         * @link http://stackoverflow.com/a/11247412
         *
         * @returns {boolean}
         */
        function _arrayContains(a, v)
        {
            var length = a.length;
            for (var i = 0; i < length; i++) {
                if (a[i] === v) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Get unique values in an array
         *
         * @param {array} a Target array
         *
         * @link http://stackoverflow.com/a/11247412
         *
         * @returns {array} Unique values of array
         */
        function _arrayUnique(a)
        {
            var arr    = [];
            var length = a.length;
            for (var i = 0; i < length; i++) {
                if (!_arrayContains(arr, a[i])) {
                    arr.push(a[i]);
                }
            }

            return arr;
        }

        /**
         * Return limit of items in multiple value of input.
         *
         * @function _getTruncateMultipleLimit
         *
         * @returns {integer} Limit of items
         */
        function _getTruncateMultipleLimit()
        {
            return _truncateMultipleLimit;
        }

        /**
         * This function is used to get the value from the form element
         *
         * @param {object} targetObj Object of target input element
         *
         * @function _getStrInputVal
         *
         * @returns {array}
         */
        function _getStrInputVal(targetObj)
        {
            var type = targetObj.attr('type');
            var val  = '';
            switch (type) {
                case 'radio':
                case 'checkbox':
                    var checked = targetObj.is(':checked');
                    if (!checked) {
                        return val;
                    }

                    val = targetObj.val();
                break;

                case 'hidden':
                    val = '';
                break;

                default:
                    var tagName = targetObj.prop('tagName').toLowerCase();
                    switch (tagName) {
                        case 'select':
                            var selected        = targetObj.children('option:selected');
                            var selectedVal     = null;
                            var selectedTexts   = [];
                            var selectedItemObj = null;
                            $.each(
                                selected,
                                function (index, selectedItem) {
                                          selectedItemObj = $(selectedItem);
                                          selectedVal     = selectedItemObj.val();
                                    if (selectedVal) {
                                        selectedTexts.push(selectedItemObj.text());
                                    }
                                }
                            );
                            if (selectedTexts.length > 0) {
                                val = selectedTexts.join(', ');
                            }
                        break;

                        case 'input':
                            val = targetObj.val();
                        break;

                        default:
                            val = '';
                        break;
                    }//end switch
                break;
            }//end switch

            return val;
        }

        /**
         * Build string of filter conditions from form inputs value
         *
         * @returns {string} String of filter conditions
         */
        function _buildFilterCondition()
        {
            var result      = '';
            var targetInput = $('.filter-form :input:not(.filter-condition)');
            if (targetInput.length === 0) {
                return result;
            }

            var conditions            = [];
            var targetItemObj         = null;
            var name                  = null;
            var tagName               = null;
            var val                   = '';
            var index                 = null;
            var condText              = null;
            var titleField            = null;
            var condTextTemp          = null;
            var titleFieldTemp        = null;
            var groupCond             = null;
            var isMultipleValue       = false;
            var multVal               = [];
            var cacheMultipleValue    = [];
            var truncateMultipleLimit = _getTruncateMultipleLimit();
            var objMultipleVal        = null;

            $.each(
                targetInput,
                function (index, targetItem) {
                    val           = '';
                    targetItemObj = $(targetItem);
                    name          = targetItemObj.attr('name');
                    if (!name) {
                        return true;
                    }

                    tagName = targetItemObj.prop('tagName').toLowerCase();
                    if (((name.length > 2) && (name.substr(-2) === '[]'))
                        || ((tagName === 'select') && (targetItemObj.prop('multiple')))
                    ) {
                          isMultipleValue = true;
                    }

                    multVal = [];
                    if (isMultipleValue) {
                        if ($.inArray(name, cacheMultipleValue) !== -1) {
                            return true;
                        }

                        objMultipleVal = $('.filter-form :input[name="' + name + '"]');
                        $.each(
                            objMultipleVal,
                            function (index, multipleValItem) {
                                var multipleValItemObj = $(multipleValItem);
                                var valItem            = _getStrInputVal(multipleValItemObj);
                                if (valItem) {
                                    if (multVal.length > truncateMultipleLimit) {
                                        return true;
                                    } else if (multVal.length === truncateMultipleLimit) {
                                        multVal.push('...');
                                        return true;
                                    } else {
                                        multVal.push(valItem);
                                    }
                                }
                            }
                        );
                        multVal    = _arrayUnique(multVal);
                        var valStr = multVal.join(', ');
                        if (valStr) {
                            val = '[ ' + valStr + ' ]';
                        }

                        cacheMultipleValue.push(name);
                    } else {
                        val = _getStrInputVal(targetItemObj);
                    }//end if

                    if (!val) {
                        return true;
                    }

                    condText     = '=';
                    condTextTemp = targetItemObj.parents('.form-group').find('button.btn-filter-condition').text();
                    if (condTextTemp) {
                        condText = condTextTemp;
                    }

                    titleFieldTemp = targetItemObj.data('filter-label');
                    if (titleFieldTemp) {
                        titleField = titleFieldTemp;
                    } else {
                        var thIndex = targetItemObj.parents('th').index();
                        if (thIndex === -1) {
                            thIndex = targetItemObj.parents('td').index();
                        }

                        if (thIndex !== -1) {
                            titleFieldTemp = targetItemObj.parents('table').find('thead > tr:eq(0) > th:eq(' + thIndex + ')').text();
                            if (titleFieldTemp) {
                                titleField = titleFieldTemp;
                            }
                        }
                    }

                    if (targetItemObj.attr('data-toggle') === 'autocomplete') {
                        val = '*' + val + '*';
                    }

                    conditions.push(titleField + ' ' + $.trim(condText) + ' ' + val);
                }
            );

            groupCond = $('[name="data[FilterCond][group]"]:first').parents('.btn-group').children('button[data-toggle="dropdown"]').text();
            if (!groupCond) {
                groupCond = '&&';
            }

            groupCond  = ' ' + $.trim(groupCond) + ' ';
            conditions = _arrayUnique(conditions);
            result     = conditions.join(groupCond);
            return result;
        }

        /**
         * Set condition button to default - `equal`.
         *
         * @param {object} context jQuery object of cloned filter row
         *
         * @returns {null}
         */
        function _resetConditionBtn(context)
        {
            if (!context) {
                return;
            }

            var targetInput = context.find('input.filter-condition[type="radio"][value=""]');
            if (targetInput.length === 0) {
                return;
            }

            targetInput.trigger('click');
        }

        /**
         * This function used for bind ajax reset form.
         *
         * @function updateFilterForm
         * @memberof ViewExtension
         * @requires jQuery.Form, jQuery.Pjax
         * @see      {@link http://malsup.com/jquery/form} jQuery.Form,
         *  {@link https://github.com/defunkt/jquery-pjax} PJAX
         *
         * @returns {boolean}
         */
        ViewExtension.updateFilterForm = function () {
            var target = $('form.filter-form');
            if (target.length === 0) {
                return true;
            }

            target.off('reset.ViewExtension').on(
                'reset.ViewExtension',
                function (e) {
                    var form = $(this);
                    form.clearForm();
                    $('select', this).val('');
                    $('[data-toggle="select"]', this).selectpicker('refresh');
                    var toggle = form.data('toggle');
                    if (toggle === 'ajax-form') {
                        if (!jQuery.fn.ajaxSubmit) {
                            return true;
                        }
                    } else if (toggle === 'pjax-form') {
                        if (!jQuery.fn.pjax || !jQuery.support.pjax) {
                            return true;
                        }
                    }

                    form.trigger('submit');
                    return false;
                }
            );

            return true;
        };

        /**
    * This function used for bind jQuery.Cloneya plugin.
    * Selector: `[data-toggle="clone-target"], .clone-wrapper`.
     *
    * @function updateCloneFilterRow
    * @memberof ViewExtension
    * @requires jQuery.Cloneya
    * @see      {@link https://github.com/Yapapaya/jquery-cloneya} jQuery.Cloneya
    *
    * @returns {boolean}
    */
        ViewExtension.updateCloneFilterRow = function () {
            if (!jQuery.fn.cloneya) {
                return false;
            }

            var target = ($('[data-toggle="clone-target"],.clone-wrapper'));
            if (target.length === 0) {
                return true;
            }

            target.off('after_append.cloneya.ViewExtension').on(
                'after_append.cloneya.ViewExtension',
                function (event, toclone, newclone) {
                    _resetConditionBtn(newclone);
                }
            ).off('after_delete.cloneya.ViewExtension').on(
                'after_delete.cloneya.ViewExtension',
                function (event, toclone, newclone) {
                    ViewExtension.setConditiosText();
                }
            );
            return true;
        };

        /**
         * This function used to update text of current filter condition
         * Selector: `[data-toggle="filter-conditions"]`.
         *
         * @function setConditiosText
         * @memberof ViewExtension
         *
         * @returns {boolean}
         */
        ViewExtension.setConditiosText = function () {
            var conditionsTag = $('[data-toggle="filter-conditions"]');
            if (conditionsTag.length === 0) {
                return false;
            }

            var conditionsText = _buildFilterCondition();
            if (!conditionsText) {
                conditionsText = conditionsTag.data('empty-text');
            }

            var oldConditions = conditionsTag.html();
            if (conditionsText !== oldConditions) {
                conditionsTag.html(conditionsText);
            }

            return true;
        };

        /**
         * This function is used to bind events to form inputs for
         *  dynamic update condition text from input value.
         *
         * @function updateFilterInput
         * @memberof ViewExtension
         *
         * @returns {null}
         */
        ViewExtension.updateFilterInput = function () {
            $('.filter-form :input').off('keydown.ViewExtension').on(
                'keydown.ViewExtension',
                function (e) {
                    ViewExtension.setConditiosText();
                }
            );
            $('.filter-form :input').off('paste.ViewExtension').on(
                'paste.ViewExtension',
                function (e) {
                    ViewExtension.setConditiosText();
                }
            );
            $('.filter-form :input').off('input.ViewExtension').on(
                'input.ViewExtension',
                function (e) {
                    ViewExtension.setConditiosText();
                }
            );
            $('.filter-form :input').off('blur.ViewExtension').on(
                'blur.ViewExtension',
                function (e) {
                    ViewExtension.setConditiosText();
                }
            );
            $('.filter-form :input').off('change.ViewExtension').on(
                'change.ViewExtension',
                function (e) {
                    setTimeout(
                        function () {
                            ViewExtension.setConditiosText();
                        },
                        0
                    );
                }
            );
            $('.filter-form :input[data-toggle="datetimepicker"]').off('dp.change.ViewExtension').on(
                'dp.change.ViewExtension',
                function (e) {
                    ViewExtension.setConditiosText();
                }
            );
        };

        /**
         * This function used to bind click events to
         *  button `Select / deselect all` for change icon and
         *  select or deselect all ID checkboxes.
         *
         * @function updateSelectAllBtn
         * @memberof ViewExtension
         *
         * @returns {null}
         */
        ViewExtension.updateSelectAllBtn = function () {
            $('[data-toggle="btn-action-select-all"]').off('click.ViewExtension').on(
                'click.ViewExtension',
                function (e) {
                    e.preventDefault();
                    var stateCheckbox = $(this).data('select-state');
                    if (typeof stateCheckbox === 'undefined') {
                        stateCheckbox = true;
                    }

                    $(this).data('select-state', !stateCheckbox);

                    $('.filter-form tr [type=checkbox]').prop('checked', stateCheckbox).trigger('change');
                }
            );
        };

        /**
         * This function used to bind click events to button `Execute action`
         *
         * @function updateGroupActionBtn
         * @memberof ViewExtension
         * @requires Bootbox,jQuery.Chosen
         * @see      {@link https://github.com/needim/noty noty,
         *  {@link http://bootboxjs.com} Bootbox,
         *  {@link https://harvesthq.github.io/chosen} Chosen
         *
         * @returns {null}
         */
        ViewExtension.updateGroupActionBtn = function () {
            $('.filter-form button[name="data[FilterAction]"][value="group-action"]').off('click.ViewExtension').on(
                'click.ViewExtension',
                function (e) {
                    e.preventDefault();
                    var title          = $(this).data('dialog-title');
                    var labelBtnOk     = $(this).data('dialog-btn-ok');
                    var labelBtnCancel = $(this).data('dialog-btn-cancel');
                    var placeholder    = $(this).data('dialog-sel-placeholder');
                    var inputOptions   = $(this).data('dialog-sel-options');
                    if (!title) {
                        title = 'Select';
                    }

                    if ((typeof inputOptions !== 'object') || (inputOptions.length === 0)) {
                        $(this).prop('disabled', true);
                        return false;
                    }

                    if (typeof Noty !== 'undefined') {
                        var objNoty = new Noty(
                            {
                                text: '',
                                closeWith: ['button'],
                                force: true,
                                modal: true,
                                layout: 'center',
                                theme: 'bootstrap-v3',
                                type: 'alert',
                                buttons: [
                                Noty.button(
                                    labelBtnOk,
                                    'btn btn-warning',
                                    function () {
                                        var action = $('div.noty_body #selectTaskInputDialog').val();
                                        objNoty.close();
                                        if (action) {
                                            $('.filter-form input[name="data[FilterGroup][action]"]').val(action);
                                            $(e.target).trigger('submit');
                                        }
                                    },
                                    {id: 'buttonOk', 'data-status': 'ok'}
                                ),
                            Noty.button(
                                labelBtnCancel,
                                'btn btn-default',
                                function () {
                                    objNoty.close();
                                },
                                {id: 'buttonCancel', 'data-status': 'cancel'}
                            )
                            ],
                            callbacks: {
                                beforeShow: function () {
                                    var selectInput = $('<select id="selectTaskInputDialog" data-toggle="select"/>');
                                    $('<option />', {value: '', text: placeholder}).appendTo(selectInput);
                                    $(inputOptions).each(
                                        function () {
                                            $('<option />', {value: this.value, text: this.text}).appendTo(selectInput);
                                        }
                                    );

                                    var div = $('<div class="form-group" />').append('<label for="selectTaskInputDialog" class="control-label">' + placeholder + '</label>');
                                    div.append(selectInput);
                                    this.setText(div.html(), true);
                                },
                                onShow: function () {
                                    $('div.noty_buttons #buttonCancel', $(this.barDom)).focus();
                                    var targetSelect = $('#selectTaskInputDialog', $(this.barDom));
                                    targetSelect.off('change.ViewExtension').on(
                                        'change.ViewExtension',
                                        function (e) {
                                            var state = true;
                                            if ($(this).val()) {
                                                state = false;
                                            }

                                            $(this).parents('div.noty_bar').find('div.noty_buttons #buttonOk').prop('disabled', state);
                                        }
                                    );
                                    targetSelect.trigger('change');

                                    if (typeof MainAppScripts !== 'undefined') {
                                        MainAppScripts.updateSelect();
                                    }
                                },
                                onTemplate: function () {
                                    $('div.noty_buttons', $(this.barDom)).addClass('text-center').css('border-top', '1px solid #e7e7e7');
                                    $('div.noty_buttons button', $(this.barDom)).css('margin', '0 2px');
                                    $(this.barDom).css('overflow', 'visible');
                                }
                                }
                            }
                        ).show();
                    } else if (typeof bootbox !== 'undefined') {
                        var locale = $('html').attr('lang');
                        if (!locale) {
                            locale = 'en';
                        }

                        bootbox.setLocale(locale);
                        var dialog = bootbox.prompt(
                            {
                                size: 'small',
                                title: title,
                                buttons: {
                                    cancel: {
                                        label: labelBtnCancel,
                                        className: 'btn-default'
                                    },
                                    confirm: {
                                        label: labelBtnOk,
                                        className: 'btn-warning'
                                    }
                                },
                                inputType: 'select',
                                inputOptions: inputOptions,
                                callback: function (result) {
                                    if (!result) {
                                        return;
                                    }

                                    $('.filter-form input[name="data[FilterGroup][action]"]').val(result);
                                    $(e.target).trigger('submit');
                                }
                            }
                        );
                        dialog.init(
                            function () {
                                var targetSelect = $('.modal-body .bootbox-input-select');
                                if (targetSelect.length === 0) {
                                    return;
                                }

                                targetSelect.off('change.ViewExtension').on(
                                    'change.ViewExtension',
                                    function (e) {
                                        var state = true;
                                        if ($(this).val()) {
                                            state = false;
                                        }

                                         $('.modal-footer button[data-bb-handler="confirm"]').prop('disabled', state);
                                    }
                                );
                                targetSelect.trigger('change');

                                if (typeof MainAppScripts !== 'undefined') {
                                    MainAppScripts.updateSelect();
                                }
                            }
                        );
                    } else {
                        var list = '';
                        $.each(
                            inputOptions,
                            function (i, options) {
                                if (list) {
                                    list += ' ';
                                }

                                list += (i + 1) + '. ' + options.text + ' - ' + options.value + '.';
                            }
                        );
                        title          += ' [' + list + ']';
                        var action      = '';
                        var actionIndex = 0;
                        do {
                            action      = prompt(title);
                            actionIndex = parseInt(action, 10);
                            if (action === null) {
                                actionIndex = -1;
                                break;
                            } else if (isNaN(actionIndex)) {
                                continue;
                            }

                            actionIndex--;
                            if (typeof inputOptions[actionIndex] !== 'undefined') {
                                break;
                            }
                        } while (true)if (actionIndex >= 0) {
                            $('.filter-form input[name="data[FilterGroup][action]"]').val(inputOptions[actionIndex].value);
                            $(e.target).trigger('submit');
                        }
                    }//end if

                    return false;
                }
            );
        };

        return ViewExtension;
    }
);

/**
 * Registration handler of event `MainAppScripts:update`.
 *  Used for bind event handler on filter form.
 *
 * @function ready
 *
 * @returns {null}
 */
$(
    function () {
        $(document).off('MainAppScripts:update.ViewExtension').on(
            'MainAppScripts:update.ViewExtension',
            function () {
                ViewExtension.updateFilterForm();
                ViewExtension.updateCloneFilterRow();
                ViewExtension.updateFilterInput();
                ViewExtension.updateSelectAllBtn();
                ViewExtension.updateGroupActionBtn();
                ViewExtension.setConditiosText();
            }
        );
    }
);
