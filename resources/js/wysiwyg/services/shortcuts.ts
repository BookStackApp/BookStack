import {$getSelection, COMMAND_PRIORITY_HIGH, FORMAT_TEXT_COMMAND, KEY_ENTER_COMMAND, LexicalEditor} from "lexical";
import {
    cycleSelectionCalloutFormats,
    formatCodeBlock, insertOrUpdateLink,
    toggleSelectionAsBlockquote,
    toggleSelectionAsHeading, toggleSelectionAsList,
    toggleSelectionAsParagraph
} from "../utils/formats";
import {EditorUiContext} from "../ui/framework/core";
import {$getNodeFromSelection} from "../utils/selection";
import {$isLinkNode, LinkNode} from "@lexical/link";
import {$showLinkForm} from "../ui/defaults/forms/objects";
import {showLinkSelector} from "../utils/links";
import {HeadingTagType} from "@lexical/rich-text/LexicalHeadingNode";

function headerHandler(context: EditorUiContext, tag: HeadingTagType): boolean {
    toggleSelectionAsHeading(context.editor, tag);
    context.manager.triggerFutureStateRefresh();
    return true;
}

function wrapFormatAction(formatAction: (editor: LexicalEditor) => any): ShortcutAction {
    return (editor: LexicalEditor, context: EditorUiContext) => {
        formatAction(editor);
        context.manager.triggerFutureStateRefresh();
        return true;
    };
}

function toggleInlineCode(editor: LexicalEditor): boolean {
    editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'code');
    return true;
}

type ShortcutAction = (editor: LexicalEditor, context: EditorUiContext) => boolean;

/**
 * List of action functions by their shortcut combo.
 * We use "meta" as an abstraction for ctrl/cmd depending on platform.
 */
const actionsByKeys: Record<string, ShortcutAction> = {
    'meta+s': () => {
        window.$events.emit('editor-save-draft');
        return true;
    },
    'meta+enter': () => {
        window.$events.emit('editor-save-page');
        return true;
    },
    'meta+1': (editor, context) => headerHandler(context, 'h2'),
    'meta+2': (editor, context) => headerHandler(context, 'h3'),
    'meta+3': (editor, context) => headerHandler(context, 'h4'),
    'meta+4': (editor, context) => headerHandler(context, 'h5'),
    'meta+5': wrapFormatAction(toggleSelectionAsParagraph),
    'meta+d': wrapFormatAction(toggleSelectionAsParagraph),
    'meta+6': wrapFormatAction(toggleSelectionAsBlockquote),
    'meta+q': wrapFormatAction(toggleSelectionAsBlockquote),
    'meta+7': wrapFormatAction(formatCodeBlock),
    'meta+e': wrapFormatAction(formatCodeBlock),
    'meta+8': toggleInlineCode,
    'meta+shift+e': toggleInlineCode,
    'meta+9': wrapFormatAction(cycleSelectionCalloutFormats),

    'meta+o': wrapFormatAction((e) => toggleSelectionAsList(e, 'number')),
    'meta+p': wrapFormatAction((e) => toggleSelectionAsList(e, 'bullet')),
    'meta+k': (editor, context) => {
        editor.getEditorState().read(() => {
            const selectedLink = $getNodeFromSelection($getSelection(), $isLinkNode) as LinkNode | null;
            $showLinkForm(selectedLink, context);
        });
        return true;
    },
    'meta+shift+k': (editor, context) => {
        showLinkSelector(entity => {
            insertOrUpdateLink(editor, {
                text: entity.name,
                title: entity.link,
                target: '',
                url: entity.link,
            });
        });
        return true;
    },
};

function createKeyDownListener(context: EditorUiContext): (e: KeyboardEvent) => void {
    return (event: KeyboardEvent) => {
        const combo = keyboardEventToKeyComboString(event);
        // console.log(`pressed: ${combo}`);
        if (actionsByKeys[combo]) {
            const handled = actionsByKeys[combo](context.editor, context);
            if (handled) {
                event.stopPropagation();
                event.preventDefault();
            }
        }
    };
}

function keyboardEventToKeyComboString(event: KeyboardEvent): string {
    const metaKeyPressed = isMac() ? event.metaKey : event.ctrlKey;

    const parts = [
        metaKeyPressed ? 'meta' : '',
        event.shiftKey ? 'shift' : '',
        event.key,
    ];

    return parts.filter(Boolean).join('+').toLowerCase();
}

function isMac(): boolean {
    return window.navigator.userAgent.includes('Mac OS X');
}

function overrideDefaultCommands(editor: LexicalEditor) {
    // Prevent default ctrl+enter command
    editor.registerCommand(KEY_ENTER_COMMAND, (event) => {
        if (isMac()) {
            return event?.metaKey || false;
        }
        return event?.ctrlKey || false;
    }, COMMAND_PRIORITY_HIGH);
}

export function registerShortcuts(context: EditorUiContext) {
    const listener = createKeyDownListener(context);
    overrideDefaultCommands(context.editor);

    return context.editor.registerRootListener((rootElement: null | HTMLElement, prevRootElement: null | HTMLElement) => {
        // add the listener to the current root element
        rootElement?.addEventListener('keydown', listener);
        // remove the listener from the old root element
        prevRootElement?.removeEventListener('keydown', listener);
    });
}