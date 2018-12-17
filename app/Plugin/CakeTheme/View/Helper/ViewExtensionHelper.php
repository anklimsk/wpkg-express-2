<?php
/**
 * This file is the helper file of the plugin.
 * View extension Helper.
 * Methods for create extended view element
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeThemeAppHelper', 'CakeTheme.View/Helper');
App::uses('Hash', 'Utility');
App::uses('NumberTextLib', 'Tools.Utility');
App::uses('Language', 'CakeBasicFunctions.Utility');

/**
 * View extension helper used for create extended view element.
 *
 * @package plugin.View.Helper
 */
class ViewExtensionHelper extends CakeThemeAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = [
		'Html',
		'CakeTheme.ExtBs3Form',
		'Time',
		'Text',
		'Number',
		'Paginator',
		'Session'
	];

/**
 * Stores the Language() utility object.
 *
 * @var object
 */
	protected $_Language = null;

/**
 * Stores the NumberTextLib() utility object.
 *
 * @var object
 */
	protected $_NumberTextLib = null;

/**
 * Stores configuration of Helper.
 *
 * @var array
 */
	protected $_config = [];

/**
 * Stores default configuration of Helper.
 *
 * @var array
 */
	protected $_defaultConfig = [
		// Default FontAwesome icon prefix
		'defaultIconPrefix' => 'fas',
		// Default FontAwesome icon size
		'defaultIconSize' => '',
		// Default Bootstrap button prefix
		'defaultBtnPrefix' => 'btn',
		// Default Bootstrap button size
		'defaultBtnSize' => '',
	];

