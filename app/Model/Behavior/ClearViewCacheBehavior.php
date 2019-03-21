<?php
/**
 * This file is the behavior file of the application. Used to clear
 *  the View cache.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('Inflector', 'Utility');

/**
 * The behavior is used to clear the View cache.
 *
 * @package app.Model.Behavior
 */
class ClearViewCacheBehavior extends ModelBehavior {

/**
 * Parameters for clearCache
 *
 * @var string|array
 */
	protected $_clearCacheParam = null;

/**
 * Return parameters for clearCache
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to retrieve parameters
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache(Model $model, $id = null) {
		$paramItem = Inflector::pluralize($model->name);
		$result = 'wpkg_' . mb_strtolower($paramItem) . '*_xml';

		return $result;
	}

/**
 * Store parameters for clearCache
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to store parameters
 * @param bool $force Flag forced update parameters before store
 * @return bool Success
 */
	public function storeClearCacheParam(Model $model, $id = null, $force = true) {
		if (empty($this->_clearCacheParam) || $force) {
			$this->_clearCacheParam = $model->getParamClearCache($id);
		}

		return !empty($this->_clearCacheParam);
	}

/**
 * Clear the View cache
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to clear the View cache
 * @param bool $forceStoreParam Flag forced update parameters before
 *  clear cache
 * @return bool Success
 */
	public function clearCache(Model $model, $id = null, $forceStoreParam = true) {
		if (!$this->storeClearCacheParam($model, $id, $forceStoreParam)) {
			return false;
		}

		return $this->_clearCache($this->_clearCacheParam);
	}

/**
 * Used to delete and invalidates a cached script files in the cache directories
 *
 * @param string|array $params As String name to be searched for processing, if name is a directory all files in
 *   directory will be processed. If array, names to be searched for processing. If clearCache() without params,
 *   all files in app/tmp/cache/views will be processed
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are processing
 * @return true if files found and deleted false otherwise
 */
	protected function _clearOPcache($params = null, $type = 'views', $ext = '.php') {
		$clearOPcache = false;
		if (!empty($ext) && (mb_stripos($ext, '.php') !== false) &&
			extension_loaded('Zend OPcache')) {
			$clearOPcache = true;
		}

		if (is_string($params) || $params === null) {
			$params = preg_replace('/\/\//', '/', $params);
			$cache = CACHE . $type . DS . $params;

			$file = $cache . $ext;
			if (is_file($file)) {
				if ($clearOPcache && opcache_is_script_cached($file)) {
					opcache_invalidate($file, true);
				}

				//@codingStandardsIgnoreStart
				@unlink($cache . $ext);
				//@codingStandardsIgnoreEnd

				return true;
			} elseif (is_dir($cache)) {
				$files = glob($cache . '*');

				if ($files === false) {
					return false;
				}

				foreach ($files as $file) {
					if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
						if ($clearOPcache && opcache_is_script_cached($file)) {
							opcache_invalidate($file, true);
						}

						//@codingStandardsIgnoreStart
						@unlink($file);
						//@codingStandardsIgnoreEnd
					}
				}
				return true;
			}
			$cache = array(
				CACHE . $type . DS . '*' . $params . $ext,
				CACHE . $type . DS . '*' . $params . '_*' . $ext
			);
			$files = array();
			while ($search = array_shift($cache)) {
				$results = glob($search);
				if ($results !== false) {
					$files = array_merge($files, $results);
				}
			}
			if (empty($files)) {
				return false;
			}
			foreach ($files as $file) {
				if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
					if ($clearOPcache && opcache_is_script_cached($file)) {
						opcache_invalidate($file, true);
					}

					//@codingStandardsIgnoreStart
					@unlink($file);
					//@codingStandardsIgnoreEnd
				}
			}
			return true;

		} elseif (is_array($params)) {
			foreach ($params as $file) {
				$this->_clearOPcache($file, $type, $ext);
			}
			return true;
		}

		return false;
	}

/**
 * Clear the View cache
 *
 * @param string|array $params As String name to be searched for deletion, if name is a directory all files in
 *  directory will be deleted. If array, names to be searched for deletion. If clearCache() without params,
 *  all files in app/tmp/cache/views will be deleted
 * @return bool Success
 */
	protected function _clearCache($params = null) {
		if (empty($params)) {
			return false;
		}
		$type = 'views';
		$ext = '.php';

		return $this->_clearOPcache($params, $type, $ext);
	}

/**
 * afterSave Callback
 *
 * Actions:
 *  - Clear the View cache.
 *
 * @param Model $model Model the callback is called on
 * @param bool $created Whether or not the save created a record.
 * @param array $options Options passed from Model::save().
 * @return bool true.
 */
	public function afterSave(Model $model, $created, $options = []) {
		$this->clearCache($model, $model->id, true);
		return true;
	}

/**
 * Stores the record about to be deleted.
 *
 * Actions:
 *  - Store parameters for clearCache.
 *
 * @param Model $model Model using this behavior.
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool
 */
	public function beforeDelete(Model $model, $cascade = true) {
		return $this->storeClearCacheParam($model, $model->id, true);
	}

/**
 * After delete method.
 *
 * Actions:
 *  - Clear the View cache.
 *
 * @param Model $model Model using this behavior
 * @return bool true to continue, false to abort the delete
 */
	public function afterDelete(Model $model) {
		$this->clearCache($model, null, false);
		return true;
	}
}
