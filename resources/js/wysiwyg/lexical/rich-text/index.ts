/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import type {
  CommandPayloadType,
  LexicalCommand,
  LexicalEditor,
  PasteCommandType,
  RangeSelection,
  TextFormatType,
} from 'lexical';
import {
  $createRangeSelection,
  $createTabNode,
  $getAdjacentNode,
  $getNearestNodeFromDOMNode,
  $getRoot,
  $getSelection,
  $insertNodes,
  $isDecoratorNode,
  $isElementNode,
  $isNodeSelection,
  $isRangeSelection,
  $isTextNode,
  $normalizeSelection__EXPERIMENTAL,
  $selectAll,
  $setSelection,
  CLICK_COMMAND,
  COMMAND_PRIORITY_EDITOR,
  CONTROLLED_TEXT_INSERTION_COMMAND,
  COPY_COMMAND,
  createCommand,
  CUT_COMMAND,
  DELETE_CHARACTER_COMMAND,
  DELETE_LINE_COMMAND,
  DELETE_WORD_COMMAND,
  DRAGOVER_COMMAND,
  DRAGSTART_COMMAND,
  DROP_COMMAND,
  ElementNode,
  FORMAT_TEXT_COMMAND,
  INSERT_LINE_BREAK_COMMAND,
  INSERT_PARAGRAPH_COMMAND,
  INSERT_TAB_COMMAND,
  isSelectionCapturedInDecoratorInput,
  KEY_ARROW_DOWN_COMMAND,
  KEY_ARROW_LEFT_COMMAND,
  KEY_ARROW_RIGHT_COMMAND,
  KEY_ARROW_UP_COMMAND,
  KEY_BACKSPACE_COMMAND,
  KEY_DELETE_COMMAND,
  KEY_ENTER_COMMAND,
  KEY_ESCAPE_COMMAND,
  PASTE_COMMAND,
  REMOVE_TEXT_COMMAND,
  SELECT_ALL_COMMAND,
} from 'lexical';

import {$insertDataTransferForRichText, copyToClipboard,} from '@lexical/clipboard';
import {$moveCharacter, $shouldOverrideDefaultCharacterSelection,} from '@lexical/selection';
import {$findMatchingParent, mergeRegister, objectKlassEquals,} from '@lexical/utils';
import caretFromPoint from 'lexical/shared/caretFromPoint';
import {CAN_USE_BEFORE_INPUT, IS_APPLE_WEBKIT, IS_IOS, IS_SAFARI,} from 'lexical/shared/environment';

export const DRAG_DROP_PASTE: LexicalCommand<Array<File>> = createCommand(
  'DRAG_DROP_PASTE_FILE',
);



function onPasteForRichText(
  event: CommandPayloadType<typeof PASTE_COMMAND>,
  editor: LexicalEditor,
): void {
  event.preventDefault();
  editor.update(
    () => {
      const selection = $getSelection();
      const clipboardData =
        objectKlassEquals(event, InputEvent) ||
        objectKlassEquals(event, KeyboardEvent)
          ? null
          : (event as ClipboardEvent).clipboardData;
      if (clipboardData != null && selection !== null) {
        $insertDataTransferForRichText(clipboardData, selection, editor);
      }
    },
    {
      tag: 'paste',
    },
  );
}

async function onCutForRichText(
  event: CommandPayloadType<typeof CUT_COMMAND>,
  editor: LexicalEditor,
): Promise<void> {
  await copyToClipboard(
    editor,
    objectKlassEquals(event, ClipboardEvent) ? (event as ClipboardEvent) : null,
  );
  editor.update(() => {
    const selection = $getSelection();
    if ($isRangeSelection(selection)) {
      selection.removeText();
    } else if ($isNodeSelection(selection)) {
      selection.getNodes().forEach((node) => node.remove());
    }
  });
}

// Clipboard may contain files that we aren't allowed to read. While the event is arguably useless,
// in certain occasions, we want to know whether it was a file transfer, as opposed to text. We
// control this with the first boolean flag.
export function eventFiles(
  event: DragEvent | PasteCommandType,
): [boolean, Array<File>, boolean] {
  let dataTransfer: null | DataTransfer = null;
  if (objectKlassEquals(event, DragEvent)) {
    dataTransfer = (event as DragEvent).dataTransfer;
  } else if (objectKlassEquals(event, ClipboardEvent)) {
    dataTransfer = (event as ClipboardEvent).clipboardData;
  }

  if (dataTransfer === null) {
    return [false, [], false];
  }

  const types = dataTransfer.types;
  const hasFiles = types.includes('Files');
  const hasContent =
    types.includes('text/html') || types.includes('text/plain');
  return [hasFiles, Array.from(dataTransfer.files), hasContent];
}

