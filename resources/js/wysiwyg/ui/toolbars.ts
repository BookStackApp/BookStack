import {EditorButton} from "./framework/buttons";
import {
    blockquote, bold, code,
    dangerCallout,
    h2, h3, h4, h5,
    infoCallout, italic, link, paragraph,
    redo, strikethrough, subscript,
    successCallout, superscript, underline,
    undo,
    warningCallout
} from "./defaults/button-definitions";
import {EditorContainerUiElement, EditorFormatMenu} from "./framework/containers";


export function getMainEditorFullToolbar(): EditorContainerUiElement {
    return new EditorContainerUiElement([
        new EditorButton(undo),
        new EditorButton(redo),

        new EditorFormatMenu([
            new EditorButton(h2),
            new EditorButton(h3),
            new EditorButton(h4),
            new EditorButton(h5),
            new EditorButton(blockquote),
            new EditorButton(paragraph),
            new EditorButton(infoCallout),
            new EditorButton(successCallout),
            new EditorButton(warningCallout),
            new EditorButton(dangerCallout),
        ]),

        new EditorButton(bold),
        new EditorButton(italic),
        new EditorButton(underline),
        new EditorButton(strikethrough),
        new EditorButton(superscript),
        new EditorButton(subscript),
        new EditorButton(code),

        new EditorButton(link),
    ]);
}