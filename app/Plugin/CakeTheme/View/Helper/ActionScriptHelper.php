<?php
/**
 * This file is the helper file of the application.
 * Specific files Helper.
 * Methods to retrieve specific CSS of JS files for action View
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeThemeAppHelper', 'CakeTheme.View/Helper');
App::uses('File', 'Utility');

/**
 * Specific files helper used to retrieve specific CSS of JS files for action View.
 *
 * @package plugin.View.Helper
 */
class ActionScriptHelper extends CakeThemeAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = ['Html'];

/**
 * Postfix for minimized files.
 *
 * @var string
 */
	protected $_minPostfix = '.min';

/**
 * Get postfix for minimized files. Default - `.min`.
 *
 * @return string Postfix for minimized files.
 */
	protected function _getMinPostfix() {
		return (string)$this->_minPostfix;
	}

/**
 * Return version from timestamp.
 *
 * @param string|int $timestamp Time stamp of modifications file.
 * @return string Version of file in hex.
 */
	protected function _createFileVersion($timestamp = null) {
		if (!is_int($timestamp)) {
			$timestamp = time();
		}

		$result = dechex($timestamp);

		return $result;
	}

/**
 * Replacing directory separator for Windows from backslash to slash.
 *
 * @param string $path Path to replacing.
 * @return string|bool Return String of path, or False on failure.
 */
	protected function _prepareFilePath($path = null) {
		if (empty($path)) {
			return false;
		}

		if (DIRECTORY_SEPARATOR !== '\\') {
			return $path;
		}

		$path = str_replace('\\', '/', $path);

		return $path;
	}

/**
 * Return specific file for action View
 *
 * @param string $specificFullPath Full path to specific folder.
 * @param string $specificPath Path to specific folder begin from
 *  /web_root/css/ or /web_root/js/.
 * @param string $specificFileName File name of specific file.
 * @return string|bool Return String of path to specific file, or False on failure.
 */
	protected function _getIncludeFilePath($specificFullPath = null, $specificPath = null, $specificFileName = null) {
		if (empty($specificFullPath) || empty($specificPath) ||
			empty($specificFileName)) {
			return false;
		}

		$oFile = new File($specificFullPath . $specificFileName);
		if (!$oFile->exists()) {
			return false;
		}

		$includeFilePath = $this->_prepareFilePath($specificPath . $specificFileName);
		$lastChange = $oFile->lastChange();
		$version = $this->_createFileVersion($lastChange);
		$result = $includeFilePath . '?v=' . $version;

		return $result;
	}

