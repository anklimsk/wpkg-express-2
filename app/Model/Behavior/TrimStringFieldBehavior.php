<?php
/**
 * This file is the behavior file of the application. Is used to
 *  strip whitespace (or other characters) from the beginning and
 *  end of a string fields.
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
 * The behavior is used to strip whitespace of a string fields.
 *
 * @package app.Model.Behavior
 */
class TrimStringFieldBehavior extends ModelBehavior {

/**
 * Return list of fields to trim
 *
 * @param Model $model Model using this behavior
 * @return array Return list of fields to trim
 */
	protected function _getListTrimFields(Model $model) {
		$modelName = $model->name;
		$cachePath = 'list_trim_fields.' . $modelName;
		$cached = Cache::read($cachePath, CACHE_KEY_MODEL_CFG_INFO);
		if ($cached !== false) {
			return $cached;
		}

		$schema = $model->schema();
		$result = [];
		if (empty($schema)) {
			return $result;
		}

		foreach ($schema as $field => $info) {
			if (!isset($info['type']) ||
				!in_array($info['type'], ['string', 'text'])) {
				continue;
			}

			$result[] = $field;
		}
		Cache::write($cachePath, $result, CACHE_KEY_MODEL_CFG_INFO);

		return $result;
	}

/**
 * Strip whitespace (or other characters) from the beginning and
 *  end of a string data.
 *
 * @param Model $model Model using this behavior
 * @return bool Success
 */
	protected function _trimDataFields(Model $model) {
		$trimFields = $this->_getListTrimFields($model);
		if (empty($trimFields)) {
			return true;
		}

		foreach ($trimFields as $field) {
			if (!isset($model->data[$model->alias][$field]) ||
				empty($model->data[$model->alias][$field]) ||
				!is_string($model->data[$model->alias][$field])) {
				continue;
			}

			$model->data[$model->alias][$field] = trim($model->data[$model->alias][$field]);
		}

		return true;
	}

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * Actions:
 *  - Strip whitespace (or other characters) from the beginning and
 *    end of a string data.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$this->_trimDataFields($model);

		return true;
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * Actions:
 *  - Strip whitespace (or other characters) from the beginning and
 *    end of a string data.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		$this->_trimDataFields($model);

		return true;
	}

}
