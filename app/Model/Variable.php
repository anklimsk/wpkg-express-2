<?php
/**
 * This file is the model file of the application. Used to
 *  manage variables.
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
 * @package app.Model
 */

App::uses('AppModel', 'Model');

/**
 * The model is used to manage variables.
 *
 * @package app.Model
 */
class Variable extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'name';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Tree',
		'ScopeTree',
		'BreadCrumbExt' => [
			'refTypeField' => 'ref_type',
			'refIdField' => 'ref_id'
		],
		'UpdateModifiedDate',
		'MoveExt',
		'TrimStringField',
		'ClearViewCache',
		'ValidationRules'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'ref_type' => [
			'rule' => ['checkRange', 'VARIABLE_TYPE_', false],
			'message' => 'Invalid variable type.'
		],
		'ref_id' => [
			'rule' => 'notBlank',
			'message' => 'Invalid variable reference ID.'
		],
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'The variable name attribute is invalid.'
		],
		'value' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'The variable value attribute is invalid.'
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'Attribute' => [
			'className' => 'Attribute',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => [
				'ref_type' => [
						ATTRIBUTE_TYPE_HOST,
						ATTRIBUTE_TYPE_PROFILE,
						ATTRIBUTE_TYPE_PACKAGE,
						ATTRIBUTE_TYPE_CONFIG
				],
				'ref_node' => ATTRIBUTE_NODE_VARIABLE
			],
			'fields' => [
				'Attribute.pcre_parsing',
				'Attribute.hostname',
				'Attribute.os',
				'Attribute.architecture',
				'Attribute.ipaddresses',
				'Attribute.domainname',
				'Attribute.groups',
				'Attribute.lcid',
				'Attribute.lcidOS'
			]
		],
		'Check' => [
			'className' => 'Check',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => CHECK_PARENT_TYPE_VARIABLE],
			'fields' => [
				'Check.ref_type',
				'Check.type',
				'Check.condition',
				'Check.path',
				'Check.value',
				'Check.id',
				'Check.parent_id',
				'Check.lft',
				'Check.rght'
			],
			'order' => ['Check.lft' => 'asc']
		],
	];

/**
 * Returns a list of all events that will fire in the model during it's lifecycle.
 * Add listener callbacks for events `Model.beforeUpdateTree` and `Model.afterUpdateTree`.
 *
 * @return array
 */
	public function implementedEvents() {
		$events = parent::implementedEvents();
		$events['Model.beforeUpdateTree'] = ['callable' => 'beforeUpdateTree', 'passParams' => true];
		$events['Model.afterUpdateTree'] = ['callable' => 'afterUpdateTree'];

		return $events;
	}

/**
 * Called before each update tree. Return a non-true result
 * to halt the update tree.
 *
 * @param array $options Options:
 *  - `id`: ID of moved record,
 *  - `newParentId`: ID of new parent for moved record,
 *  - `method`: method of move - moveUp or moveDown,
 *  - `delta`: delta for moving.
 * @return bool True if the operation should continue, false if it should abort
 */
	public function beforeUpdateTree($options = []) {
		return $this->storeClearCacheParam($options['id'], true);
	}

/**
 * Called after each successful update tree operation.
 *
 * Actions:
 *  - Clear View cache after drag and drop.
 *
 * @return void 
 */
	public function afterUpdateTree() {
		$this->clearCache(null, false);
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Set field `parent_id` to Null.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['parent_id'] = null;

		return true;
	}

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('VARIABLE_TYPE_', $refType);
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Variable');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Variables');

		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param string $typeName Object type name
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getNameExt($id = null, $typeName = null, $primary = true) {
		if (empty($typeName)) {
			return false;
		}
		$name = (string)$this->getName($id);
		if (!empty($name)) {
			$name = "'" . $name . "' ";
		}
		if ($primary) {
			$result = __('Variable %sof the %s', $name, $typeName);
		} else {
			$result = __('variable %s%s', $name, $typeName);
		}

		return $result;
	}