/**
 * Return list of specific files for action View.
 *
 * @param string $type Type of specific files: `CSS` or `JS`.
 * @param string|array $params List of sub folders in `specific` folder for retrieve
 *  list of specific files.
 * @param bool $includeOnlyParam If True, to include in the result only those
 *  files that are listed in the parameter $params. If false to include in the result
 *  all of the specific files found in the parameter $params.
 * @param bool $useUserRolePrefix If False, use only action name without route prefix.
 * @return array List of specific files for action View.
 */
	public function getFilesForAction($type = 'css', $params = [], $includeOnlyParam = false, $useUserRolePrefix = false) {
		$result = [];
		$typeLow = mb_strtolower($type);
		$typeUp = mb_strtoupper($type);
		if (!in_array($typeLow, ['css', 'js'])) {
			return $result;
		}

		$specificDir = mb_strtolower((string)$this->request->param('controller'));
		if (empty($specificDir)) {
			return $result;
		}

		$action = $this->request->param('action');
		if (!$useUserRolePrefix && Configure::check('Routing.prefixes')) {
			$prefixes = (array)Configure::read('Routing.prefixes');

			foreach ($prefixes as $prefix) {
				if (mb_stripos($action, $prefix . '_') === 0) {
					$action = mb_substr($action, mb_strlen($prefix . '_'));
				}
			}
		}
		$specificFiles = [$action];

		$specificFilesExt = $this->_View->get('specific' . $typeUp);
		if (!empty($specificFilesExt)) {
			if (!is_array($specificFilesExt)) {
				$specificFilesExt = [$specificFilesExt];
			}
			$specificFiles = array_merge($specificFiles, $specificFilesExt);
		}

		if (!empty($params)) {
			if (!is_array($params)) {
				$params = [$params];
			}
			foreach ($params as &$param) {
				$param = mb_strtolower($param);
			}
			unset($param);
		} else {
			$params = [];
		}

		$stamp = Configure::read('Asset.timestamp');
		$timestampEnabled = $stamp === 'force' || ($stamp === true && Configure::read('debug') > 0);

		if (!$timestampEnabled) {
			$cacheKeyData = compact('typeLow', 'specificDir', 'specificFiles', 'params');
			$cacheKey = md5(serialize($cacheKeyData));
			$cached = Cache::read($cacheKey, CAKE_THEME_CACHE_KEY_SPECIFIC_FILES);
			if (!empty($cached)) {
				return $cached;
			}
		}

		$minPostfix = $this->_getMinPostfix();
		$wwwRoot = Configure::read('App.www_root');
		$sysPath = [];
		$sysPath['CSS'] = $wwwRoot . 'css' . DS;
		$sysPath['JS'] = $wwwRoot . 'js' . DS;

		$specificPath = constant('CAKE_THEME_SPECIFIC_' . $typeUp . '_DIR') . DS;
		$specificFullPath = $sysPath[$typeUp] . $specificPath;
		$specificPathWithSpecificDir = $specificPath . $specificDir . DS;
		$specificFullPathWithSpecificDir = $specificFullPath . $specificDir . DS;
		$specificExt = '.' . $typeLow;
		foreach ($specificFiles as $specificFile) {
			$specificPathParam = $specificPathWithSpecificDir;
			$specificFullPathParam = $specificFullPathWithSpecificDir;
			if (!empty($specificFile)) {
				$specificFile = mb_strtolower($specificFile);
				if (stripos($specificFile, DS) !== false) {
					$specificPathParam = $specificPath;
					$specificFullPathParam = $specificFullPath;
				}
			}
			if (empty($params) || (!empty($params) && !$includeOnlyParam)) {
				$specificFileName = $specificFile . $minPostfix . $specificExt;
				$resultItem = $this->_getIncludeFilePath($specificFullPathParam, $specificPathParam, $specificFileName);
				if ($resultItem !== false) {
					$result[] = $resultItem;
				} else {
					$specificFileName = $specificFile . $specificExt;
					$resultItem = $this->_getIncludeFilePath($specificFullPathParam, $specificPathParam, $specificFileName);
					if ($resultItem !== false) {
						$result[] = $resultItem;
					}
				}
			}

			$path = $specificPathWithSpecificDir;
			$pathFull = $specificFullPathWithSpecificDir;
			foreach ($params as $paramPath) {
				$path .= $paramPath . DS;
				$pathFull .= $paramPath . DS;
				$specificFileName = $specificFile . $minPostfix . $specificExt;
				$resultItem = $this->_getIncludeFilePath($pathFull, $path, $specificFileName);
				if ($resultItem !== false) {
					$result[] = $resultItem;
				} else {
					$specificFileName = $specificFile . $specificExt;
					$resultItem = $this->_getIncludeFilePath($pathFull, $path, $specificFileName);
					if ($resultItem !== false) {
						$result[] = $resultItem;
					}
				}
			}
		}
		if (!$timestampEnabled) {
			Cache::write($cacheKey, $result, CAKE_THEME_CACHE_KEY_SPECIFIC_FILES);
		}

		return $result;
	}

/**
 * Returns one or many `<script>` tags depending on the number of specific files for action View scripts.
 *
 * @param array|bool $options Array of options, and html attributes see above. If boolean sets $options['inline'] = value
 *   included before. See HtmlHelper::script();
 * @param array|string $params List of sub folders in `specific` folder for retrieve
 *   list of specific files.
 * @param bool $includeOnlyParam If True, to include in the result only those
 *   files that are listed in the parameter $params. If false to include in the result
 *   all of the specific files found in the parameter $params.
 * @param bool $useUserRolePrefix If False, use only action name without route prefix.
 * @return mixed String of `<script />` tags or null if $inline is false or if $once is true and the file has been
 * @see ActionScriptHelper::getFilesForAction() Return list of specific files for action View
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::script
 */
	public function script($options = [], $params = [], $includeOnlyParam = false, $useUserRolePrefix = false) {
		$scripts = $this->getFilesForAction('js', $params, $includeOnlyParam, $useUserRolePrefix);
		if (empty($scripts)) {
			return null;
		}

		return $this->Html->script($scripts, $options);
	}

/**
 * Creates a link element for specific files for action View CSS stylesheets.
 *
 * @param array $options Array of options and HTML arguments.
 * @param array|string $params List of sub folders in `specific` folder for retrieve
 *   list of specific files.
 * @param bool $includeOnlyParam If True, to include in the result only those
 *   files that are listed in the parameter $params. If false to include in the result
 *   all of the specific files found in the parameter $params.
 * @param bool $useUserRolePrefix If False, use only action name without route prefix.
 * @return string CSS `<link />` or `<style />` tag, depending on the type of link. See HtmlHelper::css();
 * @see ActionScriptHelper::getFilesForAction() Return list of specific files for action View
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::css
 */
	public function css($options = [], $params = [], $includeOnlyParam = false, $useUserRolePrefix = false) {
		$css = $this->getFilesForAction('css', $params, $includeOnlyParam, $useUserRolePrefix);
		if (empty($css)) {
			return null;
		}

		return $this->Html->css($css, $options);
	}
}
