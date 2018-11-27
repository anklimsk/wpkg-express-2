<?php
/**
 * This file is the model file of the application. Used to
 *  manage package notify types.
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
 * The model is used to manage package notify types.
 *
 * @package app.Model
 */
class PackageNotifyType extends AppModel {

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
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PACKAGE_NOTIFY_TYPE],
		'InitDBdata' => [
			'constantPrefix' => 'PACKAGE_NOTIFY_',
			'toLowerCase' => true
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
		'name' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The notify type is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The notify type already exists.',
				'last' => true
			],
		]
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Convert notify type to lowercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['name'] = mb_strtolower($this->data[$this->alias]['name']);

		return true;
	}

/**
 * Initialization of database table the initial values
 *
 * @return bool Success
 */
	public function initDbTable() {
		return parent::initDbTable();
	}

/**
 * Return list of package notify types
 *
 * @return array Return list of package notify types
 */
	public function getListPackageNotifyTypes() {
		return $this->getList(null, 'package_notify', null, null, true);
	}
}
