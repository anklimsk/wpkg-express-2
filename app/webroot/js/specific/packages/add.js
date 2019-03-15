/**
 * File for action Add of controller Packages
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
 * @file File for action Add of controller Packages
 * @version 0.1
 */

/**
 * @version 0.1
 * @namespace AppActionScriptsPackagesAdd
 */
var AppActionScriptsPackagesAdd = AppActionScriptsPackagesAdd || {};

(function ($) {
	'use strict';

	/**
	 * Check changing strategy is initialized.
	 *
	 * @returns {boolean}
	 */
	 function _checkInitStrategy() {
		var inputPkgId = $('#PackageIdText');
		var strategy = inputPkgId.data('strategy');
		if (typeof strategy === 'undefined') {
			return false;
		}

		return true;
	 }

	/**
	 * Return current strategy.
	 *
	 * @returns {integer}
	 */
	function _getStrategy() {
		var strategy = 0;
		if (!_checkInitStrategy()) {
			return strategy;
		}

		var inputPkgId = $('#PackageIdText');
		strategy = inputPkgId.data('strategy');

		return strategy;
	}

	/**
	 * Store strategy.
	 *
	 * @param {integer} strategy Strategy to store
	 *
	 * @returns {null}
	 */
	function _setStrategy(strategy) {
		if ((strategy ^ 0) !== strategy) {
			return;
		}

		var inputPkgId = $('#PackageIdText');
		inputPkgId.data('strategy', strategy);
	}

	/**
	 * Increment strategy.
	 *
	 * @returns {null}
	 */
	function _incrementStrategy() {
		var strategy = _getStrategy();
		strategy++;
		if (strategy > 2) {
			strategy = 0;
		}

		_setStrategy(strategy);
	}

	/**
	 * This function used as callback for click event for
	 *  changing strategy.
	 *
	 * @param {object} e Event object
	 *
	 * @callback _changeStrategy
	 *
	 * @returns {null}
	 */
	function _changeStrategy(e) {
		_incrementStrategy();

		var btnChangeStrategy = $(e.target);
		if (_checkInitStrategy() && btnChangeStrategy.hasClass('btn-default')) {
			btnChangeStrategy.removeClass('btn-default').addClass('btn-success');
		}

		$('#PackageName').trigger('keyup.AppActionScriptsPackagesAdd');
	}

	/**
	 * This function used as callback for keyup event for
	 *  generating package ID from name by strategy.
	 *
	 * @param {object} e Event object
	 *
	 * @callback _generateId
	 *
	 * @returns {null}
	 */
	function _generateId(e) {
		if (!_checkInitStrategy()) {
			return;
		}

		var pkgName = $('#PackageName').val();
		if (pkgName.length === 0) {
			return;
		}

		var strategy = _getStrategy();
		var pkgId = '';
		pkgName = pkgName.replace(/[^\d\w\s\-\_]+/g, '');
		switch (strategy) {
			case 1:
				pkgId = pkgName.replace(/[\s]+/g, '_');
			break;
			case 2:
				var words = pkgName.split(/[\s\_\-]/);
				$.each(words, function (i, word) {
					pkgId += word.charAt(0).toUpperCase();
				});
			break;
			case 0:
			default:
				pkgName = pkgName.toLowerCase();
				pkgId = pkgName.replace(/(^|[\s\_\-])\S/g, function(c) { return c.toUpperCase(); }).replace(/[\s\_\-]+/g, '');
			break;
		}
		$('#PackageIdText').val(pkgId).change();
	}

	/**
	 * This function used as callback for click event for
	 *  setting priority form input from form select value.
	 *
	 * @param {object} e Event object
	 *
	 * @callback _setPriority
	 *
	 * @returns {null}
	 */
	function _setPriority(e) {
		e.preventDefault();

		var priorityValue = $(e.target).data('priority-value');
		if (typeof priorityValue === 'number') {
			$('#PackagePriority').val(priorityValue);
		}
	}

	/**
	 * This function used as callback for change event to
	 *  exclude the intersection of dependency lists.
	 *
	 * @param {object} e Event object
	 *
	 * @callback _updateListDependencies
	 *
	 * @returns {null}
	 */
	function _updateListDependencies(e) {
		var currentSelVal = $(this).val();
		var currentSelId = $(this).attr('id');
		var listSelectId = [
			'DependsOnDependsOn',
			'IncludesIncludes',
			'ChainsChains'
		];

		$.each(listSelectId, function(i, selectId) {
			if (currentSelId == selectId) {
				return true;
			}
			var objDependSelect = $("#" + selectId);
			var valDependSelect = objDependSelect.val();

			if (!!valDependSelect) {
				var diffVal = $.grep(valDependSelect, function(n, i){
					return $.inArray(n, currentSelVal) < 0;
				});

				objDependSelect.val(diffVal);
			}
		});

		$('.dependency-select').selectpicker('render');
	}

	/**
	 * This function is used to bind click event for
	 *  changing strategy.
	 *
	 * @function updateBtnGenerateId
	 * @memberof AppActionScriptsPackagesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsPackagesAdd.updateBtnGenerateId = function () {
		$('#btnGenerateId').off('click.AppActionScriptsPackagesAdd').on('click.AppActionScriptsPackagesAdd', _changeStrategy);
	};

	/**
	 * This function is used to bind keyup event for
	 *  generating package ID from name by strategy.
	 *
	 * @function updateInputPackageName
	 * @memberof AppActionScriptsPackagesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsPackagesAdd.updateInputPackageName = function () {
		$('#PackageName').off('keyup.AppActionScriptsPackagesAdd').on('keyup.AppActionScriptsPackagesAdd', _generateId);
	};

	/**
	 * This function is used to bind click event for
	 *  setting priority form input from form select value.
	 *
	 * @function updateListDropdownButtonPackagePriority
	 * @memberof AppActionScriptsPackagesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsPackagesAdd.updateListDropdownButtonPackagePriority = function () {
		$('#DropdownPackagePriorities li > a').off('click.AppActionScriptsPackagesAdd').on('click.AppActionScriptsPackagesAdd', _setPriority);
	};

	/**
	 * This function is used to bind keyup event to
	 *  exclude the intersection of dependency lists.
	 *
	 * @function updateListDependencies
	 * @memberof AppActionScriptsPackagesAdd
	 *
	 * @returns {null}
	 */
	AppActionScriptsPackagesAdd.updateListDependencies = function () {
		$('.dependency-select').off('change.AppActionScriptsPackagesAdd').on('change.AppActionScriptsPackagesAdd', _updateListDependencies);
	};

	return AppActionScriptsPackagesAdd;
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
		$(document).off('MainAppScripts:update.AppActionScriptsPackagesAdd').on(
			'MainAppScripts:update.AppActionScriptsPackagesAdd',
			function () {
				AppActionScriptsPackagesAdd.updateBtnGenerateId();
				AppActionScriptsPackagesAdd.updateInputPackageName();
				AppActionScriptsPackagesAdd.updateListDropdownButtonPackagePriority();
				AppActionScriptsPackagesAdd.updateListDependencies();
			}
		);
	}
);
