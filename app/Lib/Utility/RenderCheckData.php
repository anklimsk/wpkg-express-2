<?php
/**
 * This file is the util file of the application.
 * RenderCheckData Utility.
 * Methods to make check data more readable.
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
 * @package app.Lib.Utility
 */

App::uses('CakeText', 'Utility');
App::uses('CakeNumber', 'Utility');

/**
 * Check helper library.
 * Methods to make check data more readable.
 *
 * @package app.Lib.Utility
 */
class RenderCheckData {

/**
 * Return information of check.
 *
 * @param array $data Data of check for processing
 * @return string Return information of check.
 */
	public static function getTextCheckCondition($data = []) {
		$out = '';
		if (empty($data) || !is_array($data)) {
			return $out;
		}

		switch ($data['type']) {
			case CHECK_TYPE_LOGICAL:
				$conditionName = __d('logical_check_type', constValToLcSingle('CHECK_CONDITION_LOGICAL_', $data['condition'], ' '));
				$out = __('Logical %s', mb_strtoupper($conditionName));
				break;
			case CHECK_TYPE_REGISTRY:
				$path = h($data['path']);
				switch ($data['condition']) {
					case CHECK_CONDITION_REGISTRY_EXISTS:
						$conditionName = __('exists');
						break;
					case CHECK_CONDITION_REGISTRY_EQUALS:
						$conditionName = __("equals '%s'", h($data['value']));
						break;
					default:
						$conditionName = __('unknown');
				}
				$out = __("Registry path '%s' %s", $path, $conditionName);
				break;
			case CHECK_TYPE_FILE:
				$path = h($data['path']);
				$conditionSymb = null;
				switch ($data['condition']) {
					case CHECK_CONDITION_FILE_EXISTS:
						$conditionName = __('exists');
						break;
					case CHECK_CONDITION_FILE_SIZE_EQUALS:
						$size = CakeNumber::toReadableSize((float)$data['value']);
						$sizeBytes = CakeNumber::format($data['value']) . ' ' . __('bytes');
						$sizeText = '<span data-toggle="tooltip" title="' . $sizeBytes . '" class="help">' .
							$size . '</span>';
						$conditionName = __('has a size = %s', $sizeText);
						break;
					case CHECK_CONDITION_FILE_VERSION_SMALLER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_FILE_VERSION_LESS_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&le;';
						}
					case CHECK_CONDITION_FILE_VERSION_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_FILE_VERSION_GREATER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
					case CHECK_CONDITION_FILE_VERSION_GREATER_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&ge;';
						}
						$conditionName = __('has a version %s %s', $conditionSymb, h($data['value']));
						break;
					case CHECK_CONDITION_FILE_DATE_MODIFY_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_FILE_DATE_MODIFY_NEWER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_FILE_DATE_MODIFY_OLDER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
						$conditionName = __('has a modify date %s %s', $conditionSymb, h($data['value']));
						break;
					case CHECK_CONDITION_FILE_DATE_CREATE_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_FILE_DATE_CREATE_NEWER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_FILE_DATE_CREATE_OLDER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
						$conditionName = __('has a create date %s %s', $conditionSymb, h($data['value']));
						break;
					case CHECK_CONDITION_FILE_DATE_ACCESS_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_FILE_DATE_ACCESS_NEWER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_FILE_DATE_ACCESS_OLDER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
						$conditionName = __('has a access date %s %s', $conditionSymb, h($data['value']));
						break;
					default:
						$conditionName = __('unknown');
				}
				$out = __("File '%s' %s", $path, $conditionName);
				break;
			case CHECK_TYPE_EXECUTE:
				$command = h($data['path']);
				$conditionSymb = null;
				switch ($data['condition']) {
					case CHECK_CONDITION_EXECUTE_EXIT_CODE_SMALLER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_EXECUTE_EXIT_CODE_LESS_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&le;';
						}
					case CHECK_CONDITION_EXECUTE_EXIT_CODE_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
					case CHECK_CONDITION_EXECUTE_EXIT_CODE_GREATER_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&ge;';
						}
						$conditionName = $conditionSymb . ' ' . h($data['value']);
						break;
					default:
						$conditionName = __('unknown');
				}
				$out = __("Execute '%s' and ensure the returned exit code %s", $command, $conditionName);
				break;
			case CHECK_TYPE_UNINSTALL:
				$path = h($data['path']);
				$conditionSymb = null;
				switch ($data['condition']) {
					case CHECK_CONDITION_UNINSTALL_EXISTS:
						$conditionName = __('exists');
						break;
					case CHECK_CONDITION_UNINSTALL_VERSION_SMALLER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&lt;';
						}
					case CHECK_CONDITION_UNINSTALL_VERSION_LESS_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&le;';
						}
					case CHECK_CONDITION_UNINSTALL_VERSION_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '=';
						}
					case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN:
						if (empty($conditionSymb)) {
							$conditionSymb = '&gt;';
						}
					case CHECK_CONDITION_UNINSTALL_VERSION_GREATER_THAN_OR_EQUAL_TO:
						if (empty($conditionSymb)) {
							$conditionSymb = '&ge;';
						}
						$conditionName = __("has a version %s '%s'", $conditionSymb, h($data['value']));
						break;
					default:
						$conditionName = __('unknown');
				}
				$out = __("Uninstall path '%s' %s", $path, $conditionName);
				break;
			case CHECK_TYPE_HOST:
				switch ($data['condition']) {
					case CHECK_CONDITION_HOST_NAME:
						$conditionName = __("Host name is '%s'", h($data['value']));
						break;
					case CHECK_CONDITION_HOST_OS:
						$osName = ucfirst(constValToLcSingle('ATTRIBUTE_OS_', $data['value'], ' ', false, false));
						if (!empty($osName)) {
							$osName = __d('attribute_os', $osName);
						} else {
							$osName = h($data['value']);
						}
						$conditionName = __("Host OS is '%s'", $osName);
						break;
					case CHECK_CONDITION_HOST_ARCHITECTURE:
						$arch = constValToLcSingle('ATTRIBUTE_ARCHITECTURE_', $data['value'], ' ', false, false);
						if (empty($arch)) {
							$arch = h($data['value']);
						}
						$conditionName = __("Host architecture is '%s'", $arch);
						break;
					case CHECK_CONDITION_HOST_IP_ADDRESSES:
						$conditionName = __("Host IP addresses is '%s'", h($data['value']));
						break;
					case CHECK_CONDITION_HOST_DOMAIN_NAME:
						$conditionName = __("Host domain name is '%s'", h($data['value']));
						break;
					case CHECK_CONDITION_HOST_GROUPS:
						$conditionName = __("Host is group member '%s'", h($data['value']));
						break;
					case CHECK_CONDITION_HOST_LCID:
					case CHECK_CONDITION_HOST_LCID_OS:
						if (!empty($data['value'])) {
							$listLcId = explode(',', $data['value']);
							foreach ($listLcId as &$itemLcId) {
								$lcName = ucfirst(constValToLcSingle('ATTRIBUTE_LCID_', $itemLcId, false, false, false));
								if (!empty($lcName)) {
									$itemLcId = __d('attribute_lcid', $lcName);
								} else {
									$itemLcId = h($itemLcId);
								}
							}
							unset($itemLcId);
							$value = CakeText::toList($listLcId, __('and'));
						}
						if ($data['condition'] == CHECK_CONDITION_HOST_LCID) {
							$conditionName = __("Host language ID is '%s'", $value);
						} else {
							$conditionName = __("Host language ID OS is '%s'", $value);
						}
						break;
					case CHECK_CONDITION_HOST_ENVIRONMENT:
						$conditionName = __("Host environment is '%s'", h($data['value']));
						break;
					default:
						$conditionName = __('Host is unknown');
				}
				$out = $conditionName;
				break;
		}

		return $out;
	}
}
