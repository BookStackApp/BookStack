# Lexical based editor todo

## In progress

//

## Main Todo

- Media resize support (like images)
- Mac: Shortcut support via command.

## Secondary Todo

- Color picker support in table form color fields
- Table caption text support
- Support media src conversions (https://github.com/tinymce/tinymce/blob/release/6.6/modules/tinymce/src/plugins/media/main/ts/core/UrlPatterns.ts)

## Bugs

- Can't select iframe embeds by themselves. (click enters iframe)
- Removing link around image via button deletes image, not just link 
- `SELECTION_CHANGE_COMMAND` not fired when clicking out of a table cell. Prevents toolbar hiding on table unselect.
- Template drag/drop not handled when outside core editor area (ignored in margin area).
- Table row copy/paste does not handle merged cells
  - TinyMCE fills gaps with the  cells that would be visually in the row