import * as DrawIO from '../services/drawio';
import {MarkdownEditor} from "./index.mjs";
import {EntitySelectorPopup, ImageManager} from "../components";
import {MarkdownEditorInputSelection} from "./inputs/interface";

interface ImageManagerImage {
    id: number;
    name: string;
    thumbs: { display: string; };
    url: string;
}

export class Actions {

    protected readonly editor: MarkdownEditor;
    protected lastContent: { html: string; markdown: string } = {
        html: '',
        markdown: '',
    };

    constructor(editor: MarkdownEditor) {
        this.editor = editor;
    }

    updateAndRender() {
        const content = this.editor.input.getText();
        this.editor.config.inputEl.value = content;

        const html = this.editor.markdown.render(content);
        window.$events.emit('editor-html-change', '');
        window.$events.emit('editor-markdown-change', '');
        this.lastContent.html = html;
        this.lastContent.markdown = content;
        this.editor.display.patchWithHtml(html);
    }

    getContent() {
        return this.lastContent;
    }

    showImageInsert() {
        const imageManager = window.$components.first('image-manager') as ImageManager;

        imageManager.show((image: ImageManagerImage) => {
            const imageUrl = image.thumbs?.display || image.url;
            const selectedText = this.editor.input.getSelectionText();
            const newText = `[![${selectedText || image.name}](${imageUrl})](${image.url})`;
            this.#replaceSelection(newText, newText.length);
        }, 'gallery');
    }

    insertImage() {
        const newText = `![${this.editor.input.getSelectionText()}](http://)`;
        this.#replaceSelection(newText, newText.length - 1);
    }

    insertLink() {
        const selectedText = this.editor.input.getSelectionText();
        const newText = `[${selectedText}]()`;
        const cursorPosDiff = (selectedText === '') ? -3 : -1;
        this.#replaceSelection(newText, newText.length + cursorPosDiff);
    }

    showImageManager() {
        const selectionRange = this.editor.input.getSelection();
        const imageManager = window.$components.first('image-manager') as ImageManager;
        imageManager.show((image: ImageManagerImage) => {
            this.#insertDrawing(image, selectionRange);
        }, 'drawio');
    }

    // Show the popup link selector and insert a link when finished
    showLinkSelector() {
        const selectionRange = this.editor.input.getSelection();

        const selector = window.$components.first('entity-selector-popup') as EntitySelectorPopup;
        const selectionText = this.editor.input.getSelectionText(selectionRange);
        selector.show(entity => {
            const selectedText = selectionText || entity.name;
            const newText = `[${selectedText}](${entity.link})`;
            this.#replaceSelection(newText, newText.length, selectionRange);
        }, {
            initialValue: selectionText,
            searchEndpoint: '/search/entity-selector',
            entityTypes: 'page,book,chapter,bookshelf',
            entityPermission: 'view',
        });
    }

    // Show draw.io if enabled and handle save.
    startDrawing() {
        const url = this.editor.config.drawioUrl;
        if (!url) return;

        const selectionRange = this.editor.input.getSelection();

        DrawIO.show(url, () => Promise.resolve(''), async pngData => {
            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            try {
                const resp = await window.$http.post('/images/drawio', data);
                this.#insertDrawing(resp.data as ImageManagerImage, selectionRange);
                DrawIO.close();
            } catch (err) {
                this.handleDrawingUploadError(err);
                throw new Error(`Failed to save image with error: ${err}`);
            }
        });
    }

