<?php
/**
 * This file is the model file of the application. Used to
 *  manage packages.
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
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to manage packages.
 *
 * @package app.Model
 */
class Package extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'id_text';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'CanDisable',
		'GetXmlInfo',
		'BreadCrumbExt',
		'GetInfo',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PACKAGE],
		'GetNumber' => ['cacheConfig' => CACHE_KEY_STATISTICS_INFO_PACKAGE],
		'GroupAction',
		'ChangeState',
		'GetGraphInfo',
		'GetChartInfo',
		'TemplateData',
		'ValidationRules',
		'ClearViewCache'
	];

/**
 * Array of virtual fields this model has.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#virtualfields
 */
	public $virtualFields = [
		'full_name' => "CONCAT(Package.name, ' (', Package.id_text, ')')"
	];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Package name is invalid.',
			'last' => true
		],
		'id_text' => [
			'alphaNumeric' => [
				'rule' => ['custom', '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]{1}[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}_\-]*$/u'], // '/^[a-z0-9]{1}[a-z0-9_\-]+$/i'
				'required' => true,
				'message' => 'The package id must start with a letter or number and only contain: letters, numbers, underscores, and hyphens.',
				'last' => true
			],
			'uniqueID' => [
				'rule' => 'isUniqueID',
				'required' => true,
				'message' => 'That package already exists.',
				'last' => true
			]
		],
		'revision' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Package revision is invalid.',
			'last' => true
		],
		'priority' => [
			'rule' => 'numeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => "The package's priority attribute is not an integer.",
			'last' => true
		],
		'reboot_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's reboot attribute is invalid.",
			'last' => true
		],
		'execute_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's execute attribute is invalid.",
			'last' => true
		],
		'notify_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's notify attribute is invalid.",
			'last' => true
		],
		'precheck_install_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's precheck-install attribute is invalid.",
			'last' => true
		],
		'precheck_remove_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's precheck-remove attribute is invalid.",
			'last' => true
		],
		'precheck_upgrade_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's precheck-upgrade attribute is invalid.",
			'last' => true
		],
		'precheck_downgrade_id' => [
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => "The package's precheck-downgrade attribute is invalid.",
			'last' => true
		],
		'DependsOn' => [
			'selfDependency' => [
				'rule' => ['selfDependency', 'id'],
				'message' => 'Depended on self',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			],
			'intersectDependency' => [
				'rule' => ['intersectDependency', 'id'],
				'message' => 'Dependency intersection',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			]
		],
		'Includes' => [
			'selfDependency' => [
				'rule' => ['selfDependency', 'id'],
				'message' => 'Included on self',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			],
			'intersectDependency' => [
				'rule' => ['intersectDependency', 'id'],
				'message' => 'Dependency intersection',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			]
		],
		'Chains' => [
			'selfDependency' => [
				'rule' => ['selfDependency', 'id'],
				'message' => 'Chain on self',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			],
			'intersectDependency' => [
				'rule' => ['intersectDependency', 'id'],
				'message' => 'Dependency intersection',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			]
		]
	];

