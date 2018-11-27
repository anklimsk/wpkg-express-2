<?php
/**
 * This file is the model file of the plugin.
 * Process table filter information.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeThemeAppModel', 'CakeTheme.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * Filter for CakeTheme.
 *
 * @package plugin.Model
 */
class Filter extends CakeThemeAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'Filter';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = false;

/**
 * Return data for autocomplete
 *
 * @param string $query Query string for autocomplete
 * @param string $type Type of autocomplete
 *  (field name include model name, e.g., ModelName.FieldName).
 * @param string $plugin Name of plugin for target model of filter.
 * @param int|string $limit Limit for autocomplete data
 * @return array Data for autocomplete.
 */
	public function getAutocomplete($query = null, $type = null, $plugin = null, $limit = 0) {
		$result = [];
		if (empty($query) || !is_string($query)) {
			return $result;
		}

		$query = trim($query);
		if (empty($query) || !is_string($type)) {
			return $result;
		}

		$limit = (int)$limit;
		if ($limit <= 0) {
			$limit = CAKE_THEME_AUTOCOMPLETE_LIMIT;
		}

		$modelInfo = $this->_getModelInfoFromField($type, $plugin);
		if ($modelInfo === false) {
			return $result;
		}
		extract($modelInfo);
		$modelObj = ClassRegistry::init($modelName, true);
		if ($modelObj === false) {
			return false;
		}

		$conditions = $this->getCondition($type, $query, null, null, true);
		if ($conditions === false) {
			return $result;
		}

		$fields = ['DISTINCT ' . $fieldFullName];
		$modelObj->recursive = -1;
		$extractPath = '{n}.' . $fieldFullName;
		if ($modelObj->isVirtualField($fieldName)) {
			$modelObj->recursive = 0;
			$fieldFullName = 'virtual_field_' . Inflector::underscore($fieldName);
			$fields = ['DISTINCT ' . $modelObj->getVirtualField('name') . ' AS ' . $fieldFullName];
			$extractPath = '{n}.0.' . $fieldFullName;
		}
		$order = [$fieldFullName => 'asc'];
		$queryResult = $modelObj->find('all', compact('conditions', 'fields', 'order', 'limit'));
		if (empty($queryResult)) {
			return $result;
		}

		$result = Hash::extract($queryResult, $extractPath);

		return $result;
	}

/**
 * Return information of model from field name
 *
 * @param string $field Field name
 * @param string $plugin Name of plugin for target model of filter.
 * @return array|bool Array information of model in format:
 *  - key `modelName`, value - name of model;
 *  - key `fieldName`, value - name of field;
 *  - key `fieldFullName`, value - name of field include name of model.
 *  Return False on failure.
 */
	protected function _getModelInfoFromField($field = null, $plugin = null) {
		$result = false;
		if (empty($field) || !is_string($field)) {
			return $result;
		}

		if (strpos($field, '.') === false) {
			return $result;
		}

		list($modelName, $fieldName) = pluginSplit($field);
		if (!empty($plugin)) {
			$modelName = $plugin . '.' . $modelName;
		}
		$modelObj = ClassRegistry::init($modelName, true);
		if ($modelObj === false) {
			return false;
		}

		$fieldFullName = $modelObj->alias . '.' . $fieldName;
		$result = compact('modelName', 'fieldName', 'fieldFullName');

		return $result;
	}

/**
 * Return condition sign in SQL format from 2 char format.
 *
 * @param string $conditionSign Condition sign in 2 char format.
 * @return string Condition sign in SQL format.
 */
	protected function _parseConditionSign($conditionSign = null) {
		$result = '';
		if (empty($conditionSign)) {
			return $result;
		}

		$conditionSign = (string)mb_convert_case($conditionSign, MB_CASE_LOWER);
		switch ($conditionSign) {
			case 'gt':
				$result = '>';
				break;
			case 'ge':
				$result = '>=';
				break;
			case 'lt':
				$result = '<';
				break;
			case 'le':
				$result = '<=';
				break;
			case 'ne':
				$result = '<>';
				break;
			case 'eq':
			default:
				$result = '';
		}

		return $result;
	}

/**
 * Return string of logical group condition.
 *
 * @param string $condition Group condition, can be one of:
 *  `AND`, `OR`, `NOT`.
 * @return string Group condition from input string,
 *  or string `AND` on failure.
 */
	protected function _parseConditionGroup($condition = null) {
		$result = 'AND';
		if (empty($condition)) {
			return $result;
		}

		$condition = (string)mb_convert_case($condition, MB_CASE_UPPER);
		if (in_array($condition, ['AND', 'OR', 'NOT'])) {
			$result = $condition;
		}

		return $result;
	}

