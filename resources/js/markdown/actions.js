import * as DrawIO from '../services/drawio';

export class Actions {

    /**
     * @param {MarkdownEditor} editor
     */
    constructor(editor) {
        this.editor = editor;
        this.lastContent = {
            html: '',
            markdown: '',
        };
    }

    updateAndRender() {
        const content = this.#getText();
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
        /** @type {ImageManager} * */
        const imageManager = window.$components.first('image-manager');

        imageManager.show(image => {
            const imageUrl = image.thumbs.display || image.url;
            const selectedText = this.#getSelectionText();
            const newText = `[![${selectedText || image.name}](${imageUrl})](${image.url})`;
            this.#replaceSelection(newText, newText.length);
        }, 'gallery');
    }

    insertImage() {
        const newText = `![${this.#getSelectionText()}](http://)`;
        this.#replaceSelection(newText, newText.length - 1);
    }

    insertLink() {
        const selectedText = this.#getSelectionText();
        const newText = `[${selectedText}]()`;
        const cursorPosDiff = (selectedText === '') ? -3 : -1;
        this.#replaceSelection(newText, newText.length + cursorPosDiff);
    }

    showImageManager() {
        const selectionRange = this.#getSelectionRange();
        /** @type {ImageManager} * */
        const imageManager = window.$components.first('image-manager');
        imageManager.show(image => {
            this.#insertDrawing(image, selectionRange);
        }, 'drawio');
    }

    // Show the popup link selector and insert a link when finished
    showLinkSelector() {
        const selectionRange = this.#getSelectionRange();

        /** @type {EntitySelectorPopup} * */
        const selector = window.$components.first('entity-selector-popup');
        selector.show(entity => {
            const selectedText = this.#getSelectionText(selectionRange) || entity.name;
            const newText = `[${selectedText}](${entity.link})`;
            this.#replaceSelection(newText, newText.length, selectionRange);
        });
    }

    // Show draw.io if enabled and handle save.
    startDrawing() {
        const url = this.editor.config.drawioUrl;
        if (!url) return;

        const selectionRange = this.#getSelectionRange();

        DrawIO.show(url, () => Promise.resolve(''), pngData => {
            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            window.$http.post('/images/drawio', data).then(resp => {
                this.#insertDrawing(resp.data, selectionRange);
                DrawIO.close();
            }).catch(err => {
                this.handleDrawingUploadError(err);
            });
        });
    }

