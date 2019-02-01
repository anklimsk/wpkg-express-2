<?php
/**
 * This file is the controller file of the application. Used for
 *  render a charts.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for render a charts.
 *
 * This controller allows to perform the following operations:
 *  - to render a chart for package.
 *
 * @package app.Controller
 */
class ChartsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Charts';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Chart',
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
			'admin_dataset'
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
 * Base of action `view`. Used to view a chart.
 *
 * @param int|string $refType ID type of object chart
 * @param int|string $refId Record ID of object chart
 * @return void
 */
	protected function _view($refType = null, $refId = null) {
		$this->view = 'view';
		$refTypeName = $this->Chart->getNameTypeFor($refType);
		if (empty($refTypeName) || empty($refId)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for chart')));
		}

		$pageHeader = $this->Chart->getFullName(null, $refType, null, $refId);
		if (empty($pageHeader)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for chart')));
		}

		$this->ViewExtension->setRedirectUrl(true, $refTypeName);
		$breadCrumbs = $this->Chart->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Viewing');
		$chartType = 'doughnut';
		$chartTitle = $this->Chart->getChartTitle($refType, $refId);
		$chartClickUrl = $this->Chart->getChartClickUrl($refType, $refId);

		$this->set(compact('breadCrumbs', 'pageHeader', 'chartType', 'chartTitle',
			'chartClickUrl', 'refType', 'refId'));
	}

/**
 * Action `view`. Used to view a chart.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object chart
 * @param int|string $refId Record ID of object chart
 * @return void
 */
	public function admin_view($refType = null, $refId = null) {
		$this->_view($refType, $refId);
	}

/**
 * Base of action `dataset`. Used to return data for chart.
 *
 * POST Data:
 *  - `refType`: ID type of object chart;
 *  - `refId`: Record ID of object chart
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	protected function _dataset() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [];
		$refType = $this->request->data('refType');
		$refId = $this->request->data('refId');
		$data = $this->Chart->getChartData($refType, $refId);
		if (empty($data)) {
			$data = [];
		}

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `dataset`. Used to return data for chart.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_dataset() {
		$this->_dataset();
	}
}
