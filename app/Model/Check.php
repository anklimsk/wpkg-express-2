<?php
/**
 * This file is the model file of the application. Used to
 *  manage checks.
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
App::uses('ClassRegistry', 'Utility');
App::uses('RenderCheckData', 'Utility');

/**
 * The model is used to manage checks.
 *
 * @package app.Model
 */
class Check extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'type';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Tree',
		'ScopeTree',
		'TrimStringField',
		'BreadCrumbExt' => [
			'refTypeField' => 'ref_type',
			'refIdField' => 'ref_id'
		],
		'UpdateModifiedDate',
		'MoveExt',
		'ClearViewCache',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_CHECK],
		'ValidationRules'
	];

/**
 * Name of the validation string domain to use when translating validation errors.
 *
 * @var array
 */
	public $validationDomain = 'validation_errors_check';

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
		'ref_id' => [
			'rule' => 'notBlank',
			'message' => 'Invalid check reference ID.',
		],
		'ref_type' => [
			'rule' => ['checkRange', 'CHECK_PARENT_TYPE_', false],
			'message' => 'Invalid check type.'
		],
		'type' => [
			'rule' => ['checkRange', 'CHECK_TYPE_', false],
			'message' => 'Check type attribute is invalid.'
		],
		'condition' => [
			'rule' => ['checkRange', 'CHECK_CONDITION_', false],
			'message' => 'Check condition attribute is invalid.'
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
		CHECK_TYPE_LOGICAL => [
			CHECK_CONDITION_LOGICAL_NOT => [
			],
			CHECK_CONDITION_LOGICAL_AND => [
			],
			CHECK_CONDITION_LOGICAL_OR => [
			],
			CHECK_CONDITION_LOGICAL_AT_LEAST => [
			],
			CHECK_CONDITION_LOGICAL_AT_MOST => [
			]
		],
		CHECK_TYPE_REGISTRY => [
			CHECK_CONDITION_REGISTRY_EXISTS => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a registry path.'
					]
				]
			],
			CHECK_CONDITION_REGISTRY_EQUALS => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a registry path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a registry value.'
					]
				]
			]
		],
		CHECK_TYPE_FILE => [
			CHECK_CONDITION_FILE_EXISTS => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				]
			],
			CHECK_CONDITION_FILE_SIZE_EQUALS => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file size.'
					]
				]
			],
			CHECK_CONDITION_FILE_VERSION_SMALLER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file version.'
					]
				]
			],
			CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file version.'
					]
				]
			],
			CHECK_CONDITION_FILE_VERSION_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file version.'
					]
				]
			],
			CHECK_CONDITION_FILE_VERSION_GREATER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file version.'
					]
				]
			],
			CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file version.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file modification date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file modification date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file modification date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file creation date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file creation date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file creation date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file access date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file access date.'
					]
				]
			],
			CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a file access date.'
					]
				]
			]
		],
		CHECK_TYPE_UNINSTALL => [
			CHECK_CONDITION_UNINSTALL_EXISTS => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				]
			],
			CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a program version.'
					]
				]
			],
			CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a program version.'
					]
				]
			],
			CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a program version.'
					]
				]
			],
			CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a program version.'
					]
				]
			],
			CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a Add/Remove program name.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a program version.'
					]
				]
			]
		],
		CHECK_TYPE_EXECUTE => [
			CHECK_CONDITION_EXECUTE_EXIT_CODE_SMALLER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a executable path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a exit code.'
					]
				]
			],
			CHECK_CONDITION_EXECUTE_EXIT_CODE_LESS_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a executable path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a exit code.'
					]
				]
			],
			CHECK_CONDITION_EXECUTE_EXIT_CODE_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a executable path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a exit code.'
					]
				]
			],
			CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a executable path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a exit code.'
					]
				]
			],
			CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN_OR_EQUAL_TO => [
				'path' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a executable path.'
					]
				],
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a exit code.'
					]
				]
			]
		],
		CHECK_TYPE_HOST => [
			CHECK_CONDITION_HOST_HOSTNAME => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a host name.'
					]
				]
			],
			CHECK_CONDITION_HOST_OS => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a OS.'
					]
				]
			],
			CHECK_CONDITION_HOST_ARCHITECTURE => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a architecture.'
					]
				]
			],
			CHECK_CONDITION_HOST_IP_ADDRESSES => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a ip addresses.'
					]
				]
			],
			CHECK_CONDITION_HOST_DOMAIN_NAME => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a domain name.'
					]
				]
			],
			CHECK_CONDITION_HOST_GROUPS => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a groups.'
					]
				]
			],
			CHECK_CONDITION_HOST_LCID => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a language ID.'
					]
				]
			],
			CHECK_CONDITION_HOST_LCID_OS => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter a language ID OS.'
					]
				]
			],
			CHECK_CONDITION_HOST_ENVIRONMENT => [
				'value' => [
					'empty' => [
						'rule' => 'notBlank',
						'required' => true,
						'allowEmpty' => false,
						'last' => true,
						'message' => 'You must enter check of environment variables.'
					]
				]
			]
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
					ATTRIBUTE_TYPE_PACKAGE,
					ATTRIBUTE_TYPE_PROFILE,
					ATTRIBUTE_TYPE_ACTION,
					ATTRIBUTE_TYPE_VARIABLE
				],
				'ref_node' => ATTRIBUTE_NODE_CHECK
			],
			'fields' => [
				'Attribute.hostname',
				'Attribute.os',
				'Attribute.architecture',
				'Attribute.ipaddresses',
				'Attribute.domainname',
				'Attribute.groups',
				'Attribute.lcid',
				'Attribute.lcidOS'
			]
		]
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
 * Called during validation operations, before validation.
 *
 * Actions:
 *  - Create validation rules;
 *  - Converting an array of language code IDs to a string.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		if (!isset($this->data[$this->alias]['type']) ||
			!isset($this->data[$this->alias]['condition'])) {
			return false;
		}

		if (!$this->createValidationRules($this->data[$this->alias]['type'], $this->data[$this->alias]['condition']) ||
			!$this->_convertLcidToString()) {
			return false;
		}

		return true;
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Converting an array of language code IDs to a string;
 *  - Set field `parent_id` to Null for empty values.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		if (!$this->_convertLcidToString()) {
			return false;
		}

		if (isset($this->data[$this->alias]['parent_id']) &&
			empty($this->data[$this->alias]['parent_id'])) {
			$this->data[$this->alias]['parent_id'] = null;
		}

		return true;
	}

