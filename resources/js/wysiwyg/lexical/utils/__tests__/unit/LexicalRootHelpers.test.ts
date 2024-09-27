/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import {$createParagraphNode, $createTextNode, $getRoot} from 'lexical';
import {initializeUnitTest} from 'lexical/__tests__/utils';

export function $rootTextContent(): string {
  const root = $getRoot();

  return root.getTextContent();
}

export function $isRootTextContentEmpty(
    isEditorComposing: boolean,
    trim = true,
): boolean {
  if (isEditorComposing) {
    return false;
  }

  let text = $rootTextContent();

  if (trim) {
    text = text.trim();
  }

  return text === '';
}

export function $isRootTextContentEmptyCurry(
    isEditorComposing: boolean,
    trim?: boolean,
): () => boolean {
  return () => $isRootTextContentEmpty(isEditorComposing, trim);
}

describe('LexicalRootHelpers tests', () => {
  initializeUnitTest((testEnv) => {
    it('textContent', async () => {
      const editor = testEnv.editor;

      expect(editor.getEditorState().read($rootTextContent)).toBe('');

      await editor.update(() => {
        const root = $getRoot();
        const paragraph = $createParagraphNode();
        const text = $createTextNode('foo');
        root.append(paragraph);
        paragraph.append(text);

        expect($rootTextContent()).toBe('foo');
      });

      expect(editor.getEditorState().read($rootTextContent)).toBe('foo');
    });

    it('isBlank', async () => {
      const editor = testEnv.editor;

      expect(
        editor
          .getEditorState()
          .read($isRootTextContentEmptyCurry(editor.isComposing())),
      ).toBe(true);

      await editor.update(() => {
        const root = $getRoot();
        const paragraph = $createParagraphNode();
        const text = $createTextNode('foo');
        root.append(paragraph);
        paragraph.append(text);

        expect($isRootTextContentEmpty(editor.isComposing())).toBe(false);
      });

      expect(
        editor
          .getEditorState()
          .read($isRootTextContentEmptyCurry(editor.isComposing())),
      ).toBe(false);
    });
  });
});