/**
 * Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = []) {
		parent::__construct($View, $settings);
		$this->_Language = new Language();
		if (CakePlugin::loaded('Tools')) {
			$this->_NumberTextLib = new NumberTextLib();
		}
		$config = [];
		if (Configure::check('CakeTheme.ViewExtension.Helper')) {
			$config = (array)Configure::read('CakeTheme.ViewExtension.Helper');
		}

		$this->_config = $config + $this->_defaultConfig;
		$this->_optionsForElem = $this->_getListOptionsForElem();
	}

/**
 * Return default all options.
 *
 * @return mixed Return all default options.
 */
	protected function _getListOptionsForElem() {
		$cachePath = 'DefaultOptions_ViewExt_' . $this->_currUIlang;
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$result = [];
		$result['yesNo'] = [
			'yes' => __d('view_extension', 'Yes'),
			'no' => __d('view_extension', 'No')
		];
		$result['showEmpty'] = __d('view_extension', '&lt;None&gt;');
		$result['popupModalLink'] = [
			'escape' => false,
			'class' => 'popup-link',
			'target' => '_blank',
			'data-popover-placement' => 'auto top',
			'data-modal-title' => __d('view_extension', 'Detail information'),
		];
		$result['popupLink'] = [
			'escape' => false,
			'class' => 'popup-link',
			'target' => '_blank',
			'data-popover-placement' => 'auto top',
		];
		$result['modalLink'] = [
			'escape' => false,
			'data-modal-title' => __d('view_extension', 'Detail information'),
		];
		$result['confirmLink'] = [
			'escape' => false,
			'data-confirm-msg' => __d('view_extension', 'Are you sure you wish to delete this data?'),
			'data-confirm-btn-ok' => __d('view_extension', 'Yes'),
			'data-confirm-btn-cancel' => __d('view_extension', 'No')
		];
		$result['paginationSortPjax'] = [
			'linkOpt' => [
				'title' => __d('view_extension', 'Click to sort by this fiels'),
				'data-toggle' => 'pjax'
			],
			'iconDir' => [
				'asc' => $this->iconTag('fas fa-long-arrow-alt-up'),
				'desc' => $this->iconTag('fas fa-long-arrow-alt-down'),
			]
		];
		$result['truncateText'] = [
			'truncateOpt' => [
				'ellipsis' => $this->Html->link($this->iconTag('fas fa-angle-double-right'), '#', [
					'role' => 'button',
					'escape' => false,
					'data-toggle' => 'collapse-text-expand',
					'class' => 'collapse-text-action-btn',
					'title' => __d('view_extension', 'Expand text')
					]),
				'exact' => false,
				'html' => true
			],
			'collapseLink' => $this->Html->link($this->iconTag('fas fa-angle-double-left'), '#', [
				'role' => 'button',
				'escape' => false,
				'data-toggle' => 'collapse-text-roll-up',
				'class' => 'collapse-text-action-btn',
				'title' => __d('view_extension', 'Roll up text')
			])
		];
		$result['form'] = [
			'role' => 'form',
			'requiredcheck' => true,
			'data-required-msg' => __d('view_extension', 'Please fill in this field'),
		];
		$result['barPaging'] = [
			'firstPage' => [
				'icon' => $this->iconTag('fas fa-angle-double-left'),
				'linkOpt' => [
					'tag' => 'li',
					'escape' => false,
					'title' => __d('view_extension', 'First page'),
					'data-toggle' => 'tooltip',
				]
			],
			'previousPage' => [
				'icon' => $this->iconTag('fas fa-angle-left'),
				'linkOpt' => [
					'tag' => 'li',
					'escape' => false,
					'title' => __d('view_extension', 'Previous page'),
					'data-toggle' => 'tooltip',
				]
			],
			'numbers' => [
				'separator' => '',
				'ellipsis' => '...',
				'tag' => 'li',
				'currentLink' => true,
				'currentClass' => 'active',
				'currentTag' => 'a'
			],
			'nextPage' => [
				'icon' => $this->iconTag('fas fa-angle-right'),
				'linkOpt' => [
					'tag' => 'li',
					'escape' => false,
					'title' => __d('view_extension', 'Next page'),
					'data-toggle' => 'tooltip',
				]
			],
			'lastPage' => [
				'icon' => $this->iconTag('fas fa-angle-double-right'),
				'linkOpt' => [
					'tag' => 'li',
					'escape' => false,
					'title' => __d('view_extension', 'Last page'),
					'data-toggle' => 'tooltip',
				]
			],
			'showList' => [
				'icon' => $this->iconTag('fas fa-align-justify'),
				'linkOpt' => [
					'title' => __d('view_extension', 'Show as list'),
					'escape' => false
				]
			],
			'goToPage' => [
				'link' => $this->Html->link(
					$this->iconTag('fas fa-ellipsis-h'),
					'#',
					[
						'role' => 'button',
						'escape' => false,
						'data-toggle' => 'collapse',
						'aria-expanded' => 'false',
						'data-target' => '.control-go-to-page',
						'title' => __d('view_extension', 'Go to the page'),
					]
				),
				'spin' => [
					'id' => 'gotopagebar',
					'class' => 'input-sm',
					'autocomplete' => 'off',
					'min' => '1',
					'step' => '1',
					'verticalbuttons' => 'false',
					'postfix' => $this->iconTag('fas fa-share'),
					'postfix_extraclass' => 'btn btn-default btn-go-to-page',
					'title' => __d('view_extension', 'The page number to quickly jump')
				]
			],
			'numLines' => [
				'id' => 'numlinesbar',
				'type' => 'select',
				'style' => 'btn-default btn-sm',
				'width' => false,
				'live-search' => false,
				'size' => 'auto',
				'class' => 'form-control show-tick filter-condition input-sm',
				'autocomplete' => 'off',
				'div' => false,
				'label' => false,
				'title' => __d('view_extension', 'Number of lines'),
			]
		];
		$result['buttonsMove'] = [
			'btnDrag' => $this->buttonLink(
				'fas fa-arrows-alt',
				'btn-primary',
				'#',
				[
					'role' => 'button',
					'data-toggle' => 'drag',
					'title' => __d('view_extension', 'Drag and drop this item'),
				]
			),
			'btnsMove' => [
				[
					'icon' => 'fas fa-angle-double-up',
					'title' => __d('view_extension', 'Move top'),
					'url' => ['top']
				],
				[
					'icon' => 'fas fa-angle-up',
					'title' => __d('view_extension', 'Move up'),
					'url' => ['up']
				],
				[
					'icon' => 'fas fa-angle-down',
					'title' => __d('view_extension', 'Move down'),
					'url' => ['down']
				],
				[
					'icon' => 'fas fa-angle-double-down',
					'title' => __d('view_extension', 'Move bottom'),
					'url' => ['bottom']
				],
			]
		];
		$result['loadMore'] = [
			'linkTitle' => __d('view_extension', 'Load more'),
			'linkOpt' => [
				'role' => 'button',
				'data-toggle' => 'load-more',
				'title' => __d('view_extension', 'Load more informations')
			]
		];
		$result['btnPrint'] = $this->button(
			'fas fa-print fa-lg',
			'btn btn-default',
			[
				'data-toggle' => 'print',
				'class' => 'view-extension-print-btn',
				'title' => __d('view_extension', 'Print this page')
			]
		);
		$result['btnHeaderMenu'] = $this->ExtBs3Form->button(
			$this->iconTag('fas fa-bars fa-lg') . '&nbsp;' .
			$this->Html->tag('span', '', ['class' => 'caret']),
			[
				'type' => 'button',
				'class' => 'btn btn-default dropdown-toggle',
				'data-toggle' => 'dropdown',
				'title' => __d('view_extension', 'Menu of operations'),
				'aria-haspopup' => 'true',
				'aria-expanded' => 'false',
				'id' => 'pageHeaderMenu'
			]
		);
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * Return deep of levels for PCRE patterns of active menu item.
 *
 * @return int Return deep of levels for PCRE patterns of active menu item
 */
	protected function _getDeepLevelsActiveMenuPattern() {
		$result = 2;

		return $result;
	}

/**
 * Return pattern for PCRE of active menu item.
 *
 * @param int $level Level of pattern for PCRE
 * @param array $urlInfo Array information of current URL.
 * @return string|bool Pattern for PCRE, or False on failure.
 * @see ViewExtensionHelper::_parseCurrentUrl()
 */
	protected function _getActiveMenuPattern($level = 0, $urlInfo = null) {
		if (empty($urlInfo) || !is_array($urlInfo)) {
			return false;
		}

		$level = (int)$level;
		extract($urlInfo);
		if (($level < 0) || ($level > 2)) {
			$level = 0;
		}

		if ($level > 1) {
			$action = 'index';
		}
		if ($level == 0) {
			$userPrefix = $this->Session->read('Auth.User.prefix');
			if (empty($prefix) && !empty($userPrefix)) {
				$prefix = $userPrefix;
			}
		}

		$url = compact(
			'controller',
			'action',
			'plugin',
			'prefix'
		);
		if (!empty($prefix)) {
			$url[$prefix] = true;
		}
		$href = $this->url($url);
		$href = preg_quote($href, '/');
		if (!empty($pass) || !empty($named)) {
			if (((($level == 0) && (mb_stripos($action, 'index') === false)) ||
				($level == 2)) && ($href !== '\/')) {
				$href .= '\/?.*';
			}
		}
		$activePattern = '/<a\s+href=\"' . $href . '\".*>/iu';

		return $activePattern;
	}

/**
 * Return Array of pattern for PCRE of active menu item.
 *
 * @param array $urlInfo Array information of current URL.
 * @return array Return array of patterns for PCRE for all levels.
 * @see ViewExtensionHelper::_getActiveMenuPattern()
 */
	protected function _getActiveMenuPatterns($urlInfo = null) {
		$deepLevelsActivePattern = $this->_getDeepLevelsActiveMenuPattern();
		$activePatterns = [];
		for ($activePatternLevel = 0; $activePatternLevel <= $deepLevelsActivePattern; $activePatternLevel++) {
			$activePatterns[] = $this->_getActiveMenuPattern($activePatternLevel, $urlInfo);
		}

		return $activePatterns;
	}

/**
 * Prepare a list of the icons main menu by adding a drop-down
 *  element for sub-menu item.
 *
 * @param array &$iconList Array list of icons for main menu.
 * @return void
 * @see ViewExtensionHelper::_getActiveMenuPattern()
 */
	protected function _prepareIconList(array &$iconList) {
		if (empty($iconList) || !is_array($iconList)) {
			return;
		}

		foreach ($iconList as $i => &$iconListItem) {
			if (is_array($iconListItem) && isAssoc($iconListItem)) {
				foreach ($iconListItem as $topMenu => $subMenu) {
					$subMenuList = '';
					foreach ($subMenu as $subMenuItem) {
						$subMenuClass = null;
						if ($subMenuItem === 'divider') {
							$subMenuItem = '';
							$subMenuClass = 'divider';
						}
						$subMenuList .= $this->Html->tag('li', $subMenuItem, ['class' => $subMenuClass]);
					}
					$topMenuItem = $topMenu . $this->Html->tag('ul', $subMenuList, ['class' => 'dropdown-menu']);
				}
				$iconListItem = $topMenuItem;
			}
		}
	}

/**
 * Return array information of current URL.
 *
 * @return array Information of current URL.
 */
	protected function _parseCurrentUrl() {
		$prefix = $this->request->param('prefix');
		$plugin = $this->request->param('plugin');
		$controller = $this->request->param('controller');
		$action = $this->request->param('action');
		$named = $this->request->param('named');
		$pass = $this->request->param('pass');
		$named = (!empty($named) ? true : false);
		$pass = (!empty($pass) ? true : false);

		$result = compact(
			'prefix',
			'plugin',
			'controller',
			'action',
			'named',
			'pass'
		);

		return $result;
	}

/**
 * Return HTML element of main menu.
 *  For mark menu item as active, define view variable `activeMenuUrl`
 *  as array with URL contained in this menu item.
 *
 * @param array $iconList Array list of icons for main menu.
 * @return string HTML element of main menu.
 * @link http://getbootstrap.com/components/#navbar
 */
	public function getMenuList($iconList = null) {
		$activeMenuUrl = $this->_View->get('activeMenuUrl');
		$urlInfo = $this->_parseCurrentUrl();
		if (!empty($activeMenuUrl) && is_array($activeMenuUrl)) {
			$urlInfo = $activeMenuUrl + $urlInfo;
		}
		$activePatterns = $this->_getActiveMenuPatterns($urlInfo);
		$deepLevelsActivePattern = $this->_getDeepLevelsActiveMenuPattern();
		$activeMenu = [];
		$menuList = '';
		$cachePath = null;

		if (empty($iconList) || !is_array($iconList)) {
			return $menuList;
		}

		$this->_prepareIconList($iconList);
		$dataStr = serialize($iconList + $urlInfo) . '_' . $this->_currUIlang;
		$cachePath = 'MenuList.' . md5($dataStr);
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		foreach ($iconList as $i => $iconListItem) {
			for ($activePatternLevel = 0; $activePatternLevel <= $deepLevelsActivePattern; $activePatternLevel++) {
				if (!$activePatterns[$activePatternLevel]) {
					continue;
				}

				if (preg_match($activePatterns[$activePatternLevel], $iconListItem)) {
					$activeMenu[$activePatternLevel][] = $i;
				}
			}
		}

		for ($activePatternLevel = 0; $activePatternLevel <= $deepLevelsActivePattern; $activePatternLevel++) {
			if (isset($activeMenu[$activePatternLevel]) && !empty($activeMenu[$activePatternLevel])) {
				break;
			}
		}

		foreach ($iconList as $i => $iconListItem) {
			$iconListItemClass = null;
			if (isset($activeMenu[$activePatternLevel]) && in_array($i, $activeMenu[$activePatternLevel])) {
				$iconListItemClass = 'active';
			}

			$menuList .= $this->Html->tag('li', $iconListItem, ['class' => $iconListItemClass]);
		}

		if (!empty($menuList)) {
			$menuList = $this->Html->tag('ul', $menuList, ['class' => 'nav navbar-nav navbar-right']);
		}
		Cache::write($cachePath, $menuList, CAKE_THEME_CACHE_KEY_HELPERS);

		return $menuList;
	}

/**
 * Return text `Yes` or `No` for target data.
 *
 * @param mixed $data Data for checking.
 * @return string Text `Yes` or `No`.
 */
	public function yesNo($data = null) {
		if ((bool)$data) {
			$result = $this->_getOptionsForElem('yesNo.yes');
		} else {
			$result = $this->_getOptionsForElem('yesNo.no');
		}

		return $result;
	}

/**
 * Return list of `No` and `Yes` text.
 *
 * @return array List of `No` and `Yes` text.
 */
	public function yesNoList() {
		$result = [
			0 => $this->yesNo(false),
			1 => $this->yesNo(true),
		];

		return $result;
	}

/**
 * Return text `<None>` if target data is empty.
 *
 * @param mixed $data Data for checking.
 * @param mixed $dataRet Data for return, if target
 *  data is not empty. Default - target data.
 * @param mixed $emptyRet Data for return, if target
 *  data is empty. Default - `<None>`.
 * @param bool $isHtml Flag of trimmings HTML tags from result,
 *  if False.
 * @return string Text `Yes` or `No`.
 */
	public function showEmpty($data = null, $dataRet = null, $emptyRet = null, $isHtml = true) {
		if (empty($dataRet)) {
			$dataRet = $data;
		}

		if (empty($emptyRet)) {
			$emptyRet = $this->_getOptionsForElem('showEmpty');
		}

		if (empty($data)) {
			$emptyStr = $emptyRet;
		} else {
			$emptyStr = $dataRet;
		}

		$result = (!$isHtml ? h($emptyStr) : $emptyStr);

		return $result;
	}

/**
 * Return popover and modal link.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://getbootstrap.com/javascript/#popovers,
 *  http://getbootstrap.com/javascript/#modals
 */
	public function popupModalLink($title = null, $url = null, $options = []) {
		$optionsDefault = $this->_getOptionsForElem('popupModalLink');

		return $this->_createLink('modal-popover', $title, $url, $options, $optionsDefault);
	}

/**
 * Return popover link.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://getbootstrap.com/javascript/#popovers
 */
	public function popupLink($title = null, $url = null, $options = []) {
		$optionsDefault = $this->_getOptionsForElem('popupLink');

		return $this->_createLink('popover', $title, $url, $options, $optionsDefault);
	}

/**
 * Return modal link.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://getbootstrap.com/javascript/#modals
 */
	public function modalLink($title = null, $url = null, $options = []) {
		$optionsDefault = $this->_getOptionsForElem('modalLink');

		return $this->_createLink('modal', $title, $url, $options, $optionsDefault);
	}

/**
 * Return array options for confirm link.
 *
 * @return array Options for confirm link.
 */
	protected function _getOptionsConfirmLink() {
		$options = $this->_getOptionsForElem('confirmLink');

		return $options;
	}

/**
 * Return link with confirmation dialog.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://bootboxjs.com
 */
	public function confirmLink($title = null, $url = null, $options = []) {
		$optionsDefault = $this->_getOptionsConfirmLink();

		return $this->_createLink(null, $title, $url, $options, $optionsDefault);
	}

/**
 * Return link with confirmation dialog used POST request.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://bootboxjs.com
 */
	public function confirmPostLink($title = null, $url = null, $options = []) {
		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}

		$optionsDefault = $this->_getOptionsConfirmLink();
		$optionsDefault['role'] = 'post-link';

		return $this->ExtBs3Form->postLink($title, $url, $options + $optionsDefault);
	}

