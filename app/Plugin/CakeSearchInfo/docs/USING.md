# Using this plugin

## Install this plugin

1. Copy configuration file from `app/Plugin/CakeSearchInfo/Config/cakesearchinfo.php` to `app/Config`.
2. Edit config file and configure plugin [See `Example of configuration file`](#example-of-configuration-file)
3. Include component `CakeSearchInfo.SearchFilter` in your `AppController`:

   ```php
   public $components = [
       'CakeSearchInfo.SearchFilter'
   ];
   ```

4. Copy i18n files from `app/Plugin/CakeSearchInfo/Locale/rus/LC_MESSAGES/cake_search_info.*` to
`app/Locale/rus/LC_MESSAGES`
5. To start the search, go to the link `/cake_search_info/search` or simply `/search`
6. If you want to use in your layer:
   - Include helper `CakeSearchInfo.Search` in your `AppController`:

      ```php
      public $helpers = [
          'CakeSearchInfo.Search'
      ];
      ```

   - Add in your `layout`:

      ```php
      echo $this->Html->script('CakeSearchInfo.SearchInfo.min.js');
      echo $this->Html->css('CakeSearchInfo.SearchInfo.min.css');

      echo $this->Search->createFormSearch($search_targetFields, $search_targetFieldsSelected, $search_urlActionSearch, $search_targetDeep, $search_querySearchMinLength);
      ```

   - Add in your `Controller` method:

      ```php
      /**
       * Action `index`. Used to begin search.
       *
       * @return void
       */
      public function index() {
          $search_urlActionSearch = ['controller' => 'some_controoler', 'action' => 'search'];
          $this->set(compact('search_urlActionSearch'));
      }

      /**
       * Action `search`. Used to view a result of search.
       *
       * @return void
       */
      public function search() {
          $whitelist = [];
          $this->SearchFilter->search($whitelist);
      }
      ```

## Example of configuration file

   ```php
   $config['CakeSearchInfo'] = [
       'QuerySearchMinLength' => 0,
       'AutocompleteLimit' => 10,
       'TargetDeep' => 0,
       'DefaultSearchAnyPart' => true,
       'TargetModels' => [
   /*
           'ModelName' => [
               'fields' => [
                   'ModelName.FieldName' => __('Field name'),
                   'ModelName.FieldName2' => __('Field name 2'),
               ],
               'order' => ['ModelName.FieldName' => 'direction'],
               'name' => __('Scope name'),
               'recursive' => 0, // not necessary - default: -1
               'contain' => null, // not necessary - default: null
               'conditions' => ['ModelName.FieldName' => 'SomeValue'], // not necessary - used as global conditions
               'url' => [
                   'controller' => 'modelnames',
                   'action' => 'view',
                   'plugin' => 'pluginname',
               ],  // not necessary - used in link to result
               'id' => 'ModelName.id', // not necessary - used in link to result
           ],
   */
       ],
       'IncludeFields' => [
   /*
           'ModelName' => [
               'ModelName.FieldName',
               'ModelName.FieldName2',
           ]
   */
       ],
   ];
   ```
