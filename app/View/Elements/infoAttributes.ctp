<?php
/**
 * This file is the view file of the application. Used to render
 *  information about attributes.
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

App::uses('Hash', 'Utility');

if (!isset($attributes)) {
	$attributes = [];
}

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($showShort)) {
	$showShort = false;
}

if (!isset($displayInline)) {
	$displayInline = false;
}

if (empty($attributes)) {
	return;
}

if (isset($attributes[0])) {
	$attributes['Attribute'] = $attributes[0];
	unset($attributes[0]);
}

$listOs = [];
if (isset($attributes['Attribute']['listOs'])) {
	$listOs = $attributes['Attribute']['listOs'];
}

$listLangId = [];
if (isset($attributes['Attribute']['listLangId'])) {
	$listLangId = $attributes['Attribute']['listLangId'];
}

	$pcreParsing = $attributes['Attribute']['pcre_parsing'];
	if (isset($listOs[$attributes['Attribute']['os']])) {
		$attributes['Attribute']['os'] = $listOs[$attributes['Attribute']['os']];
	}
	$lcidFields = ['lcid', 'lcidOS'];
	foreach ($lcidFields as $lcidField) {
		if (!isset($attributes['Attribute'][$lcidField]) ||
			empty($attributes['Attribute'][$lcidField])) {
			continue;
		}

		$listLcId = explode(',', $attributes['Attribute'][$lcidField]);
		foreach ($listLcId as &$itemLcId) {
			if (isset($listLangId[$itemLcId])) {
				$itemLcId = __d('attribute_lcid', $listLangId[$itemLcId]);
			}
		}
		unset($itemLcId);
		$attributes['Attribute'][$lcidField] = $this->Text->toList($listLcId, __('or'));
	}

$displayData = [];
$tagName = 'dl';
$className = 'dl-horizontal';
$labelPostFix = ':';
if ($displayInline) {
	$labelPostFix = '';
	$tagName = 'samp';
	$className = null;
}
	if (!$showShort && !$displayInline) {
		$displayData[__('Attribute type') . $labelPostFix] = $fullName;
		$displayData[__('Parsing PCRE on attributes') . $labelPostFix] = $this->ViewExtension->yesNo($pcreParsing);
	}
	$pathValues = [
		__('Host name') => 'Attribute.hostname',
		__('OS') => 'Attribute.os',
		__('Architecture') => 'Attribute.architecture',
		__('IP addresses') => 'Attribute.ipaddresses',
		__('Groups') => 'Attribute.groups',
		__('Domain name') => 'Attribute.domainname',
		__('Language ID') => 'Attribute.lcid',
		__('Language ID OS') => 'Attribute.lcidOS',
	];
	foreach ($pathValues as $label => $path) {
		$itemVal = Hash::get($attributes, $path);
		if (empty($itemVal) &&
			($showShort || $displayInline)) {
			continue;
		}
		$displayData[$label . $labelPostFix] = $this->ViewExtension->showEmpty(h($itemVal));
	}

	$result = '';
	foreach ($displayData as $label => $value) {
		if ($displayInline) {
			$result .= (empty($result) ? '' : '; ') .
				$this->Html->tag('strong', $label) . ' = ' . $this->Html->tag('var', $value);
		} else {
			$result .= $this->Html->tag('dt', $label);
			$result .= $this->Html->tag('dd', $value);
		}
	}
	if (empty($result)) {
		return;
	}

	if ($displayInline) {
		$result = '[' . $result . ']';
	}
	echo $this->Html->tag($tagName, $result, ['class' => $className]);
