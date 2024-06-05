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

    abstract render(context: EditorUiContext): HTMLElement;

}