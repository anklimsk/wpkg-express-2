<?php
/**
 * This file is the Form helper file of the CakePHP based on Bs3FormHelper.
 * Use for fix radio and checkbox input create by BS3Helper.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('Bs3FormHelper', 'Bs3Helpers.View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('Language', 'CakeBasicFunctions.Utility');
App::uses('Hash', 'Utility');

/**
 * ExtBs3FormHelper Form helper.
 *
 * @package plugin.View.Helper
 */
class ExtBs3FormHelper extends Bs3FormHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = [
		'CakeTheme.ViewExtension'
	];

/**
 * Stores the Language() utility object.
 *
 * @var object
 */
	protected $_Language = null;

/**
 * Stores default options for helper methods.
 *
 * @var array
 */
	protected $_optionsForElem = [];

/**
 * Current language of UI.
 *
 * @var string
 */
	protected $_currUIlang;

/**
 * Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = []) {
		parent::__construct($View, $settings);

		$this->_currUIlang = (string)Configure::read('Config.language');
		if (empty($this->_currUIlang)) {
			$this->_currUIlang = 'eng';
		}
		$this->_Language = new Language();
		$this->_optionsForElem = $this->_getListOptionsForElem();
	}

/**
 * Returns an HTML FORM element.
 *  Fix Ignore options `url` => false
 *
 * @param mixed|null $model The model name for which the form is being defined. Should
 *   include the plugin name for plugin models. e.g. `ContactManager.Contact`.
 *   If an array is passed and $options argument is empty, the array will be used as options.
 *   If `false` no model is used.
 * @param array $options An array of html attributes and options.
 * @return string A formatted opening FORM tag.
 * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-create
 */
	public function create($model = null, $options = []) {
		$form = parent::create($model, $options);
		if (!isset($options['url']) || ($options['url'] !== false)) {
			return $form;
		}

		//  Ignore options `url` => false
		$result = mb_ereg_replace('action=\"[^\"]*\"', '', $form);

		return $result;
	}

/**
 * Return default options by path.
 *
 * @param string $path Path to retrieve options
 * @return mixed Return default options by path.
 */
	protected function _getOptionsForElem($path = null) {
		if (empty($path)) {
			return null;
		}
		$result = Hash::get($this->_optionsForElem, $path);

		return $result;
	}

/**
 * Return default all options.
 *
 * @return mixed Return all default options.
 */
	protected function _getListOptionsForElem() {
		$cachePath = 'DefaultOptions_Form_' . $this->_currUIlang;
		$cached = Cache::read($cachePath, CAKE_THEME_CACHE_KEY_HELPERS);
		if (!empty($cached)) {
			return $cached;
		}

		$result = [];
		$result['select'] = [
			'extraOpt' => [
				'actions-box',
				'container',
				'count-selected-text',
				'deselect-all-text',
				'dropdown-align-right',
				'dropup-auto',
				'header',
				'hide-disabled',
				'icon-base',
				'live-search',
				'live-search-normalize',
				'live-search-placeholder',
				'live-search-style',
				'max-options',
				'max-options-text',
				'mobile',
				'multiple-separator',
				'none-selected-text',
				'select-all-text',
				'selected-text-format',
				'select-on-tab',
				'show-content',
				'show-icon',
				'show-subtext',
				'show-tick',
				'size',
				'style',
				'tick-icon',
				'width',
				'window-padding',
			]
		];
		$result['dateTime'] = [
			'defaultOpt' => [
				'label' => false,
				'data-toggle' => 'datetimepicker'
			],
			'extraOpt' => [
				'date-format',
				'date-locale',
				'icon-type',
				'widget-position-horizontal',
				'widget-position-vertical'
			]
		];
		$result['spin'] = [
			'defaultOpt' => [
				'data-toggle' => 'spin',
				'data-spin-verticalbuttons' => 'true',
				'label' => false,
			],
			'extraOpt' => [
				'min',
				'max',
				'step',
				'decimals',
				'maxboostedstep',
				'verticalbuttons',
				'prefix',
				'prefix_extraclass',
				'postfix',
				'postfix_extraclass',
			]
		];
		$result['flag'] = [
			'defaultOpt' => [
				'type' => 'select'
			],
			'extraOpt' => [
				'country-url',
			],
			'divOpt' => [
				'data-toggle' => 'flag-select',
				'data-button-type' => 'btn-default form-control',
			]
		];
		$result['autocomplete'] = [
			'defaultOpt' => [
				'data-toggle' => 'autocomplete',
				'label' => false,
			],
			'extraOpt' => [
				'type',
				'plugin',
				'url',
				'local',
				'min-length'
			]
		];
		$result['createUploadForm'] = [
			'type' => 'file',
			'default' => false,
			'url' => false
		];
		$result['upload'] = [
			'btnTitle' => $this->Html->tag('span', __d('view_extension', 'Select file...')),
			'inputOpt' => [
				'type' => 'file',
				'secure' => false,
				'label' => false,
				'div' => false,
				'data-toggle' => 'fileupload',
				'name' => 'files',
				'data-fileupload-btntext-upload' => __d('view_extension', 'Upload'),
				'data-fileupload-btntext-abort' => __d('view_extension', 'Abort'),
				'data-fileupload-msgtext-processing' => __d('view_extension', 'Processing...'),
				'data-fileupload-msgtext-error' => __d('view_extension', 'File upload failed'),
			]
		];
		$result['createFormTabs'] = [
			'tabListOpt' => [
				'class' => 'nav nav-pills nav-stacked col-xs-4 col-sm-4 col-md-4 col-lg-4'
			],
			'tabContentClass' => 'tab-content col-xs-8 col-lg-8 col-sm-8 col-md-8 col-lg-8',
			'submitBtn' => [
				'title' => __d('view_extension', 'Save'),
				'options' => [
					'class' => 'btn btn-success btn-md',
					'div' => 'form-group text-center'
				]
			]
		];
		Cache::write($cachePath, $result, CAKE_THEME_CACHE_KEY_HELPERS);

		return $result;
	}

