<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the hosts.
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
 * The controller is used for management information about the hosts.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of host;
 *  - preview and download XML file of host;
 *  - verifying and recovery state list of hosts;
 *  - to enable or disable host;
 *  - set or unset the host usage flag as a template;
 *  - to copy host;
 *  - to create new host based on template;
 *  - to generate new hosts based on template by name from LDAP;
 *  - autocomplete name of computers from LDAP.
 *
 * @package app.Controller
 */
class HostsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Hosts';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'CakeTheme.Filter',
		'ViewData' => ['TargetModel' => 'Host'],
		'ExportData' => ['TargetModel' => 'Host'],
		'ChangeState' => ['TargetModel' => 'Host'],
		'VerifyData' => ['TargetModel' => 'Host'],
		'CakeTheme.Move' => ['model' => 'Host'],
		'TemplateData' => ['TargetModel' => 'Host'],
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Cache',
		'GeshiExt',
		'Tools.Tree',
		'Indicator'
	];

/**
 * Settings for component 'Paginator'
 *
 * @var array
 */
	public $paginate = [
		'page' => 1,
		'limit' => 20,
		'maxLimit' => 250,
		'fields' => [
			'Host.id',
			'Host.mainprofile_id',
			'Host.lft',
			'Host.rght',
			'Host.enabled',
			'Host.template',
			'Host.id_text',
			'Host.notes',
			'Host.modified',
		],
		'order' => [
			'Host.lft' => 'asc'
		],
		'contain' => ['MainProfile']
	];

/**
 * Called before the controller action.
 *
 * Actions:
 *  - Configure components.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Security->unlockedActions = [
			'admin_drop',
			'admin_move',
			'admin_computers',
			'admin_autocomplete'
		];

		parent::beforeFilter();
	}

/**
 * Base of action `index`. Used to view a full list of hosts.
 *
 * @return void
 */
	protected function _index() {
		$this->ViewData->actionIndex();
		$pageHeader = __('Index of hosts');
		$headerMenuActions = $this->viewVars['headerMenuActions'];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-magic',
			__('Generate based on LDAP'),
			['controller' => 'hosts', 'action' => 'generate', 'plugin' => null],
			['title' => __('Generate from template based on LDAP'), 'data-toggle' => 'modal']
		];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-pencil-ruler',
			__('Build a graph'),
			['controller' => 'graph', 'action' => 'build'],
			['title' => __('Build a graph for host'), 'data-toggle' => 'modal',
			'data-modal-size' => 'lg']
		];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-clipboard-check',
			__('Verify state of list hosts'),
			['controller' => 'hosts', 'action' => 'verify'],
			['title' => __('Verify state of list variables'), 'data-toggle' => 'modal']
		];

		$this->set(compact('pageHeader', 'headerMenuActions'));
	}

/**
 * Action `index`. Used to export a XML data of hosts.
 *  User role - user.
 *
 * @return void
 */
	public function index() {
		$this->ExportData->export();
	}

/**
 * Action `index`. Used to view a full list of hosts.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about host.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$this->ViewData->actionView($id);
		$pageHeader = __('Information of host');
		$this->set(compact('pageHeader'));
	}

/**
 * Action `view`. Used to view information about host.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_view($id = null) {
		$this->_view($id);
	}

/**
 * Base of action `preview`. Used to preview XML information of host.
 *
 * @param int|string $id ID of record for previewing.
 * @return void
 */
	protected function _preview($id = null) {
		$this->view = 'preview';
		$this->ExportData->preview($id);
	}

