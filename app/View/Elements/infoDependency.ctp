<?php
/**
 * This file is the view file of the application. Used to render
 *  information about dependencies.
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
 * @package app.View.Elements
 */

if (!isset($dependencies)) {
	$dependencies = [];
}

if (!isset($bindLimit)) {
	$bindLimit = 0;
}

if (!isset($controllerName)) {
	$controllerName = null;
}

if (!isset($attrRefType)) {
	$attrRefType = null;
}

if (!isset($attrRefNode)) {
	$attrRefNode = null;
}

if (!isset($checkRefType)) {
	$checkRefType = null;
}

if (!isset($dependencyType)) {
	$dependencyType = null;
}

if (!isset($btnActions)) {
	$btnActions = null;
}

if (!isset($label)) {
	$label = null;
}

if (!isset($extInfoElement)) {
	$extInfoElement = null;
}

if (!isset($extBtnElement)) {
	$extBtnElement = null;
}

if (!isset($includeIdText)) {
	$includeIdText = false;
}

$useExtInfoElement = false;
if (!empty($extInfoElement) && $this->elementExists($extInfoElement)) {
	$useExtInfoElement = true;
}

$useExtBtnElement = false;
if (!empty($extBtnElement) && $this->elementExists($extBtnElement)) {
	$useExtBtnElement = true;
}

$list = [];
	foreach ($dependencies as $dependencyItem) {
		$actions = '';
		if (isset($dependencyItem['id'])) {
			if (!empty($attrRefType) && !empty($attrRefNode)) {
				$actions .= $this->ViewExtension->buttonLink(
					'fas fa-tasks',
					'btn-success',
					['controller' => 'attributes', 'action' => 'modify', $attrRefType, $attrRefNode, $dependencyItem['id']],
					[
						'title' => __('Edit attributes'),
						'action-type' => 'modal',
					]
				);
			}
			if (!empty($checkRefType)) {
				$actions .= $this->ViewExtension->buttonLink(
					'far fa-check-circle',
					'btn-success',
					['controller' => 'checks', 'action' => 'view', $checkRefType, $dependencyItem['id']],
					[
						'title' => __('Edit checks'),
						'action-type' => 'modal',
						'data-modal-size' => 'lg'
					]
				);
			}
		}
		if ($useExtBtnElement) {
			$actions .= $this->element($extBtnElement, ['data' => $dependencyItem]);
		}
		if (!empty($dependencyType) && isset($dependencyItem['id'])) {
			$actions .= $this->ViewExtension->buttonLink(
				'fas fa-trash-alt',
				'btn-danger',
				['controller' => 'dependencies', 'action' => 'delete', $dependencyType, $dependencyItem['id']],
				[
					'title' => __('Delete record'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this record?')
				]
			);
		}
		$dependencyInfo = [];
		$dependencyName = '';
		$checks = '';
		$dependencyItemData = $dependencyItem;
		if (!empty($modelName) && isset($dependencyItem[$modelName])) {
			$dependencyItemData = $dependencyItem[$modelName];
		}
		if (isset($dependencyItemData['name'])) {
			$dependencyName = h($dependencyItemData['name']);
			if ($includeIdText && isset($dependencyItemData['id_text'])) {
				$dependencyName .= ' (' . h($dependencyItemData['id_text']) . ')';
			}
		} elseif (isset($dependencyItemData['id_text'])) {
			 $dependencyName = h($dependencyItemData['id_text']);
		} elseif (isset($dependencyItemData['id'])) {
			$dependencyName = $dependencyItemData['id'];
		}
		if (!$dependencyItemData['enabled']) {
			$dependencyName = $this->Html->tag('s', $dependencyName);
		}
		if (!empty($controllerName) && isset($dependencyItemData['id'])) {
			$dependencyName = $this->ViewExtension->popupModalLink(
				$dependencyName,
				['controller' => $controllerName, 'action' => 'view', $dependencyItemData['id']],
				[
					'data-modal-size' => 'lg',
					'data-popover-size' => 'lg'
				]
			);
		}
		$dependencyInfo[] = $dependencyName;
		if (isset($dependencyItem['Attribute']) && !empty($dependencyItem['Attribute'])) {
			$attributes = $this->element('infoAttributes', ['attributes' => $dependencyItem['Attribute'],
				'displayInline' => true]);
			if (!empty($attributes)) {
				$dependencyInfo[] = $attributes;
			}
		}
		if (isset($dependencyItem['Check']) && !empty($dependencyItem['Check'])) {
			$checks = $this->element('infoChecks', ['checks' => $dependencyItem['Check'], 'nest' => true,
				'expandAll' => false, 'displayItalics' => true]);
		}
		if ($useExtInfoElement) {
			$extInfo = $this->element($extInfoElement, ['data' => $dependencyItem]);
			if (!empty($extInfo)) {
				$dependencyInfo[] = $extInfo;
			}
		}
		$dependencyLabel = implode(' ', $dependencyInfo);
		$row = $this->Html->div('dependency', $this->Html->tag('span', $dependencyLabel) .
			' ' . $this->Html->tag('span', $actions, ['class' => 'action hide-popup'])) . $checks;
		$list[] = $row;
	}

	$listDependencies = $this->ViewExtension->showEmpty($list,
		$this->ViewExtension->collapsibleList($list, $bindLimit));

	$htmlListDependencies = '';
	if (!empty($btnActions)) {
		$htmlListDependencies .= $this->Html->div('pull-right hide-popup', $btnActions);
	}
	$htmlListDependencies .= $this->Html->div('pull-left bottom-buffer', $listDependencies);

	if (empty($label)) {
		echo $htmlListDependencies;
		return;
	}
?>
<dl class="dl-horizontal">
<?php
	echo $this->Html->tag('dt', $label . ':');
	echo $this->Html->tag('dd', $htmlListDependencies);
?>
</dl>
