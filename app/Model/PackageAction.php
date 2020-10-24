<?php
/**
 * This file is the model file of the application. Used to
 *  manage package actions.
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
App::uses('Hash', 'Utility');

/**
 * The model is used to manage package actions.
 *
 * @package app.Model
 */
class PackageAction extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'command';

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
			'refIdField' => 'package_id'
		],
		'UpdateModifiedDate',
		'MoveExt',
		'TrimStringField',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PACKAGE_ACTION],
		'ClearViewCache',
	];

/**
 * Name of the validation string domain to use when translating validation errors.
 *
 * @var array
 */
	public $validationDomain = 'validation_errors_package_action';

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
		'package_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'action_type_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'command_type_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'include_action_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => true,
			'required' => true,
			'last' => true,
		],
		'command' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'The package action command attribute is invalid.'
		],
		'timeout' => [
			'rule' => 'numeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'The package action timeout attribute is invalid.'
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
		'workdir' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'The package action download target is invalid.'
		],
		'command' => [
			'rule' => ['url', true],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'The package action download URL is invalid.'
		],
		'timeout' => [
			'rule' => 'numeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'The package action timeout attribute is invalid.'
		],
		'expand_url' => [
			'rule' => 'boolean',
			'message' => "The package action's expandURL attribute must be true or false.",
			'last' => true
		],
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'Package' => [
			'className' => 'Package',
			'foreignKey' => 'package_id',
			'dependent' => true,
			'fields' => [
				'Package.id',
				'Package.enabled',
				'Package.id_text',
				'Package.name'
			],
			'order' => ['Package.name' => 'asc']
		],
		'PackageActionType' => [
			'className' => 'PackageActionType',
			'foreignKey' => 'action_type_id',
			'fields' => [
				'PackageActionType.name',
				'PackageActionType.command'
			]
		],
		'IncludeAction' => [
			'className' => 'PackageActionType',
			'foreignKey' => 'include_action_id',
			'fields' => 'IncludeAction.name'
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
				'Attribute.ref_type' => ATTRIBUTE_TYPE_PACKAGE,
				'Attribute.ref_node' => ATTRIBUTE_NODE_ACTION
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
		],
		'ExitCode' => [
			'className' => 'ExitCode',
			'foreignKey' => 'package_action_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => 'ExitCode.code'
		],
		'Check' => [
			'className' => 'Check',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => CHECK_PARENT_TYPE_ACTION],
			'fields' => [
				'Check.ref_type',
				'Check.type',
				'Check.condition',
				'Check.path',
				'Check.value',
				'Check.id',
				'Check.parent_id'
			],
			'order' => ['Check.lft' => 'asc']
		],
	];

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string|false $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
		public function __construct($id = false, $table = null, $ds = null) {
			parent::__construct($id, $table, $ds);

			$intDataType = $this->getTypeIntegerByDS();
			$this->hasMany['ExitCode']['order'] = ['(CASE WHEN ' . $this->ExitCode->alias .
				'.code = \'*\' THEN 0 WHEN ' . $this->ExitCode->alias . '.code = \'any\' THEN 0 ELSE CAST(' .
				$this->ExitCode->alias . '.code AS ' . $intDataType . ') END)' => 'asc'];
		}

/**
 * Returns a list of all events that will fire in the model during it's lifecycle.
 * Add listener callbacks for event `Model.afterUpdateTree`.
 *
 * @return array
 */
	public function implementedEvents() {
		$events = parent::implementedEvents();
		$events['Model.afterUpdateTree'] = ['callable' => 'afterUpdateTree'];

		return $events;
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
 *  - Create validation rules.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		if (!isset($this->data[$this->alias]['action_type_id']) ||
			!isset($this->data[$this->alias]['command_type_id'])) {
			return false;
		}

		return $this->createValidationRules($this->data[$this->alias]['action_type_id'], $this->data[$this->alias]['command_type_id']);
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Set field `parent_id` to Null;
 *  - Remove char CR and LF from command.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['parent_id'] = null;
		if (isset($this->data[$this->alias]['command'])) {
			$this->data[$this->alias]['command'] = str_replace(
				["\r", "\n"],
				'',
				$this->data[$this->alias]['command']
			);
		}

		return true;
	}

