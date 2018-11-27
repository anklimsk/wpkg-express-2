<?php
/**
 * This file is the behavior file of the application. Is used to
 *  processing moving and drag and drop items.
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

App::uses('MoveBehavior', 'CakeTheme.Model/Behavior');

/**
 * The behavior is used to processing moving and drag and drop items.
 *
 * @package app.Model.Behavior
 */
class MoveExtBehavior extends MoveBehavior {

/**
 * Modify scope for Tree behavior.
 *
 * @param Model $model Model using this behavior
 * @param int $id ID of record for processing
 * @return bool Success.
 */
	protected function _modifyScopeModelIfY(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}

		if ($model->Behaviors->loaded('ScopeTree')) {
			$model->id = $id;
			if (!$model->modifyScopeModelIfY()) {
				return false;
			}
		}

		return true;
	}

/**
 * Move item to new position of tree
 *
 * @param Model $model Model using this behavior.
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int $id ID of record for moving
 * @param int $delta Delta for moving
 * @throws InternalErrorException if delta for moving < 0
 * @throws InternalErrorException if direction for moving not is: `up`, `down`, `top` or `bottom`
 * @triggers Model.beforeUpdateTree $model array($options)
 * @triggers Model.afterUpdateTree $model
 * @return bool Success
 */
	public function moveItem(Model $model, $direct = null, $id = null, $delta = 1) {
		if (!$this->_modifyScopeModelIfY($model, $id)) {
			return false;
		}

		return parent::moveItem($model, $direct, $id, $delta);
	}

/**
 * Move item to new position of tree use drag and drop.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id The ID of the item to moving to new position.
 * @param int|string|null $newParentId New parent ID of item.
 * @param int|string|null $oldParentId Old parent ID of item.
 * @param array $dropData Array of ID subtree for item.
 * @return bool
 * @triggers Model.beforeUpdateTree $model array($options)
 * @triggers Model.afterUpdateTree $model
 * @see https://github.com/johnny/jquery-sortable
 */
	public function moveDrop(Model $model, $id = null, $newParentId = null, $oldParentId = null, $dropData = null) {
		if (!$this->_modifyScopeModelIfY($model, $id)) {
			return false;
		}

		return parent::moveDrop($model, $id, $newParentId, $oldParentId, $dropData);
	}
}