/**
 * Create validation rules by type ID and condition ID
 *
 * @param int|string $typeId ID of type for check
 * @param int|string $conditionId ID of condition for check
 * @return bool Return success.
 */
	public function createValidationRules($typeId = null, $conditionId = null) {
		if (empty($typeId) || empty($conditionId)) {
			return false;
		}

		if (!isset($this->_validateExt[$typeId][$conditionId])) {
			return false;
		}
		$this->validate = Hash::merge($this->_validateDefault, $this->_validateExt[$typeId][$conditionId]);

		return true;
	}

/**
 * Convert an array of language code IDs to a string.
 *
 * @return bool Return success.
 */
	protected function _convertLcidToString() {
		if (!isset($this->data[$this->alias]['type']) ||
			!isset($this->data[$this->alias]['condition'])) {
			return false;
		}

		if (!isset($this->data[$this->alias]['value']) ||
			empty($this->data[$this->alias]['value'])) {
			return true;
		}

		if (($this->data[$this->alias]['type'] != CHECK_TYPE_HOST) ||
			(!in_array($this->data[$this->alias]['condition'], [CHECK_CONDITION_HOST_LCID, CHECK_CONDITION_HOST_LCID_OS]))) {
			return true;
		}

		$this->data[$this->alias]['value'] = $this->Attribute->lcidToString($this->data[$this->alias]['value']);
		return true;
	}

