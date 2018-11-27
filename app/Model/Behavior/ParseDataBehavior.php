<?php
/**
 * This file is the behavior file of the application. Is used for
 *  caching data and render error on parsing XML.
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
 * The behavior is used for caching data and render 
 *  error on parsing XML.
 *
 * @package app.Model.Behavior
 */
class ParseDataBehavior extends ModelBehavior {

/**
 * Cache of records by name
 *
 * @var array
 */
	protected $_cacheNames = [];

/**
 * Cache of models
 *
 * @var array
 */
	protected $_cacheModels = [];

/**
 * Create cache of records by name
 *
 * @param Model $model Model using this behavior
 * @param string $modelName Name of model
 * @param string $nameField Name of field for value of cache
 * @param bool $toLowerCase Convert field value to lowercase
 * @param string $cacheKey Additional key for cache
 * @param int|string $limit Limit of list
 * @return bool Success
 */
	public function createNamesCache(Model $model, $modelName = null, $nameField = null, $toLowerCase = true, $cacheKey = null, $limit = null) {
		if (empty($cacheKey)) {
			$cacheKey = 'default';
		}
		$objModel = $this->getModelNamesCache($model, $modelName);
		$data = $objModel->getCacheData($nameField, $limit);
		if ($toLowerCase) {
			$procData = [];
			foreach ($data as $key => $value) {
				$key = mb_strtolower($key);
				$procData[$key] = $value;
			}
			$data = $procData;
			unset($procData);
		}
		$this->_cacheNames[$modelName][$cacheKey] = $data;

		return true;
	}

/**
 * Return record ID from cache by name
 *
 * @param Model $model Model using this behavior
 * @param string $modelName Name of model
 * @param string $name Name for retrieve data
 * @param mixed $default Default value if no data is
 *  found in the cache.
 * @param bool $toLowerCase Convert $name value to lowercase
 * @param string $cacheKey Additional key for cache
 * @return mixed Retrun record ID or default value
 */
	public function getIdFromNamesCache(Model $model, $modelName = null, $name = null, $default = null, $toLowerCase = true, $cacheKey = null) {
		if (empty($modelName) || empty($name)) {
			return $default;
		}

		if ($toLowerCase) {
			$name = mb_strtolower($name);
		}
		if (empty($cacheKey)) {
			$cacheKey = 'default';
		}
		if (!isset($this->_cacheNames[$modelName][$cacheKey][$name])) {
			return $default;
		}

		return $this->_cacheNames[$modelName][$cacheKey][$name];
	}

/**
 * Add record ID with name to cache
 *
 * @param Model $model Model using this behavior
 * @param string $modelName Name of model
 * @param string $name Name of data
 * @param int|string $id Record ID to add
 * @param bool $toLowerCase Convert $name value to lowercase
 * @param string $cacheKey Additional key for cache
 * @return bool Success
 */
	public function setIdNamesCache(Model $model, $modelName = null, $name = null, $id = null, $toLowerCase = true, $cacheKey = null) {
		if (empty($modelName) || empty($name) || empty($id)) {
			return false;
		}

		if ($toLowerCase) {
			$name = mb_strtolower($name);
		}
		if (empty($cacheKey)) {
			$cacheKey = 'default';
		}
		if (!isset($this->_cacheNames[$modelName])) {
			return false;
		}

		$this->_cacheNames[$modelName][$cacheKey][$name] = $id;
		return true;
	}

/**
 * Remove record ID with name from cache
 *
 * @param Model $model Model using this behavior
 * @param string $modelName Name of model
 * @param string $name Name of data
 * @param bool $toLowerCase Convert $name value to lowercase
 * @param string $cacheKey Additional key for cache
 * @return bool Success
 */
	public function resetIdNamesCache(Model $model, $modelName = null, $name = null, $toLowerCase = true, $cacheKey = null) {
		if (empty($modelName) || empty($name)) {
			return false;
		}

		if ($toLowerCase) {
			$name = mb_strtolower($name);
		}
		if (empty($cacheKey)) {
			$cacheKey = 'default';
		}
		if (!isset($this->_cacheNames[$modelName])) {
			return false;
		}

		if (!isset($this->_cacheNames[$modelName][$cacheKey][$name])) {
			return false;
		}
		unset($this->_cacheNames[$modelName][$cacheKey][$name]);

		return true;
	}

/**
 * Return object of model by model name from cache and add, if not exists
 *
 * @param Model $model Model using this behavior
 * @param string $modelName Name of model for retrieve object
 * @throws InternalErrorException if method "getAllForGraph" is not found in model
 * @return object Retrun object of model
 */
	public function getModelNamesCache(Model $model, $modelName = null) {
		if (empty($modelName)) {
			throw new InternalErrorException(__('Invalid name of model for cache'));
		}
		if (isset($this->_cacheModels[$modelName])) {
			return $this->_cacheModels[$modelName];
		}

		$objModel = ClassRegistry::init($modelName, true);
		if ($objModel === false) {
			throw new InternalErrorException(__('Invalid name of model for cache'));
		}
		$this->_cacheModels[$modelName] = $objModel;

		return $objModel;
	}

/**
 * Return a formatted list of XML parsing errors
 *
 * @param Model $model Model using this behavior
 * @param array $data Data of XML parsing errors
 * @param int|string $deep Limit for deep recursion
 * @param int|string $level Current level of recursion
 * @return string Return a formatted list of XML parsing errors
 */
	public function renderErrorMessages(Model $model, $data = [], $deep = 10, $level = 0) {
		$result = '';
		if (empty($data) || ($level > $deep)) {
			return $result;
		}

		$level++;
		$needCloseTag = false;
		foreach ($data as $msgtype => $msginfo) {
			if (!is_int($msgtype)) {
				$result .= '<li><b>' . $msgtype . ':</b>';
				$needCloseTag = true;
			}
			if (is_array($msginfo)) {
				$result .= $this->renderErrorMessages($model, $msginfo, $deep, $level);
				if ($needCloseTag) {
					$needCloseTag = false;
					$result .= '</li>';
				}
			} else {
				if ($needCloseTag) {
					$needCloseTag = false;
					$result .= '</li>';
				}
				$result .= '<li>' . $msginfo . '</li>';
			}
		}
		if (!empty($result)) {
			$result = '<ul>' . $result . '</ul>';
		}

		return $result;
	}

}
