<?php
/**
 * This file is the model file of the plugin.
 * Search information in project database.
 * Methods for search information in project database.
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeSearchInfoAppModel', 'CakeSearchInfo.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeText', 'Utility');
App::uses('Inflector', 'Utility');
App::import(
	'Vendor',
	'CakeSearchInfo.LangCorrect',
	['file' => 'LangCorrect' . DS . 'autoload.php']
);

/**
 * Search for CakeSearchInfo.
 *
 * @package plugin.Model
 */
class Search extends CakeSearchInfoAppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = false;

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = ['CakeTheme.BreadCrumb'];

/**
 * Cache of schema for target models
 *
 * @var array
 */
	protected $_schemaCache = [];

/**
 * Object of model `ConfigSearchInfo`
 *
 * @var object
 */
	protected $_modelConfigSearchInfo = null;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_modelConfigSearchInfo = ClassRegistry::init('CakeSearchInfo.ConfigSearchInfo');

		parent::__construct($id, $table, $ds);
	}

/**
 * Search information in project database
 *
 * @param array $findType Type of `find` method. One of:
 *  - 'all' - find all records;
 *  - 'count' - calculate amount of records;
 * @param array $conditionsQuery Conditions for find in format:
 *  - key `query`: value - query string;
 *  - key `target`: value -  array of target for find;
 * @param int $limit Limit for find.
 * @param int $page Page number for pagination.
 * @param array|string $orderDef Sorting order.
 * @return bool|int|array Return False on failure.
 *  If $findType equal `count` return integer amount of records.
 *  If $findType equal `all` return array of result find in format:
 *  - key - target model; value - result of search;
 */
	protected function _searchInfo($findType = 'all', $conditionsQuery = null, $limit = null, $page = 1, $orderDef = null) {
		if (!empty($findType)) {
			$findType = mb_strtolower($findType);
		} else {
			$findType = 'all';
		}

		$result = false;
		if ($findType === 'count') {
			$result = 0;
		} elseif (($limit <= 0) || ($page < 1)) {
			return $result;
		}

		if (empty($orderDef)) {
			$orderDef = [];
		} elseif (!is_array($orderDef)) {
			$orderDef = [$orderDef];
		}

		if (empty($conditionsQuery) || !is_array($conditionsQuery) ||
			!isset($conditionsQuery['query']) || empty($conditionsQuery['query'])) {
			return $result;
		}

		$query = trim($conditionsQuery['query']);
		if (empty($query)) {
			return $result;
		}

		$target = null;
		if (isset($conditionsQuery['target'])) {
			$target = $conditionsQuery['target'];
		}

		$queryConfigFull = $this->getQueryConfig();
		$queryConfig = $this->getQueryConfig($target);
		$resultCountType = 0;
		$resultCountCurr = 0;
		$resultCountTotal = 0;
		$targetOffsetStart = ($page - 1) * $limit;
		$dataNeedProcess = true;
		$resultSearch = [];
		$conditionsDefault = array_diff_key($conditionsQuery, ['query' => null, 'target' => null]);
		foreach ($queryConfig['modelConfig'] as $modelName => $config) {
			$modelObj = ClassRegistry::init($modelName, true);
			if ($modelObj === false) {
				continue;
			}

			if (!isset($config['fields']) || empty($config['fields'])) {
				continue;
			}

			$fields = $this->_getFieldsFromConfig($config['fields']);
			if (empty($fields)) {
				continue;
			}

			$conditions = $conditionsDefault;
			if (isset($config['conditions']) && !empty($config['conditions'])) {
				$conditions = Hash::merge($conditions, (array)$config['conditions']);
			}

			$queryCondition = ($queryConfig['anyPart'] ? '%' : '') . $query . '%';
			foreach ($fields as $fieldName) {
				if (!$this->_checkFieldType($fieldName)) {
					continue;
				}

				if ($modelObj->isVirtualField($fieldName)) {
					$conditions['OR'][$fieldName . ' like'] = $queryCondition;
				} else {
					$conditions['OR']['LOWER(' . $fieldName . ') like'] = mb_strtolower($queryCondition);
				}
			}
			if (empty($conditions)) {
				continue;
			}

			if (count($conditions['OR']) == 1) {
				$conditions = array_diff_key($conditions, ['OR' => null]) + $conditions['OR'];
			}

			$findOpt = compact('conditions');
			if ($modelObj->Behaviors->loaded('Containable') && isset($config['contain']) &&
				!empty($config['contain'])) {
				$findOpt['contain'] = $config['contain'];
			} else {
				$findOpt['recursive'] = -1;
				if (isset($config['recursive'])) {
					$findOpt['recursive'] = (int)$config['recursive'];
				}
			}
			$findCount = $modelObj->find('count', $findOpt);
			if ($findCount == 0) {
				continue;
			}

			$resultCountTotal += $findCount;
			if ($findType === 'count') {
				continue;
			}

			$resultSearch[$modelName]['amount'] = $findCount;
			$resultSearch[$modelName]['data'] = [];
			if (!$dataNeedProcess) {
				continue;
			}

			$includeFields = $this->_modelConfigSearchInfo->getIncludeFields();
			if (isset($includeFields[$modelName]) && !empty($includeFields[$modelName])) {
				$fields = array_merge($fields, (array)$includeFields[$modelName]);
			}
			if (isset($queryConfigFull['modelConfig'][$modelName]['fields']) && !empty($queryConfigFull['modelConfig'][$modelName]['fields'])) {
				$fields = array_merge($fields, array_keys((array)$queryConfigFull['modelConfig'][$modelName]['fields']));
			}
			if (isset($includeFields[$modelName]) || isset($queryConfigFull['modelConfig'][$modelName]['fields'])) {
				$fields = array_values(array_unique($fields));
			}

			foreach ($fields as $i => $fieldName) {
				if (substr_count($fieldName, '.') !== 1) {
					unset($fields[$i]);
				}
			}
			if (!in_array($modelObj->alias . '.' . $modelObj->primaryKey, $fields)) {
				array_unshift($fields, $modelObj->alias . '.' . $modelObj->primaryKey);
			}

			$order = $orderDef;
			if (isset($config['order'])) {
				$order += (array)$config['order'];
			}

			$findOpt += compact('fields', 'order');
			if ($targetOffsetStart > 0) {
				if ($targetOffsetStart >= ($resultCountType + $findCount)) {
					$resultCountType += $findCount;
					continue;
				} elseif ($targetOffsetStart > $resultCountType) {
					$findOpt['offset'] = $targetOffsetStart - $resultCountType;
				}
			}
			$resultCountType += $findCount;
			$findOpt['limit'] = $limit;
			if (($resultCountCurr > 0) && ($resultCountCurr < $limit)) {
				$findOpt['limit'] = $limit - $resultCountCurr;
			}

			$findResult = $modelObj->find('all', $findOpt);
			if (empty($findResult)) {
				continue;
			}

			$resultCountCurr += count($findResult);
			$resultSearch[$modelName]['data'] = $findResult;
			if ($resultCountCurr >= $limit) {
				$dataNeedProcess = false;
			}
		}
		if ($findType === 'count') {
			$result = $resultCountTotal;
		} else {
			if (!empty($resultSearch)) {
				$resultSearch['count'] = $resultCountCurr;
				$resultSearch['total'] = $resultCountTotal;
			}
			$result = $resultSearch;
		}

		return $result;
	}