/**
 * Detailed list of hasAndBelongsToMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasandbelongstomany-habtm
 */
	public $hasAndBelongsToMany = [
		'Profile' => [
			'className' => 'Profile',
			'joinTable' => 'packages_profiles',
			'foreignKey' => 'package_id',
			'associationForeignKey' => 'profile_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => [
				'Profile.id',
				'Profile.id_text',
				'Profile.enabled'
			],
			'order' => ['Profile.id_text' => 'asc']
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'Archive' => [
			'className' => 'Archive',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => GARBAGE_TYPE_PACKAGE],
			'fields' => [
				'Archive.id',
				'Archive.ref_type',
				'Archive.ref_id',
				'Archive.revision',
				'Archive.name',
				'Archive.data',
				'Archive.modified'
			],
		],
		'PackageAction' => [
			'className' => 'PackageAction',
			'foreignKey' => 'package_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => [
				'PackageAction.action_type_id',
				'PackageAction.command_type_id',
				'PackageAction.include_action_id',
				'PackageAction.command',
				'PackageAction.timeout',
				'PackageAction.workdir',
				'PackageAction.expand_url',
				'PackageAction.id'
			],
			'order' => [
				'PackageAction.action_type_id' => 'asc',
				'PackageAction.lft' => 'asc'
			]
		],
		'Check' => [
			'className' => 'Check',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => CHECK_PARENT_TYPE_PACKAGE],
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
		'PackagesPackage' => [
			'className' => 'PackagesPackage',
			'foreignKey' => 'package_id',
			'dependent' => true,
			'fields' => [
				'PackagesPackage.id',
				'PackagesPackage.package_id',
				'PackagesPackage.dependency_id'
			]
		],
		'PackagesInclude' => [
			'className' => 'PackagesInclude',
			'foreignKey' => 'package_id',
			'dependent' => true,
			'fields' => [
				'PackagesInclude.id',
				'PackagesInclude.package_id',
				'PackagesInclude.dependency_id'
			]
		],
		'PackagesChain' => [
			'className' => 'PackagesChain',
			'foreignKey' => 'package_id',
			'dependent' => true,
			'fields' => [
				'PackagesChain.id',
				'PackagesChain.package_id',
				'PackagesChain.dependency_id'
			]
		],
		'Variable' => [
			'className' => 'Variable',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => VARIABLE_TYPE_PACKAGE],
			'order' => ['Variable.lft' => 'asc'],
			'fields' => [
				'Variable.id',
				'Variable.ref_type',
				'Variable.name',
				'Variable.value'
			]
		]
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'PackageRebootType' => [
			'className' => 'PackageRebootType',
			'foreignKey' => 'reboot_id',
			'conditions' => '',
			'fields' => 'PackageRebootType.name'
		],
		'PackageExecuteType' => [
			'className' => 'PackageExecuteType',
			'foreignKey' => 'execute_id',
			'conditions' => '',
			'fields' => 'PackageExecuteType.name'
		],
		'PackageNotifyType' => [
			'className' => 'PackageNotifyType',
			'foreignKey' => 'notify_id',
			'conditions' => '',
			'fields' => 'PackageNotifyType.name'
		],
		'PackagePrecheckTypeInstall' => [
			'className' => 'PackagePrecheckType',
			'foreignKey' => 'precheck_install_id',
			'conditions' => '',
			'fields' => 'PackagePrecheckTypeInstall.name'
		],
		'PackagePrecheckTypeRemove' => [
			'className' => 'PackagePrecheckType',
			'foreignKey' => 'precheck_remove_id',
			'conditions' => '',
			'fields' => 'PackagePrecheckTypeRemove.name'
		],
		'PackagePrecheckTypeUpgrade' => [
			'className' => 'PackagePrecheckType',
			'foreignKey' => 'precheck_upgrade_id',
			'conditions' => '',
			'fields' => 'PackagePrecheckTypeUpgrade.name'
		],
		'PackagePrecheckTypeDowngrade' => [
			'className' => 'PackagePrecheckType',
			'foreignKey' => 'precheck_downgrade_id',
			'conditions' => '',
			'fields' => 'PackagePrecheckTypeDowngrade.name'
		],
	];

/**
 * Detailed list of hasOne associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasone
 */
	public $hasOne = [
		'PackagePriority' => [
			'className' => 'PackagePriority',
			'foreignKey' => '',
			'conditions' => ['PackagePriority.value = Package.priority'],
			'fields' => ['PackagePriority.name'],
			'order' => ''
		],
	];

/**
 * Flag of the import process activity
 *
 * @var bool
 */
	protected $_importState = false;

/**
 * Checking the intersection of dependencies
 *
 * @param array $data Data to check
 * @return bool Return True, if no intersection found
 */
	public function intersectDependency($data = null) {
		if (empty($data)) {
			return true;
		}

		$value = reset($data);
		$field = key($data);
		$listDependModels = [
			'DependsOn',
			'Includes',
			'Chains'
		];
		$listProcessModels = array_diff($listDependModels, (array)$field);
		foreach ($listProcessModels as $modelName) {
			if (!isset($this->data[$modelName][$modelName]) ||
				empty($this->data[$modelName][$modelName])) {
				continue;
			}

			$intersectData = array_intersect($value, $this->data[$modelName][$modelName]);
			if (!empty($intersectData)) {
				return false;
			}
		}

		return true;
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Set field `revision` and `priority` to `0` for empty values;
 *  - Reset flag using as template if not set;
 *  - Add package to archive, if revision is changed.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$zeroFields = [
			'revision',
			'priority'
		];
		foreach ($zeroFields as $field) {
			if (isset($this->data[$this->alias][$field])) {
				if (trim($this->data[$this->alias][$field]) === '') {
					$this->data[$this->alias][$field] = 0;
				}
			}
		}

		if (!isset($this->data[$this->alias]['template'])) {
			$this->data[$this->alias]['template'] = false;
		}

		if (isset($this->data[$this->alias]['id']) &&
			isset($this->data[$this->alias]['revision']) &&
			!$this->data[$this->alias]['template'] &&
			!$this->getImportState()) {
			$revision = $this->field('revision');
			if (($revision !== false) && ($this->data[$this->alias]['revision'] != $revision)) {
				$this->Archive->addPackage($this->data[$this->alias]['id']);
			}
		}

		return true;
	}