/**
 * Return all checks for ID type and ID of the associated record.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @return array|bool Return all checks,
 *  or False on failure.
 */
	public function getChecks($refType = null, $refId = null) {
		if (empty($refType) || empty($refId)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_id' => $refId,
			$this->alias . '.ref_type' => $refType
		];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.ref_type',
			$this->alias . '.type',
			$this->alias . '.condition',
			$this->alias . '.path',
			$this->alias . '.value',
			$this->alias . '.lft',
			$this->alias . '.parent_id',
			$this->alias . '.ref_id'
		];
		$order = [$this->alias . '.lft' => 'asc'];
		$contain = ['Attribute' => ['fields' => '*']];

		return $this->find('all', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return default values of check
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of check.
 */
	public function getDefaultValues($refType = null, $refId = null, $includeModelAlias = true) {
		$defaultValues = [
			'ref_id' => $refId,
			'ref_type' => $refType,
			'parent_id' => null,
			'type' => CHECK_TYPE_UNINSTALL,
			'condition' => CHECK_CONDITION_UNINSTALL_EXISTS,
			'path' => '',
			'value' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return logical check name by condition ID
 *
 * @param int|string $conditionId ID of condition for check
 * @return string Return logical check name.
 */
	protected function _getConditionNameById($conditionId = null) {
		$conditionName = constValToLcSingle('CHECK_CONDITION_LOGICAL_', $conditionId, ' ');
		$result = __d('logical_check_type', $conditionName);

		return $result;
	}

/**
 * Return list of logical checks for ID type and ID of the associated record.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @return array|bool Return list of logical checks,
 *  or False on failure.
 */
	public function getLogicalChecksList($refType = null, $refId = null) {
		if (!in_array($refType, constsVals('CHECK_PARENT_TYPE_')) || empty($refId)) {
			return false;
		}

		$conditions = [
			$this->alias . '.ref_id' => $refId,
			$this->alias . '.ref_type' => $refType,
			$this->alias . '.type' => CHECK_TYPE_LOGICAL
		];
		$logicalChecks = $this->generateTreeList($conditions, '{n}.Check.id', '{n}.Check.condition', '-', -1);
		foreach ($logicalChecks as $k => &$v) {
			$pattern = '/^(-*)(\d+)$/';
			if (!preg_match($pattern, $v, $logicalChecksLevel)) {
				$logicalChecksLevel = [0 => $v, 1 => '', 2 => $v];
			}

			$conditionName = $this->_getConditionNameById($logicalChecksLevel[2]);
			$v = '- ' . $logicalChecksLevel[1] . __('Logical %s', mb_strtoupper($conditionName));
		}
		unset($v);
		$rootNode = [null => __('Root node')];
		$logicalChecks = $rootNode + $logicalChecks;

		return $logicalChecks;
	}

/**
 * Return list of check types
 *
 * @return array Return list of check types
 */
	public function getListCheckTypes() {
		return $this->getListDataFromConstant('CHECK_TYPE_', 'check_type');
	}

/**
 * Return list of check conditions for check type
 *
 * @param string $typeName Name of type
 * @return array Return list of check conditions
 */
	public function getListCheckConditions($typeName = null) {
		$result = [];
		if (empty($typeName)) {
			return $result;
		}

		$prefix = 'CHECK_CONDITION_' . strtoupper($typeName);
		return $this->getListDataFromConstant($prefix, 'check_condition');
	}

/**
 * Return array for render check XML elements
 *
 * @param array $data Information of checks
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($data = []) {
		$refs = [];
		$result = [];
		if (empty($data) || !is_array($data)) {
			return $result;
		}

		foreach ($data as $check) {
			switch ($check['type']) {
				case CHECK_TYPE_LOGICAL:
					$type = 'logical';
					switch ($check['condition']) {
						case CHECK_CONDITION_LOGICAL_NOT:
							$condition = 'not';
							break;
						case CHECK_CONDITION_LOGICAL_AND:
							$condition = 'and';
							break;
						case CHECK_CONDITION_LOGICAL_OR:
							$condition = 'or';
							break;
						case CHECK_CONDITION_LOGICAL_AT_LEAST:
							$condition = 'atleast';
							break;
						case CHECK_CONDITION_LOGICAL_AT_MOST:
							$condition = 'atmost';
							break;
					}
					break;
				case CHECK_TYPE_REGISTRY:
					$type = 'registry';
					switch ($check['condition']) {
						case CHECK_CONDITION_REGISTRY_EXISTS:
							$condition = 'exists';
							break;
						case CHECK_CONDITION_REGISTRY_EQUALS:
							$condition = 'equals';
							$value = $check['value'];
							break;
					}
					$path = $check['path'];
					break;
				case CHECK_TYPE_FILE:
					$type = 'file';
					switch ($check['condition']) {
						case CHECK_CONDITION_FILE_EXISTS:
							$condition = 'exists';
							break;
						case CHECK_CONDITION_FILE_SIZE_EQUALS:
							$condition = 'sizeequals';
							break;
						case CHECK_CONDITION_FILE_VERSION_SMALLER_THAN:
							$condition = 'versionsmallerthan';
							break;
						case CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO:
							$condition = 'versionlessorequal';
							break;
						case CHECK_CONDITION_FILE_VERSION_EQUAL_TO:
							$condition = 'versionequalto';
							break;
						case CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO:
							$condition = 'versiongreaterorequal';
							break;
						case CHECK_CONDITION_FILE_VERSION_GREATER_THAN:
							$condition = 'versiongreaterthan';
							break;
						case CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO:
							$condition = 'datemodifyequalto';
							break;
						case CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN:
							$condition = 'datemodifynewerthan';
							break;
						case CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN:
							$condition = 'datemodifyolderthan';
							break;
						case CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO:
							$condition = 'datecreateequalto';
							break;
						case CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN:
							$condition = 'datecreatenewerthan';
							break;
						case CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN:
							$condition = 'datecreateolderthan';
							break;
						case CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO:
							$condition = 'dateaccessequalto';
							break;
						case CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN:
							$condition = 'dateaccessnewerthan';
							break;
						case CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN:
							$condition = 'dateaccessolderthan';
							break;
					}
					if ($check['condition'] != CHECK_CONDITION_FILE_EXISTS) {
						$value = $check['value'];
					}
					$path = $check['path'];
					break;
				case CHECK_TYPE_EXECUTE:
					$type = 'execute';
					switch ($check['condition']) {
						case CHECK_CONDITION_EXECUTE_EXIT_CODE_SMALLER_THAN:
							$condition = 'exitcodesmallerthan';
							break;
						case CHECK_CONDITION_EXECUTE_EXIT_CODE_LESS_THAN_OR_EQUAL_TO:
							$condition = 'exitcodelessorequal';
							break;
						case CHECK_CONDITION_EXECUTE_EXIT_CODE_EQUAL_TO:
							$condition = 'exitcodeequalto';
							break;
						case CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN_OR_EQUAL_TO:
							$condition = 'exitcodegreaterorequal';
							break;
						case CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN:
							$condition = 'exitcodegreaterthan';
							break;
					}
					$path = $check['path'];
					$value = $check['value'];
					break;
				case CHECK_TYPE_UNINSTALL:
					$type = 'uninstall';
					switch ($check['condition']) {
						case CHECK_CONDITION_UNINSTALL_EXISTS:
							$condition = 'exists';
							break;
						case CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN:
							$condition = 'versionsmallerthan';
							break;
						case CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO:
							$condition = 'versionlessorequal';
							break;
						case CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO:
							$condition = 'versionequalto';
							break;
						case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO:
							$condition = 'versiongreaterorequal';
							break;
						case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN:
							$condition = 'versiongreaterthan';
							break;
					}
					if ($check['condition'] != CHECK_CONDITION_UNINSTALL_EXISTS) {
						$value = $check['value'];
					}
					$path = $check['path'];
					break;
				case CHECK_TYPE_HOST:
					$type = 'host';
					switch ($check['condition']) {
						case CHECK_CONDITION_HOST_HOSTNAME:
							$condition = 'hostname';
							break;
						case CHECK_CONDITION_HOST_OS:
							$condition = 'os';
							break;
						case CHECK_CONDITION_HOST_ARCHITECTURE:
							$condition = 'architecture';
							break;
						case CHECK_CONDITION_HOST_IP_ADDRESSES:
							$condition = 'ipaddresses';
							break;
						case CHECK_CONDITION_HOST_DOMAIN_NAME:
							$condition = 'domainname';
							break;
						case CHECK_CONDITION_HOST_GROUPS:
							$condition = 'groups';
							break;
						case CHECK_CONDITION_HOST_LCID:
							$condition = 'lcid';
							break;
						case CHECK_CONDITION_HOST_LCID_OS:
							$condition = 'lcidOS';
							break;
						case CHECK_CONDITION_HOST_ENVIRONMENT:
							$condition = 'environment';
							break;
					}
					if ($check['condition'] != CHECK_CONDITION_UNINSTALL_EXISTS) {
						$value = $check['value'];
					}
					$path = null;//$check['path'];
					break;
				default:
					$type = 'unknown';
					$condition = 'unknown';
			}

			$chkAttribs = ['check' => ['@type' => $type, '@condition' => $condition]];
			if (isset($path)) {
				$chkAttribs['check']['@path'] = $path;
				unset($path);
			}
			if (isset($value)) {
				$chkAttribs['check']['@value'] = $value;
				unset($value);
			}

			if (isset($check['Attribute'])) {
				$chkAttribs['check'] += $this->Attribute->getXMLnodeAttr($check['Attribute']);
			}

			$thisref = &$refs[$check['id']];
			foreach ($chkAttribs as $key => $attrib) {
				$thisref[$key] = $chkAttribs[$key];
			}

			if (empty($check['parent_id'])) {
				$result[$check['id']] = &$thisref;
			} else {
				$refs[$check['parent_id']]['check'][$check['id']] = &$thisref;
			}
		}

		return $this->_prepareCheck($result);
	}

/**
 * Prepare data for render XML
 *
 * @param array $data Information of checks
 * @return array Return array for render XML
 */
	protected function _prepareCheck($data = []) {
		$result = [];
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $resKey => $resVal) {
			if (is_array($resVal)) {
				$resVal = $this->_prepareCheck($resVal);
				if (is_numeric($resKey)) {
					if (isset($result['check'])) {
						$result['check'] = array_merge($result['check'], $resVal);
					} else {
						$result['check'] = $resVal;
					}
				} elseif ($resKey == 'check') {
					$result[] = $resVal;
				}
			} else {
				$result[$resKey] = $resVal;
			}
		}

		return $result;
	}

/**
 * Return type name by type ID
 *
 * @param int|string $refType ID of type
 * @return string Return type name
 */
	public function getNameTypeFor($refType = null) {
		return $this->getNameConstantForVal('CHECK_PARENT_TYPE_', $refType);
	}

/**
 * Return object Model for type by ID type.
 *
 * @param int|string $refType ID type of object
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel($refType = null) {
		$modelName = null;
		switch ($refType) {
			case CHECK_PARENT_TYPE_ACTION:
				$modelName = 'PackageAction';
				break;
			case CHECK_PARENT_TYPE_PROFILE:
				$modelName = 'PackagesProfile';
				break;
		}
		if (empty($modelName)) {
			$type = $this->getNameTypeFor($refType);
			if (empty($type)) {
				return false;
			}
			$modelName = ucfirst($type);
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
		$result = __('Check');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Checks');
		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getName($id = null) {
		$result = false;
		if (empty($id)) {
			return $result;
		}

		if (is_array($id)) {
			if (!isset($id[$this->alias])) {
				return $result;
			}
			$data = $id;
		} elseif (ctype_digit((string)$id)) {
			$data = $this->get($id);
			if (empty($data)) {
				return $result;
			}
		} else {
			return $result;
		}
		$result = RenderCheckData::getTextCheckCondition($data[$this->alias]);

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
		if ($name !== '') {
			$name = "'" . $name . "' ";
		}
		if ($primary) {
			$result = __('Check %sof the %s', $name, $typeName);
		} else {
			$result = __('check %s%s', $name, $typeName);
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
		$refInfo = $modelType->getRefInfo($refId);
		$typeName = $modelType->getFullName($refId, $refInfo['refType'], null, $refInfo['refId'], false);
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
		$refInfo = $modelType->getRefInfo($refId);
		$result = $modelType->getBreadcrumbInfo($refId, $refInfo['refType'], null, $refInfo['refId']);
		$link = false;
		if (!empty($refType) && !empty($refId)) {
			$link = ['action' => 'view', $refType, $refId];
		}
		$result[] = $this->createBreadcrumb(null, $link);
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false, false);
		}

		return $result;
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
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return false;
		}

		return $modelType->getParamClearCache($refId);
	}

/**
 * Remove checks without reference records
 *
 * @param int|string $refType ID type of object
 * @return bool Success
 */
	public function clearUnusedChecks($refType = null) {
		$modelType = $this->getRefTypeModel($refType);
		if (empty($modelType)) {
			return $result;
		}
		$bindCfg = [
			'belongsTo' => [
				$modelType->name => [
					'className' => $modelType->name,
					'foreignKey' => '',
					'conditions' => [
						$this->alias . '.ref_type' => $refType,
						$this->alias . '.ref_id = ' . $modelType->alias . '.id',
					],
					'dependent' => false
				]
			]
		];
		$this->bindModel($bindCfg, true);
		$conditions = [
			$this->alias . '.ref_type' => $refType,
			$modelType->alias . '.id' => null
		];
		$this->recursive = 0;

		return $this->deleteAll($conditions, true);
	}
}
