import {
    $getSelection,
    COMMAND_PRIORITY_HIGH,
    FORMAT_TEXT_COMMAND,
    KEY_ENTER_COMMAND,
    LexicalEditor,
    TextFormatType
} from "lexical";
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

function toggleInlineFormat(editor: LexicalEditor, format: TextFormatType): boolean {
    editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
    return true;
}

type ShortcutAction = (editor: LexicalEditor, context: EditorUiContext) => boolean;

/**
 * List of action functions by their shortcut combo.
 * We use "meta" as an abstraction for ctrl/cmd depending on platform.
 */
const baseActionsByKeys: Record<string, ShortcutAction> = {
    'meta+8': (e) => toggleInlineFormat(e, 'code'),
    'meta+shift+e': (e) => toggleInlineFormat(e, 'code'),
    'meta+b': (e) => toggleInlineFormat(e, 'bold'),
    'meta+i': (e) => toggleInlineFormat(e, 'italic'),
    'meta+u': (e) => toggleInlineFormat(e, 'underline'),
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
        editor.getEditorState().read(() => {
            const selection = $getSelection();
            const selectionText = selection?.getTextContent() || '';
            showLinkSelector(entity => {
                insertOrUpdateLink(editor, {
                    text: entity.name,
                    title: entity.link,
                    target: '',
                    url: entity.link,
                });
            }, selectionText);
        });
        return true;
    },
};

/**
 * An extended set of the above, used for fuller-featured editors with heavier block-level formatting.
 */
const extendedActionsByKeys: Record<string, ShortcutAction> = {
    ...baseActionsByKeys,
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
    'meta+7': wrapFormatAction(formatCodeBlock),
    'meta+e': wrapFormatAction(formatCodeBlock),
    'meta+q': wrapFormatAction(toggleSelectionAsBlockquote),
    'meta+9': wrapFormatAction(cycleSelectionCalloutFormats),
};

function createKeyDownListener(context: EditorUiContext, useExtended: boolean): (e: KeyboardEvent) => void {
    const baseKeySetToUse = useExtended ? extendedActionsByKeys : baseActionsByKeys;
    const keySetToUse = extendKeySetWithKeyCodes(baseKeySetToUse);
    return (event: KeyboardEvent) => {
        const comboStrings = keyboardEventToKeyComboStrings(event);
        // console.log(comboStrings, event, keySetToUse);
        for (const combo of comboStrings) {
            if (keySetToUse[combo]) {
                const handled = keySetToUse[combo](context.editor, context);
                if (handled) {
                    event.stopPropagation();
                    event.preventDefault();
                }
                break;
            }
        }
    };
}

/**
 * Takes a shortcut key set and returns a new set with added variations of shortcts where
 * they can be sensibly represented as their key code instead of just key, which we can use
 * for matching in scenarios where the physical key may be represented of the letter used
 * in the shortcut, but produces a different 'key' value.
 * Useful for Cyrillic scenarios where the keyboard key would show a latin character
 * as an option, and therefore be expected for use for the relevant latin shortcut, but the main
 * key output is a Cyrillic character.
 */
function extendKeySetWithKeyCodes(keySet: Record<string, ShortcutAction>): Record<string, ShortcutAction> {
    const newKeys: Record<string, ShortcutAction> = {};

    const setKeys = Object.keys(keySet);
    for (const keyCombo of setKeys) {
        const action = keySet[keyCombo];
        newKeys[keyCombo] = action;

        const comboParts = keyCombo.split('+');
        const lastComboPart = comboParts.pop() || '';
        if (lastComboPart.match(/^[a-zA-Z]$/)) {
            const keyCode = lastComboPart.toUpperCase().charCodeAt(0);
            comboParts.push(String(keyCode));
            const newCombo = comboParts.join('+');
            newKeys[newCombo] = action;
        }
    }

    return newKeys;
}

function keyboardEventToKeyComboStrings(event: KeyboardEvent): string[] {
    const metaKeyPressed = isMac() ? event.metaKey : event.ctrlKey;

    const mainParts = [
        metaKeyPressed ? 'meta' : '',
        event.shiftKey ? 'shift' : '',
        event.key,
    ];

    const toReturn = [
        mainParts.filter(Boolean).join('+').toLowerCase(),
    ];

    // If ending with a standard latin character, provide an alternative
    // keyCode based option for scenarios of dual-language keyboard use.
    const keyCode = event.keyCode || 0;
    if (keyCode >= 65 && keyCode <= 90) {
        const keyCodeParts = [...mainParts];
        keyCodeParts.pop();
        keyCodeParts.push(String(keyCode));
        toReturn.push(keyCodeParts.filter(Boolean).join('+').toLowerCase());
    }

    return toReturn;
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

export function registerShortcuts(context: EditorUiContext, useExtended: boolean) {
    const listener = createKeyDownListener(context, useExtended);
    overrideDefaultCommands(context.editor);

    return context.editor.registerRootListener((rootElement: null | HTMLElement, prevRootElement: null | HTMLElement) => {
        // add the listener to the current root element
        rootElement?.addEventListener('keydown', listener);
        // remove the listener from the old root element
        prevRootElement?.removeEventListener('keydown', listener);
    });
}