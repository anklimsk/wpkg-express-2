<?php
/**
 * This file is the view helper file of the plugin.
 * Check result View Helper.
 * Method to build HTML element with status text from status value
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */
App::uses('CakeInstallerAppHelper', 'CakeInstaller.View/Helper');

/**
 * Build HTML element with status text from status value.
 *
 * @package plugin.View.Helper
 */
class CheckResultHelper extends CakeInstallerAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = ['Html'];

/**
 * Build HTML element with status icon from status value
 *
 * @param int $state State for build HTML elemeth.
 *  Integer value:
 *   - `0` - Bad;
 *   - `1` - Warning;
 *   - `2` - Success.
 * @return string Result HTML element with status text
 */
	public function getStateElement($state = null) {
		if (is_bool($state) && ($state === true)) {
			$state = 2;
		}

		switch ((int)$state) {
			case 2:
				$stateText = $this->Html->tag('span', '', ['class' => 'fas fa-check']);
				$titleText = __d('cake_installer', 'Ok');
				$labelClass = 'label-success';
				break;
			case 1:
				$stateText = $this->Html->tag('span', '', ['class' => 'fas fa-minus']);
				$titleText = __d('cake_installer', 'Ok');
				$labelClass = 'label-warning';
				break;
			default:
				$stateText = $this->Html->tag('span', '', ['class' => 'fas fa-times']);
				$titleText = __d('cake_installer', 'Bad');
				$labelClass = 'label-danger';
		}

		return $this->Html->tag('span', $stateText, [
			'class' => 'label' . ' ' . $labelClass,
			'title' => $titleText, 'data-toggle' => 'tooltip']);
	}

/**
 * Return class for list element from status value
 *
 * @param int $state State for build HTML elemeth.
 *  Integer value:
 *   - `0` - Bad;
 *   - `1` - Warning;
 *   - `2` - Success.
 * @return string Return class name
 */
	public function getStateItemClass($state = null) {
		if (is_bool($state) && ($state === true)) {
			$state = 2;
		}

		switch ((int)$state) {
			case 2:
				$classItem = '';
				break;
			case 1:
				$classItem = 'list-group-item-warning';
				break;
			default:
				$classItem = 'list-group-item-danger';
		}

		return $classItem;
	}

/**
 * Return list of states
 *
 * @param array $list List of states for rendering in format:
 *  - key `textItem`, value - text of list item;
 *  - key `classItem`, value - class of list item.
 * @return string Return list of states
 */
	public function getStateList($list = null) {
		$result = '';
		if (empty($list)) {
			return $result;
		}

		$listText = '';
		foreach ($list as $listItem) {
			$classItem = '';
			if (is_array($listItem)) {
				if (!isset($listItem['textItem'])) {
					continue;
				}

				$textItem = $listItem['textItem'];
				if (isset($listItem['classItem']) && !empty($listItem['classItem'])) {
					$classItem = ' ' . $listItem['classItem'];
				}
			} else {
				$textItem = $listItem;
			}

			$listText .= $this->Html->tag('li', $textItem, ['class' => 'list-group-item' . $classItem]);
		}
		if (empty($listText)) {
			return $result;
		}

		$result = $this->Html->tag('ul', $listText, ['class' => 'list-group']);

		return $result;
	}
}
