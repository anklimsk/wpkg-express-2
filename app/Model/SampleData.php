<?php
/**
 * This file is the model file of the application. Used to
 *  manage sample data.
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
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to manage sample data.
 *
 * @package app.Model
 */
class SampleData extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'name';

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
				'message' => 'The data file name is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The data file name already exists.',
				'last' => true
			],
		],
		'hash' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Hash is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'Hash is already exists.',
				'last' => true
			],
		],
	];

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Convert file name to lowercase.
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
 * Return list of MD5 hash data
 *
 * @param string|null $fileName File name to retrieve data
 * @param int|string|null $limit Limit of list
 * @return array Return list of MD5 hash data
 */
	public function getListMD5hash($fileName = null, $limit = null) {
		$result = [];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.name',
			$this->alias . '.hash',
		];
		$conditions = [];
		if (!empty($fileName)) {
			$conditions[$this->alias . '.name'] = $fileName;
		}
		$order = [$this->alias . '.name' => 'asc'];
		$recursive = -1;

		$data = $this->find('all', compact('conditions', 'fields', 'order', 'recursive', 'limit'));
		if (empty($data)) {
			return $result;
		}

		$result = Hash::combine($data, '{n}.' . $this->alias . '.name', '{n}.' . $this->alias);

		return $result;
	}

/**
 * Install sample data from XML file 
 * 
 * @param Import $modelImport Import Model
 * @param string|null $xmlFile File to install
 * @param array $listMD5hash Cache of MD5 data
 * @return bool Success
 */
	protected function _importFile(Import $modelImport, $xmlFile = null, $listMD5hash = []) {
		if (empty($xmlFile)) {
			return false;
		}

		$oFile = new File($xmlFile, false);
		if (!$oFile->exists()) {
			return false;
		}

		$name = mb_strtolower($oFile->name());
		$hash = $oFile->md5();

		$dataToSave = [
			$this->alias => compact('name', 'hash')
		];
		if (isset($listMD5hash[$name])) {
			if ($listMD5hash[$name]['hash'] === $hash) {
				return true;
			}

			$dataToSave[$this->alias]['id'] = $listMD5hash[$name]['id'];
		}

		$this->clear();
		if (!$modelImport->importXml($xmlFile) || !$this->save($dataToSave)) {
			return false;
		}

		return true;
	}

/**
 * Install sample data from XML files
 * 
 * @return bool Success
 */
	public function installSampleData() {
		$wwwRoot = Configure::read('App.www_root');
		if (empty($wwwRoot)) {
			return false;
		}

		$xmlDirPath = $wwwRoot . 'files' . DS . 'XML' . DS;
		if (!file_exists($xmlDirPath)) {
			return false;
		}

		$orderList = [
			'CONFIG',
			'PACKAGE',
			'PROFILE',
			'HOST',
			''
		];
		$oXmlDir = new Folder($xmlDirPath, false, false);
		list(, $xmlFiles) = $oXmlDir->read(true, false, true);
		if (empty($xmlFiles)) {
			return true;
		}

		$result = true;
		$modelImport = ClassRegistry::init('Import');
		$listMD5hash = $this->getListMD5hash();
		foreach ($orderList as $orderItem) {
			$fileNamePostfix = 'DATA';
			if (!empty($orderItem)) {
				$fileNamePostfix = 'TEMPLATE';
			}
			$pcrePattern = '/' . $orderItem . '.*_' . $fileNamePostfix . '\.xml$/';
			$xmlFilesType = preg_grep($pcrePattern, $xmlFiles);
			if (empty($xmlFilesType)) {
				continue;
			}

			foreach ($xmlFilesType as $xmlFile) {
				if (!$this->_importFile($modelImport, $xmlFile, $listMD5hash)) {
					$result = false;
				}
			}
		}

		return $result;
	}
}
