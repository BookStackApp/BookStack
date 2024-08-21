# Lexical based editor todo

## In progress

// 

## Main Todo

- Alignments: Handle inline block content (image, video)
- Support media src conversions (https://github.com/tinymce/tinymce/blob/release/6.6/modules/tinymce/src/plugins/media/main/ts/core/UrlPatterns.ts)
- Media resize support (like images)
- Table caption text support
- Table Cut/Copy/Paste column
- Mac: Shortcut support via command.

## Secondary Todo

- Color picker support in table form color fields

## Bugs

- Image resizing currently bugged, maybe change to ghost resizer in decorator instead of updating core node.
- Removing link around image via button deletes image, not just link 
- `SELECTION_CHANGE_COMMAND` not fired when clicking out of a table cell. Prevents toolbar hiding on table unselect.
- Template drag/drop not handled when outside core editor area (ignored in margin area).
- Table row copy/paste does not handle merged cells
  - TinyMCE fills gaps with the  cells that would be visually in the row