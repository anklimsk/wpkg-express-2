/**
 * File for action View of controller Charts
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
 * @file File for action View of controller Charts
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsChartsView
 */
var AppActionScriptsChartsView = AppActionScriptsChartsView || {};

(function ($) {
	'use strict';

	/**
	 * This function used to hide message.
	 *
	 * @returns {null}
	 */
	function _hideMessages() {
		$('#chartListMessages .alert').addClass('hidden');
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

		var objMsg = $('#msgChart' + id);
		if (objMsg.length === 0) {
			return;
		}
		_hideMessages();
		objMsg.removeClass('hidden');
	}

	/**
	 * This function used for AJAX get data for chart.
	 *
	 * @param {string} url URL for getting data
	 * @param {integer} refType ID type of object chart
	 * @param {integer} refId Record ID of object chart
	 *
	 * @returns {object} Data for chart
	 */
	function _getChartData(url, refType, refId) {
		if (!url || !refType || !refId) {
			return false;
		}

		var result = false;
		var dataPost = {
			refType: refType,
			refId: refId
		};
		$.ajax(
			{
				url: url,
				async: false,
				method: 'POST',
				dataType: 'json',
				data: dataPost,
				global: false,
				success: function (response) {
					if (!response) {
						result = {};
						return;
					}

					result = response;
				}
			}
		);

		return result;
	}

	/**
	 * This function is used to create chart.
	 *
	 * @function updateChart
	 * @memberof AppActionScriptsChartsView
	 *
	 * @returns {null}
	 */
	AppActionScriptsChartsView.updateChart = function () {
		var target = $('#chart-wpkg');
		if (!target) {
			_showMessage('Error');
			return;
		}

		var displayTitle = true;
		var removeCanvas = false;
		var refType = target.data('chart-ref-type');
		var refId = target.data('chart-ref-id');
		var url = target.data('chart-url');
		var chartType = target.data('chart-type');
		if (!chartType) {
			chartType = 'doughnut';
		}
		var chartTitle = target.data('chart-title');
		if (!chartTitle) {
			chartTitle = '';
			displayTitle = false;
		}
		var chartClickUrl = target.data('chart-click-url');

		var chartData = _getChartData(url, refType, refId);
		if (chartData === false) {
			_showMessage('Error');
			removeCanvas = true;
		} else if (chartData.length === 0) {
			_showMessage('NoData');
			removeCanvas = true;
		} else {
			_hideMessages();
		}

		if (removeCanvas) {
			target.remove();
			return;
		}

		var options = {};
		var ctx = target.get(0).getContext('2d');
		var chart = new Chart(
			ctx,
			{
				type: chartType,
				data: chartData,
				options: {
					title: {
						display: displayTitle,
						text: chartTitle
					}
				}
			},
			options
		);

		if (!chartClickUrl) {
			return;
		}

		target.off('click.AppActionScriptsChartsView').on('click.AppActionScriptsChartsView', function(e) {
			var activePoints = chart.getElementsAtEvent(e);
			if (!activePoints[0]) {
				return false;
			}

			var lastChartUrl = chartClickUrl.charAt(chartClickUrl.length - 1);
			if ((lastChartUrl !== '=') && (lastChartUrl !== '/')) {
				chartClickUrl += '/';
			}

			var chartData = activePoints[0]['_chart'].config.data;
			var idx = activePoints[0]['_index'];

			var label = chartData.labels[idx];
			var value = chartData.datasets[0].data[idx];

			var url = chartClickUrl + label;

			window.open(url, '_blank');
			window.focus();

			return false;
		});
	};

	return AppActionScriptsChartsView;
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
		$(document).off('MainAppScripts:update.AppActionScriptsChartsView').on(
			'MainAppScripts:update.AppActionScriptsChartsView',
			function () {
				AppActionScriptsChartsView.updateChart();
			}
		);
	}
);
