<?php
App::uses('AppCakeTestCase', 'CakeSearchInfo.Test');
App::uses('View', 'View');
App::uses('SearchHelper', 'CakeSearchInfo.View/Helper');

/**
 * SearchHelper Test Case
 */
class SearchHelperTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->_targetObject = new SearchHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testCreateFormSearchEmptyTargetFieldsAndEmptyUrl method
 *
 * @return void
 */
	public function testCreateFormSearchEmptyTargetFieldsAndEmptyUrl() {
		$result = $this->_targetObject->createFormSearch(null, null, null, 0, 0);
		$expected = '<form action="/cake_search_info/search/search" role="search" data-toggle="pjax-form" class="navbar-form search-form" autocomplete="off" id="SearchForm" method="get" accept-charset="utf-8"><div class="form-group"><div class="input-group"><input name="query" class="form-control search-input-text clear-btn" maxlength="50" autocomplete="off" placeholder="' . __d('cake_search_info', 'Search') . '" required="required" data-toggle="autocomplete-search" data-autocomplete-url="/cake_search_info/search/autocomplete.json" data-autocomplete-min-length="0" type="text" id="SearchQuery"/><div class="input-group-btn"><button class="btn btn-success btn-search" type="submit" title="' . __d('cake_search_info', 'Search information') . '" data-toggle="tooltip"><span class="fas fa-search fa-lg"></span></button></div></div></div></form>';
		$this->assertData($expected, $result);
	}

/**
 * testCreateFormSearchDeep0 method
 *
 * @return void
 */
	public function testCreateFormSearchDeep0() {
		$targetFields = [
			'City' => 'City',
		];
		$targetFieldsSelected = [];
		$urlActionSearch = '/search/index';
		$targetDeep = 0;
		$querySearchMinLength = 2;
		$result = $this->_targetObject->createFormSearch($targetFields, $targetFieldsSelected, $urlActionSearch, $targetDeep, $querySearchMinLength);
		$expected = '<form action="/search/index" role="search" data-toggle="pjax-form" class="navbar-form search-form" autocomplete="off" id="SearchForm" method="get" accept-charset="utf-8"><div class="form-group"><div class="input-group"><input name="query" class="form-control search-input-text clear-btn" maxlength="50" autocomplete="off" placeholder="' . __d('cake_search_info', 'Search') . '" required="required" data-toggle="autocomplete-search" data-autocomplete-url="/cake_search_info/search/autocomplete.json" data-autocomplete-min-length="2" type="text" id="SearchQuery"/><div class="input-group-btn"><button class="btn btn-success btn-search" type="submit" title="' . __d('cake_search_info', 'Search information') . '" data-toggle="tooltip"><span class="fas fa-search fa-lg"></span></button><div class="btn-group search-scope-filter"><input type="hidden" name="target" value="" id="SearchTarget_"/>' . "\n" .
			'<select name="target[]" data-placeholder="&lt;span class=&quot;fas fa-filter fa-lg&quot;&gt;&lt;/span&gt;" title="' . __d('cake_search_info', 'Set search scope filter') . '" class="form-control non-break-list" autocomplete="off" multiple="multiple" data-toggle="select" data-style="btn-info" data-selected-text-format="static" data-size="10" data-width="100%" data-actions-box="true" data-live-search="false" id="SearchTarget">' . "\n" .
				'<optgroup label="' . __d('cake_search_info', 'Search settings') . '">' . "\n" .
					'<option value="anyPart">' . __d('cake_search_info', 'In any part of the string') . '</option>' . "\n" .
				'</optgroup>' . "\n" .
				'<optgroup label="' . __d('cake_search_info', 'The scope of the search') . '">' . "\n" .
					'<option value="City">City</option>' . "\n" .
				'</optgroup>' . "\n" .
				'</select></div></div></div></div></form>';
		$this->assertData($expected, $result);
	}