/**
 * If label is a array, transform it to proper label options array with the text option
 *
 * @param array $options An array of HTML attributes.
 * @return array An array of HTML attributes
 */
	protected function _initLabel($options) {
		if (isset($options['label']) && is_array($options['label'])) {
			$options['label'] = [
				'text' => $options['label']
			];
		}

		return parent::_initLabel($options);
	}

/**
 * Returns a formatted LABEL element for HTML FORMs. Will automatically generate
 * a `for` attribute if one is not provided.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param string|array $text Text that will appear in the label field. If
 *  $text is left undefined the text will be inflected from the
 *  fieldName. If $text is array use format:
 *  - key `0`: value - Text of label;
 *  - key `1`: value - Tooltip of label;
 *  - key `2`: value - Postfix text of label.
 * @param array|string $options An array of HTML attributes, or a string, to be used as a class name.
 * @return string The formatted LABEL element
 * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::label
 */
	public function label($fieldName = null, $text = null, $options = []) {
		$result = '';
		$labelText = $text;
		if (is_array($text)) {
			if (count($text) < 1) {
				return $result;
			}
			$labelText = array_shift($text);
			$tooltipText = array_shift($text);
			$labelPostfix = array_shift($text);
			if (!empty($tooltipText)) {
				$labelText .= '&nbsp;' . $this->Html->tag(
					'abbr',
					'[?]',
					['title' => $tooltipText, 'data-toggle' => 'tooltip']
				);
			}
			if (!empty($labelPostfix)) {
				$labelText .= $labelPostfix;
			}
		}

		return parent::label($fieldName, $labelText, $options);
	}

/**
 * Returns text for label from field name.
 *
 * @param string $fieldName Field name
 * @return string Text for label
 */
	public function getLabelTextFromField($fieldName = null) {
		$text = '';
		if (empty($fieldName)) {
			return $text;
		}

		if (strpos($fieldName, '.') !== false) {
			$fieldElements = explode('.', $fieldName);
			$text = array_pop($fieldElements);
		} else {
			$text = $fieldName;
		}
		if (substr($text, -3) === '_id') {
			$text = substr($text, 0, -3);
		}
		$text = Inflector::humanize(Inflector::underscore($text));

		return $text;
	}

