import {patchDomFromHtmlString} from "../services/vdom";

export class Display {

    /**
     * @param {MarkdownEditor} editor
     */
    constructor(editor) {
        this.editor = editor;
        this.container = editor.config.displayEl;

        this.doc = null;
        this.lastDisplayClick = 0;

        if (this.container.contentDocument.readyState === 'complete') {
            this.onLoad();
        } else {
            this.container.addEventListener('load', this.onLoad.bind(this));
        }
    }

    onLoad() {
        this.doc = this.container.contentDocument;

        this.loadStylesIntoDisplay();
        this.doc.body.className = 'page-content';

        // Prevent markdown display link click redirect
        this.doc.addEventListener('click', this.onDisplayClick.bind(this));
    }

    /**
     * @param {MouseEvent} event
     */
    onDisplayClick(event) {
        const isDblClick = Date.now() - this.lastDisplayClick < 300;

        const link = event.target.closest('a');
        if (link !== null) {
            event.preventDefault();
            window.open(link.getAttribute('href'));
            return;
        }

        const drawing = event.target.closest('[drawio-diagram]');
        if (drawing !== null && isDblClick) {
            this.editor.actions.editDrawing(drawing);
            return;
        }

        this.lastDisplayClick = Date.now();
    }

    loadStylesIntoDisplay() {
        this.doc.documentElement.classList.add('markdown-editor-display');

        // Set display to be dark mode if parent is
        if (document.documentElement.classList.contains('dark-mode')) {
            this.doc.documentElement.style.backgroundColor = '#222';
            this.doc.documentElement.classList.add('dark-mode');
        }

        this.doc.head.innerHTML = '';
        const styles = document.head.querySelectorAll('style,link[rel=stylesheet]');
        for (const style of styles) {
            const copy = style.cloneNode(true);
            this.doc.head.appendChild(copy);
        }
    }

    /**
     * Patch the display DOM with the given HTML content.
     * @param {String} html
     */
    patchWithHtml(html) {
        const body = this.doc.body;

        if (body.children.length === 0) {
            const wrap = document.createElement('div');
            this.doc.body.append(wrap);
        }

        const target = body.children[0];

        patchDomFromHtmlString(target, html);
    }

    /**
     * Scroll to the given block index within the display content.
     * Will scroll to the end if the index is -1.
     * @param {Number} index
     */
    scrollToIndex(index) {
        const elems = this.doc.body?.children[0]?.children;
        if (elems && elems.length <= index) return;

        const topElem = (index === -1) ? elems[elems.length-1] : elems[index];
        topElem.scrollIntoView({ block: 'start', inline: 'nearest', behavior: 'smooth'});
    }

}