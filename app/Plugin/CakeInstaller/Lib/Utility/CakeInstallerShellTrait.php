<?php
/**
 * This file is the trait CakeInstallerShellTrait
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Lib.Utility
 */

/**
 * CakeInstallerShellTrait trait
 *
 */
trait CakeInstallerShellTrait {

/**
 * Input value from list.
 *
 * @param Shell $shell Shell using this function
 * @param array $inputList Array of value for choose
 * @param string $inputMessage Message of input line.
 * @param string $titleMessage Title of list values.
 * @param null|string $currentValue Current value (mark `*`).
 * @param bool $useExit If True, Add list item `Exit`.
 * @param int $defaultIndex Default index of value from list.
 *  If negative - position from end of list.
 * @param int $linesOnPage Amount lines of value on page.
 * @see CakeInstallerShell::_setuilang() CakeInstallerShell::_setuilang() Store language of UI installer,
 *  Create and write language of UI application in the settings file.
 * @return null|bool Success, or Null on failure.
 */
	public function inputFromList(Shell $shell, $inputList = null, $inputMessage = null, $titleMessage = null, $currentValue = null, $useExit = true, $defaultIndex = -1, $linesOnPage = 15) {
		if (empty($inputList) || !is_array($inputList)) {
			return null;
		}

		if (empty($inputMessage)) {
			$inputMessage = __d('cake_installer', 'Input the number from list');
		}

		$defaultIndex = (int)$defaultIndex;
		$linesOnPage = (int)$linesOnPage;
		if ($linesOnPage < 10) {
			$linesOnPage = 10;
		}
		$inputListPages = array_chunk($inputList, $linesOnPage, true);
		$pageAmount = count($inputListPages);
		$page = 1;
		$needClear = false;
		while ($page <= $pageAmount) {
			if ($needClear) {
				$shell->clear();
			}
			if (!empty($titleMessage)) {
				$shell->out($titleMessage);
				$shell->hr();
			}
			$index = 1;
			$currentValueIndex = null;
			$indexedList = [];
			$inputListPage = $inputListPages[$page - 1];
			if (($page == 1) && $useExit) {
				$inputListPage['exit'] = __d('cake_installer', 'Exit');
			}
			if (($page > 1) || ($page < $pageAmount)) {
				$inputListPage['hr'] = '';
				if ($page > 1) {
					$inputListPage['prevPage'] = '<- ' . __d('cake_installer', 'Prev page');
				}
				if ($page < $pageAmount) {
					$inputListPage['nextPage'] = __d('cake_installer', 'Next page') . ' ->';
				}
			}
			foreach ($inputListPage as $listItemName => $listItemDescription) {
				if ($listItemName === 'hr') {
					$shell->hr();
					continue;
				}

				if ($currentValue === $listItemName) {
					$listItemDescription = '<info>*' . $listItemDescription . '</info>';
					$currentValueIndex = $index;
				}
				$shell->out(sprintf('%4d. %s', $index, $listItemDescription));
				$indexedList[$index] = $listItemName;
				$index++;
			}
			if ($index > 1) {
				$index--;
			}
			$defaultIndexPage = $defaultIndex;
			if (!empty($currentValueIndex)) {
				$defaultIndexPage = $currentValueIndex;
			} elseif ($defaultIndex > 0) {
				if ($defaultIndex > $index) {
					$defaultIndexPage = $index;
				}
			} elseif ($defaultIndex == 0) {
				$defaultIndexPage = 1;
			} else {
				if ($defaultIndex + $index <= 0) {
					$defaultIndexPage = 1;
				} else {
					$defaultIndexPage = $index + $defaultIndex + 1;
				}
			}
			$shell->hr();
			if ($pageAmount > 1) {
				$shell->out(__d('cake_installer', 'Page %d of %d', $page, $pageAmount));
			}
			$inputMessageText = $inputMessage . sprintf(' [%d-%d]:', 1, $index);
			do {
				$selectIndex = $shell->in($inputMessageText, null, $defaultIndexPage);
			} while ((is_string($selectIndex) && !ctype_digit($selectIndex)) || ($selectIndex < 1) ||
				($selectIndex > $index) || !isset($indexedList[$selectIndex]));

			switch ($indexedList[$selectIndex]) {
				case 'exit':
					return $shell->_stop();
				//break;
				case 'nextPage':
					$page++;
					$needClear = true;
					break;
				case 'prevPage':
					$page--;
					$needClear = true;
					break;
				default:
					return $indexedList[$selectIndex];
			}
		}

		return null;
	}
}
