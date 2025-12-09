import {
    $createTextNode,
    $getSelection, $isRangeSelection,
    COMMAND_PRIORITY_NORMAL, RangeSelection, TextNode
} from "lexical";
import {KEY_AT_COMMAND} from "lexical/LexicalCommands";
import {$createMentionNode} from "@lexical/link/LexicalMentionNode";
import {el, htmlToDom} from "../utils/dom";
import {EditorUiContext} from "../ui/framework/core";
import {debounce} from "../../services/util";
import {removeLoading, showLoading} from "../../services/dom";


function enterUserSelectMode(context: EditorUiContext, selection: RangeSelection) {
    const textNode = selection.getNodes()[0] as TextNode;
    const selectionPos = selection.getStartEndPoints();
    if (!selectionPos) {
        return;
    }

    const offset = selectionPos[0].offset;

    // Ignore if the @ sign is not after a space or the start of the line
    const atStart = offset === 0;
    const afterSpace = textNode.getTextContent().charAt(offset - 1) === ' ';
    if (!atStart && !afterSpace) {
        return;
    }

    const split = textNode.splitText(offset);
    const newNode = split[atStart ? 0 : 1];

    const mention = $createMentionNode(0, '', '');
    newNode.replace(mention);
    mention.select();

    const revertEditorMention = () => {
        context.editor.update(() => {
            const text = $createTextNode('@');
            mention.replace(text);
            text.selectEnd();
        });
    };

    requestAnimationFrame(() => {
        const mentionDOM = context.editor.getElementByKey(mention.getKey());
        if (!mentionDOM) {
            revertEditorMention();
            return;
        }

        const selectList = buildAndShowUserSelectorAtElement(context, mentionDOM);
        handleUserListLoading(selectList);
        handleUserSelectCancel(context, selectList, revertEditorMention);
    });


    // TODO - On enter, replace with name mention element.
}

function handleUserSelectCancel(context: EditorUiContext, selectList: HTMLElement, revertEditorMention: () => void) {
    const controller = new AbortController();

    const onCancel = () => {
        revertEditorMention();
        selectList.remove();
        controller.abort();
    }

    selectList.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            onCancel();
        }
    }, {signal: controller.signal});

    const input = selectList.querySelector('input') as HTMLInputElement;
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' && input.value === '') {
            onCancel();
            event.preventDefault();
            event.stopPropagation();
        }
    }, {signal: controller.signal});

    context.editorDOM.addEventListener('click', (event) => {
        onCancel()
    }, {signal: controller.signal});
    context.editorDOM.addEventListener('keydown', (event) => {
        onCancel();
    }, {signal: controller.signal});
}

function handleUserListLoading(selectList: HTMLElement) {
    const cache = new Map<string, string>();

    const updateUserList = async (searchTerm: string) => {
        // Empty list
        for (const child of [...selectList.children].slice(1)) {
            child.remove();
        }

        // Fetch new content
        let responseHtml = '';
        if (cache.has(searchTerm)) {
            responseHtml = cache.get(searchTerm) || '';
        } else {
            const loadingWrap = el('li');
            showLoading(loadingWrap);
            selectList.appendChild(loadingWrap);

            const resp = await window.$http.get(`/search/users/mention?search=${searchTerm}`);
            responseHtml = resp.data as string;
            cache.set(searchTerm, responseHtml);
            loadingWrap.remove();
        }

        const doc = htmlToDom(responseHtml);
        const toInsert = doc.querySelectorAll('li');
        for (const listEl of toInsert) {
            const adopted = window.document.adoptNode(listEl) as HTMLElement;
            selectList.appendChild(adopted);
        }

    };

    // Initial load
    updateUserList('');

    const input = selectList.querySelector('input') as HTMLInputElement;
    const updateUserListDebounced = debounce(updateUserList, 200, false);
    input.addEventListener('input', () => {
        const searchTerm = input.value;
        updateUserListDebounced(searchTerm);
    });
}

function buildAndShowUserSelectorAtElement(context: EditorUiContext, mentionDOM: HTMLElement): HTMLElement {
    const searchInput = el('input', {type: 'text'});
    const searchItem = el('li', {}, [searchInput]);
    const userSelect = el('ul', {class: 'suggestion-box dropdown-menu'}, [searchItem]);

    context.containerDOM.appendChild(userSelect);

    userSelect.style.display = 'block';
    userSelect.style.top = '0';
    userSelect.style.left = '0';
    const mentionPos = mentionDOM.getBoundingClientRect();
    const userSelectPos = userSelect.getBoundingClientRect();
    userSelect.style.top = `${mentionPos.bottom - userSelectPos.top + 3}px`;
    userSelect.style.left = `${mentionPos.left - userSelectPos.left}px`;

    searchInput.focus();

    return userSelect;
}

export function registerMentions(context: EditorUiContext): () => void {
    const editor = context.editor;

    const unregisterCommand = editor.registerCommand(KEY_AT_COMMAND, function (event: KeyboardEvent): boolean {
        const selection = $getSelection();
        if ($isRangeSelection(selection) && selection.isCollapsed()) {
            window.setTimeout(() => {
                editor.update(() => {
                    enterUserSelectMode(context, selection);
                });
            }, 1);
        }
        return false;
    }, COMMAND_PRIORITY_NORMAL);

    return (): void => {
        unregisterCommand();
    };
}