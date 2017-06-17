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
        let innerCodeElem = codeBlocks[i].querySelector('code[class^=language-]');
        let mode = '';
        if (innerCodeElem !== null) {
            let langName = innerCodeElem.className.replace('language-', '');
            if (typeof modeMap[langName] !== 'undefined') mode = modeMap[langName];
        }
        codeBlocks[i].innerHTML = codeBlocks[i].innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
        let content = codeBlocks[i].textContent;
        console.log('MODE', mode);

        CodeMirror(function(elt) {
            codeBlocks[i].parentNode.replaceChild(elt, codeBlocks[i]);
        }, {
            value: content,
            mode:  mode,
            lineNumbers: true,
            theme: 'base16-light',
            readOnly: true
        });
    }

};

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

