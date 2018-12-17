<?php
/**
 * This file is the model file of the application. Used to
 *  manage report hosts.
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
 * @package app.Model
 */

App::uses('AppModel', 'Model');

/**
 * The model is used to manage report hosts.
 *
 * @package app.Model
 */
class ReportHost extends AppModel {

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
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_REPORT_HOST],
		'BreadCrumbExt'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The host name is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The host name already exists.',
				'last' => true
			],
		],
		'date' => [
			'rule' => ['datetime', 'ymd'],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Report date is invalid.'
		],
		'hash' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Hash is invalid.'
		],
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
				'ref_type' => ATTRIBUTE_TYPE_HOST,
				'ref_node' => ATTRIBUTE_NODE_REPORT
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
	];

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Convert host name to uppercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['name'] = mb_strtoupper($this->data[$this->alias]['name']);

		return true;
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
		$name = (string)$this->getName($id);
		if (!empty($name)) {
			$name = "'" . $name . "'";
		}
		$result = __('Host %s of report', $name);

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
		return $this->getNameExt($id, null, $primary);
	}

/**
 * Return list of MD5 hash reports
 *
 * @param string|null $hostName Host name to retrieve data
 * @param int|string|null $limit Limit of list
 * @return array Return list of MD5 hash reports
 */
	public function getListMD5hash($hostName = null, $limit = null) {
		$result = [];
		$fields = [
			$this->alias . '.name',
			$this->alias . '.hash',
		];
		$conditions = [];
		if (!empty($hostName)) {
			$conditions[$this->alias . '.name'] = $hostName;
		}
		$order = [$this->alias . '.name' => 'asc'];
		$recursive = -1;

		$data = $this->find('list', compact('conditions', 'fields', 'order', 'recursive', 'limit'));
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $key => $value) {
			$key = mb_strtolower($key);
			$result[$key] = $value;
		}

		return $result;
	}

/**
 * Remove host attributes.
 *
 * @param int|string $refId Record ID of host to remove
 *  attributes.
 * @return bool Success
 */
	public function removeHostAttributes($refId = null) {
		$conditions = [
			$this->Attribute->alias . '.ref_type' => ATTRIBUTE_TYPE_HOST,
			$this->Attribute->alias . '.ref_node' => ATTRIBUTE_NODE_REPORT
		];
		if (!empty($refId)) {
			$conditions[$this->Attribute->alias . '.ref_id'] = $refId;
		}

		return $this->Attribute->deleteAll($conditions, false, false);
	}

/**
 * Remove hosts without report records
 *
 * @return bool Success
 */
	public function clearUnusedHosts() {
		$bindCfg = [
			'hasOne' => [
				'Report' => [
					'className' => 'Report',
					'foreignKey' => 'host_id',
					'dependent' => false
				],
			]
		];
		$this->bindModel($bindCfg, true);
		$conditions = ['Report.id' => null];
		$contain = ['Report'];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.id',
		];
		$listHostId = $this->find('list', compact('conditions', 'fields', 'contain'));
		if (empty($listHostId)) {
			return true;
		}

		$this->recursive = -1;
		$conditions = [$this->alias . '.id' => $listHostId];
		if (!$this->deleteAll($conditions, false)) {
			return false;
		}

		return $this->removeHostAttributes($listHostId);
	}

}
