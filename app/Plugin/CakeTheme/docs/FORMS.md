# Using `ExtBs3Form` helper

1. Map `Form` helper to `ExtBs3Form` helper in your `AppController`:

   ```php
   public $helpers = [
       'Form' => [
           'className' => 'CakeTheme.ExtBs3Form'
       ],
       ...
   ];
   ```

# Creating forms with tabs, with an progress bar of filling the input

1. In your `View` file add:

   ```php
   echo $this->Form->createFormTabs($inputList, $inputStatic, $tabsList, $modelName, $legend);
   ```

   Where:
   - `$inputList` - array of inputs in format: key - model.field, value - form input options;
   - `$inputStatic` - array of static text in format: key - model.field or specific field name, 
     value - array of options: `label` - text of label, `value` - data for rendering (if not set or
     if empty missing data by field name in request data array).
   - `$tabsList` - array config of tabs in format: key - tab label, value - array of inputs fields;
   - `$modelName` - string of model name for which the form is being defined;
   - `$legend` - string of legend in form;
   - `$options` An array of html attributes and options of `Form` element.

## Creating forms for AJAX upload files

1. In your `Controller`:
   - Load `Upload` component:

      ```php
      /**
       * Array containing the names of components this controller uses. Component names
       * should not contain the "Component" portion of the class name.
       *
       * @var array
       * @link http://book.cakephp.org/2.0/en/controllers/components.html
       */
      public $components = [
          'CakeTheme.Upload' => ['uploadDir' => 'path/to/upload/dir'],
          ...
      ];
      ```

   - Unlock `upload` action:

      ```php
      /**
       * Called before the controller action. You can use this method to configure and customize components
       * or perform logic that needs to happen before each controller action.
       *
       * Actions:
       *  - Configure components - unlock upload action.
       *
       * @return void
       * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
       */
      public function beforeFilter() {
          $this->Security->unlockedActions = [
              'upload'
          ];

          parent::beforeFilter();
      }
      ```

   - Add an action method `upload`:

      ```php
      /**
       * Action `upload`. Used to upload file
       *
       * @throws BadRequestException if request is not `AJAX` or not `JSON`
       * @throws MethodNotAllowedException if request is not `POST`
       * @return void
       */
      public function upload() {
          Configure::write('debug', 0);
          if (!$this->request->is('ajax') || !$this->RequestHandler->prefers('json')) {
              throw new BadRequestException(__('Invalid request'));
          }
      
          $this->request->allowMethod('post');
          $uploadDir = $this->Upload->getUploadDir();
          $maxFileSize = 1024 * 1024; // 1 Mb
          $acceptfiletypes = '/\.(jpe?g)$/i'
          $data = $this->Upload->upload($maxFileSize, $acceptfiletypes);
          if (!isset($data['files'][0])) {
              $this->set(compact('data'));
              $this->set('_serialize', 'data');

              return;
          }

          $oFile = $data['files'][0];
          $fileName = $uploadDir . $oFile->name;
          if (!file_exists($fileName)) {
              $this->set(compact('data'));
              $this->set('_serialize', 'data');

              return;
          }

          $oFile->url = '';
          /*
          Process file $fileName
          Set error in need
          $oFile->error = __('Unable to update file.');
          */
          $data['files'][0] = $oFile;

          $this->set(compact('data'));
          $this->set('_serialize', 'data');
      }
      ```

2. In your `View` file add:

   ```php
   echo $this->Form->createUploadForm('SomeModel');
   echo $this->Form->upload($url, $maxfilesize, $acceptfiletypes, $redirecturl, $btnUploadTitle, $btnUploadClass));
   echo $this->Form->end();
   ```

   Where:
   - `$url` - URL for upload;
   - `$maxfilesize` - Maximum file size for upload, bytes.
   - `$acceptfiletypes` -  PCRE for checking uploaded file, e.g.: `(\.|\/)(jpe?g)$`.
   - `$redirecturl` - URL for redirect on successful upload.
   - `$btnTitle` - Title of upload button.
   - `$btnClass` - Class of upload button.
     See https://blueimp.github.io/jQuery-File-Upload

## Render validation error for hidden fields in form

