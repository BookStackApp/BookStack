import DrawIO from "../services/drawio";

export class Actions {
    /**
     * @param {MarkdownEditor} editor
     */
    constructor(editor) {
        this.editor = editor;
    }

    updateAndRender() {
        const content = this.editor.cm.getValue();
        this.editor.config.inputEl.value = content;

        const html = this.editor.markdown.render(content);
        window.$events.emit('editor-html-change', html);
        window.$events.emit('editor-markdown-change', content);
        this.editor.display.patchWithHtml(html);
    }

    insertImage() {
        const cursorPos = this.editor.cm.getCursor('from');
        /** @type {ImageManager} **/
        const imageManager = window.$components.first('image-manager');
        imageManager.show(image => {
            const imageUrl = image.thumbs.display || image.url;
            let selectedText = this.editor.cm.getSelection();
            let newText = "[![" + (selectedText || image.name) + "](" + imageUrl + ")](" + image.url + ")";
            this.editor.cm.focus();
            this.editor.cm.replaceSelection(newText);
            this.editor.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        }, 'gallery');
    }

    insertLink() {
        const cursorPos = this.editor.cm.getCursor('from');
        const selectedText = this.editor.cm.getSelection() || '';
        const newText = `[${selectedText}]()`;
        this.editor.cm.focus();
        this.editor.cm.replaceSelection(newText);
        const cursorPosDiff = (selectedText === '') ? -3 : -1;
        this.editor.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length+cursorPosDiff);
    }

    showImageManager() {
        const cursorPos = this.editor.cm.getCursor('from');
        /** @type {ImageManager} **/
        const imageManager = window.$components.first('image-manager');
        imageManager.show(image => {
            this.insertDrawing(image, cursorPos);
        }, 'drawio');
    }

    // Show the popup link selector and insert a link when finished
    showLinkSelector() {
        const cursorPos = this.editor.cm.getCursor('from');
        /** @type {EntitySelectorPopup} **/
        const selector = window.$components.first('entity-selector-popup');
        selector.show(entity => {
            let selectedText = this.editor.cm.getSelection() || entity.name;
            let newText = `[${selectedText}](${entity.link})`;
            this.editor.cm.focus();
            this.editor.cm.replaceSelection(newText);
            this.editor.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        });
    }

    // Show draw.io if enabled and handle save.
    startDrawing() {
        const url = this.editor.config.drawioUrl;
        if (!url) return;

        const cursorPos = this.editor.cm.getCursor('from');

        DrawIO.show(url,() => {
            return Promise.resolve('');
        }, (pngData) => {

            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            window.$http.post("/images/drawio", data).then(resp => {
                this.insertDrawing(resp.data, cursorPos);
                DrawIO.close();
            }).catch(err => {
                this.handleDrawingUploadError(err);
            });
        });
    }

    insertDrawing(image, originalCursor) {
        const newText = `<div drawio-diagram="${image.id}"><img src="${image.url}"></div>`;
        this.editor.cm.focus();
        this.editor.cm.replaceSelection(newText);
        this.editor.cm.setCursor(originalCursor.line, originalCursor.ch + newText.length);
    }

    // Show draw.io if enabled and handle save.
    editDrawing(imgContainer) {
        const drawioUrl = this.editor.config.drawioUrl;
        if (!drawioUrl) {
            return;
        }

        const cursorPos = this.editor.cm.getCursor('from');
        const drawingId = imgContainer.getAttribute('drawio-diagram');

        DrawIO.show(drawioUrl, () => {
            return DrawIO.load(drawingId);
        }, (pngData) => {

            const data = {
                image: pngData,
                uploaded_to: Number(this.editor.config.pageId),
            };

            window.$http.post("/images/drawio", data).then(resp => {
                const newText = `<div drawio-diagram="${resp.data.id}"><img src="${resp.data.url}"></div>`;
                const newContent = this.editor.cm.getValue().split('\n').map(line => {
                    if (line.indexOf(`drawio-diagram="${drawingId}"`) !== -1) {
                        return newText;
                    }
                    return line;
                }).join('\n');
                this.editor.cm.setValue(newContent);
                this.editor.cm.setCursor(cursorPos);
                this.editor.cm.focus();
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
        console.log(error);
    }

    // Make the editor full screen
    fullScreen() {
        const container = this.editor.config.container;
        const alreadyFullscreen = container.classList.contains('fullscreen');
        container.classList.toggle('fullscreen', !alreadyFullscreen);
        document.body.classList.toggle('markdown-fullscreen', !alreadyFullscreen);
    }

    // Scroll to a specified text
    scrollToText(searchText) {
        if (!searchText) {
            return;
        }

        const content = this.editor.cm.getValue();
        const lines = content.split(/\r?\n/);
        let lineNumber = lines.findIndex(line => {
            return line && line.indexOf(searchText) !== -1;
        });

        if (lineNumber === -1) {
            return;
        }

        this.editor.cm.scrollIntoView({
            line: lineNumber,
        }, 200);
        this.editor.cm.focus();
        // set the cursor location.
        this.editor.cm.setCursor({
            line: lineNumber,
            char: lines[lineNumber].length
        })
    }

    focus() {
        this.editor.cm.focus();
    }

    /**
     * Insert content into the editor.
     * @param {String} content
     */
    insertContent(content) {
        this.editor.cm.replaceSelection(content);
    }

    /**
     * Prepend content to the editor.
     * @param {String} content
     */
    prependContent(content) {
        const cursorPos = this.editor.cm.getCursor('from');
        const newContent = content + '\n' + this.editor.cm.getValue();
        this.editor.cm.setValue(newContent);
        const prependLineCount = content.split('\n').length;
        this.editor.cm.setCursor(cursorPos.line + prependLineCount, cursorPos.ch);
    }

    /**
     * Append content to the editor.
     * @param {String} content
     */
    appendContent(content) {
        const cursorPos = this.editor.cm.getCursor('from');
        const newContent = this.editor.cm.getValue() + '\n' + content;
        this.editor.cm.setValue(newContent);
        this.editor.cm.setCursor(cursorPos.line, cursorPos.ch);
    }

    /**
     * Replace the editor's contents
     * @param {String} content
     */
    replaceContent(content) {
        this.editor.cm.setValue(content);
    }

    /**
     * @param {String|RegExp} search
     * @param {String} replace
     */
    findAndReplaceContent(search, replace) {
        const text = this.editor.cm.getValue();
        const cursor = this.editor.cm.listSelections();
        this.editor.cm.setValue(text.replace(search, replace));
        this.editor.cm.setSelections(cursor);
    }

    /**
     * Replace the start of the line
     * @param {String} newStart
     */
    replaceLineStart(newStart) {
        const cursor = this.editor.cm.getCursor();
        let lineContent = this.editor.cm.getLine(cursor.line);
        const lineLen = lineContent.length;
        const lineStart = lineContent.split(' ')[0];

        // Remove symbol if already set
        if (lineStart === newStart) {
            lineContent = lineContent.replace(`${newStart} `, '');
            this.editor.cm.replaceRange(lineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
            this.editor.cm.setCursor({line: cursor.line, ch: cursor.ch - (newStart.length + 1)});
            return;
        }

        const alreadySymbol = /^[#>`]/.test(lineStart);
        let posDif = 0;
        if (alreadySymbol) {
            posDif = newStart.length - lineStart.length;
            lineContent = lineContent.replace(lineStart, newStart).trim();
        } else if (newStart !== '') {
            posDif = newStart.length + 1;
            lineContent = newStart + ' ' + lineContent;
        }
        this.editor.cm.replaceRange(lineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
        this.editor.cm.setCursor({line: cursor.line, ch: cursor.ch + posDif});
    }

    /**
     * Wrap the line in the given start and end contents.
     * @param {String} start
     * @param {String} end
     */
    wrapLine(start, end) {
        const cursor = this.editor.cm.getCursor();
        const lineContent = this.editor.cm.getLine(cursor.line);
        const lineLen = lineContent.length;
        let newLineContent = lineContent;

        if (lineContent.indexOf(start) === 0 && lineContent.slice(-end.length) === end) {
            newLineContent = lineContent.slice(start.length, lineContent.length - end.length);
        } else {
            newLineContent = `${start}${lineContent}${end}`;
        }

        this.editor.cm.replaceRange(newLineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
        this.editor.cm.setCursor({line: cursor.line, ch: cursor.ch + start.length});
    }

    /**
     * Wrap the selection in the given contents start and end contents.
     * @param {String} start
     * @param {String} end
     */
    wrapSelection(start, end) {
        const selection = this.editor.cm.getSelection();
        if (selection === '') return this.wrapLine(start, end);

        let newSelection = selection;
        const frontDiff = 0;
        let endDiff;

        if (selection.indexOf(start) === 0 && selection.slice(-end.length) === end) {
            newSelection = selection.slice(start.length, selection.length - end.length);
            endDiff = -(end.length + start.length);
        } else {
            newSelection = `${start}${selection}${end}`;
            endDiff = start.length + end.length;
        }

        const selections = this.editor.cm.listSelections()[0];
        this.editor.cm.replaceSelection(newSelection);
        const headFirst = selections.head.ch <= selections.anchor.ch;
        selections.head.ch += headFirst ? frontDiff : endDiff;
        selections.anchor.ch += headFirst ? endDiff : frontDiff;
        this.editor.cm.setSelections([selections]);
    }

    replaceLineStartForOrderedList() {
        const cursor = this.editor.cm.getCursor();
        const prevLineContent = this.editor.cm.getLine(cursor.line - 1) || '';
        const listMatch = prevLineContent.match(/^(\s*)(\d)([).])\s/) || [];

        const number = (Number(listMatch[2]) || 0) + 1;
        const whiteSpace = listMatch[1] || '';
        const listMark = listMatch[3] || '.'

        const prefix = `${whiteSpace}${number}${listMark}`;
        return this.replaceLineStart(prefix);
    }

    /**
     * Handle image upload and add image into markdown content
     * @param {File} file
     */
    uploadImage(file) {
        if (file === null || file.type.indexOf('image') !== 0) return;
        let ext = 'png';

        if (file.name) {
            let fileNameMatches = file.name.match(/\.(.+)$/);
            if (fileNameMatches.length > 1) ext = fileNameMatches[1];
        }

        // Insert image into markdown
        const id = "image-" + Math.random().toString(16).slice(2);
        const placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
        const selectedText = this.editor.cm.getSelection();
        const placeHolderText = `![${selectedText}](${placeholderImage})`;
        const cursor = this.editor.cm.getCursor();
        this.editor.cm.replaceSelection(placeHolderText);
        this.editor.cm.setCursor({line: cursor.line, ch: cursor.ch + selectedText.length + 3});

        const remoteFilename = "image-" + Date.now() + "." + ext;
        const formData = new FormData();
        formData.append('file', file, remoteFilename);
        formData.append('uploaded_to', this.editor.config.pageId);

        window.$http.post('/images/gallery', formData).then(resp => {
            const newContent = `[![${selectedText}](${resp.data.thumbs.display})](${resp.data.url})`;
            this.findAndReplaceContent(placeHolderText, newContent);
        }).catch(err => {
            window.$events.emit('error', this.editor.config.text.imageUploadError);
            this.findAndReplaceContent(placeHolderText, selectedText);
            console.log(err);
        });
    }

    syncDisplayPosition() {
        // Thanks to http://liuhao.im/english/2015/11/10/the-sync-scroll-of-markdown-editor-in-javascript.html
        const scroll = this.editor.cm.getScrollInfo();
        const atEnd = scroll.top + scroll.clientHeight === scroll.height;
        if (atEnd) {
            this.editor.display.scrollToIndex(-1);
            return;
        }

        const lineNum = this.editor.cm.lineAtHeight(scroll.top, 'local');
        const range = this.editor.cm.getRange({line: 0, ch: null}, {line: lineNum, ch: null});
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
    insertTemplate(templateId, posX, posY) {
        const cursorPos = this.editor.cm.coordsChar({left: posX, top: posY});
        this.editor.cm.setCursor(cursorPos);
        window.$http.get(`/templates/${templateId}`).then(resp => {
            const content = resp.data.markdown || resp.data.html;
            this.editor.cm.replaceSelection(content);
        });
    }

    /**
     * Insert multiple images from the clipboard.
     * @param {File[]} images
     */
    insertClipboardImages(images) {
        const cursorPos = this.editor.cm.coordsChar({left: event.pageX, top: event.pageY});
        this.editor.cm.setCursor(cursorPos);
        for (const image of images) {
            this.editor.actions.uploadImage(image);
        }
    }
}