/**
 * Return link used with data-toggle attribute.
 *
 * @param string $toggle Value of attribute `data-toggle` for `<a>` tags.
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of options and HTML attributes.
 * @param array $optionsDefault Default HTML options for link element
 * @return string An `<a />` element.
 * @link http://ashleydw.github.io/lightbox
 */
	protected function _createLink($toggle = null, $title = null, $url = null, $options = [], $optionsDefault = []) {
		if (empty($options)) {
			$options = [];
		}
		if (empty($optionsDefault)) {
			$optionsDefault = [];
		}

		if (!empty($toggle)) {
			$options['data-toggle'] = $toggle;
		}
		if (!empty($optionsDefault)) {
			$options += $optionsDefault;
		}

		return $this->Html->link($title, $url, $options);
	}

/**
 * Return link used AJAX request.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 */
	public function ajaxLink($title, $url = null, $options = []) {
		return $this->_createLink('ajax', $title, $url, $options);
	}

/**
 * Return link used AJAX request without render result.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 */
	public function requestOnlyLink($title, $url = null, $options = []) {
		return $this->_createLink('request-only', $title, $url, $options);
	}

/**
 * Return link used PJAX request.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link https://github.com/defunkt/jquery-pjax
 */
	public function pjaxLink($title, $url = null, $options = []) {
		return $this->_createLink('pjax', $title, $url, $options);
	}

/**
 * Return link used Lightbox for Bootstrap.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://ashleydw.github.io/lightbox
 */
	public function lightboxLink($title, $url = null, $options = []) {
		return $this->_createLink('lightbox', $title, $url, $options);
	}

/**
 * Generates a sorting link used PJAX request. Sets named parameters for the sort and direction.
 *  Handles direction switching automatically.
 *
 * ### Options:
 *
 * - `escape` Whether you want the contents html entity encoded, defaults to true.
 * - `model` The model to use, defaults to PaginatorHelper::defaultModel().
 * - `direction` The default direction to use when this link isn't active.
 * - `lock` Lock direction. Will only use the default direction then, defaults to false.
 *
 * @param string $key The name of the key that the recordset should be sorted.
 * @param string $title Title for the link. If $title is null $key will be used
 *  for the title and will be generated by inflection.
 * @param array|string $options Options for sorting link. See above for list of keys.
 * @return string A link sorting default by 'asc'. If the resultset is sorted 'asc' by the specified
 *  key the returned link will sort by 'desc'.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/paginator.html#PaginatorHelper::sort,
 *  https://github.com/defunkt/jquery-pjax
 */
	public function paginationSortPjax($key = null, $title = null, $options = []) {
		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}

		$optionsDefault = $this->_getOptionsForElem('paginationSortPjax.linkOpt');
		if ($this->request->is('modal')) {
			$optionsDefault['data-toggle'] = 'modal';
			//$optionsDefault['data-disable-use-stack'] = 'true';
		}

		$sortKey = $this->Paginator->sortKey();
		if (!empty($sortKey) && ($key === $sortKey)) {
			$sortDir = $this->Paginator->sortDir();
			if ($sortDir === 'asc') {
				$dirIcon = $this->_getOptionsForElem('paginationSortPjax.iconDir.asc');
			} else {
				$dirIcon = $this->_getOptionsForElem('paginationSortPjax.iconDir.desc');
			}
			$title .= $dirIcon;
			$options['escape'] = false;
		}

		return $this->Paginator->sort($key, $title, $options + $optionsDefault);
	}

/**
 * Return link used AJAX request and show progress bar of
 *  execution task from queue.
 *
 * @param string $title The content to be wrapped by `<a>` tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for link element
 * @return string An `<a />` element.
 * @link http://ricostacruz.com/nprogress
 */
	public function progressSseLink($title, $url = null, $options = []) {
		return $this->_createLink('progress-sse', $title, $url, $options);
	}