1. In your `View` file add:

   ```php
   echo $this->Form->hiddenFields($hiddenFields);
   ```

   Where `$hiddenFields` - array of hidden fields name,
     like this `Modelname.fieldname`

## Creating forms inputs

### Label with tooltip

Example of label

```php
echo #this->Form->label('SomeModel.field', [__('Label'), __('Tooltip'), ':']);
echo #this->Form->label($fieldName, $text, $options);

// Getting text for label from field name
$text = $this->Form->getLabelTextFromField($fieldName);
echo #this->Form->label($fieldName, $text, $options);
```

Where:
- `$fieldName` This should be `Modelname.fieldname`
- `$text` Text that will appear in the label field. If `$text` is left undefined
 the text will be inflected from the `$fieldName`. If `$text` is array use format:
  * key `0`: value - Text of label;
  * key `1`: value - Tooltip of label;
  * key `2`: value - Postfix text of label.
- `$options` An array of HTML attributes, or a string, to be used as a class name.

### Text input with mask

Example of text input with mask

```php
// Creates a email input widget
echo #this->Form->email($fieldName, $options);

// Creates a integer input widget
echo #this->Form->integer($fieldName, $options);

// Creates a float input widget
echo #this->Form->float($fieldName, $options);

// Creates input with input mask
echo #this->Form->text($fieldName, ['data-inputmask-mask' => '9{1,' . $numbers . '}'/* 'data-toggle' => 'input-mask-mask' */]);

// Creates input with mask alias
echo #this->Form->text($fieldName, ['data-inputmask-alias' => 'email']);

// Creates input with regular expression as a mask
echo #this->Form->text($fieldName, ['data-inputmask-regex' => '[0-9]*'/* 'data-toggle' => 'input-mask-regex' */]);
```

Where:
- `$fieldName` Name of a field, like this `Modelname.fieldname`
- `$options` Array of HTML attributes.
  See https://github.com/RobinHerbots/jquery.inputmask

### Date and time picker

Example of date and time picker

```php
// Creates a date Picker input widget.
echo $this->Form->dateSelect($fieldName, $options);

// Creates a time Picker input widget.
echo $this->Form->timeSelect($fieldName, $options);

// Creates a date and time Picker input widget
echo $this->Form->dateTimeSelect($fieldName, $options);
```

Where:
- `$fieldName` Name of a field, like this "Modelname.fieldname"
- `$options` Array of HTML attributes and widget options:
   * `date-format` - Date format, in js moment format;
   * `date-locale` - Current locale, e.g. `en`;
   * `icon-type` - Icon for button, e.g. `date` or `time`;
   * `widget-position-horizontal` - Horizontal position of widget: `auto`, `left` or `right`;
   * `widget-position-vertical` - Vertical position of widget: `auto`, `top` or `bottom`.
   *  Set to false form disable button.
      See https://github.com/Eonasdan/bootstrap-datetimepicker

### Spinner input

Example of date and time picker

```php
echo $this->Form->spin($fieldName, $options);
```

Where:
- `$fieldName` Name of a field, like this "Modelname.fieldname"
- `$options` Array of HTML attributes and widget options:
   * `min` - Minimum value;
   * `max` - Maximum value;
   * `step` - Incremental/decremental step on up/down change;
   * `decimals` - Number of decimal points;
   * `maxboostedstep` - Maximum step when boosted;
   * `verticalbuttons` -    Enables the traditional up/down buttons;
   * `prefix` - Text before the input;
   * `prefix_extraclass` - Extra class(es) for prefix;
   * `postfix` - Text after the input;
   * `postfix_extraclass` - Extra class(es) for postfix.

### Select with search and `AJAX` loading list