/**
 * Get state of flag any part search from list of target fields
 *
 * @param string|array $target Target fields or model.
 * @return bool State of flag any part search
 */
	public function getAnyPartFlag($target = null) {
		$anyPart = false;
		if (empty($target)) {
			$anyPart = $this->_modelConfigSearchInfo->getFlagDefaultSearchAnyPart();
		} elseif (is_array($target) && in_array(CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART, $target)) {
			$anyPart = true;
		}

		return $anyPart;
	}

/**
 * Get query string from string data
 *
 * @param string $queryData Query data.
 * @return string Query string
 */
	public function getQueryStr($queryData = null) {
		$query = (string)$queryData;
		if (!empty($query)) {
			$query = trim($query);
		}

		return $query;
	}

/**
 * Get configuration for query
 *
 * @param string|array $target Target fields or model.
 * @return array Return array of configuration for query in format:
 *  - deep 0:
 *   [
 *      'anyPart' => false,
 *      'modelConfig' => [
 *          'ModelName' => [
 *              'fields' => [
 *                  'ModelName.FieldName1' => __('Field name 1'),
 *                  'ModelName.FieldName2' => __('Field name 2'),
 *                  'ModelName.FieldName3' => __('Field name 3'),
 *                  'ModelName.FieldName4' => __('Field name 4'),
 *                  'ModelName.FieldName5' => __('Field name 5')
 *              ],
 *              'order' => [
 *                  'ModelName.FieldName' => 'direction'
 *              ],
 *              'name' => __('Scope name')
 *          ]
 *      ]
 *  ]
 *  - deep 1:
 *   [
 *      'anyPart' => false,
 *      'modelConfig' => [
 *          'ModelName' => [
 *              'fields' => [
 *                  'ModelName.FieldName' => __('Field name')
 *              ],
 *              'order' => [
 *                  'ModelName.FieldName' => 'direction'
 *              ],
 *           'conditions' => [
 *                  'ModelName.FieldName' => 'SomeValue'
 *              ],
 *              'name' => __('Scope name'),
 *          ]
 *      ]
 *  ]
 */
	public function getQueryConfig($target = null) {
		$targetModels = $this->_modelConfigSearchInfo->getTargetModels();
		$cachePath = null;
		$result = [
			'anyPart' => $this->getAnyPartFlag($target),
			'modelConfig' => []
		];

		if (empty($target)) {
			$result['modelConfig'] = $targetModels;

			return $result;
		}

		if (!is_array($target)) {
			$target = [$target];
		}

		$language = (string)Configure::read('Config.language');
		$dataStr = serialize($targetModels + $target) . '_' . $language;
		$cachePath = 'QueryConfig.' . md5($dataStr);
		$cached = Cache::read($cachePath, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG);
		if (!empty($cached)) {
			return $cached;
		}

		$targetDeep = $this->_modelConfigSearchInfo->getTargetDeep();
		if ($targetDeep < 1) {
			$result['modelConfig'] = array_intersect_key($targetModels, array_flip($target));
		} else {
			$modelConfig = [];
			foreach ($target as $targetModelField) {
				if (strpos($targetModelField, '.') === false) {
					continue;
				}

				list($targetModel, $targetField) = pluginSplit($targetModelField);
				if (!isset($targetModels[$targetModel]['fields'][$targetField])) {
					continue;
				}

				$targetData = $targetModels[$targetModel];
				$targetData['fields'] = [$targetField => $targetModels[$targetModel]['fields'][$targetField]];
				$modelConfig = Hash::mergeDiff($modelConfig, [$targetModel => $targetData]);
			}
			foreach ($modelConfig as $modelName => &$modelCfg) {
				if (isset($modelCfg['order'])) {
					if (is_array($modelCfg['order']) && (count($modelCfg['order']) === 1) &&
						(count(array_intersect_key($modelCfg['order'], $modelCfg['fields'])) === 1)) {
						continue;
					}
				}
				$fields = array_keys($modelCfg['fields']);
				$field = array_shift($fields);
				if (empty($field)) {
					if (isset($modelCfg['order'])) {
						unset($modelCfg['order']);
					}
				}
			}
			$result['modelConfig'] = $modelConfig;
		}
		Cache::write($cachePath, $result, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG);

		return $result;
	}

