<?php
/**
 * This file is the model file of the application. Used to
 *  get the xml download list.
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
 * The model is used to get the xml download list.
 *
 * @package app.Model
 */
class Download extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt',
		'GetList' => ['cacheConfig' => 'default'],
	];

/**
 * Return list of controllers for download XML
 *
 * @return array Return list of controllers
 */
	public function getListDownloads() {
		return $this->getListDataFromConstant('DOWNLOAD_XML_LIST_ITEM_', 'download_xml');
	}

/**
 * Return list of controllers for export XML
 *
 * @return array Return list of controllers
 */
	public function getListExports() {
		return $this->getListDataFromConstant('EXPORT_XML_LIST_ITEM_', 'download_xml');
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Downloading XML files');

		return $result;
	}
}
