/**
 * File for action Add of controller ExitCodes
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @file File for action Add of controller ExitCodes
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsExitCodesAdd
 */
var AppActionScriptsExitCodesAdd = AppActionScriptsExitCodesAdd || {};

(function ($) {
	'use strict';

	/**
	 * This function used as callback for change event for
	 *  retrieve description for exit code.
	 *
	 * @param {object} e Event object
	 *
	 * @callback _getDescription
	 *
	 * @returns {null}
	 */
	function _getDescription(e) {
		var objTargetInput = $(e.target);
		var objHelpBlock = $('#ExitCodeCodeHelpBlock');
		var url = $(e.target).data('description-url');
		var val = objTargetInput.val();
		var dataPost = {
			code: val
		};

		if (!url || !val || (objHelpBlock.length === 0)) {
			return;
		}
		objHelpBlock.html('');
		$.ajax(
			{
				url: url,
				method: 'POST',
				dataType: 'json',
				data: dataPost,
				global: false,
				success: function (data) {
					if (!data.result) {
						return;
					}

					objHelpBlock.html(data.description);
				}
			}
		); 
	}

	/**
	 * This function is used to bind typeahead:change event for
	 *  retrieve description for exit code.
	 *
	 * @function updateInputExitCodeCode
	 * @memberof AppActionScriptsExitCodesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsExitCodesAdd.updateInputExitCodeCode = function () {
		$('#ExitCodeCode').off('typeahead:change.AppActionScriptsExitCodesAdd').on('typeahead:change.AppActionScriptsExitCodesAdd', _getDescription).trigger('typeahead:change.AppActionScriptsExitCodesAdd');
	};

	return AppActionScriptsExitCodesAdd;
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
		$(document).off('MainAppScripts:update.AppActionScriptsExitCodesAdd').on(
			'MainAppScripts:update.AppActionScriptsExitCodesAdd',
			function () {
				AppActionScriptsExitCodesAdd.updateInputExitCodeCode();
			}
		);
	}
);