/**
 * Create validation rules by type ID and command type ID
 *
 * @param int|string $typeId ID of type for package action
 * @param int|string $commandTypeId ID of command type for 
 *  package action
 * @return bool Return success.
 */
	public function createValidationRules($typeId = null, $commandTypeId = null) {
		if (empty($typeId) || empty($commandTypeId)) {
			return false;
		}

		$this->validate = $this->_validateDefault;
		if ($commandTypeId == ACTION_COMMAND_TYPE_INCLUDE) {
			$this->validator()->remove('command');
			return true;
		}

		if ($typeId != ACTION_TYPE_DOWNLOAD) {
			return true;
		}

		$this->validate = Hash::merge($this->validate, $this->_validateExt);
		return true;
	}

/**
 * Return all package actions for package ID.
 *
 * @param int|string $refId ID of package record
 * @return array|bool Return all checks,
 *  or False on failure.
 */
	public function getPackageActions($refId = null) {
		if (empty($refId)) {
			return false;
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.package_id',
			$this->alias . '.action_type_id',
			$this->alias . '.command_type_id',
			$this->alias . '.include_action_id',
			$this->alias . '.parent_id',
			$this->alias . '.lft',
			$this->alias . '.rght',
			$this->alias . '.command',
			$this->alias . '.timeout',
			$this->alias . '.workdir',
			$this->alias . '.expand_url',
		];
		$conditions = [$this->alias . '.package_id' => $refId];
		$contain = [
			'Check',
			'Check.Attribute' => ['fields' => '*'],
			'ExitCode',
			'ExitCode.ExitcodeRebootType',
			'ExitCode.ExitCodeDirectory',
			'IncludeAction',
			'PackageActionType',
			'Attribute' => ['fields' => '*'],
		];
		$order = [
			$this->alias . '.action_type_id' => 'asc',
			$this->alias . '.lft' => 'asc'
		];

		return $this->find('all', compact('conditions', 'fields', 'contain', 'order'));
	}

