# Visual Theme System

BookStack allows visual customization via the theme system which enables you to extensively customize views, translation text & icons.

This is part of the theme system alongside the [logical theme system](./logical-theme-system.md).

**Note:** This theme system itself is maintained and supported but usages of this system, including the files you are able to override, are not considered stable and may change upon any update. You should test any customizations made after updates.

## Getting Started

*[Video Guide](https://foss.video/w/ibNY6bGmKFV1tva3Jz4KfA)*

This makes use of the theme system. Create a folder for your theme within your BookStack `themes` directory. As an example we'll use `my_theme`, so we'd create a `themes/my_theme` folder.
You'll need to tell BookStack to use your theme via the `APP_THEME` option in your `.env` file. For example: `APP_THEME=my_theme`.

## Customizing View Files

Content placed in your `themes/<theme_name>/` folder will override the original view files found in the `resources/views` folder. These files are typically [Laravel Blade](https://laravel.com/docs/10.x/blade) files.
As an example, I could override the `resources/views/books/parts/list-item.blade.php` file with my own template at the path `themes/<theme_name>/books/parts/list-item.blade.php`. 

## Customizing Icons

SVG files placed in a `themes/<theme_name>/icons` folder will override any icons of the same name within `resources/icons`. You'd typically want to follow the format convention of the existing icons, where no XML deceleration is included and no width & height attributes are set, to ensure optimal compatibility. 

## Customizing Text Content

Folders with PHP translation files placed in a `themes/<theme_name>/lang` folder will override translations defined within the `lang` folder. Custom translations are merged with the original files, so you only need to override the select translations you want to override, you don't need to copy the whole original file. Note that you'll need to include the language folder.

As an example, Say I wanted to change 'Search' to 'Find'; Within a `themes/<theme_name>/lang/en/common.php` file I'd set the following:

```php
<?php
return [
    'search' => 'find',
];
```

## Publicly Accessible Files

As part of deeper customizations you may want to expose additional files 
(images, scripts, styles, etc...) as part of your theme, in a way so they're
accessible in public web-space to browsers.

To achieve this, you can put files within a `themes/<theme_name>/public` folder.
BookStack will serve any files within this folder from a `/theme/<theme_name>` base path.

As an example, if I had an image located at `themes/custom/public/cat.jpg`, I could access
that image via the URL path `/theme/custom/cat.jpg`. That's assuming that `custom` is the currently
configured application theme.

There are some considerations to these publicly served files:

- Only a predetermined range "web safe" content-types are currently served.
  - This limits running into potential insecure scenarios in serving problematic file types.
- A static 1-day cache time it set on files served from this folder.
  - You can use alternative cache-breaking techniques (change of query string) upon changes if needed. 
  - If required, you could likely override caching at the webserver level.  