/**
 * Return list of icon sizes.
 *
 * @return array List of icon sizes.
 * @link http://fontawesome.io
 */
	protected function _getListIconSizes() {
		$result = [
			'fa-xs',
			'fa-sm',
			'fa-lg',
			'fa-2x',
			'fa-3x',
			'fa-5x',
			'fa-7x',
			'fa-10x',
		];

		return $result;
	}

/**
 * Return list of icon prefixes.
 *
 * @return array List of icon prefixes.
 * @link http://fontawesome.io
 */
	protected function _getListIconPrefixes() {
		$result = [
			'fab',
			'fas',
			'far',
			'fal',
			'fa',
		];

		return $result;
	}

/**
 * Return list of button.
 *
 * @return array List of button sizes.
 * @link http://getbootstrap.com/css/#buttons
 */
	protected function _getListButtons() {
		$result = [
			'btn-default',
			'btn-primary',
			'btn-success',
			'btn-info',
			'btn-warning',
			'btn-danger',
			'btn-link',
			'btn-block',
		];

		return $result;
	}

/**
 * Return list of button sizes.
 *
 * @return array List of button sizes.
 * @link http://getbootstrap.com/css/#buttons
 */
	protected function _getListButtonSizes() {
		$result = [
			'btn',
			'btn-lg',
			'btn-sm',
			'btn-xs',
		];

		return $result;
	}

/**
 * Return list of button prefixes.
 *
 * @return array List of button prefixes.
 * @link http://getbootstrap.com/css/#buttons
 */
	protected function _getListButtonPrefixes() {
		$result = [
			'btn',
		];

		return $result;
	}

/**
 * Return class name for icon or button. Exclude base class `fa` or `btn`,
 *  and add class of size, if need.
 *
 * @param string $elementClass Class of target element.
 * @return string Class name for icon or button.
 */
	protected function _getClassForElement($elementClass = null) {
		$elementClass = mb_strtolower((string)$elementClass);
		$cachePath = 'ClassForElem.' . md5($elementClass);
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$result = '';
		if (empty($elementClass)) {
			return $result;
		}

		if (mb_strpos($elementClass, 'fa-') !== false) {
			$elemList = [];
			$elemPrefixes = $this->_getListIconPrefixes();
			$elemPrefix = (string)$this->_config['defaultIconPrefix'];
			$elemSizes = $this->_getListIconSizes();
			$elemSize = (string)$this->_config['defaultIconSize'];
		} elseif (mb_strpos($elementClass, 'btn-') !== false) {
			$elemList = $this->_getListButtons();
			$elemPrefixes = $this->_getListButtonPrefixes();
			$elemPrefix = (string)$this->_config['defaultBtnPrefix'];
			$elemSizes = $this->_getListButtonSizes();
			$elemSize = (string)$this->_config['defaultBtnSize'];
		} else {
			return $result;
		}
		$aElem = explode(' ', $elementClass, 5);
		$aClass = [];
		foreach ($aElem as $elemItem) {
			if (empty($elemItem)) {
				continue;
			}

			if (in_array($elemItem, $elemSizes)) {
				$elemSize = $elemItem;
			} elseif (in_array($elemItem, $elemPrefixes)) {
				$elemPrefix = $elemItem;
			} elseif (empty($elemList) || (!empty($elemList) && in_array($elemItem, $elemList))) {
				$aClass[] = $elemItem;
			}
		}
		if (!empty($elemList) && empty($aClass)) {
			return $result;
		}

		if (!empty($elemPrefix)) {
			array_unshift($aClass, $elemPrefix);
		}
		if (!empty($elemSize)) {
			array_push($aClass, $elemSize);
		}
		$aClass = array_unique($aClass);
		$result = implode(' ', $aClass);
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * Process option `action-type`.
 * List of values `action-type`:
 *  - `confirm`: create link with confirmation action;
 *  - `confirm-post`: create link with confirmation action
 *   and POST request;
 *  - `post`: create link with POST request;
 *  - `modal`: create link with opening result in modal window.
 *
 * @param array &$options Array of options for process
 * @return bool Return True, if need POST link. GET link otherwise.
 */
	protected function _processActionTypeOpt(array &$options) {
		$result = false;
		if (empty($options) || !isset($options['action-type'])) {
			return $result;
		}

		$actionType = $options['action-type'];
		unset($options['action-type']);
		switch ($actionType) {
			case 'confirm':
				// no break
			case 'confirm-post':
				$optionsDefault = $this->_getOptionsConfirmLink();
				$options += $optionsDefault;
				if ($actionType === 'confirm-post') {
					$result = true;
					$options['role'] = 'post-link';
				}
				break;
			case 'post':
				$result = true;
				$options['role'] = 'post-link';
				break;
			case 'modal':
				$options['data-toggle'] = 'modal';
				break;
		}

		return $result;
	}

/**
 * Return class for button element
 *
 * @param string $btn class of button for process
 * @return string Class for button element
 * @link http://getbootstrap.com/css/#buttons
 */
	public function getBtnClass($btn = null) {
		$btnClass = $this->_getClassForElement($btn);
		if (!empty($btnClass)) {
			$result = $btnClass;
		} else {
			$result = $this->_getClassForElement('btn-default');
		}

		return $result;
	}

/**
 * Return HTML element of icon
 *
 * @param string $icon class of icon
 * @param array|string $options HTML options for icon element
 * @return string HTML element of icon
 * @link http://fontawesome.io
 */
	public function iconTag($icon = null, $options = []) {
		$icon = mb_strtolower((string)$icon);
		$dataStr = serialize(compact('icon', 'options')) . '_' . $this->_currUIlang;
		$cachePath = 'iconTag.' . md5($dataStr);
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$result = '';
		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}

		$iconClass = $this->_getClassForElement($icon);
		if (empty($iconClass)) {
			return $result;
		}

		$options['class'] = $iconClass;
		$result = $this->Html->tag('span', '', $options);
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * Return HTML element of button by link
 *
 * @param string $icon class of icon
 * @param string $btn class of button
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for button element
 *  List of values option `action-type`:
 *   - `confirm`: create link with confirmation action;
 *   - `confirm-post`: create link with confirmation action
 *     and POST request;
 *   - `post`: create link with POST request;
 *   - `modal`: create link with opening result in modal window.
 * @return string HTML element of button.
 * @link http://fontawesome.io,
 *  http://getbootstrap.com/css/#buttons
 */
	public function buttonLink($icon = null, $btn = null, $url = null, $options = []) {
		$result = '';
		if (empty($icon)) {
			return $result;
		}

		$title = '';
		if ($icon === strip_tags($icon)) {
			if ($this->_getClassForElement($icon)) {
				$icon .= ' fa-fw';
			}
			$title = $this->iconTag($icon);
		}
		if (empty($title)) {
			$title = $icon;
		}

		$button = $this->getBtnClass($btn);
		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}
		$options['class'] = $button . (isset($options['class']) ? ' ' . $options['class'] : '');

		$optionsDefault = [
			'escape' => false,
		];
		if (isset($options['title'])) {
			$optionsDefault['data-toggle'] = 'title';
		}

		$postLink = $this->_processActionTypeOpt($options);
		if ($postLink) {
			if (!isset($options['block'])) {
				$options['block'] = 'confirm-form';
			}
			$result = $this->ExtBs3Form->postLink($title, $url, $options + $optionsDefault);
		} else {
			$result = $this->Html->link($title, $url, $options + $optionsDefault);
		}

		return $result;
	}