/**
 * Return default values of package action
 *
 * @param int|string $refId ID of package record
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of package action.
 */
	public function getDefaultValues($refId = null, $includeModelAlias = true) {
		$defaultValues = [
			'package_id' => $refId,
			'action_type_id' => ACTION_TYPE_INSTALL,
			'command_type_id' => ACTION_COMMAND_TYPE_COMMAND,
			'include_action_id' => null,
			'parent_id' => null,
			'command' => '',
			'timeout' => ACTION_COMMAND_DEFAULT_TIMEOUT,
			'workdir' => '',
			'expand_url' => false
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return list of command types
 *
 * @return array Return list of command types
 */
	public function getListCommandTypes() {
		$result = [
			ACTION_COMMAND_TYPE_COMMAND => __('Command'),
			ACTION_COMMAND_TYPE_INCLUDE => __('Include'),
		];

		return $result;
	}

/**
 * Return list of package actions types for package
 *
 * @param int|string $packageId The ID of the package record.
 * @return array Return list of package actions types
 */
	public function getListActionTypes($packageId = null) {
		$result = [];
		if (empty($packageId)) {
			return $result;
		}

		$fields = [
			$this->alias . '.action_type_id',
			'PackageActionType.name',
		];
		$conditions = [$this->alias . '.package_id' => $packageId];
		$order = [
			$this->alias . '.action_type_id',
		];
		$contain = ['PackageActionType'];

		return $this->find('list', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Verifying list of package actions
 *
 * @param int|string $packageId The ID of the package record.
 * @return array Return list of results verifying list of
 *  package actions
 */
	public function verifyActions($packageId = null) {
		$result = [];
		if (empty($packageId)) {
			return $result;
		}

		$actions = $this->getListActionTypes($packageId);
		if (empty($actions)) {
			return $result;
		}

		foreach ($actions as $actionId => $actionName) {
			if (!$this->setScopeModel($actionId, $packageId)) {
				continue;
			}
			$actionState = $this->verify();
			$result[] = compact('actionId', 'actionName', 'actionState');
		}

		return $result;
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->Package->getParamClearCache();
	}

/**
 * Return object package Model.
 *
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel() {
		return ClassRegistry::init('Package', true);
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'actions';
		return $controllerName;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Package action');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Package actions');

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
		if (empty($id)) {
			return false;
		}
		if (is_array($id)) {
			if (!isset($id[$this->alias]['action_type_id'])) {
				return false;
			}

			$result = $this->PackageActionType->getName($id[$this->alias]['action_type_id']);
		} else {
			$fields = [
				$this->alias . '.include_action_id',
				$this->alias . '.command_type_id',
				$this->alias . '.command',
			];
			$conditions = [$this->alias . '.id' => $id];
			$contain = ['PackageActionType'];
			$data = $this->find('first', compact('conditions', 'fields', 'contain'));
			if (empty($data)) {
				return false;
			}

			$result = $data['PackageActionType']['name'];
		}
		$result = mb_ucfirst(__d('package_action_type', $result));

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
			$result = __('Action(s) %sof the %s', $name, $typeName);
		} else {
			$result = __('action(s) %s%s', $name, $typeName);
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

		if (empty($refId)) {
			$refId = $this->getRefId($id);
		}

		$typeName = $modelType->getFullName($refId, null, null, null, false);
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
		$modelType = $this->getRefTypeModel();
		if (empty($modelType)) {
			return $result;
		}

		if (empty($refId)) {
			$refId = $this->getRefId($id);
		}

		$result = $modelType->getBreadcrumbInfo($refId);
		$link = false;
		if (!empty($refId)) {
			$link = ['action' => 'view', $refId];
		}
		$result[] = $this->createBreadcrumb(null, $link);
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

/**
 * Return array for render package action XML elements
 *
 * @param array $data Information of package actions
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($data = []) {
		$result = [];
		if (empty($data) || !is_array($data)) {
			return $result;
		}

		foreach ($data as $action) {
			$isCommand = $action['PackageActionType']['command'];
			$commandTypeName = $action['PackageActionType']['name'];
			$actionAttribs = [];
			if ($isCommand) {
				$actionAttribs['@type'] = $commandTypeName;
			}
			if ($action['command_type_id'] == ACTION_COMMAND_TYPE_COMMAND) {
				switch ($action['action_type_id']) {
					case ACTION_TYPE_DOWNLOAD:
						$actionAttribs['@url'] = $action['command'];
						$actionAttribs['@target'] = $action['workdir'];
						if (!empty($action['timeout'])) {
							$actionAttribs['@timeout'] = $action['timeout'];
						}
						if ($action['expand_url']) {
							$actionAttribs['@expandURL'] = 'true';
						} else {
							$actionAttribs['@expandURL'] = 'false';
						}
						break;
					default:
						$actionAttribs['@cmd'] = $action['command'];
						if (!empty($action['workdir'])) {
							$actionAttribs['@workdir'] = $action['workdir'];
						}
						if (!empty($action['timeout'])) {
							$actionAttribs['@timeout'] = $action['timeout'];
						}
				}
			} elseif (isset($action['IncludeAction']['name'])) {
				$actionAttribs['@include'] = $action['IncludeAction']['name'];
			} else {
				continue;
			}

			if (isset($action['Attribute'])) {
				$actionAttribs += $this->Attribute->getXMLnodeAttr($action['Attribute']);
			}

			if (isset($action['Check']) && !empty($action['Check'])) {
				$actionAttribs['condition'] = $this->Check->getXMLdata($action['Check']);
			}

			if (isset($action['ExitCode'])) {
				$actionAttribs += $this->ExitCode->getXMLdata($action['ExitCode']);
			}

			if ($isCommand) {
				$result['commands']['command'][] = $actionAttribs;
			} else {
				$result[$commandTypeName][] = $actionAttribs;
			}
		}

		return $result;
	}

/**
 * Get data for autocomplete from constants value
 *
 * @param string $query Query data.
 * @param string $prefix Prefix of constans
 * @param int $limit Limit for find.
 * @return array Return array of data for autocomple.
 */
	protected function _getAutocompleteData($query = null, $prefix = null, $limit = null) {
		$result = [];
		$query = trim($query);
		if (empty($query) || empty($prefix)) {
			return $result;
		}

		if (empty($limit)) {
			$limit = AUTOCOMPLETE_ARRAY_DATA_LIMIT;
		}

		$data = constsVals($prefix);
		$pattern = '/^' . preg_quote($query, '/') . '/ui';
		$result = preg_grep($pattern, $data);
		if (!empty($result) && !empty($limit)) {
			$result = array_slice($result, 0, $limit);
		}

		return $result;
	}

/**
 * Return data for autocomplete
 *
 * @param string $query Query string for autocomplete
 * @param string $type Request type: `switch` or `command`
 * @param int|string $limit Limit for autocomplete data
 * @return array Data for autocomplete.
 */
	public function getAutocomplete($query = null, $type = null, $limit = null) {
		$result = [];
		$query = trim($query);
		if (empty($query)) {
			return $result;
		}

		$type = mb_strtolower($type);
		switch ($type) {
			case 'switch':
				$result = $this->_getAutocompleteData($query, 'SWITCH_', $limit);
				break;
			case 'command':
				$result = $this->_getAutocompleteData($query, 'VARIABLE_COMMAND_', $limit);
				break;
		}

		return $result;
	}
}
