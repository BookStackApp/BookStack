"use strict";
const MarkdownIt = require("markdown-it");
const mdTasksLists = require('markdown-it-task-lists');
const code = require('./code');

module.exports = function (ngApp, events) {

    /**
     * TinyMCE
     * An angular wrapper around the tinyMCE editor.
     */
    ngApp.directive('tinymce', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            scope: {
                tinymce: '=',
                mceModel: '=',
                mceChange: '='
            },
            link: function (scope, element, attrs) {

                function tinyMceSetup(editor) {
                    editor.on('ExecCommand change NodeChange ObjectResized', (e) => {
                        let content = editor.getContent();
                        $timeout(() => {
                            scope.mceModel = content;
                        });
                        scope.mceChange(content);
                    });

                    editor.on('keydown', (event) => {
                        scope.$emit('editor-keydown', event);
                    });

                    editor.on('init', (e) => {
                        scope.mceModel = editor.getContent();
                    });

                    scope.$on('html-update', (event, value) => {
                        editor.setContent(value);
                        editor.selection.select(editor.getBody(), true);
                        editor.selection.collapse(false);
                        scope.mceModel = editor.getContent();
                    });
                }

                scope.tinymce.extraSetups.push(tinyMceSetup);
                tinymce.init(scope.tinymce);
            }
        }
    }]);

    const md = new MarkdownIt({html: true});
    md.use(mdTasksLists, {label: true});

    /**
     * Markdown input
     * Handles the logic for just the editor input field.
     */
    ngApp.directive('markdownInput', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            scope: {
                mdModel: '=',
                mdChange: '='
            },
            link: function (scope, element, attrs) {

                // Codemirror Setup
                element = element.find('textarea').first();
                let cm = code.markdownEditor(element[0]);

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
                extraKeys[`${metaKey}-S`] = function(cm) {scope.$emit('save-draft');};
                // Show link selector
                extraKeys[`Shift-${metaKey}-K`] = function(cm) {showLinkSelector()};
                // Insert Link
                extraKeys[`${metaKey}-K`] = function(cm) {insertLink()};
                // FormatShortcuts
                extraKeys[`${metaKey}-1`] = function(cm) {replaceLineStart('##');};
                extraKeys[`${metaKey}-2`] = function(cm) {replaceLineStart('###');};
                extraKeys[`${metaKey}-3`] = function(cm) {replaceLineStart('####');};
                extraKeys[`${metaKey}-4`] = function(cm) {replaceLineStart('#####');};
                extraKeys[`${metaKey}-5`] = function(cm) {replaceLineStart('');};
                extraKeys[`${metaKey}-d`] = function(cm) {replaceLineStart('');};
                extraKeys[`${metaKey}-6`] = function(cm) {replaceLineStart('>');};
                extraKeys[`${metaKey}-q`] = function(cm) {replaceLineStart('>');};
                extraKeys[`${metaKey}-7`] = function(cm) {wrapSelection('\n```\n', '\n```');};
                extraKeys[`${metaKey}-8`] = function(cm) {wrapSelection('`', '`');};
                extraKeys[`Shift-${metaKey}-E`] = function(cm) {wrapSelection('`', '`');};
                extraKeys[`${metaKey}-9`] = function(cm) {wrapSelection('<p class="callout info">', '</p>');};
                cm.setOption('extraKeys', extraKeys);

                // Update data on content change
                cm.on('change', (instance, changeObj) => {
                    update(instance);
                });

                // Handle scroll to sync display view
                cm.on('scroll', instance => {
                    // Thanks to http://liuhao.im/english/2015/11/10/the-sync-scroll-of-markdown-editor-in-javascript.html
                    let scroll = instance.getScrollInfo();
                    let atEnd = scroll.top + scroll.clientHeight === scroll.height;
                    if (atEnd) {
                        scope.$emit('markdown-scroll', -1);
                        return;
                    }
                    let lineNum = instance.lineAtHeight(scroll.top, 'local');
                    let range = instance.getRange({line: 0, ch: null}, {line: lineNum, ch: null});
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(md.render(range), 'text/html');
                    let totalLines = doc.documentElement.querySelectorAll('body > *');
                    scope.$emit('markdown-scroll', totalLines.length);
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
                    cm.replaceSelection(placeHolderText);

                    let remoteFilename = "image-" + Date.now() + "." + ext;
                    let formData = new FormData();
                    formData.append('file', file, remoteFilename);

                    window.$http.post('/images/gallery/upload', formData).then(resp => {
                        replaceContent(placeholderImage, resp.data.thumbs.display);
                    }).catch(err => {
                        events.emit('error', trans('errors.image_upload_error'));
                        replaceContent(placeHolderText, selectedText);
                        console.log(err);
                    });
                }

                // Show the popup link selector and insert a link when finished
                function showLinkSelector() {
                    let cursorPos = cm.getCursor('from');
                    window.EntitySelectorPopup.show(entity => {
                        let selectedText = cm.getSelection() || entity.name;
                        let newText = `[${selectedText}](${entity.link})`;
                        cm.focus();
                        cm.replaceSelection(newText);
                        cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
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

                // Show the image manager and handle image insertion
                function showImageManager() {
                    let cursorPos = cm.getCursor('from');
                    window.ImageManager.show(image => {
                        let selectedText = cm.getSelection();
                        let newText = "![" + (selectedText || image.name) + "](" + image.thumbs.display + ")";
                        cm.focus();
                        cm.replaceSelection(newText);
                        cm.setCursor(cursorPos.line, cursorPos.ch + newText.length);
                    });
                }

                // Update the data models and rendered output
                function update(instance) {
                    let content = instance.getValue();
                    element.val(content);
                    $timeout(() => {
                        scope.mdModel = content;
                        scope.mdChange(md.render(content));
                    });
                }
                update(cm);

                // Listen to commands from parent scope
                scope.$on('md-insert-link', showLinkSelector);
                scope.$on('md-insert-image', showImageManager);
                scope.$on('markdown-update', (event, value) => {
                    cm.setValue(value);
                    element.val(value);
                    scope.mdModel = value;
                    scope.mdChange(md.render(value));
                });

            }
        }
    }]);

    /**
     * Markdown Editor
     * Handles all functionality of the markdown editor.
     */
    ngApp.directive('markdownEditor', ['$timeout', '$rootScope', function ($timeout, $rootScope) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {

                // Editor Elements
                const $display = element.find('.markdown-display').first();
                const $insertImage = element.find('button[data-action="insertImage"]');
                const $insertEntityLink = element.find('button[data-action="insertEntityLink"]');

                // Prevent markdown display link click redirect
                $display.on('click', 'a', function(event) {
                    event.preventDefault();
                    window.open(this.getAttribute('href'));
                });

                // Editor UI Actions
                $insertEntityLink.click(e => {scope.$broadcast('md-insert-link');});
                $insertImage.click(e => {scope.$broadcast('md-insert-image');});

                // Handle scroll sync event from editor scroll
                $rootScope.$on('markdown-scroll', (event, lineCount) => {
                    let elems = $display[0].children[0].children;
                    if (elems.length > lineCount) {
                        let topElem = (lineCount === -1) ? elems[elems.length-1] : elems[lineCount];
                        $display.animate({
                            scrollTop: topElem.offsetTop
                        }, {queue: false, duration: 200, easing: 'linear'});
                    }
                });
            }
        }
    }]);

    /**
     * Page Editor Toolbox
     * Controls all functionality for the sliding toolbox
     * on the page edit view.
     */
    ngApp.directive('toolbox', [function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {

                // Get common elements
                const $buttons = elem.find('[toolbox-tab-button]');
                const $content = elem.find('[toolbox-tab-content]');
                const $toggle = elem.find('[toolbox-toggle]');

                // Handle toolbox toggle click
                $toggle.click((e) => {
                    elem.toggleClass('open');
                });

                // Set an active tab/content by name
                function setActive(tabName, openToolbox) {
                    $buttons.removeClass('active');
                    $content.hide();
                    $buttons.filter(`[toolbox-tab-button="${tabName}"]`).addClass('active');
                    $content.filter(`[toolbox-tab-content="${tabName}"]`).show();
                    if (openToolbox) elem.addClass('open');
                }

                // Set the first tab content active on load
                setActive($content.first().attr('toolbox-tab-content'), false);

                // Handle tab button click
                $buttons.click(function (e) {
                    let name = $(this).attr('toolbox-tab-button');
                    setActive(name, true);
                });
            }
        }
    }]);
};
