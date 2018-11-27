<?php
/**
 * This file is the behavior file of the application. Is used for processing
 *  group data.
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
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');

/**
 * The behavior is used for processing group data.
 *
 * @package app.Model.Behavior
 */
class GroupActionBehavior extends ModelBehavior {

/**
 * Change field state for multiple records
 *
 * @param Model $model Model using this behavior
 * @param bool $state Value to change
 * @param array $conditions SQL conditions
 * @throws InternalErrorException if field "enabled" is not found in model
 * @return bool Success
 */
	public function changeStateAll(Model $model, $state = false, $conditions = []) {
		if (!$model->schema('enabled')) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", 'enabled', $model->name));
		}

		if (empty($conditions)) {
			return false;
		}

		$recursive = -1;
		$data = $model->find('all', compact('conditions', 'recursive'));
		if (empty($data)) {
			return false;
		}

		foreach ($data as &$dataItem) {
			$dataItem[$model->alias]['enabled'] = $state;
		}
		unset($dataItem);

		return (bool)$model->saveAll($data);
	}

/**
 * Process group action
 *
 * @param Model $model Model using this behavior
 * @param string $groupAction Name of group action for processing
 * @param array $conditions SQL conditions of group action for processing
 * @return null|bool Return Null, on failure. If success, return True,
 *  False otherwise.
 */
	public function processGroupAction(Model $model, $groupAction = null, $conditions = null) {
		if (($groupAction === false) || empty($conditions)) {
			return null;
		}

		$result = false;
		$state = null;
		switch ($groupAction) {
			case 'group-data-dis':
				$state = false;
			case 'group-data-enb':
				if ($state === null) {
					$state = true;
				}
				$result = $this->changeStateAll($model, $state, $conditions);
				break;
			case 'group-data-del':
				$result = $model->deleteAll($conditions, true, true);
				break;
		}

		return $result;
	}

}
