<?php
/**
 * This file is the model file of the application. Used to
 *  save and restore table settings such as sorting, number of records
 *  per page and filter parameters.
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
 * The model is used to save and restore table settings such as sorting,
 *  number of records per page and filter parameters.
 *
 * @package app.Model
 */
class Bookmark extends AppModel {

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
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
		'data' => [
			'rule' => 'notBlank',
			'message' => 'Incorrect data',
			'allowEmpty' => false,
			'required' => true
		],
	];

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 *  - Unserialize data of bookmark.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		if (empty($results)) {
			return $results;
		}

		foreach ($results as &$result) {
			if (!isset($result[$this->alias]['data']) || empty($result[$this->alias]['data'])) {
				continue;
			}

			$result[$this->alias]['data'] = unserialize($result[$this->alias]['data']);
		}

		return $results;
	}

/**
 * Called during validation operations, before validation.
 *
 * Actions:
 *  - Serialize data of bookmark.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		if (isset($this->data[$this->alias]['data'])
			&& !empty($this->data[$this->alias]['data'])
			&& is_array($this->data[$this->alias]['data'])
		) {
			$this->data[$this->alias]['data'] = serialize($this->data[$this->alias]['data']);
		}
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Serialize data of bookmark.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		if (isset($this->data[$this->alias]['data'])
			&& !empty($this->data[$this->alias]['data'])
			&& is_array($this->data[$this->alias]['data'])
		) {
			$this->data[$this->alias]['data'] = serialize($this->data[$this->alias]['data']);
		}

		return true;
	}

/**
 * Create bookmark
 *
 * @param string|null $key Page key.
 * @param array $data Data of bookmark.
 * @return bool Success.
 */
	public function createBookmark($key = null, $data = []) {
		if (empty($key) || !is_array($data)) {
			return false;
		}

		$bookmark = $this->getBookmark($key);
		if (!$bookmark) {
			$bookmark = [
				$this->alias => [
					'hash' => $key,
					'data' => $data
				]
			];
		} else {
			$bookmark[$this->alias]['data'] = $data;
		}
		$this->create();

		return (bool)$this->save($bookmark);
	}

/**
 * Return array of bookmark
 *
 * @param string|null $key Page key.
 * @return array|bool Return array of bookmark information,
 *  or False on failure.
 */
	public function getBookmark($key = null) {
		if (empty($key)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.hash',
			$this->alias . '.data',
		];
		$conditions = [
			$this->alias . '.hash' => $key,
		];

		return $this->find('first', compact('fields', 'conditions'));
	}

/**
 * Clear bookmark
 *
 * @param string|null $key Page key.
 * @return bool Success.
 */
	public function clearBookmark($key = null) {
		return $this->createBookmark($key, []);
	}
}
