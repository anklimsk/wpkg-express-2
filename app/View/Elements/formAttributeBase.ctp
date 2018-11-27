<?php
/**
 * This file is the view file of the application. Used to render
 *  form for editing attributes.
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

if (!isset($fullName)) {
	$fullName = null;
}

if (!isset($listOs)) {
	$listOs = [];
}

if (!isset($listArch)) {
	$listArch = [];
}

if (!isset($listLangId)) {
	$listLangId = [];
}

if (!isset($isAddAction)) {
	$isAddAction = false;
}

	$pcreParsing = (bool)$this->request->data('Attribute.pcre_parsing');
	$modelName = 'Attribute';
	$legend = __('Attributes');
	$inputStatic = [
		$modelName . '.full_name' => ['label' => __('Attribute type') . ':',
			'value' => h($fullName), 'escape' => false],
	];
	$pcreParsingOptionsOs = [
		'title' => __('This contains the full description of the operations system, which consists of the following parts: OS-caption; OS-description; CSD-version (usually the service pack); OS-version.')
	];
	$pcreParsingOptionsLcid = [
		'title' => __('This contains the language identifier of the operating system (e.g. 407,c07,1407).'),
	];
	$pcreParsingOptionsLcidOs = [
		'title' => __('This contains the language identifier of the operating system INSTALL (e.g. 407,c07,1407).'),
	];
	if (!$pcreParsing) {
		$pcreParsingOptionsOs = [
			'type' => 'select',
			'options' => $listOs,
			'empty' => __('Not specified'),
			'label' => [
				__('OS'),
				$pcreParsingOptionsOs['title'],
				':'
			],
		];
		$pcreParsingOptionsLcid = [
			'type' => 'select',
			'multiple' => true,
			'options' => $listLangId,
			'empty' => __('Not specified'),
			'label' => [
				__('Language ID'),
				$pcreParsingOptionsLcid['title'],
				':'
			],
		];
		$pcreParsingOptionsLcidOs = [
			'type' => 'select',
			'multiple' => true,
			'options' => $listLangId,
			'empty' => __('Not specified'),
			'label' => [
				__('Language ID OS'),
				$pcreParsingOptionsLcidOs['title'],
				':'
			],
		];
	}
	$inputList = [
		$modelName . '.id' => ['type' => 'hidden'],
		$modelName . '.ref_type' => ['type' => 'hidden'],
		$modelName . '.ref_node' => ['type' => 'hidden'],
		$modelName . '.ref_id' => ['type' => 'hidden'],
		$modelName . '.pcre_parsing' => [
			'label' => [
				__('Parsing PCRE on attributes'),
				__('Parsing of regular expressions in the extended attributes.'),
				':'
			],
			'type' => 'checkbox',
			'autocomplete' => 'off'
		],
		$modelName . '.hostname' => [
			'label' => __('Host name') . ':',
			'title' => __('This contains the name of the system, which is also contained in the Windows environment variable %COMPUTERNAME%.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off', 'autofocus' => true
		],
		$modelName . '.os' => $pcreParsingOptionsOs + ['label' => __('OS') . ':',
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.architecture' => ['type' => 'select', 'options' => $listArch,
			'empty' => __('Not specified'), 'label' => [__('Architecture'), __('This contains the processor architecture of the current operating system: x86, x64 or ia64.'), ':'],
			'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.ipaddresses' => ['label' => __('IP addresses') . ':',
			'title' => __('This contains all currently active IP addresses of the system or an empty string, if no network adapter is currently active.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.domainname' => ['label' => __('Domain name') . ':',
			'title' => __('This contains the name of the domain the system belongs to or an empty string, if it is not a domain member.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.groups' => ['label' => __('Groups') . ':',
			'title' => __('This contains the names of all groups the system belongs to or an empty string, if it is not a member of any group.'),
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.lcid' => $pcreParsingOptionsLcid + ['label' => __('Language ID') . ':',
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off'],
		$modelName . '.lcidOS' => $pcreParsingOptionsLcidOs + ['label' => __('Language ID OS') . ':',
			'type' => 'text', 'data-toggle' => 'tooltip', 'autocomplete' => 'off']
	];
	$tabsList = [
		__('Type, name') => [
			$modelName . '.id',
			$modelName . '.ref_type',
			$modelName . '.ref_node',
			$modelName . '.ref_id',
			$modelName . '.actions',
			$modelName . '.pcre_parsing',
			$modelName . '.full_name',
		],
		__('Host name, architecture, OS type, IP addresses') => [
			$modelName . '.hostname',
			$modelName . '.architecture',
			$modelName . '.os',
			$modelName . '.ipaddresses'
		],
		__('Domain name, groups member, language ID') => [
			$modelName . '.domainname',
			$modelName . '.groups',
			$modelName . '.lcid',
			$modelName . '.lcidOS'
		]
	];
	if ($isAddAction) {
		$removeItems = [0, 4];
		foreach ($removeItems as $removeItem) {
			unset($tabsList[__('Type, name')][$removeItem]);
		}
	}
	echo $this->Form->createFormTabs($inputList, $inputStatic, $tabsList, $legend, $modelName);