/**
 * Set schema information of model in cache
 *
 * @param string $modelName Model name
 * @param array|null $schema Array of database table metadata.
 * @return bool True on success, False otherwise.
 */
	protected function _setSchemaCache($modelName = null, $schema = null) {
		if (empty($modelName)) {
			return false;
		}

		$modelName = (string)$modelName;
		$this->_schemaCache[$modelName] = $schema;

		return true;
	}

/**
 * Get schema information of model from cache
 *
 * @param string $modelName Model name
 * @return array|null Array of database table metadata.
 *  Null on failure.
 */
	protected function _getSchemaCache($modelName = null) {
		if (empty($modelName)) {
			return null;
		}

		$modelName = (string)$modelName;
		if (!isset($this->_schemaCache[$modelName])) {
			return null;
		}

		return $this->_schemaCache[$modelName];
	}

/**
 * Get schema information of model
 *
 * @param string $modelName Model name
 * @return array|null Array of database table metadata.
 *  Null on failure.
 */
	protected function _getSchemaForModel($modelName = null) {
		if (empty($modelName)) {
			return null;
		}

		$modelName = (string)$modelName;
		$cached = $this->_getSchemaCache($modelName);
		if ($cached !== null) {
			return $cached;
		}

		$modelObj = ClassRegistry::init($modelName, true);
		if ($modelObj === false) {
			return null;
		}

		$defaultSchema = [
			'type' => 'string',
			'null' => false,
			'default' => null,
			'length' => null,
		];
		$result = $modelObj->schema();
		$virtualFields = $modelObj->getVirtualField();
		if (!empty($result) && !empty($virtualFields)) {
			foreach ($virtualFields as $virtualFieldName => $virtualFieldValue) {
				$result[$virtualFieldName] = $defaultSchema;
			}
		}
		$this->_setSchemaCache($modelName, $result);

		return $result;
	}