/**
 * Called after each successful save operation.
 *
 * Actions:
 *  - Clear View cache;
 *  - Set or create variable `%Revision%`.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
		clearCache('wpkg_wpi_config_js');
		$modelSetting = ClassRegistry::init('Setting');
		if ($this->getImportState() || !$modelSetting->getConfig('AutoVarRevision') ||
			!isset($this->data[$this->alias]['revision'])) {
			return;
		}

		$revision = $this->data[$this->alias]['revision'];
		$this->Variable->createVariableAutoRevision($this->id, $revision);
	}

/**
 * Called before every deletion operation.
 *
 * Actions:
 *  - Store data as garbage.
 *
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
 */
	public function beforeDelete($cascade = true) {
		if (!$this->getImportState()) {
			$this->bindModel(
				[
					'hasMany' => [
						'Wpi' => [
							'className' => 'Wpi',
							'foreignKey' => 'package_id',
							'dependent' => true
						]
					]
				],
				false
			);
		}

		$modelGarbage = ClassRegistry::init('Garbage');
		return $modelGarbage->storeData(GARBAGE_TYPE_PACKAGE, $this->id);
	}

/**
 * Called after every deletion operation.
 *
 * Actions:
 *  - Clear cache.
 *
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterdelete
 */
	public function afterDelete() {
		clearCache('wpkg_wpi_config_js');
		if ($this->getImportState()) {
			return;
		}

		$this->unbindModel(
			[
				'hasMany' => [
					'Wpi'
				]
			],
			false
		);
	}

