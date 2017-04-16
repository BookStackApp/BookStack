"use strict";
const DropZone = require("dropzone");
const markdown = require("marked");

module.exports = function (ngApp, events) {

    /**
     * Common tab controls using simple jQuery functions.
     */
    ngApp.directive('tabContainer', function() {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                const $content = element.find('[tab-content]');
                const $buttons = element.find('[tab-button]');

                if (attrs.tabContainer) {
                    let initial = attrs.tabContainer;
                    $buttons.filter(`[tab-button="${initial}"]`).addClass('selected');
                    $content.hide().filter(`[tab-content="${initial}"]`).show();
                } else {
                    $content.hide().first().show();
                    $buttons.first().addClass('selected');
                }

                $buttons.click(function() {
                    let clickedTab = $(this);
                    $buttons.removeClass('selected');
                    $content.hide();
                    let name = clickedTab.addClass('selected').attr('tab-button');
                    $content.filter(`[tab-content="${name}"]`).show();
                });
            }
        };
    });

    /**
     * Sub form component to allow inner-form sections to act like their own forms.
     */
    ngApp.directive('subForm', function() {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.on('keypress', e => {
                    if (e.keyCode === 13) {
                        submitEvent(e);
                    }
                });

                element.find('button[type="submit"]').click(submitEvent);

                function submitEvent(e) {
                    e.preventDefault();
                    if (attrs.subForm) scope.$eval(attrs.subForm);
                }
            }
        };
    });

    /**
     * DropZone
     * Used for uploading images
     */
    ngApp.directive('dropZone', [function () {
        return {
            restrict: 'E',
            template: `
            <div class="dropzone-container">
                <div class="dz-message">{{message}}</div>
            </div>
            `,
            scope: {
                uploadUrl: '@',
                eventSuccess: '=',
                eventError: '=',
                uploadedTo: '@',
            },
            link: function (scope, element, attrs) {
                scope.message = attrs.message;
                if (attrs.placeholder) element[0].querySelector('.dz-message').textContent = attrs.placeholder;
                let dropZone = new DropZone(element[0].querySelector('.dropzone-container'), {
                    url: scope.uploadUrl,
                    init: function () {
                        let dz = this;
                        dz.on('sending', function (file, xhr, data) {
                            let token = window.document.querySelector('meta[name=token]').getAttribute('content');
                            data.append('_token', token);
                            let uploadedTo = typeof scope.uploadedTo === 'undefined' ? 0 : scope.uploadedTo;
                            data.append('uploaded_to', uploadedTo);
                        });
                        if (typeof scope.eventSuccess !== 'undefined') dz.on('success', scope.eventSuccess);
                        dz.on('success', function (file, data) {
                            $(file.previewElement).fadeOut(400, function () {
                                dz.removeFile(file);
                            });
                        });
                        if (typeof scope.eventError !== 'undefined') dz.on('error', scope.eventError);
                        dz.on('error', function (file, errorMessage, xhr) {
                            console.log(errorMessage);
                            console.log(xhr);
                            function setMessage(message) {
                                $(file.previewElement).find('[data-dz-errormessage]').text(message);
                            }

                            if (xhr.status === 413) setMessage(trans('errors.server_upload_limit'));
                            if (errorMessage.file) setMessage(errorMessage.file[0]);

                        });
                    }
                });
            }
        };
    }]);

    /**
     * Dropdown
     * Provides some simple logic to create small dropdown menus
     */
    ngApp.directive('dropdown', [function () {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                const menu = element.find('ul');
                element.find('[dropdown-toggle]').on('click', function () {
                    menu.show().addClass('anim menuIn');
                    let inputs = menu.find('input');
                    let hasInput = inputs.length > 0;
                    if (hasInput) {
                        inputs.first().focus();
                        element.on('keypress', 'input', event => {
                            if (event.keyCode === 13) {
                                event.preventDefault();
                                menu.hide();
                                menu.removeClass('anim menuIn');
                                return false;
                            }
                        });
                    }
                    element.mouseleave(function () {
                        menu.hide();
                        menu.removeClass('anim menuIn');
                    });
                });
            }
        };
    }]);

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

                // Custom tinyMCE plugins
                tinymce.PluginManager.add('customhr', function (editor) {
                    editor.addCommand('InsertHorizontalRule', function () {
                        let hrElem = document.createElement('hr');
                        let cNode = editor.selection.getNode();
                        let parentNode = cNode.parentNode;
                        parentNode.insertBefore(hrElem, cNode);
                    });

                    editor.addButton('hr', {
                        icon: 'hr',
                        tooltip: 'Horizontal line',
                        cmd: 'InsertHorizontalRule'
                    });

                    editor.addMenuItem('hr', {
                        icon: 'hr',
                        text: 'Horizontal line',
                        cmd: 'InsertHorizontalRule',
                        context: 'insert'
                    });
                });

                tinymce.init(scope.tinymce);
            }
        }
    }]);

    let renderer = new markdown.Renderer();
    // Custom markdown checkbox list item
    // Attribution: https://github.com/chjj/marked/issues/107#issuecomment-44542001
    renderer.listitem = function(text) {
        if (/^\s*\[[x ]\]\s*/.test(text)) {
            text = text
                .replace(/^\s*\[ \]\s*/, '<input type="checkbox"/>')
                .replace(/^\s*\[x\]\s*/, '<input type="checkbox" checked/>');
            return `<li class="checkbox-item">${text}</li>`;
        }
        return `<li>${text}</li>`;
    };

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

                // Set initial model content
                element = element.find('textarea').first();
                let content = element.val();
                scope.mdModel = content;
                scope.mdChange(markdown(content, {renderer: renderer}));

                element.on('change input', (event) => {
                    content = element.val();
                    $timeout(() => {
                        scope.mdModel = content;
                        scope.mdChange(markdown(content, {renderer: renderer}));
                    });
                });

                scope.$on('markdown-update', (event, value) => {
                    element.val(value);
                    scope.mdModel = value;
                    scope.mdChange(markdown(value));
                });

            }
        }
    }]);

    /**
     * Markdown Editor
     * Handles all functionality of the markdown editor.
     */
    ngApp.directive('markdownEditor', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {

                // Elements
                const $input = element.find('[markdown-input] textarea').first();
                const $display = element.find('.markdown-display').first();
                const $insertImage = element.find('button[data-action="insertImage"]');
                const $insertEntityLink = element.find('button[data-action="insertEntityLink"]');

                // Prevent markdown display link click redirect
                $display.on('click', 'a', function(event) {
                    event.preventDefault();
                    window.open(this.getAttribute('href'));
                });

                let currentCaretPos = 0;

                $input.blur(event => {
                    currentCaretPos = $input[0].selectionStart;
                });

                // Scroll sync
                let inputScrollHeight,
                    inputHeight,
                    displayScrollHeight,
                    displayHeight;

                function setScrollHeights() {
                    inputScrollHeight = $input[0].scrollHeight;
                    inputHeight = $input.height();
                    displayScrollHeight = $display[0].scrollHeight;
                    displayHeight = $display.height();
                }

                setTimeout(() => {
                    setScrollHeights();
                }, 200);
                window.addEventListener('resize', setScrollHeights);
                let scrollDebounceTime = 800;
                let lastScroll = 0;
                $input.on('scroll', event => {
                    let now = Date.now();
                    if (now - lastScroll > scrollDebounceTime) {
                        setScrollHeights()
                    }
                    let scrollPercent = ($input.scrollTop() / (inputScrollHeight - inputHeight));
                    let displayScrollY = (displayScrollHeight - displayHeight) * scrollPercent;
                    $display.scrollTop(displayScrollY);
                    lastScroll = now;
                });

                // Editor key-presses
                $input.keydown(event => {
                    // Insert image shortcut
                    if (event.which === 73 && event.ctrlKey && event.shiftKey) {
                        event.preventDefault();
                        let caretPos = $input[0].selectionStart;
                        let currentContent = $input.val();
                        const mdImageText = "![](http://)";
                        $input.val(currentContent.substring(0, caretPos) + mdImageText + currentContent.substring(caretPos));
                        $input.focus();
                        $input[0].selectionStart = caretPos + ("![](".length);
                        $input[0].selectionEnd = caretPos + ('![](http://'.length);
                        return;
                    }

                    // Insert entity link shortcut
                    if (event.which === 75 && event.ctrlKey && event.shiftKey) {
                        showLinkSelector();
                        return;
                    }

                    // Pass key presses to controller via event
                    scope.$emit('editor-keydown', event);
                });

                // Insert image from image manager
                $insertImage.click(event => {
                    window.ImageManager.showExternal(image => {
                        let caretPos = currentCaretPos;
                        let currentContent = $input.val();
                        let mdImageText = "![" + image.name + "](" + image.thumbs.display + ")";
                        $input.val(currentContent.substring(0, caretPos) + mdImageText + currentContent.substring(caretPos));
                        $input.change();
                    });
                });

                function showLinkSelector() {
                    window.showEntityLinkSelector((entity) => {
                        let selectionStart = currentCaretPos;
                        let selectionEnd = $input[0].selectionEnd;
                        let textSelected = (selectionEnd !== selectionStart);
                        let currentContent = $input.val();

                        if (textSelected) {
                            let selectedText = currentContent.substring(selectionStart, selectionEnd);
                            let linkText = `[${selectedText}](${entity.link})`;
                            $input.val(currentContent.substring(0, selectionStart) + linkText + currentContent.substring(selectionEnd));
                        } else {
                            let linkText = ` [${entity.name}](${entity.link}) `;
                            $input.val(currentContent.substring(0, selectionStart) + linkText + currentContent.substring(selectionStart))
                        }
                        $input.change();
                    });
                }
                $insertEntityLink.click(showLinkSelector);

                // Upload and insert image on paste
                function editorPaste(e) {
                    e = e.originalEvent;
                    if (!e.clipboardData) return
                    let items = e.clipboardData.items;
                    if (!items) return;
                    for (let i = 0; i < items.length; i++) {
                        uploadImage(items[i].getAsFile());
                    }
                }

                $input.on('paste', editorPaste);

                // Handle image drop, Uploads images to BookStack.
                function handleImageDrop(event) {
                    event.stopPropagation();
                    event.preventDefault();
                    let files = event.originalEvent.dataTransfer.files;
                    for (let i = 0; i < files.length; i++) {
                        uploadImage(files[i]);
                    }
                }

                $input.on('drop', handleImageDrop);

                // Handle image upload and add image into markdown content
                function uploadImage(file) {
                    if (file.type.indexOf('image') !== 0) return;
                    let formData = new FormData();
                    let ext = 'png';
                    let xhr = new XMLHttpRequest();

                    if (file.name) {
                        let fileNameMatches = file.name.match(/\.(.+)$/);
                        if (fileNameMatches) {
                            ext = fileNameMatches[1];
                        }
                    }

                    // Insert image into markdown
                    let id = "image-" + Math.random().toString(16).slice(2);
                    let selectStart = $input[0].selectionStart;
                    let selectEnd = $input[0].selectionEnd;
                    let content = $input[0].value;
                    let selectText = content.substring(selectStart, selectEnd);
                    let placeholderImage = window.baseUrl(`/loading.gif#upload${id}`);
                    let innerContent = ((selectEnd > selectStart) ? `![${selectText}]` : '![]') + `(${placeholderImage})`;
                    $input[0].value = content.substring(0, selectStart) +  innerContent + content.substring(selectEnd);

                    $input.focus();
                    $input[0].selectionStart = selectStart;
                    $input[0].selectionEnd = selectStart;

                    let remoteFilename = "image-" + Date.now() + "." + ext;
                    formData.append('file', file, remoteFilename);
                    formData.append('_token', document.querySelector('meta[name="token"]').getAttribute('content'));

                    xhr.open('POST', window.baseUrl('/images/gallery/upload'));
                    xhr.onload = function () {
                        let selectStart = $input[0].selectionStart;
                        if (xhr.status === 200 || xhr.status === 201) {
                            let result = JSON.parse(xhr.responseText);
                            $input[0].value = $input[0].value.replace(placeholderImage, result.thumbs.display);
                            $input.change();
                        } else {
                            console.log(trans('errors.image_upload_error'));
                            console.log(xhr.responseText);
                            $input[0].value = $input[0].value.replace(innerContent, '');
                            $input.change();
                        }
                        $input.focus();
                        $input[0].selectionStart = selectStart;
                        $input[0].selectionEnd = selectStart;
                    };
                    xhr.send(formData);
                }

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

    /**
     * Tag Autosuggestions
     * Listens to child inputs and provides autosuggestions depending on field type
     * and input. Suggestions provided by server.
     */
    ngApp.directive('tagAutosuggestions', ['$http', function ($http) {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {

                // Local storage for quick caching.
                const localCache = {};

                // Create suggestion element
                const suggestionBox = document.createElement('ul');
                suggestionBox.className = 'suggestion-box';
                suggestionBox.style.position = 'absolute';
                suggestionBox.style.display = 'none';
                const $suggestionBox = $(suggestionBox);

                // General state tracking
                let isShowing = false;
                let currentInput = false;
                let active = 0;

                // Listen to input events on autosuggest fields
                elem.on('input focus', '[autosuggest]', function (event) {
                    let $input = $(this);
                    let val = $input.val();
                    let url = $input.attr('autosuggest');
                    let type = $input.attr('autosuggest-type');

                    // Add name param to request if for a value
                    if (type.toLowerCase() === 'value') {
                        let $nameInput = $input.closest('tr').find('[autosuggest-type="name"]').first();
                        let nameVal = $nameInput.val();
                        if (nameVal !== '') {
                            url += '?name=' + encodeURIComponent(nameVal);
                        }
                    }

                    let suggestionPromise = getSuggestions(val.slice(0, 3), url);
                    suggestionPromise.then(suggestions => {
                        if (val.length === 0) {
                            displaySuggestions($input, suggestions.slice(0, 6));
                        } else  {
                            suggestions = suggestions.filter(item => {
                                return item.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                            }).slice(0, 4);
                            displaySuggestions($input, suggestions);
                        }
                    });
                });

                // Hide autosuggestions when input loses focus.
                // Slight delay to allow clicks.
                let lastFocusTime = 0;
                elem.on('blur', '[autosuggest]', function (event) {
                    let startTime = Date.now();
                    setTimeout(() => {
                        if (lastFocusTime < startTime) {
                            $suggestionBox.hide();
                            isShowing = false;
                        }
                    }, 200)
                });
                elem.on('focus', '[autosuggest]', function (event) {
                    lastFocusTime = Date.now();
                });

                elem.on('keydown', '[autosuggest]', function (event) {
                    if (!isShowing) return;

                    let suggestionElems = suggestionBox.childNodes;
                    let suggestCount = suggestionElems.length;

                    // Down arrow
                    if (event.keyCode === 40) {
                        let newActive = (active === suggestCount - 1) ? 0 : active + 1;
                        changeActiveTo(newActive, suggestionElems);
                    }
                    // Up arrow
                    else if (event.keyCode === 38) {
                        let newActive = (active === 0) ? suggestCount - 1 : active - 1;
                        changeActiveTo(newActive, suggestionElems);
                    }
                    // Enter or tab key
                    else if ((event.keyCode === 13 || event.keyCode === 9) && !event.shiftKey) {
                        currentInput[0].value = suggestionElems[active].textContent;
                        currentInput.focus();
                        $suggestionBox.hide();
                        isShowing = false;
                        if (event.keyCode === 13) {
                            event.preventDefault();
                            return false;
                        }
                    }
                });

                // Change the active suggestion to the given index
                function changeActiveTo(index, suggestionElems) {
                    suggestionElems[active].className = '';
                    active = index;
                    suggestionElems[active].className = 'active';
                }

                // Display suggestions on a field
                let prevSuggestions = [];

                function displaySuggestions($input, suggestions) {

                    // Hide if no suggestions
                    if (suggestions.length === 0) {
                        $suggestionBox.hide();
                        isShowing = false;
                        prevSuggestions = suggestions;
                        return;
                    }

                    // Otherwise show and attach to input
                    if (!isShowing) {
                        $suggestionBox.show();
                        isShowing = true;
                    }
                    if ($input !== currentInput) {
                        $suggestionBox.detach();
                        $input.after($suggestionBox);
                        currentInput = $input;
                    }

                    // Return if no change
                    if (prevSuggestions.join() === suggestions.join()) {
                        prevSuggestions = suggestions;
                        return;
                    }

                    // Build suggestions
                    $suggestionBox[0].innerHTML = '';
                    for (let i = 0; i < suggestions.length; i++) {
                        let suggestion = document.createElement('li');
                        suggestion.textContent = suggestions[i];
                        suggestion.onclick = suggestionClick;
                        if (i === 0) {
                            suggestion.className = 'active';
                            active = 0;
                        }
                        $suggestionBox[0].appendChild(suggestion);
                    }

                    prevSuggestions = suggestions;
                }

                // Suggestion click event
                function suggestionClick(event) {
                    currentInput[0].value = this.textContent;
                    currentInput.focus();
                    $suggestionBox.hide();
                    isShowing = false;
                }

                // Get suggestions & cache
                function getSuggestions(input, url) {
                    let hasQuery = url.indexOf('?') !== -1;
                    let searchUrl = url + (hasQuery ? '&' : '?') + 'search=' + encodeURIComponent(input);

                    // Get from local cache if exists
                    if (typeof localCache[searchUrl] !== 'undefined') {
                        return new Promise((resolve, reject) => {
                            resolve(localCache[searchUrl]);
                        });
                    }

                    return $http.get(searchUrl).then(response => {
                        localCache[searchUrl] = response.data;
                        return response.data;
                    });
                }

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
};
