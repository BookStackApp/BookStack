import {Component} from './component';
import {buildForInput} from '../wysiwyg/config';

export class WysiwygInput extends Component {

    setup() {
        this.elem = this.$el;

        const config = buildForInput({
            language: this.$opts.language,
            containerElement: this.elem,
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.$opts.textDirection,
            translations: {},
            translationMap: window.editor_translations,
        });

        window.tinymce.init(config).then(editors => {
            this.editor = editors[0];
        });
    }

}
