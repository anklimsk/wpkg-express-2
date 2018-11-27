<?php
/**
 * This file is the helper file of the plugin.
 * Search information Helper.
 * Methods for create extended view element for
 *  search information.
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */
App::uses('CakeSearchInfoAppHelper', 'CakeSearchInfo.View/Helper');

/**
 * Specific files helper used to retrieve specific CSS of JS files for action View.
 *
 * @package plugin.View.Helper
 */
class SearchHelper extends CakeSearchInfoAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = [
		'Html',
		'CakeTheme.ExtBs3Form',
	];

/**
 * Return search information form.
 *
 * @param array $targetFields List of target fields
 * @param array $targetFieldsSelected List of selected target fields
 * @param array|string $urlActionSearch URL to search action.
 * @param int $targetDeep Level of search:
 *  0 - search information in all fields of target model;
 *  1 - search information in selected fields of target model.
 * @param int $querySearchMinLength Minimal length of query.
 * @return string Return search information form.
 */
	public function createFormSearch($targetFields = null, $targetFieldsSelected = null, $urlActionSearch = null, $targetDeep = 0, $querySearchMinLength = 0) {
		if (!is_array($targetFields)) {
			$targetFields = [];
		}
		if (!is_array($targetFieldsSelected)) {
			$targetFieldsSelected = [];
		}

		if (empty($urlActionSearch)) {
			$urlActionSearch = '/cake_search_info/search/search';
		}

		$urlAutocomplete = '/cake_search_info/search/autocomplete.json';
		$formSearch = $this->ExtBs3Form->create('Search', [
			'type' => 'get',
			'url' => $urlActionSearch,
			'role' => 'search',
			'data-toggle' => 'pjax-form',
			'class' => 'navbar-form search-form',
			'autocomplete' => 'off'
		]);

		$inputSearch = $this->ExtBs3Form->input('query', ['maxlength' => 50, 'label' => false, 'autocomplete' => 'off', 'div' => false,
			'type' => 'text', 'placeholder' => __d('cake_search_info', 'Search'), 'required' => true, 'class' => 'form-control search-input-text clear-btn',
			'data-toggle' => 'autocomplete-search', 'data-autocomplete-url' => $urlAutocomplete,
			'data-autocomplete-min-length' => (int)$querySearchMinLength]);
		$buttonsSearch = $this->ExtBs3Form->button(
			$this->Html->tag('span', '', ['class' => 'fas fa-search fa-lg']),
			['class' => 'btn btn-success btn-search', 'type' => 'submit',
				'title' => __d(
					'cake_search_info',
					'Search information'
				),
			'data-toggle' => 'tooltip']
		);

		if (!empty($targetFields)) {
			$filterOptions = [
				__d('cake_search_info', 'Search settings') => [
					CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART => __d('cake_search_info', 'In any part of the string')
				]
			];
			foreach ($targetFields as $targetFieldLabel => $targetFieldName) {
				if (($targetDeep > 0) && is_array($targetFieldName)) {
					$filterOptions[$targetFieldLabel] = $targetFieldName;
				} else {
					$filterOptions[__d('cake_search_info', 'The scope of the search')][$targetFieldName] = $targetFieldLabel;
				}
			}
			$buttonsSearch .= $this->Html->div(
				'btn-group search-scope-filter',
				$this->ExtBs3Form->select('target', $filterOptions, [
					'style' => 'btn-info',
					'selected-text-format' => 'static',
					'data-placeholder' => $this->Html->tag('span', '', ['class' => 'fas fa-filter fa-lg']),
					'size' => '10',
					'width' => '100%',
					'actions-box' => 'true',
					'live-search' => 'false',
					'title' => __d('cake_search_info', 'Set search scope filter'),
					'class' => 'form-control non-break-list',
					'autocomplete' => 'off',
					'escape' => false,
					'multiple' => true,
					'value' => $targetFieldsSelected
				])
			);
		}
		$buttonsSearch = $this->Html->div('input-group-btn', $buttonsSearch);
		$formSearch .= $this->Html->div('form-group', $this->Html->div('input-group', $inputSearch . $buttonsSearch));
		$formSearch .= $this->ExtBs3Form->end();

		return $formSearch;
	}

/**
 * Return information about correction query of search.
 *
 * @param string $query Query string
 * @param string $queryCorrect Corrected query string
 * @param array $target List of target fields
 * @param array $optUrl Array of extented options for URL
 * @return string Return information about correction query of search.
 */
	public function correctQuery($query = null, $queryCorrect = null, $target = null, $optUrl = null) {
		$result = '';
		if (!is_array($optUrl)) {
			$optUrl = [];
		}

		if (empty($query) || empty($queryCorrect)) {
			return $result;
		}

		$correct = true;
		$correctQueryMsg = $this->Html->tag('p', __d(
			'cake_search_info',
			'Showing results for "%s"',
			$this->Html->tag('em', $this->Html->link(
				h($queryCorrect),
				['?' => http_build_query(compact('target', 'correct') + ['query' => $queryCorrect])] + $optUrl
			))
		)) .
			$this->Html->tag('footer', __d(
				'cake_search_info',
				'Search instead "%s"',
				$this->Html->tag('em', $this->Html->link(
					h($query),
					['?' => http_build_query(compact('query', 'target', 'correct'))] + $optUrl
				))
			));
		$result = $this->Html->tag('blockquote', $correctQueryMsg);

		return $result;
	}
}
