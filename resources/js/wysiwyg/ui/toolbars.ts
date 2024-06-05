import {EditorButton, FormatPreviewButton} from "./framework/buttons";
import {
    blockquote, bold, code,
    dangerCallout,
    h2, h3, h4, h5, image,
    infoCallout, italic, link, paragraph,
    redo, strikethrough, subscript,
    successCallout, superscript, underline,
    undo,
    warningCallout
} from "./defaults/button-definitions";
import {EditorContainerUiElement, EditorFormatMenu, EditorSimpleClassContainer} from "./framework/containers";
import {el} from "../helpers";


export function getMainEditorFullToolbar(): EditorContainerUiElement {
    return new EditorSimpleClassContainer('editor-toolbar-main', [
        new EditorButton(undo),
        new EditorButton(redo),

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

        new EditorButton(bold),
        new EditorButton(italic),
        new EditorButton(underline),
        new EditorButton(strikethrough),
        new EditorButton(superscript),
        new EditorButton(subscript),
        new EditorButton(code),

        new EditorButton(link),
        new EditorButton(image),
    ]);
}