/**
 * Return HTML element of button
 *
 * @param string $icon class of icon
 * @param string $btn class of button
 * @param array|string $options HTML options for button element
 * @return string HTML element of button.
 * @link http://fontawesome.io,
 *  http://getbootstrap.com/css/#buttons
 */
	public function button($icon = null, $btn = null, $options = []) {
		$result = '';
		if (empty($icon)) {
			return $result;
		}

		$title = '';
		if ($icon === strip_tags($icon)) {
			if ($this->_getClassForElement($icon)) {
				$icon .= ' fa-fw';
			}
			$title = $this->iconTag($icon);
		}
		if (empty($title)) {
			$title = $icon;
		}

		$button = $this->getBtnClass($btn);
		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}
		$options['class'] = $button . (isset($options['class']) ? ' ' . $options['class'] : '');

		$optionsDefault = [
			'escape' => false,
			'type' => 'button',
		];
		if (isset($options['title'])) {
			$optionsDefault['data-toggle'] = 'title';
		}
		$result = $this->ExtBs3Form->button($title, $options + $optionsDefault);

		return $result;
	}

/**
 * Return URL with user role prefix
 *  For disable use user prefix, add to url array key
 *  `prefix` with value `false`.
 *
 * @param array|string $url URL for adding prefix
 * @return array|string Return URL with user role prefix
 */
	public function addUserPrefixUrl($url = null) {
		if (empty($url)) {
			return $url;
		}

		$userPrefix = $this->Session->read('Auth.User.prefix');
		if (empty($userPrefix)) {
			return $url;
		}

		if (!is_array($url)) {
			if (($url === '/') || (stripos($url, '/') === false)) {
				return $url;
			}
			$url = Router::parse($url);
		}

		if (isset($url['prefix']) && ($url['prefix'] === false)) {
			$url[$userPrefix] = false;
			if (isset($url['prefix'])) {
				unset($url['prefix']);
			}

			return $url;
		}

		$url[$userPrefix] = true;

		return $url;
	}

/**
 * Return HTML element of menu item label
 *
 * @param string $title Title of menu label
 * @return string HTML element of menu item label
 */
	public function menuItemLabel($title = null) {
		$result = '';
		if (empty($title)) {
			return $result;
		}

		$result = $this->Html->tag('span', $title, ['class' => 'menu-item-label visible-xs-inline']);

		return $result;
	}

/**
 * Return HTML element of menu item
 *
 * @param string $icon class of icon
 * @param string $title Title of menu label
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for button element
 * @param int $badgeNumber The number for display to the right of the menu item
 * @return string HTML element of menu item
 * @link http://getbootstrap.com/components/#navbar,
 *  http://fontawesome.io,
 *  http://getbootstrap.com/css/#buttons
 */
	public function menuItemLink($icon = null, $title = null, $url = null, $options = [], $badgeNumber = 0) {
		$result = '';
		if (empty($icon) || empty($title)) {
			return $result;
		}

		$caret = '';
		if (empty($options) || !is_array($options)) {
			$options = [];
		}
		$options['escape'] = false;
		$options['title'] = $title;
		$badgeNumber = (int)$badgeNumber;
		if (empty($url)) {
			$url = '#';
			$caret = $this->Html->tag('span', '', ['class' => 'caret']);
			$options += [
				'class' => 'dropdown-toggle',
				'data-toggle' => 'dropdown',
				'role' => 'button',
				'aria-haspopup' => 'true',
				'aria-expanded' => 'false'
			];
		} else {
			$url = $this->addUserPrefixUrl($url);
		}

		if ($this->_getClassForElement($icon)) {
			$icon .= ' fa-fw';
		}
		$iconTag = $this->iconTag($icon) . $this->menuItemLabel($title) . $caret;
		if ($badgeNumber > 0) {
			$iconTag .= '&nbsp;' . $this->Html->tag('span', $this->Number->format(
				$badgeNumber,
				['thousands' => ' ', 'before' => '', 'places' => 0]
			), ['class' => 'badge']);
		}
		if (!isset($options['data-toggle'])) {
			$options['data-toggle'] = 'tooltip';
		}
		$result = $this->Html->link(
			$iconTag,
			$url,
			$options
		);

		return $result;
	}

/**
 * Return HTML element of item for page header menu
 *
 * @param string $icon class of icon
 * @param string $titleText Title of menu label
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array|string $options HTML options for button element
 *  List of values option `action-type`:
 *   - `confirm`: create link with confirmation action;
 *   - `confirm-post`: create link with confirmation action
 *	and POST request;
 *   - `post`: create link with POST request;
 *   - `modal`: create link with opening result in modal window.
 * @return string HTML element of item for page header menu
 * @link http://fontawesome.io
 */
	public function menuActionLink($icon = null, $titleText = null, $url = null, $options = []) {
		$result = '';
		if (empty($icon)) {
			return $result;
		}

		if ($this->_getClassForElement($icon)) {
			$icon .= ' fa-fw';
		}
		$iconClass = $this->_getClassForElement($icon);
		if (empty($iconClass)) {
			return $result;
		}

		$title = $this->Html->tag('span', '', ['class' => $iconClass]);
		if (!empty($titleText)) {
			$title .= $this->Html->tag('span', $titleText, ['class' => 'menu-item-label']);
		}

		if (empty($options)) {
			$options = [];
		} elseif (!is_array($options)) {
			$options = [$options];
		}

		$optionsDefault = [
			'escape' => false,
		];
		if (isset($options['title'])) {
			$optionsDefault['data-toggle'] = 'title';
		}

		$url = $this->addUserPrefixUrl($url);
		$postLink = $this->_processActionTypeOpt($options);
		if ($postLink) {
			$result = $this->ExtBs3Form->postLink($title, $url, $options + $optionsDefault);
		} else {
			$result = $this->Html->link($title, $url, $options + $optionsDefault);
		}

		return $result;
	}

/**
 * Return HTML element of time ago
 *
 * @param int|string|DateTime $time UNIX timestamp, strtotime() valid string
 *  or DateTime object
 * @param string $format strftime format string
 * @return string HTML element of time ago
 * @link http://timeago.yarp.com
 */
	public function timeAgo($time = null, $format = null) {
		if (empty($time)) {
			$time = time();
		}
		if (empty($format)) {
			$format = '%x %X';
		}

		$result = $this->Html->tag(
			'time',
			$this->Time->i18nFormat($time, $format),
			[
				'data-toggle' => 'timeago',
				'datetime' => date('c', $this->Time->fromString($time)),
				'class' => 'help'
			]
		);

		return $result;
	}