/**
 * testCreateFormSearchDeep1 method
 *
 * @return void
 */
	public function testCreateFormSearchDeep1() {
		$targetFields = [
			'City' => [
				'City.City.name' => 'City name',
				'City.City.zip' => 'ZIP code',
				'City.City.population' => 'Population of city',
				'City.City.description' => 'Description of city',
				'City.City.virt_zip_name' => 'ZIP code with city name',
			],
		];
		$targetFieldsSelected = [
			'anyPart',
			'City.City.zip'
		];
		$urlActionSearch = '/search/index';
		$targetDeep = 1;
		$querySearchMinLength = 2;
		$result = $this->_targetObject->createFormSearch($targetFields, $targetFieldsSelected, $urlActionSearch, $targetDeep, $querySearchMinLength);
		$expected = '<form action="/search/index" role="search" data-toggle="pjax-form" class="navbar-form search-form" autocomplete="off" id="SearchForm" method="get" accept-charset="utf-8"><div class="form-group"><div class="input-group"><input name="query" class="form-control search-input-text clear-btn" maxlength="50" autocomplete="off" placeholder="' . __d('cake_search_info', 'Search') . '" required="required" data-toggle="autocomplete-search" data-autocomplete-url="/cake_search_info/search/autocomplete.json" data-autocomplete-min-length="2" type="text" id="SearchQuery"/><div class="input-group-btn"><button class="btn btn-success btn-search" type="submit" title="' . __d('cake_search_info', 'Search information') . '" data-toggle="tooltip"><span class="fas fa-search fa-lg"></span></button><div class="btn-group search-scope-filter"><input type="hidden" name="target" value="" id="SearchTarget_"/>' . "\n" .
			'<select name="target[]" data-placeholder="&lt;span class=&quot;fas fa-filter fa-lg&quot;&gt;&lt;/span&gt;" title="' . __d('cake_search_info', 'Set search scope filter') . '" class="form-control non-break-list" autocomplete="off" multiple="multiple" data-toggle="select" data-style="btn-info" data-selected-text-format="static" data-size="10" data-width="100%" data-actions-box="true" data-live-search="false" id="SearchTarget">' . "\n" .
				'<optgroup label="' . __d('cake_search_info', 'Search settings') . '">' . "\n" .
					'<option value="anyPart" selected="selected">' . __d('cake_search_info', 'In any part of the string') . '</option>' . "\n" .
				'</optgroup>' . "\n" .
				'<optgroup label="City">' . "\n" .
					'<option value="City.City.name">City name</option>' . "\n" .
					'<option value="City.City.zip" selected="selected">ZIP code</option>' . "\n" .
					'<option value="City.City.population">Population of city</option>' . "\n" .
					'<option value="City.City.description">Description of city</option>' . "\n" .
					'<option value="City.City.virt_zip_name">ZIP code with city name</option>' . "\n" .
				'</optgroup>' . "\n" .
			'</select></div></div></div></div></form>';
		$this->assertData($expected, $result);
	}

/**
 * testCorrectQuery method
 *
 * @return void
 */
	public function testCorrectQuery() {
		$query = 'ujhjl';
		$queryCorrect = 'город';
		$target = [
			'City',
			CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART
		];
		$optUrl = [
			'controller' => 'search',
			'action' => 'some_act',
		];
		$result = $this->_targetObject->correctQuery($query, $queryCorrect, $target, $optUrl);
		$expected = '<blockquote><p>' . __d('cake_search_info', 'Showing results for "%s"', '<em><a href="/search/some_act?target%5B0%5D=City&amp;target%5B1%5D=anyPart&amp;correct=1&amp;query=%D0%B3%D0%BE%D1%80%D0%BE%D0%B4">город</a></em>') . '</p><footer>' . __d('cake_search_info', 'Search instead "%s"', '<em><a href="/search/some_act?query=ujhjl&amp;target%5B0%5D=City&amp;target%5B1%5D=anyPart&amp;correct=1">ujhjl</a></em>') . '</footer></blockquote>';
		$this->assertData($expected, $result);
	}
}
