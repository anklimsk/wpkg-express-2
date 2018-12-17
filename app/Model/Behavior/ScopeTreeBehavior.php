<?php
/**
 * This file is the behavior file of the application. Is used to
 *  set scope for Tree behavior.
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
 * The behavior is used to set scope for Tree behavior.
 *
 * @package app.Model.Behavior
 */
class ScopeTreeBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if Behavior Tree not loaded in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!$model->Behaviors->loaded('Tree')) {
			throw new InternalErrorException(__('Behavior %s not loaded in %s', 'Tree', $model->name));
		}
		$model->Behaviors->setPriority('ScopeTree', 5);
	}

/**
 * Set scope for Tree behavior.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refType ID type of object
 * @param int|string $refId Record ID of the object
 * @return bool Success.
 */
	public function setScopeModel(Model $model, $refType = null, $refId = null) {
		if (empty($refType) || empty($refId)) {
			return false;
		}

		$scope = [];
		switch ($model->name) {
			case 'Check':
			case 'Variable':
				$scope = [
					$model->alias . '.ref_id' => $refId,
					$model->alias . '.ref_type' => $refType
				];
				break;
			case 'PackageAction':
				$scope = [
					$model->alias . '.package_id' => $refId,
					$model->alias . '.action_type_id' => $refType
				];
				break;
			default:
				return false;
		}

		$model->Behaviors->load('Tree', compact('scope'));

		return true;
	}

/**
 * Modify scope for Tree behavior.
 *
 * @param Model $model Model using this behavior.
 * @return bool Success.
 */
	public function modifyScopeModelIfY(Model $model) {
		if ($model->Behaviors->Tree->settings[$model->alias]['scope'] !== '1 = 1') {
			return true;
		}

		switch ($model->name) {
			case 'Check':
			case 'Variable':
				$refIdField = 'ref_id';
				$refTypeField = 'ref_type';
				break;
			case 'PackageAction':
				$refIdField = 'package_id';
				$refTypeField = 'action_type_id';
				break;
			default:
				return false;
		}

		if (isset($model->data[$model->alias][$refIdField]) && isset($model->data[$model->alias][$refTypeField])) {
			$refId = $model->data[$model->alias][$refIdField];
			$refType = $model->data[$model->alias][$refTypeField];
		} elseif ($model->id) {
			$refId = $model->field($refIdField);
			$refType = $model->field($refTypeField);
		} else {
			return false;
		}

		return $this->setScopeModel($model, $refType, $refId);
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * Actions:
 *  - Modify scope for Tree behavior.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = []) {
		return $this->modifyScopeModelIfY($model);
	}

/**
 * Before delete is called before any delete occurs on the attached model, but after the model's
 * beforeDelete is called. Returning false from a beforeDelete will abort the delete.
 *
 * Actions:
 *  - Modify scope for Tree behavior.
 *
 * @param Model $model Model using this behavior
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return mixed False if the operation should abort. Any other result will continue.
 */
	public function beforeDelete(Model $model, $cascade = true) {
		return $this->modifyScopeModelIfY($model);
	}
}
