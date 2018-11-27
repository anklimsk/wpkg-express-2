<?php
/**
 * This file is the behavior file of the application. Is used to
 *  verify the ability to delete or disable a data item.
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
 * The behavior is used to verify the ability to delete or disable a
 *  data item.
 *
 * @package app.Model.Behavior
 */
class CanDisableBehavior extends ModelBehavior {

/**
 * The text of the message about the impossibility of performing the operation
 *
 * @var string
 */
	protected $_msgCheckDisable = '';

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if method "checkDisable" is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!method_exists($model, 'checkDisable')) {
			throw new InternalErrorException(__("Method '%s' is not found in model %s", 'checkDisable', $model->name));
		}
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * Actions:
 *  - Checking the ability to perform operations.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save()
 * @return mixed False if the operation should abort. Any other result will continue
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		if (empty($model->id)) {
			return true;
		}

		if (!isset($model->data[$model->alias]['enabled']) ||
			$model->data[$model->alias]['enabled']) {
			return true;
		}

		return $this->_checkDisable($model, $model->id);
	}

/**
 * Before delete is called before any delete occurs on the attached model, but after the model's
 * beforeDelete is called. Returning false from a beforeDelete will abort the delete.
 *
 * Actions:
 *  - Checking the ability to perform operations.
 *
 * @param Model $model Model using this behavior
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return mixed False if the operation should abort. Any other result will continue
 */
	public function beforeDelete(Model $model, $cascade = true) {
		return $this->_checkDisable($model, $model->id);
	}

/**
 * Checking the ability to perform operations
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to check
 * @return bool Return True, if possible
 */
	protected function _checkDisable(Model $model, $id = null) {
		$this->_setMessageCheckDisable('');
		$msg = $model->checkDisable($id);
		if ($msg === true) {
			return true;
		}
		if ($msg !== false) {
			$this->_setMessageCheckDisable($msg);
		}

		return false;
	}

/**
 * Set text of the message about the impossibility of performing the operation
 *
 * @param string $msg Text of message
 * @return void
 */
	protected function _setMessageCheckDisable($msg = '') {
		$this->_msgCheckDisable = (string)$msg;
	}

/**
 * Return text of the message about the impossibility of performing the operation
 *
 * @param Model $model Model using this behavior
 * @return void
 */
	public function getMessageCheckDisable(Model $model) {
		return $this->_msgCheckDisable;
	}
}