/**
 * Prepare extra option for form input.
 *
 * @param array &$options List of options for prepare
 * @param string|array $listExtraOptions List of extra options.
 * @param string $optionPrefix Prefix for extra options.
 * @return void
 */
	protected function _prepareExtraOptions(array &$options, $listExtraOptions = [], $optionPrefix = 'data-') {
		if (empty($options) || empty($listExtraOptions)) {
			return;
		}

		if (!is_array($listExtraOptions)) {
			$listExtraOptions = [$listExtraOptions];
		}
		$extraOptions = array_intersect_key($options, array_flip($listExtraOptions));
		if (empty($extraOptions)) {
			return;
		}

		foreach ($extraOptions as $extraOptionName => $extraOptionValue) {
			if (is_bool($extraOptionValue)) {
				if ($extraOptionValue) {
					$extraOptionValue = 'true';
				} else {
					$extraOptionValue = 'false';
				}
			}
			$options[$optionPrefix . $extraOptionName] = $extraOptionValue;
			unset($options[$extraOptionName]);
		}
	}

/**
 * Returns a formatted SELECT element.
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 *  SELECT element
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted SELECT element
 * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function select($fieldName, $options = [], $attributes = []) {
		if (empty($attributes)) {
			$attributes = [];
		}

		$label = '';
		if (isset($attributes['label']) && ($attributes['label'] !== false)) {
			$label = $this->label($fieldName, $attributes['label']);
			$attributes['label'] = false;
		}
		if (!isset($attributes['multiple']) || ($attributes['multiple'] !== 'checkbox')) {
			$attributes['data-toggle'] = 'select';
			$listExtraOptions = $this->_getOptionsForElem('select.extraOpt');
			$this->_prepareExtraOptions($attributes, $listExtraOptions);
		}
		$this->_initInputOptions([]);
		$select = parent::select($fieldName, $options, $attributes);
		$result = $label . $select;
		if (!empty($label)) {
			$result = $this->Html->div('form-group', $result);
		}

		return $result;
	}

/**
 * Creates input with input mask alias.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param string $alias Input mask alias.
 * @return string An HTML text input element.
 * @see {@link https://github.com/RobinHerbots/jquery.inputmask} jQuery.Inputmask
 */
	protected function _inputMaskAlias($fieldName, $options = [], $alias = '') {
		if (!is_array($options)) {
			$options = [];
		}
		$defaultOptions = [
			'label' => false,
		];
		$options += $defaultOptions;
		$options['data-inputmask-alias'] = $alias;

		return $this->text($fieldName, $options);
	}

/**
 * Creates a Date/Time Picker input widget.
 *
 * ### Options:
 *
 *  `date-format` - Date format, in js moment format;
 *  `date-locale` - Current locale, e.g. `en`;
 *  `icon-type` - Icon for button, e.g. `date` or `time`;
 *  `widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
 *  `widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
 *   Set to false form disable button.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param string $type Type of widget: date, time or datetime.
 * @return string An HTML text input element.
 * @see {@link https://github.com/Eonasdan/bootstrap-datetimepicker} Twitter Bootstrap 3 Date/Time Picker
 */
	protected function _dateTime($fieldName, $options = [], $type = null) {
		if (!is_array($options)) {
			$options = [];
		}
		$optionsDefault = $this->_getOptionsForElem('dateTime.defaultOpt');
		$options = $optionsDefault + $options;
		switch (mb_strtolower($type)) {
			case 'time':
				$iconType = 'time';
				$dateFormat = 'HH:mm:ss';
				$inputmaskAlias = 'hh:mm:ss';
				break;
			case 'datetime':
				$iconType = 'date';
				$dateFormat = 'YYYY-MM-DD HH:mm:ss';
				$inputmaskAlias = 'yyyy-mm-dd hh:mm:ss';
				break;
			case 'date':
			default:
				$iconType = 'date';
				$dateFormat = 'YYYY-MM-DD';
				$inputmaskAlias = 'yyyy-mm-dd';
		}
		if (!isset($options['icon-type'])) {
			$options['icon-type'] = $iconType;
		}
		if (!isset($options['date-format'])) {
			$options['date-format'] = $dateFormat;
		}
		if (!isset($options['data-inputmask-alias'])) {
			$options['data-inputmask-alias'] = $inputmaskAlias;
		}
		if (!isset($options['data-date-locale'])) {
			$options['data-date-locale'] = $this->_Language->getCurrentUiLang(true);
		}
		$listExtraOptions = $this->_getOptionsForElem('dateTime.extraOpt');
		$this->_prepareExtraOptions($options, $listExtraOptions);

		return $this->text($fieldName, $options);
	}

