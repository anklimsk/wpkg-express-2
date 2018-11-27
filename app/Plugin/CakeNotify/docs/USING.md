# Using this plugin

## Sending E-mail

1. If necessary, copy `footer` file from `app/Plugin/CakeNotify/View/Elements/mailFooter.ctp` to `app/View/Elements/mailFooter.ctp` 
   and edit it.
2. To send an E-mail, add to your `Model`:

   ```php
   $modelSendEmail = ClassRegistry::init('CakeNotify.SendEmail');
   $config = 'smtp';
   $domain = $modelSendEmail->getDomain();
   $from = ['report@' . $domain, 'Project name...'];
   $to = 'user@fabrikam.com';
   $subject = __('Test sending email');
   $template = 'test';
   $vars = compact('test');
   $helpers = ['Time'];

   // To send using task queues
   $result = $modelSendEmail->putQueueEmail(compact('config', 'from', 'to', 'subject', 'template', 'vars', 'helpers'));

   // To send now
   $result = $modelSendEmail->sendEmailNow(compact('config', 'from', 'to', 'subject', 'template', 'vars', 'helpers'));
   ```

   Where:
   - `$config` - Name of email configuration. Default - 'smtp'.
   - `$from` - E-mail from;
   - `$to` - email to;
   - `$subject` - Subject of email;
   - `$template` - Template of E-mail. e.g. 'template' of array `['template', 'layout']`;
   - `$vars` - Variables of View. Used in template.
   - `$helpers` - List of View helpers. Used in template.

## Notification to the user using `WEB Notifications API`

To notify the user, add to your `Model`:

```php
$modelNotification = ClassRegistry::init('CakeNotify.Notification');
$tag = 'new_job';
$title = __('New job');
$body = __('Received a new job');
$extendInfo = [
    'data' => [
        'url' => ['controller' => 'jobs', 'action' => 'latest', 'plugin' => null],
        'icon' => '/img/cake.icon.png'
     ],
     'user_role' => USER_ROLE_MANAGER | USER_ROLE_ADMIN,
];
$result = $modelNotification->createNotification($tag, $title, $body, $extendInfo);
```

Where:
- `$tag` - The ID of the notification. Is used to replace the messages with the same tag.
- `$title` - The title of the notification;
- `$body` - The body string of the notification;
- `$extendInfo` - Extended info for notification. List of key:
   * `data` - Data associated with the notification;
   * `user_role` - Bit mask of user roles to notify users with a certain role;
   * `user_id` - The ID of user to personal notify;
   * `expires` - Date and time of expire notification.
