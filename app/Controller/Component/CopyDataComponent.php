<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to copying data
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
 * CopyData Component.
 *
 * The base actions of the controller, used to copying data
 * @package app.Controller.Component
 */
class CopyDataComponent extends BaseDataComponent {

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if CopyItem behavior is not loaded on target model
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		parent::__construct($collection, $settings);

		if (!$this->_modelTarget->Behaviors->loaded('CopyItem')) {
			throw new InternalErrorException(__("Behavior '%s' is not loaded in target model", 'CopyItem'));
		}
	}

/**
 * Action `copy`. Used to copying data.
 *
 * @param int|string $id ID of source record.
 * @return CakeResponse|null
 */
	public function actionCopy($id = null) {
		$targetName = $this->_getTargetName();
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		if (!empty($id) && !$this->_modelTarget->exists($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}

		$this->_controller->request->allowMethod('post');
		$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);
		if ($this->_modelTarget->makeCopy($id)) {
			$this->_controller->Flash->success(__('The %s has been copied.', mb_ucfirst($targetNameI18n)));
		} else {
			$this->_controller->Flash->error(__('The %s could not be copied. Please, try again.', $targetNameI18n));
		}

		return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
	}
}