/**
 * Creates a email input widget.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/RobinHerbots/jquery.inputmask} jQuery.Inputmask
 */
	public function email($fieldName, $options = []) {
		return $this->_inputMaskAlias($fieldName, $options, 'email');
	}

/**
 * Creates a integer input widget.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/RobinHerbots/jquery.inputmask} jQuery.Inputmask
 */
	public function integer($fieldName, $options = []) {
		return $this->_inputMaskAlias($fieldName, $options, 'integer');
	}

/**
 * Creates a float input widget.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/RobinHerbots/jquery.inputmask} jQuery.Inputmask
 */
	public function float($fieldName, $options = []) {
		return $this->_inputMaskAlias($fieldName, $options, 'decimal');
	}

/**
 * Creates a date Picker input widget.
 *
 * ### Options:
 *
 *  `date-format` - Date format, in js moment format;
 *  `date-locale` - Current locale, e.g. `en`;
 *  `icon-type` - Icon for button, e.g. `date` or `time`;
 *  `widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
 *  `widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
 *   Set to false form disable button.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/Eonasdan/bootstrap-datetimepicker} Twitter Bootstrap 3 Date/Time Picker
 */
	public function dateSelect($fieldName, $options = []) {
		return $this->_dateTime($fieldName, $options, 'date');
	}

/**
 * Creates a time Picker input widget.
 *
 * ### Options:
 *
 *  `date-format` - Date format, in js moment format;
 *  `date-locale` - Current locale, e.g. `en`;
 *  `icon-type` - Icon for button, e.g. `date` or `time`;
 *  `widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
 *  `widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
 *   Set to false form disable button.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/Eonasdan/bootstrap-datetimepicker} Twitter Bootstrap 3 Date/Time Picker
 */
	public function timeSelect($fieldName, $options = []) {
		return $this->_dateTime($fieldName, $options, 'time');
	}

/**
 * Creates a date and time Picker input widget.
 *
 * ### Options:
 *
 *  `date-format` - Date format, in js moment format;
 *  `date-locale` - Current locale, e.g. `en`;
 *  `icon-type` - Icon for button, e.g. `date` or `time`;
 *  `widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
 *  `widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
 *   Set to false form disable button.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/Eonasdan/bootstrap-datetimepicker} Twitter Bootstrap 3 Date/Time Picker
 */
	public function dateTimeSelect($fieldName, $options = []) {
		return $this->_dateTime($fieldName, $options, 'datetime');
	}

/**
 * Creates a touch spin input widget.
 *
 * ### Options:
 *
 * - `min` - Minimum value;
 * - `max` - Maximum value;
 * - `step` - Incremental/decremental step on up/down change;
 * - `decimals` - Number of decimal points;
 * - `maxboostedstep` - Maximum step when boosted;
 * - `verticalbuttons` - Enables the traditional up/down buttons;
 * - `prefix` - Text before the input;
 * - `prefix_extraclass` - Extra class(es) for prefix;
 * - `postfix` - Text after the input;
 * - `postfix_extraclass` - Extra class(es) for postfix.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link http://www.virtuosoft.eu/code/bootstrap-touchspin} TouchSpin
 */
	public function spin($fieldName, $options = []) {
		if (!is_array($options)) {
			$options = [];
		}
		$defaultOptions = $this->_getOptionsForElem('spin.defaultOpt');
		$options = $defaultOptions + $options;
		$listExtraOptions = $this->_getOptionsForElem('spin.extraOpt');
		$this->_prepareExtraOptions($options, $listExtraOptions, 'data-spin-');
		$numbers = '';
		$decimals = '';
		if (isset($options['data-spin-max']) && !empty($options['data-spin-max'])) {
			$numbers = mb_strlen($options['data-spin-max']);
		}
		if (isset($options['data-spin-decimals']) && !empty($options['data-spin-decimals'])) {
			$decimals = (int)$options['data-spin-decimals'];
		}

		$options['data-inputmask-mask'] = '9{1,' . $numbers . '}';
		if (!empty($decimals)) {
			$options['data-inputmask-mask'] .= '.9{' . $decimals . '}';
		}

		return $this->text($fieldName, $options);
	}

