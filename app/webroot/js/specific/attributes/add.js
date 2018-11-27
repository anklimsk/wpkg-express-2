/**
 * File for action Add of controller Attributes
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
 * @file File for action Add of controller Graph
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsAttributesAdd
 */
var AppActionScriptsAttributesAdd = AppActionScriptsAttributesAdd || {};

(function ($) {
	'use strict';

	/**
	 * This function is used to bind change event for
	 *  update form.
	 *
	 * @function updatePcreParsingCheckbox
	 * @memberof AppActionScriptsAttributesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsAttributesAdd.updatePcreParsingCheckbox = function () {
		$('#AttributePcreParsing').off('change.AppActionScriptsAttributesAdd').on('change.AppActionScriptsAttributesAdd', function(e) {
			var formClass = '.form-tabs';
			var objForm = $(formClass);
			var url = objForm.attr('action');
			var target = $(this);
			var dataToggle = null;
			var pcreParsing = '0';
			var ext = '';
			if (!url) {
				return;
			}

			var extInfo = url.match(/\.(?:mod|pop)$/);
			if (extInfo) {
				ext = extInfo[0];
			}
			url = url.replace(/(\/pcreParsing:[\d]{1}|\.(mod|pop)$)/g, '');
			if (target.is(':checked')) {
				pcreParsing = '1';
			}
			url += '/pcreParsing:' + pcreParsing + ext + ' ' + formClass;
			dataToggle = objForm.attr('data-toggle');
			objForm.parent().load(url, function() {
				if (dataToggle) {
					$(formClass).attr('data-toggle', dataToggle);
				}
				$(document).trigger('MainAppScripts:update');
			});

		});
	};

	return AppActionScriptsAttributesAdd;
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
		$(document).off('MainAppScripts:update.AppActionScriptsAttributesAdd').on(
			'MainAppScripts:update.AppActionScriptsAttributesAdd',
			function () {
				AppActionScriptsAttributesAdd.updatePcreParsingCheckbox();
			}
		);
	}
);
