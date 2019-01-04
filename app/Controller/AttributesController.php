<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the attributes.
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
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for management information about the attributes.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit and delete attributes.
 *
 * @package app.Controller
 */
class AttributesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Attributes';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ChangeState' => ['TargetModel' => 'Attribute'],
	];

/**
 * Base of action `index`. Used for redirect to home page.
 *
 * @return void
 */
	protected function _index() {
		$this->redirect('/');
	}

/**
 * Action `index`. Used for redirect to home page.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about attributes.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$this->view = 'view';
		$attributes = $this->Attribute->get($id, true);
		if (empty($attributes)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for attributes')));
		}
		$refId = $attributes['Attribute']['ref_id'];
		$refType = $attributes['Attribute']['ref_type'];
		$refNode = $attributes['Attribute']['ref_node'];
		$refTypeName = $this->Attribute->getNameTypeFor($refType, $refNode);
		$breadCrumbs = $this->Attribute->getBreadcrumbInfo(null, $refType, $refNode, $refId);
		$breadCrumbs[] = __('Viewing');
		$fullName = $this->Attribute->getFullName($id, $refType, $refNode, $refId, true);
		$pageHeader = __('Information of attributes');
		$headerMenuActions = [
			[
				'fas fa-pencil-alt',
				__('Edit attributes'),
				['controller' => 'attributes', 'action' => 'edit', $attributes['Attribute']['id']],
				['title' => __('Editing information of this attributes')]
			],
			[
				'far fa-trash-alt',
				__('Delete attributes'),
				['controller' => 'attributes', 'action' => 'delete', $attributes['Attribute']['id']],
				[
					'title' => __('Delete attributes'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this attributes?'),
				]
			]
		];
		$this->ViewExtension->setRedirectUrl(true, $refTypeName);

		$this->set(compact('attributes', 'breadCrumbs', 'fullName', 'refType', 'refNode', 'refId',
			'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information about attributes.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_view($id = null) {
		$this->_view($id);
	}

/**
 * Base of action `add`. Used to add attributes.
 *
 * POST Data:
 *  - Attribute: array data of attributes
 *
 * @param int|string $refType ID type of object attributes
 * @param int|string $refNode ID node of object attributes
 * @param int|string $refId Record ID of the node attributes
 * @return void
 */
	protected function _add($refType = null, $refNode = null, $refId = null) {
		$this->view = 'add';
		$refTypeName = $this->Attribute->getNameTypeFor($refType);
		$fullName = $this->Attribute->getFullName(null, $refType, $refNode, $refId);
		if (empty($refTypeName) || empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID, type or node for attributes')));
		}
		$breadCrumbs = $this->Attribute->getBreadcrumbInfo(null, $refType, $refNode, $refId);
		$breadCrumbs[] = __('Adding');
		if ($refTypeName === 'action') {
			$refTypeName = 'package';
		}
		if ($this->request->is('post')) {
			$this->Attribute->create();
			if ($this->Attribute->save($this->request->data)) {
				$this->Flash->success(__('Attributes has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Attributes could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Attribute->getDefaultValues($refType, $refNode, $refId);
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, $refTypeName);
			}
		}
		$this->_parseNamedParam();
		$listOs = $this->Attribute->getListOS();
		$listArch = $this->Attribute->getListArchitecture();
		$listLangId = $this->Attribute->getListLangID();
		$pageHeader = __('Adding attribute');

		$this->set(compact('breadCrumbs', 'fullName', 'refType', 'refNode', 'refId',
			'listOs', 'listArch', 'listLangId', 'pageHeader'));
	}

/**
 * Action `add`. Used to add attribute.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object attributes
 * @param int|string $refNode ID node of object attributes
 * @param int|string $refId Record ID of the node attributes
 * @return void
 */
	public function admin_add($refType = null, $refNode = null, $refId = null) {
		$this->_add($refType, $refNode, $refId);
	}

/**
 * Base of action `edit`. Used to edit information about attributes.
 *
 * POST Data:
 *  - Attribute: array data of attributes
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$attribute = $this->Attribute->get($id, false);
		if (empty($attribute)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for attributes')));
		}

		$refId = $attribute['Attribute']['ref_id'];
		$refType = $attribute['Attribute']['ref_type'];
		$refNode = $attribute['Attribute']['ref_node'];
		$refTypeName = $this->Attribute->getNameTypeFor($refType, $refNode);
		$breadCrumbs = $this->Attribute->getBreadcrumbInfo(null, $refType, $refNode, $refId);
		$breadCrumbs[] = __('Editing');
		$fullName = $this->Attribute->getFullName($id, $refType, $refNode, $refId);
		if ($this->request->is(['post', 'put'])) {
			if ($this->Attribute->save($this->request->data)) {
				$this->Flash->success(__('Attributes has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Attributes could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $attribute;
			$lcFields = ['lcid', 'lcidOS'];
			foreach ($lcFields as $lcField) {
				$lcFieldValue = $this->request->data('Attribute.' . $lcField);
				if (!empty($lcFieldValue)) {
					$this->request->data('Attribute.' . $lcField, explode(',', $lcFieldValue));
				}
			}
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, $refTypeName);
			}
		}
		$this->_parseNamedParam();
		$listOs = $this->Attribute->getListOS();
		$listArch = $this->Attribute->getListArchitecture();
		$listLangId = $this->Attribute->getListLangID();
		$pageHeader = __('Editing attributes');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Delete attributes'),
				['controller' => 'attributes', 'action' => 'delete', $attribute['Attribute']['id']],
				[
					'title' => __('Delete attributes'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this attributes?'),
				]
			]
		];

		$this->set(compact('breadCrumbs', 'fullName', 'refType', 'refNode', 'refId',
			'listOs', 'listArch', 'listLangId', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `edit`. Used to edit information about attributes.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `modify`. Used to add or edit attributes.
 *
 * @param int|string $refType ID type of object attributes
 * @param int|string $refNode ID node of object attributes
 * @param int|string $refId Record ID of the node attributes
 * @return void
 */
	protected function _modify($refType = null, $refNode = null, $refId = null) {
		$url = ['controller' => 'attributes'];
		$id = $this->Attribute->getIdFor($refType, $refNode, $refId);
		if (empty($id)) {
			$url['action'] = 'add';
			$url[] = $refType;
			$url[] = $refNode;
			$url[] = $refId;
		} else {
			$url['action'] = 'edit';
			$url[] = $id;
		}
		if ($this->request->is('modal')) {
			$url['ext'] = 'mod';
		}
		$this->redirect($url);
	}

/**
 * Action `modify`. Used to add or edit attributes
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object attributes
 * @param int|string $refNode ID node of object attributes
 * @param int|string $refId Record ID of the node attributes
 * @return void
 */
	public function admin_modify($refType = null, $refNode = null, $refId = null) {
		$this->_modify($refType, $refNode, $refId);
	}

/**
 * Base of action `delete`. Used to delete attributes.
 *
 * @param int|string $id ID of record for deleting
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _delete($id = null) {
		$this->ChangeState->delete($id);
	}

/**
 * Action `delete`. Used to delete attributes.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Processing named parameters from request.
 *  Used to set flag of parsing PCRE in attributes.
 *
 * @return void
 */
	protected function _parseNamedParam() {
		$argPcreParsing = (string)$this->request->param('named.pcreParsing');
		if (!ctype_digit($argPcreParsing)) {
			return;
		}

		$this->request->data('Attribute.pcre_parsing', (bool)$argPcreParsing);
	}

}
