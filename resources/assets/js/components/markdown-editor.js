const MarkdownIt = require("markdown-it");
const mdTasksLists = require('markdown-it-task-lists');
const code = require('../services/code');

const DrawIO = require('../services/drawio');

class MarkdownEditor {

    constructor(elem) {
        this.elem = elem;
        this.markdown = new MarkdownIt({html: true});
        this.markdown.use(mdTasksLists, {label: true});

        this.display = this.elem.querySelector('.markdown-display');
        this.input = this.elem.querySelector('textarea');
        this.htmlInput = this.elem.querySelector('input[name=html]');
        this.cm = code.markdownEditor(this.input);

        this.onMarkdownScroll = this.onMarkdownScroll.bind(this);
        this.init();

        // Scroll to text if needed.
        const queryParams = (new URL(window.location)).searchParams;
        const scrollText = queryParams.get('content-text');
        if (scrollText) {
            this.scrollToText(scrollText);
        }
    }

    init() {

        let lastClick = 0;

        // Prevent markdown display link click redirect
        this.display.addEventListener('click', event => {
            let isDblClick = Date.now() - lastClick < 300;

            let link = event.target.closest('a');
            if (link !== null) {
                event.preventDefault();
                window.open(link.getAttribute('href'));
                return;
            }

            let drawing = event.target.closest('[drawio-diagram]');
            if (drawing !== null && isDblClick) {
                this.actionEditDrawing(drawing);
                return;
            }

            lastClick = Date.now();
        });

        // Button actions
        this.elem.addEventListener('click', event => {
            let button = event.target.closest('button[data-action]');
            if (button === null) return;

            let action = button.getAttribute('data-action');
            if (action === 'insertImage') this.actionInsertImage();
            if (action === 'insertLink') this.actionShowLinkSelector();
            if (action === 'insertDrawing' && event.ctrlKey) {
                this.actionShowImageManager();
                return;
            }
            if (action === 'insertDrawing') this.actionStartDrawing();
        });

        window.$events.listen('editor-markdown-update', value => {
            this.cm.setValue(value);
            this.updateAndRender();
        });

        this.codeMirrorSetup();
    }

    // Update the input content and render the display.
    updateAndRender() {
        let content = this.cm.getValue();
        this.input.value = content;
        let html = this.markdown.render(content);
        window.$events.emit('editor-html-change', html);
        window.$events.emit('editor-markdown-change', content);
        this.display.innerHTML = html;
        this.htmlInput.value = html;
    }

    onMarkdownScroll(lineCount) {
        let elems = this.display.children;
        if (elems.length <= lineCount) return;

        let topElem = (lineCount === -1) ? elems[elems.length-1] : elems[lineCount];
        // TODO - Replace jQuery
        $(this.display).animate({
            scrollTop: topElem.offsetTop
        }, {queue: false, duration: 200, easing: 'linear'});
    }

