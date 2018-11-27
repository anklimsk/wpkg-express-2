<?php
/**
 * This file is the behavior file of the plugin. Is used for adding
 *  validation rules from config file.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The behavior is used for adding validation rules from config file.
 *
 * @package plugin.Model.Behavior
 */
class BindValidationBehavior extends ModelBehavior {

/**
 * Object of model `ConfigSync`
 *
 * @var object
 */
	protected $_modelConfigSync = null;

/**
 * Initiate behavior for the model using specified settings.
 *
 * Available settings:
 *
 * - dataModel: (string, optional) model name with new data.
 *   Default - EmployeeEdit.
 *
 * @param Model $model Model using the behavior
 * @param array $settings Settings to override for model.
 * @return void
 */
	public function setup(Model $model, $settings = []) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = ['ldapField' => null];
		}
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], $settings);

		$this->_modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
	}

/**
 * Return name of bind LDAP field.
 *
 * @param Model $model Model using this behavior
 * @return string Return Name of bind LDAP field.
 */
	protected function _getLdapField(Model $model) {
		return $this->settings[$model->alias]['ldapField'];
	}

/**
 * Return list of validation rules
 *
 * @param Model $model Model using this behavior
 * @return bool|string|array Return list of validation rules.
 *  Return True, if rule is not configured, or False of failure.
 */
	protected function _getValidationRules(Model $model) {
		$cachePath = 'validation_rules_' . $model->alias;
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_CONFIG);
		if ($cached !== false) {
			return $cached;
		}

		$ldapFields = $this->_modelConfigSync->getLdapFieldsInfo();
		if (empty($ldapFields)) {
			return false;
		}

		$ldapField = $this->_getLdapField($model);
		if (empty($ldapField)) {
			return false;
		}

		$result = Hash::get($ldapFields, $ldapField . '.rules');
		if (empty($result)) {
			$result = true;
		}
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_CONFIG);

		return $result;
	}

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = []) {
		$rules = $this->_getValidationRules($model);
		if (($rules === false) || ($rules === true)) {
			return $rules;
		}

		$validator = $model->validator();
		$validator['value'] = $rules;

		return true;
	}
}
