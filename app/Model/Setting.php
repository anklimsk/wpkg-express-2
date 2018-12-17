<?php
/**
 * This file is the model file of the application. Used to
 *  manage settings of application.
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

App::uses('SettingBase', 'CakeSettingsApp.Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Utility');

/**
 * The model is used to manage settings of application.
 *
 * @package app.Model
 */
class Setting extends SettingBase {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'Setting';

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'IntAuthUser' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid internal Username',
			'allowEmpty' => true,
		],
		'IntAuthPassword' => [
			'intauthpassword' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Invalid internal password',
			],
			'intauthpasswordequal' => [
				'rule' => 'validPasswords',
				'required' => true,
				'last' => true,
				'message' => 'Passwords dont match'
			]
		],
		'XmlAuthUser' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid xml Username',
			'allowEmpty' => true,
		],
		'XmlAuthPassword' => [
			'xmlauthpassword' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Invalid xml password',
			],
			'xmlauthpasswordequal' => [
				'rule' => 'validPasswords',
				'required' => true,
				'last' => true,
				'message' => 'Passwords dont match'
			]
		],
		'SmbAuthUser' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid SMB Username',
			'allowEmpty' => true,
		],
		'SmbAuthPassword' => [
			'smbauthpassword' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Invalid SMB password',
			],
			'smbauthpasswordequal' => [
				'rule' => 'validPasswords',
				'required' => true,
				'last' => true,
				'message' => 'Passwords dont match'
			]
		],
		'SmbWorkgroup' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid Workgroup or domain',
			'allowEmpty' => true,
		],
		'SmbServer' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid SMB server name',
			'allowEmpty' => true,
		],
		'SmbLogShare' => [
			'rule' => 'notBlank',
			'required' => true,
			'message' => 'Invalid SMB share name for logs',
			'allowEmpty' => true,
		],
	];

/**
 * Decrypt a stettings value using AES-256.
 *
 * @param string $data Data to decrypt.
 * @return string Decrypted data. Any trailing null bytes will be removed.
 */
	protected function _decryptData($data = null) {
		if (empty($data)) {
			return $data;
		}

		return Security::decrypt(base64_decode($data), Configure::read('Security.key'));
	}

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 *  - Decrypt a stettings values.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed Result of the find operation
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false, $key = null) {
		if (empty($results)) {
			return $results;
		}

		$passFieldsEnc = $this->_getListPassFieldsEncrypt();
		if (empty($key) || in_array($key, $passFieldsEnc)) {
			if (empty($key)) {
				foreach ($passFieldsEnc as $passField) {
					if (isset($results[$this->alias][$passField])) {
						$results[$this->alias][$passField] = $this->_decryptData($results[$this->alias][$passField]);
					}
				}
			} else {
				$results = $this->_decryptData($results);
			}
		}

		return $results;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Encrypt a stettings values;
 *  - Generates password hash.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$hashType = 'sha256';
		$passFieldsHash = $this->_getListPassFieldsHash();
		foreach ($passFieldsHash as $passField) {
			if (!empty($this->data[$this->alias][$passField])) {
				$passwordHasher = new SimplePasswordHasher(compact('hashType'));
				$this->data[$this->alias][$passField] = $passwordHasher->hash(
					$this->data[$this->alias][$passField]
				);
			}
		}
		$passFieldsEnc = $this->_getListPassFieldsEncrypt();
		foreach ($passFieldsEnc as $passField) {
			if (!empty($this->data[$this->alias][$passField])) {
				$this->data[$this->alias][$passField] = base64_encode(Security::encrypt($this->data[$this->alias][$passField], Configure::read('Security.key')));
			}
		}

		return true;
	}

/**
 * Called after each successful save operation.
 *
 * Actions:
 *  - Clear the View cache.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
		clearCache(null, 'views', '.php');
	}

/**
 * Return list of fields to encrypt
 *
 * @return array Return list of fields.
 */
	protected function _getListPassFieldsEncrypt() {
		$passFieldsEnc = [
			'XmlAuthPassword',
			'SmbAuthPassword'
		];

		return $passFieldsEnc;
	}

/**
 * Return list of fields to generates password hash
 *
 * @return array Return list of fields.
 */
	protected function _getListPassFieldsHash() {
		$passFieldsHash = [
			'IntAuthPassword',
		];

		return $passFieldsHash;
	}

/**
 * Return extended variables for form of application settings
 *
 * @return array Extended variables
 */
	public function getVars() {
		$variables = [];

		return $variables;
	}

/**
 * Check application is correctly configured
 *
 * @return bool Success
 */
	public function isAuthGroupConfigured() {
		$markerFile = $this->getPathMarkerFile();
		if ($this->_checkMarkerFile($markerFile)) {
			return true;
		}

		$intUsr = $this->getConfig('IntAuthUser');
		$intPswd = $this->getConfig('IntAuthPassword');
		if (empty($intUsr) || empty($intPswd)) {
			return false;
		}

		return $this->_createMarkerFile($markerFile);
	}
}
