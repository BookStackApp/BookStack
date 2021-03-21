# Visual Theme System

BookStack allows visual customization via the theme system which enables you to extensively customize views, translation text & icons.

This theme system itself is maintained and supported but usages of this system, including the files you are able to override, are not considered stable and may change upon any update. You should test any customizations made after updates.

## Getting Started

This makes use of the theme system. Create a folder for your theme within your BookStack `themes` directory. As an example we'll use `my_theme`, so we'd create a `themes/my_theme` folder.
You'll need to tell BookStack to use your theme via the `APP_THEME` option in your `.env` file. For example: `APP_THEME=my_theme`.

## Customizing View Files

Content placed in your `themes/<theme_name>/` folder will override the original view files found in the `resources/views` folder. These files are typically [Laravel Blade](https://laravel.com/docs/6.x/blade) files.

## Customizing Icons

SVG files placed in a `themes/<theme_name>/icons` folder will override any icons of the same name within `resources/icons`. You'd typically want to follow the format convention of the existing icons, where no XML deceleration is included and no width & height attributes are set, to ensure optimal compatibility. 

## Customizing Text Content

Folders with PHP translation files placed in a `themes/<theme_name>/lang` folder will override translations defined within the `resources/lang` folder. Custom translations are merged with the original files so you only need to override the select translations you want to override, you don't need to copy the whole original file. Note that you'll need to include the language folder.

As an example, Say I wanted to change 'Search' to 'Find'; Within a `themes/<theme_name>/lang/en/common.php` file I'd set the following:

```php
<?php
return [
    'search' => 'find',
];
```