/**
 * Action `preview`. Used to preview XML information of host.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `add`. Used to add host.
 *
 * POST Data:
 *  - Host: array data of host;
 *  - Profile: array data of additional associated profiles.
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$this->Host->bindHabtmAssocProfiles();
		if ($this->request->is('post')) {
			$this->Host->create();
			if ($this->Host->saveHost($this->request->data)) {
				$this->Flash->success(__('Host has been saved.'));

				return $this->ViewData->redirectToNewData('host');
			} else {
				$this->Flash->error(__('Host could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Host->getDefaultValues();
			$this->ViewExtension->setRedirectUrl(null, 'host');
		}
		$profiles = $this->Host->MainProfile->getList();
		$breadCrumbs = $this->Host->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		$pageHeader = __('Adding host');

		$this->set(compact('profiles', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add host.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about host.
 *
 * POST Data:
 *  - Host: array data of host;
 *  - Profile: array data of additional associated profiles.
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$host = $this->Host->get($id, [], false);
		if (empty($host)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for host')));
		}

		$this->Host->bindHabtmAssocProfiles();
		if ($this->request->is(['post', 'put'])) {
			if ($this->Host->saveHost($this->request->data)) {
				$this->Flash->success(__('Host has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'host');
			} else {
				$this->Flash->error(__('Host could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $host;
			$this->ViewExtension->setRedirectUrl(null, 'host');
		}
		$profiles = $this->Host->MainProfile->getList();
		$breadCrumbs = $this->Host->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		$pageHeader = __('Editing host');

		$this->set(compact('profiles', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `edit`. Used to edit information about host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete host.
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
 * Action `delete`. Used to delete host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `enable`. Used to enable host.
 *
 * @param int|string $id ID of record for enabling
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _enabled($id = null, $state = false) {
		$this->ChangeState->enabled($id, $state);
	}

/**
 * Action `enable`. Used to enable host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for enabling
 * @param bool $state State of flag
 * @return void
 */
	public function admin_enabled($id = null, $state = false) {
		$this->_enabled($id, $state);
	}

/**
 * Base of action `template`. Used to set or unset the host
 *  usage flag as a template.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _template($id = null, $state = false) {
		$this->ChangeState->template($id, $state);
	}

/**
 * Action `template`. Used to set or unset the host
 *  usage flag as a template.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @return void
 */
	public function admin_template($id = null, $state = false) {
		$this->_template($id, $state);
	}

/**
 * Base of action `download`. Used to download XML file
 *  of host.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	protected function _download($id = null) {
		$this->ExportData->download($id);
	}

/**
 * Action `download`. Used to download XML file
 *  of host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	public function admin_download($id = null) {
		$this->_download($id);
	}

/**
 * Base of action `copy`. Used to copy host.
 *
 * @param int|string $id ID of record for copying
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST`
 * @return void
 */
	protected function _copy($id = null) {
		$this->TemplateData->actionCopy($id);
	}

/**
 * Action `copy`. Used to copy host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for copying
 * @return void
 */
	public function admin_copy($id = null) {
		$this->_copy($id);
	}

/**
 * Base of action `create`. Used to create new host based on template.
 *
 * @param int|string $id ID of record for createing
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST`
 * @return void
 */
	protected function _create($id = null) {
		$this->TemplateData->actionCreate($id);
	}

/**
 * Action `create`. Used to create new host based on template.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for createing
 * @return void
 */
	public function admin_create($id = null) {
		$this->_create($id);
	}

/**
 * Base of action `generate`. Used to generate new hosts
 *  based on template by name from LDAP.
 *
 * @return void
 */
	protected function _generate() {
		$this->view = 'generate';
		$listHostTemplates = $this->Host->getListTemplates();
		if (empty($listHostTemplates)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('Hosts template list is empty')));
		}

		if ($this->request->is('post')) {
			$this->loadModel('ExtendQueuedTask');
			$computers = $this->request->data('Host.computers');
			$hostTemplateId = $this->request->data('Host.host_template_id');
			$profileTemplateId = $this->request->data('Host.profile_template_id');
			$taskParam = compact('computers', 'hostTemplateId', 'profileTemplateId');
			if (!empty($computers) && !empty($hostTemplateId) &&
				(bool)$this->ExtendQueuedTask->createJob('GenerateXml', $taskParam, null, 'generate')) {
				$this->Flash->success(__('Generating XML put in queue...'));
				$this->ViewExtension->setProgressSseTask('GenerateXml');

				return $this->ViewExtension->redirectByUrl(null, 'host');
			} else {
				$this->Flash->error(__('Generating XML put in queue unsuccessfully'));
			}
		} else {
			$host = [
				'Host' => [
					'computers' => [],
					'host_template_id' => null,
					'profile_template_id' => null,
				]
			];
			$this->request->data = $host;
			$this->ViewExtension->setRedirectUrl(null, 'host');
		}
		$listProfileTemplates = $this->Host->MainProfile->getListTemplates();
		$breadCrumbs = $this->Host->getBreadcrumbInfo();
		$breadCrumbs[] = __('Generating');
		$pageHeader = __('Generating host');

		$this->set(compact('breadCrumbs', 'pageHeader', 'listHostTemplates', 'listProfileTemplates'));
	}

