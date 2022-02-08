function elemIsCodeBlock(elem) {
    return elem.className === 'CodeMirrorContainer';
}

function showPopup(editor) {
    const selectedNode = editor.selection.getNode();

    if (!elemIsCodeBlock(selectedNode)) {
        const providedCode = editor.selection.getNode().textContent;
        window.components.first('code-editor').open(providedCode, '', (code, lang) => {
            const wrap = document.createElement('div');
            wrap.innerHTML = `<pre><code class="language-${lang}"></code></pre>`;
            wrap.querySelector('code').innerText = code;

            editor.formatter.toggle('pre');
            const node = editor.selection.getNode();
            editor.dom.setHTML(node, wrap.querySelector('pre').innerHTML);
            editor.fire('SetContent');

            editor.focus()
        });
        return;
    }

    const lang = selectedNode.hasAttribute('data-lang') ? selectedNode.getAttribute('data-lang') : '';
    const currentCode = selectedNode.querySelector('textarea').textContent;

    window.components.first('code-editor').open(currentCode, lang, (code, lang) => {
        const editorElem = selectedNode.querySelector('.CodeMirror');
        const cmInstance = editorElem.CodeMirror;
        if (cmInstance) {
            window.importVersioned('code').then(Code => {
                Code.setContent(cmInstance, code);
                Code.setMode(cmInstance, lang, code);
            });
        }
        const textArea = selectedNode.querySelector('textarea');
        if (textArea) textArea.textContent = code;
        selectedNode.setAttribute('data-lang', lang);

        editor.focus()
    });
}

function codeMirrorContainerToPre(codeMirrorContainer) {
    const textArea = codeMirrorContainer.querySelector('textarea');
    const code = textArea.textContent;
    const lang = codeMirrorContainer.getAttribute('data-lang');

    codeMirrorContainer.removeAttribute('contentEditable');
    const pre = document.createElement('pre');
    const codeElem = document.createElement('code');
    codeElem.classList.add(`language-${lang}`);
    codeElem.textContent = code;
    pre.appendChild(codeElem);

    codeMirrorContainer.parentElement.replaceChild(pre, codeMirrorContainer);
}


/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {

    const $ = editor.$;

    editor.ui.registry.addIcon('codeblock', '<svg width="24" height="24"><path d="M4 3h16c.6 0 1 .4 1 1v16c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V4c0-.6.4-1 1-1Zm1 2v14h14V5Z"/><path d="M11.103 15.423c.277.277.277.738 0 .922a.692.692 0 0 1-1.106 0l-4.057-3.78a.738.738 0 0 1 0-1.107l4.057-3.872c.276-.277.83-.277 1.106 0a.724.724 0 0 1 0 1.014L7.6 12.012ZM12.897 8.577c-.245-.312-.2-.675.08-.955.28-.281.727-.27 1.027.033l4.057 3.78a.738.738 0 0 1 0 1.107l-4.057 3.872c-.277.277-.83.277-1.107 0a.724.724 0 0 1 0-1.014l3.504-3.412z"/></svg>')

    editor.ui.registry.addButton('codeeditor', {
        tooltip: 'Insert code block',
        icon: 'codeblock',
        onAction() {
            editor.execCommand('codeeditor');
        }
    });

    editor.addCommand('codeeditor', () => {
        showPopup(editor);
    });

    // Convert
    editor.on('PreProcess', function (e) {
        $('div.CodeMirrorContainer', e.node).each((index, elem) => {
            codeMirrorContainerToPre(elem);
        });
    });

    editor.on('dblclick', event => {
        let selectedNode = editor.selection.getNode();
        if (!elemIsCodeBlock(selectedNode)) return;
        showPopup(editor);
    });

    function parseCodeMirrorInstances(Code) {

        // Recover broken codemirror instances
        $('.CodeMirrorContainer').filter((index ,elem) => {
            return typeof elem.querySelector('.CodeMirror').CodeMirror === 'undefined';
        }).each((index, elem) => {
            codeMirrorContainerToPre(elem);
        });

        const codeSamples = $('body > pre').filter((index, elem) => {
            return elem.contentEditable !== "false";
        });

        codeSamples.each((index, elem) => {
            Code.wysiwygView(elem);
        });
    }

    editor.on('init', async function() {
        const Code = await window.importVersioned('code');
        // Parse code mirror instances on init, but delay a little so this runs after
        // initial styles are fetched into the editor.
        editor.undoManager.transact(function () {
            parseCodeMirrorInstances(Code);
        });
        // Parsed code mirror blocks when content is set but wait before setting this handler
        // to avoid any init 'SetContent' events.
        setTimeout(() => {
            editor.on('SetContent', () => {
                setTimeout(() => parseCodeMirrorInstances(Code), 100);
            });
        }, 200);
    });
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}