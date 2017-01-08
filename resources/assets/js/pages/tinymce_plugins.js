
module.exports = function(tinymce) {
    "use strict";


tinymce.PluginManager.add('insertcode', function(editor, url) {
    let $ = editor.$;

    const defaultLanguages = [
        {text: 'Text', value: 'text'},
        {text: 'C', value: 'c'},
        {text: 'C#', value: 'csharp'},
        {text: 'C++', value: 'cpp'},
        {text: 'CSS', value: 'css'},
        {text: 'HTML', value: 'markup'},
        {text: 'Java', value: 'java'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'PHP', value: 'php'},
        {text: 'Python', value: 'python'},
        {text: 'Ruby', value: 'ruby'},
        {text: 'XML', value: 'markup'}
    ];

    function getCurrentCodeBox() {
        let currentNode = editor.selection.getNode();
        if (currentNode && currentNode.nodeName === 'PRE') return currentNode;
        return false;
    }

    function getCurrentLanguage() {
        let matches;
        let node = getCurrentCodeBox();
        if (!node) return false;
        if (node.className.indexOf('language-') === -1) return false;
        if (node) {
            matches = node.className.match(/language-(\w+)/);
            return matches ? matches[1] : 'text';
        }
        return '';
    }

    function colorize(elem) {
        let matches = elem.className.match(/language-(\w+)/);
        let mode = matches ? matches[1] : 'null';
        CodeMirror.requireMode(mode, () => {
            CodeMirror.colorize([elem], mode);
        });
    }

    editor.on('PreProcess', function(e) {
        $('pre', e.node).each(function(idx, elm) {
            let $elm = $(elm);
            let code = elm.textContent;

            $elm.attr('class', $.trim($elm.attr('class')));
            $elm.removeAttr('contentEditable');

            $elm.empty().append($('<pre></pre>').each(function() {
                // Needs to be textContent since innerText produces BR:s
                this.textContent = code;
            }));
        });
    });

    editor.on('undo', () => {
        $('pre').each((idx, elm) => {
            colorize(elm);
        })
    });

    editor.on('SetContent', function() {
        let unprocessedCodeSamples = $('pre').filter(function(idx, elm) {
            return elm.contentEditable !== "false";
        });

        if (unprocessedCodeSamples.length) {
            editor.undoManager.transact(function() {
                unprocessedCodeSamples.each(function(idx, elm) {

                    $(elm).find('br').each(function(idx, elm) {
                        elm.parentNode.replaceChild(editor.getDoc().createTextNode('\n'), elm);
                    });

                    elm.innerHTML = editor.dom.encode(elm.textContent);
                    elm.className = $.trim(elm.className);
                    elm.contentEditable = false;
                    colorize(elm);
                });
            });
        }
    });

    // Add a button that opens a window
    editor.addButton('insertcode', {
        text: 'My button',
        icon: false,
        onclick: function() {
            // Open window
            editor.windowManager.open({
                title: 'Example plugin',
                body: [
                    {
                        type: 'listbox',
                        name: 'language',
                        label: 'Language',
                        maxWidth: 600,
                        value: getCurrentLanguage(),
                        values: defaultLanguages
                    }
                ],
                onselect: function(e) {
                    let value = e.target.state.data.value;
                    console.log('CHANGE', value);
                    // Insert content when the window form is submitted
                }
            });
        }
    });
});

};
