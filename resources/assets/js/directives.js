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
                extraKeys[`${metaKey}-9`] = function(cm) {wrapSelection('<p class="callout info">', '</div>');};
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
                    cm.setCursor({line: cursor.line, ch: cursor.ch + (newLineContent.length - lineLen)});
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
                    window.showEntityLinkSelector(entity => {
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

    ngApp.directive('entityLinkSelector', [function($http) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {

                const selectButton = element.find('.entity-link-selector-confirm');
                let callback = false;
                let entitySelection = null;

                // Handle entity selection change, Stores the selected entity locally
                function entitySelectionChange(entity) {
                    entitySelection = entity;
                    if (entity === null) {
                        selectButton.attr('disabled', 'true');
                    } else {
                        selectButton.removeAttr('disabled');
                    }
                }
                events.listen('entity-select-change', entitySelectionChange);

                // Handle selection confirm button click
                selectButton.click(event => {
                    hide();
                    if (entitySelection !== null) callback(entitySelection);
                });

                // Show selector interface
                function show() {
                    element.fadeIn(240);
                }

                // Hide selector interface
                function hide() {
                    element.fadeOut(240);
                }
                scope.hide = hide;

                // Listen to confirmation of entity selections (doubleclick)
                events.listen('entity-select-confirm', entity => {
                    hide();
                    callback(entity);
                });

                // Show entity selector, Accessible globally, and store the callback
                window.showEntityLinkSelector = function(passedCallback) {
                    show();
                    callback = passedCallback;
                };

            }
        };
    }]);


    ngApp.directive('entitySelector', ['$http', '$sce', function ($http, $sce) {
        return {
            restrict: 'A',
            scope: true,
            link: function (scope, element, attrs) {
                scope.loading = true;
                scope.entityResults = false;
                scope.search = '';

                // Add input for forms
                const input = element.find('[entity-selector-input]').first();

                // Detect double click events
                let lastClick = 0;
                function isDoubleClick() {
                    let now = Date.now();
                    let answer = now - lastClick < 300;
                    lastClick = now;
                    return answer;
                }

                // Listen to entity item clicks
                element.on('click', '.entity-list a', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    let item = $(this).closest('[data-entity-type]');
                    itemSelect(item, isDoubleClick());
                });
                element.on('click', '[data-entity-type]', function(event) {
                    itemSelect($(this), isDoubleClick());
                });

                // Select entity action
                function itemSelect(item, doubleClick) {
                    let entityType = item.attr('data-entity-type');
                    let entityId = item.attr('data-entity-id');
                    let isSelected = !item.hasClass('selected') || doubleClick;
                    element.find('.selected').removeClass('selected').removeClass('primary-background');
                    if (isSelected) item.addClass('selected').addClass('primary-background');
                    let newVal = isSelected ? `${entityType}:${entityId}` : '';
                    input.val(newVal);

                    if (!isSelected) {
                        events.emit('entity-select-change', null);
                    }

                    if (!doubleClick && !isSelected) return;

                    let link = item.find('.entity-list-item-link').attr('href');
                    let name = item.find('.entity-list-item-name').text();

                    if (doubleClick) {
                        events.emit('entity-select-confirm', {
                            id: Number(entityId),
                            name: name,
                            link: link
                        });
                    }

                    if (isSelected) {
                        events.emit('entity-select-change', {
                            id: Number(entityId),
                            name: name,
                            link: link
                        });
                    }
                }

                // Get search url with correct types
                function getSearchUrl() {
                    let types = (attrs.entityTypes) ? encodeURIComponent(attrs.entityTypes) : encodeURIComponent('page,book,chapter');
                    return window.baseUrl(`/ajax/search/entities?types=${types}`);
                }

                // Get initial contents
                $http.get(getSearchUrl()).then(resp => {
                    scope.entityResults = $sce.trustAsHtml(resp.data);
                    scope.loading = false;
                });

                // Search when typing
                scope.searchEntities = function() {
                    scope.loading = true;
                    input.val('');
                    let url = getSearchUrl() + '&term=' + encodeURIComponent(scope.search);
                    $http.get(url).then(resp => {
                        scope.entityResults = $sce.trustAsHtml(resp.data);
                        scope.loading = false;
                    });
                };
            }
        };
    }]);

    ngApp.directive('commentReply', [function () {
        return {
            restrict: 'E',
            templateUrl: 'comment-reply.html',
            scope: {
              pageId: '=',
              parentId: '=',
              parent: '='
            },
            link: function (scope, element) {
                scope.isReply = true;
                element.find('textarea').focus();
                scope.$on('evt.comment-success', function (event) {
                    // no need for the event to do anything more.
                    event.stopPropagation();
                    event.preventDefault();
                    scope.closeBox();
                });

                scope.closeBox = function () {
                    element.remove();
                    scope.$destroy();
                };
            }
        };
    }]);

    ngApp.directive('commentEdit', [function () {
         return {
            restrict: 'E',
            templateUrl: 'comment-reply.html',
            scope: {
              comment: '='
            },
            link: function (scope, element) {
                scope.isEdit = true;
                element.find('textarea').focus();
                scope.$on('evt.comment-success', function (event, commentId) {
                   // no need for the event to do anything more.
                   event.stopPropagation();
                   event.preventDefault();
                   if (commentId === scope.comment.id && !scope.isNew) {
                       scope.closeBox();
                   }
                });

                scope.closeBox = function () {
                    element.remove();
                    scope.$destroy();
                };
            }
        };
    }]);


    ngApp.directive('commentReplyLink', ['$document', '$compile', function ($document, $compile) {
        return {
            scope: {
                comment: '='
            },
            link: function (scope, element, attr) {
                element.on('$destroy', function () {
                    element.off('click');
                    scope.$destroy();
                });

                element.on('click', function (e) {
                    e.preventDefault();
                    var $container = element.parents('.comment-actions').first();
                    if (!$container.length) {
                        console.error('commentReplyLink directive should be placed inside a container with class comment-box!');
                        return;
                    }
                    if (attr.noCommentReplyDupe) {
                        removeDupe();
                    }

                    compileHtml($container, scope, attr.isReply === 'true');
                });
            }
        };

        function compileHtml($container, scope, isReply) {
            let lnkFunc = null;
            if (isReply) {
                lnkFunc = $compile('<comment-reply page-id="comment.pageId" parent-id="comment.id" parent="comment"></comment-reply>');
            } else {
                lnkFunc = $compile('<comment-edit comment="comment"></comment-add>');
            }
            var compiledHTML = lnkFunc(scope);
            $container.append(compiledHTML);
        }

        function removeDupe() {
            let $existingElement = $document.find('.comments-list comment-reply, .comments-list comment-edit');
            if (!$existingElement.length) {
                return;
            }

            $existingElement.remove();
        }
    }]);

    ngApp.directive('commentDeleteLink', ['$window', function ($window) {
        return {
            controller: 'CommentDeleteController',
            scope: {
                comment: '='
            },
            link: function (scope, element, attr, ctrl) {

                element.on('click', function(e) {
                    e.preventDefault();
                    var resp = $window.confirm(trans('entities.comment_delete_confirm'));
                    if (!resp) {
                        return;
                    }

                    ctrl.delete(scope.comment);
                });
            }
        };
    }]);
};
