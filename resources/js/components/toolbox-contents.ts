import {Component} from "./component";
import {debounce} from "../services/util";
import {EditorToolboxChangeEventData} from "./editor-toolbox";
import {PageEditor} from "./page-editor";
import {MarkdownEditor} from "./markdown-editor";
import {WysiwygEditorTinymce} from "./wysiwyg-editor-tinymce";
import {WysiwygEditor} from "./wysiwyg-editor";
import {elem} from "../services/dom";
import {patchDomFromDom} from "../services/vdom";

interface ToolboxContentHeader {
    // The text shown for the header
    text: string;
    // The level/depth of the header
    level: number;
    // The index of the header relative to all other headers in the content
    index: number;
    // The id set for the header (if at all)
    id: string;
}

export class ToolboxContents extends Component {
    protected container!: HTMLElement;
    protected noneEl!: HTMLElement;
    protected display!: HTMLElement;
    protected isActive: boolean = false;

    setup() {
        this.container = this.$el as HTMLLinkElement;
        this.noneEl = this.$refs.none;
        this.display = this.$refs.display;

        // Listen to when visible in the editor toolbox so we only update when visible
        window.addEventListener('editor-toolbox-change', ((event: CustomEvent<EditorToolboxChangeEventData>) => {
            const tabName: string = event.detail.tab;
            const isOpen = event.detail.open;
            if (tabName === 'contents' && isOpen) {
                if (this.isActive !== true) {
                    this.render();
                }
                this.isActive = true;
            } else {
                this.isActive = false;
            }
        }) as EventListener);

        // Listen to content changes from the editor
        const onContentChangeDebounced = debounce(this.onContentChange.bind(this), 500, false);
        window.$events.listen('editor-html-change', onContentChangeDebounced);
        window.$events.listen('editor-markdown-change', onContentChangeDebounced);

        // Listen to header click
        this.container.addEventListener('click', (event) => {
            const header = (event.target as HTMLElement).closest('li[data-index]');
            if (header instanceof HTMLElement) {
                event.preventDefault();
                this.onHeaderSelect(header);
            }
        });
    }

    protected onHeaderSelect(headerElement: HTMLElement): void {
        const id = headerElement.getAttribute('data-id') || '';
        const index = Number(headerElement.getAttribute('data-index') || '');
        window.$events.emit('editor::focus-heading', {
            index,
            id,
        });
    }

    protected onContentChange(): void {
        if (!this.isActive) {
            return;
        }
        this.render();
    }

    protected async render(): Promise<void> {
        const editorHtml = await this.getEditorHtml();
        const headers = this.parseHeadersFromHtml(editorHtml);
        this.rebaseHeaders(headers);

        const headerDom = this.headersToDom(headers);
        let displayChild = this.display.firstElementChild;
        if (!displayChild) {
            displayChild = document.createElement("ul");
            this.display.appendChild(displayChild);
        }
        patchDomFromDom(displayChild, headerDom);

        this.noneEl.hidden = headers.length > 0;
    }

    protected async getEditorHtml(): Promise<string> {
        const pageEditorComponent = window.$components.first('page-editor') as PageEditor;
        const editor = pageEditorComponent.getEditorComponent() as (MarkdownEditor|WysiwygEditorTinymce|WysiwygEditor);
        return (await editor.getContent()).html;
    }

    protected parseHeadersFromHtml(html: string): ToolboxContentHeader[] {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const headerNodes = doc.querySelectorAll('h1, h2, h3, h4, h5, h6');
        const headers: ToolboxContentHeader[] = [];

        for (let i = 0; i < headerNodes.length; i++) {
            const headerNode = headerNodes[i];
            const level = Number(headerNode.tagName.replace('H', ''));
            headers.push({
                text: headerNode.textContent,
                level,
                id: headerNode.id || '',
                index: i,
            });
        }

        return headers;
    }

    protected rebaseHeaders(headers: ToolboxContentHeader[]): void {
        if (headers.length === 0) {
            return;
        }

        do {
            var minLevel = Math.min(...headers.map(h => h.level));
            if (minLevel > 1) {
                for (const header of headers) {
                    header.level--;
                }
            }
        } while (minLevel > 1);
    }

    protected headersToDom(headers: ToolboxContentHeader[]): HTMLElement {
        const headerItems = headers.map(header => {
            return elem('li', {
                'data-level': String(header.level),
                'data-index': String(header.index),
                'data-id': header.id,
                id: `page-contents-${header.id}`,
                class: `page-nav-item h${header.level}`,
            }, [
                elem('a', {class: 'text-limit-lines-1 block', href: `#${header.id}`}, [header.text]),
                elem('div', {class: 'link-background sidebar-page-nav-bullet'}),
            ]);
        });
        return elem('ul', {
            class: 'sidebar-page-nav menu'
        }, headerItems);
    }
}
