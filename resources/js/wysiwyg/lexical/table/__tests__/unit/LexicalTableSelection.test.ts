/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import {$createTableSelection} from '@lexical/table';
import {
  $createParagraphNode,
  $createTextNode,
  $getRoot,
  $setSelection,
  EditorState,
  type LexicalEditor,
  ParagraphNode,
  RootNode,
  TextNode,
} from 'lexical';
import {createTestEditor} from 'lexical/__tests__/utils';

describe('table selection', () => {
  let originalText: TextNode;
  let parsedParagraph: ParagraphNode;
  let parsedRoot: RootNode;
  let parsedText: TextNode;
  let paragraphKey: string;
  let textKey: string;
  let parsedEditorState: EditorState;
  let root: HTMLDivElement;
  let container: HTMLDivElement | null = null;
  let editor: LexicalEditor | null = null;

  beforeEach(() => {
    container = document.createElement('div');
    root = document.createElement('div');
    root.setAttribute('contenteditable', 'true');
    document.body.appendChild(container);
  });

  afterEach(() => {
    container?.remove();
  });

  function init(onError?: () => void) {
    editor = createTestEditor({
      nodes: [],
      onError: onError || jest.fn(),
      theme: {
        text: {
          bold: 'editor-text-bold',
          italic: 'editor-text-italic',
          underline: 'editor-text-underline',
        },
      },
    })

    editor.setRootElement(root);
  }

  async function update(fn: () => void) {
    editor!.update(fn);

    return Promise.resolve().then();
  }

  beforeEach(async () => {
    init();

    await update(() => {
      const paragraph = $createParagraphNode();
      originalText = $createTextNode('Hello world');
      const selection = $createTableSelection();
      selection.set(
        originalText.getKey(),
        originalText.getKey(),
        originalText.getKey(),
      );
      $setSelection(selection);
      paragraph.append(originalText);
      $getRoot().append(paragraph);
    });

    const stringifiedEditorState = JSON.stringify(
      editor!.getEditorState().toJSON(),
    );

    parsedEditorState = editor!.parseEditorState(stringifiedEditorState);
    parsedEditorState.read(() => {
      parsedRoot = $getRoot();
      parsedParagraph = parsedRoot.getFirstChild()!;
      paragraphKey = parsedParagraph.getKey();
      parsedText = parsedParagraph.getFirstChild()!;
      textKey = parsedText.getKey();
    });
  });

  it('Parses the nodes of a stringified editor state', async () => {
    expect(parsedRoot).toEqual({
      __cachedText: null,
      __dir: null,
      __first: paragraphKey,
      __key: 'root',
      __last: paragraphKey,
      __next: null,
      __parent: null,
      __prev: null,
      __size: 1,
      __style: '',
      __type: 'root',
    });
    expect(parsedParagraph).toEqual({
      __alignment: "",
      __dir: null,
      __first: textKey,
      __id: '',
      __inset: 0,
      __key: paragraphKey,
      __last: textKey,
      __next: null,
      __parent: 'root',
      __prev: null,
      __size: 1,
      __style: '',
      __textFormat: 0,
      __textStyle: '',
      __type: 'paragraph',
    });
    expect(parsedText).toEqual({
      __detail: 0,
      __format: 0,
      __key: textKey,
      __mode: 0,
      __next: null,
      __parent: paragraphKey,
      __prev: null,
      __style: '',
      __text: 'Hello world',
      __type: 'text',
    });
  });

  it('Parses the text content of the editor state', async () => {
    expect(parsedEditorState.read(() => $getRoot().__cachedText)).toBe(null);
    expect(parsedEditorState.read(() => $getRoot().getTextContent())).toBe(
      'Hello world',
    );
  });
});
