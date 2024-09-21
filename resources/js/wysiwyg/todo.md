# Lexical based editor todo

## In progress

- RTL/LTR support
  - Basic implementation added 
  - Test across main range of content blocks
  - Test that HTML is being set as expected
  - Test editor defaults when between RTL/LTR modes

## Main Todo

- Mac: Shortcut support via command.
- Translations

## Secondary Todo

- Color picker support in table form color fields
- Color picker for color controls
- Table caption text support
- Support media src conversions (https://github.com/tinymce/tinymce/blob/release/6.6/modules/tinymce/src/plugins/media/main/ts/core/UrlPatterns.ts)

## Bugs

- Focus/click area reduced to content area, single line on initial access
- List selection can get lost on nesting/unnesting