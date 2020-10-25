<?php
/**
 * This file is the componet file of the application.
 *  Base for data processing components
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

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');

/**
 * BaseData Component.
 *
 * Base for data processing components
 * @package app.Controller.Component
 */
class BaseDataComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of target model
 *
 * @var object
 */
	protected $_modelTarget = null;

/**
 * Object of model `Setting`
 *
 * @var object
 */
	protected $_modelSetting = null;

/**
 * Name of target model
 *
 * @var string
 */
	protected $_targetName = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if empty settings `TargetModel`
 * @throws InternalErrorException if invalid model name in settings `TargetModel`
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		if (!isset($settings['TargetModel']) || empty($settings['TargetModel'])) {
			throw new InternalErrorException(__('Invalid target model'));
		}

		$this->_modelTarget = ClassRegistry::init($settings['TargetModel'], true);
		if (!$this->_modelTarget) {
			throw new InternalErrorException(__('Target model is not found'));
		}

		$this->_modelSetting = ClassRegistry::init('Setting');
		$this->_targetName = $this->_modelTarget->name;

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('Flash')) {
			$this->_controller->Flash = $this->_controller->Components->load('Flash');
			$this->_controller->Flash->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('CakeTheme.ViewExtension')) {
			$this->_controller->ViewExtension = $this->_controller->Components->load('CakeTheme.ViewExtension');
			$this->_controller->ViewExtension->initialize($this->_controller);
		}
	}

/**
 * Get target model name.
 *
 * @param bool $toLowerCase If True, returns the name in lowercase.
 * @return string Target model name
 */
	protected function _getTargetName($toLowerCase = true) {
		$targetName = $this->_targetName;
		if ($toLowerCase) {
			$targetName = mb_strtolower($targetName);
		}

		return $targetName;
	}

/**
 * Get target model name in plural form.
 *
 * @return string Target model name in plural form
 */
	protected function _getTargetNamePlural() {
		$targetName = $this->_getTargetName();
		$targetNamePlural = Inflector::pluralize($targetName);

		return $targetNamePlural;
	}

/**
 * Validation the ID argument for the exist of a record.
 *
 * @param int|string $id ID of record for validation
 * @return CakeResponse|null|bool Return True on Success, CakeResponse or Null on failure
 */
	protected function _validateId($id = null) {
		if (!empty($id) && !$this->_modelTarget->exists($id)) {
			$targetNameI18n = $this->_getTargetName(true);
			if (method_exists($this->_modelTarget, 'getTargetName')) {
				$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
			}
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}

		return true;
	}

}