/**
 * Saving package information use transactions.
 *
 * @param array $data Array information package to save.
 * @return bool Success.
 */
	public function savePackage($data = []) {
		$result = true;
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		$this->bindHabtmPackageDependecies(false);
		$result = $this->saveAll($data);
		if ($result) {
			if (!$this->PackagesPackage->Attribute->clearUnusedAttributes(ATTRIBUTE_TYPE_PACKAGE, ATTRIBUTE_NODE_DEPENDS)) {
				$result = false;
			}
			if (!$this->PackagesInclude->Attribute->clearUnusedAttributes(ATTRIBUTE_TYPE_PACKAGE, ATTRIBUTE_NODE_INCLUDE)) {
				$result = false;
			}
			if (!$this->PackagesChain->Attribute->clearUnusedAttributes(ATTRIBUTE_TYPE_PACKAGE, ATTRIBUTE_NODE_CHAIN)) {
				$result = false;
			}
		}
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Temporarily bind an additional new 'reverse' HABTM relationship,
 *  which gives us which packages depend on this package
 *
 * @param bool $reverse Flag of reverse bind.
 * @return bool Success.
 */
	public function bindHabtmPackageDependecies($reverse = false) {
		$dependModelsInfo = [
			'DependsOn' => 'packages_packages',
			'Includes' => 'packages_includes',
			'Chains' => 'packages_chains'
		];
		$foreignKey = 'package_id';
		$associationForeignKey = 'dependency_id';
		if ($reverse) {
			$dependModelsInfo = [
				'InDependencies' => 'packages_packages',
				'InInclusions' => 'packages_includes',
				'InChains' => 'packages_chains'
			];
			$foreignKey = 'dependency_id';
			$associationForeignKey = 'package_id';
		}

		$bindCfg = [];
		$hasAndBelongsToMany = $this->getAssociated('hasAndBelongsToMany');
		foreach ($dependModelsInfo as $dependModel => $dependTable) {
			if (!empty($hasAndBelongsToMany) && in_array($dependModel, $hasAndBelongsToMany)) {
				continue;
			}

			$bindCfg[$dependModel] = [
				'className' => 'PackageDependency',
				'joinTable' => $dependTable,
				'foreignKey' => $foreignKey,
				'associationForeignKey' => $associationForeignKey,
				'unique' => 'keepExisting',
				'fields' => [
					$dependModel . '.id',
					$dependModel . '.enabled',
					$dependModel . '.id_text',
					$dependModel . '.name'
				],
				'order' => [$dependModel . '.name' => 'asc']
			];
		}
		if (empty($bindCfg)) {
			return true;
		}

		return $this->bindModel(['hasAndBelongsToMany' => $bindCfg], false);
	}

/**
 * Return information of package
 *
 * @param int|string $id The ID of the record to read.
 * @param bool $full Flag of inclusion in the result
 *  full information.
 * @return array|bool Return information of package,
 *  or False on failure.
 */
	public function get($id = null, $full = true) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.reboot_id',
			$this->alias . '.execute_id',
			$this->alias . '.notify_id',
			$this->alias . '.precheck_install_id',
			$this->alias . '.precheck_remove_id',
			$this->alias . '.precheck_upgrade_id',
			$this->alias . '.precheck_downgrade_id',
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.name',
			$this->alias . '.id_text',
			$this->alias . '.revision',
			$this->alias . '.priority',
			$this->alias . '.notes',
			$this->alias . '.created',
			$this->alias . '.modified'
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [];
		if ($full) {
			$extContain = [
				'PackagePriority',
				'PackageRebootType',
				'PackageExecuteType',
				'PackageNotifyType',
				'PackagePrecheckTypeInstall',
				'PackagePrecheckTypeRemove',
				'PackagePrecheckTypeUpgrade',
				'PackagePrecheckTypeDowngrade',
				'Check',
				'Check.Attribute' => ['fields' => '*'],
				'PackageAction',
				'PackageAction.Check',
				'PackageAction.Check.Attribute' => ['fields' => '*'],
				'PackageAction.ExitCode',
				'PackageAction.ExitCode.ExitcodeRebootType',
				'PackageAction.ExitCode.ExitCodeDirectory',
				'PackageAction.IncludeAction',
				'PackageAction.PackageActionType',
				'PackageAction.Attribute' => ['fields' => '*'],
				'Variable',
				'Variable.Attribute' => ['fields' => '*'],
				'Variable.Check',
				'Variable.Check.Attribute' => ['fields' => '*'],
				'Profile',
				'InDependencies',
				'InInclusions',
				'InChains',
				'PackagesPackage.PackageDependency',
				'PackagesPackage.Attribute' => ['fields' => '*'],
				'PackagesInclude.PackageDependency',
				'PackagesInclude.Attribute' => ['fields' => '*'],
				'PackagesChain.PackageDependency',
				'PackagesChain.Attribute' => ['fields' => '*'],
			];
			$contain = Hash::merge($contain, $extContain);

			$this->bindHabtmPackageDependecies(true);
		} else {
			$contain = [
				'DependsOn',
				'Includes',
				'Chains'
			];
			$this->bindHabtmPackageDependecies(false);
		}

		$result = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($result) || !$full) {
			return $result;
		}

		$result = $this->PackagesPackage->sortDependencyData($result);
		$result = $this->PackagesInclude->sortDependencyData($result);
		$result = $this->PackagesChain->sortDependencyData($result);

		return $result;
	}

