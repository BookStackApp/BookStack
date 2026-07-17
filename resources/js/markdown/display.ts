import { patchDomFromHtmlString } from '../services/vdom';
import {MarkdownEditor} from "./index.mjs";

export class Display {
    protected editor: MarkdownEditor;
    protected container: HTMLIFrameElement;
    protected doc: Document | null = null;
    protected lastDisplayClick: number = 0;

    constructor(editor: MarkdownEditor) {
        this.editor = editor;
        this.container = editor.config.displayEl;

        if (this.container.contentDocument?.readyState === 'complete') {
            this.onLoad();
        } else {
            this.container.addEventListener('load', this.onLoad.bind(this));
        }

        this.updateVisibility(Boolean(editor.settings.get('showPreview')));
        editor.settings.onChange('showPreview', (show) => this.updateVisibility(Boolean(show)));
    }

    protected updateVisibility(show: boolean): void {
        const wrap = this.container.closest('.markdown-editor-wrap') as HTMLElement;
        wrap.style.display = show ? '' : 'none';
    }

    protected onLoad(): void {
        this.doc = this.container.contentDocument;

        if (!this.doc) return;

        this.loadStylesIntoDisplay();
        this.doc.body.className = 'page-content';

        // Prevent markdown display link click redirect
        this.doc.addEventListener('click', this.onDisplayClick.bind(this));
    }

    protected onDisplayClick(event: MouseEvent): void {
        const isDblClick = Date.now() - this.lastDisplayClick < 300;

        const link = (event.target as Element).closest('a');
        if (link !== null) {
            event.preventDefault();
            const href = link.getAttribute('href');
            if (href) {
                window.open(href);
            }
            return;
        }

        const drawing = (event.target as Element).closest('[drawio-diagram]') as HTMLElement;
        if (drawing !== null && isDblClick) {
            this.editor.actions.editDrawing(drawing);
            return;
        }

        this.lastDisplayClick = Date.now();
    }

    protected loadStylesIntoDisplay(): void {
        if (!this.doc) return;

        this.doc.documentElement.classList.add('markdown-editor-display');

        // Set display to be dark mode if the parent is
        if (document.documentElement.classList.contains('dark-mode')) {
            this.doc.documentElement.style.backgroundColor = '#222';
            this.doc.documentElement.classList.add('dark-mode');
        }

        this.doc.head.innerHTML = '';
        const styles = document.head.querySelectorAll('style,link[rel=stylesheet]');
        for (const style of styles) {
            const copy = style.cloneNode(true) as HTMLElement;
            this.doc.head.appendChild(copy);
        }
    }

    /**
     * Patch the display DOM with the given HTML content.
     */
    public patchWithHtml(html: string): void {
        if (!this.doc) return;

        const { body } = this.doc;

        if (body.children.length === 0) {
            const wrap = document.createElement('div');
            this.doc.body.append(wrap);
        }

        const target = body.children[0] as HTMLElement;

        patchDomFromHtmlString(target, html);
    }

    /**
     * Scroll to the given block index within the display content.
     * Will scroll to the end if the index is -1.
     */
    public scrollToIndex(index: number): void {
        const elems = this.doc?.body?.children[0]?.children;
        if (!elems || elems.length <= index) return;

        const topElem = (index === -1) ? elems[elems.length - 1] : elems[index];
        (topElem as Element).scrollIntoView({
            block: 'start',
            inline: 'nearest',
            behavior: 'smooth'
        });
    }
}