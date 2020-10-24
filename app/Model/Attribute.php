<?php
/**
 * This file is the model file of the application. Used to
 *  manage attributes.
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
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('CheckPcrePattern', 'Utility');

/**
 * The model is used to manage attributes.
 *
 * @package app.Model
 */
class Attribute extends AppModel {

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'TrimStringField',
		'BreadCrumbExt' => [
			'refTypeField' => 'ref_type',
			'refNodeField' => 'ref_node',
			'refIdField' => 'ref_id'
		],
		'UpdateModifiedDate',
		'ClearViewCache',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_ATTRIBUTE],
		'ValidationRules'
	];

/**
 * Name of the validation string domain to use when translating validation errors.
 *
 * @var array
 */
	public $validationDomain = 'validation_errors_attribute';

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [];

/**
 * List of default validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	protected $_validateDefault = [
		'ref_type' => [
			'rule' => ['checkRange', 'ATTRIBUTE_TYPE_', false],
			'message' => 'Invalid attribute type.',
			'required' => true,
			'allowEmpty' => false,
			'last' => true,
		],
		'ref_node' => [
			'checkRangeNode' => [
				'rule' => ['checkRange', 'ATTRIBUTE_NODE_', false],
				'message' => 'Invalid attribute node.',
				'required' => true,
				'allowEmpty' => false,
				'last' => true,
			],
			'checkFillOneField' => [
				'rule' => 'checkFillOneField',
				'message' => 'At least one field must be filled in.',
				'required' => false,
				'allowEmpty' => true,
				'last' => true,
			],
		],
		'ref_id' => [
			'refexists' => [
				'rule' => 'notBlank',
				'message' => 'Invalid attribute reference ID.',
				'last' => true
			],
			'isuniquenew' => [
				'rule' => [
					'isUnique',
					[
						'ref_id',
						'ref_type',
						'ref_node'
					],
					false
				],
				'on' => 'create',
				'message' => 'The attributes already exists.',
				'last' => true
			]
		],
		'pcre_parsing' => [
			'rule' => 'boolean',
			'message' => 'Invalid PCRE parsing flag.',
			'required' => true,
			'allowEmpty' => true,
			'last' => true,
		],
		'architecture' => [
			'checkArchitecture' => [
				'rule' => ['checkRange', 'ATTRIBUTE_ARCHITECTURE_', false],
				'message' => 'Invalid architecture.',
				'required' => false,
				'allowEmpty' => true,
				'last' => true,
			],
		]
	];

/**
 * List of extended validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	protected $_validateExt = [
		'normal' => [
			'hostname' => [
				'minLength' => [
					'rule' => ['minLength', 1],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'Computer name must contain at least 1 characters.'
				],
				'maxLength' => [
					'rule' => ['maxLength', 15],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'Computer name must contain no more than 15 characters.'
				],
				'forbiddenCharacters' => [
					'rule' => ['custom', '/^[^\/\:\*\?\"\<\>\|\\\]+$/'],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'Computer name must not contain the following characters: (\), (/), (:), (*), (?), ("), (<>), (<), (>), (|).'
				]
			],
			'ipaddresses' => [
				'ip' => [
					'rule' => 'ip',
					'required' => false,
					'allowEmpty' => true,
					'message' => 'Enter a valid IP address.'
				]
			],
			'domainname' => [
				'minLength' => [
					'rule' => ['minLength', 2],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'DNS domain name must contain at least 2 characters.'
				],
				'maxLength' => [
					'rule' => ['maxLength', 24],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'DNS domain name must contain no more than 24 characters.'
				],
				'forbiddenCharacters' => [
					'rule' => ['custom', '/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*[a-zA-Z0-9]$/'],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'DNS domain name must not contain the following characters: (,), (~), (:), (!), (@), (#), ($), (%), (^), (&), (\'), (.), (()), ({}), (_).'
				]
			],
			'groups' => [
				'maxLength' => [
					'rule' => ['maxLength', 100],
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
					'message' => 'Group name must contain no more than 100 characters.'
				]
			],
		],
		'pcre' => [
			'hostname' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'os' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'ipaddresses' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'domainname' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'groups' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'lcid' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
			'lcidOS' => [
				'checkRegex' => [
					'rule' => 'checkRegex',
					'message' => 'Invalid PCRE pattern.',
					'required' => false,
					'allowEmpty' => true,
					'last' => true,
				],
			],
		]
	];

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$virtualFields = $this->_getListVirtualFields();
		foreach ($virtualFields as $virtualFieldName => $virtualFieldValue) {
			$this->virtualFields[$virtualFieldName] = 1;
		}
	}

/**
 * PCRE pattern validation
 *
 * @param array $data Data to check
 * @return bool Return True, if valid
 */
	public function checkRegex($data = null) {
		if (!$this->data[$this->alias]['pcre_parsing']) {
			return true;
		}

		$value = reset($data);
		return CheckPcrePattern::checkPattern($value);
	}