/**
 * Return icon for file from extension.
 *
 * @param string $extension Extension of file
 * @return string Icon for file.
 * @link http://fontawesome.io
 */
	public function getIconForExtension($extension = null) {
		$extension = mb_strtolower((string)$extension);
		$cachePath = 'IconForExtension.' . md5($extension);
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$result = 'far fa-file';
		if (empty($extension)) {
			Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

			return $result;
		}

		$extensions = [
			'far fa-file-archive' => ['zip', 'rar', '7z'],
			'far fa-file-word' => ['doc', 'docx'],
			'far fa-file-code' => ['htm', 'html', 'xml', 'js'],
			'far fa-file-image' => ['jpg', 'jpeg', 'png', 'gif'],
			'far fa-file-pdf' => ['pdf'],
			'far fa-file-video' => ['avi', 'mp4'],
			'far fa-file-powerpoint' => ['ppt', 'pptx'],
			'far fa-file-audio' => ['mp3', 'wav', 'flac'],
			'far fa-file-excel' => ['xls', 'xlsx'],
			'far fa-file-alt' => ['txt'],
		];

		foreach ($extensions as $icon => $extensionsList) {
			if (in_array($extension, $extensionsList)) {
				$result = $icon;
				break;
			}
		}
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * Return truncated text with button `expand` and `roll up`.
 *
 * @param string $text Text to truncate.
 * @param int $length Length of returned string.
 * @return string Truncated text.
 */
	public function truncateText($text = null, $length = 0) {
		$result = '';
		if (empty($text)) {
			return $result;
		}

		$text = (string)$text;
		$length = (int)$length;
		if ($length <= 0) {
			$length = 50;
		}

		if (($text === h($text)) && (mb_strlen($text) <= $length)) {
			return $text;
		}

		$truncateOpt = $this->_getOptionsForElem('truncateText.truncateOpt');
		$tuncatedText = $this->Text->truncate($text, $length, $truncateOpt);
		if ($tuncatedText === $text) {
			return $tuncatedText;
		}

		$result = $this->Html->div('collapse-text-truncated', $tuncatedText);
		$result .= $this->Html->div(
			'collapse-text-original',
			$text . $this->_getOptionsForElem('truncateText.collapseLink')
		);
		$result = $this->Html->div('collapse-text-expanded', $result);

		return $result;
	}

/**
 * Return array of options for HTML Form element.
 *
 * @param array $options HTML options for Form element
 * @return array Return array of options.
 */
	public function getFormOptions($options = []) {
		if (empty($options) || !is_array($options)) {
			$options = [];
		}

		$optionsDefault = $this->_getOptionsForElem('form');
		$result = $optionsDefault + $options;

		return $result;
	}

/**
 * Return language name for library Tools.NumberText.
 *
 * @param string $langCode Languge code in format `ISO 639-1` or `ISO 639-2`
 * @return string|bool Return language name in format RFC5646, or False on failure.
 * @link http://numbertext.org/ Universal number to text conversion languag
 */
	protected function _getLangForNumberText($langCode = null) {
		if (!is_object($this->_NumberTextLib)) {
			return false;
		}
		$cachePath = 'lang_code_number_lib_' . md5(serialize(func_get_args()));
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_LANG_CODE);
		if (!empty($cached)) {
			return $cached;
		}

		$langNumb = $this->_Language->getLangForNumberText($langCode);
		$result = $this->_NumberTextLib->setLang($langNumb);
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_LANG_CODE);

		return $result;
	}

/**
 * Return number as text.
 *
 * @param string $number Number for processing
 * @param string $langCode Languge code in format `ISO 639-1` or `ISO 639-2`
 * @return string|bool Return number as text, or False on failure.
 */
	public function numberText($number = null, $langCode = null) {
		if (!is_object($this->_NumberTextLib)) {
			return false;
		}
		$langNumb = $this->_getLangForNumberText($langCode);

		return $this->_NumberTextLib->numberText($number, $langNumb);
	}

/**
 * Return bar of state.
 *
 * @param array $stateData Array of state in format:
 *  - key `stateName`, value: name of state;
 *  - key `stateId`, value: ID of state;
 *  - key `amount`, value: amount elements in this state;
 *  - key `stateUrl`, value: url for state, e.g.:
 *    array('controller' => 'trips', 'action' => 'index', '?' => array('data[FilterData][0][Trip][state_id]' => '2')) [Not necessary];
 *  - key `class`: ID of state, value: class of state for progress bar,
 *    e.g.: 'progress-bar-danger progress-bar-striped' [Not necessary].
 * @return string Return bar of state.
 */
	public function barState($stateData = null) {
		$result = '';
		if (!is_array($stateData)) {
			$stateData = [];
		}

		if (empty($stateData)) {
			$stateData = [
				[
					'stateName' => $this->showEmpty(null),
					'stateId' => null,
					'amount' => 0,
					'stateUrl' => null,
				]
			];
		}

		$totalAmount = Hash::apply($stateData, '{n}.amount', 'array_sum');
		$percSum = 0;
		$countState = count($stateData);
		foreach ($stateData as $i => $stateItem) {
			$class = 'progress-bar';
			if (isset($stateItem['class']) && !empty($stateItem['class'])) {
				$class .= ' ' . $stateItem['class'];
			}
			if ($totalAmount > 0) {
				$perc = round($stateItem['amount'] / $totalAmount * 100, 2);
			} else {
				$perc = 100;
			}
			$percRound = round($perc);
			if ($percSum + $percRound > 100) {
				$percRound = 100 - $percSum;
			} else {
				if ($i == $countState - 1) {
					$percRound = 100 - $percSum;
				}
			}
			$percSum += $percRound;
			$stateName = $stateItem['stateName'];
			$progressBar = $this->Html->div(
				$class,
				$stateName,
				['role' => 'progressbar', 'style' => 'width:' . $percRound . '%',
					'title' => $stateName . ': ' . $this->Number->format(
						$stateItem['amount'],
						['thousands' => ' ', 'before' => '', 'places' => 0]
					) . ' (' . $perc . '%)',
					'data-toggle' => 'tooltip']
			);

			if (isset($stateItem['stateUrl']) && !empty($stateItem['stateUrl'])) {
				$progressBar = $this->Html->link(
					$progressBar,
					$stateItem['stateUrl'],
					['target' => '_blank', 'escape' => false]
				);
			}
			$result .= $progressBar;
		}
		$result = $this->Html->div('progress', $result);

		return $result;
	}

/**
 * Return list of last changed data.
 *
 * @param array $lastInfo Array of last information in format:
 *  - key `label`, value: label of list item;
 *  - key `modified`, value: date and time of last modification;
 *  - key `id`, value: ID of record.
 * @param string $labelList Label of list.
 * @param string $controllerName Name of controller for viewing.
 * @param string $actionName Name of controller action for viewing.
 * @param array|string $linkOptions HTML options for link element.
 * @param int $length Length of list item label string.
 * @return string Return list of last changed data.
 */
	public function listLastInfo($lastInfo = null, $labelList = null, $controllerName = null, $actionName = null, $linkOptions = [], $length = 0) {
		if (!is_array($lastInfo)) {
			$lastInfo = [];
		}

		if (empty($labelList) && !empty($controllerName)) {
			$labelList = Inflector::humanize(Inflector::underscore($controllerName));
		}

		if (empty($actionName)) {
			$actionName = 'view';
		}

		$lastInfoList = '';
		if (!empty($labelList)) {
			$lastInfoList .= $this->Html->tag('dt', $labelList . ':');
		}
		if (!empty($lastInfo)) {
			$lastInfoListData = [];
			foreach ($lastInfo as $lastInfoItem) {
				$label = $this->truncateText(h($lastInfoItem['label']), $length);
				if (!empty($controllerName) && !empty($lastInfoItem['id'])) {
					$url = $this->addUserPrefixUrl(['controller' => $controllerName, 'action' => $actionName, $lastInfoItem['id']]);
					$label = $this->popupModalLink($label, $url, $linkOptions);
				}
				$lastInfoListData[] = $label .
					' (' . $this->timeAgo($lastInfoItem['modified']) . ')';
			}
			$lastInfoListItem = $this->Html->nestedList($lastInfoListData, null, null, 'ol');
		} else {
			$lastInfoListItem = $this->showEmpty(null);
		}
		$lastInfoList .= $this->Html->tag('dd', $lastInfoListItem);
		$result = $this->Html->tag('dl', $lastInfoList, ['class' => 'dl-horizontal']);

		return $result;
	}

