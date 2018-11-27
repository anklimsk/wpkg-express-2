<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to viewing data.
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
 * ViewData Component.
 *
 * The base actions of the controller, used to viewing data.
 * @package app.Controller.Component
 */
class ViewDataComponent extends BaseDataComponent {

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);

		if (!$this->_controller->Components->loaded('CakeTheme.Filter')) {
			$this->_controller->Filter = $this->_controller->Components->load('CakeTheme.Filter');
			$this->_controller->Filter->initialize($this->_controller);
		}
	}

/**
 * Action `index`. Used to view a full list of data.
 *
 * @param array $defaultConditions Default conditions for pagination.
 * @param array $groupActions List of group actions for processing.
 * @throws BadRequestException if request is not HTML
 * @throws InternalErrorException if request is POST and
 *  behavior 'GroupAction' is not loaded in target model
 * @return void
 */
	public function actionIndex($defaultConditions = [], $groupActions = []) {
		$this->_controller->view = 'index';
		if (!$this->_controller->ViewExtension->isHtml()) {
			throw new BadRequestException(__('Invalid request'));
		}

		$targetName = $this->_getTargetName();
		$targetNamePlural = $this->_getTargetNamePlural();
		$targetNamePluralI18n = $this->_getTargetNamePlural();
		$controllerName = $targetNamePlural;
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt') || method_exists($this->_modelTarget, 'getControllerName')) {
			$controllerName = $this->_modelTarget->getControllerName();
		}
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt') || method_exists($this->_modelTarget, 'getGroupName')) {
			$targetNamePluralI18n = $this->_modelTarget->getGroupName();
		}
		if (empty($defaultConditions)) {
			$defaultConditions = [];
		}
		if (empty($groupActions)) {
			$groupActions = [
				'group-data-dis' => __('Disable selected items'),
				'group-data-enb' => __('Enable selected items'),
				'group-data-del' => __('Delete selected items'),
			];
		}
		$conditions = $defaultConditions + $this->_controller->Filter->getFilterConditions();
		$usePost = true;
		if ($this->_controller->request->is('post')) {
			if (!$this->_modelTarget->Behaviors->loaded('GroupAction')) {
				throw new InternalErrorException(__("Behavior '%s' is not loaded in target model", 'GroupAction'));
			}

			$groupAction = $this->_controller->Filter->getGroupAction(array_keys($groupActions));
			$resultGroupProcess = $this->_modelTarget->processGroupAction($groupAction, $conditions);
			if ($resultGroupProcess !== null) {
				if ($resultGroupProcess) {
					$conditions = null;
					$this->_controller->Flash->success(__('Selected items has been processed.'));
				} else {
					$this->_controller->Flash->error(__('Selected items could not be processed. Please, try again.'));
				}
			}
		} else {
			if ((empty($defaultConditions) && !empty($conditions)) ||
				(!empty($defaultConditions) && ($conditions !== $defaultConditions))) {
				$usePost = false;
			}
		}
		$this->_controller->Paginator->settings = $this->_controller->paginate;
		$data = $this->_controller->Paginator->paginate($this->_modelTarget->alias, $conditions);
		if (empty($data)) {
			$this->_controller->Flash->information(__('%s not found', $targetNamePluralI18n));
		}
		$breadCrumbs = [];
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo();
			$breadCrumbs[] = __('Index');
		}
		$pageHeader = __('Index of %s', $targetNamePluralI18n);
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add %s', $targetNameI18n),
				['controller' => $controllerName, 'action' => 'add'],
				['title' => __('Add %s', $targetNameI18n), 'data-toggle' => 'modal']
			],
			[
				'fas fa-plus-square',
				__('Create from template'),
				['controller' => $controllerName, 'action' => 'create', 'plugin' => null],
				['title' => __('Create %s from template', $targetNameI18n), 'data-toggle' => 'modal']
			],
			'divider',
			[
				'far fa-file-code',
				__('Preview XML'),
				['controller' => $controllerName, 'action' => 'preview'],
				['title' => __('Preview full XML file'), 'data-toggle' => 'modal', 'data-modal-size' => 'lg']
			],
			[
				'fas fa-file-download',
				__('Download XML'),
				['controller' => $controllerName, 'action' => 'download', 'ext' => 'xml'],
				['title' => __('Download full XML file')]
			],
		];

		$this->_controller->ViewExtension->setRedirectUrl(true, $targetName);
		$this->_controller->set($targetNamePlural, $data);
		$this->_controller->set(compact('groupActions', 'usePost', 'breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information of data.
 *
 * @param int|string $id ID of record for viewing
 * @throws InternalErrorException if method 'get' is not found
 *  in target model
 * @return void
 */
	public function actionView($id = null) {
		if (!method_exists($this->_modelTarget, 'get')) {
			throw new InternalErrorException(__("Method '%s' is not found in target model", 'get'));
		}

		$this->_controller->view = 'view';
		$targetName = $this->_getTargetName();
		$targetNamePlural = $this->_getTargetNamePlural();
		$controllerName = $targetNamePlural;
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt') || method_exists($this->_modelTarget, 'getControllerName')) {
			$controllerName = $this->_modelTarget->getControllerName();
		}
		if (!$this->_modelTarget->exists($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}
		$breadCrumbs = [];
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo($id);
			$breadCrumbs[] = __('Viewing');
		}

		$bindLimit = 5;
		$data = $this->_modelTarget->get($id, true);
		$pageHeader = __('Information of %s', $targetNameI18n);
		$headerMenuActions = [
			[
				'fas fa-pencil-alt',
				__('Editing %s', $targetNameI18n),
				['controller' => $controllerName, 'action' => 'edit', $data[$this->_modelTarget->alias]['id']],
				['title' => __('Editing information of this %s', $targetNameI18n)]
			],
			[
				'far fa-trash-alt',
				__('Delete %s', $targetNameI18n),
				['controller' => $controllerName, 'action' => 'delete', $data[$this->_modelTarget->alias]['id']],
				[
					'title' => __('Delete %s', $targetNameI18n), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this %s?', $targetNameI18n),
				]
			]
		];
		if (method_exists($this->_modelTarget, 'getXMLdata')) {
			$headerMenuActionsExt = [
				'divider',
				[
					'far fa-file-code',
					__('Preview XML'),
					['controller' => $controllerName, 'action' => 'preview', $data[$this->_modelTarget->alias]['id']],
					['title' => __('Preview XML file'), 'data-toggle' => 'modal', 'data-modal-size' => 'lg']
				]
			];
			if (method_exists($this->_modelTarget, 'getDownloadName')) {
				$headerMenuActionsExt[] = [
					'fas fa-file-download',
					__('Download XML'),
					['controller' => $controllerName, 'action' => 'download', $data[$this->_modelTarget->alias]['id'], 'ext' => 'xml'],
					['title' => __('Download XML file'), 'skip-modal' => true]
				];
			}
			$headerMenuActions = array_merge($headerMenuActions, $headerMenuActionsExt);
		}
		if ($this->_modelTarget->Behaviors->loaded('GetGraphInfo')) {
			$graphType = constant('GRAPH_TYPE_' . mb_strtoupper($targetName));
			$headerMenuActionsExt = [
				'divider',
				[
					'fas fa-project-diagram',
					__('Graph of relations'),
					['controller' => 'graph', 'action' => 'view', $graphType, $data[$this->_modelTarget->alias]['id']],
					['title' => __('Graph of relations'), 'data-toggle' => 'modal', 'data-modal-size' => 'lg']
				]
			];
			$headerMenuActions = array_merge($headerMenuActions, $headerMenuActionsExt);
		}
		if ($this->_modelTarget->Behaviors->loaded('CopyItem')) {
			$headerMenuActionsExt = [
				'fas fa-copy',
				__('Copy %s', $targetNameI18n),
				['controller' => $controllerName, 'action' => 'copy', $data[$this->_modelTarget->alias]['id']],
				[
					'title' => __('Copy %s', $targetNameI18n), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to copy this %s?', $targetNameI18n),
				]
			];
			array_unshift($headerMenuActions, $headerMenuActionsExt);
		}
		if ($this->_modelTarget->Behaviors->loaded('TemplateData') &&
			$this->_modelTarget->checkIsTemplate($data[$this->_modelTarget->alias]['id'])) {
			$headerMenuActionsExt = [
				'fas fa-plus-square',
				__('Create from template'),
				['controller' => $controllerName, 'action' => 'create', $data[$this->_modelTarget->alias]['id']],
				[
					'title' => __('Create %s from this template', $targetNameI18n),
				]
			];
			array_unshift($headerMenuActions, $headerMenuActionsExt);
		}

		$this->_controller->ViewExtension->setRedirectUrl(true, $targetName);
		$this->_controller->set($targetName, $data);
		$this->_controller->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions', 'bindLimit'));
	}
}