/**
 * Check the model field to support search
 *
 * @param string $modelFieldName Model field name for check
 * @return bool Return True, if model field is supports search.
 *  False otherwise.
 */
	protected function _checkFieldType($modelFieldName = null) {
		if (empty($modelFieldName) || !is_string($modelFieldName)) {
			return false;
		}

		if (strpos($modelFieldName, '.') === false) {
			return false;
		}

		list($modelName, $fieldName) = pluginSplit($modelFieldName);
		$schema = $this->_getSchemaForModel($modelName);
		if (($schema === null) || !is_array($schema) || !isset($schema[$fieldName]['type']) ||
			!in_array($schema[$fieldName]['type'], ['text', 'string'])) {
			return false;
		}

		return true;
	}

/**
 * Get target fields information
 *
 * @param bool $isList If True - return list of target fields.
 *  Otherwise - return information about target fields.
 * @return array Information about target fields in format:
 *  - deep 0:
 *    - $isList - False:
 *  [
 *      __('Scope name 1') => 'ModelName1',
 *      __('Scope name 2') => 'ModelName2',
 *      __('Scope name 3') => 'ModelName3'
 *  ]
 *    - $isList - True:
 *  [
 *      'ModelName1',
 *      'ModelName2',
 *      'ModelName3'
 *  ]
 *  - deep 1:
 *    - $isList - False:
 *  [
 *      __('Scope name 1') => [
 *          'ModelName1.ModelName1.FieldName1' => __('Field name 1'),
 *          'ModelName1.ModelName1.FieldName2' => __('Field name 2'),
 *          'ModelName1.ModelName1.FieldName3' => __('Field name 3')
 *      ],
 *      __('Scope name 2') => [
 *          'ModelName2.ModelName2.FieldName1' => __('Field name 1')
 *      ],
 *      __('Scope name 3') => [
 *          'ModelName3.ModelName2.FieldName1' => __('Field name 1'),
 *          'ModelName3.ModelName3.FieldName1' => __('Field name 2')
 *      ]
 *  ]
 *    - $isList - True:
 *  [
 *      'ModelName1.ModelName1.FieldName1',
 *      'ModelName1.ModelName1.FieldName2',
 *      'ModelName1.ModelName1.FieldName3',
 *      'ModelName2.ModelName2.FieldName1',
 *      'ModelName3.ModelName2.FieldName1',
 *      'ModelName3.ModelName3.FieldName1',
 *  ]
 */
	protected function _getTargetFields($isList = false) {
		$result = [];
		$targetModels = $this->_modelConfigSearchInfo->getTargetModels();
		$targetDeep = $this->_modelConfigSearchInfo->getTargetDeep();
		$cachePath = null;
		if (empty($targetModels)) {
			return $result;
		}

		$language = (string)Configure::read('Config.language');
		$dataStr = serialize($targetModels + compact('targetDeep', 'isList', 'language'));
		$cachePath = 'TargetFields.' . md5($dataStr);
		$cached = Cache::read($cachePath, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG);
		if (!empty($cached)) {
			return $cached;
		}

		foreach ($targetModels as $modelName => $modelConfig) {
			$modelObj = ClassRegistry::init($modelName, true);
			if ($modelObj === false) {
				continue;
			}

			if (empty($modelConfig) || !is_array($modelConfig)) {
				continue;
			}

			$modelLabel = (isset($modelConfig['name']) ? $modelConfig['name'] : $modelName);
			if ($targetDeep < 1) {
				if ($isList) {
					$result[] = $modelName;
				} else {
					$result[$modelLabel] = $modelName;
				}
			} else {
				if (!isset($modelConfig['fields']) || empty($modelConfig['fields'])) {
					continue;
				}

				foreach ($modelConfig['fields'] as $modelFieldName => $fieldLabel) {
					if (!$this->_checkFieldType($modelFieldName)) {
						continue;
					}

					if ($isList) {
						$result[] = $modelName . '.' . $modelFieldName;
					} else {
						$result[$modelLabel][$modelName . '.' . $modelFieldName] = $fieldLabel;
					}
				}
			}
		}
		if ($isList) {
			if (($targetDeep > 0) && !empty($result)) {
				$result = array_values(array_unique($result));
			}
		}
		Cache::write($cachePath, $result, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_CFG);

		return $result;
	}