/**
 * Return condition for filter data and filter condition.
 *
 * @param array $filterData Data of filter for build conditions.
 * @param array $filterConditions Conditions of filter for build conditions.
 * @param string $plugin Name of plugin for target model of filter.
 * @param int $limit Limit of filter row for build conditions.
 * @return array Return array of condition.
 */
	public function buildConditions($filterData = null, $filterConditions = null, $plugin = null, $limit = CAKE_THEME_FILTER_ROW_LIMIT) {
		$result = [];
		if (empty($filterData) || !is_array($filterData)) {
			return $result;
		}

		if (!is_array($filterConditions)) {
			$filterConditions = [];
		}

		$conditionsGroup = null;
		if (isset($filterConditions['group'])) {
			$conditionsGroup = $filterConditions['group'];
		}
		$conditionSignGroup = $this->_parseConditionGroup($conditionsGroup);
		$conditionsCache = [];
		$filterRowCount = 0;
		$limit = (int)$limit;
		if ($limit <= 0) {
			$limit = CAKE_THEME_FILTER_ROW_LIMIT;
		}

		foreach ($filterData as $index => $modelInfo) {
			if (!is_int($index) || !is_array($modelInfo)) {
				continue;
			}

			$filterRowCount++;
			if ($filterRowCount > $limit) {
				break;
			}

			foreach ($modelInfo as $filterModel => $filterField) {
				if (!is_array($filterField)) {
					continue;
				}

				foreach ($filterField as $filterFieldName => $filterFieldValue) {
					if ($filterFieldValue === '') {
						continue;
					}

					$condSign = null;
					if (isset($filterConditions[$index][$filterModel][$filterFieldName])) {
						$condSign = $filterConditions[$index][$filterModel][$filterFieldName];
					}
					$condition = $this->getCondition($filterModel . '.' . $filterFieldName, $filterFieldValue, $condSign, $plugin);
					if ($condition === false) {
						continue;
					}

					$cacheKey = md5(serialize($condition));
					if (in_array($cacheKey, $conditionsCache)) {
						continue;
					}

					$result[$conditionSignGroup][] = $condition;
					$conditionsCache[] = $cacheKey;
				}
			}
		}
		if (isset($result[$conditionSignGroup]) && (count($result[$conditionSignGroup]) == 1)) {
			$result = array_shift($result[$conditionSignGroup]);
		}

		return $result;
	}

/**
 * Return condition for field, condition, and data string
 *
 * @param string $field Field name
 * @param array|string $data Data value
 * @param string $conditionSign Condition sign in 2 char format
 * @param string $plugin Name of plugin for target model of filter.
 * @param bool $isAutocomplete Flag of autocomplete condition
 * @return array Return array of condition, or False on failure.
 */
	public function getCondition($field = null, $data = null, $conditionSign = null, $plugin = null, $isAutocomplete = false) {
		if (empty($field) || (empty($data) && ($data !== '0'))) {
			return false;
		}

		$modelInfo = $this->_getModelInfoFromField($field, $plugin);
		if ($modelInfo === false) {
			return false;
		}

		extract($modelInfo);
		$modelObj = ClassRegistry::init($modelName, true);
		if ($modelObj === false) {
			return false;
		}

		$conditions = [];
		$fieldType = $modelObj->getColumnType($fieldName);
		if ($fieldType === null) {
			if (!$modelObj->isVirtualField($fieldName)) {
				return false;
			}

			$fieldType = 'virtual';
		}

		$prefix = '%';
		if ($isAutocomplete) {
			$prefix = '';
		}

		$conditionSign = $this->_parseConditionSign($conditionSign);
		if (!empty($conditionSign)) {
			$conditionSign = ' ' . $conditionSign;
		}

		switch ($fieldType) {
			case 'binary':
				return false;
			case 'boolean':
				if (is_array($data)) {
					if (count($data) > 1) {
						return false;
					}

					$data = array_shift($data);
				}

				$conditions[$fieldFullName] = $data;
				break;
			case 'integer':
			case 'biginteger':
			case 'float':
				$conditions[$fieldFullName . $conditionSign] = $data;
				break;
			case 'date':
			case 'time':
			case 'datetime':
			case 'timestamp':
				if (is_array($data)) {
					if (count($data) > 1) {
						return false;
					}

					$data = array_shift($data);
				}
				$date = strtotime($data);
				if ($date === false) {
					$data = $prefix . $data . '%';
					$conditionSign = ' like';
				} else {
					if ($fieldType === 'date') {
						$dateFormat = 'Y-m-d';
					} elseif ($fieldType === 'time') {
						$dateFormat = 'H:i:s';
					} elseif ($fieldType === 'datetime') {
						$dateFormat = 'Y-m-d H:i:s';
					}
					$data = date($dateFormat, $date);
				}
				$conditions[$fieldFullName . $conditionSign] = $data;
				break;
			case 'string':
			case 'text':
			case 'virtual':
				if (is_array($data)) {
					if (count($data) > 1) {
						return false;
					}

					$data = array_shift($data);
				}

				if ($fieldType === 'virtual') {
					$conditions[$fieldFullName . ' like'] = $prefix . $data . '%';
				} else {
					$conditions['LOWER(' . $fieldFullName . ') like'] = mb_strtolower($prefix . $data . '%');
				}
				break;
			default:
				return false;
		}

		return $conditions;
	}
}
