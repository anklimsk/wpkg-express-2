# Filter for table data

1. Include component `CakeTheme.Filter` in your `Controller` and use function `getFilterConditions()`
    for retrieve condition, e.g.:

   ```php
   $conditions = $this->Filter->getFilterConditions();
   $employees = $this->Paginator->paginate('Employee', $conditions);
   ```

2. Modify your `View` file:
   - Before tag `<table>` add:

      ```php
      <div class="table-responsive table-filter">
      <?php echo $this->Filter->openFilterForm(); ?>
      ```

   - Inside tag `<thead>` add:
      ```php
      echo $this->Filter->createFilterForm($formInputs, $plugin, $usePrint, $exportType);
      ```
      Where:
      - `$formInputs` - array of fields uses as table columns in format: 
         * key - `model.field`, value - form input options, or value - `model.field` for default form input;
      - If pagination field is not equal filter form input field, set option: `[..., 'pagination-field' => 'model.field']`;
      - For disable pagination and filter form input, use option: `[..., 'disabled' => true]`;
      - For escaping of title and attributes set escape to false to disable: `[..., 'escape' => false]`;
      - For using select input, use `[..., 'options' => [1 => 'Some val. 1', 2 => 'Some val. 2']]`;
      - `$plugin` - Name of plugin for target model of filter;
      - `$usePrint` -  If True, display Print button (default True);
      - `$exportType` - Extension of exported file, for display Export button.
3. After close tag `</table>` add:

   ```php
   <?php echo $this->Filter->closeFilterForm(); ?>
   ```

4. If used postLink inside filter table:
   - Add option `['block' => 'confirm-form']` in postLink:

      ```php
      $this->Form->postLink(.., .., [..., 'block' => 'confirm-form']);
      ```

   - Add after `echo $this->Filter->closeFilterForm();`

      ```php
      echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
      ```

## Using group action

1. Modify your `View` file:

   ```php
   <div class="table-responsive table-filter">
   <?php echo $this->Filter->openFilterForm(true); ?>
               ...
               </thead>
   <?php if (!empty($tableData)): ?>
               <tfoot>
   <?php
       echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
   ?>
               </tfoot>
   <?php endif; ?>
               <tbody>
   ```

2. In your `Controller` add:

   ```php
   public function index() {
       $groupActions = [
           'group-data-del' => __('Delete selected tasks')
       ];
       $conditions = $this->Filter->getFilterConditions('SomeModel');
       if ($this->request->is('post')) {
           $groupAction = $this->Filter->getGroupAction(array_keys($groupActions));
           $resultGroupProcess = $this->SomeModel->processGroupAction($groupAction, $conditions);
           if ($resultGroupProcess !== null) {
               if ($resultGroupProcess) {
                   $conditions = null;
                   $this->Flash->success(__('Selected tasks has been processed.'));
               } else {
                   $this->Flash->error(__('Selected tasks could not be processed. Please, try again.'));
               }
           }
       }
       $this->Paginator->settings = $this->paginate;
       $tableData = $this->Paginator->paginate('SomeModel', $conditions);

       $this->set(compact('tableData', 'groupActions'));
   }
   ```

3. In your `Model` add:

   ```php
   /**
    * Process group action
    *
    * @param $groupAction string Name of group action for processing
    * @param $conditions array Conditions of group action for processing
    * @return null|bool Return Null, on failure. If success, return True,
    *  False otherwise.
    */
   public function processGroupAction($groupAction = null, $conditions = null) {
       if (($groupAction === false) || empty($conditions)) {
           return null;
       }

       $result = false;
       switch ($groupAction) {
           case 'group-data-del':
               $result = $this->deleteAll($conditions, false);
           break;
       }

       return $result;
   }
   ```

## Set autocomplete limit

In your `AppController` add to method `beforeFilter()`:

```php
Configure::write('ViewExtension.AutocompleteLimit', $limit);
```

Where `$limit` - value of limit for autocomplete.

## Example of `View` file:

```php
    <div class="table-responsive table-filter">
<?php echo $this->Filter->openFilterForm(true); ?>
        <table class="table table-hover table-striped table-condensed">
            <caption>
<?php
    echo __('Some data');
?>
            </caption>
            <thead>
<?php 
    $formInputs = [
        'SomeModel.id' => [
            'label' => 'ID',
            'disabled' => true,
            'class-header' => 'action',
            'not-use-input' => true
        ],    
        'SomeModel.name' => [
            'label' => __('Name'),
        ],        
        'SomeModel.date' => [
            'label' => __('Date'),
        ],
    ];
    $exportType = 'xlsx';
    echo $this->Filter->createFilterForm($formInputs, null, true, $exportType);
?>
            </thead>
<?php if (!empty($tableData)): ?>
            <tfoot>
<?php
    echo $this->Filter->createGroupActionControls($formInputs, $groupActions, true);
?>
            </tfoot>
<?php endif; ?>
            <tbody>
<?php    
    foreach($tableData as $tableDataItem) {
        $tableRow = [];
        $attrRow = [];
        $actions = $this->ViewExtension->buttonLink(
            'fas fa-pencil-alt',
            'btn-warning',
            ['controller' => 'somecontrs', 'action' => 'edit', $tableDataItem['SomeModel']['id']],
            [
                'title' => __('Edit data'),
                'action-type' => 'modal',
                'class' => 'app-tour-btn-edit'
            ]
        ) .
        $this->ViewExtension->buttonLink(
            'fas fa-trash-alt',
            'btn-danger',
            ['controller' => 'somecontrs', 'action' => 'delete', $tableDataItem['SomeModel']['id']],
            [
                'title' => __('Delete department'), 'action-type' => 'confirm-post',
                'data-confirm-msg' => __('Are you sure you wish to delete this data \'%s\'?', h($tableDataItem['SomeModel']['name'])),
            ]
        );

        $tableRow[] = [$this->Filter->createFilterRowCheckbox('SomeModel.id', $tableDataItem['SomeModel']['id']),
            ['class' => 'action text-center']];
        $tableRow[] = $this->ViewExtension->popupModalLink(h($tableDataItem['SomeModel']['name']), 
                ['controller' => 'somecontrs', 'action' => 'view', $tableDataItem['SomeModel']['id']],
                ['data-modal-size' => 'lg']);
        $tableRow[] = [$this->ViewExtension->showEmpty($tableDataItem['SomeModel']['date'], '%x'),
            $this->Time->i18nFormat($tableDataItem['SomeModel']['date'], '%x')), ['class' => 'center']];        
        $tableRow[] = [$actions, ['class' => 'action text-center']];

        echo $this->Html->tableCells([$tableRow], $attrRow, $attrRow);
    }
?>
            </tbody>
        </table>
<?php
    echo $this->Filter->closeFilterForm();
    echo $this->Html->div('confirm-form-block', $this->fetch('confirm-form'));
?>
    </div>
<?php
    echo $this->ViewExtension->buttonsPaging();
```
