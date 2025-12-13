import {EditorDecorator} from "../framework/decorator";
import {EditorUiContext} from "../framework/core";
import {el, htmlToDom} from "../../utils/dom";
import {showLoading} from "../../../services/dom";
import {MentionNode} from "@lexical/link/LexicalMentionNode";
import {debounce} from "../../../services/util";
import {$createTextNode} from "lexical";

function userClickHandler(onSelect: (id: number, name: string, slug: string)=>void): (event: PointerEvent) => void {
    return (event: PointerEvent) => {
        const userItem = (event.target as HTMLElement).closest('a[data-id]') as HTMLAnchorElement | null;
        if (!userItem) {
            return;
        }

        const id = Number(userItem.dataset.id || '0');
        const name = userItem.dataset.name || '';
        const slug = userItem.dataset.slug || '';

        onSelect(id, name, slug);
        event.preventDefault();
    };
}

function handleUserSelectCancel(context: EditorUiContext, selectList: HTMLElement, controller: AbortController, onCancel: () => void): void {
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

export class MentionDecorator extends EditorDecorator {
    protected completedSetup: boolean = false;
    protected abortController: AbortController | null = null;
    protected selectList: HTMLElement | null = null;
    protected mentionElement: HTMLElement | null = null;

    setup(element: HTMLElement) {
        this.mentionElement = element;
        this.completedSetup = true;
    }

    showSelection() {
        if (!this.mentionElement) {
            return;
        }

        this.hideSelection();
        this.abortController = new AbortController();

        this.selectList = buildAndShowUserSelectorAtElement(this.context, this.mentionElement);
        handleUserListLoading(this.selectList);

        this.selectList.addEventListener('click', userClickHandler((id, name, slug) => {
            this.context.editor.update(() => {
                const mentionNode = this.getNode() as MentionNode;
                this.hideSelection();
                mentionNode.setUserDetails(id, name, slug);
                mentionNode.selectNext();
            });
        }), {signal: this.abortController.signal});

        handleUserSelectCancel(this.context, this.selectList, this.abortController, this.revertMention.bind(this));
    }

    hideSelection() {
        this.abortController?.abort();
        this.selectList?.remove();
    }

    revertMention() {
        this.hideSelection();
        this.context.editor.update(() => {
            const text = $createTextNode('@');
            this.getNode().replace(text);
            text.selectEnd();
        });
    }

    update() {
        //
    }

    render(element: HTMLElement): void {
        if (this.completedSetup) {
            this.update();
        } else {
            this.setup(element);
        }
    }
}