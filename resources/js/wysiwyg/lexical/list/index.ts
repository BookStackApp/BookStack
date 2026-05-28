/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {SerializedListItemNode} from './LexicalListItemNode';
import type {ListType, SerializedListNode} from './LexicalListNode';
import {
  $getSelection,
  $isRangeSelection, COMMAND_PRIORITY_NORMAL, INSERT_PARAGRAPH_COMMAND, LexicalCommand, LexicalEditor
} from 'lexical';

import {createCommand} from 'lexical';

import {$handleListInsertParagraph, insertList, removeList} from './formatList';
import {
  $createListItemNode,
  $isListItemNode,
  ListItemNode,
} from './LexicalListItemNode';
import {$createListNode, $isListNode, ListNode} from './LexicalListNode';
import {$getListDepth} from './utils';
import {mergeRegister} from "@lexical/utils";
import {$getAncestor, INTERNAL_$isBlock} from "lexical/LexicalUtils";

export {
  $createListItemNode,
  $createListNode,
  $getListDepth,
  $handleListInsertParagraph,
  $isListItemNode,
  $isListNode,
  insertList,
  ListItemNode,
  ListNode,
  ListType,
  removeList,
  SerializedListItemNode,
  SerializedListNode,
};

export const INSERT_UNORDERED_LIST_COMMAND: LexicalCommand<void> =
  createCommand('INSERT_UNORDERED_LIST_COMMAND');
export const INSERT_ORDERED_LIST_COMMAND: LexicalCommand<void> = createCommand(
  'INSERT_ORDERED_LIST_COMMAND',
);
export const INSERT_CHECK_LIST_COMMAND: LexicalCommand<void> = createCommand(
  'INSERT_CHECK_LIST_COMMAND',
);
export const REMOVE_LIST_COMMAND: LexicalCommand<void> = createCommand(
  'REMOVE_LIST_COMMAND',
);

export function registerLists(editor: LexicalEditor): () => void {
  return mergeRegister(

      // Override the default insert paragraph command when within a list item
      // so that new blocks are inserted as their own list items.
      editor.registerCommand(INSERT_PARAGRAPH_COMMAND, () => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }

        const anchorNode = selection.anchor.getNode();
        const block = $getAncestor(anchorNode, INTERNAL_$isBlock)!;
        const blockParent = block.getParent();
        if ($isListItemNode(blockParent)) {
            const newBlock = selection.insertParagraph();
            if (newBlock) {
                const newListItem = $createListItemNode();
                const newBlockSiblings = newBlock.getNextSiblings();
                newListItem.append(newBlock, ...newBlockSiblings);
                blockParent.insertAfter(newListItem, false);
                newListItem.selectStart();
            }
            return true;
        }

        return false;
      }, COMMAND_PRIORITY_NORMAL),
  );
}
