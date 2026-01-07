import {EditorDecorator} from "../framework/decorator";
import {EditorUiContext} from "../framework/core";
import {el, htmlToDom} from "../../utils/dom";
import {showLoading} from "../../../services/dom";
import {MentionNode} from "@lexical/link/LexicalMentionNode";
import {debounce} from "../../../services/util";
import {$createTextNode} from "lexical";
import {KeyboardNavigationHandler} from "../../../services/keyboard-navigation";

import searchIcon from "@icons/search.svg";

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
        for (const child of [...selectList.children]) {
            child.remove();
        }

        // Fetch new content
        let responseHtml = '';
        if (cache.has(searchTerm)) {
            responseHtml = cache.get(searchTerm) || '';
        } else {
            const loadingWrap = el('div', {class: 'flex-container-row items-center dropdown-search-item'});
            showLoading(loadingWrap);
            selectList.appendChild(loadingWrap);

            const resp = await window.$http.get(`/search/users/mention?search=${searchTerm}`);
            responseHtml = resp.data as string;
            cache.set(searchTerm, responseHtml);
            loadingWrap.remove();
        }

        const doc = htmlToDom(responseHtml);
        const toInsert = [...doc.body.children];
        for (const listEl of toInsert) {
            const adopted = window.document.adoptNode(listEl) as HTMLElement;
            selectList.appendChild(adopted);
        }
    };

    // Initial load
    updateUserList('');

    const input = selectList.parentElement?.querySelector('input') as HTMLInputElement;
    const updateUserListDebounced = debounce(updateUserList, 200, false);
    input.addEventListener('input', () => {
        const searchTerm = input.value;
        updateUserListDebounced(searchTerm);
    });
}

function buildAndShowUserSelectorAtElement(context: EditorUiContext, mentionDOM: HTMLElement): HTMLElement {
    const searchInput = el('input', {type: 'text'});
    const list = el('div', {class: 'dropdown-search-list'});
    const iconWrap = el('div');
    iconWrap.innerHTML = searchIcon;
    const icon = iconWrap.children[0] as HTMLElement;
    icon.classList.add('svg-icon');
    const userSelect = el('div', {class: 'dropdown-search-dropdown compact card'}, [
        el('div', {class: 'dropdown-search-search'}, [icon, searchInput]),
        list,
    ]);

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
    protected abortController: AbortController | null = null;
    protected dropdownContainer: HTMLElement | null = null;
    protected mentionElement: HTMLElement | null = null;

    setup(element: HTMLElement) {
        this.mentionElement = element;

        element.addEventListener('click', (event: PointerEvent) => {
            this.showSelection();
            event.preventDefault();
            event.stopPropagation();
        });
    }

    showSelection() {
        if (!this.mentionElement || this.dropdownContainer) {
            return;
        }

        this.hideSelection();
        this.abortController = new AbortController();

        this.dropdownContainer = buildAndShowUserSelectorAtElement(this.context, this.mentionElement);
        handleUserListLoading(this.dropdownContainer.querySelector('.dropdown-search-list') as HTMLElement);

        this.dropdownContainer.addEventListener('click', userClickHandler((id, name, slug) => {
            this.context.editor.update(() => {
                const mentionNode = this.getNode() as MentionNode;
                this.hideSelection();
                mentionNode.setUserDetails(id, name, slug);
                mentionNode.selectNext();
            });
        }), {signal: this.abortController.signal});

        handleUserSelectCancel(this.context, this.dropdownContainer, this.abortController, () => {
            if ((this.getNode() as MentionNode).hasUserSet()) {
                this.hideSelection()
            } else {
                this.revertMention();
            }
        });

        new KeyboardNavigationHandler(this.dropdownContainer);
    }

    hideSelection() {
        this.abortController?.abort();
        this.dropdownContainer?.remove();
        this.abortController = null;
        this.dropdownContainer = null;
        this.context.manager.focus();
    }

    revertMention() {
        this.hideSelection();
        this.context.editor.update(() => {
            const text = $createTextNode('@');
            const before = this.getNode().getPreviousSibling();
            this.getNode().replace(text);
            requestAnimationFrame(() => {
                this.context.editor.update(() => {
                    if (text.isAttached()) {
                        text.selectEnd();
                    } else if (before?.isAttached()) {
                        before?.selectEnd();
                    }
                });
            });
        });
    }

    render(element: HTMLElement): void {
        this.setup(element);
    }
}