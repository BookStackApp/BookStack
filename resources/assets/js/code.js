require('codemirror/mode/css/css');
require('codemirror/mode/clike/clike');
require('codemirror/mode/go/go');
require('codemirror/mode/htmlmixed/htmlmixed');
require('codemirror/mode/javascript/javascript');
require('codemirror/mode/markdown/markdown');
require('codemirror/mode/nginx/nginx');
require('codemirror/mode/php/php');
require('codemirror/mode/powershell/powershell');
require('codemirror/mode/python/python');
require('codemirror/mode/ruby/ruby');
require('codemirror/mode/shell/shell');
require('codemirror/mode/sql/sql');
require('codemirror/mode/toml/toml');
require('codemirror/mode/xml/xml');
require('codemirror/mode/yaml/yaml');

const CodeMirror = require('codemirror');

const modeMap = {
    css: 'css',
    c: 'clike',
    java: 'clike',
    scala: 'clike',
    kotlin: 'clike',
    'c++': 'clike',
    'c#': 'clike',
    csharp: 'clike',
    go: 'go',
    html: 'htmlmixed',
    javascript: 'javascript',
    json: {name: 'javascript', json: true},
    js: 'javascript',
    php: 'php',
    md: 'markdown',
    mdown: 'markdown',
    markdown: 'markdown',
    nginx: 'nginx',
    powershell: 'powershell',
    py: 'python',
    python: 'python',
    ruby: 'ruby',
    rb: 'ruby',
    shell: 'shell',
    bash: 'shell',
    toml: 'toml',
    sql: 'sql',
    xml: 'xml',
    yaml: 'yaml',
    yml: 'yaml',
};

module.exports.highlight = function() {
    let codeBlocks = document.querySelectorAll('.page-content pre');
    for (let i = 0; i < codeBlocks.length; i++) {
        highlightElem(codeBlocks[i]);
    }
};

function highlightElem(elem) {
    let innerCodeElem = elem.querySelector('code[class^=language-]');
    let mode = '';
    if (innerCodeElem !== null) {
        let langName = innerCodeElem.className.replace('language-', '');
        if (typeof modeMap[langName] !== 'undefined') mode = modeMap[langName];
    }
    elem.innerHTML = elem.innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
    let content = elem.textContent;

    CodeMirror(function(elt) {
        elem.parentNode.replaceChild(elt, elem);
    }, {
        value: content,
        mode:  mode,
        lineNumbers: true,
        theme: 'base16-light',
        readOnly: true
    });
}

module.exports.highlightElem = highlightElem;

module.exports.wysiwygView = function(elem) {
    let doc = elem.ownerDocument;
    elem.innerHTML = elem.innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
    let content = elem.textContent;
    let newWrap = doc.createElement('div');
    let newTextArea = doc.createElement('textarea');

    newWrap.className = 'CodeMirrorContainer';
    newTextArea.style.display = 'none';
    elem.parentNode.replaceChild(newWrap, elem);

    newWrap.appendChild(newTextArea);
    newWrap.contentEditable = false;
    newTextArea.textContent = content;

    let cm = CodeMirror(function(elt) {
        newWrap.appendChild(elt);
    }, {
        value: content,
        mode:  '',
        lineNumbers: true,
        theme: 'base16-light',
        readOnly: true
    });
    setTimeout(() => {
        cm.refresh();
    }, 300);
    return newWrap;
};

// module.exports.wysiwygEditor = function(elem) {
//     let doc = elem.ownerDocument;
//     let newWrap = doc.createElement('div');
//     newWrap.className = 'CodeMirrorContainer';
//     let newTextArea = doc.createElement('textarea');
//     newTextArea.style.display = 'none';
//     elem.innerHTML = elem.innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
//     let content = elem.textContent;
//     elem.parentNode.replaceChild(newWrap, elem);
//     newWrap.appendChild(newTextArea);
//     let cm = CodeMirror(function(elt) {
//         newWrap.appendChild(elt);
//     }, {
//         value: content,
//         mode:  '',
//         lineNumbers: true,
//         theme: 'base16-light',
//         readOnly: true
//     });
//     cm.on('change', event => {
//         newTextArea.innerText = cm.getValue();
//     });
//     setTimeout(() => {
//         cm.refresh();
//     }, 300);
// };

module.exports.markdownEditor = function(elem) {
    let content = elem.textContent;

    let cm = CodeMirror(function(elt) {
        elem.parentNode.insertBefore(elt, elem);
        elem.style.display = 'none';
    }, {
        value: content,
        mode:  "markdown",
        lineNumbers: true,
        theme: 'base16-light',
        lineWrapping: true
    });
    return cm;

};

