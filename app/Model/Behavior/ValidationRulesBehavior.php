<?php
/**
 * This file is the behavior file of the application. Used as 
 *  rules for data validation.
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
 * The behavior is used as rules for data validation.
 *
 * @package app.Model.Behavior
 */
class ValidationRulesBehavior extends ModelBehavior {

/**
 * Return list of HABTM associated models
 *
 * @param Model $model Model using this behavior
 * @return array Return list of models
 */
	protected function _getListHABTMmodel(Model $model) {
		$habtmModels = [];
		if (empty($model->hasAndBelongsToMany)) {
			return $habtmModels;
		}

		$habtmModels = array_keys($model->hasAndBelongsToMany);
		return $habtmModels;
	}

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * Actions:
 *  - Prepare HABTM data to validate.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$habtmModels = $this->_getListHABTMmodel($model);
		if (empty($habtmModels)) {
			return true;
		}

		foreach ($habtmModels as $habtmModel) {
			if (isset($model->data[$habtmModel][$habtmModel])) {
				$model->data[$model->name][$habtmModel] = $model->data[$habtmModel][$habtmModel];
			}
		}

		return true;
	}

/**
 * afterValidate is called just after model data was validated, you can use this callback
 * to perform any data cleanup or preparation if needed
 *
 * Actions:
 *  - Restore HABTM data after validate.
 *
 * @param Model $model Model using this behavior
 * @return mixed False will stop this event from being passed to other behaviors
 */
	public function afterValidate(Model $model) {
		$habtmModels = $this->_getListHABTMmodel($model);
		if (empty($habtmModels)) {
			return true;
		}

		foreach ($habtmModels as $habtmModel) {
					unset($model->data[$model->name][$habtmModel]);
					if (isset($model->validationErrors[$habtmModel])) {
						$model->$habtmModel->validationErrors[$habtmModel] = $model->validationErrors[$habtmModel];
					}
		}

		return true;
	}

/**
 * Returns False if field passed match any of their matching values.
 *  Use flag of case sensitivity from settings `caseSensitivity`.
 *
 * @param Model $model Model using this behavior
 * @param array $data Field/value pairs to search
 * @return bool False if any records matching a field are found
 */
	public function isUniqueID(Model $model, $data = null) {
		if (empty($data)) {
			return false;
		}

		$fields = array_keys($data);
		$field = array_shift($fields);
		$modelConfig = ClassRegistry::init('Config');
		$caseSensitivity = $modelConfig->getConfig('caseSensitivity');
				$conditions = [$model->alias . '.' . $field . ' = \'' . $data[$field] . '\''];

				if ($model->isDataSourceMysql()) {
					if ($caseSensitivity) {
						$conditions[0] = 'BINARY ' . $model->alias . '.' . $field . ' = \'' . $data[$field] . '\'';
					}
				} else {
					if (!$caseSensitivity) {
						$conditions[0] = 'LOWER(' . $model->alias . '.' . $field . ') = LOWER(\'' . $data[$field] . '\')';
					}
				}

		if (!empty($model->id)) {
			$conditions[$model->alias . '.' . $model->primaryKey . ' !='] = $model->id;
		}
		$recursive = -1;

		return !$model->find('count', compact('conditions', 'recursive'));
	}

/**
 * Returns False if the data passed does not match the list of values
 *  based on the constant prefix.
 *
 * @param Model $model Model using this behavior
 * @param array $data Field/value pairs to search
 * @param string $prefix Prefix of constans
 * @param bool $includeFieldName Flag of including the field name in the
 *  constant prefix
 * @return bool False if the data passed does not match the list of values
 */
	public function checkRange(Model $model, $data = null, $prefix = null, $includeFieldName = false) {
		if (empty($data) || empty($prefix)) {
			return false;
		}

		$value = reset($data);
		$field = key($data);
		if ($includeFieldName) {
			$prefix .= mb_strtoupper($field) . '_';
		}

		return in_array($value, constsVals($prefix));
	}

/**
 * Returns False if the data passed does match the dependent field value.
 *
 * @param Model $model Model using this behavior
 * @param array $data Field/value pairs to search
 * @param string $fieldNameDepend Name of dependent field
 * @return bool Returns False if the data passed does match the dependent field value.
 */
	public function selfDependency(Model $model, $data = null, $fieldNameDepend = null) {
		if (empty($data)) {
			return true;
		}

		if (empty($fieldNameDepend)) {
			return false;
		}

		if (!isset($model->data[$model->alias][$fieldNameDepend])) {
			return true;
		}

		$value = (array)reset($data);
		if (in_array($model->data[$model->alias][$fieldNameDepend], $value)) {
			return false;
		}

		return true;
	}

}