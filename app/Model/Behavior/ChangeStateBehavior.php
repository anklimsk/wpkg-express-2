<?php
/**
 * This file is the behavior file of the application. Is used to
 *  change the value of a table field by id or value of the specified field
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
 * The behavior is used to change the value of a table field by id or value
 *  of the specified field.
 *
 * @package app.Model.Behavior
 */
class ChangeStateBehavior extends ModelBehavior {

/**
 * Defaults
 *
 * @var array
 */
	protected $_defaults = [
		'conditionsField' => 'id_text'
	];

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if specified field is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config + $this->_defaults;
		$conditionsField = $this->settings[$model->alias]['conditionsField'];
		if (!empty($conditionsField) && !$model->hasField($conditionsField)) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", $conditionsField, $model->name));
		}
	}

/**
 * Change the value of a table field by id or value of the specified field.
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID or specified field value to process
 * @param string $field Field name to change value
 * @param bool $state Value to change
 * @throws InternalErrorException if field $field is not found in model
 * @return void
 */
	public function setState(Model $model, $id = null, $field = null, $state = false) {
		if (empty($id)) {
			return false;
		}

		if (empty($field)) {
			$field = 'enabled';
		}
		if (!$model->hasField($field)) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", $field, $model->name));
		}

		if (!ctype_digit((string)$id)) {
			$conditionsField = $this->settings[$model->alias]['conditionsField'];
			if (empty($conditionsField)) {
				return false;
			}

			$conditions = [
				$model->alias . '.' . $conditionsField => $id,
			];

			$id = $model->field('id', $conditions);
			if (empty($id)) {
				return false;
			}
		}
		$model->id = $id;

		return $model->saveField($field, $state);
	}

}