/**
 * Return default values of package
 *
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of package.
 */
	public function getDefaultValues($includeModelAlias = true) {
		$defaultValues = [
			'reboot_id' => PACKAGE_REBOOT_FALSE,
			'execute_id' => PACKAGE_EXECUTE_DEFAULT,
			'notify_id' => PACKAGE_NOTIFY_TRUE,
			'precheck_install_id' => PACKAGE_PRECHECK_ALWAYS,
			'precheck_remove_id' => PACKAGE_PRECHECK_NEVER,
			'precheck_upgrade_id' => PACKAGE_PRECHECK_NEVER,
			'precheck_downgrade_id' => PACKAGE_PRECHECK_NEVER,
			'name' => '',
			'id_text' => '',
			'enabled' => true,
			'template' => false,
			'revision' => 0,
			'priority' => 0,
			'notes' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return data array for XML
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of package.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled packages.
 * @return array Return data array for XML
 */
	public function getAllForXML($id = null, $exportnotes = false, $exportdisabled = false) {
		$conditions = [];
		if (!$exportdisabled) {
			$conditions[$this->alias . '.enabled'] = true;
		}
		if (!empty($id)) {
			$conditions[$this->alias . '.id'] = $id;
		}

		$fields = [
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.name',
			$this->alias . '.id_text',
			$this->alias . '.revision',
			$this->alias . '.priority',
			$this->alias . '.notes',
		];
		if ($exportnotes) {
			$fields[] = $this->alias . '.notes';
		}

		$order = [
			$this->alias . '.priority' => 'desc',
			$this->alias . '.id_text' => 'asc'
		];
		$contain = [
			'PackageRebootType',
			'PackageExecuteType',
			'PackageNotifyType',
			'PackagePrecheckTypeInstall',
			'PackagePrecheckTypeRemove',
			'PackagePrecheckTypeUpgrade',
			'PackagePrecheckTypeDowngrade',
			'Check',
			'Check.Attribute',
			'PackageAction',
			'PackageAction.Check',
			'PackageAction.Check.Attribute',
			'PackageAction.ExitCode',
			'PackageAction.ExitCode.ExitcodeRebootType',
			'PackageAction.IncludeAction',
			'PackageAction.PackageActionType',
			'PackageAction.Attribute',
			'Variable',
			'Variable.Attribute',
			'Variable.Check',
			'Variable.Check.Attribute',
			'PackagesPackage.PackageDependency',
			'PackagesPackage.Attribute',
			'PackagesInclude.PackageDependency',
			'PackagesInclude.Attribute',
			'PackagesChain.PackageDependency',
			'PackagesChain.Attribute'
		];

		return $this->find('all', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return data array for render XML
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportdisable The flag of disable data export to XML.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of package.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled packages.
 * @return array Return data array for render XML
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($id = null, $exportdisable = false, $exportnotes = false, $exportdisabled = false) {
		$baseUrl = Configure::read('App.fullBaseUrl');
		$result = [
			'packages:packages' => [
				'xmlns:packages' => 'http://www.wpkg.org/packages',
				'xmlns:wpkg' => 'http://www.wpkg.org/wpkg',
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'@xsi:schemaLocation' => 'http://www.wpkg.org/packages ' . $baseUrl . '/xsd/packages.xsd'
			]];

		if ($exportdisable) {
			return $result;
		}

		$packages = $this->getAllForXML($id, $exportnotes, $exportdisabled);
		if (empty($packages)) {
			return $result;
		}

		foreach ($packages as $package) {
			if (!$package[$this->alias]['enabled'] && !$exportdisabled) {
				continue;
			}

			$packageAttribs = [
				'@id' => $package[$this->alias]['id_text'],
				'@name' => $package[$this->alias]['name'],
				'@revision' => $package[$this->alias]['revision'],
				'@priority' => $package[$this->alias]['priority'],
				'@reboot' => $package['PackageRebootType']['name'],
				'@notify' => $package['PackageNotifyType']['name'],
				'@execute' => $package['PackageExecuteType']['name'],
				'@precheck-install' => $package['PackagePrecheckTypeInstall']['name'],
				'@precheck-remove' => $package['PackagePrecheckTypeRemove']['name'],
				'@precheck-upgrade' => $package['PackagePrecheckTypeUpgrade']['name'],
				'@precheck-downgrade' => $package['PackagePrecheckTypeDowngrade']['name'],
			];

			if ($packageAttribs['@notify'] === 'true') {
				unset($packageAttribs['@notify']);
			}

			if (empty($packageAttribs['@execute']) ||
				($packageAttribs['@execute'] === 'default')) {
				unset($packageAttribs['@execute']);
			}

			$listPrecheckAttribs = [
				'@precheck-install' => 'always',
				'@precheck-remove' => 'never',
				'@precheck-upgrade' => 'never',
				'@precheck-downgrade' => 'never'
			];
			foreach ($listPrecheckAttribs as $precheckAttribName => $precheckAttribValue) {
				if ($packageAttribs[$precheckAttribName] === $precheckAttribValue) {
					unset($packageAttribs[$precheckAttribName]);
				}
			}

			if (isset($package[$this->alias]['notes']) && !empty($package[$this->alias]['notes'])) {
				$packageAttribs[XML_SPECIFIC_TAG_NOTES] = preg_replace('/[\-]{2,}/', '-', $package[$this->alias]['notes']);
			}

			if (isset($package[$this->alias]['template']) && $package[$this->alias]['template']) {
				$packageAttribs[XML_SPECIFIC_TAG_TEMPLATE] = __('Use as template');
			}

			$xmlItemArray = $packageAttribs;
			if (isset($package['Variable'])) {
				$xmlItemArray += $this->Variable->getXMLdata($package['Variable']);
			}

			if (isset($package['PackagesPackage'])) {
				$xmlItemArray += $this->PackagesPackage->getXMLdata($package['PackagesPackage']);
			}

			if (isset($package['PackagesInclude'])) {
				$xmlItemArray += $this->PackagesInclude->getXMLdata($package['PackagesInclude']);
			}

			if (isset($package['PackagesChain'])) {
				$xmlItemArray += $this->PackagesChain->getXMLdata($package['PackagesChain']);
			}

			if (isset($package['Check'])) {
				$xmlItemArray += $this->Check->getXMLdata($package['Check']);
			}

			if (isset($package['PackageAction'])) {
				$xmlItemArray += $this->PackageAction->getXMLdata($package['PackageAction']);
			}

			if (!$package[$this->alias]['enabled'] && $exportdisabled) {
				$result['packages:packages'][XML_SPECIFIC_TAG_DISABLED]['package'][] = $xmlItemArray;
			} else {
				$result['packages:packages']['package'][] = $xmlItemArray;
			}
		}

		return $result;
	}

/**
 * Return list of packages
 *
 * @param array|null $conditions SQL conditions
 * @return array Return list of packages
 */
	public function getListPackages($conditions = false) {
		return $this->getList($conditions, null, null, 'full_name');
	}

/**
 * Return list of packages exclude one package by ID
 *
 * @param int|string $id The ID of the record to exclude
 * @return array Return list of packages
 */
	public function getListDependencyPackages($id = null) {
		$conditions = [];
		if (!empty($id)) {
			$conditions = [$this->alias . '.id <>' => $id];
		}

		return $this->getListPackages($conditions);
	}

/**
 * Return list of packages revision
 *
 * @return array Return list of packages revision
 */
	public function getListRevisions() {
		$conditions = [];
		$fields = [
			$this->alias . '.id_text',
			$this->alias . '.revision',
		];
		$order = [$this->alias . '.id_text' => 'asc'];
		$recursive = -1;

		return $this->find('list', compact('conditions', 'fields', 'order', 'recursive'));
	}

/**
 * Checking the ability to perform operations `delete` or `disable`
 *
 * @param int|string $id Record ID to check
 * @return bool|string Return True, if possible. False on failure or 
 *  error message if not possible.
 */
	public function checkDisable($id = null) {
		if (empty($id) || !$this->bindHabtmPackageDependecies(true)) {
			return false;
		}

		$bindModelInfo = [
			'InDependencies' => __x('check disable', 'dependencies'),
			'InInclusions' => __x('check disable', 'inclusions'),
			'InChains' => __x('check disable', 'chains'),
		];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.id_text',
			$this->alias . '.name',
		];
		$conditions = [
			$this->alias . '.id' => $id
		];
		$contain = [];
		foreach ($bindModelInfo as $bindModelName => $label) {
			$contain[$bindModelName] = ['conditions' => [$bindModelName . '.enabled' => true]];
		}
		$data = $this->find('first', compact('fields', 'conditions', 'contain'));
		if (empty($data)) {
			return false;
		}

		$result = '';
		foreach ($bindModelInfo as $bindModelName => $dependName) {
			if (!isset($data[$bindModelName]) || empty($data[$bindModelName])) {
				continue;
			}

			$listPackages = '<ul>';
			foreach ($data[$bindModelName] as $package) {
				$listPackages .= '<li>' . h($package['name']) . ' (' . h($package['id_text']) . ')</li>';
			}
			$listPackages .= '</ul>';
			$result .= __("The package '%s' cannot be deleted or disabled because it is exists in %s for the following packages: %s",
				$data[$this->alias]['name'] . ' (' . $data[$this->alias]['id_text'] . ')',
				$dependName,
				$listPackages
			);
		}
		if (empty($result)) {
			return true;
		}

		return $result;
	}

/**
 * Return list of profiles that include a package by ID
 *
 * @param int|string $id Record ID to retrieve list
 * @return array|bool Return list of profiles, or False
 *  on failure.
 */
	public function getListProfiles($id = null) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.reboot_id',
			$this->alias . '.execute_id',
			$this->alias . '.notify_id',
			$this->alias . '.precheck_install_id',
			$this->alias . '.precheck_remove_id',
			$this->alias . '.precheck_upgrade_id',
			$this->alias . '.precheck_downgrade_id',
			$this->alias . '.id_text',
			$this->alias . '.name',
			$this->alias . '.revision',
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'Profile',
		];

		return $this->find('first', compact('conditions', 'fields', 'contain'));
	}

/**
 * Return download name from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @param bool $isFullData Flag of full data
 * @return string Return download name
 */
	public function getDownloadName($xmlDataArray = [], $isFullData = false) {
		$nameXpath = $this->getNameAttributeXpath();
		return $this->getDownloadNameFromXml($xmlDataArray, $nameXpath, $isFullData);
	}

/**
 * Return name from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @return string Return name
 */
	public function getNameFromXml($xmlDataArray = []) {
		$nameXpath = $this->getNameAttributeXpath();
		return $this->getAttributeValueFromXml($xmlDataArray, $nameXpath);
	}

/**
 * Return Xpath for data of packages
 *
 * @return string Return Xpath
 */
	public function getDataXpath() {
		$dataXpath = 'packages:packages.package';
		return $dataXpath;
	}

/**
 * Return value for `revision` attribute of package
 *  from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @return string Return revision
 */
	public function getRevisionFromXml($xmlDataArray = []) {
		$dataXpath = $this->getDataXpath();
		$revisionXpath = $dataXpath . '.0.@revision';
		return $this->getAttributeValueFromXml($xmlDataArray, $revisionXpath);
	}

/**
 * Return Xpath for `id` attribute of package
 *
 * @return string Return Xpath
 */
	public function getIdAttributeXpath() {
		$dataXpath = $this->getDataXpath();
		$idXpath = $dataXpath . '.0.@id';
		return $idXpath;
	}

/**
 * Return Xpath for `name` attribute of package
 *
 * @return string Return Xpath
 */
	public function getNameAttributeXpath() {
		$dataXpath = $this->getDataXpath();
		$nameXpath = $dataXpath . '.0.@name';
		return $nameXpath;
	}

/**
 * Return Xpath for `name` attribute of package
 *
 * @return string Return Xpath
 */
	public function getAdditionalAttributeXpath() {
		return $this->getNameAttributeXpath();
	}

/**
 * Return Xpath for `template` element of package
 *
 * @return string Return Xpath
 */
	public function getTemplateElementXpath() {
		$dataXpath = $this->getDataXpath();
		$templateXpath = $dataXpath . '.0.template';
		return $templateXpath;
	}

/**
 * Return Xpath for `notes` element of package
 *
 * @return string Return Xpath
 */
	public function getNotesElementXpath() {
		$dataXpath = $this->getDataXpath();
		$notesXpath = $dataXpath . '.0.notes';
		return $notesXpath;
	}

/**
 * Return label of additional attribute for input in form.
 *
 * @return string Return label of additional attribute.
 */
	public function getLabelAdditAttrib() {
		$label = __('Name');
		return $label;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Package');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Packages');

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
		$name = $id;
		if (is_array($id) || ctype_digit((string)$id)) {
			$name = (string)$this->getName($id);
		}
		if (empty($name)) {
			return false;
		}
		if ($primary) {
			$result = __("Package '%s'", $name);
		} else {
			$result = __("package '%s'", $name);
		}

		return $result;
	}

