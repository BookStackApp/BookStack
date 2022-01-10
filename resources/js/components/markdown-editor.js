import MarkdownIt from "markdown-it";
import mdTasksLists from 'markdown-it-task-lists';
import code from '../services/code';
import Clipboard from "../services/clipboard";
import {debounce} from "../services/util";

import DrawIO from "../services/drawio";

class MarkdownEditor {

    setup() {
        this.elem = this.$el;

        this.pageId = this.$opts.pageId;
        this.textDirection = this.$opts.textDirection;
        this.imageUploadErrorText = this.$opts.imageUploadErrorText;
        this.serverUploadLimitText = this.$opts.serverUploadLimitText;

        this.markdown = new MarkdownIt({html: true});
        this.markdown.use(mdTasksLists, {label: true});

        this.display = this.elem.querySelector('.markdown-display');

        this.displayStylesLoaded = false;
        this.input = this.elem.querySelector('textarea');
        this.cm = code.markdownEditor(this.input);

        this.onMarkdownScroll = this.onMarkdownScroll.bind(this);

        const displayLoad = () => {
            this.displayDoc = this.display.contentDocument;
            this.init();
        };

        if (this.display.contentDocument.readyState === 'complete') {
            displayLoad();
        } else {
            this.display.addEventListener('load', displayLoad.bind(this));
        }

        window.$events.emitPublic(this.elem, 'editor-markdown::setup', {
            markdownIt: this.markdown,
            displayEl: this.display,
            codeMirrorInstance: this.cm,
        });
    }

    init() {

        let lastClick = 0;

        // Prevent markdown display link click redirect
        this.displayDoc.addEventListener('click', event => {
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
            if (action === 'insertDrawing' && (event.ctrlKey || event.metaKey)) {
                this.actionShowImageManager();
                return;
            }
            if (action === 'insertDrawing') this.actionStartDrawing();
            if (action === 'fullscreen') this.actionFullScreen();
        });

        // Mobile section toggling
        this.elem.addEventListener('click', event => {
            const toolbarLabel = event.target.closest('.editor-toolbar-label');
            if (!toolbarLabel) return;

            const currentActiveSections = this.elem.querySelectorAll('.markdown-editor-wrap');
            for (let activeElem of currentActiveSections) {
                activeElem.classList.remove('active');
            }

            toolbarLabel.closest('.markdown-editor-wrap').classList.add('active');
        });

        window.$events.listen('editor-markdown-update', value => {
            this.cm.setValue(value);
            this.updateAndRender();
        });

        this.codeMirrorSetup();
        this.listenForBookStackEditorEvents();

        // Scroll to text if needed.
        const queryParams = (new URL(window.location)).searchParams;
        const scrollText = queryParams.get('content-text');
        if (scrollText) {
            this.scrollToText(scrollText);
        }
    }

    // Update the input content and render the display.
    updateAndRender() {
        const content = this.cm.getValue();
        this.input.value = content;
        const html = this.markdown.render(content);
        window.$events.emit('editor-html-change', html);
        window.$events.emit('editor-markdown-change', content);

        // Set body content
        this.displayDoc.body.className = 'page-content';
        this.displayDoc.body.innerHTML = html;

        // Copy styles from page head and set custom styles for editor
        this.loadStylesIntoDisplay();
    }

    loadStylesIntoDisplay() {
        if (this.displayStylesLoaded) return;
        this.displayDoc.documentElement.classList.add('markdown-editor-display');
        // Set display to be dark mode if parent is

        if (document.documentElement.classList.contains('dark-mode')) {
            this.displayDoc.documentElement.style.backgroundColor = '#222';
            this.displayDoc.documentElement.classList.add('dark-mode');
        }

        this.displayDoc.head.innerHTML = '';
        const styles = document.head.querySelectorAll('style,link[rel=stylesheet]');
        for (let style of styles) {
            const copy = style.cloneNode(true);
            this.displayDoc.head.appendChild(copy);
        }

        this.displayStylesLoaded = true;
    }

    onMarkdownScroll(lineCount) {
        const elems = this.displayDoc.body.children;
        if (elems.length <= lineCount) return;

        const topElem = (lineCount === -1) ? elems[elems.length-1] : elems[lineCount];
        topElem.scrollIntoView({ block: 'start', inline: 'nearest', behavior: 'smooth'});
    }