/**
 * Action action `generate`. Used to generate new hosts
 *  based on template by name from LDAP.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_generate() {
		$this->_generate();
	}

/**
 * Base of action `unused`. Used to disable unused
 *  hosts and profiles.
 *
 * @return void
 */
	protected function _unused() {
		$this->loadModel('ExtendQueuedTask');
		$this->ViewExtension->setRedirectUrl(null, 'host');
		if ((bool)$this->ExtendQueuedTask->createJob('DisableUnused', null, null, 'process')) {
			$this->Flash->success(__('Disabling unused hosts and profiles put in queue...'));
			$this->ViewExtension->setProgressSseTask('DisableUnused');
		} else {
			$this->Flash->error(__('Disabling unused hosts and profiles put in queue unsuccessfully.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'host');
	}

/**
 * Action `unused`. Used to disable unused hosts and
 *  profiles.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_unused() {
		$this->_unused();
	}

/**
 * Base of action `computers`. Used to get a list of computer 
 *  names from LDAP by query.
 *
 * POST Data:
 *  - `q`: query string to get a list of computer.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	protected function _computers() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->RequestHandler->prefers('json')) {
			throw new BadRequestException(__('Invalid request'));
		}

		$this->request->allowMethod('post');
		$query = (string)$this->request->data('q');
		$data = $this->Host->getListNotProcessedComputersByQuery($query);

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `computers`. Used to get a list of computer
 *  names from LDAP by query.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_computers() {
		$this->_computers();
	}

/**
 * Base of action `autocomplete`. Is used to autocomplete computer name.
 *
 * POST Data:
 *  - `query`: query string for autocomple.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	protected function _autocomplete() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$query = (string)$this->request->data('query');
		$limit = $this->Setting->getConfig('AutocompleteLimit');
		$data = $this->Host->getListComputersByQuery($query, $limit);

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `autocomplete`. Is used to autocomplete computer name.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_autocomplete() {
		$this->_autocomplete();
	}

/**
 * Action `move`. Used to move host to new position.
 * 
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int|string $id ID of record for moving
 * @param int|string $delta Delta for moving
 * @throws MethodNotAllowedException if request is not POST
 * @return void
 */
	protected function _move($direct = null, $id = null, $delta = 1) {
		set_time_limit(MOVE_HOST_TIME_LIMIT);
		$this->Move->moveItem($direct, $id, $delta);
	}

/**
 * Action `move`. Used to move host to new position.
 *  User role - administrator.
 *
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int|string $id ID of record for moving
 * @param int|string $delta Delta for moving
 * @return void
 */
	public function admin_move($direct = null, $id = null, $delta = 1) {
		$this->_move($direct, $id, $delta);
	}

/**
 * Action `drop`. Used to drag and drop host to new position
 *
 * POST Data:
 *  - `target` The ID of the item to moving to new position;
 *  - `parent` New parent ID of item;
 *  - `parentStart` Old parent ID of item;
 *  - `tree` Array of ID subtree for item. 
 *
 * @throws BadRequestException if request is not AJAX, POST or JSON.
 * @throws InternalErrorException if tree of subordinate is disabled
 * @throws MethodNotAllowedException if request is not POST
 * @return void
 */
	protected function _drop() {
		$this->Move->dropItem();
	}

/**
 * Action `drop`. Used to drag and drop host to new position
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_drop() {
		$this->_drop();
	}

/**
 * Base of action `verify`. Used to verify state list of hosts.
 *
 * @return void
 */
	protected function _verify() {
		$this->VerifyData->actionVerify();
	}

/**
 * Action `verify`. Used to verify state list of hosts.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_verify() {
		$this->_verify();
	}

/**
 * Base of action `recover`. Used to recover state list of
 *  hosts.
 *
 * @return void
 */
	protected function _recover() {
		$this->VerifyData->actionRecover();
	}

/**
 * Action `recover`. Used to recover state list of
 *  hosts.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_recover() {
		$this->_recover();
	}

}
