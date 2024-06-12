import {EditorButton} from "./framework/buttons";
import {
    blockquote, bold, clearFormating, code,
    dangerCallout, details,
    h2, h3, h4, h5, highlightColor, image,
    infoCallout, italic, link, paragraph,
    redo, source, strikethrough, subscript,
    successCallout, superscript, textColor, underline,
    undo,
    warningCallout
} from "./defaults/button-definitions";
import {EditorContainerUiElement, EditorSimpleClassContainer} from "./framework/core";
import {el} from "../helpers";
import {EditorFormatMenu} from "./framework/blocks/format-menu";
import {FormatPreviewButton} from "./framework/blocks/format-preview-button";
import {EditorDropdownButton} from "./framework/blocks/dropdown-button";
import {EditorColorPicker} from "./framework/blocks/color-picker";


export function getMainEditorFullToolbar(): EditorContainerUiElement {
    return new EditorSimpleClassContainer('editor-toolbar-main', [
        // History state
        new EditorButton(undo),
        new EditorButton(redo),

        // Block formats
        new EditorFormatMenu([
            new FormatPreviewButton(el('h2'), h2),
            new FormatPreviewButton(el('h3'), h3),
            new FormatPreviewButton(el('h4'), h4),
            new FormatPreviewButton(el('h5'), h5),
            new FormatPreviewButton(el('blockquote'), blockquote),
            new FormatPreviewButton(el('p'), paragraph),
            new FormatPreviewButton(el('p', {class: 'callout info'}), infoCallout),
            new FormatPreviewButton(el('p', {class: 'callout success'}), successCallout),
            new FormatPreviewButton(el('p', {class: 'callout warning'}), warningCallout),
            new FormatPreviewButton(el('p', {class: 'callout danger'}), dangerCallout),
        ]),

        // Inline formats
        new EditorButton(bold),
        new EditorButton(italic),
        new EditorButton(underline),
        new EditorDropdownButton(textColor, [
            new EditorColorPicker('color'),
        ]),
        new EditorDropdownButton(highlightColor, [
            new EditorColorPicker('background-color'),
        ]),
        new EditorButton(strikethrough),
        new EditorButton(superscript),
        new EditorButton(subscript),
        new EditorButton(code),
        new EditorButton(clearFormating),

        // Insert types
        new EditorButton(link),
        new EditorButton(image),
        new EditorButton(details),

        // Meta elements
        new EditorButton(source),
    ]);
}