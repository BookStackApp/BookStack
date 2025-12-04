# WYSIWYG JavaScript API

TODO - Link to this from JS code doc.
TODO - Create JS events and add to the js public events doc.

TODO - Document the JS API.

TODO - Add testing coverage

**Warning: This API is currently in development and may change without notice.**

This document covers the JavaScript API for the (newer Lexical-based) WYSIWYG editor.
This API is custom-built, and designed to abstract the internals of the editor away
to provide a stable interface for performing common customizations.

Only the methods and properties documented here are guaranteed to be stable **once this API
is out of initial development**.
Other elements may be accessible but are not designed to be used directly, and therefore may change
without notice.
Stable parts of the API may still change where needed, but such changes would be noted as part of BookStack update advisories.

## Overview

The API is provided as an object, which itself provides a number of modules
via its properties:

- `ui` - Provides all actions related to the UI of the editor, like buttons and toolbars.

Each of these modules, and the relevant types used within, can be found detailed below.

---

## UI Module

This module provides all actions related to the UI of the editor, like buttons and toolbars.

### Methods

#### createButton(options)

Creates a new button which can be used by other methods.
This takes an option object with the following properties:

- `label` - string, optional - Used for the button text if no icon provided, or the button tooltip if an icon is provided.
- `icon` - string, optional - The icon to use for the button. Expected to be an SVG string.
- `action` - callback, required - The action to perform when the button is clicked.

The function returns an [EditorApiButton](#editorapibutton) object.

**Example**

```javascript
const button = api.ui.createButton({
    label: 'Warn',
    icon: '<svg>...</svg>',
    action: () => {
        window.alert('You clicked the button!');
    }
});
```

### getMainToolbarSections()

Get the sections of the main editor toolbar. These are those which contain groups of buttons
with overflow control.

The function returns an array of [EditorToolbarSection](#editortoolbarsection) objects.

**Example**

```javascript
const sections = api.ui.getMainToolbarSections();
if (sections.length > 0) {
    sections[0].addButton(button);
}
```

### Types

These are types which may be provided from UI module methods.
The methods on these types are documented using standard TypeScript notation.

#### EditorApiButton

Represents a button created via the `createButton` method.
This has the following methods:

- `setActive(isActive: boolean): void` - Sets whether the button should be in an active state or not (typically active buttons appear as pressed).

#### EditorToolbarSection

Represents a section of the main editor toolbar, which contains a set of buttons.
This has the following methods:

- `getLabel(): string` - Gets the label of the section.
- `addButton(button: EditorApiButton, targetIndex: number = -1): void` - Adds a button to the section.
  - By default, this will append the button, although a target index can be provided to insert the button at a specific position.