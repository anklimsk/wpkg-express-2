<?php
/**
 * This file is the controller file of the application. Used to
 *  view statistical information about packages, profiles, hosts,
 *  errors, and the installation status of packages.
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
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used to view statistical information about
 *  packages, profiles, hosts, errors, and the installation status
 *  of packages.
 *
 * This controller allows to perform the following operations:
 *  - to view statistical information.
 *
 * @package app.Controller
 */
class HomeController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Home';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Log',
		'Report',
		'Package',
		'Profile',
		'Host',
		'Statistic',
		'Download'
	];

/**
 * Base of action `index`. Used to view statistical information.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$pageHeader = __('General information');
		$showBreadcrumb = false;

		$limit = 5;
		$stateLogData = $stateReportData = [];
		$lastPackages = $lastProfiles = $lastHosts = [];
		$statistics = [];
		$listExport = [];
		$showSearchForm = false;
		if ($this->UserInfo->checkUserRole(USER_ROLE_ADMIN)) {
			$stateLogData = $this->Log->getBarStateInfo();
			$stateReportData = $this->Report->getBarStateInfo();

			$lastPackages = $this->Package->getLastInfo($limit);
			$lastProfiles = $this->Profile->getLastInfo($limit);
			$lastHosts = $this->Host->getLastInfo($limit);
			$statistics = $this->Statistic->getStaticticsInfo();

			$listExport = $this->Download->getListExports();
			$showSearchForm = true;
		}

		$this->set(compact('pageHeader', 'showBreadcrumb', 'stateLogData', 'stateReportData',
			'lastPackages', 'lastProfiles', 'lastHosts', 'statistics', 'listExport', 'showSearchForm'));
	}

/**
 * Action `index`. Used to view statistical information.
 *  User role - user.
 *
 * @return void
 */
	public function index() {
		$this->_index();
	}

/**
 * Action `index`. Used to view statistical information.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

}
