<?php
/**
 * This file is the model file of the application. Used to
 *  recover a corrupted tree (list).
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
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used to recover a corrupted tree (list).
 *
 * @package app.Model
 */
class RecoverTree extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Recover a corrupted tree (list)
 *
 * @param string $targetModelName Model name to recover.
 * @param int|string $refType ID type of object
 * @param int|string $refId Record ID of the object
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function recover($targetModelName = null, $refType = null, $refId = null, $idTask = null) {
		$modelExtendQueuedTask = ClassRegistry::init('ExtendQueuedTask');
		if (empty($targetModelName)) {
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid arguments'));
			return false;
		}

		$modelTarget = ClassRegistry::init($targetModelName, true);
		if (!$modelTarget) {
			return false;
		}

		if ($modelTarget->Behaviors->loaded('ScopeTree')) {
			if (empty($refType)) {
				$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid arguments'));
				return false;
			}

			if (!$modelTarget->setScopeModel($refType, $refId)) {
				$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Error on setting scope of tree (list)'));
				return false;
			}
		}

		if (!$modelTarget->recover()) {
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Error on recovering tree (list)'));
			return false;
		}

		return true;
	}

}
