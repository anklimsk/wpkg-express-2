<?php
/**
 * This file is the controller file of the application. Used for
 *  generate a graph.
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
 * The controller is used for generate a graph.
 *
 * This controller allows to perform the following operations:
 *  - to generate a grap for package, profile and host;
 *  - to generate a full graph for host by hostname.
 *
 * @package app.Controller
 */
class GraphController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-ActionTypes
 */
	public $name = 'Graph';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'GraphViz',
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
			'admin_generate'
		];

		parent::beforeFilter();
	}

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
 * Base of action `view`. Used to view a graph.
 *
 * @param int|string $refType ID type of object graph
 * @param int|string $refId Record ID of object graph
 * @param bool $useBuildGraph Flag of using build mode for host
 * @return void
 */
	protected function _view($refType = null, $refId = null, $useBuildGraph = false) {
		$this->view = 'view';
		$refTypeName = $this->GraphViz->getNameTypeFor($refType);
		if (empty($refTypeName) || (!$useBuildGraph && empty($refId))) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for graph')));
		}

		$pageHeader = $this->GraphViz->getFullName(null, $refType, null, $refId);
		if (empty($pageHeader)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for graph')));
		}

		$this->ViewExtension->setRedirectUrl(true, $refTypeName);
		$data = [
			'ref_type' => $refType,
			'ref_id' => $refId,
			'full_graph' => $useBuildGraph,
			'host_name' => ''
		];
		$this->request->data('GraphViz', $data);
		$headerMenuActions = [
			[
				'fas fa-file-download',
				__('Download this graph'),
				'#',
				[
					'title' => __('Download this graph'),
					'skip-modal' => true,
					'class' => 'disabled',
					'id' => 'linkDownloadGraph',
					'data-file-name' => $pageHeader . '.svg'
				]
			],
		];
		$breadCrumbs = $this->GraphViz->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = ($useBuildGraph ? __('Building a graph') : __('Viewing'));

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions',
			'useBuildGraph'));
	}

/**
 * Action `view`. Used to view a graph.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object graph
 * @param int|string $refId Record ID of object graph
 * @return void
 */
	public function admin_view($refType = null, $refId = null) {
		$this->_view($refType, $refId, false);
	}

/**
 * Action `build`. Used to view a graph for host by hostname.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_build() {
		$this->_view(GRAPH_TYPE_HOST, null, true);
	}

/**
 * Base of action `generate`. Used to generate a graph.
 *
 * POST Data:
 *  - `GraphViz.ref_type`: ID type of object graph;
 *  - `GraphViz.ref_id`: Record ID of object graph;
 *  - `GraphViz.pkg-full_graph`: flag of generate full graph;
 *  - `GraphViz.host_name`: hostname for build graph.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @throws InternalErrorException if the error in the process of 
 *  generating the graph
 * @return void
 */
	protected function _generate() {
		$this->view = 'generate';
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post')) {
			throw new BadRequestException();
		}

		$type = $this->request->data('GraphViz.ref_type');
		$id = $this->request->data('GraphViz.ref_id');
		$full = $this->request->data('GraphViz.full_graph');
		$hostName = $this->request->data('GraphViz.host_name');

		$data = null;
		if (!empty($hostName)) {
			$data = $this->GraphViz->buildGraph($hostName, $full);
		} elseif (!empty($id)) {
			$data = $this->GraphViz->getGraph($type, $id, $full);
		} else {
			$this->set(compact('data'));
			return;
		}
		if ($data === false) {
			throw new InternalErrorException(__('Error on creating graph'));
		}
		$this->set(compact('data'));
	}

/**
 * Action `generate`. Used to generate a graph.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_generate() {
		$this->_generate();
	}
}
