# Using `ViewExtension` helper

## Creating page header with small menu with typical actions

Example:

```php
$this->ViewExtension->menuHeaderPage($headerMenuActions);
```
Where `$headerMenuActions` - array of actions links, e.g.:

```php
$headerMenuActions = [
    $this->ViewExtension->menuActionLink(
        'far fa-trash-alt',
        __('Delete data'),
        ['controller' => 'some_controller', 'action' => 'delete', $data['SomeModel']['id']],
        [
            'title' => __('Delete profile'), 'action-type' => 'confirm-post',
            'data-confirm-msg' => __('Are you sure you wish to delete this data?'),
        ]
    ),
    'divider',
    $this->ViewExtension->menuActionLink(
        'fas fa-download',
        __('Download XML data'),
        ['controller' => 'some_controller', 'action' => 'download', $data['SomeModel']['id'], 'ext' => 'xml'],
        [
            'title' => __('Download XML file'),
            'skip-modal' => true
        ]
    ),
];
echo $this->ViewExtension->menuHeaderPage($headerMenuActions);
```

For using page header and small menu of actions:

```php
echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
```

Where
- `$pageTitle` - string title of page;
- `$headerMenuActions` - array of actions links.

## Creating tooltips

Add HTML options to element:
- `['data-toggle' => 'tooltip', 'title' => 'Some tooltip']`;
- `['data-toggle' => 'someToggle', 'title' => 'Some tooltip']`;
- `['data-tooltip-text' => Some tooltip]`
Example:

```php
echo $this->Html->link('some link', '/', ['data-toggle' => 'tooltip', 'title' => 'Some tooltip']);
```

## Creating time ago block

Example:

```php
$this->ViewExtension->timeAgo($time, $format);
```
Where:
- `$time` UNIX timestamp, strtotime() valid string or DateTime object
- `$format` strftime format string

## AJAX render the block at regular intervals

Add HTML options to element: `['data-toggle' => 'repeat']`
Example:

```php
echo $this->Html->div('some-class', 'Data to update', ['data-toggle' => 'repeat']);
```

## Create a button for printing page

Example:

```php
echo $this->ViewExtension->buttonPrint();
```

## Printing text `Yes` or `No` for target data

Example:

```php
echo $this->ViewExtension->yesNo($data);
$listYesNo = $this->ViewExtension->yesNoList();
```

## Printing text `<None>` if target data is empty

Example:

```php
echo $this->ViewExtension->showEmpty($data, $dataRet, $emptyRet, $isHtml);
```

Where:
- `$data` - Data for checking
- `$dataRet` - Data for return, if target data is not empty. Default - target data
- `$emptyRet` - Data for return, if target data is empty (default - `<None>`)
- `$isHtml` - Flag of trimmings HTML tags from result, if False

## Creating HTML element of icon

Example:

```php
echo $this->ViewExtension->iconTag($icon, $options);
```

Where:
- `$icon` - Class of icon
- `$options` - HTML options for icon element
See http://fontawesome.io

## Creating HTML element of button

Example:

```php
echo $this->ViewExtension->button($icon, $btn, $options);
```

Where:
- `$icon` - Class of icon
- `$btn` - Class of button
- `$options` - HTML options for button element
See http://fontawesome.io

## Adding user role prefix to URL

Example:

```php
echo $this->ViewExtension->addUserPrefixUrl($url);
```

Where:
- `$url` - URL for adding prefix

## Getting icon class for file from extension

Example:

```php
echo $this->ViewExtension->getIconForExtension($extension);
```

Where:
- `$extension` - Extension of file`

## Creating truncated text with buttons `expand` and `roll up`

Example:

```php
echo $this->ViewExtension->truncateText($text, $length);
```

Where:
- `$text` - Text to truncate
- `$length` - Length of returned string

## Printing a number as text

Require plugin `Tools`. Use the composer to install:
`composer require dereuromark/cakephp-tools:^0.12.3`
Example:

```php
echo $this->ViewExtension->numberText($number, $langCode);
```

Where:
- `$number` - Number for processing
- `$langCode` - Languge code in format `ISO 639-1` or `ISO 639-2`

## Creating progress bar with state

Example:

```php
echo $this->ViewExtension->barState($stateData);
```

Where:
- `$stateData` - Array of state in format:
   * key `stateName`, value: name of state;
   * key `stateId`, value: ID of state;
   * `amount`, value: amount elements in this state
   * key `stateUrl`, value: url for state, e.g.:
     array('controller' => 'posts', 'action' => 'index', '?' => array('data[FilterData][0][Post][state_id]' => '2')) [Not necessary]
   * key `class`: ID of state, value: class of state for progress bar,
     e.g.: 'progress-bar-danger progress-bar-striped' [Not necessary]

## Creating a list of the most recently modified data

Example:

```php
echo $this->ViewExtension->listLastInfo($lastInfo, $labelList, $controllerName, $actionName, $length);
```

Where:
- `$lastInfo` - Array of last information in format:
   * key `label`, value: label of list item;
   * key `modified`, value: date and time of last modification;
   * key `id`, value: ID of record.
- `$labelList` - Label of list
- `$controllerName` -  Name of controller for viewing
- `$actionName` - Name of controller action for viewing
- `$length` - Length of list item label string

## Creating collapsible list

Example:

```php
echo $this->ViewExtension->collapsibleList($listData, $showLimit, $listClass, $listTag);
```

Where:
- `$listData` - List data
- `$showLimit` - Limit of the displayed list
- `$listClass` -  Class of the list tag
- `$listTag` - Type of list tag to use (ol/ul)
