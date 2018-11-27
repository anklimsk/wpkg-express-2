<?php
/**
 * This file is the view file of the plugin. Used for rendering table
 *  a result of search.
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */
	App::uses('Hash', 'Utility');
	App::uses('Inflector', 'Utility');

if (!isset($query)) {
	$query = '';
}

if (!isset($queryCorrect)) {
	$queryCorrect = '';
}

if (!isset($correct)) {
	$correct = false;
}

if (!isset($search_targetDeep)) {
	$search_targetDeep = 0;
}

if (!isset($search_targetFields)) {
	$search_targetFields = [];
}

if (!isset($target)) {
	$target = [];
}

if (!isset($queryConfig)) {
	$queryConfig = [
		'modelConfig' => [],
		'anyPart' => false
	];
}

if (!isset($result) || !is_array($result)) {
	$result = [];
}

if (empty($result)) {
	return;
}

if (!empty($queryCorrect) && !$correct) {
	$query = $queryCorrect;
}

	$resultGroup = '';
	$resultTitle = __d('cake_search_info', 'Results of search "%s"', h($query));
	$targetKeys = array_flip($target);
	$resultTypesList = [];
foreach ($result as $resultType => $resultData) {
	if (!isset($queryConfig['modelConfig'][$resultType]['name'])) {
		continue;
	}

	$typeName = $queryConfig['modelConfig'][$resultType]['name'];
	if (!isset($search_targetFields[$typeName])) {
		continue;
	}
	if ($search_targetDeep < 1) {
		$target = [$search_targetFields[$typeName]];
	} else {
		$targetKeysType = array_intersect_key($search_targetFields[$typeName], $targetKeys);
		if (empty($targetKeysType)) {
			continue;
		}
		$target = array_keys($targetKeysType);
	}
	if ($queryConfig['anyPart']) {
		$target[] = CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART;
	}

	$resultTypesList[] = $this->Html->link(
		$typeName,
		['controller' => 'search', 'action' => 'search', 'plugin' => 'cake_search_info',
			'?' => http_build_query(compact(
				'query',
				'target'
			))]
	) . '&nbsp;' .
		$this->Html->tag(
			'span',
			$this->Number->format(
				$resultData['amount'],
				['thousands' => ' ', 'before' => '', 'places' => 0]
			),
			['class' => 'badge']
		);
}
if (!empty($resultTypesList)) {
	$resultGroup = $this->Html->nestedList(
		$resultTypesList,
		['class' => 'list-group'],
		['class' => 'list-group-item'],
		'ul'
	);
}

	$resultTotal = '';
if (isset($result['total']) && ($result['total'] > 0)) {
	$resultTotal = $this->Html->div(
		'panel-footer',
		__d(
			'cake_search_info',
			'Found %s %s.',
			$this->Number->format(
				$result['total'],
				['thousands' => ' ', 'before' => '', 'places' => 0]
			),
			__dn('cake_search_info', 'result', 'results', $result['total'])
		)
	);
}

	echo $this->Html->div(
		'panel panel-default',
		$this->Html->div('panel-heading', $this->Html->tag(
			'h2',
			$resultTitle,
			['class' => 'panel-title']
		)) .
		$resultGroup . $resultTotal
	);

	$highlightOptions = [
		'format' => '<mark>\1</mark>',
		'html' => true];

	$truncateOpt = [
		'ellipsis' => '...',
		'exact' => false,
		'html' => false
	];
	foreach ($result as $resultType => $resultData) {
		if (!isset($queryConfig['modelConfig'][$resultType]['fields']) || !is_array($queryConfig['modelConfig'][$resultType]['fields'])) {
			continue;
		}

		foreach ($resultData['data'] as $i => $resultDataItem) {
			if ($i === 0) {
				if (isset($queryConfig['modelConfig'][$resultType]['name'])) {
					$typeName = $queryConfig['modelConfig'][$resultType]['name'];
				} else {
					$typeName = $resultType;
				}
				echo $this->Html->div('page-header', $this->Html->tag('h4', $typeName));
			}

			$resultItem = '';
			$resultLink = false;
			foreach ($queryConfig['modelConfig'][$resultType]['fields'] as $fieldPath => $fieldName) {
				$resultDataText = (string)Hash::get($resultDataItem, $fieldPath);
				if (empty($resultDataText)) {
					$fieldValue = __d('cake_search_info', '&lt;None&gt;');
				} else {
					$fieldValue = $this->Text->highlight($this->Text->truncate(h($resultDataText), 100, $truncateOpt), $query, $highlightOptions);
					if (!$resultLink) {
						$resultLink = true;
						$idPath = $resultType . '.id';
						if (isset($queryConfig['modelConfig'][$resultType]['id'])) {
							$idPath = $queryConfig['modelConfig'][$resultType]['id'];
						}

						$id = (int)Hash::get($resultDataItem, $idPath);
						$urlConfig = [
							'controller' => null,
							'action' => 'view',
							'plugin' => null
						];
						if (isset($queryConfig['modelConfig'][$resultType]['url']) && !empty($queryConfig['modelConfig'][$resultType]['url']) &&
							is_array($queryConfig['modelConfig'][$resultType]['url'])) {
							$urlConfig = array_intersect_key($queryConfig['modelConfig'][$resultType]['url'], $urlConfig) + $urlConfig;
						}
						extract($urlConfig);
						if (empty($controller)) {
							$controller = Inflector::pluralize(mb_strtolower($resultType));
						}
						if (!empty($controller) && !empty($id)) {
							$urlResult = compact('controller', 'action', 'plugin') + [$id];
							$urlResult = $this->ViewExtension->addUserPrefixUrl($urlResult);
							$fieldValue = $this->Html->link($fieldValue, $urlResult, ['escape' => false, 'target' => '_blank']);
						}
					}
				}

				$resultItem .= $this->Html->tag('dt', $fieldName . ':');
				$resultItem .= $this->Html->tag('dd', $fieldValue);
			}

			if (!empty($resultItem)) {
				echo $this->Html->tag('dl', $resultItem, ['class' => 'dl-horizontal dl-ws-normal']);
				echo $this->Html->tag('hr');
			}
		}
	}

	echo $this->ViewExtension->barPaging(false, false, false, true);
