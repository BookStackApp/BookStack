import {EditorButton} from "../framework/buttons";
import {EditorContainerUiElement, EditorSimpleClassContainer, EditorUiContext, EditorUiElement} from "../framework/core";
import {EditorFormatMenu} from "../framework/blocks/format-menu";
import {FormatPreviewButton} from "../framework/blocks/format-preview-button";
import {EditorDropdownButton} from "../framework/blocks/dropdown-button";
import {EditorColorPicker} from "../framework/blocks/color-picker";
import {EditorTableCreator} from "../framework/blocks/table-creator";
import {EditorColorButton} from "../framework/blocks/color-button";
import {EditorOverflowContainer} from "../framework/blocks/overflow-container";
import {
    cellProperties, clearTableFormatting,
    copyColumn,
    copyRow,
    cutColumn,
    cutRow,
    deleteColumn,
    deleteRow,
    deleteTable,
    deleteTableMenuAction,
    insertColumnAfter,
    insertColumnBefore,
    insertRowAbove,
    insertRowBelow,
    mergeCells,
    pasteColumnAfter,
    pasteColumnBefore,
    pasteRowAfter,
    pasteRowBefore, resizeTableToContents,
    rowProperties,
    splitCell,
    table, tableProperties
} from "./buttons/tables";
import {about, fullscreen, redo, source, undo} from "./buttons/controls";
import {
    blockquote, dangerCallout,
    h2,
    h3,
    h4,
    h5,
    infoCallout,
    paragraph,
    successCallout,
    warningCallout
} from "./buttons/block-formats";
import {
    bold, clearFormating, code,
    highlightColor, highlightColorAction,
    italic,
    strikethrough, subscript,
    superscript,
    textColor, textColorAction,
    underline
} from "./buttons/inline-formats";
import {
    alignCenter,
    alignJustify,
    alignLeft,
    alignRight,
    directionLTR,
    directionRTL
} from "./buttons/alignments";
import {
    bulletList,
    indentDecrease,
    indentIncrease,
    numberList,
    taskList
} from "./buttons/lists";
import {
    codeBlock,
    details, detailsEditLabel, detailsToggle, detailsUnwrap,
    diagram, diagramManager,
    editCodeBlock,
    horizontalRule,
    image,
    link, media,
    unlink
} from "./buttons/objects";
import {el} from "../../utils/dom";
import {EditorButtonWithMenu} from "../framework/blocks/button-with-menu";
import {EditorSeparator} from "../framework/blocks/separator";
import {EditorContextToolbarDefinition} from "../framework/toolbars";

