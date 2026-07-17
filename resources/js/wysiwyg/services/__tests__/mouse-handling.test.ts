import {
    createTestContext, destroyFromContext, dispatchEditorMouseClick,
} from "lexical/__tests__/utils";
import {
    $getRoot, LexicalEditor, LexicalNode,
    ParagraphNode,
} from "lexical";
import {registerRichText} from "@lexical/rich-text";
import {EditorUiContext} from "../../ui/framework/core";
import {registerMouseHandling} from "../mouse-handling";
import {$createTableNode, TableNode} from "@lexical/table";

describe('Mouse-handling service tests', () => {

    let context!: EditorUiContext;
    let editor!: LexicalEditor;

    beforeEach(() => {
        context = createTestContext();
        editor = context.editor;
        registerRichText(editor);
        registerMouseHandling(context);
    });

    afterEach(() => {
        destroyFromContext(context);
    });

    test('Click below last table inserts new empty paragraph', () => {
        let tableNode!: TableNode;
        let lastRootChild!: LexicalNode|null;

        editor.updateAndCommit(() => {
            tableNode = $createTableNode();
            $getRoot().append(tableNode);
            lastRootChild = $getRoot().getLastChild();
        });

        expect(lastRootChild).toBeInstanceOf(TableNode);

        const tableDOM = editor.getElementByKey(tableNode.getKey());
        const rect = tableDOM?.getBoundingClientRect();
        dispatchEditorMouseClick(editor, 0, (rect?.bottom || 0) + 1)

        editor.getEditorState().read(() => {
            lastRootChild = $getRoot().getLastChild();
        });

        expect(lastRootChild).toBeInstanceOf(ParagraphNode);
    });
});