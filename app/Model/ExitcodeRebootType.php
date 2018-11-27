<?php
/**
 * This file is the model file of the application. Used to
 *  manage reboot types for exit codes.
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
 * The model is used to manage reboot types for exit codes.
 *
 * @package app.Model
 */
class ExitcodeRebootType extends AppModel {

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
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_EXITCODE_REBOOT_TYPE],
		'InitDBdata' => [
			'constantPrefix' => 'EXITCODE_REBOOT_',
			'toLowerCase' => true
		]
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
				'message' => 'The reboot type is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The reboot type already exists.',
				'last' => true
			],
		]
	];

/**
 * Initialization of database table the initial values
 *
 * @return bool Success
 */
	public function initDbTable() {
		return parent::initDbTable();
	}

/**
 * Return list of reboot types
 *
 * @return array Return list of reboot types
 */
	public function getListRebootTypes() {
		$order = [$this->alias . '.id' => 'asc'];

		return $this->getList(null, 'exit_code_reboot', $order, null, true);
	}

}
