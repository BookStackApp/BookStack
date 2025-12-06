import {EditorUiContext} from "../ui/framework/core";
import {appendHtmlToEditor, insertHtmlIntoEditor, prependHtmlToEditor} from "../utils/actions";


export class EditorApiContentModule {
    readonly #context: EditorUiContext;

    constructor(context: EditorUiContext) {
        this.#context = context;
    }

    insertHtml(html: string, position: string = 'selection'): void {
        const validPositions = ['start', 'end', 'selection'];
        if (!validPositions.includes(position)) {
            throw new Error(`Invalid position: ${position}. Valid positions are: ${validPositions.join(', ')}`);
        }

        if (position === 'start') {
            prependHtmlToEditor(this.#context.editor, html);
        } else if (position === 'end') {
            appendHtmlToEditor(this.#context.editor, html);
        } else {
            insertHtmlIntoEditor(this.#context.editor, html);
        }
    }
}