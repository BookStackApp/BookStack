
<script src="{{ baseUrl('/libs/codemirror/lib/codemirror.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/addon/mode/loadmode.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/addon/search/searchcursor.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/addon/edit/matchbrackets.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/addon/runmode/runmode.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/addon/runmode/colorize.js') }}"></script>
<script src="{{ baseUrl('/libs/codemirror/keymap/sublime.js') }}"></script>
<script>
    var preTags = document.querySelectorAll('pre');

    CodeMirror.modeURL = window.baseUrl('/libs/codemirror/mode/%N/%N.js');

    for (var i = 0; i < preTags.length ;i++) {
        loadCodeMirror(preTags[i])
    }

    function loadCodeMirror(preElem) {
        console.log(preElem);
        var matches = preElem.className.match(/language-(\w+)/);
        var mode = matches ? matches[1] : 'null';
        var editor = CodeMirror(function(elt) {
            preElem.parentNode.replaceChild(elt, preElem);
        }, {
            value: preElem.textContent,
            keyMap: "sublime",
            lineNumbers: true,
            readOnly: true
        });
        editor.setOption('mode', mode);
        if (mode !== 'null') {
            console.log(mode);
            CodeMirror.autoLoadMode(editor, mode);
        }
        return editor;
    }

</script>