/**
 * Checking at least one field must be filled in
 *
 * @param array $data Data to check
 * @return bool Return True, if valid
 */
	public function checkFillOneField($data = null) {
		if (!isset($this->data[$this->alias]) || empty($this->data[$this->alias])) {
			return false;
		}

		$listFields = [
			'hostname',
			'os',
			'architecture',
			'ipaddresses',
			'domainname',
			'groups',
			'lcid',
			'lcidOS'
		];
		$dataToCheck = array_intersect_key($this->data[$this->alias], array_flip($listFields));
		$processedData = Hash::filter($dataToCheck);
		$result = !empty($processedData);

		return $result;
	}

/**
 * Create validation rules by PCRE parsing flag
 *
 * @param bool $pcreParsing PCRE parsing flag
 * @return bool Return success.
 */
	public function createValidationRules($pcreParsing = false) {
		$type = 'normal';
		if ($pcreParsing) {
			$type = 'pcre';
		}
		if (!isset($this->_validateExt[$type])) {
			return false;
		}
		$this->validate = Hash::merge($this->_validateDefault, $this->_validateExt[$type]);

		return true;
	}

/**
 * Called during validation operations, before validation.
 *
 * Actions:
 *  - Create validation rules.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		return $this->createValidationRules($this->data[$this->alias]['pcre_parsing']);
	}

/**
 * Called after each find operation.
 *
 * Actions:
 *  - Update result if used virtual fields.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		$virtualFields = $this->_getListVirtualFields();
		foreach ($results as $key => $val) {
			foreach ($virtualFields as $virtualFieldName => $virtualFieldValue) {
				if (isset($val[$this->alias][$virtualFieldName])) {
					$results[$key][$this->alias][$virtualFieldName] = $virtualFieldValue;
				}
			}
		}

		return $results;
	}

/**
 * Converting an array of language code IDs to a string
 *
 * @param array|string $lcid Language code IDs
 * @return string Return list of language code IDs.
 */
	public function lcidToString($lcid = []) {
		$result = '';
		if (empty($lcid)) {
			return $result;
		}
		if (!is_array($lcid)) {
			return $lcid;
		}
		$result = implode(',', $lcid);

		return $result;
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Converting an array of language code IDs to a string.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$fields = ['lcid', 'lcidOS'];
		foreach ($fields as $field) {
			if (isset($this->data[$this->alias][$field])) {
				$this->data[$this->alias][$field] = $this->lcidToString($this->data[$this->alias][$field]);
			}
		}

		return true;
	}

/**
 * Return list of virtual fields
 *
 * @return array Return list of virtual fields.
 */
	protected function _getListVirtualFields() {
		$listOs = $this->getListOS();
		$listLangId = $this->getListLangID();
		$listVirtualFields = compact('listOs', 'listLangId');

		return $listVirtualFields;
	}

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('ATTRIBUTE_TYPE_', $refType);
	}

/**
 * Return node name by node ID
 *
 * @param int|string $refNode ID of node
 * @return string Return node name
 */
	public function getNameNodeFor($refNode = null) {
		return $this->getNameConstantForVal('ATTRIBUTE_NODE_', $refNode);
	}