    #insertDrawing(image: ImageManagerImage, originalSelectionRange: MarkdownEditorInputSelection) {
        const newText = `<div drawio-diagram="${image.id}"><img src="${image.url}"></div>`;
        this.#replaceSelection(newText, newText.length, originalSelectionRange);
    }

    // Show draw.io if enabled and handle save.
    editDrawing(imgContainer: HTMLElement) {
        const {drawioUrl} = this.editor.config;
        if (!drawioUrl) {
            return;
        }

        const selectionRange = this.editor.input.getSelection();
        const drawingId = imgContainer.getAttribute('drawio-diagram') || '';
        if (!drawingId) {
            return;
        }

        DrawIO.show(drawioUrl, () => DrawIO.load(drawingId), async pngData => {
            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            try {
                const resp = await window.$http.post('/images/drawio', data);
                const image = resp.data as ImageManagerImage;
                const newText = `<div drawio-diagram="${image.id}"><img src="${image.url}"></div>`;
                const newContent = this.editor.input.getText().split('\n').map(line => {
                    if (line.indexOf(`drawio-diagram="${drawingId}"`) !== -1) {
                        return newText;
                    }
                    return line;
                }).join('\n');
                this.editor.input.setText(newContent, selectionRange);
                DrawIO.close();
            } catch (err) {
                this.handleDrawingUploadError(err);
                throw new Error(`Failed to save image with error: ${err}`);
            }
        });
    }

    handleDrawingUploadError(error: any): void {
        if (error.status === 413) {
            window.$events.emit('error', this.editor.config.text.serverUploadLimit);
        } else {
            window.$events.emit('error', this.editor.config.text.imageUploadError);
        }
        console.error(error);
    }

    // Make the editor full screen
    fullScreen() {
        const {container} = this.editor.config;
        const alreadyFullscreen = container.classList.contains('fullscreen');
        container.classList.toggle('fullscreen', !alreadyFullscreen);
        document.body.classList.toggle('markdown-fullscreen', !alreadyFullscreen);
    }

    // Scroll to a specified text
    scrollToText(searchText: string): void {
        if (!searchText) {
            return;
        }

        const lineRange = this.editor.input.searchForLineContaining(searchText);
        if (lineRange) {
            this.editor.input.setSelection(lineRange, true);
            this.editor.input.focus();
        }
    }

    focus() {
        this.editor.input.focus();
    }

    /**
     * Insert content into the editor.
     */
    insertContent(content: string) {
        this.#replaceSelection(content, content.length);
    }

    /**
     * Prepend content to the editor.
     */
    prependContent(content: string): void {
        content = this.#cleanTextForEditor(content);
        const selectionRange = this.editor.input.getSelection();
        const selectFrom = selectionRange.from + content.length + 1;
        this.editor.input.spliceText(0, 0, `${content}\n`, {from: selectFrom});
        this.editor.input.focus();
    }

    /**
     * Append content to the editor.
     */
    appendContent(content: string): void {
        content = this.#cleanTextForEditor(content);
        this.editor.input.appendText(content);
        this.editor.input.focus();
    }

    /**
     * Replace the editor's contents
     */
    replaceContent(content: string): void {
        this.editor.input.setText(content);
    }

    /**
     * Replace the start of the line
     * @param {String} newStart
     */
    replaceLineStart(newStart: string): void {
        const selectionRange = this.editor.input.getSelection();
        const lineRange = this.editor.input.getLineRangeFromPosition(selectionRange.from);
        const lineContent = this.editor.input.getSelectionText(lineRange);
        const lineStart = lineContent.split(' ')[0];

        // Remove symbol if already set
        if (lineStart === newStart) {
            const newLineContent = lineContent.replace(`${newStart} `, '');
            const selectFrom = selectionRange.from + (newLineContent.length - lineContent.length);
            this.editor.input.spliceText(lineRange.from, lineRange.to, newLineContent, {from: selectFrom});
            return;
        }

        let newLineContent = lineContent;
        const alreadySymbol = /^[#>`]/.test(lineStart);
        if (alreadySymbol) {
            newLineContent = lineContent.replace(lineStart, newStart).trim();
        } else if (newStart !== '') {
            newLineContent = `${newStart} ${lineContent}`;
        }

        const selectFrom = selectionRange.from + (newLineContent.length - lineContent.length);
        this.editor.input.spliceText(lineRange.from, lineRange.to, newLineContent, {from: selectFrom});
    }

    /**
     * Wrap the selection in the given contents start and end contents.
     */
    wrapSelection(start: string, end: string): void {
        const selectRange = this.editor.input.getSelection();
        const selectionText = this.editor.input.getSelectionText(selectRange);
        if (!selectionText) {
            this.#wrapLine(start, end);
            return;
        }

        let newSelectionText: string;
        let newRange = {from: selectRange.from, to: selectRange.to};

        if (selectionText.startsWith(start) && selectionText.endsWith(end)) {
            newSelectionText = selectionText.slice(start.length, selectionText.length - end.length);
            newRange.to = selectRange.to - (start.length + end.length);
        } else {
            newSelectionText = `${start}${selectionText}${end}`;
            newRange.to = selectRange.to + (start.length + end.length);
        }

        this.editor.input.spliceText(
            selectRange.from,
            selectRange.to,
            newSelectionText,
            newRange,
        );
    }

    replaceLineStartForOrderedList() {
        const selectionRange = this.editor.input.getSelection();
        const lineRange = this.editor.input.getLineRangeFromPosition(selectionRange.from);
        const prevLineRange = this.editor.input.getLineRangeFromPosition(lineRange.from - 1);
        const prevLineText = this.editor.input.getSelectionText(prevLineRange);

        const listMatch = prevLineText.match(/^(\s*)(\d)([).])\s/) || [];

        const number = (Number(listMatch[2]) || 0) + 1;
        const whiteSpace = listMatch[1] || '';
        const listMark = listMatch[3] || '.';

        const prefix = `${whiteSpace}${number}${listMark}`;
        return this.replaceLineStart(prefix);
    }

    /**
     * Cycles through the type of callout block within the selection.
     * Creates a callout block if none existing, and removes it if cycling past the danger type.
     */
    cycleCalloutTypeAtSelection() {
        const selectionRange = this.editor.input.getSelection();
        const lineRange = this.editor.input.getLineRangeFromPosition(selectionRange.from);
        const lineText = this.editor.input.getSelectionText(lineRange);

        const formats = ['info', 'success', 'warning', 'danger'];
        const joint = formats.join('|');
        const regex = new RegExp(`class="((${joint})\\s+callout|callout\\s+(${joint}))"`, 'i');
        const matches = regex.exec(lineText);
        const format = (matches ? (matches[2] || matches[3]) : '').toLowerCase();

        if (format === formats[formats.length - 1]) {
            this.#wrapLine(`<p class="callout ${formats[formats.length - 1]}">`, '</p>');
        } else if (format === '') {
            this.#wrapLine('<p class="callout info">', '</p>');
        } else if (matches) {
            const newFormatIndex = formats.indexOf(format) + 1;
            const newFormat = formats[newFormatIndex];
            const newContent = lineText.replace(matches[0], matches[0].replace(format, newFormat));
            const lineDiff = newContent.length - lineText.length;
            const anchor = Math.min(selectionRange.from, selectionRange.to);
            const head = Math.max(selectionRange.from, selectionRange.to);
            this.editor.input.spliceText(
                lineRange.from,
                lineRange.to,
                newContent,
                {from: anchor + lineDiff, to: head + lineDiff}
            );
        }
    }

    syncDisplayPosition(event: Event): void {
        // Thanks to http://liuhao.im/english/2015/11/10/the-sync-scroll-of-markdown-editor-in-javascript.html
        const scrollEl = event.target as HTMLElement;
        const atEnd = Math.abs(scrollEl.scrollHeight - scrollEl.clientHeight - scrollEl.scrollTop) < 1;
        if (atEnd) {
            this.editor.display.scrollToIndex(-1);
            return;
        }

        const range = this.editor.input.getTextAboveView();
        const parser = new DOMParser();
        const doc = parser.parseFromString(this.editor.markdown.render(range), 'text/html');
        const totalLines = doc.documentElement.querySelectorAll('body > *');
        this.editor.display.scrollToIndex(totalLines.length);
    }

    /**
     * Fetch and insert the template of the given ID.
     * The page-relative position provided can be used to determine insert location if possible.
     */
    async insertTemplate(templateId: string, event: MouseEvent): Promise<void> {
        const cursorPos = this.editor.input.eventToPosition(event).from;
        const responseData = (await window.$http.get(`/templates/${templateId}`)).data as {markdown: string, html: string};
        const content = responseData.markdown || responseData.html;
        this.editor.input.spliceText(cursorPos, cursorPos, content, {from: cursorPos});
    }

    /**
     * Insert multiple images from the clipboard from an event at the provided
     * screen coordinates (Typically form a paste event).
     */
    insertClipboardImages(images: File[], event: MouseEvent): void {
        const cursorPos = this.editor.input.eventToPosition(event).from;
        for (const image of images) {
            this.uploadImage(image, cursorPos);
        }
    }

    /**
     * Handle image upload and add image into Markdown content
     */
    async uploadImage(file: File, position: number|null = null): Promise<void> {
        if (file === null || file.type.indexOf('image') !== 0) return;
        let ext = 'png';

        if (position === null) {
            position = this.editor.input.getSelection().from;
        }

        if (file.name) {
            const fileNameMatches = file.name.match(/\.(.+)$/);
            if (fileNameMatches && fileNameMatches.length > 1) {
                ext = fileNameMatches[1];
            }
        }

        // Insert image into markdown
        const id = `image-${Math.random().toString(16).slice(2)}`;
        const placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
        const placeHolderText = `![](${placeholderImage})`;
        this.editor.input.spliceText(position, position, placeHolderText, {from: position});

        const remoteFilename = `image-${Date.now()}.${ext}`;
        const formData = new FormData();
        formData.append('file', file, remoteFilename);
        formData.append('uploaded_to', this.editor.config.pageId);

        try {
            const image = (await window.$http.post('/images/gallery', formData)).data as ImageManagerImage;
            const newContent = `[![](${image.thumbs.display})](${image.url})`;
            this.#findAndReplaceContent(placeHolderText, newContent);
        } catch (err: any) {
            window.$events.error(err?.data?.message || this.editor.config.text.imageUploadError);
            this.#findAndReplaceContent(placeHolderText, '');
            console.error(err);
        }
    }

    /**
     * Replace the current selection and focus the editor.
     * Takes an offset for the cursor, after the change, relative to the start of the provided string.
     * Can be provided a selection range to use instead of the current selection range.
     */
    #replaceSelection(newContent: string, offset: number = 0, selection: MarkdownEditorInputSelection|null = null) {
        selection = selection || this.editor.input.getSelection();
        const selectFrom = selection.from + offset;
        this.editor.input.spliceText(selection.from, selection.to, newContent, {from: selectFrom, to: selectFrom});
        this.editor.input.focus();
    }

    /**
     * Cleans the given text to work with the editor.
     * Standardises line endings to what's expected.
     */
    #cleanTextForEditor(text: string): string {
        return text.replace(/\r\n|\r/g, '\n');
    }

    /**
     * Find and replace the first occurrence of [search] with [replace]
     */
    #findAndReplaceContent(search: string, replace: string): void {
        const newText = this.editor.input.getText().replace(search, replace);
        this.editor.input.setText(newText);
    }

    /**
     * Wrap the line in the given start and end contents.
     */
    #wrapLine(start: string, end: string): void {
        const selectionRange = this.editor.input.getSelection();
        const lineRange = this.editor.input.getLineRangeFromPosition(selectionRange.from);
        const lineContent = this.editor.input.getSelectionText(lineRange);
        let newLineContent: string;
        let lineOffset: number;

        if (lineContent.startsWith(start) && lineContent.endsWith(end)) {
            newLineContent = lineContent.slice(start.length, lineContent.length - end.length);
            lineOffset = -(start.length);
        } else {
            newLineContent = `${start}${lineContent}${end}`;
            lineOffset = start.length;
        }

        this.editor.input.spliceText(lineRange.from, lineRange.to, newLineContent, {from: selectionRange.from + lineOffset});
    }

}
