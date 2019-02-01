<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the dependencies.
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
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for management information about the dependencies.
 *
 * This controller allows to perform the following operations:
 *  - delete dependency records.
 *
 * @package app.Controller
 */
class DependenciesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Dependencies';

/**
 * Base of action `delete`. Used to delete dependency record.
 *
 * @param string $type Type of data to delete
 * @param int|string $id Record ID to delete
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _delete($type = null, $id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl();
		$resultDelete = $this->Dependency->deleteRecord($type, $id);
		if ($resultDelete === true) {
			$this->Flash->success(__('Record has been deleted.'));
		} else {
			if ($resultDelete === false) {
				$this->Flash->error(__('Record could not be deleted. Please, try again.'));
			} else {
				$this->Flash->warning($resultDelete);
			}
		}

		return $this->ViewExtension->redirectByUrl();
	}

/**
 * Action `delete`. Used to delete dependency record.
 *  User role - administrator.
 *
 * @param string $type Type of data to delete
 * @param int|string $id Record ID to delete
 * @return void
 */
	public function admin_delete($type = null, $id = null) {
		$this->_delete($type, $id);
	}

}