    #insertDrawing(image, originalSelectionRange) {
        const newText = `<div drawio-diagram="${image.id}"><img src="${image.url}"></div>`;
        this.#replaceSelection(newText, newText.length, originalSelectionRange);
    }

    // Show draw.io if enabled and handle save.
    editDrawing(imgContainer) {
        const {drawioUrl} = this.editor.config;
        if (!drawioUrl) {
            return;
        }

        const selectionRange = this.#getSelectionRange();
        const drawingId = imgContainer.getAttribute('drawio-diagram');

        DrawIO.show(drawioUrl, () => DrawIO.load(drawingId), pngData => {
            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            window.$http.post('/images/drawio', data).then(resp => {
                const newText = `<div drawio-diagram="${resp.data.id}"><img src="${resp.data.url}"></div>`;
                const newContent = this.#getText().split('\n').map(line => {
                    if (line.indexOf(`drawio-diagram="${drawingId}"`) !== -1) {
                        return newText;
                    }
                    return line;
                }).join('\n');
                this.#setText(newContent, selectionRange);
                DrawIO.close();
            }).catch(err => {
                this.handleDrawingUploadError(err);
            });
        });
    }

    handleDrawingUploadError(error) {
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
    scrollToText(searchText) {
        if (!searchText) {
            return;
        }

        const text = this.editor.cm.state.doc;
        let lineCount = 1;
        let scrollToLine = -1;
        for (const line of text.iterLines()) {
            if (line.includes(searchText)) {
                scrollToLine = lineCount;
                break;
            }
            lineCount += 1;
        }

        if (scrollToLine === -1) {
            return;
        }

        const line = text.line(scrollToLine);
        this.#setSelection(line.from, line.to, true);
        this.focus();
    }

    focus() {
        if (!this.editor.cm.hasFocus) {
            this.editor.cm.focus();
        }
    }

    /**
     * Insert content into the editor.
     * @param {String} content
     */
    insertContent(content) {
        this.#replaceSelection(content, content.length);
    }

    /**
     * Prepend content to the editor.
     * @param {String} content
     */
    prependContent(content) {
        content = this.#cleanTextForEditor(content);
        const selectionRange = this.#getSelectionRange();
        const selectFrom = selectionRange.from + content.length + 1;
        this.#dispatchChange(0, 0, `${content}\n`, selectFrom);
        this.focus();
    }

    /**
     * Append content to the editor.
     * @param {String} content
     */
    appendContent(content) {
        content = this.#cleanTextForEditor(content);
        this.#dispatchChange(this.editor.cm.state.doc.length, `\n${content}`);
        this.focus();
    }

    /**
     * Replace the editor's contents
     * @param {String} content
     */
    replaceContent(content) {
        this.#setText(content);
    }

    /**
     * Replace the start of the line
     * @param {String} newStart
     */
    replaceLineStart(newStart) {
        const selectionRange = this.#getSelectionRange();
        const line = this.editor.cm.state.doc.lineAt(selectionRange.from);

        const lineContent = line.text;
        const lineStart = lineContent.split(' ')[0];

        // Remove symbol if already set
        if (lineStart === newStart) {
            const newLineContent = lineContent.replace(`${newStart} `, '');
            const selectFrom = selectionRange.from + (newLineContent.length - lineContent.length);
            this.#dispatchChange(line.from, line.to, newLineContent, selectFrom);
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
        this.#dispatchChange(line.from, line.to, newLineContent, selectFrom);
    }

    /**
     * Wrap the selection in the given contents start and end contents.
     * @param {String} start
     * @param {String} end
     */
    wrapSelection(start, end) {
        const selectRange = this.#getSelectionRange();
        const selectionText = this.#getSelectionText(selectRange);
        if (!selectionText) {
            this.#wrapLine(start, end);
            return;
        }

        let newSelectionText = selectionText;
        let newRange;

        if (selectionText.startsWith(start) && selectionText.endsWith(end)) {
            newSelectionText = selectionText.slice(start.length, selectionText.length - end.length);
            newRange = selectRange.extend(selectRange.from, selectRange.to - (start.length + end.length));
        } else {
            newSelectionText = `${start}${selectionText}${end}`;
            newRange = selectRange.extend(selectRange.from, selectRange.to + (start.length + end.length));
        }

        this.#dispatchChange(
            selectRange.from,
            selectRange.to,
            newSelectionText,
            newRange.anchor,
            newRange.head,
        );
    }

    replaceLineStartForOrderedList() {
        const selectionRange = this.#getSelectionRange();
        const line = this.editor.cm.state.doc.lineAt(selectionRange.from);
        const prevLine = this.editor.cm.state.doc.line(line.number - 1);

        const listMatch = prevLine.text.match(/^(\s*)(\d)([).])\s/) || [];

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
        const selectionRange = this.#getSelectionRange();
        const line = this.editor.cm.state.doc.lineAt(selectionRange.from);

        const formats = ['info', 'success', 'warning', 'danger'];
        const joint = formats.join('|');
        const regex = new RegExp(`class="((${joint})\\s+callout|callout\\s+(${joint}))"`, 'i');
        const matches = regex.exec(line.text);
        const format = (matches ? (matches[2] || matches[3]) : '').toLowerCase();

        if (format === formats[formats.length - 1]) {
            this.#wrapLine(`<p class="callout ${formats[formats.length - 1]}">`, '</p>');
        } else if (format === '') {
            this.#wrapLine('<p class="callout info">', '</p>');
        } else {
            const newFormatIndex = formats.indexOf(format) + 1;
            const newFormat = formats[newFormatIndex];
            const newContent = line.text.replace(matches[0], matches[0].replace(format, newFormat));
            const lineDiff = newContent.length - line.text.length;
            this.#dispatchChange(
                line.from,
                line.to,
                newContent,
                selectionRange.anchor + lineDiff,
                selectionRange.head + lineDiff,
            );
        }
    }

    syncDisplayPosition(event) {
        // Thanks to http://liuhao.im/english/2015/11/10/the-sync-scroll-of-markdown-editor-in-javascript.html
        const scrollEl = event.target;
        const atEnd = Math.abs(scrollEl.scrollHeight - scrollEl.clientHeight - scrollEl.scrollTop) < 1;
        if (atEnd) {
            this.editor.display.scrollToIndex(-1);
            return;
        }

        const blockInfo = this.editor.cm.lineBlockAtHeight(scrollEl.scrollTop);
        const range = this.editor.cm.state.sliceDoc(0, blockInfo.from);
        const parser = new DOMParser();
        const doc = parser.parseFromString(this.editor.markdown.render(range), 'text/html');
        const totalLines = doc.documentElement.querySelectorAll('body > *');
        this.editor.display.scrollToIndex(totalLines.length);
    }

    /**
     * Fetch and insert the template of the given ID.
     * The page-relative position provided can be used to determine insert location if possible.
     * @param {String} templateId
     * @param {Number} posX
     * @param {Number} posY
     */
    async insertTemplate(templateId, posX, posY) {
        const cursorPos = this.editor.cm.posAtCoords({x: posX, y: posY}, false);
        const {data} = await window.$http.get(`/templates/${templateId}`);
        const content = data.markdown || data.html;
        this.#dispatchChange(cursorPos, cursorPos, content, cursorPos);
    }

    /**
     * Insert multiple images from the clipboard from an event at the provided
     * screen coordinates (Typically form a paste event).
     * @param {File[]} images
     * @param {Number} posX
     * @param {Number} posY
     */
    insertClipboardImages(images, posX, posY) {
        const cursorPos = this.editor.cm.posAtCoords({x: posX, y: posY}, false);
        for (const image of images) {
            this.uploadImage(image, cursorPos);
        }
    }

    /**
     * Handle image upload and add image into markdown content
     * @param {File} file
     * @param {?Number} position
     */
    async uploadImage(file, position = null) {
        if (file === null || file.type.indexOf('image') !== 0) return;
        let ext = 'png';

        if (position === null) {
            position = this.#getSelectionRange().from;
        }

        if (file.name) {
            const fileNameMatches = file.name.match(/\.(.+)$/);
            if (fileNameMatches.length > 1) ext = fileNameMatches[1];
        }

        // Insert image into markdown
        const id = `image-${Math.random().toString(16).slice(2)}`;
        const placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
        const placeHolderText = `![](${placeholderImage})`;
        this.#dispatchChange(position, position, placeHolderText, position);

        const remoteFilename = `image-${Date.now()}.${ext}`;
        const formData = new FormData();
        formData.append('file', file, remoteFilename);
        formData.append('uploaded_to', this.editor.config.pageId);

        try {
            const {data} = await window.$http.post('/images/gallery', formData);
            const newContent = `[![](${data.thumbs.display})](${data.url})`;
            this.#findAndReplaceContent(placeHolderText, newContent);
        } catch (err) {
            window.$events.emit('error', this.editor.config.text.imageUploadError);
            this.#findAndReplaceContent(placeHolderText, '');
            console.error(err);
        }
    }

    /**
     * Get the current text of the editor instance.
     * @return {string}
     */
    #getText() {
        return this.editor.cm.state.doc.toString();
    }

    /**
     * Set the text of the current editor instance.
     * @param {String} text
     * @param {?SelectionRange} selectionRange
     */
    #setText(text, selectionRange = null) {
        selectionRange = selectionRange || this.#getSelectionRange();
        this.#dispatchChange(0, this.editor.cm.state.doc.length, text, selectionRange.from);
        this.focus();
    }

    /**
     * Replace the current selection and focus the editor.
     * Takes an offset for the cursor, after the change, relative to the start of the provided string.
     * Can be provided a selection range to use instead of the current selection range.
     * @param {String} newContent
     * @param {Number} cursorOffset
     * @param {?SelectionRange} selectionRange
     */
    #replaceSelection(newContent, cursorOffset = 0, selectionRange = null) {
        selectionRange = selectionRange || this.editor.cm.state.selection.main;
        const selectFrom = selectionRange.from + cursorOffset;
        this.#dispatchChange(selectionRange.from, selectionRange.to, newContent, selectFrom);
        this.focus();
    }

    /**
     * Get the text content of the main current selection.
     * @param {SelectionRange} selectionRange
     * @return {string}
     */
    #getSelectionText(selectionRange = null) {
        selectionRange = selectionRange || this.#getSelectionRange();
        return this.editor.cm.state.sliceDoc(selectionRange.from, selectionRange.to);
    }

    /**
     * Get the range of the current main selection.
     * @return {SelectionRange}
     */
    #getSelectionRange() {
        return this.editor.cm.state.selection.main;
    }

    /**
     * Cleans the given text to work with the editor.
     * Standardises line endings to what's expected.
     * @param {String} text
     * @return {String}
     */
    #cleanTextForEditor(text) {
        return text.replace(/\r\n|\r/g, '\n');
    }

    /**
     * Find and replace the first occurrence of [search] with [replace]
     * @param {String} search
     * @param {String} replace
     */
    #findAndReplaceContent(search, replace) {
        const newText = this.#getText().replace(search, replace);
        this.#setText(newText);
    }

    /**
     * Wrap the line in the given start and end contents.
     * @param {String} start
     * @param {String} end
     */
    #wrapLine(start, end) {
        const selectionRange = this.#getSelectionRange();
        const line = this.editor.cm.state.doc.lineAt(selectionRange.from);
        const lineContent = line.text;
        let newLineContent;
        let lineOffset = 0;

        if (lineContent.startsWith(start) && lineContent.endsWith(end)) {
            newLineContent = lineContent.slice(start.length, lineContent.length - end.length);
            lineOffset = -(start.length);
        } else {
            newLineContent = `${start}${lineContent}${end}`;
            lineOffset = start.length;
        }

        this.#dispatchChange(line.from, line.to, newLineContent, selectionRange.from + lineOffset);
    }

    /**
     * Dispatch changes to the editor.
     * @param {Number} from
     * @param {?Number} to
     * @param {?String} text
     * @param {?Number} selectFrom
     * @param {?Number} selectTo
     */
    #dispatchChange(from, to = null, text = null, selectFrom = null, selectTo = null) {
        const tr = {changes: {from, to, insert: text}};

        if (selectFrom) {
            tr.selection = {anchor: selectFrom};
            if (selectTo) {
                tr.selection.head = selectTo;
            }
        }

        this.editor.cm.dispatch(tr);
    }

    /**
     * Set the current selection range.
     * Optionally will scroll the new range into view.
     * @param {Number} from
     * @param {Number} to
     * @param {Boolean} scrollIntoView
     */
    #setSelection(from, to, scrollIntoView = false) {
        this.editor.cm.dispatch({
            selection: {anchor: from, head: to},
            scrollIntoView,
        });
    }

}
