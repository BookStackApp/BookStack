# Lexical based editor todo

## In progress

- Add Type: Video/media/embed
    - TinyMce media embed supported:
      - iframe
      - embed
      - object
      - video - Can take sources
      - audio  - Can take sources
    - Pretty much all attributes look like they were supported.
    - Core old logic seen here: https://github.com/tinymce/tinymce/blob/main/modules/tinymce/src/plugins/media/main/ts/core/DataToHtml.ts
    - Copy/store attributes on node based on allow list?
      - width, height, src, controls, etc... Take valid values from MDN

## Main Todo

- Alignments: Use existing classes for blocks
- Alignments: Handle inline block content (image, video)
- Table features
- Image paste upload
- Keyboard shortcuts support
- Draft/change management (connect with page editor component)
- Add ID support to all block types
- Template drag & drop / insert
- Video attachment drop / insert
- Task list render/import from existing format
- Link popup menu for cross-content reference
- Link heading-based ID reference menu
- Image gallery integration for insert
- Image gallery integration for form
- Drawing gallery integration

## Bugs

- Image resizing currently bugged, maybe change to ghost resizer in decorator instead of updating core node.
- Removing link around image via button deletes image, not just link 
- `SELECTION_CHANGE_COMMAND` not fired when clicking out of a table cell. Prevents toolbar hiding on table unselect.