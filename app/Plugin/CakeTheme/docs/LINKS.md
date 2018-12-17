# Creation the links

- Example of links

   ```php
   // With confirmation dialog
   echo $this->ViewExtension->confirmLink($title, $url, $options);

   // With confirmation dialog and `POST ` request
   echo $this->ViewExtension->confirmPostLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['action-type' => 'confirm', 'data-confirm-msg' => __('Are you sure you wish to delete this data?')]);
   echo $this->Html->link($title, $url, ['action-type' => 'confirm-post', 'data-confirm-btn-ok' => __('Yes'), 'data-confirm-btn-cancel' => __('No')]);

   // `AJAX`request without render result (only Flash message);
   echo $this->ViewExtension->requestOnlyLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'request-only']);

   // `AJAX`request 
   echo $this->ViewExtension->ajaxLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'ajax']);
   echo $this->Html->div(null, $this->Html->link($title, $url), ['data-toggle' => 'ajax']);

   // `PJAX`request 
   echo $this->ViewExtension->pjaxLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'pjax']);
   echo $this->Html->div(null, $this->Html->link($title, $url), ['data-toggle' => 'pjax']);

   // Lnk used `Lightbox` for `Bootstrap` (http://ashleydw.github.io/lightbox)
   echo $this->ViewExtension->lightboxLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'lightbox']);

   // Disabled links
   echo $this->Html->link($title, $url, ['class' => 'disabled']);
   echo $this->Html->div('disabled', $this->Html->link($title, $url, $options));

   // `Popover` and `Modal` link
   echo $this->ViewExtension->popupModalLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'popover', 'link-use-modal' => true]);

   // `Popover` link
   echo $this->ViewExtension->popupLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'popover']);

   // `Popover` link with popover window size
   echo $this->ViewExtension->popupLink($title, $url, $options + ['data-popover-size' => 'lg']);
   echo $this->Html->link($title, $url, ['data-toggle' => 'popover', 'data-popover-size' => 'lg']);

   // `Modal` link
   echo $this->ViewExtension->modalLink($title, $url, $options);
   echo $this->Html->link($title, $url, ['data-toggle' => 'modal']);

   // `Modal` link with modal window size
   echo $this->ViewExtension->modalLink($title, $url, $options + ['data-modal-size' => 'lg']);
   echo $this->Html->link($title, $url, ['data-toggle' => 'modal', 'data-modal-size' => 'lg']);
   ```

   Where:
   * `$title` - The content to be wrapped by `<a>` tags.
   * `$url` - Cake-relative URL or array of URL parameters, or external URL (starts with http://)
   * `$options` - HTML options for link element
     See https://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::link

- Generates a sorting link for pagination with `PJAX` request ()
   See https://book.cakephp.org/2.0/en/core-libraries/helpers/paginator.html#PaginatorHelper::sort

   ```php
   echo $this->ViewExtension->paginationSortPjax($key, $title, $options);
   ```

- Creating HTML element of button by link

   ```php
   echo $this->ViewExtension->buttonLink($icon, $btn, $url, $options);
   ```

   Where:
   * `$icon` - Class of icon
   * `$btn` - Class of button
   * `$url` - Cake-relative URL or array of URL parameters, or external URL (starts with http://)
   * `$options` - HTML options for button element
     List of values option `action-type`:
      + `confirm`: create link with confirmation action;
      + `confirm-post`: create link with confirmation action and `POST` request;
      + `post`: create link with `POST` request;
      + `modal`: create link with opening result in modal window.

## Creating `Modal` and `Popup` window

- Include component `CakeTheme.ViewExtension` in your `Controller`;
- Place the view file in the subdirectory `mod` or `pop`;
- In `Modal` and `Popup` view file use next `CSS` classes:
   * hide element: `.hide-popup`, `.hide-modal`, `.hide-popup-modal`;
   * show element: `.show-popup`, `.show-modal`, `.show-popup-modal`.
- Checking the request is `Modal` or `Popup`:

   ```php
   if ($this->request->id('modal')) {
       echo  'This is an Modal request';
   }
   ...
   if ($this->request->id('popup')) {
       echo  'This is an Popup request';
   }
   ```
