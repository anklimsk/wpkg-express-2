<?php
/**
 * This file is the behavior file of the application. Is used to
 *  restore data from garbage.
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
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('RenderXmlData', 'Utility');

/**
 * The behavior is used to restore data from garbage.
 *
 * @package app.Model.Behavior
 */
class RestoreBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if PHP extension "bz2" is not loaded
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!extension_loaded('bz2')) {
			throw new InternalErrorException(__('PHP Extension "%s" is not loaded', 'bz2'));
		}
	}

/**
 * After find callback. Can be used to modify any results returned by find.
 *
 * Actions:
 *  - Decompress data.
 *
 * @param Model $model Model using this behavior
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 */
	public function afterFind(Model $model, $results, $primary = false) {
		if (!$primary || empty($results)) {
			return $results;
		}

		foreach ($results as &$resultItem) {
			if (isset($resultItem[$model->alias]['data']) && !empty($resultItem[$model->alias]['data'])) {
				$data = bzdecompress($resultItem[$model->alias]['data']);
				if (is_int($data)) {
					$data = false;
				}
				$resultItem[$model->alias]['data'] = $data;
			}
		}

		return $results;
	}

/**
 * Return object Model by ID type of object.
 *
 * @param int|string $refType ID type of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	protected function _getTargetModel($refType = null) {
		if (empty($refType)) {
			return false;
		}

		$refTypeName = constValToLcSingle('GARBAGE_TYPE_', $refType, false, false, false);
		if (empty($refTypeName)) {
			return false;
		}

		$objModel = ClassRegistry::init(ucfirst($refTypeName), true);
		return $objModel;
	}

/**
 * Store data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refType ID type of object
 * @param int|string $refId Record ID of object
 * @return bool Success
 */
	public function storeData(Model $model, $refType = null, $refId = null) {
		if (empty($refType) || empty($refId)) {
			return false;
		}

		$modelTarget = $this->_getTargetModel($refType);
		if (!$modelTarget) {
			return false;
		}

		$xmlDataArray = $modelTarget->getXMLdata($refId, false, true, true);
		if (empty($xmlDataArray)) {
			return false;
		}

		reset($xmlDataArray);
		$root = key($xmlDataArray);
		if (isset($xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED])) {
			$xmlInfo = $xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED];
			unset($xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED]);
			$xmlDataArray[$root] = array_merge($xmlDataArray[$root], $xmlInfo);
		}

		$xmlString = RenderXmlData::renderXml($xmlDataArray, true);
		$data = bzcompress($xmlString, 9);
		$name = $modelTarget->getNameFromXml($xmlDataArray);
		$revision = '0';
		if ($modelTarget->name === 'Package') {
			$revision = $modelTarget->getRevisionFromXml($xmlDataArray);
		}
		$dataToSave = [
			'ref_type' => $refType,
			'ref_id' => $refId,
			'data' => $data,
			'name' => $name,
		];

		switch ($model->name) {
			case 'Archive':
				$dataToSave['revision'] = $revision;
			break;
		}
		$dataToSave = [$model->alias => $dataToSave];

		return (bool)$model->save($dataToSave);
	}

/**
 * Restore data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id Record ID for restore
 * @param bool $removeAfterRestore Flag of removing data
 *  after restore
 * @return bool Success
 */
	public function restoreData(Model $model, $id = null, $removeAfterRestore = false) {
		if (empty($id)) {
			return false;
		}

		$data = $model->read(null, $id);
		if (empty($data)) {
			return false;
		}

		if (empty($data[$model->alias]['data'])) {
			return false;
		}

		$modelImport = ClassRegistry::init('Import');
		$result = $modelImport->importXml($data[$model->alias]['data']);
		if ($result && $removeAfterRestore) {
			$model->delete($id);
		}

		return $result;
	}

/**
 * Clear garbage.
 *
 * @param Model $model Model using this behavior.
 * @param int|string $refType ID type of object
 * @param int|string $refId Record ID of object
 * @return bool Success
 */
	public function clearData(Model $model, $refType = null, $refId = null) {
		if (empty($refType)) {
			if (!empty($refId)) {
				return false;
			}

			$dataSource = $model->getDataSource();
			return $dataSource->truncate($model);
		}

		$conditions = [
			$model->alias . '.ref_type' => $refType,
		];
		if (!empty($refId)) {
			$conditions[$model->alias . '.ref_id'] = $refId;
		}

		return $model->deleteAll($conditions, false, false);
	}

/**
 * Return string with XML data.
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID for retrieve data
 *  after restore
 * @return string|bool Return string with XML data,
 *  or False on failure.
 */
	public function getXMLdata(Model $model, $id = null) {
		if (empty($id)) {
			return false;
		}

		$model->id = $id;
		$data = $model->field('data');
		if (empty($data)) {
			return false;
		}

		return $data;
	}

/**
 * Return download name by record ID
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID for retrieve download name
 * @return string Return download name
 */
	public function getDownloadName(Model $model, $id = []) {
		$ext = '.xml';
		$name = '';

		if (method_exists($model, 'getFullName')) {
			$name = $model->getFullName($id);
		} else {
			$model->id = $id;
			$name = $model->field($model->displayField);
		}
		if (empty($name)) {
			$name = __('unknown');
		}

		$result = Inflector::slug($name);
		$result .= $ext;

		return $result;
	}
}
