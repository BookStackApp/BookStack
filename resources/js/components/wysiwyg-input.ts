import {Component} from './component';
import {el} from "../wysiwyg/utils/dom";
import {SimpleWysiwygEditorInterface} from "../wysiwyg";

export class WysiwygInput extends Component {
    private elem!: HTMLTextAreaElement;
    private wysiwygEditor!: SimpleWysiwygEditorInterface;
    private textDirection!: string;

    async setup() {
        this.elem = this.$el as HTMLTextAreaElement;
        this.textDirection = this.$opts.textDirection;

        type WysiwygModule = typeof import('../wysiwyg');
        const wysiwygModule = (await window.importVersioned('wysiwyg')) as WysiwygModule;
        const container = el('div', {class: 'basic-editor-container'});
        this.elem.parentElement?.appendChild(container);
        this.elem.hidden = true;

        this.wysiwygEditor = wysiwygModule.createBasicEditorInstance(container as HTMLElement, this.elem.value, {
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.textDirection,
            translations: (window as unknown as Record<string, Object>).editor_translations,
        });

        this.wysiwygEditor.onChange(() => {
            this.wysiwygEditor.getContentAsHtml().then(html => {
                this.elem.value = html;
            });
        });
    }
}
