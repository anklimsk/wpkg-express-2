# Creating a collapsible tree with support for moving and drag and drop

Require plugin `Tools`. Use the composer to install:
`composer require dereuromark/cakephp-tools:^0.12.3`

## Creating a collapsible tree

1. Include `Tools.Tree` helper in your `Controller`:

   ```php
   /**
    * An array containing the names of helpers this controller uses. The array elements should
    * not contain the "Helper" part of the class name.
    *
    * @var mixed
    * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
    */
   public $helpers = [
       'Tools.Tree',
   ];
   ```

2. In your `View` file add:

   ```php
   echo $this->AssetCompress->css('CakeTheme.tree', ['block' => 'css']);
   echo $this->AssetCompress->script('CakeTheme.tree', ['block' => 'script']);

   $expandClass = '';
   if ($expandAll) {
       $expandClass = 'bonsai-expand-all';
   }

   $treeOptions = [
       'class' => 'bonsai-treeview' . $expandClass,
       'model' => 'SomeModel',
       'id' => 'some-tree',
       'element' => 'SomeElement'
   ];
   $treeWrapOptions = [
       'data-url' => $dropUrl,
       'data-nested' => 'true',
       'data-change-parent' => 'false',
       'data-toggle' => 'draggable',

   ];
   $tree = $this->Tree->generate($treeData, $treeOptions);
   if ($draggable) {
       $tree = $this->Html->div(null, $tree, $treeWrapOptions);
   }
   echo $tree;
   ```

## Adding support for moving and drag and drop

1. In your `Controller` add: 
   - Include the `Move` component:

      ```php
      public $components = [
          'CakeTheme.Move' => ['model' => 'ModelName'],
          ...,
      ];
      ```

   - Include the `ViewExtension` helper:

      ```php
      public $helpers = [
          ...,
          'CakeTheme.ViewExtension'
      ];
      ```

   - Add action `move` and `drop`:

      ```php
      /**
       * Action `move`. Used to move employee to new position.
       * 
       * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
       * @param int|string $id ID of record for moving
       * @param int|string $delta Delta for moving
       * @throws InternalErrorException if tree of subordinate is disabled
       * @throws MethodNotAllowedException if request is not POST
       * @return void
       */
      public function move($direct = null, $id = null, $delta = 1) {
          $this->Move->moveItem($direct, $id, $delta);
      }

      /**
       * Action `drop`. Used to drag and drop employee to new position, 
       *  include new manager.
       *
       * POST Data:
       *  - `target` The ID of the item to moving to new position;
       *  - `parent` New parent ID of item;
       *  - `parentStart` Old parent ID of item;
       *  - `tree` Array of ID subtree for item. 
       * @throws BadRequestException if request is not AJAX, POST or JSON.
       * @throws InternalErrorException if tree of subordinate is disabled
       * @throws MethodNotAllowedException if request is not POST
       * @return void
       */
      public function drop() {
          $this->Move->dropItem();
      }
      ```

2. In your `Model` add:
   - Include behaviors `Tree` and `Move`:

      ```php
      public $actsAs = [
          'Tree',
          'CakeTheme.Move',
          ...,
      ];
      ```

   - If needed, add callback functions:

      ```php
      /**
       * Returns a list of all events that will fire in the model during it's lifecycle.
       * Add listener callbacks for events `Model.beforeUpdateTree` and `Model.afterUpdateTree`.
       *
       * @return array
       */
      public function implementedEvents() {
          $events = parent::implementedEvents();
          $events['Model.beforeUpdateTree'] = array('callable' => 'beforeUpdateTree', 'passParams' => true);
          $events['Model.afterUpdateTree'] = array('callable' => 'afterUpdateTree');
          return $events;
      }

      /**
       * Called before each update tree. Return a non-true result
       * to halt the update tree.
       *
       * @param array $options Options:
       *  - `id`: ID of moved record,
       *  - `newParentId`: ID of new parent for moved record,
       *  - `method`: method of move - moveUp or moveDown,
       *  - `delta`: delta for moving.
       * @return bool True if the operation should continue, false if it should abort
       */
      public function beforeUpdateTree($options = array()) {
          return true;
      }

      /**
       * Called after each successful update tree operation.
       *  
       * @return void 
       */ 
      public function afterUpdateTree() {

      }
   ```

3. In your `View` add:
   - Include `JS` and `CSS` files:

      ```php
      echo $this->AssetCompress->css('CakeTheme.tree');
      echo $this->AssetCompress->script('CakeTheme.tree');
      ```

   - Create move buttons:

      ```php
      echo $this->ViewExtension->buttonsMove($url, $useDrag, $glue, $useGroup);
      ```

      Where:
      * `$url` - URL to move action in controller, e.g.: 
        ['controller' => 'employees', 'action' => 'move', $employee['Employee']['id']]
      * `$useDrag` - flag of using drag and drop button
      * `$glue` - glue for buttons, e.g.: `&nbsp;`
      * `$useGroup` - If `True` and empty $glue, use group buttons.
      * Wrap table in `DIV` element:

         ```php
         <?php
         $dataUrl = $this->Html->url(
             [
                 'controller' => 'posts',
                 'action' => 'drop',
                 'ext' => 'json',
             ]
         );
         ?>
         <div data-toggle="draggable" data-url="<?php echo $dataUrl; ?>">
             <table>
             ...
                 <tbody>
                     <tr data-id="<?php echo $ID; ?>">
                     ...
                     </tr>
                 </tbody>
             </table>
         </div>
         ```

         Where `$ID` - ID of table row for moving.
