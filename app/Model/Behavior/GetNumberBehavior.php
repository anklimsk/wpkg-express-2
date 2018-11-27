<?php
/**
 * This file is the behavior file of the application. Is used to
 *  get a number of data.
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
 * The behavior is used to get a number of data.
 *
 * @package app.Model.Behavior
 */
class GetNumberBehavior extends ModelBehavior {

/**
 * Defaults
 *
 * @var array
 */
	protected $_defaults = [
		'cacheConfig' => null
	];

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if specified name of the cache
 *  configuration is invalid.
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config + $this->_defaults;
		$cacheConfig = $this->settings[$model->alias]['cacheConfig'];
		if (empty($cacheConfig)) {
			throw new InternalErrorException(__('Invalid name of the cache configuration to use'));
		}
	}

/**
 * afterSave Callback
 *
 * Actions:
 *  - Clear cache.
 *
 * @param Model $model Model the callback is called on
 * @param bool $created Whether or not the save created a record.
 * @param array $options Options passed from Model::save().
 * @return bool true.
 */
	public function afterSave(Model $model, $created, $options = []) {
		Cache::clear(false, $this->settings[$model->alias]['cacheConfig']);
		return true;
	}

/**
 * After delete method.
 *
 * Actions:
 *  - Clear cache.
 *
 * @param Model $model Model using this behavior
 * @return bool true to continue, false to abort the delete
 */
	public function afterDelete(Model $model) {
		Cache::clear(false, $this->settings[$model->alias]['cacheConfig']);
		return true;
	}

/**
 * Return the number of data
 *
 * @param Model $model Model using this behavior
 * @param array $conditions SQL conditions
 * @return int Number of data
 */
	public function getNumberOf(Model $model, $conditions = null) {
		$dataStr = serialize($conditions);
		$cachePath = 'NumberInfo.' . md5($dataStr);
		$cached = Cache::read($cachePath, $this->settings[$model->alias]['cacheConfig']);
		if (!empty($cached)) {
			return $cached;
		}

		$recursive = -1;
		$result = $model->find('count', compact('conditions', 'recursive'));
		Cache::write($cachePath, $result, $this->settings[$model->alias]['cacheConfig']);

		return $result;
	}

}