/**
 * Return controls of pagination as page buttons.
 *
 * @param bool $showCounterInfo If true, show information about
 *  pages and records
 * @param bool $useShowList If true, show button `Show as list`
 * @param bool $useGoToPage If true, show button `Go to the page`
 * @param bool $useChangeNumLines If true, show select `Number of lines`
 * @return string Return controls of pagination as page buttons.
 */
	public function barPaging($showCounterInfo = true, $useShowList = true, $useGoToPage = true, $useChangeNumLines = true) {
		$result = '';
		$paginatorParams = $this->Paginator->params();
		$count = (int)Hash::get($paginatorParams, 'count');
		if ($count == 0) {
			return $result;
		}

		$urlPaginator = [];
		if (isset($this->Paginator->options['url'])) {
			$urlPaginator = (array)$this->Paginator->options['url'];
		}

		$pageCount = (int)Hash::get($paginatorParams, 'pageCount');
		$page = (int)Hash::get($paginatorParams, 'page');
		$current = (int)Hash::get($paginatorParams, 'current');
		$limit = (int)Hash::get($paginatorParams, 'limit');
		if ($limit == 0) {
			$limit = 20;
		}
		if ($pageCount < 2) {
			$useShowList = false;
			$useGoToPage = false;
		}
		if ($page > 1) {
			$useShowList = false;
		}
		if ($count <= 10) {
			$useChangeNumLines = false;
		}

		if ($showCounterInfo) {
			if ($pageCount === 1) {
				$records = __dn('view_extension', 'record', 'records', $count);
				$result .= $this->Html->para('small', $this->Paginator->counter(__d('view_extension', 'Showing {:count} %s', $records)));
			} else {
				$records = __dn('view_extension', 'record', 'records', $current);
				$result .= $this->Html->para('small', $this->Paginator->counter(__d('view_extension', 'Page {:page} of {:pages}, showing {:current} %s out of {:count} total, starting on record {:start}, ending on {:end}', $records)));
			}
		}

		$pageButtons = '';
		if ($page > 2) {
			$pageButtons .= $this->Paginator->first(
				$this->_getOptionsForElem('barPaging.firstPage.icon'),
				$this->_getOptionsForElem('barPaging.firstPage.linkOpt')
			);
		}
		if ($this->Paginator->hasPrev()) {
			$pageButtons .= $this->Paginator->prev(
				$this->_getOptionsForElem('barPaging.previousPage.icon'),
				$this->_getOptionsForElem('barPaging.previousPage.linkOpt')
			);
		}
		$pageButtons .= $this->Paginator->numbers(
			$this->_getOptionsForElem('barPaging.numbers')
		);
		if ($this->Paginator->hasNext()) {
			$pageButtons .= $this->Paginator->next(
				$this->_getOptionsForElem('barPaging.nextPage.icon'),
				$this->_getOptionsForElem('barPaging.nextPage.linkOpt')
			);
		}
		if (($pageCount - $page) > 1) {
			$pageButtons .= $this->Paginator->last(
				$this->_getOptionsForElem('barPaging.lastPage.icon'),
				$this->_getOptionsForElem('barPaging.lastPage.linkOpt')
			);
		}
		if ($useShowList) {
			$urlShowList = $this->Paginator->url(['show' => 'list'], true);
			$urlShowList = array_merge($urlPaginator, $urlShowList);
			$pageButtons .= $this->Html->tag('li', $this->pjaxLink(
				$this->_getOptionsForElem('barPaging.showList.icon'),
				$this->Paginator->url($urlShowList, true),
				$this->_getOptionsForElem('barPaging.showList.linkOpt')
			));
		}
		if ($useGoToPage) {
			$urlGo = $this->Paginator->url(['page' => 0], true);
			$urlGo = array_merge($urlPaginator, $urlGo);
			$pageButtons .= $this->Html->tag(
				'li',
				$this->_getOptionsForElem('barPaging.goToPage.link') .
				$this->Html->div(
					'control-go-to-page collapse',
					$this->ExtBs3Form->spin(
						null,
						[
							'max' => $pageCount,
							'data-url' => $this->Paginator->url($urlGo),
							'data-curr-value' => $page,
							'value' => $page,
						] + $this->_getOptionsForElem('barPaging.goToPage.spin')
					)
				)
			);
		}

		if (!empty($pageButtons)) {
			$optionsPageButtons = ['class' => 'pagination pagination-sm hidden-print hide-popup',
				'data-toggle' => 'pjax'];
			if ($this->request->is('modal')) {
				$optionsPageButtons['data-toggle'] = 'modal';
				//$optionsPageButtons['data-disable-use-stack'] = 'true';
			}
			$result .= $this->Html->tag('ul', $pageButtons, $optionsPageButtons);
		}
		if ($useChangeNumLines) {
			$urlChangeLimit = $this->Paginator->url(['limit' => $limit], true);
			$urlChangeLimit = array_merge($urlPaginator, $urlChangeLimit);
			$numLinesOptions = [];
			$numLinesList = [
				10,
				20,
				30,
				40,
				50,
				100,
				150,
				200,
				250,
			];
			$exitFor = false;
			foreach ($numLinesList as $numLines) {
				if ($exitFor) {
					break;
				}
				if ($count < $numLines) {
					$exitFor = true;
				}
				$numLinesOptions[$numLines] = (string)$numLines;
			}

			if (count($numLinesOptions) > 1) {
				$result .= $this->Html->div(
					'control-change-num-lines',
					$this->ExtBs3Form->input(
						null,
						[
							'value' => $limit,
							'data-url' => $this->Paginator->url($urlChangeLimit),
							'data-curr-value' => $limit,
							'options' => $numLinesOptions
						] + $this->_getOptionsForElem('barPaging.numLines')
					)
				);
			}
		}
		if (!empty($result)) {
			$result = $this->Html->div('paging', $result);
		}

		return $result;
	}

/**
 * Return buttons for moving items.
 *
 * @param array $url Base url for moving items
 * @param bool $useDrag If True, add button `Drag`
 * @param string $glue String as glue for buttons
 * @param bool $useGroup If True and empty $glue,
 *  use group buttons.
 * @return string Return buttons for moving items.
 * @see MoveComponent::moveItem()
 * @see MoveComponent::dropItem()
 */
	public function buttonsMove($url = null, $useDrag = true, $glue = '', $useGroup = false) {
		$result = '';
		if (empty($url) || !is_array($url)) {
			return $result;
		}

		$actions = [];
		if ($useDrag) {
			$actions[] = $this->_getOptionsForElem('buttonsMove.btnDrag');
		}
		$buttons = $this->_getOptionsForElem('buttonsMove.btnsMove');
		foreach ($buttons as $button) {
			$actions[] = $this->buttonLink(
				$button['icon'],
				'btn-info',
				array_merge($button['url'], $url),
				['title' => $button['title'], 'data-toggle' => 'move']
			);
		}
		$result = implode($glue, $actions);
		if (empty($glue) && $useGroup) {
			$result = $this->Html->div('btn-group', $result, ['role' => 'group']);
		}

		return $result;
	}

/**
 * Return controls of pagination as button `Load more`.
 *
 * @param string $targetSelector jQuery selector to select data
 *  from server response and selection of the element on the
 *  page to add new data.
 * @return string Return button `Load more`.
 */
	public function buttonLoadMore($targetSelector = null) {
		$result = '';
		if (empty($targetSelector)) {
			$targetSelector = 'table tbody';
		}

		$hasNext = $this->Paginator->hasNext();
		if (!$hasNext) {
			$paginatorParams = $this->Paginator->params();
			$count = (int)Hash::get($paginatorParams, 'count');
			$records = __dn('view_extension', 'record', 'records', $count);
			$result = $this->Html->div(
				'load-more',
				$this->Html->para('small', $this->Paginator->counter(__d('view_extension', 'Showing {:count} %s', $records)))
			);

			return $result;
		}

		$urlPaginator = [];
		if (isset($this->Paginator->options['url'])) {
			$urlPaginator = (array)$this->Paginator->options['url'];
		}
		$paginatorParams = $this->Paginator->params();
		$count = (int)Hash::get($paginatorParams, 'count');
		$page = (int)Hash::get($paginatorParams, 'page');
		$limit = (int)Hash::get($paginatorParams, 'limit');
		$urlLoadMore = $this->Paginator->url(['page' => $page + 1, 'show' => 'list'], true);
		$urlLoadMore = array_merge($urlPaginator, $urlLoadMore);
		$btnClass = $this->getBtnClass('btn-default btn-sm');
		$btnClass .= ' btn-block';
		$startRecord = 0;
		if ($count >= 1) {
			$startRecord = (($page - 1) * $limit) + 1;
		}
		$endRecord = $startRecord + $limit - 1;
		$records = __dn('view_extension', 'record', 'records', $endRecord);
		$result = $this->Html->div(
			'load-more',
			$this->Html->para('small', $this->Paginator->counter(
				__d('view_extension', 'Current loaded page {:page} of {:pages}, showing {:end} %s of {:count} total', $records)
			)) .
			$this->Html->link(
				$this->_getOptionsForElem('loadMore.linkTitle'),
				$urlLoadMore,
				[
					'class' => $btnClass . ' hidden-print',
					'data-target-selector' => $targetSelector,
				] + $this->_getOptionsForElem('loadMore.linkOpt')
			)
		);

		return $result;
	}

