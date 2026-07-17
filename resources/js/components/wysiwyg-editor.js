import {Component} from './component';

export class WysiwygEditor extends Component {

    setup() {
        this.elem = this.$el;
        this.editContainer = this.$refs.editContainer;
        this.input = this.$refs.input;

        /** @var {SimpleWysiwygEditorInterface|null} */
        this.editor = null;

        const translations = {
            ...window.editor_translations,
            imageUploadErrorText: this.$opts.imageUploadErrorText,
            serverUploadLimitText: this.$opts.serverUploadLimitText,
        };

        window.importVersioned('wysiwyg').then(wysiwyg => {
            const editorContent = this.input.value;
            this.editor = wysiwyg.createPageEditorInstance(this.editContainer, editorContent, {
                drawioUrl: this.getDrawIoUrl(),
                pageId: Number(this.$opts.pageId),
                darkMode: document.documentElement.classList.contains('dark-mode'),
                textDirection: this.$opts.textDirection,
                translations,
            });
            window.wysiwyg = this.editor;
        });

        let handlingFormSubmit = false;
        this.input.form.addEventListener('submit', event => {
            if (!this.editor) {
                return;
            }

            if (!handlingFormSubmit) {
                event.preventDefault();
                handlingFormSubmit = true;
                this.editor.getContentAsHtml().then(html => {
                    this.input.value = html;
                    setTimeout(() => {
                        this.input.form.requestSubmit();
                    }, 5);
                });
            } else {
                handlingFormSubmit = false;
            }
        });
    }

    getDrawIoUrl() {
        const drawioUrlElem = document.querySelector('[drawio-url]');
        if (drawioUrlElem) {
            return drawioUrlElem.getAttribute('drawio-url');
        }
        return '';
    }

    /**
     * Get the content of this editor.
     * Used by the parent page editor component.
     * @return {Promise<{html: String}>}
     */
    async getContent() {
        return {
            html: await this.editor.getContentAsHtml(),
        };
    }

}