    codeMirrorSetup() {
        const cm = this.cm;
        const context = this;

        // Text direction
        // cm.setOption('direction', this.textDirection);
        cm.setOption('direction', 'ltr'); // Will force to remain as ltr for now due to issues when HTML is in editor.
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

        const onScrollDebounced = debounce((instance) => {
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
        }, 100);

        // Handle scroll to sync display view
        cm.on('scroll', instance => {
            onScrollDebounced(instance);
        });

        // Handle image paste
        cm.on('paste', (cm, event) => {
            const clipboard = new Clipboard(event.clipboardData || event.dataTransfer);

            // Don't handle the event ourselves if no items exist of contains table-looking data
            if (!clipboard.hasItems() || clipboard.containsTabularData()) {
                return;
            }

            const images = clipboard.getImages();
            for (const image of images) {
                uploadImage(image);
            }
        });

        // Handle image & content drag n drop
        cm.on('drop', (cm, event) => {

            const templateId = event.dataTransfer.getData('bookstack/template');
            if (templateId) {
                const cursorPos = cm.coordsChar({left: event.pageX, top: event.pageY});
                cm.setCursor(cursorPos);
                event.preventDefault();
                window.$http.get(`/templates/${templateId}`).then(resp => {
                    const content = resp.data.markdown || resp.data.html;
                    cm.replaceSelection(content);
                });
            }

            const clipboard = new Clipboard(event.dataTransfer);
            if (clipboard.hasItems() && clipboard.getImages().length > 0) {
                const cursorPos = cm.coordsChar({left: event.pageX, top: event.pageY});
                cm.setCursor(cursorPos);
                event.stopPropagation();
                event.preventDefault();
                const images = clipboard.getImages();
                for (const image of images) {
                    uploadImage(image);
                }
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
            const id = "image-" + Math.random().toString(16).slice(2);
            const placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
            const selectedText = cm.getSelection();
            const placeHolderText = `![${selectedText}](${placeholderImage})`;
            const cursor = cm.getCursor();
            cm.replaceSelection(placeHolderText);
            cm.setCursor({line: cursor.line, ch: cursor.ch + selectedText.length + 3});

            const remoteFilename = "image-" + Date.now() + "." + ext;
            const formData = new FormData();
            formData.append('file', file, remoteFilename);
            formData.append('uploaded_to', context.pageId);

            window.$http.post('/images/gallery', formData).then(resp => {
                const newContent = `[![${selectedText}](${resp.data.thumbs.display})](${resp.data.url})`;
                replaceContent(placeHolderText, newContent);
            }).catch(err => {
                window.$events.emit('error', context.imageUploadErrorText);
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
        const cursorPos = this.cm.getCursor('from');
        window.ImageManager.show(image => {
            const imageUrl = image.thumbs.display || image.url;
            let selectedText = this.cm.getSelection();
            let newText = "[![" + (selectedText || image.name) + "](" + imageUrl + ")](" + image.url + ")";
            this.cm.focus();
            this.cm.replaceSelection(newText);
            this.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        }, 'gallery');
    }

    actionShowImageManager() {
        const cursorPos = this.cm.getCursor('from');
        window.ImageManager.show(image => {
            this.insertDrawing(image, cursorPos);
        }, 'drawio');
    }

    // Show the popup link selector and insert a link when finished
    actionShowLinkSelector() {
        const cursorPos = this.cm.getCursor('from');
        window.EntitySelectorPopup.show(entity => {
            let selectedText = this.cm.getSelection() || entity.name;
            let newText = `[${selectedText}](${entity.link})`;
            this.cm.focus();
            this.cm.replaceSelection(newText);
            this.cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
        });
    }

    getDrawioUrl() {
        const drawioUrlElem = document.querySelector('[drawio-url]');
        return drawioUrlElem ? drawioUrlElem.getAttribute('drawio-url') : false;
    }

    // Show draw.io if enabled and handle save.
    actionStartDrawing() {
        const url = this.getDrawioUrl();
        if (!url) return;

        const cursorPos = this.cm.getCursor('from');

        DrawIO.show(url,() => {
            return Promise.resolve('');
        }, (pngData) => {

            const data = {
                image: pngData,
                uploaded_to: Number(this.pageId),
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
        this.cm.focus();
        this.cm.replaceSelection(newText);
        this.cm.setCursor(originalCursor.line, originalCursor.ch + newText.length);
    }

    // Show draw.io if enabled and handle save.
    actionEditDrawing(imgContainer) {
        const drawioUrl = this.getDrawioUrl();
        if (!drawioUrl) {
            return;
        }

        const cursorPos = this.cm.getCursor('from');
        const drawingId = imgContainer.getAttribute('drawio-diagram');

        DrawIO.show(drawioUrl, () => {
            return DrawIO.load(drawingId);
        }, (pngData) => {

            let data = {
                image: pngData,
                uploaded_to: Number(this.pageId),
            };

            window.$http.post("/images/drawio", data).then(resp => {
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
                this.handleDrawingUploadError(err);
            });
        });
    }

    handleDrawingUploadError(error) {
        if (error.status === 413) {
            window.$events.emit('error', this.serverUploadLimitText);
        } else {
            window.$events.emit('error', this.imageUploadErrorText);
        }
        console.log(error);
    }

    // Make the editor full screen
    actionFullScreen() {
        const alreadyFullscreen = this.elem.classList.contains('fullscreen');
        this.elem.classList.toggle('fullscreen', !alreadyFullscreen);
        document.body.classList.toggle('markdown-fullscreen', !alreadyFullscreen);
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

    listenForBookStackEditorEvents() {

        function getContentToInsert({html, markdown}) {
            return markdown || html;
        }

        // Replace editor content
        window.$events.listen('editor::replace', (eventContent) => {
            const markdown = getContentToInsert(eventContent);
            this.cm.setValue(markdown);
        });

        // Append editor content
        window.$events.listen('editor::append', (eventContent) => {
            const cursorPos = this.cm.getCursor('from');
            const markdown = getContentToInsert(eventContent);
            const content = this.cm.getValue() + '\n' + markdown;
            this.cm.setValue(content);
            this.cm.setCursor(cursorPos.line, cursorPos.ch);
        });

        // Prepend editor content
        window.$events.listen('editor::prepend', (eventContent) => {
            const cursorPos = this.cm.getCursor('from');
            const markdown = getContentToInsert(eventContent);
            const content = markdown + '\n' + this.cm.getValue();
            this.cm.setValue(content);
            const prependLineCount = markdown.split('\n').length;
            this.cm.setCursor(cursorPos.line + prependLineCount, cursorPos.ch);
        });

        // Insert editor content at the current location
        window.$events.listen('editor::insert', (eventContent) => {
            const markdown = getContentToInsert(eventContent);
            this.cm.replaceSelection(markdown);
        });

        // Focus on editor
        window.$events.listen('editor::focus', () => {
            this.cm.focus();
        });
    }
}

export default MarkdownEditor ;
