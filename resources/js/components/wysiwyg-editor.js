import {Component} from './component';

export class WysiwygEditor extends Component {

    setup() {
        this.elem = this.$el;
        this.editArea = this.$refs.editArea;

        window.importVersioned('wysiwyg').then(wysiwyg => {
            wysiwyg.createPageEditorInstance(this.editArea);
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
