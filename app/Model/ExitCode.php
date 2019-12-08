<?php
/**
 * This file is the model file of the application. Used to
 *  manage package action exit codes.
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
 * @package app.Model
 */

App::uses('AppModel', 'Model');

/**
 * The model is used to manage package action exit codes.
 *
 * @package app.Model
 */
class ExitCode extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'code';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'TrimStringField',
		'BreadCrumbExt',
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
		'reboot_id' => [
			'checkRange' => [
				'rule' => ['checkRange', 'EXITCODE_REBOOT_', false],
				'message' => 'Incorrect foreign key',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
			],
		],
		'code' => [
			'isValidCode' => [
				'rule' => ['custom', '/^(?:any|\-?\d+|\*)$/'],
				'required' => true,
				'message' => "Attribute the exit code must be an integer or a string of 'any' or the symbol *.",
				'last' => true
			],
			'isUniqueCode' => [
				'rule' => 'isUniqueCode',
				'required' => true,
				'message' => "The exit code's code already exists for this package action.",
				'last' => true
			],
		],
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'PackageAction' => ['className' => 'PackageAction',
			'foreignKey' => 'package_action_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'ExitcodeRebootType' => [
			'className' => 'ExitcodeRebootType',
			'foreignKey' => 'reboot_id',
			'conditions' => '',
			'fields' => 'ExitcodeRebootType.name'
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'ExitCodeDirectory' => [
			'className' => 'ExitCodeDirectory',
			'foreignKey' => '',
			'conditions' => '',
			'order' => '',
			'dependent' => false
		]
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

		$db = $this->getDataSource();
		$modelExitCodeDirectory = $this->ExitCodeDirectory;
		$intDataType = $this->getTypeIntegerByDS();
		$alias = $modelExitCodeDirectory->alias;
		$table = $db->fullTableName($modelExitCodeDirectory);
		$conditions = '';
		$fields = [
			$alias . '.description'
		];
		$fields = $db->fields($modelExitCodeDirectory, $alias, $fields, true);
		$joins = [
			[
				'table' => $db->fullTableName($this),
				'alias' => $this->alias,
				'type' => 'INNER',
				'conditions' => [
					'AND' => [
						$this->alias . '.' . $this->primaryKey . ' = {$__cakeID__$}',
						$this->alias . '.code NOT IN (\'any\', \'*\')',
						$alias . '.code = CAST(' . $this->alias . '.code AS ' . $intDataType . ')'
					]
				]
			]
		];
		$queryData = compact('fields', 'table', 'alias', 'conditions', 'joins');
		$query = $db->buildStatement($queryData, $modelExitCodeDirectory);
		$this->hasMany['ExitCodeDirectory']['finderQuery'] = $query;
	}

/**
 * Returns False if field passed match any of their matching values.
 *
 * @param array $data Field/value pairs to search
 * @return bool False if any records matching a field are found
 */
	public function isUniqueCode($data = null) {
		if (empty($data) || !isset($this->data[$this->alias]['package_action_id'])) {
			return false;
		}

		$value = strtolower(trim((string)array_shift($data)));
				$conditions = [
			$this->alias . '.package_action_id' => $this->data[$this->alias]['package_action_id']
		];

		$anyValues = ['*', 'any'];
		if (in_array($value, $anyValues)) {
			$value = $anyValues;
		}
		$conditions[$this->alias . '.code'] = $value;

		if (!empty($this->id)) {
			$conditions[$this->alias . '.' . $this->primaryKey . ' !='] = $this->id;
		}
		$recursive = -1;

		return !$this->find('count', compact('conditions', 'recursive'));
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Convert exit code to lowercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['code'] = mb_strtolower($this->data[$this->alias]['code']);

		return true;
	}

/**
 * Return default values of exit code
 *
 * @param int|string $refId The ID of the package action.
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of exit code.
 */
	public function getDefaultValues($refId = null, $includeModelAlias = true) {
		$defaultValues = [
			'package_action_id' => $refId,
			'reboot_id' => EXITCODE_REBOOT_FALSE,
			'code' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return information of exit codes for package action
 *
 * @param int|string $refId The ID of the package action.
 * @return array|bool Return information of exit codes,
 *  or False on failure.
 */
	public function getExitCodes($refId = null) {
		if (empty($refId)) {
			return false;
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.package_action_id',
			$this->alias . '.reboot_id',
			$this->alias . '.code',
		];
		$conditions = [$this->alias . '.package_action_id' => $refId];
		$contain = [
			'PackageAction',
			'ExitcodeRebootType',
			'ExitCodeDirectory'
		];
		$intDataType = $this->getTypeIntegerByDS();
			$order = [
				'(CASE WHEN ' . $this->alias . '.code = \'*\' THEN 0 WHEN ' .
					$this->alias . '.code = \'any\' THEN 0 ELSE CAST(' .
					$this->alias . '.code AS ' . $intDataType . ') END)' => 'asc'
			];

		return $this->find('all', compact('conditions', 'fields', 'contain', 'order'));
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->PackageAction->Package->getParamClearCache();
	}

/**
 * Return object package action Model.
 *
 * @return object|bool Return object Model,
 *  or False on failure.
 */
	public function getRefTypeModel() {
		return ClassRegistry::init('PackageAction', true);
	}

/**
 * Return ID of the package action by the record ID
 *
 * @param int|string $id ID of record
 *  for retrieving package action ID
 * @return string|bool Return package action ID,
 *  or False on failure.
 */
	public function getRefId($id = null) {
		if (empty($id)) {
			return false;
		}

		$this->id = $id;
		return $this->field('package_action_id');
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Exit code');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Exit codes');

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
			$result = __('Exit codes %sof the %s', $name, $typeName);
		} else {
			$result = __('exit codes %s%s', $name, $typeName);
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

		$result = $modelType->getBreadcrumbInfo($refId);
		$link = ['action' => 'view', $refId];
		$result[] = $this->createBreadcrumb(null, $link);
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

/**
 * Return array for render exit code XML elements
 *
 * @param array $data Information of exit codes
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($data = []) {
		$result = [];
		if (empty($data) || !is_array($data)) {
			return $result;
		}

		foreach ($data as $exitcode) {
			$exitCodeAttribs = ['@code' => $exitcode['code']];
			if (isset($exitcode['ExitcodeRebootType']['name']) &&
				($exitcode['ExitcodeRebootType']['name'] !== 'null')) {
				$exitCodeAttribs['@reboot'] = $exitcode['ExitcodeRebootType']['name'];
			}
			$result['exit'][] = $exitCodeAttribs;
		}

		return $result;
	}
}
