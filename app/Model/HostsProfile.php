<?php
/**
 * This file is the model file of the application. Used to
 *  manage additional associated profiles of host.
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
 * The model is used to manage additional associated
 *  profiles of host.
 *
 * @package app.Model
 */
class HostsProfile extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'profile_id';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt',
		'ClearViewCache'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'host_id' => [
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
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'Host' => [
			'className' => 'Host',
			'foreignKey' => 'host_id',
			'dependent' => false,
			'fields' => [
				'Host.id',
				'Host.enabled',
				'Host.id_text'
			],
			'order' => ['Host.id_text' => 'asc']
		],
		'Profile' => [
			'className' => 'Profile',
			'foreignKey' => 'profile_id',
			'dependent' => false,
			'fields' => [
				'Profile.id',
				'Profile.enabled',
				'Profile.id_text'
			],
			'order' => ['Profile.id_text' => 'asc']
		]
	];

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
		if (is_array($id)) {
			if (!isset($id[$this->alias]['host_id']) || !isset($id[$this->alias]['profile_id'])) {
				return false;
			}
			$data = $id;
		} else {
			$data = $this->get($id);
			if (empty($data)) {
				return false;
			}
		}
		$hostId = $data[$this->alias]['host_id'];
		$profileId = $data[$this->alias]['profile_id'];
		$hostName = $this->Host->getFullName($hostId, null, null, null, false);
		$profileName = $this->Profile->getFullName($profileId, null, null, null, false);
		if (empty($hostName)) {
			return false;
		}
		if (empty($profileName)) {
			$profileName = __('profile');
		}

		$result = __('associated %s of the %s', $profileName, $hostName);
		if ($primary) {
			$result = mb_ucfirst($result);
		}
		return $result;
	}

/**
 * Return parameters for clearCache
 *
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache() {
		return $this->Host->getParamClearCache();
	}
}
