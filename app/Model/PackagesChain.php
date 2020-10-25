<?php
/**
 * This file is the model file of the application. Used to
 *  manage packages chains.
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
 * The model is used to manage packages chains.
 *
 * @package app.Model
 */
class PackagesChain extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'dependency_id';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt',
		'UpdateModifiedDate',
		'ClearViewCache',
		'ValidationRules',
		'DependencyInfo' => [
			'dependencyModelName' => 'PackageDependency'
		]
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
		'package_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'dependency_id' => [
			'naturalNumber' => [
				'rule' => 'naturalNumber',
				'message' => 'Incorrect foreign key',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
			],
			'selfDependency' => [
				'rule' => ['selfDependency', 'package_id'],
				'message' => 'Chain on self',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
			]
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
				'Attribute.ref_type' => ATTRIBUTE_TYPE_PACKAGE,
				'Attribute.ref_node' => ATTRIBUTE_NODE_CHAIN
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
		]
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
		'PackageDependency' => [
			'className' => 'Package',
			'foreignKey' => 'dependency_id',
			'dependent' => true,
			'fields' => [
				'PackageDependency.id',
				'PackageDependency.enabled',
				'PackageDependency.id_text',
				'PackageDependency.name'
			],
			'order' => ['PackageDependency.name' => 'asc']
		]
	];

/**
 * Return array for render package chains XML elements
 *
 * @param array $data Information of package chains
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($data = []) {
		return $this->getDependsXMLdata($data, 'package-id', 'chain');
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
		$format = __('chain %s of the %s');
		return $this->getDependsNameExt($id, $typeName, $primary, $format);
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
		$label = __('Chain of package');
		return $this->getDependsBreadcrumbInfo($id, $refType, $refNode, $refId, $includeRoot, $label);
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->Package->getParamClearCache();
	}
}
