# Creating a page footer

1. Copy `View` element file from `app/Plugin/CakeTheme/View/Elements/footerPage.ctp.default` to
     `app/View/Elements/footerPage.ctp` (if needed).
2. Edit element file [See `Example of footer`](#example-of-page-footer)
3. Define next valiable in method `beforeFilter` your `AppController`, e.g.:

   ```php
   /**
    * Called before the controller action. You can use this method to configure and customize components
    * or perform logic that needs to happen before each controller action.
    *
    * Actions:
    *  - Set global variables for View.
    *
    * @return void
    * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
    */
   public function beforeFilter() {
       // If `true`, show footer
       $showFooter = true;

       // Version of project
       $projectVersion = '';

       // Project author
       $projectAuthor = '';

       $this->set(compact(
           'showFooter',
           'projectVersion',
           'projectAuthor'
       ));
   }
   ```

## Example of page footer

```php
<?php
if (!isset($projectVersion)) {
    $projectVersion = null;
    if (defined('PROJECT_VERSION')) {
        $projectVersion = PROJECT_VERSION;
    }
}

if (!isset($projectAuthor)) {
    $projectAuthor = null;
    if (defined('PROJECT_AUTHOR')) {
        $projectAuthor = PROJECT_AUTHOR;
    }
}

if (empty($projectVersion) && empty($projectAuthor)) {
    return;
}
?>
    <div class="footer navbar-default navbar-fixed-bottom">
        <div class="container-fluid">
<?php
    if (!empty($projectVersion)):
?>
            <div class="pull-left">
                <small>
                    <em>
<?php
    $projectVersionTag = $this->Html->tag('samp', $projectVersion);
    echo __d('view_extension', 'Version: %s', $projectVersionTag);
?>
                    </em>
                </small>
            </div>
<?php
    endif;
    if (!empty($projectAuthor)):
?>
            <div class="pull-right">
                <small>
<?php
    echo $projectAuthor;
?>
                </small>
            </div>
<?php
    endif;
?>
        </div>
    </div>
```
