<?php
/**
 * This file is the model file of the plugin.
 * Methods for management settings of application.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('SettingBase', 'CakeSettingsApp.Model');

/**
 * Setting for CakeSettingsApp.
 *
 * @package plugin.Model
 */
class Setting extends SettingBase {

	public $afterFindState = false;

	public $beforeSaveState = false;

	public $afterSaveState = false;

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
		'CountryCode' => [
			'rule' => ['lengthBetween', 2, 2],
			'message' => 'This field must contain a valid country code',
			'required' => true,
			'allowEmpty' => false,
		],
	];

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		$this->afterFindState = true;

		return $results;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->beforeSaveState = true;

		return true;
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
		$this->afterSaveState = true;
	}

/**
 * Return extended variables for form of application settings
 *
 * @return array Extended variables
 */
	public function getVars() {
		$variables = [
			'countries' => [
				'BY' => 'Belarus',
				'RU' => 'Russia',
				'US' => 'United States'
			]
		];

		return $variables;
	}
}
