<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to remove, enable,
 *  disable, and change the state of the `template` flag.
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
 * @package app.Controller.Component
 */

App::uses('BaseDataComponent', 'Controller/Component');

/**
 * ChangeState Component.
 *
 * The base actions of the controller, used to remove, enable,
 *  disable, and change the state of the `template` flag.
 * @package app.Controller.Component
 */
class ChangeStateComponent extends BaseDataComponent {

/**
 * Action `delete`. Used to remove data.
 *
 * @param int|string $id ID of record for remove
 * @throws BadRequestException if request is not POST or DELETE
 * @return CakeResponse|null
 */
	public function delete($id = null) {
		$targetName = $this->_getTargetName();
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		$this->_modelTarget->id = $id;
		if (!$this->_modelTarget->exists()) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}

		$this->_controller->request->allowMethod('post', 'delete');
		$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);
		if ($this->_modelTarget->delete()) {
			$this->_controller->Flash->success(__('The %s has been deleted.', mb_ucfirst($targetNameI18n)));
		} else {
			$msg = null;
			if ($this->_modelTarget->Behaviors->loaded('CanDisable')) {
				$msg = $this->_modelTarget->getMessageCheckDisable();
			}
			if (!empty($msg)) {
				$this->_controller->Flash->warning($msg);
			} else {
				$this->_controller->Flash->error(__('The %s could not be deleted. Please, try again.', $targetNameI18n));
			}
		}

		return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
	}

/**
 * Used to change state of flag field.
 *
 * @param int|string $id ID of record for processing
 * @param string $field Name of field for processing
 * @param bool $state State
 * @param string $stateName Name of state
 * @param bool $useCanDisable Flag of using `CanDisable` behaviors
 * @throws InternalErrorException if ChangeState behavior is not loaded on target model
 * @return CakeResponse|null
 */
	public function changeStateField($id = null, $field = null, $state = false, $stateName = null, $useCanDisable = true) {
		if (!$this->_modelTarget->Behaviors->loaded('ChangeState')) {
			throw new InternalErrorException(__("Behavior '%s' is not loaded in target model", 'ChangeState'));
		}

		if (empty($stateName)) {
			$stateName = $field;
		}

		$targetName = $this->_getTargetName();
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		if (!$this->_modelTarget->exists($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}

		$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);
		if ($this->_modelTarget->setState($id, $field, $state)) {
			$this->_controller->Flash->success(__('The %s has been %s.', mb_ucfirst($targetNameI18n), $stateName));
		} else {
			$msg = null;
			if (!$state && $useCanDisable && $this->_modelTarget->Behaviors->loaded('CanDisable')) {
				$msg = $this->_modelTarget->getMessageCheckDisable();
			}
			if (!empty($msg)) {
				$this->_controller->Flash->warning($msg);
			} else {
				$this->_controller->Flash->error(__('The %s could not be %s. Please, try again.', $targetNameI18n, $stateName));
			}
		}

		return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
	}

/**
 * Action `enabled`. Used to enable or disable data.
 *
 * @param int|string $id ID of record for processing
 * @param bool $state State
 * @return CakeResponse|null
 */
	public function enabled($id = null, $state = false) {
		$stateName = __x('change state', 'enabled');
		if (!$state) {
			$stateName = __x('change state', 'disabled');
		}

		return $this->changeStateField($id, 'enabled', $state, $stateName, true);
	}

/**
 * Action `template`. Used to change template flag.
 *
 * @param int|string $id ID of record for processing
 * @param bool $state State
 * @return CakeResponse|null
 */
	public function template($id = null, $state = false) {
		$stateName = __x('change state', 'use as template');
		if (!$state) {
			$stateName = __x('change state', 'don\'t used as template');
		}

		return $this->changeStateField($id, 'template', $state, $stateName, false);
	}

}
