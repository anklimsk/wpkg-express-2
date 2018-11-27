# Pagination controls elements:

1. To use all pagination controls add next line after table:

   ```php
   echo $this->ViewExtension->buttonsPaging($targetSelector, $showCounterInfo, $useShowList, $useGoToPage, $useChangeNumLines);
   ```

   To hide information about pages and records set `$showCounterInfo` to `false`
2. To use only load more button:

   ```php
   echo $this->buttonLoadMore($targetSelector);
   ```

3. To use only bar of page controls:

   ```php
   echo $this->barPaging($showCounterInfo, $useShowList, $useGoToPage, $useChangeNumLines);
   ```

## Change the limit of entries on the page

To change the limit of entries on the page, set `$useChangeNumLines` to `true`

## Go to the page;

To use go to the page, set `$useGoToPage` to `true`

## Load more button (display as list).

To use load more button, set `$useShowList` to `true`
Use `$targetSelector` to select data from server response and selection of the element on the
  page to add new data (default: `table tbody`).