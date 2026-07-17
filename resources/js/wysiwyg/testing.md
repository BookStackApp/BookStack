# Testing Guidance

This is testing guidance specific for this Lexical-based WYSIWYG editor.
There is a lot of pre-existing test code carried over form the fork of lexical, but since there we've added a range of helpers and altered how testing can be done to make things a bit simpler and aligned with how we run tests.

This document is an attempt to document the new best options for added tests with an aim for standardisation on these approaches going forward.

## Utils Location

Most core test utils can be found in the file at path: resources/js/wysiwyg/lexical/core/__tests__/utils/index.ts

## Test Example

This is an example of a typical test using the common modern utilities to help perform actions or assertions. Comments are for this example only, and are not expected in actual test files.

```ts
import {
    createTestContext,
    dispatchKeydownEventForNode, 
    expectEditorStateJSONPropToEqual,
    expectNodeShapeToMatch
} from "lexical/__tests__/utils";
import {
    $getRoot,
    ParagraphNode,
    TextNode
} from "lexical";

describe('A specific service or file or function', () => {
    test('it does thing', async () => {
        // Create the editor context and get an editor reference
        const {editor} = createTestContext();

        // Run an action within the editor.
        let pNode: ParagraphNode;
        editor.updateAndCommit(() => {
            pNode = new ParagraphNode();
            const text = new TextNode('Hello!');
            pNode.append(text);
            $getRoot().append(pNode);
        });

        // Dispatch key events via the DOM
        dispatchKeydownEventForNode(pNode!, editor, ' ');

        // Check the shape (and text) of the resulting state
        expectNodeShapeToMatch(editor, [{type: 'paragraph', children: [
                {text: 'Hello!'},
            ]}]);

        // Check specific props in the resulting JSON state
        expectEditorStateJSONPropToEqual(editor, '0.0.text', 'Hello!');
    });
});
```