/**
 * Get list of target fields
 *
 * @return array List of target fields
 * @see Search::_getTargetFields()
 */
	public function getTargetFieldsList() {
		return $this->_getTargetFields(true);
	}

/**
 * Get information about target fields
 *
 * @return array Information about target fields
 * @see Search::_getTargetFields()
 */
	public function getTargetFields() {
		return $this->_getTargetFields(false);
	}

/**
 * Return data for pagination
 *
 * @param array $conditions Conditions for pagination.
 * @param array $fields Fields list.
 * @param array|string $order Sorting order.
 * @param int $limit Limit for pagination.
 * @param int $page Page number for pagination.
 * @param int $recursive Number of associations to recurse through during find calls.
 *  Fetches only the first level by default.
 * @param array $extra Extra parametrs for pagination.
 * @return mixed On success array data or null|false on failure.
 * @see Search::_searchInfo()
 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = []) {
		if (empty($order)) {
			$order = [];
		} elseif (!is_array($order)) {
			$order = [$order];
		}

		$sort = null;
		$direction = null;
		if (!empty($extra)) {
			$sort = Hash::get($extra, 'sort');
			$direction = Hash::get($extra, 'direction');
		}
		if (empty($direction)) {
			$direction = 'asc';
		}
		if (!empty($sort)) {
			$order[$sort] = $direction;
		}

		return $this->_searchInfo('all', $conditions, $limit, $page, $order);
	}

/**
 * Return count for pagination
 *
 * @param array $conditions Conditions for pagination count.
 * @param int $recursive Number of associations to recurse through during find calls.
 *  Fetches only the first level by default.
 * @param array $extra Extra parametrs for pagination count.
 * @return int Count of pagination data.
 * @see Search::_searchInfo()
 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = []) {
		return $this->_searchInfo('count', $conditions);
	}

/**
 * Get list of fields from query config fields
 *
 * @param array $fields Query config fields.
 * @return array List of fields.
 * @see Search::getQueryConfig()
 */
	protected function _getFieldsFromConfig($fields = null) {
		$result = [];
		if (empty($fields)) {
			return $result;
		}

		if (!is_array($fields)) {
			$result = [$fields];
		} else {
			$result = array_keys($fields);
		}

		return $result;
	}

/**
 * Get data for autocomplete for target model
 *
 * @param string $modelName Name of target model.
 * @param array $config Config of target model.
 * @param string $query Query data.
 * @param bool $anyPart Flag of any part search.
 * @param int $limit Limit for find.
 * @return array|bool Return array of data for autocomple or
 *  False on failure.
 */
	protected function _getAutocompleteData($modelName = null, $config = null, $query = null, $anyPart = false, $limit = null) {
		if (empty($modelName) || empty($query)) {
			return false;
		}

		if (!isset($config['fields']) || empty($config['fields'])) {
			return false;
		}

		$modelObj = ClassRegistry::init($modelName, true);
		if ($modelObj === false) {
			return false;
		}

		$fields = $this->_getFieldsFromConfig($config['fields']);
		if (empty($fields)) {
			return false;
		}

		$findOpt = compact('limit');
		if ($modelObj->Behaviors->loaded('Containable') && isset($config['contain']) &&
			!empty($config['contain'])) {
			$findOpt['contain'] = $config['contain'];
		} else {
			$findOpt['recursive'] = -1;
			if (isset($config['recursive'])) {
				$findOpt['recursive'] = (int)$config['recursive'];
			}
		}

		$result = [];
		$conditions = [];
		$order = null;
		$extractPaths = [];
		$queryCondition = ($anyPart ? '%' : '') . $query . '%';
		$queryExtract = ($anyPart ? '.*' : '') . preg_quote($query, '/') . '.*';
		$useDistinct = true;
		if (isset($config['conditions']) && !empty($config['conditions'])) {
			$conditions = (array)$config['conditions'];
		}

		foreach ($fields as &$field) {
			$isVirtualField = false;
			if ($modelObj->isVirtualField($field)) {
				$isVirtualField = true;
			}
			if ($isVirtualField) {
				$conditions['OR'][$field . ' like'] = $queryCondition;
			} else {
				$conditions['OR']['LOWER(' . $field . ') like'] = mb_strtolower($queryCondition);
				$queryExtract = mb_strtolower($queryExtract);
			}
			list($modelName, $fieldName) = pluginSplit($field);
			$extractPaths[] = '{n}.' . $modelName . '[' . $fieldName . '=/' . $queryExtract . '/ui].' . $fieldName;
			if (count($conditions['OR']) == 1) {
				$order = [$field => 'asc'];
			}

			if ($useDistinct && !$isVirtualField) {
				$field = 'DISTINCT ' . $field;
				$useDistinct = false;
			}
		}
		if (count($conditions['OR']) == 1) {
			$conditions = array_diff_key($conditions, ['OR' => null]) + $conditions['OR'];
		}

		$findOpt += compact('conditions', 'fields', 'order');
		$queryResult = $modelObj->find('all', $findOpt);
		if (empty($queryResult)) {
			return $result;
		}

		foreach ($extractPaths as $extractPath) {
			$queryResultItem = Hash::extract($queryResult, $extractPath);
			if (!empty($queryResultItem)) {
				$result = array_merge($result, $queryResultItem);
			}
		}
		sort($result);

		return $result;
	}

