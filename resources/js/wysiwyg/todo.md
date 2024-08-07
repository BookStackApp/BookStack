# Lexical based editor todo

## In progress

- Table features
  - Row properties form logic
  - Table properties form logic
    - Caption text support 
  - Resize to contents button
  - Remove formatting button

## Main Todo

- Alignments: Use existing classes for blocks (including table cells)
- Alignments: Handle inline block content (image, video)
- Image paste upload
- Keyboard shortcuts support
- Add ID support to all block types
- Link popup menu for cross-content reference
- Link heading-based ID reference menu
- Image gallery integration for insert
- Image gallery integration for form
- Drawing gallery integration
- Support media src conversions (https://github.com/tinymce/tinymce/blob/release/6.6/modules/tinymce/src/plugins/media/main/ts/core/UrlPatterns.ts)
- Media resize support (like images)

## Secondary Todo

- Color picker support in table form color fields

## Bugs

- Image resizing currently bugged, maybe change to ghost resizer in decorator instead of updating core node.
- Removing link around image via button deletes image, not just link 
- `SELECTION_CHANGE_COMMAND` not fired when clicking out of a table cell. Prevents toolbar hiding on table unselect.
- Template drag/drop not handled when outside core editor area (ignored in margin area).