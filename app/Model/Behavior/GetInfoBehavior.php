<?php
/**
 * This file is the behavior file of the application. Is used to
 *  get a list of last changed data.
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
 * The behavior is used to get a list of last changed data
 *
 * @package app.Model.Behavior
 */
class GetInfoBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if field "modified" is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!$model->schema('modified')) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", 'modified', $model->name));
		}
	}

/**
 * Return list of last changed data
 *
 * @param Model $model Model using this behavior
 * @param int|string $limit Limit of list
 * @return array Return list of last changed data
 */
	public function getLastInfo(Model $model, $limit = null) {
		$result = [];
		$fields = [
			$model->alias . '.id',
			$model->alias . '.modified',
			$model->alias . '.' . $model->displayField,
		];
		$order = [$model->alias . '.modified' => 'desc'];
		$recursive = -1;
		$data = $model->find('all', compact('fields', 'order', 'recursive', 'limit'));
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $dataItem) {
			$result[] = [
				'id' => $dataItem[$model->alias]['id'],
				'label' => $dataItem[$model->alias][$model->displayField],
				'modified' => $dataItem[$model->alias]['modified']
			];
		}

		return $result;
	}
}
