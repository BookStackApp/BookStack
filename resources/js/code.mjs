import CodeMirror from "codemirror";
import Clipboard from "clipboard/dist/clipboard.min";

// Modes
import 'codemirror/mode/css/css';
import 'codemirror/mode/clike/clike';
import 'codemirror/mode/diff/diff';
import 'codemirror/mode/fortran/fortran';
import 'codemirror/mode/go/go';
import 'codemirror/mode/haskell/haskell';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/julia/julia';
import 'codemirror/mode/lua/lua';
import 'codemirror/mode/markdown/markdown';
import 'codemirror/mode/mllike/mllike';
import 'codemirror/mode/nginx/nginx';
import 'codemirror/mode/perl/perl';
import 'codemirror/mode/pascal/pascal';
import 'codemirror/mode/php/php';
import 'codemirror/mode/powershell/powershell';
import 'codemirror/mode/properties/properties';
import 'codemirror/mode/python/python';
import 'codemirror/mode/ruby/ruby';
import 'codemirror/mode/rust/rust';
import 'codemirror/mode/shell/shell';
import 'codemirror/mode/sql/sql';
import 'codemirror/mode/stex/stex';
import 'codemirror/mode/toml/toml';
import 'codemirror/mode/vb/vb';
import 'codemirror/mode/vbscript/vbscript';
import 'codemirror/mode/xml/xml';
import 'codemirror/mode/yaml/yaml';

// Addons
import 'codemirror/addon/scroll/scrollpastend';

// Mapping of possible languages or formats from user input to their codemirror modes.
// Value can be a mode string or a function that will receive the code content & return the mode string.
// The function option is used in the event the exact mode could be dynamic depending on the code.
const modeMap = {
    css: 'css',
    c: 'text/x-csrc',
    java: 'text/x-java',
    scala: 'text/x-scala',
    kotlin: 'text/x-kotlin',
    'c++': 'text/x-c++src',
    'c#': 'text/x-csharp',
    csharp: 'text/x-csharp',
    diff: 'diff',
    for: 'fortran',
    fortran: 'fortran',
    'f#': 'text/x-fsharp',
    fsharp: 'text/x-fsharp',
    go: 'go',
    haskell: 'haskell',
    hs: 'haskell',
    html: 'htmlmixed',
    ini: 'properties',
    javascript: 'text/javascript',
    json: 'application/json',
    js: 'text/javascript',
    jl: 'text/x-julia',
    julia: 'text/x-julia',
    latex: 'text/x-stex',
    lua: 'lua',
    md: 'markdown',
    mdown: 'markdown',
    markdown: 'markdown',
    ml: 'mllike',
    nginx: 'nginx',
    perl: 'perl',
    pl: 'perl',
    powershell: 'powershell',
    properties: 'properties',
    ocaml: 'text/x-ocaml',
    pascal: 'text/x-pascal',
    pas: 'text/x-pascal',
    php: (content) => {
        return content.includes('<?php') ? 'php' : 'text/x-php';
    },
    py: 'python',
    python: 'python',
    ruby: 'ruby',
    rust: 'rust',
    rb: 'ruby',
    rs: 'rust',
    shell: 'shell',
    sh: 'shell',
    stext: 'text/x-stex',
    bash: 'shell',
    toml: 'toml',
    ts: 'text/typescript',
    typescript: 'text/typescript',
    sql: 'text/x-sql',
    vbs: 'vbscript',
    vbscript: 'vbscript',
    'vb.net': 'text/x-vb',
    vbnet: 'text/x-vb',
    xml: 'xml',
    yaml: 'yaml',
    yml: 'yaml',
};

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

    let mode = '';
    if (innerCodeElem !== null) {
        const langName = innerCodeElem.className.replace('language-', '');
        mode = getMode(langName, content);
    }

    const cm = CodeMirror(function(elt) {
        elem.parentNode.replaceChild(elt, elem);
    }, {
        value: content,
        mode:  mode,
        lineNumbers: true,
        lineWrapping: false,
        theme: getTheme(),
        readOnly: true
    });

    addCopyIcon(cm);
}

/**
 * Add a button to a CodeMirror instance which copies the contents to the clipboard upon click.
 * @param cmInstance
 */
function addCopyIcon(cmInstance) {
    const copyIcon = `<svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>`;
    const copyButton = document.createElement('div');
    copyButton.classList.add('CodeMirror-copy');
    copyButton.innerHTML = copyIcon;
    cmInstance.display.wrapper.appendChild(copyButton);

    const clipboard = new Clipboard(copyButton, {
        text: function(trigger) {
            return cmInstance.getValue()
        }
    });

    clipboard.on('success', event => {
        copyButton.classList.add('success');
        setTimeout(() => {
            copyButton.classList.remove('success');
        }, 240);
    });
}

/**
 * Search for a codemirror code based off a user suggestion
 * @param {String} suggestion
 * @param {String} content
 * @returns {string}
 */
function getMode(suggestion, content) {
    suggestion = suggestion.trim().replace(/^\./g, '').toLowerCase();

    const modeMapType = typeof modeMap[suggestion];

    if (modeMapType === 'undefined') {
        return '';
    }

    if (modeMapType === 'function') {
        return modeMap[suggestion](content);
    }

    return modeMap[suggestion];
}

/**
 * Ge the theme to use for CodeMirror instances.
 * @returns {*|string}
 */
function getTheme() {
    const darkMode = document.documentElement.classList.contains('dark-mode');
    return window.codeTheme || (darkMode ? 'darcula' : 'default');
}

/**
 * Create a CodeMirror instance for showing inside the WYSIWYG editor.
 *  Manages a textarea element to hold code content.
 * @param {HTMLElement} cmContainer
 * @param {String} content
 * @param {String} language
 * @returns {{wrap: Element, editor: *}}
 */
export function wysiwygView(cmContainer, content, language) {
    return CodeMirror(cmContainer, {
        value: content,
        mode: getMode(language, content),
        lineNumbers: true,
        lineWrapping: false,
        theme: getTheme(),
        readOnly: true
    });
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
 * @returns {CodeMirror3}
 */
export function inlineEditor(textArea, mode) {
    return CodeMirror.fromTextArea(textArea, {
        mode: getMode(mode, textArea.value),
        lineNumbers: true,
        lineWrapping: false,
        theme: getTheme(),
    });
}

/**
 * Set the mode of a codemirror instance.
 * @param cmInstance
 * @param modeSuggestion
 */
export function setMode(cmInstance, modeSuggestion, content) {
      cmInstance.setOption('mode', getMode(modeSuggestion, content));
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
 * @returns {*}
 */
export function markdownEditor(elem) {
    const content = elem.textContent;
    const config = {
        value: content,
        mode: "markdown",
        lineNumbers: true,
        lineWrapping: true,
        theme: getTheme(),
        scrollPastEnd: true,
    };

    window.$events.emitPublic(elem, 'editor-markdown-cm::pre-init', {config});

    return CodeMirror(function (elt) {
        elem.parentNode.insertBefore(elt, elem);
        elem.style.display = 'none';
    }, config);
}

/**
 * Get the 'meta' key dependent on the user's system.
 * @returns {string}
 */
export function getMetaKey() {
    let mac = CodeMirror.keyMap["default"] == CodeMirror.keyMap.macDefault;
    return mac ? "Cmd" : "Ctrl";
}