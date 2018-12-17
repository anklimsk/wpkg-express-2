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
 * @copyright Copyright 2018, Andrey Klimov.
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

if (!isset($btnActions)) {
	$btnActions = null;
}

if (!isset($label)) {
	$label = null;
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
						'action-type' => 'modal',
						'data-modal-size' => 'lg'
					]
				);
			}
		}
		$attributes = '';
		$checks = '';
		if (isset($dependencyItem['Attribute']) && !empty($dependencyItem['Attribute'])) {
			$attributes = ' ' . $this->element('infoAttributes', ['attributes' => $dependencyItem['Attribute'], 'displayInline' => true]);
		}
		if (isset($dependencyItem['Check']) && !empty($dependencyItem['Check'])) {
			$checks = $this->element('infoChecks', ['checks' => $dependencyItem['Check'], 'nest' => true, 'expandAll' => false]);
		}
		if (!empty($modelName) && isset($dependencyItem[$modelName])) {
			$dependencyItem = $dependencyItem[$modelName];
		}

		$dependencyName = '';
		if (isset($dependencyItem['name'])) {
			$dependencyName = h($dependencyItem['name']);
		} elseif (isset($dependencyItem['id_text'])) {
			 $dependencyName = h($dependencyItem['id_text']);
		} elseif (isset($dependencyItem['id'])) {
			$dependencyName = $dependencyItem['id'];
		}
		if (!$dependencyItem['enabled']) {
			$dependencyName = $this->Html->tag('s', $dependencyName);
		}
		if (!empty($controllerName) && isset($dependencyItem['id'])) {
			$dependencyName = $this->ViewExtension->popupModalLink(
				$dependencyName,
				['controller' => $controllerName, 'action' => 'view', $dependencyItem['id']],
				[
					'data-modal-size' => 'lg',
					'data-popover-size' => 'lg'
				]
			);
		}
		$row = $this->Html->div('dependency', $this->Html->tag('span', $dependencyName . $attributes) . ' ' . $this->Html->tag('span', $actions, ['class' => 'action hide-popup'])) . $checks;
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
