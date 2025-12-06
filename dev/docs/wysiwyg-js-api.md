# WYSIWYG JavaScript API

**Warning: This API is currently in development and may change without notice.**

Feedback is very much welcomed via this issue: https://github.com/BookStackApp/BookStack/issues/5937

This document covers the JavaScript API for the (newer Lexical-based) WYSIWYG editor.
This API is built and designed to abstract the internals of the editor away
to provide a stable interface for performing common customizations.

Only the methods and properties documented here are guaranteed to be stable **once this API
is out of initial development**.
Other elements may be accessible but are not designed to be used directly, and therefore may change
without notice.
Stable parts of the API may still change where needed, but such changes would be noted as part of BookStack update advisories.

The methods shown here are documented using standard TypeScript notation.

## Overview

The API is provided as an object, which itself provides a number of modules
via its properties:

- `ui` - Provides methods related to the UI of the editor, like buttons and toolbars.
- `content` - Provides methods related to the live user content being edited upon.

Each of these modules, and the relevant types used within, are documented in detail below.

---

## UI Module

This module provides methods related to the UI of the editor, like buttons and toolbars.

### Methods

#### createButton(options: object): EditorApiButton

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

### getMainToolbar(): EditorApiToolbar

Get the main editor toolbar. This is typically the toolbar at the top of the editor.
The function returns an [EditorApiToolbar](#editorapitoolbar) object, or null if no toolbar is found.

**Example**

```javascript
const toolbar = api.ui.getMainToolbar();
const sections = toolbar?.getSections() || [];
if (sections.length > 0) {
    sections[0].addButton(button);
}
```

### Types

These are types which may be provided from UI module methods.

#### EditorApiButton

Represents a button created via the `createButton` method.
This has the following methods:

- `setActive(isActive: boolean): void` - Sets whether the button should be in an active state or not (typically active buttons appear as pressed).

#### EditorApiToolbar

Represents a toolbar within the editor. This is a bar typically containing sets of buttons.
This has the following methods:

- `getSections(): EditorApiToolbarSection[]` - Provides the main [EditorApiToolbarSections](#editorapitoolbarsection) contained within this toolbar.

#### EditorApiToolbarSection

Represents a section of the main editor toolbar, which contains a set of buttons.
This has the following methods:

- `getLabel(): string` - Provides the string label of the section.
- `addButton(button: EditorApiButton, targetIndex: number = -1): void` - Adds a button to the section.
  - By default, this will append the button, although a target index can be provided to insert at a specific position.

---

## Content Module

This module provides methods related to the live user content being edited within the editor.

### Methods

#### insertHtml(html: string, position: string = 'selection'): void

Inserts the given HTML string at the given position string.
The position, if not provided, will default to `'selection'`, replacing any existing selected content (or inserting at the selection if there's no active selection range).
Valid position string values are: `selection`, `start` and `end`. `start` & `end` are relative to the whole editor document.
The HTML is not assured to be added to the editor exactly as provided, since it will be parsed and serialised to fit the editor's internal known model format. Different parts of the HTML content may be handled differently depending on if it's block or inline content.

The function does not return anything.

**Example**

```javascript
// Basic insert at selection
api.content.insertHtml('<p>Hello <strong>world</strong>!</p>');

// Insert at the start of the editor content
api.content.insertHtml('<p>I\'m at the start!</p>', 'start');
```