/**
 * Creates a flagstrap input widget.
 *
 * ### Options:
 *
 * - `options` - array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 *  SELECT element.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/blazeworx/flagstrap} Flagstrap
 */
	public function flag($fieldName, $options = []) {
		if (!empty($fieldName)) {
			$this->setEntity($fieldName);
		}

		$this->unlockField($this->field());
		if (!is_array($options)) {
			$options = [];
		}
		$defaultOptions = $this->_getOptionsForElem('flag.defaultOpt');
		$options = $defaultOptions + $options;
		$divClass = 'form-group';
		$label = '';
		$list = [];
		$options = $this->_optionsOptions($options);
		if (isset($options['label'])) {
			$label = $this->label($fieldName, $options['label'], ['class' => 'control-label']);
		}
		if (isset($options['options'])) {
			$list = $options['options'];
		}
		$listExtraOptions = $this->_getOptionsForElem('flag.extraOpt');
		$this->_prepareExtraOptions($options, $listExtraOptions);
		$errors = FormHelper::error($fieldName, null, ['class' => 'help-block']);
		if (!empty($errors)) {
			$divClass .= ' has-error error';
		}
		$divOptions = [
			'id' => $this->domId($fieldName),
			'data-input-name' => $this->_name(null, $fieldName),
			'data-selected-country' => $this->value($fieldName),
		];
		$divOptionsDefault = $this->_getOptionsForElem('flag.divOpt');
		if (!empty($list)) {
			$divOptions['data-countries'] = json_encode($list);
		}
		foreach ($listExtraOptions as $extraOptionName) {
			if (isset($options['data-' . $extraOptionName])) {
				$divClass['data-' . $extraOptionName] = $options['data-' . $extraOptionName];
			}
		}
		$result = $this->Html->div($divClass, $label .
			$this->Html->div(null, '', $divOptions + $divOptionsDefault) . $errors);

		return $result;
	}

/**
 * Creates text input with autocomplete.
 *
 * ### Options:
 *
 * - `type` - Type for autocomplete suggestions, e.g. Model.Field;
 * - `plugin` - Plugin name for autocomplete field;
 * - `url` - URL for autocomplete;
 * - `local` - Local data for autocomplete;
 * - `min-length` - minimal length of query string.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @see {@link https://github.com/bassjobsen/Bootstrap-3-Typeahead} Bootstrap 3 Typeahead
 */
	public function autocomplete($fieldName, $options = []) {
		if (!is_array($options)) {
			$options = [];
		}
		$defaultOptions = $this->_getOptionsForElem('autocomplete.defaultOpt');
		$options = $defaultOptions + $options;
		$listExtraOptions = $this->_getOptionsForElem('autocomplete.extraOpt');
		$this->_prepareExtraOptions($options, $listExtraOptions, 'data-autocomplete-');

		if (!isset($options['data-autocomplete-url']) && !isset($options['data-autocomplete-local'])) {
			$options['data-autocomplete-url'] = $this->url(
				$this->ViewExtension->addUserPrefixUrl([
					'controller' => 'filter',
					'action' => 'autocomplete',
					'plugin' => 'cake_theme',
					'ext' => 'json',
					'prefix' => false
				])
			);
		}
		if (isset($options['data-autocomplete-local']) && !empty($options['data-autocomplete-local'])) {
			if (!is_array($options['data-autocomplete-local'])) {
				$options['data-autocomplete-local'] = [$options['data-autocomplete-local']];
			}
			$options['data-autocomplete-local'] = json_encode($options['data-autocomplete-local']);
		}

		return $this->text($fieldName, $options);
	}

