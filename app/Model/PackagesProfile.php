<?php
/**
 * This file is the model file of the application. Used to
 *  manage associated packages of profile.
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
 * The model is used to manage associated packages of profile.
 *
 * @package app.Model
 */
class PackagesProfile extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'package_id';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt',
		'ClearViewCache',
		'DependencyInfo' => [
			'mainModelName' => 'Profile',
			'dependencyModelName' => 'Package',
			'mainFieldName' => 'profile_id',
			'dependencyFieldName' => 'package_id',
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
		'profile_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		],
		'installdate' => [
			'rule' => ['custom', '/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)$/'],
			'message' => 'Incorrect installation date',
			'allowEmpty' => true,
			'required' => false,
			'last' => true,
		],
		'uninstalldate' => [
			'rule' => ['custom', '/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)$/'],
			'message' => 'Incorrect uninstallation date',
			'allowEmpty' => true,
			'required' => false,
			'last' => true,
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
				'ref_type' => ATTRIBUTE_TYPE_PROFILE,
				'ref_node' => ATTRIBUTE_NODE_PACKAGE
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
			'conditions' => ['ref_type' => CHECK_PARENT_TYPE_PROFILE],
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
			'dependent' => false,
			'fields' => [
				'Package.id',
				'Package.name',
				'Package.id_text',
				'Package.revision',
				'Package.enabled'
			],
			'order' => ['Package.name' => 'asc']
		],
		'Profile' => [
			'className' => 'Profile',
			'foreignKey' => 'profile_id',
			'dependent' => false,
			'fields' => [
				'Profile.id',
				'Profile.enabled',
				'Profile.id_text',
				'Profile.notes'
			],
			'order' => ['Profile.id_text' => 'asc']
		]
	];

/**
 * Return array for render profile package XML elements
 *
 * @param array $data Information of package dependencies
 * @return array Return array for render XML elements
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($data = []) {
		$extAttributes = [
			'installdate',
			'uninstalldate'
		];
		return $this->getDependsXMLdata($data, 'package-id', 'package', $extAttributes);
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
		$format = __('associated %s of the %s');
		return $this->getDependsNameExt($id, $typeName, $primary, $format, true);
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
		$label = __x('attribute', 'Associated packages');
		return $this->getDependsBreadcrumbInfo($id, $refType, $refNode, $refId, $includeRoot, $label);
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->Profile->getParamClearCache();
	}
}
