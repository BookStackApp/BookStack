import {Component} from './component';
import {buildForInput} from '../wysiwyg/config';

export class WysiwygInput extends Component {

    setup() {
        this.elem = this.$el;

        const config = buildForInput({
            language: this.$opts.language,
            containerElement: this.elem,
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.textDirection,
            translations: {
                imageUploadErrorText: this.$opts.imageUploadErrorText,
                serverUploadLimitText: this.$opts.serverUploadLimitText,
            },
            translationMap: window.editor_translations,
        });

        window.tinymce.init(config).then(editors => {
            this.editor = editors[0];
        });
    }

}
