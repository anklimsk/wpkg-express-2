<?php
/**
 * This file is the behavior file of the application. Is used to
 *  get a list of data.
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
 * The behavior is used to get a list of data.
 *
 * @package app.Model.Behavior
 */
class GetListBehavior extends ModelBehavior {

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
 * Return list of data
 *
 * @param Model $model Model using this behavior
 * @param array|null $conditions SQL conditions
 * @param string|null $domain Domain for translation
 * @param string|array|null $order SQL ORDER BY
 * @param string|null $nameField Name of field for value of cache
 * @param bool $ucFirst Flag of conversion first char to uppercase
 * @param int|string|null $limit Limit of list
 * @return array Return list of data
 */
	public function getList(Model $model, $conditions = [], $domain = null, $order = null, $nameField = null, $ucFirst = false, $limit = null) {
		if (empty($conditions)) {
			$conditions = [];
		} elseif (!is_array($conditions)) {
			$conditions = [$conditions];
		}
		if (empty($nameField)) {
			$nameField = $model->displayField;
		}

		$currUIlang = (string)Configure::read('Config.language');
		$dataStr = serialize(compact('conditions', 'domain', 'order', 'limit', 'currUIlang'));
		$cachePath = 'ListInfo.' . md5($dataStr);
		$cached = Cache::read($cachePath, $this->settings[$model->alias]['cacheConfig']);
		if (!empty($cached)) {
			return $cached;
		}

		if ($model->hasField('enabled')) {
			$defaultConditions = [$model->alias . '.enabled' => true];
			$conditions = Hash::merge($defaultConditions, $conditions);
		}
		$fields = [
			$model->alias . '.id',
			$model->alias . '.' . $nameField,
		];
		if (empty($order)) {
			$order = [$fields[1] => 'asc'];
		}
		$recursive = -1;

		$result = $model->find('list', compact('conditions', 'fields', 'order', 'recursive', 'limit'));
		if (empty($result)) {
			Cache::write($cachePath, $result, $this->settings[$model->alias]['cacheConfig']);
			return $result;
		}

		if (!empty($domain)) {
			translArray($result, $domain);
		}
		if ($ucFirst) {
			array_walk($result, function (&$v) {
				$v = mb_ucfirst($v);
			});
		}
		Cache::write($cachePath, $result, $this->settings[$model->alias]['cacheConfig']);

		return $result;
	}

/**
 * Return list of data used as cache
 *
 * @param Model $model Model using this behavior
 * @param string $nameField Name of field for value of cache
 * @param int|string $limit Limit of list
 * @return array Return list of data
 */
	public function getCacheData(Model $model, $nameField = null, $limit = null) {
		if (empty($nameField)) {
			$nameField = $model->displayField;
		}

		$dataStr = serialize(compact('nameField', 'limit'));
		$cachePath = 'ListInfo.' . md5($dataStr);
		$cached = Cache::read($cachePath, $this->settings[$model->alias]['cacheConfig']);
		if (!empty($cached)) {
			return $cached;
		}

		$fields = [
			$model->alias . '.' . $nameField,
			$model->alias . '.id',
		];
		$order = [$model->alias . '.' . $model->displayField => 'asc'];
		$recursive = -1;

		$result = $model->find('list', compact('fields', 'order', 'recursive', 'limit'));
		Cache::write($cachePath, $result, $this->settings[$model->alias]['cacheConfig']);

		return $result;
	}

/**
 * Return list of data by prefix constant
 *
 * @param Model $model Model using this behavior
 * @param string $prefix Prefix of constans
 * @param string $domain Domain for translation
 * @return array Constants value with name
 */
	public function getListDataFromConstant(Model $model, $prefix = null, $domain = null) {
		$result = [];
		if (empty($prefix)) {
			return $result;
		}

		$currUIlang = (string)Configure::read('Config.language');
		$dataStr = serialize(compact('prefix', 'domain', 'currUIlang'));
		$cachePath = 'ListInfo.Constant.' . md5($dataStr);
		$cached = Cache::read($cachePath, CACHE_KEY_LISTS_INFO_CONSTANT);
		if (!empty($cached)) {
			return $cached;
		}

		$result = constsToWords($prefix, false);
		if (!empty($domain)) {
			translArray($result, $domain);
		}
		Cache::write($cachePath, $result, CACHE_KEY_LISTS_INFO_CONSTANT);

		return $result;
	}

}
