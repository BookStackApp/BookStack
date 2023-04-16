function elemIsCodeBlock(elem) {
    return elem.tagName.toLowerCase() === 'code-block';
}

/**
 * @param {Editor} editor
 * @param {String} code
 * @param {String} language
 * @param {function(string, string)} callback (Receives (code: string,language: string)
 */
function showPopup(editor, code, language, callback) {
    window.$components.first('code-editor').open(code, language, (newCode, newLang) => {
        callback(newCode, newLang)
        editor.focus()
    });
}

/**
 * @param {Editor} editor
 * @param {CodeBlockElement} codeBlock
 */
function showPopupForCodeBlock(editor, codeBlock) {
    showPopup(editor, codeBlock.getContent(), codeBlock.getLanguage(), (newCode, newLang) => {
        codeBlock.setContent(newCode, newLang);
    });
}

/**
 * Define our custom code-block HTML element that we use.
 * Needs to be delayed since it needs to be defined within the context of the
 * child editor window and document, hence its definition within a callback.
 * @param {Editor} editor
 */
function defineCodeBlockCustomElement(editor) {
    const doc = editor.getDoc();
    const win = doc.defaultView;

    class CodeBlockElement extends win.HTMLElement {
        constructor() {
            super();
            this.attachShadow({mode: 'open'});

            const stylesToCopy = document.querySelectorAll('link[rel="stylesheet"]:not([media="print"])');
            const copiedStyles = Array.from(stylesToCopy).map(styleEl => styleEl.cloneNode(false));

            const cmContainer = document.createElement('div');
            cmContainer.style.pointerEvents = 'none';
            cmContainer.contentEditable = 'false';
            cmContainer.classList.add('CodeMirrorContainer');

            this.shadowRoot.append(...copiedStyles, cmContainer);
        }

        getLanguage() {
            const getLanguageFromClassList = (classes) => {
                const langClasses = classes.split(' ').filter(cssClass => cssClass.startsWith('language-'));
                return (langClasses[0] || '').replace('language-', '');
            };

            const code = this.querySelector('code');
            const pre = this.querySelector('pre');
            return getLanguageFromClassList(pre.className) || (code && getLanguageFromClassList(code.className)) || '';
        }

        setContent(content, language) {
            if (this.cm) {
                importVersioned('code').then(Code => {
                    Code.setContent(this.cm, content);
                    Code.setMode(this.cm, language, content);
                });
            }

            let pre = this.querySelector('pre');
            if (!pre) {
                pre = doc.createElement('pre');
                this.append(pre);
            }
            pre.innerHTML = '';

            const code = doc.createElement('code');
            pre.append(code);
            code.innerText = content;
            code.className = `language-${language}`;
        }

        getContent() {
            const code = this.querySelector('code') || this.querySelector('pre');
            const tempEl = document.createElement('pre');
            tempEl.innerHTML = code.innerHTML.replace(/\ufeff/g, '');

            const brs = tempEl.querySelectorAll('br');
            for (const br of brs) {
                br.replaceWith('\n');
            }

            return tempEl.textContent;
        }

        connectedCallback() {
            const connectedTime = Date.now();
            if (this.cm) {
                return;
            }

            this.cleanChildContent();
            const content = this.getContent();
            const lines = content.split('\n').length;
            const height = (lines * 19.2) + 18 + 24;
            this.style.height = `${height}px`;

            const container = this.shadowRoot.querySelector('.CodeMirrorContainer');
            const renderCodeMirror = (Code) => {
                this.cm = Code.wysiwygView(container, this.shadowRoot, content, this.getLanguage());
                setTimeout(() => this.style.height = null, 12);
            };

            window.importVersioned('code').then((Code) => {
                const timeout = (Date.now() - connectedTime < 20) ? 20 : 0;
                setTimeout(() => renderCodeMirror(Code), timeout);
            });
        }

        cleanChildContent() {
            const pre = this.querySelector('pre');
            if (!pre) return;

            for (const preChild of pre.childNodes) {
                if (preChild.nodeName === '#text' && preChild.textContent === '﻿') {
                    preChild.remove();
                }
            }
        }
    }

    win.customElements.define('code-block', CodeBlockElement);
}


/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {

    editor.ui.registry.addIcon('codeblock', '<svg width="24" height="24"><path d="M4 3h16c.6 0 1 .4 1 1v16c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V4c0-.6.4-1 1-1Zm1 2v14h14V5Z"/><path d="M11.103 15.423c.277.277.277.738 0 .922a.692.692 0 0 1-1.106 0l-4.057-3.78a.738.738 0 0 1 0-1.107l4.057-3.872c.276-.277.83-.277 1.106 0a.724.724 0 0 1 0 1.014L7.6 12.012ZM12.897 8.577c-.245-.312-.2-.675.08-.955.28-.281.727-.27 1.027.033l4.057 3.78a.738.738 0 0 1 0 1.107l-4.057 3.872c-.277.277-.83.277-1.107 0a.724.724 0 0 1 0-1.014l3.504-3.412z"/></svg>')

    editor.ui.registry.addButton('codeeditor', {
        tooltip: 'Insert code block',
        icon: 'codeblock',
        onAction() {
            editor.execCommand('codeeditor');
        }
    });

    editor.ui.registry.addButton('editcodeeditor', {
        tooltip: 'Edit code block',
        icon: 'edit-block',
        onAction() {
            editor.execCommand('codeeditor');
        }
    });

    editor.addCommand('codeeditor', () => {
        const selectedNode = editor.selection.getNode();
        const doc = selectedNode.ownerDocument;
        if (elemIsCodeBlock(selectedNode)) {
            showPopupForCodeBlock(editor, selectedNode);
        } else {
            const textContent = editor.selection.getContent({format: 'text'});
            showPopup(editor, textContent, '', (newCode, newLang) => {
                const pre = doc.createElement('pre');
                const code = doc.createElement('code');
                code.classList.add(`language-${newLang}`);
                code.innerText = newCode;
                pre.append(code);

                editor.insertContent(pre.outerHTML);
            });
        }
    });

    editor.on('dblclick', event => {
        let selectedNode = editor.selection.getNode();
        if (elemIsCodeBlock(selectedNode)) {
            showPopupForCodeBlock(editor, selectedNode);
        }
    });

    editor.on('PreInit', () => {
        editor.parser.addNodeFilter('pre', function(elms) {
            for (const el of elms) {
                const wrapper = tinymce.html.Node.create('code-block', {
                    contenteditable: 'false',
                });

                const spans = el.getAll('span');
                for (const span of spans) {
                    span.unwrap();
                }
                el.attr('style', null);
                el.wrap(wrapper);
            }
        });

        editor.parser.addNodeFilter('code-block', function(elms) {
            for (const el of elms) {
                el.attr('contenteditable', 'false');
            }
        });

        editor.serializer.addNodeFilter('code-block', function(elms) {
            for (const el of elms) {
                el.unwrap();
            }
        });
    });

    editor.ui.registry.addContextToolbar('codeeditor', {
        predicate: function (node) {
            return node.nodeName.toLowerCase() === 'code-block';
        },
        items: 'editcodeeditor',
        position: 'node',
        scope: 'node'
    });

    editor.on('PreInit', () => {
        defineCodeBlockCustomElement(editor);
    });
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}