/**
 * Returns an HTML FORM element for upload file.
 *
 * ### Options:
 *
 * - `type` Form method defaults to POST
 * - `action`  The controller action the form submits to, (optional). Deprecated since 2.8, use `url`.
 * - `url`  The URL the form submits to. Can be a string or a URL array. If you use 'url'
 *	you should leave 'action' undefined.
 * - `default`  Allows for the creation of AJAX forms. Set this to false to prevent the default event handler.
 *   Will create an onsubmit attribute if it doesn't not exist. If it does, default action suppression
 *   will be appended.
 * - `onsubmit` Used in conjunction with 'default' to create AJAX forms.
 * - `inputDefaults` set the default $options for FormHelper::input(). Any options that would
 *   be set when using FormHelper::input() can be set here. Options set with `inputDefaults`
 *   can be overridden when calling input()
 * - `encoding` Set the accept-charset encoding for the form. Defaults to `Configure::read('App.encoding')`
 *
 * @param mixed|null $model The model name for which the form is being defined. Should
 *   include the plugin name for plugin models. e.g. `ContactManager.Contact`.
 *   If an array is passed and $options argument is empty, the array will be used as options.
 *   If `false` no model is used.
 * @param array $options An array of html attributes and options.
 * @return string A formatted opening FORM tag.
 * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-create
 */
	public function createUploadForm($model = null, $options = []) {
		if (empty($options) || !is_array($options)) {
			$options = [];
		}
		$optionsDefault = $this->_getOptionsForElem('createUploadForm');
		$options = $this->ViewExtension->getFormOptions($optionsDefault + $options);
		$result = $this->create($model, $options);

		return $result;
	}

/**
 * Creates a upload input widget.
 *
 * @param string $url URL for upload
 * @param int $maxfilesize Maximum file size for upload, bytes.
 * @param string $acceptfiletypes PCRE for checking uploaded file,
 *  e.g.: (\.|\/)(jpe?g)$.
 * @param string $redirecturl URL for redirect on successful upload.
 * @param string $btnTitle Title of upload button.
 * @param string $btnClass Class of upload button.
 * @return string An HTML text input element.
 * @see {@link https://blueimp.github.io/jQuery-File-Upload} File Upload
 */
	public function upload($url = null, $maxfilesize = 0, $acceptfiletypes = null, $redirecturl = null, $btnTitle = null, $btnClass = null) {
		if (empty($url)) {
			return null;
		}

		if (empty($btnTitle)) {
			$btnTitle = $this->ViewExtension->iconTag('fas fa-file-upload') . '&nbsp;' . $this->_getOptionsForElem('upload.btnTitle');
		}
		if (empty($btnClass)) {
			$btnClass = 'btn-success';
		}
		$inputId = uniqid('input_');
		$progressId = uniqid('progress_');
		$filesId = uniqid('files_');
		$inputOptions = [
			'id' => $inputId,
			'data-progress-id' => $progressId,
			'data-files-id' => $filesId,
			'data-fileupload-url' => $url,
			'data-fileupload-maxfilesize' => $maxfilesize,
			'data-fileupload-acceptfiletypes' => $acceptfiletypes,
			'data-fileupload-redirecturl' => $redirecturl
		];
		$optionsDefault = $this->_getOptionsForElem('upload.inputOpt');
		$result = $this->Html->tag(
			'span',
			$btnTitle . $this->input(null, $inputOptions + $optionsDefault),
			['class' => 'fileinput-button btn ' . $btnClass]
		) .
			$this->Html->tag('br') .
			$this->Html->tag('br') .
			$this->Html->div('progress', $this->Html->div('progress-bar progress-bar-success', ''), ['id' => $progressId]) .
			$this->Html->tag('hr') .
			$this->Html->div('files', '', ['id' => $filesId]) .
			$this->Html->tag('br');

		return $result;
	}

/**
 * Create hidden fields with validation error message.
 *
 * @param array|string $hiddenFields List of hidden fields
 *  name, like this "Modelname.fieldname"
 * @return string|null An HTML hidden input elements with error message block.
 */
	public function hiddenFields($hiddenFields = null) {
		if (empty($hiddenFields)) {
			return null;
		}

		if (!is_array($hiddenFields)) {
			$hiddenFields = [$hiddenFields];
		}

		$result = '';
		$options = ['type' => 'hidden'];
		foreach ($hiddenFields as $hiddenField) {
			$result .= $this->input($hiddenField, $options);
			if ($this->isFieldError($hiddenField)) {
				$result .= $this->Html->div(
					'form-group has-error',
					$this->error($hiddenField, null, ['class' => 'help-block'])
				);
			}
		}

		return $result;
	}