/**
 * Return full name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving full name
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullName($id = null, $refType = null, $refNode = null, $refId = null, $primary = true) {
		$result = false;
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}

		if ($refType == VARIABLE_TYPE_CONFIG) {
			$typeName = __('global environment');
		} else {
			$typeName = $modelType->getFullName($refId, null, null, null, false);
		}
		$result = $this->getNameExt($id, $typeName, $primary);

		return $result;
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo($id = null, $refType = null, $refNode = null, $refId = null, $includeRoot = null) {
		$result = [];
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}

		$result = $modelType->getBreadcrumbInfo($refId);
		$link = ['action' => 'view', $refType, $refId];
		$result[] = $this->createBreadcrumb(null, $link);
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

/**
 * Return default values of variable
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of variable.
 */
	public function getDefaultValues($refType = null, $refId = null, $includeModelAlias = true) {
		$defaultValues = [
			'ref_id' => $refId,
			'ref_type' => $refType,
			'parent_id' => null,
			'name' => '',
			'value' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return all variables for ID type and ID of the associated record.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param bool $isXmlData Flag of using as XML data
 * @return array|bool Return all variables,
 *  or False on failure.
 */
	public function getAllVariables($refType = null, $refId = null, $isXmlData = false) {
		if (empty($refType) || empty($refId)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_type' => $refType,
			$this->alias . '.ref_id' => $refId,
		];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.ref_id',
			$this->alias . '.ref_type',
			$this->alias . '.parent_id',
			$this->alias . '.lft',
			$this->alias . '.rght',
			$this->alias . '.name',
			$this->alias . '.value',
		];
		$order = [$this->alias . '.lft' => 'asc'];
		$contain = [
			'Attribute' => ['fields' => '*'],
			'Check',
			'Check.Attribute' => ['fields' => '*'],
		];
		if ($isXmlData) {
			$contain = [
				'Attribute',
				'Check',
				'Check.Attribute',
			];
		}

		return $this->find('all', compact('conditions', 'fields', 'contain', 'order'));
	}

/**
 * Return list of variables for ID type and ID of the associated
 *  record by query string.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param string $query Query data
 * @param int $limit Limit for find
 * @return array Return list of check conditions
 */
	public function getListVariables($refType = null, $refId = null, $query = null, $limit = null) {
		$result = [];
		if (empty($refType) || empty($refId)) {
			return $result;
		}

		$conditions = [
			$this->alias . '.ref_id' => $refId,
			$this->alias . '.ref_type' => $refType,
		];

		if (!empty($query)) {
			$conditions[$this->alias . '.name like'] = $query;
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.name',
		];
		$order = [$this->alias . '.name' => 'asc'];
		$recursive = -1;

		return $this->find('list', compact('conditions', 'fields', 'order', 'recursive', 'limit'));
	}

/**
 * Return array for render variable XML elements
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($refType = null, $refId = null) {
		$result = [];
		if (empty($refType)) {
			return $result;
		}

		if (is_array($refType) && empty($refId)) {
			$data = $refType;
		} else {
			$data = $this->getAllVariables($refType, $refId, true);
		}
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $var) {
			if (isset($var[$this->alias])) {
				$var = array_merge($var, $var[$this->alias]);
				unset($var[$this->alias]);
			}
			$variableAttribs = [
				'@name' => $var['name'],
				'@value' => $var['value']
			];
			if (isset($var['Attribute'])) {
				$variableAttribs += $this->Attribute->getXMLnodeAttr($var['Attribute']);
			}

			if (isset($var['Check']) && !empty($var['Check'])) {
				$variableAttribs['condition'] = $this->Check->getXMLdata($var['Check']);
			}

			$result['variable'][] = $variableAttribs;
		}

		return $result;
	}

/**
 * Return information of auto variable with revision of package
 *
 * @param int|string $packageId The ID of the package record.
 * @return array|bool Return information of variable,
 *  or False on failure.
 */
	protected function _getVariableAutoRevision($packageId = null) {
		if (empty($packageId)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_id' => $packageId,
			$this->alias . '.ref_type' => VARIABLE_TYPE_PACKAGE,
			$this->alias . '.name' => VARIABLE_AUTO_REVISION_NAME
		];
		$recursive = -1;

		return $this->find('first', compact('conditions', 'recursive'));
	}

/**
 * Create auto variables with revision for package
 *
 * @param int|string $packageId The ID of the package record.
 * @param string $revision Revision of package for processing.
 * @return bool Success.
 */
	public function createVariableAutoRevision($packageId = null, $revision = null) {
		if (empty($packageId)) {
			return false;
		}
		if (empty($revision)) {
			$revision = '0';
		}

		$result = true;
		$variableItemTemplate = [
			'ref_id' => $packageId,
			'ref_type' => VARIABLE_TYPE_PACKAGE
		];

		$variable = $this->_getVariableAutoRevision($packageId);
		if (!empty($variable)) {
			$variable[$this->alias]['name'] = VARIABLE_AUTO_REVISION_NAME;
			$variable[$this->alias]['value'] = $revision;
		} else {
			$name = VARIABLE_AUTO_REVISION_NAME;
			$value = $revision;
			$variable = [
				$this->alias => $variableItemTemplate + compact('name', 'value')
			];

			$this->create(false);
		}
		if (!$this->save($variable, ['callbacks' => 'before'])) {
			$result = false;
		}

		$query = VARIABLE_AUTO_REVISION_ITEM . '_';
		$cacheVariablesItems = $this->getListVariables(VARIABLE_TYPE_PACKAGE, $packageId, $query);
		$cacheVariablesItems = array_flip($cacheVariablesItems);
		$variableItems = explode('.', $revision);
		$variableItemsCount = count($variableItems);
		$variableItemsData = [];
		foreach ($variableItems as $itemIndex => $value) {
			$name = VARIABLE_AUTO_REVISION_ITEM . ($itemIndex + 1);
			if (isset($cacheVariablesItems[$name])) {
				$id = $cacheVariablesItems[$name];
				unset($cacheVariablesItems[$name]);
			} else {
				$id = null;
			}

			$variableData = compact('id', 'name', 'value');
			$variableItemsData[][$this->alias] = $variableItemTemplate + $variableData;
		}
		if (!empty($cacheVariablesItems)) {
			$conditionsVariableDelete = [$this->alias . '.id' => array_values($cacheVariablesItems)];
			if (!$this->deleteAll($conditionsVariableDelete, true, ['callbacks' => 'before'])) {
				$result = false;
			}
		}

		if (!$this->saveAll($variableItemsData, ['callbacks' => 'before'])) {
			$result = false;
		}

		return $result;
	}

/**
 * Deleting global variables.
 *
 * @return bool Success.
 */
	public function deleteGlobalVariables() {
		$conditions = [
			$this->alias . '.ref_type' => VARIABLE_TYPE_CONFIG
		];
		return $this->deleteAll($conditions, true, false);
	}

/**
 * Return parameters for clearCache
 *
 * @param int|string $id Record ID to retrieve parameters
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache($id = null) {
		$refType = $this->getRefType($id);
		if (empty($refType)) {
			return false;
		}

		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return false;
		}

		return $modelType->getParamClearCache();
	}

/**
 * Prepare reference parameters by ID of type and ID of associated record
 *
 * @param string $convertRef Type of conversion type and ID of
 *  associated record
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @return array Return array of reference parameters
 */
	protected function _prepareRefParam($convertRef = null, $refType = null, $refId = null) {
		$result = [];
		if (empty($convertRef)) {
			$result = compact('refType', 'refId');
			return $result;
		}

		$convertRef = mb_strtolower($convertRef);
		switch ($convertRef) {
			case 'check':
				$modelType = $this->Check->getRefTypeModel($refType);
				if (empty($modelType)) {
					return $result;
				}
				switch ($refType) {
					case CHECK_PARENT_TYPE_PACKAGE:
						$refType = VARIABLE_TYPE_PACKAGE;
						break;
					case CHECK_PARENT_TYPE_PROFILE:
						$refType = VARIABLE_TYPE_PROFILE;
						$refId = $modelType->getRefId($refId);
						break;
					case CHECK_PARENT_TYPE_ACTION:
						$refType = VARIABLE_TYPE_PACKAGE;
						$refId = $modelType->getRefId($refId);
						break;
					case CHECK_PARENT_TYPE_VARIABLE:
						$refInfo = $modelType->getRefInfo($refId);
						$refType = $refInfo['refType'];
						$refId = $refInfo['refId'];
						break;
					default:
						return $result;
				}
				break;
			default:
				return $result;
		}
		if (empty($refType) || empty($refId)) {
			return $result;
		}
		$result = compact('refType', 'refId');

		return $result;
	}

/**
 * Return data for autocomplete
 *
 * @param string $query Query string for autocomplete
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param string $convertRef Type of conversion type and ID of
 *  associated record
 * @param int|string $limit Limit for autocomplete data
 * @return array Data for autocomplete.
 */
	public function getAutocomplete($query = null, $refType = null, $refId = null, $convertRef = null, $limit = null) {
		$result = [];
		$query = trim($query);
		if (empty($query)) {
			return $result;
		}

		$query .= '%';
		$refData = $this->_prepareRefParam($convertRef, $refType, $refId);
		if (empty($refData)) {
			return $result;
		}
		$variablesPkg = $this->getListVariables($refData['refType'], $refData['refId'], $query, $limit);
		$variablesGlobal = $this->getListVariables(VARIABLE_TYPE_CONFIG, 1, $query, $limit);
		$variables = array_merge($variablesPkg, $variablesGlobal);
		$result = array_values(array_unique($variables));

		return $result;
	}
}
