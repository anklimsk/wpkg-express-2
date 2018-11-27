# Using `ViewExtension` component

## Checking the response is `HTML`

Example:

```php
if ($this->ViewExtension->isHtml()) {
    echo 'This is an HTML request';
}
```

## Using back redirect to previous page

In your `Controller` action add:

```php
// Set redirect URL to cache
$this->ViewExtension->setRedirectUrl($redirect, $key);
```

Where:
- `$redirect` - Redirect URL. If empty, use redirect URL. If `True`, use current URL.
- `$key` - Key for cache

Later:

```php
// Redirect by URL
$this->ViewExtension->redirectByUrl($defaultRedirect, $key);
```

Where:
- `$defaultRedirect` - Default redirect URL. Use if redirect URL is not found in cache.
- `$key` - Key for cache

## Using queue of tasks

Require plugin `Queue`. Use the composer to install:
`composer require dereuromark/cakephp-queue:^2.3.0`

- In your `Task` load `Model` `ExtendQueuedTask`, e.g.:

   ```php
   public $uses = [
       'Queue.QueuedTask',
       'CakeTheme.ExtendQueuedTask',
   ];
   ```

- Use progress of task:

   ```php
   $step = 0;
   $maxStep = 5;
   $this->ExtendQueuedTask->updateTaskProgress($id, $step, $maxStep);
   ```

   Where:
   * `$id` - ID of job
   * `$step` - Current step of job
   * `$maxStep` - Maximum steps of job

- Add a message with the result of the task

   ```php
   $this->ExtendQueuedTask->updateMessage($id, $message);
   // or
   $this->ExtendQueuedTask->updateTaskErrorMessage($id, $message, $keepExistingMessage);
   ```

   Where:
   * `$id` - ID of job
   * `$message` - Message for update
   * `$keepExistingMessage` - If `True`, keep existing error message

- In your `Controller` action add:

   ```php
   $this->loadModel('CakeTheme.ExtendQueuedTask');
   $taskName = 'SomeTask';
   $taskParam = ['param' => 'value'];
   $notBefore = null;
   $group = 'some_group';
   $this->ExtendQueuedTask->createJob($taskName, $taskParam, $notBefore, $group);
   $this->ViewExtension->setProgressSseTask($taskName);
   ```

   Where:
   * `$taskName` - Name of task
   * `$taskParam` - Optional parameters for the task
   * `$notBefore` - Optional date which must not be preceded
   * `$group` - Used to group similar QueuedTasks

- In your `View` file add:

   ```php
   echo $this->ViewExtension->requestOnlyLink($title, $url, $options);
   // or
   echo $this->Html->link($title, $url, ['data-toggle' => 'request-only']);
   ```

   Where:
   * `$title` - The content to be wrapped by `<a>` tags.
   * `$url` - Cake-relative URL or array of URL parameters, or external URL (starts with http://)
   * `$options` - HTML options for link element
     See https://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::link

## Set Flash message of exception

Example:

```php
return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for record of post')));
// or
$this->ViewExtension->setExceptionMessage($message, $defaultRedirect, $key);
```

Where:
- `$message` - Message to be flashed. If an instance of Exception the exception 
   message will be used and code will be set in params.
- `$defaultRedirect` - Default redirect URL. Use if redirect URL is not found in cache.
- `$key` - Key for cache
