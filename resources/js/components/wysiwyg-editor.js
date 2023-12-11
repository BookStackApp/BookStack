import {build as buildEditorConfig} from '../wysiwyg/config';
import {Component} from './component';

export class WysiwygEditor extends Component {

    setup() {
        this.elem = this.$el;

        this.pageId = this.$opts.pageId;
        this.textDirection = this.$opts.textDirection;
        this.isDarkMode = document.documentElement.classList.contains('dark-mode');

        this.tinyMceConfig = buildEditorConfig({
            language: this.$opts.language,
            containerElement: this.elem,
            darkMode: this.isDarkMode,
            textDirection: this.textDirection,
            drawioUrl: this.getDrawIoUrl(),
            pageId: Number(this.pageId),
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
     * @return {{html: String}}
     */
    getContent() {
        return {
            html: this.editor.getContent(),
        };
    }

}
