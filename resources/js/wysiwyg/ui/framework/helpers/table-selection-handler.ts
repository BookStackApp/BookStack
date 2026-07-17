import {$getNodeByKey, LexicalEditor} from "lexical";
import {NodeKey} from "lexical/LexicalNode";
import {
    $isTableNode,
    applyTableHandlers,
    HTMLTableElementWithWithTableSelectionState,
    TableNode,
    TableObserver
} from "@lexical/table";

// File adapted from logic in:
// https://github.com/facebook/lexical/blob/f373759a7849f473d34960a6bf4e34b2a011e762/packages/lexical-react/src/LexicalTablePlugin.ts#L49
// Copyright (c) Meta Platforms, Inc. and affiliates.
// License: MIT

class TableSelectionHandler {

    protected editor: LexicalEditor
    protected tableSelections = new Map<NodeKey, TableObserver>();
    protected unregisterMutationListener = () => {};

    constructor(editor: LexicalEditor) {
        this.editor = editor;
        this.init();
    }

    protected init() {
        this.unregisterMutationListener = this.editor.registerMutationListener(TableNode, (mutations) => {
            for (const [nodeKey, mutation] of mutations) {
                if (mutation === 'created') {
                    this.editor.getEditorState().read(() => {
                        const tableNode = $getNodeByKey<TableNode>(nodeKey);
                        if ($isTableNode(tableNode)) {
                            this.initializeTableNode(tableNode);
                        }
                    });
                } else if (mutation === 'destroyed') {
                    const tableSelection = this.tableSelections.get(nodeKey);

                    if (tableSelection !== undefined) {
                        tableSelection.removeListeners();
                        this.tableSelections.delete(nodeKey);
                    }
                }
            }
        });
    }

    protected initializeTableNode(tableNode: TableNode) {
        const nodeKey = tableNode.getKey();
        const tableElement = this.editor.getElementByKey(
            nodeKey,
        ) as HTMLTableElementWithWithTableSelectionState;
        if (tableElement && !this.tableSelections.has(nodeKey)) {
            const tableSelection = applyTableHandlers(
                tableNode,
                tableElement,
                this.editor,
                true,
            );
            this.tableSelections.set(nodeKey, tableSelection);
        }
    };

    teardown() {
        this.unregisterMutationListener();
        for (const [, tableSelection] of this.tableSelections) {
            tableSelection.removeListeners();
        }
    }
}

export function registerTableSelectionHandler(editor: LexicalEditor): (() => void) {
    const resizer = new TableSelectionHandler(editor);

    return () => {
        resizer.teardown();
    };
}