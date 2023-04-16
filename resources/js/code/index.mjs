import {EditorView, keymap} from "@codemirror/view";

import {copyTextToClipboard} from "../services/clipboard.js"
import {viewer, editor} from "./setups.js";
import {createView, updateViewLanguage} from "./views.js";

/**
 * Highlight pre elements on a page
 */
export function highlight() {
    const codeBlocks = document.querySelectorAll('.page-content pre, .comment-box .content pre');
    for (const codeBlock of codeBlocks) {
        highlightElem(codeBlock);
    }
}

/**
 * Highlight all code blocks within the given parent element
 * @param {HTMLElement} parent
 */
export function highlightWithin(parent) {
    const codeBlocks = parent.querySelectorAll('pre');
    for (const codeBlock of codeBlocks) {
        highlightElem(codeBlock);
    }
}

/**
 * Add code highlighting to a single element.
 * @param {HTMLElement} elem
 */
function highlightElem(elem) {
    const innerCodeElem = elem.querySelector('code[class^=language-]');
    elem.innerHTML = elem.innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
    const content = elem.textContent.trimEnd();

    let langName = '';
    if (innerCodeElem !== null) {
        langName = innerCodeElem.className.replace('language-', '');
    }

    const wrapper = document.createElement('div');
    elem.parentNode.insertBefore(wrapper, elem);

    const ev = createView({
        parent: wrapper,
        doc: content,
        extensions: viewer(wrapper),
    });

    setMode(ev, langName, content);
    elem.remove();
    addCopyIcon(ev);
}

/**
 * Add a button to a CodeMirror instance which copies the contents to the clipboard upon click.
 * @param {EditorView} editorView
 */
function addCopyIcon(editorView) {
    const copyIcon = `<svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>`;
    const checkIcon = `<svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>`;
    const copyButton = document.createElement('button');
    copyButton.setAttribute('type', 'button')
    copyButton.classList.add('cm-copy-button');
    copyButton.innerHTML = copyIcon;
    editorView.dom.appendChild(copyButton);

    const notifyTime = 620;
    const transitionTime = 60;
    copyButton.addEventListener('click', event => {
        copyTextToClipboard(editorView.state.doc.toString());
        copyButton.classList.add('success');

        setTimeout(() => {
            copyButton.innerHTML = checkIcon;
        }, transitionTime / 2);

        setTimeout(() => {
            copyButton.classList.remove('success');
        }, notifyTime);

        setTimeout(() => {
            copyButton.innerHTML = copyIcon;
        }, notifyTime + (transitionTime / 2));
    });
}

/**
 * Ge the theme to use for CodeMirror instances.
 * @returns {*|string}
 */
function getTheme() {
    // TODO - Remove
    const darkMode = document.documentElement.classList.contains('dark-mode');
    return window.codeTheme || (darkMode ? 'darcula' : 'default');
}

/**
 * Create a CodeMirror instance for showing inside the WYSIWYG editor.
 *  Manages a textarea element to hold code content.
 * @param {HTMLElement} cmContainer
 * @param {String} content
 * @param {String} language
 * @returns {EditorView}
 */
export function wysiwygView(cmContainer, content, language) {
    const ev = createView({
        parent: cmContainer,
        doc: content,
        extensions: viewer(cmContainer),
    });

    setMode(ev, language, content);

    return ev;
}


/**
 * Create a CodeMirror instance to show in the WYSIWYG pop-up editor
 * @param {HTMLElement} elem
 * @param {String} modeSuggestion
 * @returns {*}
 */
export function popupEditor(elem, modeSuggestion) {
    const content = elem.textContent;

    return CodeMirror(function(elt) {
        elem.parentNode.insertBefore(elt, elem);
        elem.style.display = 'none';
    }, {
        value: content,
        mode:  getMode(modeSuggestion, content),
        lineNumbers: true,
        lineWrapping: false,
        theme: getTheme()
    });
}

/**
 * Create an inline editor to replace the given textarea.
 * @param {HTMLTextAreaElement} textArea
 * @param {String} mode
 * @returns {EditorView}
 */
export function inlineEditor(textArea, mode) {
    const content = textArea.value;
    const config = {
        parent: textArea.parentNode,
        doc: content,
        extensions: [
            ...editor(textArea.parentElement),
            EditorView.updateListener.of((v) => {
                if (v.docChanged) {
                    textArea.value = v.state.doc.toString();
                }
            }),
        ],
    };

    // Create editor view, hide original input
    const ev = createView(config);
    setMode(ev, mode, content);
    textArea.style.display = 'none';

    return ev;
}

/**
 * Set the language mode of a codemirror EditorView.
 *
 * @param {EditorView} ev
 * @param {string} modeSuggestion
 * @param {string} content
 */
export function setMode(ev, modeSuggestion, content) {
    updateViewLanguage(ev, modeSuggestion, content);
}

/**
 * Set the content of a cm instance.
 * @param cmInstance
 * @param codeContent
 */
export function setContent(cmInstance, codeContent) {
    cmInstance.setValue(codeContent);
    setTimeout(() => {
        updateLayout(cmInstance);
    }, 10);
}

/**
 * Update the layout (codemirror refresh) of a cm instance.
 * @param cmInstance
 */
export function updateLayout(cmInstance) {
    cmInstance.refresh();
}

/**
 * Get a CodeMirror instance to use for the markdown editor.
 * @param {HTMLElement} elem
 * @param {function} onChange
 * @param {object} domEventHandlers
 * @param {Array} keyBindings
 * @returns {*}
 */
export function markdownEditor(elem, onChange, domEventHandlers, keyBindings) {
    const content = elem.textContent;
    const config = {
        parent: elem.parentNode,
        doc: content,
        extensions: [
            ...editor(elem.parentElement),
            EditorView.updateListener.of((v) => {
                onChange(v);
            }),
            EditorView.domEventHandlers(domEventHandlers),
            keymap.of(keyBindings),
        ],
    };

    // Emit a pre-event public event to allow tweaking of the configure before view creation.
    window.$events.emitPublic(elem, 'editor-markdown-cm::pre-init', {cmEditorViewConfig: config});

    // Create editor view, hide original input
    const ev = createView(config);
    setMode(ev, 'markdown', '');
    elem.style.display = 'none';

    return ev;
}