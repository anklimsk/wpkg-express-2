<?php
/**
 * This file is the model file of the application. Used to
 *  manage dependency data.
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
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used to manage dependency data.
 *
 * @package app.Model
 */
class Dependency extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Return object Model for type.
 *
 * @param int|string $type Type of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	protected function _getTypeModel($type) {
		if (empty($type)) {
			return false;
		}

		$modelName = null;
		$type = mb_strtolower($type);
		switch ($type) {
			case 'packagespackage':
				$modelName = 'PackagesPackage';
				break;
			case 'packagesinclude':
				$modelName = 'PackagesInclude';
				break;
			case 'packageschain':
				$modelName = 'PackagesChain';
				break;
			case 'packagesprofile':
				$modelName = 'PackagesProfile';
				break;
			case 'profilesprofile':
				$modelName = 'ProfilesProfile';
				break;
			case 'hostsprofile':
				$modelName = 'HostsProfile';
				break;
			default:
				return false;
		}

		$result = ClassRegistry::init($modelName, true);

		return $result;
	}

/**
 * Deleting record by type and record ID.
 *
 * @param string $type Type of data to delete
 * @param int|string $id Record ID to delete
 * @throws InternalErrorException if $type is invalid model name
 * @throws NotFoundException if record for parameter $id was not found
 * @return bool|string Return True, if possible. False on failure or
 *  error message if not possible.
 */
	public function deleteRecord($type = null, $id = null) {
		$modelType = $this->_getTypeModel($type);
		if (!$modelType) {
			throw new InternalErrorException(__('Invalid type for deleting record'));
		}

		$modelType->id = $id;
		if (!$modelType->exists()) {
			throw new NotFoundException(__('Invalid ID for deleting record'));
		}

		$result = false;
		if ($modelType->Behaviors->loaded('UpdateModifiedDate')) {
			$resultDelete = $modelType->deleteAndUpdateDate();
		} else {
			$resultDelete = $modelType->delete();
		}
		if ($resultDelete) {
			$result = true;
		} elseif ($modelType->Behaviors->loaded('CanDisable')) {
			$result = $modelType->getMessageCheckDisable();
		}

		return $result;
	}
}
