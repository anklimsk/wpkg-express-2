<?php
/**
 * This file is the model file of the application. Used to
 *  manage package precheck types.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White © 2009
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');

/**
 * The model is used to manage package precheck types.
 *
 * @package app.Model
 */
class PackagePrecheckType extends AppModel {

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
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PACKAGE_PRECHECK_TYPE],
		'InitDBdata' => [
			'constantPrefix' => 'PACKAGE_PRECHECK_',
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
				'message' => 'The precheck type is invalid.',
				'last' => true
			],
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'The precheck type already exists.',
				'last' => true
			],
		]
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Convert precheck type to lowercase.
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
 * Return list of package precheck types
 *
 * @return array Return list of package precheck types
 */
	public function getListPackagePrecheckTypes() {
		return $this->getList(null, 'package_precheck');
	}
}
