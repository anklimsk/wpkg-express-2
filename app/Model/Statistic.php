<?php
/**
 * This file is the model file of the application. Used to
 *  manage statistics information.
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
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used to manage statistics information.
 *
 * @package app.Model
 */
class Statistic extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Return list of statistics information
 *
 * @return array Return list of statistics information.
 */
	public function getStaticticsInfo() {
		$result = [];
		$listModels = [
			'Package',
			'Profile',
			'Host',
		];

		foreach ($listModels as $modelName) {
			$objModel = ClassRegistry::init($modelName);
			$label = $objModel->getGroupName();
			$numberAll = $objModel->getNumberOf();
			$conditions = [$objModel->alias . '.enabled' => false];
			$numberDisabled = $objModel->getNumberOf($conditions);
			$controller = $objModel->getControllerName();

			$result[] = compact(
				'label',
				'numberAll',
				'numberDisabled',
				'controller'
			);
		}

		return $result;
	}
}
