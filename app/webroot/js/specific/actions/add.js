/**
 * File for action Add of controller Actions
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @file File for action Add of controller Actions
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsActionsAdd
 */
var AppActionScriptsActionsAdd = AppActionScriptsActionsAdd || {};

(function ($) {
	'use strict';

	/**
	 * This function used to AJAX update form by
	 *  action type and command type.
	 *
	 * @returns {null}
	 */
	function _updateForm() {
		var formClass = '.form-edit-action';
		var objForm = $(formClass);
		var url = objForm.attr('action');
		var selectActionType = $('#PackageActionActionTypeId');
		var selectCommandType = $('#PackageActionCommandTypeId');
		var dataToggle = null;
		var ext = '';
		if (!url) {
			return;
		}

		var extInfo = url.match(/\.(?:mod|pop)$/);
		if (extInfo) {
			ext = extInfo[0];
		}
		url = url.replace(/(\/(action|command):[\d]*|\.(mod|pop)$)/g, '');
		url += '/action:' + selectActionType.val() +
			'/command:' + selectCommandType.val() +
			ext + ' ' + formClass;
		dataToggle = objForm.attr('data-toggle');
		objForm.parent().load(url, function() {
			if (dataToggle) {
				$(formClass).attr('data-toggle', dataToggle);
			}
			$(document).trigger('MainAppScripts:update');
		});
	}

	/**
	 * This function used to autocomplete data in action command form input.
	 *
	 * @param {string} term Query string
	 * @param {callback} callback Callback function for process response
	 * @param {string} url URL for getting data
	 * @param {string} pkgId ID of package
	 * @param {string} type Type of request
	 *
	 * @returns {null}
	 */
	function _search(term, callback, url, pkgId, type) {
		var data = {
			'query': term,
			'pkg-id': pkgId,
			'type': type
		};
		$.post(url, data, function (resp) { callback(resp); }, 'json').fail(function () { callback([]); });
	}

	/**
	 * This function is used to bind change event for
	 *  update form.
	 *
	 * @function updateActionType
	 * @memberof AppActionScriptsActionsAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsActionsAdd.updateActionType = function () {
		$('#PackageActionActionTypeId').off('change.AppActionScriptsActionsAdd').on('change.AppActionScriptsActionsAdd', _updateForm);
	};

	/**
	 * This function is used to bind change event for
	 *  update form.
	 *
	 * @function updateCommandType
	 * @memberof AppActionScriptsActionsAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsActionsAdd.updateCommandType = function () {
		$('#PackageActionCommandTypeId').off('change.AppActionScriptsActionsAdd').on('change.AppActionScriptsActionsAdd', _updateForm);
	};

	/**
	 * This function is used to bind keypress event for
	 *  using to autocomplete data in action command form input.
	 *
	 * @function updateCommandTextarea
	 * @memberof AppActionScriptsActionsAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsActionsAdd.updateCommandTextarea = function () {
		var textareaCommand = $('#PackageActionCommand').off('keypress.AppActionScriptsActionsAdd').on('keypress.AppActionScriptsActionsAdd', function (e) {
			if (e.keyCode == 13) {
				e.preventDefault();
			}
		});
		if (textareaCommand.length === 0) {
			return;
		}

		var Textarea = Textcomplete.editors.Textarea;
		var editor = new Textarea(textareaCommand.get(0));
		var textcomplete = new Textcomplete(editor);
		var url = textareaCommand.data('autocomplete-url');
		var pkgId = textareaCommand.data('autocomplete-pkg-id');
		textcomplete.register(
			[
				{
					match: /(%COMSPEC%\s+)(\/\w*)$/i,
					search: function (term, callback) {
						_search(term, callback, url, pkgId, 'command');
					},
					cache: true,
					replace: function (word) {
						return '$1' + word;
					}
				},
				{
					match: /(%)(\w+)$/,
					search: function (term, callback) {
						_search(term, callback, url, pkgId, 'variable');
					},
					cache: true,
					replace: function (word) {
						return '$1' + word + '%';
					}
				},
				{
					match: /(\s+)(\/\w*)$/,
					search: function (term, callback) {
						_search(term, callback, url, pkgId, 'switch');
					},
					cache: true,
					replace: function (word) {
						return '$1' + word;
					}
				}
			],
			{ debounce: 500 }
		);
	};

	return AppActionScriptsActionsAdd;
})(jQuery);

/**
 * Registration handler of event `MainAppScripts:update`
 *
 * @function ready
 *
 * @returns {null}
 */
$(
	function () {
		$(document).off('MainAppScripts:update.AppActionScriptsActionsAdd').on(
			'MainAppScripts:update.AppActionScriptsActionsAdd',
			function () {
				AppActionScriptsActionsAdd.updateActionType();
				AppActionScriptsActionsAdd.updateCommandType();
				AppActionScriptsActionsAdd.updateCommandTextarea();
			}
		);
	}
);
