/**
 * File for action View of controller Graph
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
 * @file File for action View of controller Graph
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsGraphView
 */
var AppActionScriptsGraphView = AppActionScriptsGraphView || {};

(function ($) {
	'use strict';

	/**
	 * This function used to hide message.
	 *
	 * @returns {null}
	 */
	function _hideMessages() {
		$('#graphListMessages .alert').addClass('hidden');
	}

	/**
	 * This function used to show message by ID.
	 *
	 * @param {string} id ID of message
	 *
	 * @returns {null}
	 */
	function _showMessage(id) {
		if (!id) {
			return;
		}

		var objMsg = $('#msgGraph' + id);
		if (objMsg.length === 0) {
			return;
		}
		_hideMessages();
		objMsg.removeClass('hidden');
	}

	/**
	 * This function used to clear graph.
	 *
	 * @returns {null}
	 */
	function _clearGraph() {
		$('#graph-svg').html('');
	}

	/**
	 * This function used to enable or disable form.
	 *
	 * @param {boolean} state State of form
	 *
	 * @returns {null}
	 */
	function _disableForm(state) {
		var objForm = $('#GraphVizGenerateForm');
		objForm.find('fieldset').attr('disabled', state);
		objForm.find('input').attr('disabled', state);
	}

	/**
	 * This function used to enable or disable download link.
	 *
	 * @param {boolean} state State of form
	 *
	 * @returns {null}
	 */
	function _disableDownloadLink(state) {
		var objLink = $('#linkDownloadGraph');
		if (state) {
			objLink.addClass('disabled');
		} else {
			objLink.removeClass('disabled').off('click.MainAppScripts');
		}
	}

	/**
	 * Callback function used for clear graph, show message, block
	 *  download link and form.
	 *  Callback function to be invoked before the form is submitted.
	 *
	 * @param {array} arr The form data in array format
	 * @param {object} $form jQuery-wrapped form element
	 * @param {object} options Object passed into ajaxForm/ajaxSubmit
	 *
	 * @callback _ajaxFormBeforeSubmit
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	function _ajaxFormBeforeSubmit(arr, $form, options) {
		_showMessage('Progress');
		_clearGraph();
		_disableDownloadLink(true);
		_disableForm(true);
	}

	/**
	 * Callback function used for show graph, show message, unblock
	 *  download link and form, bind Zoom to graph.
	 *  Callback function to be invoked after the form has been submitted.
	 *
	 * @param {*} responseText Value (depending on the value of the dataType option)
	 * @param {string} statusText
	 * @param {object} xhr
	 * @param {object} $form jQuery-wrapped form element
	 *
	 * @callback _ajaxFormResponse
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	function _ajaxFormSuccess(responseText, statusText, xhr, $form) {
		_disableForm(false);
		if (!responseText) {
			_showMessage('NoData');
			return;
		}

		_hideMessages();
		_disableDownloadLink(false);
		$('#graph-svg').html(responseText);
		_updateDownloadLink();
		_bindZoomGraph();
	}

	/**
	 * Callback function used for clear graph, show message, unblock
	 *  form.
	 *  Callback function to be invoked upon error
	 *
	 * @param {object} xhr
	 * @param {string} status
	 * @param {string} error
	 * @param {object} $form jQuery-wrapped form element
	 *
	 * @callback _ajaxFormError
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	function _ajaxFormError(xhr, status, error, $form) {
		_disableForm(false);
		_showMessage('Error');
		_clearGraph();

		if (xhr.status === 403) {
			location.reload();
		}
	}

	/**
	 * Callback function used to resize graph on resize window.
	 *
	 * @callback _resizeSvgGraph
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	function _resizeSvgGraph() {
		var svg = $('#graph-svg svg')[0];
		if (!svg) {
			return;
		}

		var panZoomGraph = svgPanZoom(svg);
		panZoomGraph.resize();
		panZoomGraph.fit();
		panZoomGraph.center();
	}

	/**
	 * This function used to download graph by click.
	 *
	 * @returns {null}
	 */
	function _updateDownloadLink() {
		var objLink = $('#linkDownloadGraph');
		var svg = $('#graph-svg svg')[0];
		var serializer = new XMLSerializer();
		var source = serializer.serializeToString(svg);

		if (objLink.length === 0) {
			return;
		}
		if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
			source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
		}
		if(!source.match(/^<svg[^>]+"http\:\/\/www\.w3\.org\/1999\/xlink"/)){
			source = source.replace(/^<svg/, '<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
		}

		source = '<?xml version="1.0" standalone="no"?>\r\n' + source;
		var url = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(source);
		var fileName = objLink.data('file-name');
		if (!fileName) {
			fileName = 'graph.svg';
		}

		objLink.attr({
			'href': url,
			'download': fileName
		});
	}

	/**
	 * This function used to bind svgPanZoom to graph.
	 *
	 * @returns {null}
	 */
	function _bindZoomGraph() {
		var svg = $('#graph-svg svg')[0];
		if (!svg) {
			return;
		}

		svg.setAttribute('width', '100%');
		svg.setAttribute('preserveAspectRatio', 'xMinYMin meet');
		var opt = {
			'maxZoom': 30,
			controlIconsEnabled: true,
			zoomScaleSensitivity: 0.5,
			fit: true,
			center: true
		};
		svgPanZoom(svg, opt);
		_resizeSvgGraph();
	}

	/**
	 * This function is used to bind submit event for
	 *  AJAX submit form.
	 *
	 * @function updateForm
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	AppActionScriptsGraphView.updateForm = function () {
		$('#GraphVizGenerateForm').off('submit.AppActionScriptsGraphView').on(
			'submit.AppActionScriptsGraphView',
			function (e) {
				e.preventDefault();
				$(this).ajaxSubmit(
					{
						beforeSubmit: _ajaxFormBeforeSubmit,
						success: _ajaxFormSuccess,
						error: _ajaxFormError
					}
				);
			}
		);
	};

	/**
	 * This function is used to bind change event for
	 *  AJAX submit form on change value flag full graph.
	 *
	 * @function updateForm
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	AppActionScriptsGraphView.updateCheckboxShowFull = function () {
		$('#GraphVizFullGraph').off('change.AppActionScriptsGraphView').on('change.AppActionScriptsGraphView', function (e) {
			$('#GraphVizGenerateForm').trigger('submit');
		});
	};

	/**
	 * This function is used to bind resize event for
	 *  resize graph on resize window.
	 *
	 * @function updateForm
	 * @memberof AppActionScriptsGraphView
	 *
	 * @returns {null}
	 */
	AppActionScriptsGraphView.updateWindowResize = function () {
		$(window).off('resize.AppActionScriptsGraphView').on('resize.AppActionScriptsGraphView', _resizeSvgGraph);
	};

	return AppActionScriptsGraphView;
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
		$(document).off('MainAppScripts:update.AppActionScriptsGraphView').on(
			'MainAppScripts:update.AppActionScriptsGraphView',
			function () {
				AppActionScriptsGraphView.updateForm();
				AppActionScriptsGraphView.updateCheckboxShowFull();
				AppActionScriptsGraphView.updateWindowResize();

				if (!$('#graph-svg').data('graph-init')) {
					$('#graph-svg').data('graph-init', true);
					$('#GraphVizGenerateForm').trigger('submit');
				}
			}
		);
	}
);
