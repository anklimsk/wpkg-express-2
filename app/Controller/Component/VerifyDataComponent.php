<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to verify and
 *  recovery tree data.
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
 * VerifyData Component.
 *
 * The base actions of the controller, used to verify and
 *  recovery tree data.
 * @package app.Controller.Component
 */
class VerifyDataComponent extends BaseDataComponent {

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if Tree behavior is not loaded on target model
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		parent::__construct($collection, $settings);

		if (!$this->_modelTarget->Behaviors->loaded('Tree')) {
			throw new InternalErrorException(__("Behavior '%s' is not loaded in target model", 'Tree'));
		}
	}

/**
 * Action `verify`. Used to verify tree data.
 *
 * @param int|string $refType Type of object tree.
 * @param int|string $refId Record ID of the tree owner.
 * @throws InternalErrorException if invalid $refType or $refId
 * @return void
 */
	public function actionVerify($refType = null, $refId = null) {
		$this->_controller->view = 'verify';
		set_time_limit(CHECK_TREE_TIME_LIMIT);
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$fullName = $this->_modelTarget->getFullName(null, $refType, null, $refId);
			$this->_controller->set(compact('fullName'));
		}

		if ($this->_modelTarget->Behaviors->loaded('ScopeTree')) {
			if (!$this->_modelTarget->setScopeModel($refType, $refId)) {
				throw new InternalErrorException(__('Error on setting scope of tree (list)'));
			}
		}
		$targetName = $this->_getTargetName();
		$targetNamePlural = $this->_getTargetNamePlural();
		$controllerName = $targetNamePlural;
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt') || method_exists($this->_modelTarget, 'getControllerName')) {
			$controllerName = $this->_modelTarget->getControllerName();
		}
		$breadCrumbs = [];
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo(null, $refType, null, $refId);
			$breadCrumbs[] = __('Verifying');
		}

		$treeState = $this->_modelTarget->verify();
		$pageHeader = __('Verifying state tree (list)');
		$headerMenuActions = [];
		if (($treeState !== true)) {
			$headerMenuActions[] = [
				'fas fa-redo-alt',
				__('Recovery state of tree (list)'),
				['controller' => $controllerName, 'action' => 'recover', $refType, $refId],
				['title' => __('Recovery state of tree (list)'), 'data-toggle' => 'request-only']
			];
		}

		$this->_controller->set(compact('treeState', 'breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `recover`. Used to recovery tree data.
 *
 * @param int|string $refType Type of object tree.
 * @param int|string $refId Record ID of the tree owner.
 * @return CakeResponse|null
 */
	public function actionRecover($refType = null, $refId = null) {
		$modelExtendQueuedTask = ClassRegistry::init('ExtendQueuedTask');
		$targetName = $this->_getTargetName();
		$targetModelName = $this->_getTargetName(false);
		$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);
		$taskParam = compact('targetModelName', 'refType', 'refId');
		if ((bool)$modelExtendQueuedTask->createJob('RecoveryTree', $taskParam, null, 'recovery')) {
			$this->_controller->Flash->success(__('Recovering tree (list) put in queue...'));
			$this->_controller->ViewExtension->setProgressSseTask('RecoveryTree');
		} else {
			$this->_controller->Flash->error(__('Recovering tree (list) put in queue unsuccessfully'));
		}

		return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
	}
}
