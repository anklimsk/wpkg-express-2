<?php
/**
 * This file is the componet file of the application.
 *  Used to save and restore table settings such as sorting, number of records
 *  per page and filter parameters.
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
 * @copyright Copyright 2018-2020, Andrey Klimov.
 * @package app.Controller.Component
 */

App::uses('BaseDataComponent', 'Controller/Component');
App::uses('ClassRegistry', 'Utility');

/**
 * BookmarkTable Component.
 *
 * Used to save and restore table settings such as sorting, number of records
 *  per page and filter parameters.
 * @package app.Controller.Component
 */
class BookmarkTableComponent extends BaseDataComponent {

/**
 * Current page key
 *
 * @var string|null
 */
	protected $_key = null;

/**
 * Object of model `Bookmark`
 *
 * @var object
 */
	protected $_modelBookmark = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		parent::__construct($collection, $settings);

		$this->_modelBookmark = ClassRegistry::init('Bookmark');
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @throws InternalErrorException if Paginator Component is not loaded on
 *  target controller
 * @return void
 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);

		if (!$this->_controller->Components->loaded('Paginator')) {
			throw new InternalErrorException(__("Component '%s' is not found in target controller", 'Paginator'));
		}

		$this->_setKey($this->_createKey());
	}

/**
 * Create current page key
 *
 * @return string Return current page key as MD5 hash.
 */
	protected function _createKey() {
		$plugin = $this->_controller->request->param('plugin');
		$controller = $this->_controller->request->param('controller');
		$action = $this->_controller->request->param('action');
		$dataStr = serialize(compact('plugin', 'controller', 'action'));

		return md5($dataStr);
	}

/**
 * Return current page key
 *
 * @return string Return current page key.
 */
	protected function _getKey() {
		return $this->_key;
	}

/**
 * Set current page key
 *
 * @param string|null $key Current page key.
 * @return void
 */
	protected function _setKey($key = null) {
		$this->_key = $key;
	}

/**
 * Check pagination request is "Show as list".
 *
 * @return bool Success
 */
	protected function _isShowList() {
		return $this->_controller->request->param('named.show') === 'list';
	}

/**
 * Creates an array of bookmark information
 *
 * @return void
 */
	protected function _createBookmark() {
		$paramKeys = ['named', 'pass'];
		$dataKeys = ['FilterCond', 'FilterData'];
		$params = [];
		$data = [];
		$query = [];
		foreach ($paramKeys as $paramKey) {
			$params[$paramKey] = $this->_controller->request->param($paramKey);
		}
		foreach ($dataKeys as $dataKey) {
			$dataValue = $this->_controller->request->data($dataKey);
			if (!empty($dataValue)) {
				$data[$dataKey] = $dataValue;
			}
			$queryValue = $this->_controller->request->query('data.' . $dataKey);
			if (!empty($queryValue)) {
				$query[$dataKey] = $queryValue;
			}
		}

		return compact('params', 'data', 'query');
	}

/**
 * Restore bookmark information
 * 
 * @return bool Success
 */
	public function restoreBookmark() {
		if (!$this->_controller->request->is('get')
			|| !empty($this->_controller->request->data)
			|| !empty($this->_controller->request->query)
			|| $this->_isShowList()
		) {
			return false;
		}

		$key = $this->_getKey();
		$bookmark = $this->_modelBookmark->getBookmark($key);
		if (!$bookmark
			|| !$bookmark['Bookmark']['data']
			|| !is_array($bookmark['Bookmark']['data'])
		) {
			return false;
		}

		if (!empty($bookmark['Bookmark']['data']['params'])) {
			$this->_controller->request->params = array_merge(
				$this->_controller->request->params,
				$bookmark['Bookmark']['data']['params']
			);
		}
		if (!empty($bookmark['Bookmark']['data']['data'])) {
			$this->_controller->request->query = ['data' => $bookmark['Bookmark']['data']['data']];
		} elseif (!empty($bookmark['Bookmark']['data']['query'])) {
			$this->_controller->request->query = ['data' => $bookmark['Bookmark']['data']['query']];
		}

		return true;
	}

/**
 * Store bookmark information
 * 
 * @return bool Success
 */
	public function storeBookmark() {
		if ($this->_isShowList()) {
			return true;
		}

		$key = $this->_getKey();
		$bookmark = $this->_createBookmark();

		return $this->_modelBookmark->createBookmark($key, $bookmark);
	}

/**
 * Clear bookmark information
 * 
 * @return bool Success
 */
	public function clearBookmark() {
		$key = $this->_getKey();

		return $this->_modelBookmark->clearBookmark($key);
	}
}