export function getMainEditorFullToolbar(context: EditorUiContext): EditorContainerUiElement {

    const inRtlMode = context.manager.getDefaultDirection() === 'rtl';

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
            new EditorDropdownButton({button: {label: 'Callouts', format: 'long'}, showOnHover: true, direction: 'vertical'}, [
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
            new EditorDropdownButton({ button: new EditorColorButton(textColor, 'color') }, [
                new EditorColorPicker(textColorAction),
            ]),
            new EditorDropdownButton({button: new EditorColorButton(highlightColor, 'background-color')}, [
                new EditorColorPicker(highlightColorAction),
            ]),
            new EditorButton(strikethrough),
            new EditorButton(superscript),
            new EditorButton(subscript),
            new EditorButton(code),
            new EditorButton(clearFormating),
        ]),

        // Alignment
        new EditorOverflowContainer(6, [
            new EditorButton(alignLeft),
            new EditorButton(alignCenter),
            new EditorButton(alignRight),
            new EditorButton(alignJustify),
            inRtlMode ? new EditorButton(directionLTR) : null,
            inRtlMode ? new EditorButton(directionRTL) : null,
        ].filter(x => x !== null)),

        // Lists
        new EditorOverflowContainer(3, [
            new EditorButton(bulletList),
            new EditorButton(numberList),
            new EditorButton(taskList),
            new EditorButton(indentDecrease),
            new EditorButton(indentIncrease),
        ]),

        // Insert types
        new EditorOverflowContainer(4, [
            new EditorButton(link),

            new EditorDropdownButton({button: table, direction: 'vertical', showAside: false}, [
                new EditorDropdownButton({button: {label: 'Insert', format: 'long'}, showOnHover: true, showAside: true}, [
                    new EditorTableCreator(),
                ]),
                new EditorSeparator(),
                new EditorDropdownButton({button: {label: 'Cell', format: 'long'}, direction: 'vertical', showOnHover: true}, [
                    new EditorButton(cellProperties),
                    new EditorButton(mergeCells),
                    new EditorButton(splitCell),
                ]),
                new EditorDropdownButton({button: {label: 'Row', format: 'long'}, direction: 'vertical', showOnHover: true}, [
                    new EditorButton({...insertRowAbove, format: 'long'}),
                    new EditorButton({...insertRowBelow, format: 'long'}),
                    new EditorButton({...deleteRow, format: 'long'}),
                    new EditorButton(rowProperties),
                    new EditorSeparator(),
                    new EditorButton(cutRow),
                    new EditorButton(copyRow),
                    new EditorButton(pasteRowBefore),
                    new EditorButton(pasteRowAfter),
                ]),
                new EditorDropdownButton({button: {label: 'Column', format: 'long'}, direction: 'vertical', showOnHover: true}, [
                    new EditorButton({...insertColumnBefore, format: 'long'}),
                    new EditorButton({...insertColumnAfter, format: 'long'}),
                    new EditorButton({...deleteColumn, format: 'long'}),
                    new EditorSeparator(),
                    new EditorButton(cutColumn),
                    new EditorButton(copyColumn),
                    new EditorButton(pasteColumnBefore),
                    new EditorButton(pasteColumnAfter),
                ]),
                new EditorSeparator(),
                new EditorButton({...tableProperties, format: 'long'}),
                new EditorButton(clearTableFormatting),
                new EditorButton(resizeTableToContents),
                new EditorButton(deleteTableMenuAction),
            ]),

            new EditorButton(image),
            new EditorButton(horizontalRule),
            new EditorButton(codeBlock),
            new EditorButtonWithMenu(
                new EditorButton(diagram),
                [new EditorButton(diagramManager)],
            ),
            new EditorButton(media),
            new EditorButton(details),
        ]),

        // Meta elements
        new EditorOverflowContainer(3, [
            new EditorButton(source),
            new EditorButton(about),
            new EditorButton(fullscreen),

            // Test
            // new EditorButton({
            //     label: 'Test button',
            //     action(context: EditorUiContext) {
            //         context.editor.update(() => {
            //             // Do stuff
            //         });
            //     },
            //     isActive() {
            //         return false;
            //     }
            // })
        ]),
    ]);
}

export function getBasicEditorToolbar(context: EditorUiContext): EditorContainerUiElement {
    return new EditorSimpleClassContainer('editor-toolbar-main', [
        new EditorButton(bold),
        new EditorButton(italic),
        new EditorButton(link),
        new EditorButton(bulletList),
        new EditorButton(numberList),
    ]);
}

export const contextToolbars: Record<string, EditorContextToolbarDefinition> = {
    image: {
        selector: 'img:not([drawio-diagram] img)',
        content: () => [new EditorButton(image)],
    },
    media: {
        selector: '.editor-media-wrap',
        content: () => [new EditorButton(media)],
    },
    link: {
        selector: 'a',
        content() {
            return [
                new EditorButton(link),
                new EditorButton(unlink),
            ]
        },
        displayTargetLocator(originalTarget: HTMLElement): HTMLElement {
            const image = originalTarget.querySelector('img');
            return image || originalTarget;
        }
    },
    code: {
        selector: '.editor-code-block-wrap',
        content: () => [new EditorButton(editCodeBlock)],
    },
    table: {
        selector: 'td,th',
        content() {
            return [
                new EditorOverflowContainer(2, [
                    new EditorButton(tableProperties),
                    new EditorButton(deleteTable),
                ]),
                new EditorOverflowContainer(3, [
                    new EditorButton(insertRowAbove),
                    new EditorButton(insertRowBelow),
                    new EditorButton(deleteRow),
                ]),
                new EditorOverflowContainer(3, [
                    new EditorButton(insertColumnBefore),
                    new EditorButton(insertColumnAfter),
                    new EditorButton(deleteColumn),
                ]),
            ];
        },
        displayTargetLocator(originalTarget: HTMLElement): HTMLElement {
            return originalTarget.closest('table') as HTMLTableElement;
        }
    },
    details: {
        selector: 'details',
        content() {
            return [
                new EditorButton(detailsEditLabel),
                new EditorButton(detailsToggle),
                new EditorButton(detailsUnwrap),
            ]
        },
    },
};