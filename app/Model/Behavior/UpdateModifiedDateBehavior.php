<?php
/**
 * This file is the behavior file of the application. Is used to
 *  update the modification date of an object.
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
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');

/**
 * The behavior is used to update the modification date of an object
 *
 * @package app.Model.Behavior
 */
class UpdateModifiedDateBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if method "checkDisable" is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!$model->Behaviors->loaded('BreadCrumbExt')) {
			throw new InternalErrorException(__('Behavior %s not loaded in %s', 'BreadCrumbExt', $model->name));
		}
	}

/**
 * Update the modification date of the parent object
 *
 * @param Model $model Model using this behavior
 * @param int|string|array $id ID of record for update
 * @return bool Return True, on success or False on failure.
 * @throws InternalErrorException if field "modified" is not found in parent model
 * @see BreadCrumbExtBehavior::getBreadcrumbInfo()
 */
	public function updateDateById(Model $model, $id) {
		$breadCrumbs = $model->getBreadcrumbInfoById($id);

		return $this->updateDateByBreadCrumbs($model, $breadCrumbs);
	}

/**
 * Update the modification date of the parent object
 *
 * @param Model $model Model using this behavior
 * @param array $breadCrumbs Array of information for creating breadcrumbs and getting
 *  parent object
 * @return bool Return True, on success or False on failure.
 * @throws InternalErrorException if field "modified" is not found in parent model
 * @see BreadCrumbExtBehavior::getBreadcrumbInfo()
 */
	public function updateDateByBreadCrumbs(Model $model, $breadCrumbs = []) {
		if (empty($breadCrumbs) ||
			!is_array($breadCrumbs) ||
			empty($breadCrumbs[1][1]['controller']) ||
			empty($breadCrumbs[1][1][0]) ||
			!ctype_digit($breadCrumbs[1][1][0])) {
			return false;
		}

		$id = $breadCrumbs[1][1][0];
		$modelName = Inflector::singularize($breadCrumbs[1][1]['controller']);
		$parentModel = ClassRegistry::init($modelName, true);
		if (($parentModel === false) || !$parentModel->exists($id)) {
			return false;
		}

		if (!$parentModel->schema('modified')) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", 'modified', $parentModel->name));
		}

		$data = [
			$parentModel->alias => [
				'id' => $id,
				'modified' => date('Y-m-d H:i:s')
			]
		];

		return (bool)$parentModel->save($data, false);
	}

/**
 * Saves model data to the database and update the modification date of the parent object.
 * 
 * @param Model $model Model using this behavior
 * @param array $data Data to save.
 * @param array $breadCrumbs Array of information for creating breadcrumbs and getting
 *  parent object
 * @return mixed On success Model::$data if its not empty or true, false on failure
 */
	public function saveAndUpdateDate(Model $model, $data = null, $breadCrumbs = []) {
		$dataSource = $model->getDataSource();
		$dataSource->begin();
		$result = $model->save($data);
		if (!$result) {
			$dataSource->rollback();
			return $result;
		}

		if (empty($breadCrumbs) && !empty($data[$model->alias][$model->primaryKey])) {
			$breadCrumbs = $model->getBreadcrumbInfoById(
				$data[$model->alias][$model->primaryKey]
			);
		}

		$this->updateDateByBreadCrumbs($model, $breadCrumbs);
		$dataSource->commit();

		return $result;
	}

/**
 * Removes record for given ID and update the modification date of the parent object.
 *
 * @param Model $model Model using this behavior
 * @param int|string $id ID of record to delete
 * @param array $breadCrumbs Array of information for creating breadcrumbs and getting
 *  parent object
 * @return bool True on success
 */
	public function deleteAndUpdateDate(Model $model, $id = null, $breadCrumbs = []) {
		if (empty($id)) {
			$id = $model->id;
		}

		if (empty($breadCrumbs) && !empty($id)) {
			$breadCrumbs = $model->getBreadcrumbInfoById($id);
		}

		$dataSource = $model->getDataSource();
		$dataSource->begin();
		$result = $model->delete($id);
		if (!$result) {
			$dataSource->rollback();
			return $result;
		}

		$this->updateDateByBreadCrumbs($model, $breadCrumbs);
		$dataSource->commit();

		return $result;
	}
}