/**
 * Return controls of pagination depending on the state of the
 *  named parameter "show"
 *
 * @param string $targetSelector jQuery selector to select data
 *  from server response and selection of the element on the
 *  page to add new data.
 * @param bool $showCounterInfo If true, show information about
 *  pages and records
 * @param bool $useShowList If true, show button `Show as list`
 * @param bool $useGoToPage If true, show button `Go to the page`
 * @param bool $useChangeNumLines If true, show select `Number of lines`
 * @return string Return controls of pagination.
 * @see ViewExtension::buttonLoadMore()
 * @see ViewExtension::barPaging()
 */
	public function buttonsPaging($targetSelector = null, $showCounterInfo = true, $useShowList = true, $useGoToPage = true, $useChangeNumLines = true) {
		$showType = (string)$this->request->param('named.show');
		if (mb_stripos($showType, 'list') === 0) {
			$result = $this->buttonLoadMore($targetSelector);
		} else {
			$result = $this->barPaging($showCounterInfo, $useShowList, $useGoToPage, $useChangeNumLines);
		}

		return $result;
	}

/**
 * Return button for print page.
 *
 * @return string Return for print page.
 */
	public function buttonPrint() {
		$result = $this->_getOptionsForElem('btnPrint');

		return $result;
	}

/**
 * Return menu for header of page.
 *
 * @param array|string $headerMenuActions List of menu actions.
 *  If is array with number of elements:
 *  - 1: use as menu item label;
 *  - 2: first item use as menu item label, second item use as menu item options;
 *  - 3 or 4: use parameters for ViewExtensionHelper::menuActionLink().
 * @return string Return menu for header of page.
 * @see ViewExtensionHelper::menuActionLink()
 */
	public function menuHeaderPage($headerMenuActions = null) {
		$result = '';
		if (empty($headerMenuActions)) {
			return $result;
		}

		if (!is_array($headerMenuActions)) {
			$headerMenuActions = [$headerMenuActions];
		}

		$headerMenuActionsPrep = '';
		foreach ($headerMenuActions as $action) {
			$actionOptions = [];
			if (!is_array($action)) {
				if ($action === 'divider') {
					$action = '';
					$actionOptions = ['class' => 'divider'];
				}
				$headerMenuActionsPrep .= $this->Html->tag('li', $action, $actionOptions);
			} elseif (is_array($action)) {
				$actionSize = count($action);
				if ($actionSize == 1) {
					$actionLabal = array_shift($action);
					if (empty($actionLabal)) {
						continue;
					}
					$headerMenuActionsPrep .= $this->Html->tag('li', $actionLabal);
				} elseif ($actionSize == 2) {
					$actionLabal = array_shift($action);
					if (empty($actionLabal)) {
						continue;
					}
					$actionOptions = array_shift($action);
					if (!is_array($actionOptions)) {
						$actionOptions = [];
					}
					$headerMenuActionsPrep .= $this->Html->tag('li', $actionLabal, $actionOptions);
				} elseif (($actionSize >= 3) && ($actionSize <= 4)) {
					$action = call_user_func_array([$this, 'menuActionLink'], $action);
					$headerMenuActionsPrep .= $this->Html->tag('li', $action, $actionOptions);
				}
			}
		}

		if (empty($headerMenuActionsPrep)) {
			return $result;
		}
		$result = $this->Html->div(
			'btn-group page-header-menu hidden-print',
			$this->_getOptionsForElem('btnHeaderMenu') .
				$this->Html->tag('ul', $headerMenuActionsPrep, ['class' => 'dropdown-menu', 'aria-labelledby' => 'pageHeaderMenu'])
		);

		return $result;
	}

/**
 * Return header of page.
 *
 * @param string $pageHeader Text of page header
 * @param array|string $headerMenuActions List of menu actions
 * @return string Return header of page.
 * @see ViewExtensionHelper::menuHeaderPage()
 */
	public function headerPage($pageHeader = null, $headerMenuActions = null) {
		$result = '';
		if (empty($pageHeader)) {
			return $result;
		}

		$pageHeader = (string)$pageHeader;
		$pageHeader .= $this->menuHeaderPage($headerMenuActions);

		return $this->Html->div('page-header well', $this->Html->tag('h2', $pageHeader, ['class' => 'header']));
	}

/**
 * Return collapsible list.
 *
 * @param array $listData List data
 * @param int $showLimit Limit of the displayed list
 * @param string $listClass Class of the list tag
 * @param string $listTag Type of list tag to use (ol/ul)
 * @return string Return collapsible list.
 */
	public function collapsibleList($listData = [], $showLimit = 10, $listClass = 'list-unstyled', $listTag = 'ul') {
		$result = '';
		if (empty($listData)) {
			return $result;
		}

		$showLimit = (int)$showLimit;
		if ($showLimit < 1) {
			return $result;
		}

		if (!empty($listClass)) {
			$listClass = ' ' . $listClass;
		}

		$tagsAllowed = ['ul', 'ol'];
		if (!in_array($listTag, $tagsAllowed)) {
			$listTag = 'ul';
		}

		$listDataShown = array_slice($listData, 0, $showLimit);
		$result = $this->Html->nestedList($listDataShown, ['class' => 'list-collapsible-compact' . $listClass], [], $listTag);
		if (count($listData) <= $showLimit) {
			return $result;
		}

		$listId = uniqid('collapsible-list-');
		$listDataHidden = array_slice($listData, $showLimit);
		$result .= $this->Html->nestedList(
			$listDataHidden,
			[
				'class' => 'list-collapsible-compact collapse' . $listClass,
				'id' => $listId
			],
			[],
			$listTag
		) .
		$this->button(
			'fas fa-angle-double-down',
			'btn-default',
			[
				'class' => 'top-buffer hide-popup',
				'title' => __d('view_extension', 'Show or hide full list'),
				'data-toggle' => 'collapse', 'data-target' => '#' . $listId,
				'aria-expanded' => 'false',
				'data-toggle-icons' => 'fa-angle-double-down,fa-angle-double-up'
			]
		);

		return $result;
	}

/**
 * Adds a links to the breadcrumbs array.
 *
 * @param array $breadCrumbs List of breadcrumbs in format:
 *  - first element in list item: text for link;
 *  - second element in list item: URL for link.
 * @return void
 */
	public function addBreadCrumbs($breadCrumbs = null) {
		if (empty($breadCrumbs) || !is_array($breadCrumbs)) {
			return;
		}

		foreach ($breadCrumbs as $breadCrumbInfo) {
			if (empty($breadCrumbInfo)) {
				continue;
			}

			$link = null;
			if (is_array($breadCrumbInfo)) {
				$name = array_shift($breadCrumbInfo);
				$link = array_shift($breadCrumbInfo);
			} else {
				$name = $breadCrumbInfo;
			}
			if (empty($name)) {
				continue;
			}

			$this->Html->addCrumb(h($name), $link);
		}
	}
}