1. In your `Controller`:
   - Unlock `list` action:

      ```php
      /**
       * Called before the controller action. You can use this method to configure and customize components
       * or perform logic that needs to happen before each controller action.
       *
       * Actions:
       *  - Configure components - unlock list action.
       *
       * @return void
       * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
       */
      public function beforeFilter() {
         $this->Security->unlockedActions = [
             'list'
         ];

         parent::beforeFilter();
      }
      ```

   - Add an action method `list`:

      ```php
      /**
       * Action `list`. Used to search in list.
       *
       * POST Data:
       *  - q: query data to search
       *
       * @throws BadRequestException if request is not `AJAX` or not `JSON`
       * @throws MethodNotAllowedException if request is not `POST`
       * @return void
       */
      public function list() {
          Configure::write('debug', 0);
          if (!$this->request->is('ajax') || !$this->RequestHandler->prefers('json')) {
              throw new BadRequestException(__('Invalid request'));
          }

          $this->request->allowMethod('post');
          $query = (string)$this->request->data('q');
          $data = $this->SomeModel->getListByQuery($query);

          $this->set(compact('data'));
          $this->set('_serialize', 'data');
      }
      ```

2. In your `Model` file add method `getListByQuery`:

   ```php
   /**
    * Return list for select input
    *
    * @param string $query Query string
    *  from result
    * @return array Return list for select input
    */
   public function getListByQuery($query = null) {
       $result = [];
       $query = trim($query);
       if (empty($query)) {
           return $result;
       }

       $conditions = ['LOWER(' . $this->alias . '.some_field) like'] = mb_strtolower($query . '%');
       $recursive = -1;
       $result = $this->find('list', compact('conditions', 'recursive'))

       return $result;
   }
   ```

3. In your `View` file add:

   ```php
   echo $this->Form->select(
       'SomeModel.field',
       [
           'options' => $managers,
           'empty' => __('Select data'),
           'data-abs-ajax-url' => $this->Html->url(['controller' => 'ctrl', 'action' => 'list', 'ext' => 'json']),
           'data-abs-ajax-data' => json_encode(['q' => '{{{q}}}']),
           'data-abs-min-length' => 2,
       ]
   );
   ```

### Flagstrap input

Example of flagstrap input widget

```php
echo $this->Form->flag($fieldName, $options);
```

Where:
- `$fieldName` Name of a field, like this "Modelname.fieldname"
- `$options` Array of HTML attributes and widget options:
   * `options` - array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
     See https://github.com/blazeworx/flagstrap

### Text input with autocomplete

Example of text input with autocomplete

```php
echo $this->Form->text($fieldName, $options + ['data-toggle' => 'autocomplete']);
echo $this->Form->autocomplete($fieldName, $options);
```

Where:
- `$fieldName` Name of a field, like this "Modelname.fieldname"
- `$options` Array of HTML attributes and widget options:
   * `type` - Type for autocomplete suggestions, e.g. Model.Field;
   * `plugin` - Plugin name for autocomplete field;
   * `url` - URL for autocomplete (default: /cake_theme/filter/autocomplete.json);
   * `local` - Local data for autocomplete;
   * `min-length` - minimal length of query string.
     See https://github.com/bassjobsen/Bootstrap-3-Typeahead

### Textarea input with autocomplete

Example of textarea input with autocomplete

```php
echo $this->Form->text($fieldName, $options + ['data-toggle' => 'textcomplete']);
echo $this->Form->textarea($fieldName, $options + ['data-toggle' => 'textcomplete']);
```

Where:
- `$fieldName` Name of a field, like this "Modelname.fieldname"
- `$options` Array of HTML attributes and widget options:
   * `data-textcomplete-strategies`- Array of strategies, e.g.:
      + `match`, `replace`.
      + `ajaxOptions`: A set of key/value pairs that configure the Ajax request.
        See https://github.com/yuku/textcomplete

Example options:

```php
$strategies = [
    [
        'ajaxOptions' => [
            'url' => $this->Html->url(['controller' => 'variables', 'action' => 'autocomplete', 'ext' => 'json']),
            'data' => [
                'ref-type' => 'some-type',
                'ref-id' => 'some-id',
            ]
        ],
        'match' => '(%)(\w+)$',
        'replace' => 'return "$1" + value + "%";'
    ]
];

$ptions = [
    'label' => __('Label') . ':',
    'title' => __('Tooltip.'),
    'type' => 'textarea',
    'rows' => 4,
    'data-toggle' => 'textcomplete', 
    'data-textcomplete-strategies' => json_encode($strategies)
];
```
