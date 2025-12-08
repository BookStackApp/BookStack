import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    type EditorConfig,
    ElementNode,
    LexicalEditor, LexicalNode,
    SerializedElementNode,
    Spread
} from "lexical";

export type SerializedMentionNode = Spread<{
    user_id: number;
    user_name: string;
    user_slug: string;
}, SerializedElementNode>

export class MentionNode extends ElementNode {
    __user_id: number = 0;
    __user_name: string = '';
    __user_slug: string = '';

    static getType(): string {
        return 'mention';
    }

    static clone(node: MentionNode): MentionNode {
        const newNode = new MentionNode(node.__key);
        newNode.__user_id = node.__user_id;
        newNode.__user_name = node.__user_name;
        newNode.__user_slug = node.__user_slug;
        return newNode;
    }

    setUserDetails(userId: number, userName: string, userSlug: string): void {
        const self = this.getWritable();
        self.__user_id = userId;
        self.__user_name = userName;
        self.__user_slug = userSlug;
    }

    isInline(): boolean {
        return true;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const element = document.createElement('a');
        element.setAttribute('target', '_blank');
        element.setAttribute('href', window.baseUrl('/users/' + this.__user_slug));
        element.setAttribute('data-user-mention-id', String(this.__user_id));
        element.textContent = '@' + this.__user_name;
        return element;
    }

    updateDOM(prevNode: MentionNode): boolean {
        return prevNode.__user_id !== this.__user_id;
    }

    static importDOM(): DOMConversionMap|null {
        return {
            a(node: HTMLElement): DOMConversion|null {
                if (node.hasAttribute('data-user-mention-id')) {
                    return {
                        conversion: (element: HTMLElement): DOMConversionOutput|null => {
                            const node = new MentionNode();
                            node.setUserDetails(
                                Number(element.getAttribute('data-user-mention-id') || '0'),
                                element.innerText.replace(/^@/, ''),
                                element.getAttribute('href')?.split('/user/')[1] || ''
                            );

                            return {
                                node,
                            };
                        },
                        priority: 4,
                    };
                }
                return null;
            },
        };
    }

    exportJSON(): SerializedMentionNode {
        return {
            ...super.exportJSON(),
            type: 'mention',
            version: 1,
            user_id: this.__user_id,
            user_name: this.__user_name,
            user_slug: this.__user_slug,
        };
    }

    static importJSON(serializedNode: SerializedMentionNode): MentionNode {
        return $createMentionNode(serializedNode.user_id, serializedNode.user_name, serializedNode.user_slug);
    }
}

export function $createMentionNode(userId: number, userName: string, userSlug: string) {
    const node = new MentionNode();
    node.setUserDetails(userId, userName, userSlug);
    return node;
}

export function $isMentionNode(node: LexicalNode | null | undefined): node is MentionNode {
    return node instanceof MentionNode;
}