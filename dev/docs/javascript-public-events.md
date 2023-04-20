# JavaScript Public Events

There are a range of available events emitted as part of a public & [supported](#support) API for accessing or extending JavaScript libraries and components used in the system.
These are emitted via standard DOM events so can be consumed using standard DOM APIs like so:

```javascript
window.addEventListener('event-name', event => {
   const eventData = event.detail; 
});
```

Such events are typically emitted from a DOM element relevant to event, which then bubbles up.
For most use-cases you can probably just listen on the `window` as shown above.

## Support

This event system, and the events emitted, are considered semi-supported.
Breaking changes of the event API, event names, or event properties, are possible but will be documented in update notes.
The detail provided within the events, and the libraries made accessible, are not considered supported nor stable, and changes to these won't be clearly documented changelogs.

## Event Naming Scheme

Events are typically named in the following format:

```text
<context>::<action/lifecycle>

# Examples:
editor-tinymce::setup
library-cm6::configure-theme
```

If the event is generic in use but specific to a library, the `<context>` will start with `library-` followed by the library name. Otherwise `<context>` may reflect the UI context/component.

The `<action/lifecycle>` reflects the lifecycle stage of the context, or a specific action to perform if the event is specific to a certain use-case.

## Event Listing

### `editor-markdown-cm6::pre-init`

This event is called before the markdown input editor CodeMirror instance is created or loaded.

#### Event Data

- `editorViewConfig` - An [EditorViewConfig](https://codemirror.net/docs/ref/#view.EditorViewConfig) object that will eventially be passed when creating the CodeMirror EditorView instance.

##### Example

```javascript
// Always load the editor with specific pre-defined content if empty
window.addEventListener('editor-markdown-cm6::pre-init', event => {
    const config = event.detail.editorViewConfig;
    config.doc = config.doc || "Start this page with a nice story";
});
```

### `editor-markdown::setup`

This event is called when the markdown editor loads, post configuration but before the editor is ready to use.

#### Event Data

- `markdownIt` - A references to the [MarkdownIt](https://markdown-it.github.io/markdown-it/#MarkdownIt) instance used to render markdown to HTML (Just for the preview).
- `displayEl` - The IFrame Element that wraps the HTML preview display.
- `cmEditorView` - The CodeMirror [EditorView](https://codemirror.net/docs/ref/#view.EditorView) instance used for the markdown input editor.

##### Example

```javascript
// Set all text in the display to be red by default.
window.addEventListener('editor-markdown::setup', event => {
    const display = event.detail.displayEl;
    display.contentDocument.body.style.color = 'red';
});
```

### `editor-drawio::configure`

This event is called as the embedded diagrams.net drawing editor loads, to allow configuration of the diagrams.net interface.
See [this diagrams.net page](https://www.diagrams.net/doc/faq/configure-diagram-editor) for details on the available options for the configure event.

If using a custom diagrams.net instance, via the `DRAWIO` option, you will need to ensure  your DRAWIO option URL has the `configure=1` query parameter.

#### Event Data

- `config` - The configuration object that will be passed to diagrams.net.
  - This will likely be empty by default, but modify this object in-place as needed with your desired options.

##### Example

```javascript
// Set only the "general" and "android" libraries to show by default
window.addEventListener('editor-drawio::configure', event => {
    const config = event.detail.config;
    config.enabledLibraries = ["general", "android"];
});
```

### `editor-tinymce::pre-init`

This event is called before the TinyMCE editor, used as the BookStack WYSIWYG page editor, is initialised.

#### Event Data

- `config` - Object containing the configuration that's going to be passed to [tinymce.init](https://www.tiny.cloud/docs/api/tinymce/root_tinymce/#init).

##### Example

```javascript
// Removed "bold" from the editor toolbar
window.addEventListener('editor-tinymce::pre-init', event => {
    const tinyConfig = event.detail.config;
    tinyConfig.toolbar = tinyConfig.toolbar.replace('bold ', '');
});
```

### `editor-tinymce::setup`

This event is called during the `setup` lifecycle stage of the TinyMCE editor used as the BookStack WYSIWYG editor. This is after configuration, but before the editor is fully loaded and ready to use. 

##### Event Data

- `editor` - The [tinymce.Editor](https://www.tiny.cloud/docs/api/tinymce/tinymce.editor/) instance used for the WYSIWYG editor.

##### Example

```javascript
// Replaces the editor content with redacted message 3 seconds after load.
window.addEventListener('editor-tinymce::setup', event => {
    const editor = event.detail.editor;
    setTimeout(() => {
        editor.setContent('REDACTED!');
    }, 3000);
});
```

### `library-cm6::configure-theme`

This event is called whenever a CodeMirror instance is loaded, as a method to configure the theme used by CodeMirror. This applies to all CodeMirror instances including in-page code blocks, editors using in BookStack settings, and the Page markdown editor.

#### Event Data

- `darkModeActive` - A boolean to indicate if the current view/page is being loaded with dark mode active.
- `registerViewTheme(builder)` - A method that can be called to register a new view (CodeMirror UI) theme.
  - `builder` - A function that will return  an object that will be passed into the CodeMirror [EditorView.theme()](https://codemirror.net/docs/ref/#view.EditorView^theme) function as a StyleSpec.
- `registerHighlightStyle(builder)` - A method that can be called to register a new HighlightStyle (code highlighting) theme.
  - `builder` - A function, that receives a reference to [Tag.tags](https://lezer.codemirror.net/docs/ref/#highlight.tags) and returns an array of [TagStyle](https://codemirror.net/docs/ref/#language.TagStyle) objects.

##### Example

The below shows registering a custom "Solarized dark" editor and syntax theme:

<details>
<summary>Show Example</summary>

```javascript
// Theme data taken from:
// https://github.com/craftzdog/cm6-themes/blob/main/packages/solarized-dark/src/index.ts
// (MIT License) - Copyright (C) 2022 by Takuya Matsuyama and others
const base00 = '#002b36',
    base01 = '#073642',
    base02 = '#586e75',
    base03 = '#657b83',
    base04 = '#839496',
    base05 = '#93a1a1',
    base06 = '#eee8d5',
    base07 = '#fdf6e3',
    base_red = '#dc322f',
    base_orange = '#cb4b16',
    base_yellow = '#b58900',
    base_green = '#859900',
    base_cyan = '#2aa198',
    base_blue = '#268bd2',
    base_violet = '#6c71c4',
    base_magenta = '#d33682'

const invalid = '#d30102',
    stone = base04,
    darkBackground = '#00252f',
    highlightBackground = '#173541',
    background = base00,
    tooltipBackground = base01,
    selection = '#173541',
    cursor = base04

function viewThemeBuilder() {
    return {
      '&':{color:base05,backgroundColor:background},
      '.cm-content':{caretColor:cursor},
      '.cm-cursor, .cm-dropCursor':{borderLeftColor:cursor},
      '&.cm-focused .cm-selectionBackground, .cm-selectionBackground, .cm-content ::selection':{backgroundColor:selection},
      '.cm-panels':{backgroundColor:darkBackground,color:base03},
      '.cm-panels.cm-panels-top':{borderBottom:'2px solid black'},
      '.cm-panels.cm-panels-bottom':{borderTop:'2px solid black'},
      '.cm-searchMatch':{backgroundColor:'#72a1ff59',outline:'1px solid #457dff'},
      '.cm-searchMatch.cm-searchMatch-selected':{backgroundColor:'#6199ff2f'},
      '.cm-activeLine':{backgroundColor:highlightBackground},
      '.cm-selectionMatch':{backgroundColor:'#aafe661a'},
      '&.cm-focused .cm-matchingBracket, &.cm-focused .cm-nonmatchingBracket':{outline:`1px solid ${base06}`},
      '.cm-gutters':{backgroundColor:darkBackground,color:stone,border:'none'},
      '.cm-activeLineGutter':{backgroundColor:highlightBackground},
      '.cm-foldPlaceholder':{backgroundColor:'transparent',border:'none',color:'#ddd'},
      '.cm-tooltip':{border:'none',backgroundColor:tooltipBackground},
      '.cm-tooltip .cm-tooltip-arrow:before':{borderTopColor:'transparent',borderBottomColor:'transparent'},
      '.cm-tooltip .cm-tooltip-arrow:after':{borderTopColor:tooltipBackground,borderBottomColor:tooltipBackground},
      '.cm-tooltip-autocomplete':{
        '& > ul > li[aria-selected]':{backgroundColor:highlightBackground,color:base03}
      }
    };
}

function highlightStyleBuilder(t) {
    return [{tag:t.keyword,color:base_green},
      {tag:[t.name,t.deleted,t.character,t.propertyName,t.macroName],color:base_cyan},
      {tag:[t.variableName],color:base05},
      {tag:[t.function(t.variableName)],color:base_blue},
      {tag:[t.labelName],color:base_magenta},
      {tag:[t.color,t.constant(t.name),t.standard(t.name)],color:base_yellow},
      {tag:[t.definition(t.name),t.separator],color:base_cyan},
      {tag:[t.brace],color:base_magenta},
      {tag:[t.annotation],color:invalid},
      {tag:[t.number,t.changed,t.annotation,t.modifier,t.self,t.namespace],color:base_magenta},
      {tag:[t.typeName,t.className],color:base_orange},
      {tag:[t.operator,t.operatorKeyword],color:base_violet},
      {tag:[t.tagName],color:base_blue},
      {tag:[t.squareBracket],color:base_red},
      {tag:[t.angleBracket],color:base02},
      {tag:[t.attributeName],color:base05},
      {tag:[t.regexp],color:invalid},
      {tag:[t.quote],color:base_green},
      {tag:[t.string],color:base_yellow},
      {tag:t.link,color:base_cyan,textDecoration:'underline',textUnderlinePosition:'under'},
      {tag:[t.url,t.escape,t.special(t.string)],color:base_yellow},
      {tag:[t.meta],color:base_red},
      {tag:[t.comment],color:base02,fontStyle:'italic'},
      {tag:t.strong,fontWeight:'bold',color:base06},
      {tag:t.emphasis,fontStyle:'italic',color:base_green},
      {tag:t.strikethrough,textDecoration:'line-through'},
      {tag:t.heading,fontWeight:'bold',color:base_yellow},
      {tag:t.heading1,fontWeight:'bold',color:base07},
      {tag:[t.heading2,t.heading3,t.heading4],fontWeight:'bold',color:base06},
      {tag:[t.heading5,t.heading6],color:base06},
      {tag:[t.atom,t.bool,t.special(t.variableName)],color:base_magenta},
      {tag:[t.processingInstruction,t.inserted,t.contentSeparator],color:base_red},
      {tag:[t.contentSeparator],color:base_yellow},
      {tag:t.invalid,color:base02,borderBottom:`1px dotted ${base_red}`}];
}

window.addEventListener('library-cm6::configure-theme', event => {
    const detail = event.detail;
    detail.registerViewTheme(viewThemeBuilder);
    detail.registerHighlightStyle(highlightStyleBuilder);
});
```
</details>