    codeMirrorSetup() {
        let cm = this.cm;
        // Custom key commands
        let metaKey = code.getMetaKey();
        const extraKeys = {};
        // Insert Image shortcut
        extraKeys[`${metaKey}-Alt-I`] = function(cm) {
            let selectedText = cm.getSelection();
            let newText = `![${selectedText}](http://)`;
            let cursorPos = cm.getCursor('from');
            cm.replaceSelection(newText);
            cm.setCursor(cursorPos.line, cursorPos.ch + newText.length -1);
        };
        // Save draft
        extraKeys[`${metaKey}-S`] = cm => {window.$events.emit('editor-save-draft')};
        // Save page
        extraKeys[`${metaKey}-Enter`] = cm => {window.$events.emit('editor-save-page')};
        // Show link selector
        extraKeys[`Shift-${metaKey}-K`] = cm => {this.actionShowLinkSelector()};
        // Insert Link
        extraKeys[`${metaKey}-K`] = cm => {insertLink()};
        // FormatShortcuts
        extraKeys[`${metaKey}-1`] = cm => {replaceLineStart('##');};
        extraKeys[`${metaKey}-2`] = cm => {replaceLineStart('###');};
        extraKeys[`${metaKey}-3`] = cm => {replaceLineStart('####');};
        extraKeys[`${metaKey}-4`] = cm => {replaceLineStart('#####');};
        extraKeys[`${metaKey}-5`] = cm => {replaceLineStart('');};
        extraKeys[`${metaKey}-d`] = cm => {replaceLineStart('');};
        extraKeys[`${metaKey}-6`] = cm => {replaceLineStart('>');};
        extraKeys[`${metaKey}-q`] = cm => {replaceLineStart('>');};
        extraKeys[`${metaKey}-7`] = cm => {wrapSelection('\n```\n', '\n```');};
        extraKeys[`${metaKey}-8`] = cm => {wrapSelection('`', '`');};
        extraKeys[`Shift-${metaKey}-E`] = cm => {wrapSelection('`', '`');};
        extraKeys[`${metaKey}-9`] = cm => {wrapSelection('<p class="callout info">', '</p>');};
        cm.setOption('extraKeys', extraKeys);

        // Update data on content change
        cm.on('change', (instance, changeObj) => {
            this.updateAndRender();
        });

        // Handle scroll to sync display view
        cm.on('scroll', instance => {
            // Thanks to http://liuhao.im/english/2015/11/10/the-sync-scroll-of-markdown-editor-in-javascript.html
            let scroll = instance.getScrollInfo();
            let atEnd = scroll.top + scroll.clientHeight === scroll.height;
            if (atEnd) {
                this.onMarkdownScroll(-1);
                return;
            }

            let lineNum = instance.lineAtHeight(scroll.top, 'local');
            let range = instance.getRange({line: 0, ch: null}, {line: lineNum, ch: null});
            let parser = new DOMParser();
            let doc = parser.parseFromString(this.markdown.render(range), 'text/html');
            let totalLines = doc.documentElement.querySelectorAll('body > *');
            this.onMarkdownScroll(totalLines.length);
        });

        // Handle image paste
        cm.on('paste', (cm, event) => {
            if (!event.clipboardData || !event.clipboardData.items) return;
            for (let i = 0; i < event.clipboardData.items.length; i++) {
                uploadImage(event.clipboardData.items[i].getAsFile());
            }
        });

        // Handle images on drag-drop
        cm.on('drop', (cm, event) => {
            event.stopPropagation();
            event.preventDefault();
            let cursorPos = cm.coordsChar({left: event.pageX, top: event.pageY});
            cm.setCursor(cursorPos);
            if (!event.dataTransfer || !event.dataTransfer.files) return;
            for (let i = 0; i < event.dataTransfer.files.length; i++) {
                uploadImage(event.dataTransfer.files[i]);
            }
        });

        // Helper to replace editor content
        function replaceContent(search, replace) {
            let text = cm.getValue();
            let cursor = cm.listSelections();
            cm.setValue(text.replace(search, replace));
            cm.setSelections(cursor);
        }

        // Helper to replace the start of the line
        function replaceLineStart(newStart) {
            let cursor = cm.getCursor();
            let lineContent = cm.getLine(cursor.line);
            let lineLen = lineContent.length;
            let lineStart = lineContent.split(' ')[0];

            // Remove symbol if already set
            if (lineStart === newStart) {
                lineContent = lineContent.replace(`${newStart} `, '');
                cm.replaceRange(lineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
                cm.setCursor({line: cursor.line, ch: cursor.ch - (newStart.length + 1)});
                return;
            }

            let alreadySymbol = /^[#>`]/.test(lineStart);
            let posDif = 0;
            if (alreadySymbol) {
                posDif = newStart.length - lineStart.length;
                lineContent = lineContent.replace(lineStart, newStart).trim();
            } else if (newStart !== '') {
                posDif = newStart.length + 1;
                lineContent = newStart + ' ' + lineContent;
            }
            cm.replaceRange(lineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
            cm.setCursor({line: cursor.line, ch: cursor.ch + posDif});
        }

        function wrapLine(start, end) {
            let cursor = cm.getCursor();
            let lineContent = cm.getLine(cursor.line);
            let lineLen = lineContent.length;
            let newLineContent = lineContent;

            if (lineContent.indexOf(start) === 0 && lineContent.slice(-end.length) === end) {
                newLineContent = lineContent.slice(start.length, lineContent.length - end.length);
            } else {
                newLineContent = `${start}${lineContent}${end}`;
            }

            cm.replaceRange(newLineContent, {line: cursor.line, ch: 0}, {line: cursor.line, ch: lineLen});
            cm.setCursor({line: cursor.line, ch: cursor.ch + start.length});
        }

        function wrapSelection(start, end) {
            let selection = cm.getSelection();
            if (selection === '') return wrapLine(start, end);

            let newSelection = selection;
            let frontDiff = 0;
            let endDiff = 0;

            if (selection.indexOf(start) === 0 && selection.slice(-end.length) === end) {
                newSelection = selection.slice(start.length, selection.length - end.length);
                endDiff = -(end.length + start.length);
            } else {
                newSelection = `${start}${selection}${end}`;
                endDiff = start.length + end.length;
            }

            let selections = cm.listSelections()[0];
            cm.replaceSelection(newSelection);
            let headFirst = selections.head.ch <= selections.anchor.ch;
            selections.head.ch += headFirst ? frontDiff : endDiff;
            selections.anchor.ch += headFirst ? endDiff : frontDiff;
            cm.setSelections([selections]);
        }

        // Handle image upload and add image into markdown content
        function uploadImage(file) {
            if (file === null || file.type.indexOf('image') !== 0) return;
            let ext = 'png';

            if (file.name) {
                let fileNameMatches = file.name.match(/\.(.+)$/);
                if (fileNameMatches.length > 1) ext = fileNameMatches[1];
            }

            // Insert image into markdown
            let id = "image-" + Math.random().toString(16).slice(2);
            let placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
            let selectedText = cm.getSelection();
            let placeHolderText = `![${selectedText}](${placeholderImage})`;
            let cursor = cm.getCursor();
            cm.replaceSelection(placeHolderText);
            cm.setCursor({line: cursor.line, ch: cursor.ch + selectedText.length + 2});

            let remoteFilename = "image-" + Date.now() + "." + ext;
            let formData = new FormData();
            formData.append('file', file, remoteFilename);

            window.$http.post('/images/gallery/upload', formData).then(resp => {
                replaceContent(placeholderImage, resp.data.thumbs.display);
            }).catch(err => {
                window.$events.emit('error', trans('errors.image_upload_error'));
                replaceContent(placeHolderText, selectedText);
                console.log(err);
            });
        }

        function insertLink() {
            let cursorPos = cm.getCursor('from');
            let selectedText = cm.getSelection() || '';
            let newText = `[${selectedText}]()`;
            cm.focus();
            cm.replaceSelection(newText);
            let cursorPosDiff = (selectedText === '') ? -3 : -1;
            cm.setCursor(cursorPos.line, cursorPos.ch + newText.length+cursorPosDiff);
        }

       this.updateAndRender();
    }

    actionInsertImage() {
        let cursorPos = this.cm.getCursor('from');
        window.ImageManager.show(image => {
            let selectedText = this.cm.getSelection();
            let newText = "![" + (selectedText || image.name) + "](" + image.thumbs.display + ")";
            this.cm.focus();
            this.cm.replaceSelection(newText);
            this.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        }, 'gallery');
    }

    actionShowImageManager() {
        let cursorPos = this.cm.getCursor('from');
        window.ImageManager.show(image => {
            this.insertDrawing(image, cursorPos);
        }, 'drawio');
    }

    // Show the popup link selector and insert a link when finished
    actionShowLinkSelector() {
        let cursorPos = this.cm.getCursor('from');
        window.EntitySelectorPopup.show(entity => {
            let selectedText = this.cm.getSelection() || entity.name;
            let newText = `[${selectedText}](${entity.link})`;
            this.cm.focus();
            this.cm.replaceSelection(newText);
            this.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        });
    }

    // Show draw.io if enabled and handle save.
    actionStartDrawing() {
        if (document.querySelector('[drawio-enabled]').getAttribute('drawio-enabled') !== 'true') return;
        let cursorPos = this.cm.getCursor('from');

        DrawIO.show(() => {
            return Promise.resolve('');
        }, (pngData) => {
            // let id = "image-" + Math.random().toString(16).slice(2);
            // let loadingImage = window.baseUrl('/loading.gif');
            let data = {
                image: pngData,
                uploaded_to: Number(document.getElementById('page-editor').getAttribute('page-id'))
            };

            window.$http.post(window.baseUrl('/images/drawing/upload'), data).then(resp => {
                this.insertDrawing(resp.data, cursorPos);
                DrawIO.close();
            }).catch(err => {
                window.$events.emit('error', trans('errors.image_upload_error'));
                console.log(err);
            });
        });
    }

    insertDrawing(image, originalCursor) {
        let newText = `<div drawio-diagram="${image.id}"><img src="${image.url}"></div>`;
        this.cm.focus();
        this.cm.replaceSelection(newText);
        this.cm.setCursor(originalCursor.line, originalCursor.ch + newText.length);
    }

    // Show draw.io if enabled and handle save.
    actionEditDrawing(imgContainer) {
        if (document.querySelector('[drawio-enabled]').getAttribute('drawio-enabled') !== 'true') return;
        let cursorPos = this.cm.getCursor('from');
        let drawingId = imgContainer.getAttribute('drawio-diagram');

        DrawIO.show(() => {
            return window.$http.get(window.baseUrl(`/images/base64/${drawingId}`)).then(resp => {
                return `data:image/png;base64,${resp.data.content}`;
            });
        }, (pngData) => {

            let data = {
                image: pngData,
                uploaded_to: Number(document.getElementById('page-editor').getAttribute('page-id'))
            };

            window.$http.post(window.baseUrl(`/images/drawing/upload`), data).then(resp => {
                let newText = `<div drawio-diagram="${resp.data.id}"><img src="${resp.data.url}"></div>`;
                let newContent = this.cm.getValue().split('\n').map(line => {
                    if (line.indexOf(`drawio-diagram="${drawingId}"`) !== -1) {
                        return newText;
                    }
                    return line;
                }).join('\n');
                this.cm.setValue(newContent);
                this.cm.setCursor(cursorPos);
                this.cm.focus();
                DrawIO.close();
            }).catch(err => {
                window.$events.emit('error', trans('errors.image_upload_error'));
                console.log(err);
            });
        });
    }

    // Scroll to a specified text
    scrollToText(searchText) {
        if (!searchText) {
            return;
        }

        const content = this.cm.getValue();
        const lines = content.split(/\r?\n/);
        let lineNumber = lines.findIndex(line => {
            return line && line.indexOf(searchText) !== -1;
        });

        if (lineNumber === -1) {
            return;
        }

        this.cm.scrollIntoView({
            line: lineNumber,
        }, 200);
        this.cm.focus();
        // set the cursor location.
        this.cm.setCursor({
            line: lineNumber,
            char: lines[lineNumber].length
        })
    }

}

module.exports = MarkdownEditor ;