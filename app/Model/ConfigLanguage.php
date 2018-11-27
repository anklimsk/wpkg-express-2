<?php
/**
 * This file is the model file of the application. Used to
 *  manage languages block in configuration of WPKG.
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
 * The model is used to manage languages block in
 *  configuration of WPKG.
 *
 * @package app.Model
 */
class ConfigLanguage extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'lcid';

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'lcid' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The language code is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The language code already exists.',
				'last' => true
			],
		],
		'notifyUserStart' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Message is invalid.',
				'last' => true
			],
		],
		'notifyUserStop' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Message is invalid.',
				'last' => true
			],
		],
		'notifyUserFail' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Message is invalid.',
				'last' => true
			],
		],
		'notifyUserReboot' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Message is invalid.',
				'last' => true
			],
		]
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Convert `lcid` to lowercase.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['lcid'] = mb_strtolower($this->data[$this->alias]['lcid']);

		return true;
	}

/**
 * Return data array for XML
 *
 * @return array Return data array for XML
 */
	public function getAllForXML() {
		$conditions = [];
		$fields = [
			$this->alias . '.lcid',
			$this->alias . '.notifyUserStart',
			$this->alias . '.notifyUserStop',
			$this->alias . '.notifyUserFail',
			$this->alias . '.notifyUserReboot'
		];
		$order = [$this->alias . '.lcid' => 'asc'];
		$recursive = -1;

		return $this->find('all', compact('conditions', 'fields', 'order', 'recursive'));
	}

/**
 * Return data array for render XML
 *
 * @return array Return data array for render XML
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata() {
		$result = [];
		$languages = $this->getAllForXML();
		if (empty($languages)) {
			return $result;
		}

		foreach ($languages as $language) {
			$xmlItemArray = [];
			if (isset($language[$this->alias]['lcid'])) {
				$xmlItemArray['@lcid'] = $language[$this->alias]['lcid'];
				unset($language[$this->alias]['lcid']);
			}
			foreach ($language[$this->alias] as $field => $value) {
				$xmlItemArray['string'][] = [
					'@id' => $field,
					'@' => $value
				];
			}
			$result['language'][] = $xmlItemArray;
		}

		return $result;
	}

/**
 * Deleting global WPKG configuration languages.
 *
 * @return bool Success.
 */
	public function deleteConfigurationLanguages() {
		$dataSource = $this->getDataSource();
		return $dataSource->truncate($this);
	}

}