/**
 * Return object Model for type by ID type and ID node of object.
 *
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID of node
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel($refType = null, $refNode = null) {
		$invalidNodeList = [
			ATTRIBUTE_NODE_VARIABLE,
			ATTRIBUTE_NODE_CHECK
		];
		if (in_array($refNode, $invalidNodeList)) {
			return false;
		}

		switch ($refType) {
			case ATTRIBUTE_TYPE_PACKAGE:
				$invalidNodeList = [
					ATTRIBUTE_NODE_DEPENDS,
					ATTRIBUTE_NODE_INCLUDE,
					ATTRIBUTE_NODE_CHAIN,
					ATTRIBUTE_NODE_ACTION
				];
				if (in_array($refNode, $invalidNodeList)) {
					return false;
				}
				break;
			case ATTRIBUTE_TYPE_PROFILE:
				$invalidNodeList = [ATTRIBUTE_NODE_PACKAGE, ATTRIBUTE_NODE_DEPENDS];
				if (in_array($refNode, $invalidNodeList)) {
					return false;
				}
				break;
			case ATTRIBUTE_TYPE_HOST:
				$invalidNodeList = [ATTRIBUTE_NODE_PROFILE];
				if (in_array($refNode, $invalidNodeList)) {
					return false;
				}
				break;
		}

		$type = $this->getNameTypeFor($refType);
		if (empty($type)) {
			return false;
		}
		$modelName = ucfirst($type);
		$result = ClassRegistry::init($modelName, true);

		return $result;
	}

/**
 * Return object Model for node by ID type and ID node of object.
 *
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID of node
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefNodeModel($refType = null, $refNode = null) {
		$modelName = null;
		switch ($refNode) {
			case ATTRIBUTE_NODE_PACKAGE:
				if ($refType == ATTRIBUTE_TYPE_PROFILE) {
					$modelName = 'PackagesProfile';
				}
				break;
			case ATTRIBUTE_NODE_DEPENDS:
				if ($refType == ATTRIBUTE_TYPE_PACKAGE) {
					$modelName = 'PackagesPackage';
				} elseif ($refType == ATTRIBUTE_TYPE_PROFILE) {
					$modelName = 'ProfilesProfile';
				}
				break;
			case ATTRIBUTE_NODE_INCLUDE:
				$modelName = 'PackagesInclude';
				break;
			case ATTRIBUTE_NODE_CHAIN:
				$modelName = 'PackagesChain';
				break;
			case ATTRIBUTE_NODE_ACTION:
				$modelName = 'PackageAction';
				break;
			case ATTRIBUTE_NODE_PROFILE:
				$modelName = 'HostsProfile';
				break;
		}
		if (empty($modelName)) {
			$node = $this->getNameNodeFor($refNode);
			if (empty($node)) {
				return false;
			}
			$modelName = ucfirst($node);
		}
		$result = ClassRegistry::init($modelName, true);

		return $result;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Attributes');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		return $this->getTargetName();
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
		if ($primary) {
			$result = __('Attributes of the %s', $typeName);
		} else {
			$result = __('attributes %s', $typeName);
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
		$modelType = $this->getRefTypeModel($refType, $refNode);
		$modelNode = $this->getRefNodeModel($refType, $refNode);
		if (empty($modelNode)) {
			return $result;
		}
		$name = '';
		if (!empty($modelType) && ($refType != $refNode)) {
			$typeName = $modelType->getFullName($refId);
			if (!empty($typeName)) {
				$name = $typeName;
			}
		}
		$refInfo = $modelNode->getRefInfo($refId);
		$nodeName = $modelNode->getFullName($refId, $refInfo['refType'], null, $refInfo['refId'], false);
		if (!empty($nodeName)) {
			if (!empty($name)) {
				$name .= ' ';
			}
			$name .= $nodeName;
		}
		$result = $this->getNameExt($id, $name, $primary);

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
		$modelType = $this->getRefTypeModel($refType, $refNode);
		$modelNode = $this->getRefNodeModel($refType, $refNode);
		if (empty($modelNode)) {
			return $result;
		}

		if (!empty($modelType)) {
			$result = $modelType->getBreadcrumbInfo();
		}
		$refInfo = $modelNode->getRefInfo($refId);
		$nodeInfo = $modelNode->getBreadcrumbInfo($refId, $refInfo['refType'], null, $refInfo['refId'], false);
		$result = array_merge($result, $nodeInfo);
		$result[] = $this->createBreadcrumb(null, false);

		return $result;
	}

/**
 * Return list of OS for attributes
 *
 * @return array Return list of OS
 */
	public function getListOS() {
		return $this->getListDataFromConstant('ATTRIBUTE_OS_', 'attribute_os');
	}

/**
 * Return list of architectures for attributes
 *
 * @return array Return list of architectures
 */
	public function getListArchitecture() {
		return $this->getListDataFromConstant('ATTRIBUTE_ARCHITECTURE_', 'attribute_architecture');
	}

/**
 * Return list of language codes for attributes
 *
 * @return array Return list of language codes
 */
	public function getListLangID() {
		return $this->getListDataFromConstant('ATTRIBUTE_LCID_', 'attribute_lcid');
	}

