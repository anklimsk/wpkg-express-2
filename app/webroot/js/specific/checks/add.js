/**
 * File for action Add of controller Checks
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
 * @file File for action Add of controller Checks
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsChecksAdd
 */
var AppActionScriptsChecksAdd = AppActionScriptsChecksAdd || {};

(function ($) {
	'use strict';

	/**
	 * This function used to AJAX update form by
	 *  check parent ID, check type and check condition.
	 *
	 * @returns {null}
	 */
	function _updateForm() {
		var formClass = '.form-edit-check';
		var objForm = $(formClass);
		var url = objForm.attr('action');
		var selectParent = $('#CheckParentId');
		var selectType = $('#CheckType');
		var selectCondition = $('#CheckCondition');
		var dataToggle = null;
		var ext = '';
		if (!url) {
			return;
		}

		var extInfo = url.match(/\.(?:mod|pop)$/);
		if (extInfo) {
			ext = extInfo[0];
		}
		url = url.replace(/(\/(parent|type|cond):[\d]*|\.(mod|pop)$)/g, '');
		url += '/parent:' + selectParent.val() + '/type:' + selectType.val() + '/cond:' + selectCondition.val() +
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
	 * This function is used to bind change event for
	 *  update form.
	 *
	 * @function updateSelectType
	 * @memberof AppActionScriptsChecksAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsChecksAdd.updateSelectType = function () {
		$('#CheckType').off('change.AppActionScriptsChecksAdd').on('change.AppActionScriptsChecksAdd', _updateForm);
	};

	/**
	 * This function is used to bind change event for
	 *  update form.
	 *
	 * @function updateSelectCondition
	 * @memberof AppActionScriptsChecksAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsChecksAdd.updateSelectCondition = function () {
		$('#CheckCondition').off('change.AppActionScriptsChecksAdd').on('change.AppActionScriptsChecksAdd', _updateForm);
	};

	return AppActionScriptsChecksAdd;
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
		$(document).off('MainAppScripts:update.AppActionScriptsChecksAdd').on(
			'MainAppScripts:update.AppActionScriptsChecksAdd',
			function () {
				AppActionScriptsChecksAdd.updateSelectType();
				AppActionScriptsChecksAdd.updateSelectCondition();
			}
		);
	}
);
