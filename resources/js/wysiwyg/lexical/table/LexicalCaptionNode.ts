import {
    $createTextNode,
    DOMConversionMap,
    DOMExportOutput,
    EditorConfig,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode
} from "lexical";
import {TableNode} from "@lexical/table/LexicalTableNode";


export class CaptionNode extends ElementNode {
    static getType(): string {
        return 'caption';
    }

    static clone(node: CaptionNode): CaptionNode {
        return new CaptionNode(node.__key);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor): HTMLElement {
        return document.createElement('caption');
    }

    updateDOM(_prevNode: unknown, _dom: HTMLElement, _config: EditorConfig): boolean {
        return false;
    }

    isParentRequired(): true {
        return true;
    }

    canBeEmpty(): boolean {
        return false;
    }

    exportJSON(): SerializedElementNode {
        return {
            ...super.exportJSON(),
            type: 'caption',
            version: 1,
        };
    }

    insertDOMIntoParent(nodeDOM: HTMLElement, parentDOM: HTMLElement): boolean {
        parentDOM.insertBefore(nodeDOM, parentDOM.firstChild);
        return true;
    }

    static importJSON(serializedNode: SerializedElementNode): CaptionNode {
        return $createCaptionNode();
    }

    static importDOM(): DOMConversionMap | null {
        return {
            caption: (node: Node) => ({
                conversion(domNode: Node) {
                    return {
                        node: $createCaptionNode(),
                    }
                },
                priority: 0,
            }),
        };
    }
}

export function $createCaptionNode(): CaptionNode {
    return new CaptionNode();
}

export function $isCaptionNode(node: LexicalNode | null | undefined): node is CaptionNode {
    return node instanceof CaptionNode;
}

export function $tableHasCaption(table: TableNode): boolean {
    for (const child of table.getChildren()) {
        if ($isCaptionNode(child)) {
            return true;
        }
    }
    return false;
}

export function $addCaptionToTable(table: TableNode, text: string = ''): void {
    const caption = $createCaptionNode();
    const textNode = $createTextNode(text || ' ');
    caption.append(textNode);
    table.append(caption);
}