/**
 * Return array with XML attributes
 *
 * @param array $attributes Information of attributes
 * @return array Return array with XML attributes
 */
	public function getXMLnodeAttr($attributes = []) {
		$result = [];
		if (empty($attributes)) {
			return $result;
		}

		if (isset($attributes[0])) {
			$attributes = $attributes[0];
		}
		$excludeKeys = [
			'ref_id',
			'pcre_parsing'
		];
		foreach ($attributes as $attrKey => $attrVal) {
			if (empty($attrVal) || in_array($attrKey, $excludeKeys)) {
				continue;
			}

			$result['@' . $attrKey] = $attrVal;
		}

		return $result;
	}

/**
 * Return record ID by ID type, ID node of object and
 *  ID of node.
 *
 * @param int|string $refType ID of type
 * @param int|string $refNode ID of node
 * @param int|string $refId Record ID of the node
 * @return string|bool Return record ID,
 *  or False on failure.
 */
	public function getIdFor($refType = null, $refNode = null, $refId = null) {
		if (empty($refType) || empty($refNode) || empty($refId)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_type' => $refType,
			$this->alias . '.ref_node' => $refNode,
			$this->alias . '.ref_id' => $refId,
		];

		return $this->field('id', $conditions);
	}

/**
 * Return information of attributes
 *
 * @param int|string $id The ID of the record to read.
 * @param array $options Options for find() (not used in this method).
 * @param bool $full Flag of inclusion in the result
 *  full information.
 * @return array|bool Return information of attributes,
 *  or False on failure.
 */
	public function get($id = null, array $options = [], $full = false) {
		if (empty($id)) {
			return false;
		}

		$conditions = [$this->alias . '.id' => $id];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.ref_id',
			$this->alias . '.ref_type',
			$this->alias . '.ref_node',
			$this->alias . '.pcre_parsing',
			$this->alias . '.hostname',
			$this->alias . '.os',
			$this->alias . '.architecture',
			$this->alias . '.ipaddresses',
			$this->alias . '.domainname',
			$this->alias . '.groups',
			$this->alias . '.lcid',
			$this->alias . '.lcidOS'
		];
		if ($full) {
			$fields[] = $this->alias . '.listOs';
			$fields[] = $this->alias . '.listLangId';
		}
		$recursive = -1;

		return $this->find('first', compact('conditions', 'fields', 'recursive'));
	}

/**
 * Return default values of attributes
 *
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of attributes.
 */
	public function getDefaultValues($refType = null, $refNode = null, $refId = null, $includeModelAlias = true) {
		$defaultValues = [
			'ref_id' => $refId,
			'ref_type' => $refType,
			'ref_node' => $refNode,
			'pcre_parsing' => false,
			'hostname' => '',
			'os' => '',
			'architecture' => '',
			'ipaddresses' => '',
			'domainname' => '',
			'groups' => '',
			'lcid' => '',
			'lcidOS' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return parameters for clearCache
 *
 * @param int|string $id Record ID to retrieve parameters
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache($id = null) {
		$refInfo = $this->getRefInfo($id);
		if (empty($refInfo)) {
			return false;
		}

		extract($refInfo, EXTR_OVERWRITE);
		$modelNode = $this->getRefNodeModel($refType, $refNode);
		if (empty($modelNode)) {
			return false;
		}

		return $modelNode->getParamClearCache($refId);
	}

/**
 * Remove attributes without reference records
 *
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @return bool Success
 */
	public function clearUnusedAttributes($refType = null, $refNode = null) {
		$modelNode = $this->getRefNodeModel($refType, $refNode);
		if (empty($modelNode)) {
			return $result;
		}
		$bindCfg = [
			'belongsTo' => [
				$modelNode->name => [
					'className' => $modelNode->name,
					'foreignKey' => '',
					'conditions' => [
						$this->alias . '.ref_type' => $refType,
						$this->alias . '.ref_node' => $refNode,
						$this->alias . '.ref_id = ' . $modelNode->alias . '.id',
					],
					'dependent' => false
				]
			]
		];
		$this->bindModel($bindCfg, true);
		$conditions = [
			$this->alias . '.ref_type' => $refType,
			$this->alias . '.ref_node' => $refNode,
			$modelNode->alias . '.id' => null
		];
		$this->recursive = 0;

		return $this->deleteAll($conditions, false);
	}
}
