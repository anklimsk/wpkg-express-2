<?php
/**
 * This file is the helper file of the plugin.
 * Table filter helper.
 * Used for creation table header with filter form elements.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeThemeAppHelper', 'CakeTheme.View/Helper');
App::uses('ClassRegistry', 'Utility');

/**
 * Table filter helper.
 *
 * @package plugin.View.Helper
 */
class FilterHelper extends CakeThemeAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = [
		'Html',
		'Paginator',
		'CakeTheme.ExtBs3Form',
		'CakeTheme.ViewExtension',
	];

/**
 * Cache of schema for used models
 *
 * @var array
 */
	protected $_schemaCache = [];

/**
 * Flag of using POST request
 *
 * @var bool
 */
	protected $_usePost = false;

/**
 * Size of form input
 *
 * @var string
 */
	protected $_formInputSize = '';

/**
 * Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = []) {
		parent::__construct($View, $settings);

		$btnSize = Configure::read('CakeTheme.ViewExtension.Helper.defaultBtnSize');
		if (preg_match('/btn\-(xs|sm|lg)/', $btnSize, $matches)) {
			$this->_formInputSize = $matches[1];
		}
		$this->_optionsForElem = $this->_getListOptionsForElem();
	}

/**
 * Return default all options.
 *
 * @return mixed Return all default options.
 */
	protected function _getListOptionsForElem() {
		$cachePath = 'DefaultOptions_Filter_' . $this->_currUIlang;
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$inputSize = '';
		$btnSize = '';
		$formInputSize = $this->_getFormInputSize();
		if (!empty($formInputSize)) {
			$inputSize = ' input-' . $formInputSize;
			$btnSize = ' btn-' . $formInputSize;
		}
		$emptyText = $this->ViewExtension->showEmpty('');

		$result = [];
		$result['openFilterForm'] = [
			'role' => 'form',
			'class' => 'filter-form clone-wrapper',
			'autocomplete' => 'off',
			'data-max-clone' => CAKE_THEME_FILTER_ROW_LIMIT
		];
		$result['paginTableRowHeaderActTitle'] = __d('view_extension', 'Actions');
		$result['createFilterTableRow'] = [
			'commonOptions' => [
				'label' => false,
				'required' => false,
				'secure' => false,
				'class' => 'form-control' . $inputSize
			],
			'filterText' => __d(
				'view_extension',
				'Data filter for the table: %s',
				$this->Html->tag(
					'code',
					$emptyText,
					[
						'data-toggle' => 'filter-conditions',
						'data-empty-text' => $emptyText
					]
				)
			),
			'showFilterBtn' => $this->ViewExtension->button(
				'far fa-caret-square-down',
				'btn-default',
				['class' => 'show-filter-btn',
					'title' => __d('view_extension', 'Show or hide filter'),
					'data-toggle' => 'collapse', 'data-target' => '.filter-collapse',
					'aria-expanded' => 'false',
					'data-toggle-icons' => 'fa-caret-square-down,fa-caret-square-up'
				]
			),
			'applyFilterBtn' => $this->ViewExtension->button(
				'fas fa-filter',
				'btn-info',
				[
					'type' => 'submit',
					'class' => 'exclude-clone filter-apply',
					'title' => __d('view_extension', 'Apply filter'),
					'data-toggle' => 'tooltip',
					'value' => 'filter-apply',
					'name' => 'data[FilterAction]'
				]
			),
			'clearFilterBtn' => $this->ViewExtension->button(
				'fas fa-eraser',
				'btn-warning',
				[
					'type' => 'reset',
					'class' => 'exclude-clone filter-clear',
					'title' => __d(
						'view_extension',
						'Clear filter'
					),
					'data-toggle' => 'tooltip'
				]
			),
			'addRowBtn' => $this->ViewExtension->button(
				'fas fa-plus',
				'btn-success',
				[
					'title' => __d('view_extension', 'Add row of filter'),
					'data-toggle' => 'btn-action-clone'
				]
			),
			'deleteRowBtn' => $this->ViewExtension->button(
				'fas fa-trash-alt',
				'btn-danger',
				[
					'title' => __d('view_extension', 'Delete row of filter'),
					'data-toggle' => 'btn-action-delete'
				]
			),
			'printBtnOptions' => [
				'title' => __d('view_extension', 'Print informations'),
				'data-toggle' => 'tooltip',
				'target' => '_blank'
			],
			'exportBtnOptions' => [
				'title' => __d('view_extension', 'Export informations'),
				'data-toggle' => 'tooltip'
			],
			'tableHeaderOptions' => [
				'data-toggle' => 'clone-source',
				'class' => 'filter-collapse collapse filter-controls-row'
			]
		];
		$result['createGroupActionControls'] = [
			'selectAllBtn' => $this->ViewExtension->button(
				'far fa-check-square',
				'btn-info',
				['escape' => false, 'class' => 'hidden-print',
					'title' => __d('view_extension', 'Select / deselect all'),
					'data-toggle' => 'btn-action-select-all',
					'data-toggle-icons' => 'fa-check-square,fa-square']
			),
			'groupDataProcTitle' => $this->Html->tag('strong', __d('view_extension', 'Group data processing')),
			'performActionBtn' => [
				'type' => 'submit',
				'escape' => false,
				'title' => __d('view_extension', 'Perform action'),
				'data-toggle' => 'tooltip', 'value' => 'group-action',
				'name' => 'data[FilterAction]', 'data-dialog-title' => __d('view_extension', 'Action to perform'),
				'data-dialog-sel-placeholder' => __d('view_extension', 'Select a action'),
				'data-dialog-btn-ok' => __d('view_extension', 'Perform'),
				'data-dialog-btn-cancel' => __d('view_extension', 'Cancel')
			],
		];
		$result['getBtnConditionField'] = [
			'options' => [
				'' => ['name' => '=', 'title' => '=',
					'data-subtext' => __d('view_extension', 'Equal'), 'value' => ''],
				'gt' => ['name' => '&gt;', 'title' => '&gt;',
					'data-subtext' => __d('view_extension', 'Greater than'), 'value' => 'gt'],
				'ge' => ['name' => '&ge;', 'title' => '&ge;',
					'data-subtext' => __d('view_extension', 'Greater than or equal to'), 'value' => 'ge'],
				'lt' => ['name' => '&lt;', 'title' => '&lt;',
					'data-subtext' => __d('view_extension', 'Less than'), 'value' => 'lt'],
				'le' => ['name' => '&le;', 'title' => '&le;',
					'data-subtext' => __d('view_extension', 'Less than or equal to'), 'value' => 'le'],
				'ne' => ['name' => '&ne;', 'title' => '&ne;',
					'data-subtext' => __d('view_extension', 'Not equal'), 'value' => 'ne'],
			],
			'title' => __d('view_extension', 'Change the logical condition of the field')
		];
		$result['getBtnConditionGroup'] = [
			'options' => [
				'' => ['name' => '&nbsp;&&', 'title' => '&nbsp;&&', 'data-subtext' => __d('view_extension', 'And'), 'value' => ''],
				'or' => ['name' => '&nbsp;||', 'title' => '&nbsp;||', 'data-subtext' => __d('view_extension', 'Or'), 'value' => 'or'],
				'not' => ['name' => '&nbsp;!', 'title' => '&nbsp;!', 'data-subtext' => __d('view_extension', 'Not'), 'value' => 'not'],
			],
			'title' => __d('view_extension', 'Change the logical condition of the group')
		];
		$result['getBtnCondition'] = [
			'type' => 'select',
			'title' => __d('view_extension', 'Changing the filter condition'),
			'style' => 'btn-default btn-filter-condition' . $btnSize,
			'width' => 'fit',
			'live-search' => false,
			'class' => 'form-control show-tick filter-condition' . $inputSize,
			'autocomplete' => 'off',
			'div' => false,
			'label' => false,
			'escape' => false,
			'required' => false,
			'secure' => false,
		];
		$result['prepareOptions'] = [
			'select' => [
				'empty' => '--' . __d('view_extension', 'Sel.') . '--',
				'style' => 'btn-default' . $btnSize
			],
			'string' => [
				'div' => false,
				'type' => 'autocomplete',
				'url' => $this->url([
					'controller' => 'filter',
					'action' => 'autocomplete',
					'plugin' => 'cake_theme',
					'ext' => 'json'
				])
			],
			'integer' => [
				'div' => 'input-group'
			],
			'boolean' => [
				'options' => $this->ViewExtension->yesNoList(),
				'type' => 'select',
				'style' => 'btn-default' . $btnSize,
				'empty' => '--' . __d('view_extension', 'Sel.') . '--'
			],
			'date' => [
				'div' => 'input-group',
				'widget-position-vertical' => 'bottom',
				'icon-type' => 'false',
			],
			'binary' => [
				'disabled' => true
			]
		];
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * Set flag using POST request.
 *
 * @param bool $state State of flag using POST request.
 * @return void
 */
	protected function _setFlagUsePost($state = false) {
		$this->_usePost = (bool)$state;
	}

/**
 * Return state of flag using POST request.
 *
 * @return bool State of flag using POST request
 */
	protected function _getFlagUsePost() {
		return (bool)$this->_usePost;
	}

/**
 * Return size of form input.
 *
 * @return string Size of form input
 */
	protected function _getFormInputSize() {
		return (string)$this->_formInputSize;
	}

/**
 * Return schema for model from cache.
 *
 * @param string $model Name of model for return schema.
 * @return array|null Array of schema, or null on failure.
 */
	protected function _getSchemaCache($model = null) {
		if (empty($model) || !isset($this->_schemaCache[$model])) {
			return null;
		}

		return $this->_schemaCache[$model];
	}

/**
 * Add schema for model to cache.
 *
 * @param string $model Name of model for return schema.
 * @param array|null $schema Schema of model.
 * @return void;
 */
	protected function _setSchemaCache($model = null, $schema = null) {
		if (empty($model)) {
			return;
		}

		$this->_schemaCache[$model] = $schema;
	}

/**
 * Return schema for model.
 *
 * @param string $model Name of model for return schema.
 * @return array|null Array of schema, or null on failure.
 */
	protected function _getSchema($model = null) {
		$result = null;
		if (empty($model)) {
			return $result;
		}

		$modelObj = ClassRegistry::init($model, true);
		if ($modelObj === false) {
			return $result;
		}

		$result = $modelObj->schema();
		if (empty($modelObj->virtualFields)) {
			return $result;
		}

		foreach ($modelObj->virtualFields as $virtualFieldName => $virtualFieldVal) {
			$result[$virtualFieldName] = ['type' => 'string'];
		}

		return $result;
	}

/**
 * Return HTML open tag of filter form.
 *
 * @param bool $usePost If True, using POST request.
 *  GET otherwise.
 * @return string HTML open tag of form.
 */
	public function openFilterForm($usePost = false) {
		$this->_setFlagUsePost($usePost);
		$type = 'get';
		$dataToggle = 'pjax-form';
		if ($this->_getFlagUsePost()) {
			$type = 'post';
			$dataToggle = 'ajax-form';
		} else {
			$filterData = $this->getFilterData();
			if (!empty($filterData)) {
				$this->setFilterInputData($filterData);
			}
			$conditions = $this->getFilterConditions();
			if (!empty($conditions)) {
				$this->setFilterInputConditions($conditions);
			}
		}

		$options = [
			'data-toggle' => $dataToggle,
			'type' => $type,
		];
		$optionsDefault = $this->_getOptionsForElem('openFilterForm');
		$result = $this->ExtBs3Form->create(null, $options + $optionsDefault);

		return $result;
	}

/**
 * Return HTML close tag of filter form.
 *
 * @return string HTML close tag of form.
 */
	public function closeFilterForm() {
		$type = 'get';
		if ($this->_getFlagUsePost()) {
			$type = 'post';
		}

		$this->ExtBs3Form->requestType = $type;
		$result = $this->ExtBs3Form->end();

		return $result;
	}

/**
 * Returns a row of table headers with pagination links
 *
 * @param array $paginationFields Array of fields for pagination in format:
 *   - `key`: field of filter in format `model.field`;
 *   - `value`: HTML options for pagination link.
 *    If pagination field is not equal filter form input field, use
 *    option: `pagination-field` => `model.field`.
 *    For disable pagination, use option: `disabled` => true.
 *    For setting class of header cell, use option `class-header` => `class-name`.
 *
 * @return string Row of table headers with pagination links.
 */
	protected function _createPaginationTableRow($paginationFields = null) {
		$result = '';
		if (empty($paginationFields) || !is_array($paginationFields)) {
			return $result;
		}

		$tableHeader = [];
		$includeOptions = [
			'class' => null,
			'escape' => null,
		];
		foreach ($paginationFields as $paginationField => $paginationOptions) {
			if (is_int($paginationField)) {
				if (!is_string($paginationOptions)) {
					continue;
				}

				$paginationField = $paginationOptions;
				$paginationOptions = [];
			}
			if (strpos($paginationField, '.') === false) {
				continue;
			}

			if (!is_array($paginationOptions)) {
				$paginationOptions = [$paginationOptions];
			}

			$paginationFieldUse = $paginationField;
			if (isset($paginationOptions['pagination-field']) && !empty($paginationOptions['pagination-field'])) {
				$paginationFieldUse = $paginationOptions['pagination-field'];
			}
			$label = $paginationFieldUse;
			if (isset($paginationOptions['label']) && !empty($paginationOptions['label'])) {
				$label = $paginationOptions['label'];
			}

			$paginationSortLink = $label;
			$paginationOptionsUse = array_intersect_key($paginationOptions, $includeOptions);
			if (!isset($paginationOptions['disabled']) || !$paginationOptions['disabled']) {
				$paginationSortLink = $this->ViewExtension->paginationSortPjax($paginationFieldUse, $label, $paginationOptionsUse);
			}

			if (isset($paginationOptions['class-header']) && !empty($paginationOptions['class-header'])) {
				$paginationSortLink = [$paginationSortLink => ['class' => $paginationOptions['class-header']]];
			}
			$tableHeader[] = $paginationSortLink;
		}
		$tableHeader[] = [$this->_getOptionsForElem('paginTableRowHeaderActTitle') => ['class' => 'action hide-popup']];
		$result = $this->Html->tableHeaders($tableHeader);

		return $result;
	}

/**
 * Returns a row of table headers with filter form.
 *
 * @param array $formInputs Array of inputs for filter form in format:
 *   - `key`: field of filter in format `model.field`;
 *   - `value`: HTML options  for filter input.
 *    For disable filter form input, use option: `disabled` => true.
 * @param string $plugin Name of plugin for target model of filter.
 * @param bool $usePrint If True, display Print button.
 * @param string $exportType Extension of exported file, for display Export button.
 *
 * @return string Row of table headers with filter form.
 */
	protected function _createFilterTableRow($formInputs = null, $plugin = null, $usePrint = true, $exportType = null) {
		$result = '';
		if (empty($formInputs) || !is_array($formInputs)) {
			return $result;
		}

		$commonOptions = $this->_getOptionsForElem('createFilterTableRow.commonOptions');
		$filterData = $this->getFilterData();
		if ($this->_getFlagUsePost()) {
			$usePrint = false;
			$exportType = null;
		}

		$filterButtons = $this->_getOptionsForElem('createFilterTableRow.showFilterBtn');
		if ($usePrint) {
			$filterButtons .= $this->ViewExtension->buttonLink(
				'fas fa-print',
				'btn-default',
				$this->Paginator->url(['ext' => 'prt', '?' => $this->request->query], true),
				$this->_getOptionsForElem('createFilterTableRow.printBtnOptions')
			);
		}
		if (!empty($exportType)) {
			$exportType = mb_strtolower($exportType);
			$exportIcon = $this->ViewExtension->getIconForExtension($exportType);
			$filterButtons .= $this->ViewExtension->buttonLink(
				$exportIcon,
				'btn-default',
				$this->Paginator->url(['ext' => $exportType, '?' => $this->request->query], true),
				$this->_getOptionsForElem('createFilterTableRow.exportBtnOptions')
			);
		}
		$filterHeader = $this->Html->tag(
			'span',
			$filterButtons,
			['class' => 'action pull-left hidden-print hide-popup']
		);
		$filterHeader .= $this->_getOptionsForElem('createFilterTableRow.filterText');
		$actions = $this->getBtnConditionGroup('FilterCond.group') .
			$this->_getOptionsForElem('createFilterTableRow.applyFilterBtn') .
			$this->_getOptionsForElem('createFilterTableRow.clearFilterBtn');
		$filterHeader .= $this->Html->tag(
			'span',
			$actions,
			['class' => 'action pull-right hidden-print hide-popup']
		);
		$tableHeader = [
			[$filterHeader => [
				'colspan' => count($formInputs) + 1,
				'class' => 'text-center'
			]]
		];
		$result .= $this->Html->tableHeaders($tableHeader, ['class' => 'active filter-header-row']);
		if (empty($filterData)) {
			$filterData = [null];
		}
		$firstFilterRow = true;
		$filterInputCache = [];
		$filterRowCount = 0;
		foreach ($filterData as $indexData => $filterDataItem) {
			if (!is_int($indexData)) {
				continue;
			}

			$filterRowCount++;
			if ($filterRowCount > CAKE_THEME_FILTER_ROW_LIMIT) {
				break;
			}
			$tableHeader = [];
			$emptyData = true;
			foreach ($formInputs as $inputField => $inputOptions) {
				if (is_int($inputField)) {
					if (!is_string($inputOptions)) {
						continue;
					}

					$inputField = $inputOptions;
					$inputOptions = [];
				}
				if (strpos($inputField, '.') === false) {
					continue;
				}

				if (!is_array($inputOptions)) {
					$inputOptions = [$inputOptions];
				}

				$options = $inputOptions + $commonOptions;
				$excludeOptions = [
					'class-header' => null,
				];
				$options = array_diff_key($options, $excludeOptions);
				if (isset($options['not-use-input']) && !empty($options['not-use-input'])) {
					$tableHeader[] = '';
					continue;
				}

				if (!empty($options['label']) && isset($options['label'])) {
					$options['data-filter-label'] = $options['label'];
				}
				$options['label'] = false;
				$options = $this->prepareOptions($inputField, $options, $indexData, $plugin);
				$dataPath = $indexData . '.' . $inputField;
				$value = $this->getFilterData($dataPath);
				$condition = $this->getFilterConditions($dataPath);
				$cacheKey = md5(serialize(compact('inputField', 'value', 'condition')));
				if (($value !== null) && !in_array($cacheKey, $filterInputCache)) {
					$emptyData = false;
				}
				$filterInputCache[] = $cacheKey;
				$this->ExtBs3Form->unlockField($inputField);
				$tableHeaderItem = $this->Html->div(
					'form-group',
					$this->ExtBs3Form->input($inputField, $options)
				);
				if (!empty($classItem)) {
					$tableHeaderItem = [$tableHeaderItem, ['class' => $classItem]];
				}
				$tableHeader[] = $tableHeaderItem;
			}
			$actions = [
				$this->_getOptionsForElem('createFilterTableRow.addRowBtn'),
				$this->_getOptionsForElem('createFilterTableRow.deleteRowBtn'),
			];
			$actionsBtn = implode('', $actions);
			$tableHeader[] = [
				$actionsBtn => ['class' => 'filter-action text-center']
			];
			if (!$emptyData || $firstFilterRow) {
				$result .= $this->Html->tableHeaders($tableHeader, $this->_getOptionsForElem('createFilterTableRow.tableHeaderOptions'));
				$firstFilterRow = false;
			}
		}
		$this->ExtBs3Form->requestType = null;

		return $result;
	}

/**
 * Returns a row of table header with pagination links and
 *  filter form.
 *
 * @param array $formInputs Array of inputs for filter form in format:
 *   - `key`: field of filter in format `model.field`;
 *   - `value`: HTML options for pagination link and filter input.
 *    If pagination field is not equal filter form input field, use
 *    option: `pagination-field` => `model.field`.
 *    For disable pagination and filter form input, use option: `disabled` => true.
 *    For setting class of header cell, use option `class-header` => `class-name`.
 *    For exclude form input, use option: `not-use-input` => true.
 *    For escaping of title and attributes set escape to false to disable: `escape` => false.
 *
 * @param string $plugin Name of plugin for target model of filter.
 * @param bool $usePrint If True, display Print button (default True).
 * @param string $exportType Extension of exported file, for display Export button.
 *
 * @return string Row of table header.
 */
	public function createFilterForm($formInputs = null, $plugin = null, $usePrint = true, $exportType = null) {
		$result = '';
		if (empty($formInputs) || !is_array($formInputs)) {
			return $result;
		}

		if ($usePrint) {
			$urlOptions = $this->_getExtendOptionsPaginationUrl();
			if (!isset($this->Paginator->options['url'])) {
				$this->Paginator->options['url'] = [];
			}

			$this->Paginator->options['url'] = Hash::merge($this->Paginator->options['url'], $urlOptions);
		}

		$result .= $this->_createPaginationTableRow($formInputs);
		$result .= $this->_createFilterTableRow($formInputs, $plugin, $usePrint, $exportType);

		return $result;
	}

/**
 * Returns a row of table foother with group actions controls.
 *
 * @param array $formInputs Array of inputs for filter form
 * @param array $groupActions List of group actions.
 * @param bool $useSelectAll If True, display `Select all` button.
 *
 * @return string Row of table foother.
 */
	public function createGroupActionControls($formInputs = null, $groupActions = null, $useSelectAll = false) {
		$result = '';
		if (empty($formInputs) || !is_array($formInputs) ||
			(!empty($groupActions) && !is_array($groupActions)) ||
			(empty($groupActions) && !$useSelectAll)) {
			return $result;
		}
		if (empty($groupActions)) {
			$groupActions = [];
		}

		$colspan = null;
		$countFormInputs = count($formInputs);
		if ($countFormInputs > 2) {
			$colspan = $countFormInputs;
		}
		$selectOptions = [];
		foreach ($groupActions as $value => $text) {
			$selectOptions[] = compact('text', 'value');
		}

		$tableFoother = [];
		if ($useSelectAll) {
			if (!empty($colspan)) {
				$colspan--;
			}
			$tableFoother[] = $this->_getOptionsForElem('createGroupActionControls.selectAllBtn');
		}
		$inputField = '';
		if (!empty($selectOptions)) {
			$this->ExtBs3Form->unlockField('FilterGroup.action');
			$tableFoother[] = [
				$this->ExtBs3Form->hidden('FilterGroup.action') . $this->_getOptionsForElem('createGroupActionControls.groupDataProcTitle'),
				['colspan' => $colspan, 'class' => 'text-center']
			];
			$btnOptions = ['data-dialog-sel-options' => htmlentities(json_encode($selectOptions))];
			$optionsDefault = $this->_getOptionsForElem('createGroupActionControls.performActionBtn');
			$tableFoother[] = [
				$this->ViewExtension->button(
					'fas fa-cog',
					'btn-warning',
					$btnOptions + $optionsDefault
				),
				['class' => 'action text-center']];
		} else {
			if (!empty($colspan)) {
				$colspan++;
			}
			$tableFoother[] = ['',
				['colspan' => $colspan]];
		}

		$result = $this->Html->tableCells([$tableFoother], ['class' => 'active'], ['class' => 'active']);

		return $result;
	}

/**
 * Returns control checkbox for select row of table.
 *
 * @param strind $inputField Field of filter in format `model.field`;
 * @param int $value Value of checkbox.
 *
 * @return string Form input checkbox for select row of table
 */
	public function createFilterRowCheckbox($inputField = null, $value = null) {
		$result = '';
		if (is_array($inputField)) {
			$inputField = array_keys($inputField);
			$inputField = (string)array_shift($inputField);
		}
		if (empty($inputField) || (strpos($inputField, '.') === false)) {
			return $result;
		}

		$uniqid = uniqid();
		$dataPath = 'FilterData.0.' . $inputField;
		$inputFieldName = $dataPath . '.';
		$data = (array)$this->request->data($dataPath);
		$options = [
			'value' => $value,
			'checked ' => in_array($value, $data),
			'hiddenField' => false,
			'required' => false,
			'secure' => false,
		];
		$this->setEntity($inputFieldName . $uniqid);
		$options = $this->domId($options);
		$options = $this->_initInputField($inputFieldName, $options);
		$result = $this->ExtBs3Form->checkbox($inputFieldName, $options) .
			$this->ExtBs3Form->label($inputFieldName . $uniqid, '');
		$result = $this->Html->div('checkbox', $result);

		return $result;
	}

/**
 * Return button with dropdown list of conditions for form filter input field.
 *
 * @param string $inputCondField Field name for creation button with condition.
 * @param bool $disabled State of condition button: disabled or not.
 *
 * @return string Button with dropdown list of conditions.
 */
	public function getBtnConditionField($inputCondField = null, $disabled = false) {
		$options = $this->_getOptionsForElem('getBtnConditionField.options');
		$title = $this->_getOptionsForElem('getBtnConditionField.title');

		return $this->_getBtnCondition($inputCondField, $options, $title, $disabled);
	}

/**
 * Return button with dropdown list of group conditions for form filter input field.
 *
 * @param string $inputCondField Field name for creation button with condition.
 *
 * @return string Button with dropdown list of conditions.
 */
	public function getBtnConditionGroup($inputCondField = null) {
		$options = $this->_getOptionsForElem('getBtnConditionGroup.options');
		$title = $this->_getOptionsForElem('getBtnConditionGroup.title');

		return $this->_getBtnCondition($inputCondField, $options, $title, false);
	}

/**
 * Return button with dropdown list of conditions for form filter input field.
 *
 * @param string $inputCondField Field name for creation button with condition.
 * @param array $options List conditions options for dropdown.
 * @param string $title Text of tooltip for button.
 * @param bool $disabled State of condition button: disabled or not.
 *
 * @return string Button with dropdown list of conditions.
 */
	protected function _getBtnCondition($inputCondField = null, $options = null, $title = null, $disabled = false) {
		$result = '';
		if (empty($inputCondField) || empty($options) ||
			!is_array($options)) {
			return $result;
		}

		$optionsDefault = $this->_getOptionsForElem('getBtnCondition');
		$condOptions = [
			'options' => $options,
			'title' => $title,
		] + $optionsDefault;
		$condOptions = $this->_initInputField($inputCondField, $condOptions);
		$this->ExtBs3Form->unlockField($inputCondField);
		$result = $this->ExtBs3Form->input($inputCondField, $condOptions);

		return $result;
	}

/**
 * Return request data for filter from GET or POST request.
 *
 * @param string $baseKey The name of the base parameter to retrieve the configurations.
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return array|mixed On success array data from request if its not empty or null on failure.
 */
	protected function _getFilterRequestData($baseKey = null, $key = null) {
		$filterData = [];
		if (empty($baseKey)) {
			return $filterData;
		}

		if ($this->_getFlagUsePost()) {
			$requestData = $this->request->data($baseKey);
			if (!empty($requestData)) {
				$filterData = $requestData;
			}
		} else {
			$requestData = $this->request->query('data.' . $baseKey);
			if (!empty($requestData)) {
				$filterData = array_map('unserialize', array_unique(array_map('serialize', $requestData)));
			}
		}
		if (!empty($key)) {
			return Hash::get($filterData, $key);
		}

		return $filterData;
	}

/**
 * Return conditions for filter from GET or POST request.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return array Conditions for filter.
 */
	public function getFilterConditions($key = null) {
		return $this->_getFilterRequestData('FilterCond', $key);
	}

/**
 * Return data for filter from GET or POST request.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return array Data for filter.
 */
	public function getFilterData($key = null) {
		return $this->_getFilterRequestData('FilterData', $key);
	}

/**
 * Set filter input data.
 *
 * @param mixed $data Data for set.
 * @return void
 */
	public function setFilterInputData($data = null) {
		$this->request->data('FilterData', $data);
	}

/**
 * Set filter conditions input data.
 *
 * @param mixed $data Data for set.
 * @return void
 */
	public function setFilterInputConditions($data = null) {
		$this->request->data('FilterCond', $data);
	}

/**
 * Set type form inputs for field, adding condition button and removing
 *  attibute `pagination-field`.
 *
 * @param string $fieldName Field name for preparing.
 * @param array $options Options of form input for preparing.
 * @param int $index Index of form input.
 * @param string $plugin Name of plugin for target model of filter.
 * @return array Array of prepared options for form input.
 */
	public function prepareOptions($fieldName, $options, $index = 0, $plugin = null) {
		$index = (int)$index;
		if (empty($options) || !is_array($options) || ($index < 0)) {
			return $options;
		}

		$inputType = null;
		if (isset($options['options']) && !empty($options['options'])) {
			$inputType = 'select';
		}
		if (isset($options['type']) && !empty($options['type'])) {
			$inputType = $options['type'];
		}
		if ($inputType === 'select') {
			$optionsDefault = $this->_getOptionsForElem('prepareOptions.select');
			$options = $optionsDefault + $options;
		}
		$disabled = false;
		if ((isset($options['disabled']) && $options['disabled'])) {
			$disabled = true;
		}
		if (isset($options['pagination-field'])) {
			unset($options['pagination-field']);
		}

		if (strpos($fieldName, '.') === false) {
			return $options;
		}

		list($model, $field) = pluginSplit($fieldName);
		if (empty($model) || empty($field)) {
			return $options;
		}

		if (!empty($plugin)) {
			$model = $plugin . '.' . $model;
		}
		$schema = $this->_getSchemaCache($model);
		if (empty($schema)) {
			$schema = $this->_getSchema($model);
		}

		if (empty($schema)) {
			return $options;
		}

		$this->_setSchemaCache($model, $schema);
		if (!isset($schema[$field]['type'])) {
			return $options;
		}

		$inputField = 'FilterData.' . $index . '.' . $fieldName;
		$inputCondField = 'FilterCond.' . $index . '.' . $fieldName;
		$options = $this->_initInputField($inputField, $options);
		if (!empty($inputType)) {
			return $options;
		}

		if (!isset($options['type']) || empty($options['type'])) {
			$options['type'] = 'text';
		}
		$btnCondition = $this->getBtnConditionField($inputCondField, $disabled);
		$btnCondition = $this->Html->div('input-group-btn', $btnCondition);
		$fieldType = $schema[$field]['type'];
		switch ($fieldType) {
			case 'string':
			case 'text':
				$options['data-autocomplete-type'] = $fieldName;
				if (!empty($plugin)) {
					$options['data-autocomplete-plugin'] = $plugin;
				}
				$options['class'] = ((isset($options['class']) && !empty($options['class'])) ? $options['class'] . ' ' : '') .
					'clear-btn';
				$optionsDefault = $this->_getOptionsForElem('prepareOptions.string');
				$options = $optionsDefault + $options;
				break;
			case 'integer':
			case 'biginteger':
			case 'float':
				$options['beforeInput'] = $btnCondition;
				switch ($fieldType) {
					case 'integer':
					case 'biginteger':
						$options['type'] = 'integer';
						break;
					case 'float':
						$options['type'] = 'float';
						break;
				}
				$optionsDefault = $this->_getOptionsForElem('prepareOptions.integer');
				$options = $optionsDefault + $options;
				break;
			case 'boolean':
				$optionsDefault = $this->_getOptionsForElem('prepareOptions.boolean');
				$options = $optionsDefault + $options;
				break;
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
				$options['beforeInput'] = $btnCondition;
				switch ($fieldType) {
					case 'date':
						$options['type'] = 'dateSelect';
						break;
					case 'datetime':
					case 'timestamp':
						$options['type'] = 'dateTimeSelect';
						break;
					case 'time':
						$options['type'] = 'timeSelect';
						break;
				}
				$optionsDefault = $this->_getOptionsForElem('prepareOptions.date');
				$options = $optionsDefault + $options;
				break;
			case 'binary':
				$optionsDefault = $this->_getOptionsForElem('prepareOptions.binary');
				$options = $optionsDefault + $options;
				break;
		}

		return $options;
	}

/**
 * Return extended options for pagination URL.
 * If request extension equal `prt`, return `ext` option.
 *
 * @return array Array of options for pagination URL.
 */
	protected function _getExtendOptionsPaginationUrl() {
		$options = [];
		$ext = (string)$this->request->param('ext');
		$ext = mb_strtolower($ext);
		if (empty($ext) || ($ext !== 'prt')) {
			return $options;
		}

		$options = compact('ext');

		return $options;
	}
}
