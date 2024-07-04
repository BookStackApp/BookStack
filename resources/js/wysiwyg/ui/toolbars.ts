import {EditorButton} from "./framework/buttons";
import {
    blockquote, bold, bulletList, clearFormating, code, codeBlock,
    dangerCallout, details, fullscreen,
    h2, h3, h4, h5, highlightColor, horizontalRule, image,
    infoCallout, italic, link, numberList, paragraph,
    redo, source, strikethrough, subscript,
    successCallout, superscript, table, taskList, textColor, underline,
    undo, unlink,
    warningCallout
} from "./defaults/button-definitions";
import {EditorContainerUiElement, EditorSimpleClassContainer, EditorUiContext, EditorUiElement} from "./framework/core";
import {el} from "../helpers";
import {EditorFormatMenu} from "./framework/blocks/format-menu";
import {FormatPreviewButton} from "./framework/blocks/format-preview-button";
import {EditorDropdownButton} from "./framework/blocks/dropdown-button";
import {EditorColorPicker} from "./framework/blocks/color-picker";
import {EditorTableCreator} from "./framework/blocks/table-creator";
import {EditorColorButton} from "./framework/blocks/color-button";
import {EditorOverflowContainer} from "./framework/blocks/overflow-container";

export function getMainEditorFullToolbar(): EditorContainerUiElement {
    return new EditorSimpleClassContainer('editor-toolbar-main', [

        // History state
        new EditorOverflowContainer(2, [
            new EditorButton(undo),
            new EditorButton(redo),
        ]),

        // Block formats
        new EditorFormatMenu([
            new FormatPreviewButton(el('h2'), h2),
            new FormatPreviewButton(el('h3'), h3),
            new FormatPreviewButton(el('h4'), h4),
            new FormatPreviewButton(el('h5'), h5),
            new FormatPreviewButton(el('blockquote'), blockquote),
            new FormatPreviewButton(el('p'), paragraph),
            new EditorDropdownButton({label: 'Callouts'}, true, [
                new FormatPreviewButton(el('p', {class: 'callout info'}), infoCallout),
                new FormatPreviewButton(el('p', {class: 'callout success'}), successCallout),
                new FormatPreviewButton(el('p', {class: 'callout warning'}), warningCallout),
                new FormatPreviewButton(el('p', {class: 'callout danger'}), dangerCallout),
            ]),
        ]),

        // Inline formats
        new EditorOverflowContainer(6, [
            new EditorButton(bold),
            new EditorButton(italic),
            new EditorButton(underline),
            new EditorDropdownButton(new EditorColorButton(textColor, 'color'), false, [
                new EditorColorPicker('color'),
            ]),
            new EditorDropdownButton(new EditorColorButton(highlightColor, 'background-color'), false, [
                new EditorColorPicker('background-color'),
            ]),
            new EditorButton(strikethrough),
            new EditorButton(superscript),
            new EditorButton(subscript),
            new EditorButton(code),
            new EditorButton(clearFormating),
        ]),

        // Lists
        new EditorOverflowContainer(3, [
            new EditorButton(bulletList),
            new EditorButton(numberList),
            new EditorButton(taskList),
        ]),

        // Insert types
        new EditorOverflowContainer(6, [
            new EditorButton(link),
            new EditorDropdownButton(table, false, [
                new EditorTableCreator(),
            ]),
            new EditorButton(image),
            new EditorButton(horizontalRule),
            new EditorButton(codeBlock),
            new EditorButton(details),
        ]),

        // Meta elements
        new EditorOverflowContainer(3, [
            new EditorButton(source),
            new EditorButton(fullscreen),

            // Test
            new EditorButton({
                label: 'Test button',
                action(context: EditorUiContext) {
                    context.editor.update(() => {
                        // Do stuff
                    });
                },
                isActive() {
                    return false;
                }
            })
        ]),
    ]);
}

export function getImageToolbarContent(): EditorUiElement[] {
    return [new EditorButton(image)];
}

export function getLinkToolbarContent(): EditorUiElement[] {
    return [
        new EditorButton(link),
        new EditorButton(unlink),
    ];
}