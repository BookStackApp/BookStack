import {Component} from './component';

export class WysiwygEditor extends Component {

    setup() {
        this.elem = this.$el;
        this.editContainer = this.$refs.editContainer;
        this.editContent = this.$refs.editContent;

        window.importVersioned('wysiwyg').then(wysiwyg => {
            const editorContent = this.editContent.textContent;
            wysiwyg.createPageEditorInstance(this.editContainer, editorContent);
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
        // TODO - Update
        return {
            html: this.editor.getContent(),
        };
    }

}
