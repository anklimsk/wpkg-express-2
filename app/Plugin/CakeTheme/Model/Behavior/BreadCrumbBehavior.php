<?php
/**
 * This file is the behavior file of the plugin. Is used for getting
 *  information for creating breadcrumb navigation.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('Inflector', 'Utility');
App::uses('CakeText', 'Utility');

/**
 * The behavior is used for getting nformation for creating breadcrumb navigation.
 *
 * @package plugin.Model.Behavior
 */
class BreadCrumbBehavior extends ModelBehavior {

/**
 * Return name of model that uses this behavior.
 *
 * @param Model $model Model using this behavior.
 * @param bool $toLowerCase If True, return the model name in lowercase.
 * @return string Return name of model that uses this behavior.
 */
	public function getModelName(Model $model, $toLowerCase = false) {
		$modelName = $model->name;
		if ($toLowerCase) {
			$modelName = mb_strtolower($modelName);
		}

		return $modelName;
	}

/**
 * Return plural name of model that uses this behavior.
 *
 * @param Model $model Model using this behavior.
 * @param bool $toLowerCase If True, return the model name in lowercase.
 * @return string Return plural name of model that uses this behavior.
 */
	public function getModelNamePlural(Model $model, $toLowerCase = false) {
		$modelName = $this->getModelName($model, $toLowerCase);

		return Inflector::pluralize($modelName);
	}

/**
 * Return plugin name.
 *
 * @param Model $model Model using this behavior.
 * @return string Return plugin name for breadcrumb.
 */
	public function getPluginName(Model $model) {
		$pluginName = null;

		return $pluginName;
	}

/**
 * Return controller name.
 *
 * @param Model $model Model using this behavior.
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName(Model $model) {
		$modelNamePlural = $this->getModelNamePlural($model, false);
		$controllerName = mb_strtolower(Inflector::underscore($modelNamePlural));

		return $controllerName;
	}

/**
 * Return action name.
 *
 * @param Model $model Model using this behavior.
 * @return string Return action name for breadcrumb.
 */
	public function getActionName(Model $model) {
		$actionName = 'view';

		return $actionName;
	}

/**
 * Return name of group data.
 *
 * @param Model $model Model using this behavior.
 * @return string Return name of group data
 */
	public function getGroupName(Model $model) {
		$modelNamePlural = $this->getModelNamePlural($model);
		$groupNameCamel = Inflector::humanize(Inflector::underscore($modelNamePlural));
		$groupName = mb_ucfirst(mb_strtolower($groupNameCamel));

		return $groupName;
	}

/**
 * Return name of data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getName(Model $model, $id = null) {
		if (empty($id) || empty($model->displayField)) {
			return false;
		}
		if (is_array($id)) {
			if (!isset($id[$model->alias][$model->displayField])) {
				return false;
			}

			$result = $id[$model->alias][$model->displayField];
		} else {
			$model->id = $id;
			$result = $model->field($model->displayField);
		}

		return $result;
	}

/**
 * Return full name of data.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving full name.
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullName(Model $model, $id = null) {
		$name = $this->getName($model, $id);
		if (empty($name)) {
			return false;
		}

		$modelName = $this->getModelName($model);
		$result = $modelName . ' \'' . $name . '\'';

		return $result;
	}

/**
 * Return an array of information for creating a breadcrumb.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param array|bool|null $link URL for breadcrumb or False to disable auto creation.
 * @param bool $escape Escape conversion HTML special characters to HTML entities.
 * @return array Return an array of information for creating a breadcrumb.
 */
	public function createBreadcrumb(Model $model, $id = null, $link = null, $escape = true) {
		$result = [];
		if (empty($id)) {
			$name = $model->getGroupName();
		} else {
			$name = $model->getName($id);
		}
		if (empty($name)) {
			return $result;
		}
		if ($escape) {
			$name = h($name);
		}

		if ($link === false) {
			$link = null;
		} else {
			if (!is_array($link)) {
				$link = [];
			}
			$plugin = $model->getPluginName();
			$controller = $model->getControllerName();
			if (empty($id)) {
				$action = 'index';
			} else {
				$action = $model->getActionName();
				if (empty($link)) {
					$link[] = $id;
				}
			}
			$link += compact('plugin', 'controller', 'action');
		}

		$name = CakeText::truncate($name, CAKE_THEME_BREADCRUMBS_TEXT_LIMIT);
		$result = [
			$name,
			$link
		];

		return $result;
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param Model $model Model using this behavior.
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo(Model $model, $id = null, $includeRoot = null) {
		if (!empty($id) && ($includeRoot === null)) {
			$includeRoot = true;
		}

		$result = [];
		$root = [];
		if ($includeRoot) {
			$root = $model->getBreadcrumbInfo();
			if (empty($root)) {
				return $result;
			}
		}

		$info = $model->createBreadcrumb($id);
		if (empty($info)) {
			return $result;
		}

		if (!empty($root)) {
			$result = $root;
		}
		$result[] = $info;

		return $result;
	}
}
