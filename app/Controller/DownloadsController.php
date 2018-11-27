<?php
/**
 * This file is the controller file of the application. Used for
 *  preview and download XML files.
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
 * The controller is used for preview and download XML files.
 *
 * This controller allows to perform the following operations:
 *  - preview and download XML files.
 *
 * @package app.Controller
 */
class DownloadsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-ActionTypes
 */
	public $name = 'Downloads';

/**
 * Base of action `index`. Used to view a full list of exported XML files.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$listDownload = $this->Download->getListDownloads();
		$breadCrumbs = $this->Download->getBreadcrumbInfo();
		$breadCrumbs[] = __('Index');
		$pageHeader = __('Downloading XML files');

		$this->set(compact('listDownload', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to view a full list of exported XML files.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}
}
