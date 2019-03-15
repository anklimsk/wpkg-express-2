/**
 * File for action Index of controller Creations
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
 * @file File for action Index of controller Creations
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsCreationsIndex
 */
var AppActionScriptsCreationsIndex = AppActionScriptsCreationsIndex || {};

(function ($) {
	'use strict';

	/**
	 * This function is used for bind CodeMirror.
	 *
	 * @function updateXmlInput
	 * @memberof AppActionScriptsCreationsIndex
	 *
	 * @returns {null}
	 */
	AppActionScriptsCreationsIndex.updateXmlInput = function () {
		var target = $('#CreateXml');
		var prevCodeMirror = target.next('.CodeMirror');
		var selLines = target.data('sel-lines');

		if (prevCodeMirror.length) {
			prevCodeMirror.remove();
		}

		var options = {
			lineNumbers: true,
			lineWrapping: true,
			mode: 'application/xml',
			scrollbarStyle: 'simple',
			matchTags: {bothTags: true},
			extraKeys: {'Ctrl-J': 'toMatchingTag'},
			autoCloseBrackets: true,
			theme: 'eclipse'
		};
		var editor = CodeMirror.fromTextArea(target.get(0), options);
		target.data('code-mirror', editor);

		$.each(selLines, function(i, lineNum) {
			editor.addLineClass(lineNum - 1, 'background', 'mark');
		});
	};

	/**
	 * This function is used to bind click event for
	 *  button `clear`.
	 *
	 * @function updateBtnClear
	 * @memberof AppActionScriptsCreationsIndex
	 *
	 * @returns {null}
	 */
	AppActionScriptsCreationsIndex.updateBtnClear = function () {
		$('#CreateAdminIndexForm button[type="reset"]').off('click.AppActionScriptsCreationsIndex').on('click.AppActionScriptsCreationsIndex', function(e) {
			e.stopPropagation();
			e.preventDefault();

			var target = $('#CreateXml');
			var cm = target.data('code-mirror');
			var doc = cm.getDoc();
			doc.setValue('');
			doc.clearHistory();
		});
	};

	/**
	 * This function is used to bind submit event for
	*  checking form input is not empty.
	 *
	 * @function updateFormSubmit
	 * @memberof AppActionScriptsCreationsIndex
	 *
	 * @returns {null}
	 */
	AppActionScriptsCreationsIndex.updateFormSubmit = function () {
		$('#CreateAdminIndexForm').off('submit.AppActionScriptsCreationsIndex').on('submit.AppActionScriptsCreationsIndex', function(e) {
			var target = $('#CreateXml');
			var cm = target.data('code-mirror');
			var doc = cm.getDoc();
			var value = doc.getValue();

			if ($.trim(value).length === 0) {
				return false;
			}

			return true;
		});
	};

	return AppActionScriptsCreationsIndex;
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
		$(document).off('MainAppScripts:update.AppActionScriptsCreationsIndex').on(
			'MainAppScripts:update.AppActionScriptsCreationsIndex',
			function () {
				AppActionScriptsCreationsIndex.updateXmlInput();
				AppActionScriptsCreationsIndex.updateBtnClear();
				AppActionScriptsCreationsIndex.updateFormSubmit();
			}
		);
	}
);