/**
 * Create tabable form.
 *
 * @param array $inputList List of input fields.
 * @param array $inputStaticList List of static input fields.
 * @param array $tabsList List of tabs.
 * @param string $legend Text of legend HTML tag.
 * @param string $modelName The model name for which the form
 *  is being defined.
 * @param array $options An array of html attributes and options.
 * @return string|null An HTML of tabable form.
 */
	public function createFormTabs($inputList = null, $inputStaticList = null, $tabsList = null, $legend = null, $modelName = null, $options = []) {
		if (empty($inputList) || empty($tabsList)) {
			return null;
		}

		if (empty($inputStaticList)) {
			$inputStaticList = [];
		}

		if (empty($options) || !is_array($options)) {
			$options = [];
		}

		$optionsDefault = [
			'class' => 'form-tabs form-default',
			'progressfill' => true,
		];
		$options = $this->ViewExtension->getFormOptions($optionsDefault + $options);
		$result = $this->create($modelName, $options);
		$result .= '<fieldset>';
		if (!empty($legend)) {
			$result .= $this->Html->tag('legend', $legend);
		}

		$result .= '<div class="row bottom-buffer">';
		$inputsDefaultOptions = [
			'legend' => false,
			'fieldset' => false
		];

		$id = 1;
		$navTabs = '';
		$navContent = '';
		foreach ($tabsList as $tabName => $tabFields) {
			$tabError = false;
			$tabInputs = [];
			$hiddenInputs = [];
			$staticInputs = [];
			foreach ($tabFields as $tabField) {
				if ($this->isFieldError($tabField)) {
					$tabError = true;
				}
				if (isset($inputList[$tabField])) {
					$fieldOptions = $inputList[$tabField];
					if ((isset($fieldOptions['type'])) && ($fieldOptions['type'] === 'hidden')) {
						$hiddenInputs[] = $tabField;
					} else {
						$tabInputs[$tabField] = $fieldOptions;
					}
				}
				if (isset($inputStaticList[$tabField])) {
					$staticInputs[$tabField] = $inputStaticList[$tabField];
				}
			}
			if (empty($hiddenInputs) && empty($tabInputs) &&
				empty($staticInputs)) {
				continue;
			}

			$navInputs = '';
			$navInputs .= $this->hiddenFields($hiddenInputs);
			foreach ($staticInputs as $staticField => $staticOptions) {
				$staticValueText = $this->ViewExtension->showEmpty('');
				$staticLabel = null;
				if (isset($staticOptions['label'])) {
					$staticLabel = $staticOptions['label'];
				}
				if (isset($staticOptions['value']) && !empty($staticOptions['value'])) {
					$staticValueText = $staticOptions['value'];
				} else {
					$inputData = $this->request->data($staticField);
					if (!empty($inputData)) {
						$staticValueText = $inputData;
					}
				}
				if (!isset($staticOptions['escape']) || $staticOptions['escape']) {
					$staticValueText = h($staticValueText);
				}
				$navInputs .= $this->staticControl($staticLabel, $staticValueText);
			}
			if (!empty($tabInputs)) {
				$navInputs .= $this->inputs($tabInputs, null, $inputsDefaultOptions);
			}

			if ($tabError) {
				$tabName .= '&nbsp;' . $this->ViewExtension->iconTag('fas fa-exclamation-triangle fa-lg');
			}
			$navTabs .= $this->Html->tag(
				'li',
				$this->Html->link($tabName, '#tabForm' . $id, ['data-toggle' => 'tab', 'escape' => false]),
				['class' => (empty($navTabs) ? 'active' : null)]
			);
			$navContent .= $this->Html->div(
				'tab-pane' . (empty($navContent) ? ' active' : ''),
				$navInputs,
				['id' => 'tabForm' . $id]
			);
			$id++;
		}
		if (!empty($navTabs)) {
			$result .= $this->Html->div('tabbable', $this->Html->tag(
				'ul',
				$navTabs,
				$this->_getOptionsForElem('createFormTabs.tabListOpt')
			) . $this->Html->div($this->_getOptionsForElem('createFormTabs.tabContentClass'), $navContent));
		}
		$result .= '</div></fieldset>';
		$result .= $this->submit(
			$this->_getOptionsForElem('createFormTabs.submitBtn.title'),
			$this->_getOptionsForElem('createFormTabs.submitBtn.options')
		);
		$result .= $this->end();

		return $result;
	}
}