/**
 * Return full name of data.
 *
 * @return string Return full name of data.
 */
	public function getFullDataName() {
		$result = __('full data of packages');
		return $result;
	}

/**
 * Remove associated information.
 *
 * @param int|string $id Record ID to remove associated information.
 * @return bool Success
 */
	protected function _removeAssocData($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [
			$this->Check->alias . '.ref_id' => $id,
			$this->Check->alias . '.ref_type' => CHECK_PARENT_TYPE_PACKAGE
		];
		if (!$this->Check->setScopeModel(CHECK_PARENT_TYPE_PACKAGE, $id) ||
			!$this->Check->deleteAll($conditions, true, false)) {
			return false;
		}
		$conditions = [
			$this->Variable->alias . '.ref_id' => $id,
			$this->Variable->alias . '.ref_type' => VARIABLE_TYPE_PACKAGE
		];
		if (!$this->Variable->setScopeModel(VARIABLE_TYPE_PACKAGE, $id) ||
			!$this->Variable->deleteAll($conditions, true, false)) {
			return false;
		}
		$dependModels = [
			'PackagesPackage',
			'PackagesInclude',
			'PackagesChain'
		];
		foreach ($dependModels as $dependModelName) {
			$conditions = [
				$this->$dependModelName->alias . '.package_id' => $id
			];
			if (!$this->$dependModelName->deleteAll($conditions, true, false)) {
				return false;
			}
		}
		$actions = $this->PackageAction->getListActionTypes($id);
		if (empty($actions)) {
			return true;
		}
		foreach ($actions as $actionId => $actionName) {
			if (!$this->PackageAction->setScopeModel($actionId, $id)) {
				return false;
			}
			$conditions = [$this->PackageAction->alias . '.package_id' => $id];
			if (!$this->PackageAction->deleteAll($conditions, true, false)) {
				return false;
			}
		}

		return true;
	}