/**
 * Return data for autocomplete
 *
 * @param string $query Query string for autocomplete
 * @param string|array $target Target fields or model
 * @param int|string $limit Limit for autocomplete data
 * @return array Data for autocomplete.
 */
	public function getAutocomplete($query = null, $target = null, $limit = 0) {
		$result = [];
		if (empty($query)) {
			return $result;
		}

		$query = trim($query);
		if (empty($query)) {
			return $result;
		}

		$limit = (int)$limit;
		if ($limit <= 0) {
			$limit = CAKE_SEARCH_INFO_AUTOCOMPLETE_LIMIT;
		}

		$language = (string)Configure::read('Config.language');
		$dataStr = serialize(compact('query', 'target', 'limit')) . '_' . $language;
		$cachePath = 'QueryResultAutocom.' . md5($dataStr);
		$cached = Cache::read($cachePath, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT);
		if (!empty($cached)) {
			return $cached;
		}

		$querySearchMinLength = $this->_modelConfigSearchInfo->getQuerySearchMinLength();
		if ($querySearchMinLength < 1) {
			$querySearchMinLength = CAKE_SEARCH_INFO_QUERY_SEARCH_MIN_LENGTH;
		}

		if (mb_strlen($query, 'UTF-8') < $querySearchMinLength) {
			return $result;
		}

		$textConv = new Text_LangCorrect();
		$queryConfig = $this->getQueryConfig($target);
		$lang = (string)Configure::read('Config.language');
		foreach ($queryConfig['modelConfig'] as $modelName => $config) {
			$resultItem = $this->_getAutocompleteData($modelName, $config, $query, $queryConfig['anyPart'], $limit);
			if (empty($resultItem)) {
				if (mb_strtolower($lang) !== 'rus') {
					continue;
				}
				$queryCorrect = $textConv->parse($query, Text_LangCorrect::SIMILAR_CHARS | Text_LangCorrect::KEYBOARD_LAYOUT);
				if ($query === $queryCorrect) {
					continue;
				}

				$resultItem = $this->_getAutocompleteData($modelName, $config, $queryCorrect, $queryConfig['anyPart'], $limit);
				if (empty($resultItem)) {
					continue;
				}
			}

			$result = array_merge($result, $resultItem);
		}

		$truncateOpt = [
			'ellipsis' => '',
			'exact' => false,
			'html' => false
		];
		if (!empty($result)) {
			$result = array_values(array_unique($result));
			foreach ($result as &$resultItem) {
				$resultItem = CakeText::truncate(h($resultItem), CAKE_SEARCH_INFO_AUTOCOMPLETE_RESULT_TRUNCATE_LIMIT, $truncateOpt);
			}
		}
		Cache::write($cachePath, $result, CAKE_SEARCH_INFO_CACHE_KEY_QUERY_RESULT);

		return $result;
	}

/**
 * Return plugin name.
 *
 * @return string Return plugin name for breadcrumb.
 */
	public function getPluginName() {
		$pluginName = 'cake_search_info';

		return $pluginName;
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'search';

		return $controllerName;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$groupName = __d('cake_search_info', 'Search information');

		return $groupName;
	}
}
