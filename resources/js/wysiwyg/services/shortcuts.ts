import {COMMAND_PRIORITY_HIGH, FORMAT_TEXT_COMMAND, KEY_ENTER_COMMAND, LexicalEditor} from "lexical";
import {
    cycleSelectionCalloutFormats,
    formatCodeBlock,
    toggleSelectionAsBlockquote,
    toggleSelectionAsHeading,
    toggleSelectionAsParagraph
} from "../utils/formats";
import {HeadingTagType} from "@lexical/rich-text";

function headerHandler(editor: LexicalEditor, tag: HeadingTagType): boolean {
    toggleSelectionAsHeading(editor, tag);
    return true;
}

function wrapFormatAction(formatAction: (editor: LexicalEditor) => any): ShortcutAction {
    return (editor: LexicalEditor) => {
        formatAction(editor);
        return true;
    };
}

function toggleInlineCode(editor: LexicalEditor): boolean {
    editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'code');
    return true;
}

type ShortcutAction = (editor: LexicalEditor) => boolean;

const actionsByKeys: Record<string, ShortcutAction> = {
    // Save draft
    'ctrl+s': () => {
        window.$events.emit('editor-save-draft');
        return true;
    },
    'ctrl+enter': () => {
        window.$events.emit('editor-save-page');
        return true;
    },
    'ctrl+1': (editor) => headerHandler(editor, 'h1'),
    'ctrl+2': (editor) => headerHandler(editor, 'h2'),
    'ctrl+3': (editor) => headerHandler(editor, 'h3'),
    'ctrl+4': (editor) => headerHandler(editor, 'h4'),
    'ctrl+5': wrapFormatAction(toggleSelectionAsParagraph),
    'ctrl+d': wrapFormatAction(toggleSelectionAsParagraph),
    'ctrl+6': wrapFormatAction(toggleSelectionAsBlockquote),
    'ctrl+q': wrapFormatAction(toggleSelectionAsBlockquote),
    'ctrl+7': wrapFormatAction(formatCodeBlock),
    'ctrl+e': wrapFormatAction(formatCodeBlock),
    'ctrl+8': toggleInlineCode,
    'ctrl+shift+e': toggleInlineCode,
    'ctrl+9': wrapFormatAction(cycleSelectionCalloutFormats),

    // TODO Lists
    // TODO Links
    // TODO Link selector
};

function createKeyDownListener(editor: LexicalEditor): (e: KeyboardEvent) => void {
    return (event: KeyboardEvent) => {
        // TODO - Mac Cmd support
        const combo = `${event.ctrlKey ? 'ctrl+' : ''}${event.shiftKey ? 'shift+' : ''}${event.key}`.toLowerCase();
        console.log(`pressed: ${combo}`);
        if (actionsByKeys[combo]) {
            const handled = actionsByKeys[combo](editor);
            if (handled) {
                event.stopPropagation();
                event.preventDefault();
            }
        }
    };
}

function overrideDefaultCommands(editor: LexicalEditor) {
    // Prevent default ctrl+enter command
    editor.registerCommand(KEY_ENTER_COMMAND, (event) => {
        return event?.ctrlKey ? true : false
    }, COMMAND_PRIORITY_HIGH);
}

export function registerShortcuts(editor: LexicalEditor) {
    const listener = createKeyDownListener(editor);
    overrideDefaultCommands(editor);

    return editor.registerRootListener((rootElement: null | HTMLElement, prevRootElement: null | HTMLElement) => {
        // add the listener to the current root element
        rootElement?.addEventListener('keydown', listener);
        // remove the listener from the old root element
        prevRootElement?.removeEventListener('keydown', listener);
    });
}