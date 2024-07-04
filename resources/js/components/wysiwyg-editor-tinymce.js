import {buildForEditor as buildEditorConfig} from '../wysiwyg-tinymce/config';
import {Component} from './component';

export class WysiwygEditorTinymce extends Component {

    setup() {
        this.elem = this.$el;

        this.tinyMceConfig = buildEditorConfig({
            language: this.$opts.language,
            containerElement: this.elem,
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.$opts.textDirection,
            drawioUrl: this.getDrawIoUrl(),
            pageId: Number(this.$opts.pageId),
            translations: {
                imageUploadErrorText: this.$opts.imageUploadErrorText,
                serverUploadLimitText: this.$opts.serverUploadLimitText,
            },
            translationMap: window.editor_translations,
        });

        window.$events.emitPublic(this.elem, 'editor-tinymce::pre-init', {config: this.tinyMceConfig});
        window.tinymce.init(this.tinyMceConfig).then(editors => {
            this.editor = editors[0];
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
            html: this.editor.getContent(),
        };
    }

}
