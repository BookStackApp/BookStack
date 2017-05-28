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

module.exports.highlight = function() {
    let codeBlocks = document.querySelectorAll('.page-content pre');

    for (let i = 0; i < codeBlocks.length; i++) {
        codeBlocks[i].innerHTML = codeBlocks[i].innerHTML.replace(/<br\s*[\/]?>/gi ,'\n');
        let content = codeBlocks[i].textContent;

        CodeMirror(function(elt) {
            codeBlocks[i].parentNode.replaceChild(elt, codeBlocks[i]);
        }, {
            value: content,
            mode:  "",
            lineNumbers: true,
            theme: 'base16-light',
            readOnly: true
        });
    }

};