function $handleIndentAndOutdent(
  indentOrOutdent: (block: ElementNode) => void,
): boolean {
  const selection = $getSelection();
  if (!$isRangeSelection(selection)) {
    return false;
  }
  const alreadyHandled = new Set();
  const nodes = selection.getNodes();
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    const key = node.getKey();
    if (alreadyHandled.has(key)) {
      continue;
    }
    const parentBlock = $findMatchingParent(
      node,
      (parentNode): parentNode is ElementNode =>
        $isElementNode(parentNode) && !parentNode.isInline(),
    );
    if (parentBlock === null) {
      continue;
    }
    const parentKey = parentBlock.getKey();
    if (parentBlock.canIndent() && !alreadyHandled.has(parentKey)) {
      alreadyHandled.add(parentKey);
      indentOrOutdent(parentBlock);
    }
  }
  return alreadyHandled.size > 0;
}

function $isTargetWithinDecorator(target: HTMLElement): boolean {
  const node = $getNearestNodeFromDOMNode(target);
  return $isDecoratorNode(node);
}

function $isSelectionAtEndOfRoot(selection: RangeSelection) {
  const focus = selection.focus;
  return focus.key === 'root' && focus.offset === $getRoot().getChildrenSize();
}

export function registerRichText(editor: LexicalEditor): () => void {
  const removeListener = mergeRegister(
    editor.registerCommand(
      CLICK_COMMAND,
      (payload) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection)) {
          selection.clear();
          return true;
        }
        return false;
      },
      0,
    ),
    editor.registerCommand<boolean>(
      DELETE_CHARACTER_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteCharacter(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<boolean>(
      DELETE_WORD_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteWord(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<boolean>(
      DELETE_LINE_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteLine(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      CONTROLLED_TEXT_INSERTION_COMMAND,
      (eventOrText) => {
        const selection = $getSelection();

        if (typeof eventOrText === 'string') {
          if (selection !== null) {
            selection.insertText(eventOrText);
          }
        } else {
          if (selection === null) {
            return false;
          }

          const dataTransfer = eventOrText.dataTransfer;
          if (dataTransfer != null) {
            $insertDataTransferForRichText(dataTransfer, selection, editor);
          } else if ($isRangeSelection(selection)) {
            const data = eventOrText.data;
            if (data) {
              selection.insertText(data);
            }
            return true;
          }
        }
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      REMOVE_TEXT_COMMAND,
      () => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.removeText();
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<TextFormatType>(
      FORMAT_TEXT_COMMAND,
      (format) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.formatText(format);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<boolean>(
      INSERT_LINE_BREAK_COMMAND,
      (selectStart) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.insertLineBreak(selectStart);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      INSERT_PARAGRAPH_COMMAND,
      () => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.insertParagraph();
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      INSERT_TAB_COMMAND,
      () => {
        $insertNodes([$createTabNode()]);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_ARROW_UP_COMMAND,
      (event) => {
        const selection = $getSelection();
        if (
          $isNodeSelection(selection) &&
          !$isTargetWithinDecorator(event.target as HTMLElement)
        ) {
          // If selection is on a node, let's try and move selection
          // back to being a range selection.
          const nodes = selection.getNodes();
          if (nodes.length > 0) {
            nodes[0].selectPrevious();
            return true;
          }
        } else if ($isRangeSelection(selection)) {
          const possibleNode = $getAdjacentNode(selection.focus, true);
          if (
            !event.shiftKey &&
            $isDecoratorNode(possibleNode) &&
            !possibleNode.isIsolated() &&
            !possibleNode.isInline()
          ) {
            possibleNode.selectPrevious();
            event.preventDefault();
            return true;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_ARROW_DOWN_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection)) {
          // If selection is on a node, let's try and move selection
          // back to being a range selection.
          const nodes = selection.getNodes();
          if (nodes.length > 0) {
            nodes[0].selectNext(0, 0);
            return true;
          }
        } else if ($isRangeSelection(selection)) {
          if ($isSelectionAtEndOfRoot(selection)) {
            event.preventDefault();
            return true;
          }
          const possibleNode = $getAdjacentNode(selection.focus, false);
          if (
            !event.shiftKey &&
            $isDecoratorNode(possibleNode) &&
            !possibleNode.isIsolated() &&
            !possibleNode.isInline()
          ) {
            possibleNode.selectNext();
            event.preventDefault();
            return true;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_ARROW_LEFT_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection)) {
          // If selection is on a node, let's try and move selection
          // back to being a range selection.
          const nodes = selection.getNodes();
          if (nodes.length > 0) {
            event.preventDefault();
            nodes[0].selectPrevious();
            return true;
          }
        }
        if (!$isRangeSelection(selection)) {
          return false;
        }
        if ($shouldOverrideDefaultCharacterSelection(selection, true)) {
          const isHoldingShift = event.shiftKey;
          event.preventDefault();
          $moveCharacter(selection, isHoldingShift, true);
          return true;
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_ARROW_RIGHT_COMMAND,
      (event) => {
        const selection = $getSelection();
        if (
          $isNodeSelection(selection) &&
          !$isTargetWithinDecorator(event.target as HTMLElement)
        ) {
          // If selection is on a node, let's try and move selection
          // back to being a range selection.
          const nodes = selection.getNodes();
          if (nodes.length > 0) {
            event.preventDefault();
            nodes[0].selectNext(0, 0);
            return true;
          }
        }
        if (!$isRangeSelection(selection)) {
          return false;
        }
        const isHoldingShift = event.shiftKey;
        if ($shouldOverrideDefaultCharacterSelection(selection, false)) {
          event.preventDefault();
          $moveCharacter(selection, isHoldingShift, false);
          return true;
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_BACKSPACE_COMMAND,
      (event) => {
        if ($isTargetWithinDecorator(event.target as HTMLElement)) {
          return false;
        }
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        event.preventDefault();

        return editor.dispatchCommand(DELETE_CHARACTER_COMMAND, true);
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent>(
      KEY_DELETE_COMMAND,
      (event) => {
        if ($isTargetWithinDecorator(event.target as HTMLElement)) {
          return false;
        }
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        event.preventDefault();
        return editor.dispatchCommand(DELETE_CHARACTER_COMMAND, false);
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<KeyboardEvent | null>(
      KEY_ENTER_COMMAND,
      (event) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        if (event !== null) {
          // If we have beforeinput, then we can avoid blocking
          // the default behavior. This ensures that the iOS can
          // intercept that we're actually inserting a paragraph,
          // and autocomplete, autocapitalize etc work as intended.
          // This can also cause a strange performance issue in
          // Safari, where there is a noticeable pause due to
          // preventing the key down of enter.
          if (
            (IS_IOS || IS_SAFARI || IS_APPLE_WEBKIT) &&
            CAN_USE_BEFORE_INPUT
          ) {
            return false;
          }
          event.preventDefault();
          if (event.shiftKey) {
            return editor.dispatchCommand(INSERT_LINE_BREAK_COMMAND, false);
          }
        }
        return editor.dispatchCommand(INSERT_PARAGRAPH_COMMAND, undefined);
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      KEY_ESCAPE_COMMAND,
      () => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        editor.blur();
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<DragEvent>(
      DROP_COMMAND,
      (event) => {
        const [, files] = eventFiles(event);
        if (files.length > 0) {
          const x = event.clientX;
          const y = event.clientY;
          const eventRange = caretFromPoint(x, y);
          if (eventRange !== null) {
            const {offset: domOffset, node: domNode} = eventRange;
            const node = $getNearestNodeFromDOMNode(domNode);
            if (node !== null) {
              const selection = $createRangeSelection();
              if ($isTextNode(node)) {
                selection.anchor.set(node.getKey(), domOffset, 'text');
                selection.focus.set(node.getKey(), domOffset, 'text');
              } else {
                const parentKey = node.getParentOrThrow().getKey();
                const offset = node.getIndexWithinParent() + 1;
                selection.anchor.set(parentKey, offset, 'element');
                selection.focus.set(parentKey, offset, 'element');
              }
              const normalizedSelection =
                $normalizeSelection__EXPERIMENTAL(selection);
              $setSelection(normalizedSelection);
            }
            editor.dispatchCommand(DRAG_DROP_PASTE, files);
          }
          event.preventDefault();
          return true;
        }

        const selection = $getSelection();
        if ($isRangeSelection(selection)) {
          return true;
        }

        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<DragEvent>(
      DRAGSTART_COMMAND,
      (event) => {
        const [isFileTransfer] = eventFiles(event);
        const selection = $getSelection();
        if (isFileTransfer && !$isRangeSelection(selection)) {
          return false;
        }
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand<DragEvent>(
      DRAGOVER_COMMAND,
      (event) => {
        const [isFileTransfer] = eventFiles(event);
        const selection = $getSelection();
        if (isFileTransfer && !$isRangeSelection(selection)) {
          return false;
        }
        const x = event.clientX;
        const y = event.clientY;
        const eventRange = caretFromPoint(x, y);
        if (eventRange !== null) {
          const node = $getNearestNodeFromDOMNode(eventRange.node);
          if ($isDecoratorNode(node)) {
            // Show browser caret as the user is dragging the media across the screen. Won't work
            // for DecoratorNode nor it's relevant.
            event.preventDefault();
          }
        }
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      SELECT_ALL_COMMAND,
      () => {
        $selectAll();

        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      COPY_COMMAND,
      (event) => {
        copyToClipboard(
          editor,
          objectKlassEquals(event, ClipboardEvent)
            ? (event as ClipboardEvent)
            : null,
        );
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      CUT_COMMAND,
      (event) => {
        onCutForRichText(event, editor);
        return true;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
    editor.registerCommand(
      PASTE_COMMAND,
      (event) => {
        const [, files, hasTextContent] = eventFiles(event);
        if (files.length > 0 && !hasTextContent) {
          editor.dispatchCommand(DRAG_DROP_PASTE, files);
          return true;
        }

        // if inputs then paste within the input ignore creating a new node on paste event
        if (isSelectionCapturedInDecoratorInput(event.target as Node)) {
          return false;
        }

        const selection = $getSelection();
        if (selection !== null) {
          onPasteForRichText(event, editor);
          return true;
        }

        return false;
      },
      COMMAND_PRIORITY_EDITOR,
    ),
  );
  return removeListener;
}
