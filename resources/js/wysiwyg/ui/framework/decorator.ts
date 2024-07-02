import {EditorUiContext} from "./core";
import {LexicalNode} from "lexical";

export interface EditorDecoratorAdapter {
    type: string;
    getNode(): LexicalNode;
}

export abstract class EditorDecorator {

    protected node: LexicalNode | null = null;
    protected context: EditorUiContext;

    constructor(context: EditorUiContext) {
        this.context = context;
    }

    protected getNode(): LexicalNode {
        if (!this.node) {
            throw new Error('Attempted to get use node without it being set');
        }

        return this.node;
    }

    setNode(node: LexicalNode) {
        this.node = node;
    }

    /**
     * Render the decorator.
     * Can run on both creation and update for a node decorator.
     * If an element is returned, this will be appended to the element
     * that is being decorated.
     */
    abstract render(context: EditorUiContext, decorated: HTMLElement): HTMLElement|void;

}