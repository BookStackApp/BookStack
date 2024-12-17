import {
    createTestContext, destroyFromContext,
    dispatchKeydownEventForNode, expectNodeShapeToMatch,
} from "lexical/__tests__/utils";
import {
    $createParagraphNode, $getRoot, LexicalEditor, LexicalNode,
    ParagraphNode,
} from "lexical";
import {$createDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {EditorUiContext} from "../../ui/framework/core";
import {$htmlToBlockNodes} from "../nodes";
import {ListItemNode, ListNode} from "@lexical/list";
import {$nestListItem, $unnestListItem} from "../lists";

describe('List Utils', () => {

    let context!: EditorUiContext;
    let editor!: LexicalEditor;

    beforeEach(() => {
        context = createTestContext();
        editor = context.editor;
    });

    afterEach(() => {
        destroyFromContext(context);
    });

    describe('$nestListItem', () => {
        test('nesting handles child items to leave at the same level', () => {
            const input = `<ul>
    <li>Inner A</li>
    <li>Inner B <ul>
            <li>Inner C</li>
    </ul></li>
</ul>`;
            let list!: ListNode;

            editor.updateAndCommit(() => {
                $getRoot().append(...$htmlToBlockNodes(editor, input));
                list = $getRoot().getFirstChild() as ListNode;
            });

            editor.updateAndCommit(() => {
                $nestListItem(list.getChildren()[1] as ListItemNode);
            });

            expectNodeShapeToMatch(editor, [
                {
                    type: 'list',
                    children: [
                        {
                            type: 'listitem',
                            children: [
                                {text: 'Inner A'},
                                {
                                    type: 'list',
                                    children: [
                                        {type: 'listitem', children: [{text: 'Inner B'}]},
                                        {type: 'listitem', children: [{text: 'Inner C'}]},
                                    ]
                                }
                            ]
                        },
                    ]
                }
            ]);
        });
    });

    describe('$unnestListItem', () => {
        test('middle in nested list converts to new parent item at same place', () => {
            const input = `<ul>
<li>Nested list:<ul>
    <li>Inner A</li>
    <li>Inner B</li>
    <li>Inner C</li>
</ul></li>
</ul>`;
            let innerList!: ListNode;

            editor.updateAndCommit(() => {
                $getRoot().append(...$htmlToBlockNodes(editor, input));
                innerList = (($getRoot().getFirstChild() as ListNode).getFirstChild() as ListItemNode).getLastChild() as ListNode;
            });

            editor.updateAndCommit(() => {
                $unnestListItem(innerList.getChildren()[1] as ListItemNode);
            });

            expectNodeShapeToMatch(editor, [
                {
                    type: 'list',
                    children: [
                        {
                            type: 'listitem',
                            children: [
                                {text: 'Nested list:'},
                                {
                                    type: 'list',
                                    children: [
                                        {type: 'listitem', children: [{text: 'Inner A'}]},
                                    ],
                                }
                            ],
                        },
                        {
                            type: 'listitem',
                            children: [
                                {text: 'Inner B'},
                                {
                                    type: 'list',
                                    children: [
                                        {type: 'listitem', children: [{text: 'Inner C'}]},
                                    ],
                                }
                            ],
                        }
                    ]
                }
            ]);
        });
    });
});