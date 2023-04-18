import {EditorView, keymap} from '@codemirror/view';

import {copyTextToClipboard} from '../services/clipboard';
import {viewerExtensions, editorExtensions} from './setups';
import {createView} from './views';
import {SimpleEditorInterface} from './simple-editor-interface';

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
    elem.innerHTML = elem.innerHTML.replace(/<br\s*[\/]?>/gi, '\n');
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
        extensions: viewerExtensions(wrapper),
    });

    const editor = new SimpleEditorInterface(ev);
    editor.setMode(langName, content);

    elem.remove();
    addCopyIcon(ev);
}

/**
 * Add a button to a CodeMirror instance which copies the contents to the clipboard upon click.
 * @param {EditorView} editorView
 */
function addCopyIcon(editorView) {
    const copyIcon = '<svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>';
    const checkIcon = '<svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';
    const copyButton = document.createElement('button');
    copyButton.setAttribute('type', 'button');
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
 * Create a CodeMirror instance for showing inside the WYSIWYG editor.
 * Manages a textarea element to hold code content.
 * @param {HTMLElement} cmContainer
 * @param {ShadowRoot} shadowRoot
 * @param {String} content
 * @param {String} language
 * @returns {SimpleEditorInterface}
 */
export function wysiwygView(cmContainer, shadowRoot, content, language) {
    const ev = createView({
        parent: cmContainer,
        doc: content,
        extensions: viewerExtensions(cmContainer),
        root: shadowRoot,
    });

    const editor = new SimpleEditorInterface(ev);
    editor.setMode(language, content);

    return editor;
}

/**
 * Create a CodeMirror instance to show in the WYSIWYG pop-up editor
 * @param {HTMLElement} elem
 * @param {String} modeSuggestion
 * @returns {SimpleEditorInterface}
 */
export function popupEditor(elem, modeSuggestion) {
    const content = elem.textContent;
    const config = {
        parent: elem.parentElement,
        doc: content,
        extensions: [
            ...editorExtensions(elem.parentElement),
            EditorView.updateListener.of(v => {
                if (v.docChanged) {
                    // textArea.value = v.state.doc.toString();
                }
            }),
        ],
    };

    // Create editor, hide original input
    const editor = new SimpleEditorInterface(createView(config));
    editor.setMode(modeSuggestion, content);
    elem.style.display = 'none';

    return editor;
}

/**
 * Create an inline editor to replace the given textarea.
 * @param {HTMLTextAreaElement} textArea
 * @param {String} mode
 * @returns {SimpleEditorInterface}
 */
export function inlineEditor(textArea, mode) {
    const content = textArea.value;
    const config = {
        parent: textArea.parentElement,
        doc: content,
        extensions: [
            ...editorExtensions(textArea.parentElement),
            EditorView.updateListener.of(v => {
                if (v.docChanged) {
                    textArea.value = v.state.doc.toString();
                }
            }),
        ],
    };

    // Create editor view, hide original input
    const ev = createView(config);
    const editor = new SimpleEditorInterface(ev);
    editor.setMode(mode, content);
    textArea.style.display = 'none';

    return editor;
}

/**
 * Get a CodeMirror instance to use for the markdown editor.
 * @param {HTMLElement} elem
 * @param {function} onChange
 * @param {object} domEventHandlers
 * @param {Array} keyBindings
 * @returns {EditorView}
 */
export function markdownEditor(elem, onChange, domEventHandlers, keyBindings) {
    const content = elem.textContent;
    const config = {
        parent: elem.parentElement,
        doc: content,
        extensions: [
            keymap.of(keyBindings),
            ...editorExtensions(elem.parentElement),
            EditorView.updateListener.of(v => {
                onChange(v);
            }),
            EditorView.domEventHandlers(domEventHandlers),
        ],
    };

    // Emit a pre-event public event to allow tweaking of the configure before view creation.
    window.$events.emitPublic(elem, 'editor-markdown-cm6::pre-init', {editorViewConfig: config});

    // Create editor view, hide original input
    const ev = createView(config);
    (new SimpleEditorInterface(ev)).setMode('markdown', '');
    elem.style.display = 'none';

    return ev;
}
