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
 * @copyright Copyright 2018-2020, Andrey Klimov.
 * @file File for action Index of controller Creations
 * @version 0.2
 */

/**
 * @version 0.2
 * @namespace AppActionScriptsCreationsIndex
 */
var AppActionScriptsCreationsIndex = AppActionScriptsCreationsIndex || {};

(function ($) {
	'use strict';

	function completeAfter(cm, pred) {
		var cur = cm.getCursor();
		if (!pred || pred()) setTimeout(function() {
			if (!cm.state.completionActive)
		cm.showHint({completeSingle: false});
		}, 100);

		return CodeMirror.Pass;
	};

	function completeIfAfterLt(cm) {
		return completeAfter(cm, function() {
			var cur = cm.getCursor();

			return cm.getRange(CodeMirror.Pos(cur.line, cur.ch - 1), cur) == "<";
		});
	};

	function completeIfInTag(cm) {
		return completeAfter(cm, function() {
			var tok = cm.getTokenAt(cm.getCursor());
			if (tok.type == "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1)) return false;
			var inner = CodeMirror.innerMode(cm.getMode(), tok.state).state;

			return inner.tagName;
		});
	};

	/**
	 * This function is used to get XML type.
	 *
	 * @returns {string} XML type
	 */
	function _getXmlType() {
		var xmlType = $('#CreateType').val();
		if (!xmlType) {
			return '';
		}

		var validXmlTypes = [
			'package',
			'profile',
			'host',
			'config',
			'database',
			'directory'
		];

		if ($.inArray(xmlType, validXmlTypes) === -1) {
			return '';
		}

		return xmlType;
	};

	/**
	 * This function is used to get URL for autocomplete by type.
	 *
	 * @param {string} xmlType Type of XML
	 *
	 * @returns {string} Automplete URL
	 */
	function _getAutocompleteUrl(xmlType) {
		if (!xmlType) {
			return '';
		}

		var url = '/files/JSON/' + xmlType + '.json';
		return url;
	};

	/**
	 * This function is used for AJAX getting XML template by type.
	 *
	 * @param {string} xmlType Type of XML
	 *
	 * @returns {string} XML template
	 */
	function _getEmptyTemplate(xmlType) {
		var template = '';
		if (!xmlType) {
			return template;
		}

		var url = '/admin/creations/template';
		var postData = {
			'data': {
				'type': xmlType
			}
		};
		$.ajax(
			{
				url: url,
				async: false,
				method: 'POST',
				data: postData,
				dataType: 'html',
				global: false,
				success: function (response) {
					if (!response) {
						return;
					}

					template = response;
				}
			}
		);

		return template;
	};

	/**
	 * This function is used for AJAX getting data for autocomplete.
	 *
	 * @param {string} url URL for getting data
	 *
	 * @returns {object} Data for autocomplete
	 */
	function _getAutocompleteData(url) {
		var result = {};
		if (!url) {
			return result;
		}

		$.ajax(
			{
				url: url,
				async: false,
				method: 'GET',
				dataType: 'json',
				global: false,
				success: function (response) {
					if (!response) {
						return;
					}

					result = response;
				}
			}
		);

		return result;
	};

	/**
	 * This function is used for bind CodeMirror.
	 *
	 * @function updateXmlInput
	 * @memberof AppActionScriptsCreationsIndex
	 *
	 * @returns {null}
	 */
	AppActionScriptsCreationsIndex.updateXmlInput = function () {
		if (typeof(CodeMirror) === 'undefined') {
			return;
		}

		var target = $('#CreateXml');
		if (target.length === 0) {
			return;
		}

		var prevCodeMirror = target.next('.CodeMirror');
		var selLines = target.data('sel-lines');
		var xmlType = _getXmlType();
		var url = _getAutocompleteUrl(xmlType);
		var tags = _getAutocompleteData(url);

		if (prevCodeMirror.length) {
			prevCodeMirror.remove();
		}

		var options = {
			lineNumbers: true,
			lineWrapping: true,
			mode: 'xml',
			scrollbarStyle: 'simple',
			matchTags: {bothTags: true},
			extraKeys: {
				"'<'": completeAfter,
				"'/'": completeIfAfterLt,
				"' '": completeIfInTag,
				"'='": completeIfInTag,
				'Ctrl-J': 'toMatchingTag',
				'Ctrl-Space': 'autocomplete'
			},
			autoCloseBrackets: true,
			autoCloseTags: true,
			autoRefresh: true,
			hintOptions: {schemaInfo: tags},
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
		$('.form-create-xml button[type="reset"]').off('click.AppActionScriptsCreationsIndex').on('click.AppActionScriptsCreationsIndex', function(e) {
			e.stopPropagation();
			e.preventDefault();

			var target = $('#CreateXml');
			var cm = target.data('code-mirror');
			var doc = cm.getDoc();
			var xmlType = _getXmlType();
			var template = _getEmptyTemplate(xmlType);

			doc.setValue(template);
			doc.clearHistory();
		});
	};

	/**
	 * This function is used to bind change event for
	 *  update XML by type
	 *
	 * @function updateSelectXmlType
	 * @memberof AppActionScriptsCreationsIndex
	 *
	 * @returns {null}
	 */
	AppActionScriptsCreationsIndex.updateSelectXmlType = function () {
		$('#CreateType').off('change.AppActionScriptsCreationsIndex').on('change.AppActionScriptsCreationsIndex', function(e) {
			var target = $('#CreateXml');
			var cm = target.data('code-mirror');
			var doc = cm.getDoc();
			var xmlType = _getXmlType();
			var url = _getAutocompleteUrl(xmlType);
			var tags = _getAutocompleteData(url);
			var template = _getEmptyTemplate(xmlType);

			cm.setOption('hintOptions', {schemaInfo: tags});
			doc.setValue(template);
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
		$('.form-create-xml').off('submit.AppActionScriptsCreationsIndex').on('submit.AppActionScriptsCreationsIndex', function(e) {
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
				AppActionScriptsCreationsIndex.updateSelectXmlType();
				AppActionScriptsCreationsIndex.updateBtnClear();
				AppActionScriptsCreationsIndex.updateFormSubmit();
			}
		);
	}
);
