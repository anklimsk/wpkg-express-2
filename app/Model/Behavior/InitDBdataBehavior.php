<?php
/**
 * This file is the behavior file of the application. Is used to
 *  initialize database values from constants.
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
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');

/**
 * The behavior is used to initialize database values from constants.
 *
 * @package app.Model.Behavior
 */
class InitDBdataBehavior extends ModelBehavior {

/**
 * Defaults
 *
 * @var array
 */
	protected $_defaults = [
		'constantPrefix' => null,
		'toLowerCase' => false,
		'toUpperCase' => false,
	];

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if invalid constant prefix
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config + $this->_defaults;
		if (empty($this->settings[$model->alias]['constantPrefix'])) {
			throw new InternalErrorException(__('Invalid constant prefix'));
		}
	}

/**
 * Initialization of database table the initial values
 *  from constants.
 *
 * @param Model $model Model using this behavior
 * @return bool Success
 */
	public function initDbTable(Model $model) {
		$dataToSave = [];
		$mbFuncName = null;
		if ($this->settings[$model->alias]['toLowerCase']) {
			$mbFuncName = 'mb_strtolower';
		} elseif ($this->settings[$model->alias]['toUpperCase']) {
			$mbFuncName = 'mb_strtoupper';
		}
		$prefix = $this->settings[$model->alias]['constantPrefix'];
		$constantValues = constsToWords($prefix);
		foreach ($constantValues as $constantId => $constantName) {
			if (!empty($mbFuncName)) {
				$constantName = $mbFuncName($constantName);
			}

			$dataToSave[] = [
				$model->alias => [
					'id' => $constantId,
					'name' => $constantName,
				]
			];
		}

		return (bool)$model->saveAll($dataToSave);
	}

}
