# Theme System Modules

A theme system module is a collection of customizations using the [visual](visual-theme-system.md) and [logical](logical-theme-system.md) theme systems, provided along with some metadata, that can be installed alongside other modules within a theme. They can effectively be thought of as "plugins" or "extensions" that can be applied in addition to any customizations in the active theme.

### Module Location

Modules are contained within a folder themselves, which should be located inside a `modules` folder within a [BookStack theme folder](visual-theme-system.md#getting-started). 
As an example, starting from the `themes/` top-level folder of a BookStack instance:

```txt
themes
└── my-theme
    └── modules
        ├── module-a
        │   └── bookstack-module.json
        └── module-b
            └── bookstack-module.json
```

### Module Format

A module exists as a folder in the location [as detailed above](#module-location).
The content within the module folder should then follow this format:

- `bookstack-module.json` - REQUIRED - A JSON file containing [the metadata](#module-json-metadata) for the module.
- `functions.php` - OPTIONAL - A PHP file containing code for the [logical theme system](logical-theme-system.md).
- `head/` - OPTIONAL - A folder containing HTML files which will be included into the HTML head of app-views.
- `icons/` - OPTIONAL - A folder containing any icons to use as per [the visual theme system](visual-theme-system.md#customizing-icons).
- `lang/` - OPTIONAL - A folder containing any language files to use as per [the visual theme system](visual-theme-system.md#customizing-text-content).
- `public/` - OPTIONAL - A folder containing any files to expose into public web-space as per [the visual theme system](visual-theme-system.md#publicly-accessible-files).
- `views/` - OPTIONAL - A folder containing any view additions or overrides as per [the visual theme system](visual-theme-system.md#customizing-view-files).

You can create additional directories/files for your own needs within the module, but ideally name them something unique to prevent conflicts with the above structure.

### Module JSON Metadata

Modules are required to have a `bookstack-module.json` file in the top level directory of the module.
This must be a JSON file with the following properties:

- `name` - string - An (ideally unique) name for the module.
- `description` - string - A short description of the module.
- `version` - string - A string version number generally following [semantic versioning](https://semver.org/).
  - Examples: `v0.4.0`, `4.3.12`,  `v0.1.0-beta4`.

### Customization Order/Precedence

It's possible that multiple modules may override/customize the same content.
Right now, there's no assurance in regard to the order in which modules may be loaded.
Generally they will be used/searched in order of their module folder name, but this is not assured and should not be relied upon.

It's also possible that modules customize the same content as the configured theme.
In this scenario, the theme takes precedence. Modules are designed to be more portable and instance abstract, whereas the theme folder would typically be specific to the instance. 
This allows the theme to be used to customize or override module content for the BookStack instance, without altering the module code itself.

### Module Best Practices

Here are some general best practices when it comes to creating modules:

- Use a unique name and clear description so the user can understand the purpose of the module.
- Increment the metadata version on change, keeping to [semver](https://semver.org/) to indicate compatibility of new versions.
- Where possible, prefer to [insert views before/after](logical-theme-system.md#custom-view-registration-example) instead of overriding existing views, to reduce likelihood of conflicts or update troubles.
- When using/registering custom views, use some level of unique namespacing within the view path to prevent potential conflicts with other customizations.
  - For example, I may store a view within my module as `views/my-module-name-welcome.blade.php`, to be registered as 'my-module-name-welcome'.
  - This is important since views may be resolved from other modules or the active theme, which may/will override your module level view.

### Distribution Format

Modules are expected to be distributed as a compressed ZIP file, where the ZIP contents follow that of a module folder.
Contents may optionally be placed within a nested folder inside the ZIP.
BookStack provides a `php artisan bookstack:install-module` command which allows modules to be installed from these ZIP files, either from a local path or from a web URL.
Currently, there's a hardcoded total filesize limit of 50MB for module contents installed via this method.

There is not yet any direct update mechanism for modules, although this is something we may introduce in the future.