/**
 * Remove associated information.
 *
 * @param int|string $id Record ID to remove associated information.
 * @return bool Success
 */
	public function removeAssocData($id = null) {
		if (empty($id)) {
			return false;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = $this->_removeAssocData($id);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Return flag of the import process activity.
 *
 * @return bool Return flag of the import process activity
 */
	public function getImportState() {
		return $this->_importState;
	}

/**
 * Set flag of the import process activity.
 *
 * @param bool $state State of flag for set
 * @return void
 */
	public function setImportState($state = false) {
		$this->_importState = (bool)$state;
	}

/**
 * Return data array for graph
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @return array|bool Return data array for graph, or False
 *  on failure.
 */
	public function getAllForGraph($id = null) {
		if (empty($id)) {
			return false;
		}

		$this->bindHabtmPackageDependecies(false);
		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.' . $this->displayField
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'DependsOn',
			'Includes',
			'Chains',
		];

		return $this->find('first', compact('conditions', 'fields', 'contain'));
	}

/**
 * Return the node ID of the graph by name
 *
 * @param string $name Name of graph node
 * @return string Return the node ID
 */
	public function getIdNode($name = null) {
		$result = parent::getIdNode($name);
		$initModelName = $this->getInitModelName();
		if ($initModelName === $this->name) {
			return $result;
		}

		$result .= '_' . uniqid();
		return $result;
	}

/**
 * Return full data for build a dependency graph.
 *
 * @param int|string $id Record ID to retrieve data
 * @param string $parent Name of parent graph node
 * @return array Return full data for build a dependency graph.
 */
	public function getGraphDataFull($id = null, $parent = null) {
		$result = [];
		if (empty($id) || empty($parent)) {
			return $result;
		}

		$profiles = $this->getListProfiles($id);
		if (empty($profiles)) {
			return $result;
		}

		$level = 1;
		$deepLimit = $this->Profile->getLimitGraphDeep();
		$edgeLabel = __('Present in');
		foreach ($profiles['Profile'] as $profile) {
			$node = $this->Profile->getGraphDataNode($profile, $parent, null, $edgeLabel);
			$result[] = $node;
			$parentNode = $node['name'];
			if (empty($parentNode)) {
				continue;
			}
			$this->Profile->getGraphDataHostRec($result, $profile['id'], $parentNode, $level, $deepLimit);
		}

		return $result;
	}

/**
 * Return information about data dependencies.
 *
 * @return array Return information about data dependencies
 */
	public function getGraphDependencyInfo() {
		$result = [
			'DependsOn' => ['dependLabel' => __x('dependency', 'Depends on'), 'arrowhead' => 'normal'],
			'Includes' => ['dependLabel' => __x('dependency', 'Includes on'), 'arrowhead' => 'open'],
			'Chains' => ['dependLabel' => __x('dependency', 'Chain on'), 'arrowhead' => 'empty']
		];

		return $result;
	}

/**
 * Return data array for render chart
 *
 * @param int|string $id The ID of the package to retrieve data.
 * @return array Return data array for render chart
 */
	public function getChartData($id = null) {
		$result = [];
		if (empty($id)) {
			return $result;
		}

		$modelReport = ClassRegistry::init('Report');
		$versionInfo = $modelReport->getRevisionInfo($id);
		if (empty($versionInfo)) {
			return $result;
		}
		$labels = [];
		$packageName = $this->getName($id);
		$dataset = [
			'label' => __("Versions of package '%s'", $packageName),
			'data' => []
		];

		foreach ($versionInfo as $versionInfoItem) {
			$labels[] = $versionInfoItem[$modelReport->alias]['revision'];
			$dataset['data'][] = (int)$versionInfoItem[0]['number'];
			$dataset['backgroundColor'][] = $this->getRandomColor();
		}
		$datasets = [$dataset];
		$result = compact('labels', 'datasets');

		return $result;
	}

/**
 * Return title for chart.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return string Return title for chart.
 */
	public function getChartTitle($refType = null, $refId = null) {
		$result = '';
		$packageName = $this->getName($refId);
		if (empty($packageName)) {
			return $result;
		}
		$result = __("Chart of the installed versions of the package '%s'", $packageName);

		return $result;
	}

/**
 * Return the URL to use when clicking on the chart element.
 *
 * @param int|string $refType ID of type
 * @param int|string $refId Record ID for generating chart
 * @return array Return array URL.
 */
	public function getChartClickUrl($refType = null, $refId = null) {
		$result = [];
		if (empty($refId)) {
			return $result;
		}

		$data = [
			'data' => [
				'FilterData' => [
					[
						'Report' => [
							'package_id' => $refId,
							'revision' => ''
						]
					]
				]
			]
		];
		$result = [
			'controller' => 'reports',
			'action' => 'index',
			'?' => $data
		];

		return $result;
	}
}
