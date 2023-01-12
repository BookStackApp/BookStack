# BookStack Views

All views within this folder are [Laravel blade](https://laravel.com/docs/6.x/blade) views.

### Overriding

Views can be overridden on a per-file basis via the visual theme system.
More information on this can be found within the `dev/docs/visual-theme-system.md`
file within this project.

### Convention

Views are broken down into rough domain areas. These aren't too strict although many of the folders
here will often match up to a HTTP controller. 

Within each folder views will be structured like so:

```txt
- folder/
    - page-a.blade.php
    - page-b.blade.php
    - parts/
        - partial-a.blade.php
        - partial-b.blade.php
    - subdomain/
        - subdomain-page-a.blade.php
        - subdomain-page-b.blade.php
        - parts/
            - subdomain-partial-a.blade.php
            - subdomain-partial-b.blade.php
```

If a folder contains no pages at all (For example: `attachments`, `form`) and only partials, then 
the partials can be within the top-level folder instead of pages to prevent unneeded nesting.

If a partial depends on another partial within the same directory, the naming of the child partials should be an extension of the parent.
For example:

```txt
- tag-manager.blade.php
- tag-manager-list.blade.php
- tag-manager-input.blade.php
```