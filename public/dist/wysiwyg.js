// See the "/licenses" URI for full package license details
var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);

// resources/js/wysiwyg/lexical/core/LexicalCommands.ts
function createCommand(type) {
  return __DEV__ ? { type } : {};
}
var SELECTION_CHANGE_COMMAND = createCommand(
  "SELECTION_CHANGE_COMMAND"
);
var SELECTION_INSERT_CLIPBOARD_NODES_COMMAND = createCommand("SELECTION_INSERT_CLIPBOARD_NODES_COMMAND");
var CLICK_COMMAND = createCommand("CLICK_COMMAND");
var DELETE_CHARACTER_COMMAND = createCommand(
  "DELETE_CHARACTER_COMMAND"
);
var INSERT_LINE_BREAK_COMMAND = createCommand(
  "INSERT_LINE_BREAK_COMMAND"
);
var INSERT_PARAGRAPH_COMMAND = createCommand(
  "INSERT_PARAGRAPH_COMMAND"
);
var CONTROLLED_TEXT_INSERTION_COMMAND = createCommand("CONTROLLED_TEXT_INSERTION_COMMAND");
var PASTE_COMMAND = createCommand("PASTE_COMMAND");
var REMOVE_TEXT_COMMAND = createCommand("REMOVE_TEXT_COMMAND");
var DELETE_WORD_COMMAND = createCommand(
  "DELETE_WORD_COMMAND"
);
var DELETE_LINE_COMMAND = createCommand(
  "DELETE_LINE_COMMAND"
);
var FORMAT_TEXT_COMMAND = createCommand("FORMAT_TEXT_COMMAND");
var UNDO_COMMAND = createCommand("UNDO_COMMAND");
var REDO_COMMAND = createCommand("REDO_COMMAND");
var KEY_DOWN_COMMAND = createCommand("KEYDOWN_COMMAND");
var KEY_ARROW_RIGHT_COMMAND = createCommand("KEY_ARROW_RIGHT_COMMAND");
var MOVE_TO_END = createCommand("MOVE_TO_END");
var KEY_ARROW_LEFT_COMMAND = createCommand("KEY_ARROW_LEFT_COMMAND");
var MOVE_TO_START = createCommand("MOVE_TO_START");
var KEY_ARROW_UP_COMMAND = createCommand("KEY_ARROW_UP_COMMAND");
var KEY_ARROW_DOWN_COMMAND = createCommand("KEY_ARROW_DOWN_COMMAND");
var KEY_ENTER_COMMAND = createCommand("KEY_ENTER_COMMAND");
var KEY_SPACE_COMMAND = createCommand("KEY_SPACE_COMMAND");
var KEY_BACKSPACE_COMMAND = createCommand("KEY_BACKSPACE_COMMAND");
var KEY_ESCAPE_COMMAND = createCommand("KEY_ESCAPE_COMMAND");
var KEY_DELETE_COMMAND = createCommand("KEY_DELETE_COMMAND");
var KEY_TAB_COMMAND = createCommand("KEY_TAB_COMMAND");
var INSERT_TAB_COMMAND = createCommand("INSERT_TAB_COMMAND");
var INDENT_CONTENT_COMMAND = createCommand(
  "INDENT_CONTENT_COMMAND"
);
var OUTDENT_CONTENT_COMMAND = createCommand(
  "OUTDENT_CONTENT_COMMAND"
);
var DROP_COMMAND = createCommand("DROP_COMMAND");
var DRAGSTART_COMMAND = createCommand("DRAGSTART_COMMAND");
var DRAGOVER_COMMAND = createCommand("DRAGOVER_COMMAND");
var DRAGEND_COMMAND = createCommand("DRAGEND_COMMAND");
var COPY_COMMAND = createCommand("COPY_COMMAND");
var CUT_COMMAND = createCommand("CUT_COMMAND");
var SELECT_ALL_COMMAND = createCommand("SELECT_ALL_COMMAND");
var CLEAR_EDITOR_COMMAND = createCommand(
  "CLEAR_EDITOR_COMMAND"
);
var CLEAR_HISTORY_COMMAND = createCommand(
  "CLEAR_HISTORY_COMMAND"
);
var CAN_REDO_COMMAND = createCommand("CAN_REDO_COMMAND");
var CAN_UNDO_COMMAND = createCommand("CAN_UNDO_COMMAND");
var FOCUS_COMMAND = createCommand("FOCUS_COMMAND");
var BLUR_COMMAND = createCommand("BLUR_COMMAND");
var KEY_MODIFIER_COMMAND = createCommand("KEY_MODIFIER_COMMAND");

// resources/js/wysiwyg/lexical/core/shared/canUseDOM.ts
var CAN_USE_DOM = typeof window !== "undefined" && typeof window.document !== "undefined" && typeof window.document.createElement !== "undefined";

// resources/js/wysiwyg/lexical/core/shared/environment.ts
var documentMode = CAN_USE_DOM && "documentMode" in document ? document.documentMode : null;
var IS_APPLE = CAN_USE_DOM && /Mac|iPod|iPhone|iPad/.test(navigator.platform);
var IS_FIREFOX = CAN_USE_DOM && /^(?!.*Seamonkey)(?=.*Firefox).*/i.test(navigator.userAgent);
var CAN_USE_BEFORE_INPUT = CAN_USE_DOM && "InputEvent" in window && !documentMode ? "getTargetRanges" in new window.InputEvent("input") : false;
var IS_SAFARI = CAN_USE_DOM && /Version\/[\d.]+.*Safari/.test(navigator.userAgent);
var IS_IOS = CAN_USE_DOM && /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
var IS_ANDROID = CAN_USE_DOM && /Android/.test(navigator.userAgent);
var IS_CHROME = CAN_USE_DOM && /^(?=.*Chrome).*/i.test(navigator.userAgent);
var IS_ANDROID_CHROME = CAN_USE_DOM && IS_ANDROID && IS_CHROME;
var IS_APPLE_WEBKIT = CAN_USE_DOM && /AppleWebKit\/[\d.]+/.test(navigator.userAgent) && !IS_CHROME;

// resources/js/wysiwyg/lexical/core/LexicalConstants.ts
var DOM_ELEMENT_TYPE = 1;
var DOM_TEXT_TYPE = 3;
var NO_DIRTY_NODES = 0;
var HAS_DIRTY_NODES = 1;
var FULL_RECONCILE = 2;
var IS_NORMAL = 0;
var IS_TOKEN = 1;
var IS_SEGMENTED = 2;
var IS_BOLD = 1;
var IS_ITALIC = 1 << 1;
var IS_STRIKETHROUGH = 1 << 2;
var IS_UNDERLINE = 1 << 3;
var IS_CODE = 1 << 4;
var IS_SUBSCRIPT = 1 << 5;
var IS_SUPERSCRIPT = 1 << 6;
var IS_HIGHLIGHT = 1 << 7;
var IS_ALL_FORMATTING = IS_BOLD | IS_ITALIC | IS_STRIKETHROUGH | IS_UNDERLINE | IS_CODE | IS_SUBSCRIPT | IS_SUPERSCRIPT | IS_HIGHLIGHT;
var IS_DIRECTIONLESS = 1;
var IS_UNMERGEABLE = 1 << 1;
var NON_BREAKING_SPACE = "\xA0";
var ZERO_WIDTH_SPACE = "\u200B";
var COMPOSITION_SUFFIX = IS_SAFARI || IS_IOS || IS_APPLE_WEBKIT ? NON_BREAKING_SPACE : ZERO_WIDTH_SPACE;
var DOUBLE_LINE_BREAK = "\n\n";
var COMPOSITION_START_CHAR = IS_FIREFOX ? NON_BREAKING_SPACE : COMPOSITION_SUFFIX;
var RTL = "\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC";
var LTR = "A-Za-z\xC0-\xD6\xD8-\xF6\xF8-\u02B8\u0300-\u0590\u0800-\u1FFF\u200E\u2C00-\uFB1C\uFE00-\uFE6F\uFEFD-\uFFFF";
var RTL_REGEX = new RegExp("^[^" + LTR + "]*[" + RTL + "]");
var LTR_REGEX = new RegExp("^[^" + RTL + "]*[" + LTR + "]");
var TEXT_TYPE_TO_FORMAT = {
  bold: IS_BOLD,
  code: IS_CODE,
  highlight: IS_HIGHLIGHT,
  italic: IS_ITALIC,
  strikethrough: IS_STRIKETHROUGH,
  subscript: IS_SUBSCRIPT,
  superscript: IS_SUPERSCRIPT,
  underline: IS_UNDERLINE
};
var DETAIL_TYPE_TO_DETAIL = {
  directionless: IS_DIRECTIONLESS,
  unmergeable: IS_UNMERGEABLE
};
var TEXT_MODE_TO_TYPE = {
  normal: IS_NORMAL,
  segmented: IS_SEGMENTED,
  token: IS_TOKEN
};
var TEXT_TYPE_TO_MODE = {
  [IS_NORMAL]: "normal",
  [IS_SEGMENTED]: "segmented",
  [IS_TOKEN]: "token"
};

// resources/js/wysiwyg/lexical/core/shared/invariant.ts
function invariant(cond, message, ...args) {
  if (cond) {
    return;
  }
  for (const arg of args) {
    message = (message || "").replace("%s", arg);
  }
  throw new Error(message);
}

// resources/js/wysiwyg/lexical/core/shared/normalizeClassNames.ts
function normalizeClassNames(...classNames) {
  const rval = [];
  for (const className of classNames) {
    if (className && typeof className === "string") {
      for (const [s] of className.matchAll(/\S+/g)) {
        rval.push(s);
      }
    }
  }
  return rval;
}

// resources/js/wysiwyg/lexical/core/LexicalMutations.ts
var TEXT_MUTATION_VARIANCE = 100;
var isProcessingMutations = false;
var lastTextEntryTimeStamp = 0;
function getIsProcessingMutations() {
  return isProcessingMutations;
}
function updateTimeStamp(event) {
  lastTextEntryTimeStamp = event.timeStamp;
}
function initTextEntryListener(editor) {
  if (lastTextEntryTimeStamp === 0) {
    getWindow(editor).addEventListener("textInput", updateTimeStamp, true);
  }
}
function isManagedLineBreak(dom, target, editor) {
  return (
    // @ts-expect-error: internal field
    target.__lexicalLineBreak === dom || // @ts-ignore We intentionally add this to the Node.
    dom[`__lexicalKey_${editor._key}`] !== void 0
  );
}
function getLastSelection(editor) {
  return editor.getEditorState().read(() => {
    const selection = $getSelection();
    return selection !== null ? selection.clone() : null;
  });
}
function $handleTextMutation(target, node, editor) {
  const domSelection = getDOMSelection(editor._window);
  let anchorOffset = null;
  let focusOffset = null;
  if (domSelection !== null && domSelection.anchorNode === target) {
    anchorOffset = domSelection.anchorOffset;
    focusOffset = domSelection.focusOffset;
  }
  const text = target.nodeValue;
  if (text !== null) {
    $updateTextNodeFromDOMContent(node, text, anchorOffset, focusOffset, false);
  }
}
function shouldUpdateTextNodeFromMutation(selection, targetDOM, targetNode) {
  return targetDOM.nodeType === DOM_TEXT_TYPE && targetNode.isAttached();
}
function $flushMutations(editor, mutations, observer) {
  isProcessingMutations = true;
  const shouldFlushTextMutations = performance.now() - lastTextEntryTimeStamp > TEXT_MUTATION_VARIANCE;
  try {
    updateEditor(editor, () => {
      const selection = $getSelection() || getLastSelection(editor);
      const badDOMTargets = /* @__PURE__ */ new Map();
      const rootElement = editor.getRootElement();
      const currentEditorState = editor._editorState;
      const blockCursorElement = editor._blockCursorElement;
      let shouldRevertSelection = false;
      let possibleTextForFirefoxPaste = "";
      for (let i = 0; i < mutations.length; i++) {
        const mutation = mutations[i];
        const type = mutation.type;
        const targetDOM = mutation.target;
        let targetNode = $getNearestNodeFromDOMNode(
          targetDOM,
          currentEditorState
        );
        if (targetNode === null && targetDOM !== rootElement || $isDecoratorNode(targetNode)) {
          continue;
        }
        if (type === "characterData") {
          if (shouldFlushTextMutations && $isTextNode(targetNode) && shouldUpdateTextNodeFromMutation(selection, targetDOM, targetNode)) {
            $handleTextMutation(
              // nodeType === DOM_TEXT_TYPE is a Text DOM node
              targetDOM,
              targetNode,
              editor
            );
          }
        } else if (type === "childList") {
          shouldRevertSelection = true;
          const addedDOMs = mutation.addedNodes;
          for (let s = 0; s < addedDOMs.length; s++) {
            const addedDOM = addedDOMs[s];
            const node = $getNodeFromDOMNode(addedDOM);
            const parentDOM = addedDOM.parentNode;
            if (parentDOM != null && addedDOM !== blockCursorElement && node === null && (addedDOM.nodeName !== "BR" || !isManagedLineBreak(addedDOM, parentDOM, editor))) {
              if (IS_FIREFOX) {
                const possibleText = addedDOM.innerText || addedDOM.nodeValue;
                if (possibleText) {
                  possibleTextForFirefoxPaste += possibleText;
                }
              }
              parentDOM.removeChild(addedDOM);
            }
          }
          const removedDOMs = mutation.removedNodes;
          const removedDOMsLength = removedDOMs.length;
          if (removedDOMsLength > 0) {
            let unremovedBRs = 0;
            for (let s = 0; s < removedDOMsLength; s++) {
              const removedDOM = removedDOMs[s];
              if (removedDOM.nodeName === "BR" && isManagedLineBreak(removedDOM, targetDOM, editor) || blockCursorElement === removedDOM) {
                targetDOM.appendChild(removedDOM);
                unremovedBRs++;
              }
            }
            if (removedDOMsLength !== unremovedBRs) {
              if (targetDOM === rootElement) {
                targetNode = internalGetRoot(currentEditorState);
              }
              badDOMTargets.set(targetDOM, targetNode);
            }
          }
        }
      }
      if (badDOMTargets.size > 0) {
        for (const [targetDOM, targetNode] of badDOMTargets) {
          if ($isElementNode(targetNode)) {
            const childKeys = targetNode.getChildrenKeys();
            let currentDOM = targetDOM.firstChild;
            for (let s = 0; s < childKeys.length; s++) {
              const key = childKeys[s];
              const correctDOM = editor.getElementByKey(key);
              if (correctDOM === null) {
                continue;
              }
              if (currentDOM == null) {
                targetDOM.appendChild(correctDOM);
                currentDOM = correctDOM;
              } else if (currentDOM !== correctDOM) {
                targetDOM.replaceChild(correctDOM, currentDOM);
              }
              currentDOM = currentDOM.nextSibling;
            }
          } else if ($isTextNode(targetNode)) {
            targetNode.markDirty();
          }
        }
      }
      const records = observer.takeRecords();
      if (records.length > 0) {
        for (let i = 0; i < records.length; i++) {
          const record = records[i];
          const addedNodes = record.addedNodes;
          const target = record.target;
          for (let s = 0; s < addedNodes.length; s++) {
            const addedDOM = addedNodes[s];
            const parentDOM = addedDOM.parentNode;
            if (parentDOM != null && addedDOM.nodeName === "BR" && !isManagedLineBreak(addedDOM, target, editor)) {
              parentDOM.removeChild(addedDOM);
            }
          }
        }
        observer.takeRecords();
      }
      if (selection !== null) {
        if (shouldRevertSelection) {
          selection.dirty = true;
          $setSelection(selection);
        }
        if (IS_FIREFOX && isFirefoxClipboardEvents(editor)) {
          selection.insertRawText(possibleTextForFirefoxPaste);
        }
      }
    });
  } finally {
    isProcessingMutations = false;
  }
}
function $flushRootMutations(editor) {
  const observer = editor._observer;
  if (observer !== null) {
    const mutations = observer.takeRecords();
    $flushMutations(editor, mutations, observer);
  }
}
function initMutationObserver(editor) {
  initTextEntryListener(editor);
  editor._observer = new MutationObserver(
    (mutations, observer) => {
      $flushMutations(editor, mutations, observer);
    }
  );
}

// resources/js/wysiwyg/lexical/core/LexicalNormalization.ts
function $canSimpleTextNodesBeMerged(node1, node2) {
  const node1Mode = node1.__mode;
  const node1Format = node1.__format;
  const node1Style = node1.__style;
  const node2Mode = node2.__mode;
  const node2Format = node2.__format;
  const node2Style = node2.__style;
  return (node1Mode === null || node1Mode === node2Mode) && (node1Format === null || node1Format === node2Format) && (node1Style === null || node1Style === node2Style);
}
function $mergeTextNodes(node1, node2) {
  const writableNode1 = node1.mergeWithSibling(node2);
  const normalizedNodes = getActiveEditor()._normalizedNodes;
  normalizedNodes.add(node1.__key);
  normalizedNodes.add(node2.__key);
  return writableNode1;
}
function $normalizeTextNode(textNode) {
  let node = textNode;
  if (node.__text === "" && node.isSimpleText() && !node.isUnmergeable()) {
    node.remove();
    return;
  }
  let previousNode;
  while ((previousNode = node.getPreviousSibling()) !== null && $isTextNode(previousNode) && previousNode.isSimpleText() && !previousNode.isUnmergeable()) {
    if (previousNode.__text === "") {
      previousNode.remove();
    } else if ($canSimpleTextNodesBeMerged(previousNode, node)) {
      node = $mergeTextNodes(previousNode, node);
      break;
    } else {
      break;
    }
  }
  let nextNode;
  while ((nextNode = node.getNextSibling()) !== null && $isTextNode(nextNode) && nextNode.isSimpleText() && !nextNode.isUnmergeable()) {
    if (nextNode.__text === "") {
      nextNode.remove();
    } else if ($canSimpleTextNodesBeMerged(node, nextNode)) {
      node = $mergeTextNodes(node, nextNode);
      break;
    } else {
      break;
    }
  }
}
function $normalizeSelection(selection) {
  $normalizePoint(selection.anchor);
  $normalizePoint(selection.focus);
  return selection;
}
function $normalizePoint(point) {
  while (point.type === "element") {
    const node = point.getNode();
    const offset = point.offset;
    let nextNode;
    let nextOffsetAtEnd;
    if (offset === node.getChildrenSize()) {
      nextNode = node.getChildAtIndex(offset - 1);
      nextOffsetAtEnd = true;
    } else {
      nextNode = node.getChildAtIndex(offset);
      nextOffsetAtEnd = false;
    }
    if ($isTextNode(nextNode)) {
      point.set(
        nextNode.__key,
        nextOffsetAtEnd ? nextNode.getTextContentSize() : 0,
        "text"
      );
      break;
    } else if (!$isElementNode(nextNode)) {
      break;
    }
    point.set(
      nextNode.__key,
      nextOffsetAtEnd ? nextNode.getChildrenSize() : 0,
      "element"
    );
  }
}

// resources/js/wysiwyg/lexical/core/LexicalUtils.ts
var keyCounter = 1;
function generateRandomKey() {
  return "" + keyCounter++;
}
function getRegisteredNodeOrThrow(editor, nodeType) {
  const registeredNode = editor._nodes.get(nodeType);
  if (registeredNode === void 0) {
    invariant(false, "registeredNode: Type %s not found", nodeType);
  }
  return registeredNode;
}
var isArray = Array.isArray;
var scheduleMicroTask = typeof queueMicrotask === "function" ? queueMicrotask : (fn) => {
  Promise.resolve().then(fn);
};
function $isSelectionCapturedInDecorator(node) {
  return $isDecoratorNode($getNearestNodeFromDOMNode(node));
}
function isSelectionCapturedInDecoratorInput(anchorDOM) {
  const activeElement = document.activeElement;
  if (activeElement === null) {
    return false;
  }
  const nodeName = activeElement.nodeName;
  return $isDecoratorNode($getNearestNodeFromDOMNode(anchorDOM)) && (nodeName === "INPUT" || nodeName === "TEXTAREA" || activeElement.contentEditable === "true" && getEditorPropertyFromDOMNode(activeElement) == null);
}
function isSelectionWithinEditor(editor, anchorDOM, focusDOM) {
  const rootElement = editor.getRootElement();
  try {
    return rootElement !== null && rootElement.contains(anchorDOM) && rootElement.contains(focusDOM) && // Ignore if selection is within nested editor
    anchorDOM !== null && !isSelectionCapturedInDecoratorInput(anchorDOM) && getNearestEditorFromDOMNode(anchorDOM) === editor;
  } catch (error) {
    return false;
  }
}
function isLexicalEditor(editor) {
  return editor instanceof LexicalEditor;
}
function getNearestEditorFromDOMNode(node) {
  let currentNode = node;
  while (currentNode != null) {
    const editor = getEditorPropertyFromDOMNode(currentNode);
    if (isLexicalEditor(editor)) {
      return editor;
    }
    currentNode = getParentElement(currentNode);
  }
  return null;
}
function getEditorPropertyFromDOMNode(node) {
  return node ? node.__lexicalEditor : null;
}
function $isTokenOrSegmented(node) {
  return node.isToken() || node.isSegmented();
}
function isDOMNodeLexicalTextNode(node) {
  return node.nodeType === DOM_TEXT_TYPE;
}
function getDOMTextNode(element) {
  let node = element;
  while (node != null) {
    if (isDOMNodeLexicalTextNode(node)) {
      return node;
    }
    node = node.firstChild;
  }
  return null;
}
function toggleTextFormatType(format, type, alignWithFormat) {
  const activeFormat = TEXT_TYPE_TO_FORMAT[type];
  if (alignWithFormat !== null && (format & activeFormat) === (alignWithFormat & activeFormat)) {
    return format;
  }
  let newFormat = format ^ activeFormat;
  if (type === "subscript") {
    newFormat &= ~TEXT_TYPE_TO_FORMAT.superscript;
  } else if (type === "superscript") {
    newFormat &= ~TEXT_TYPE_TO_FORMAT.subscript;
  }
  return newFormat;
}
function $isLeafNode(node) {
  return $isTextNode(node) || $isLineBreakNode(node) || $isDecoratorNode(node);
}
function $setNodeKey(node, existingKey) {
  if (existingKey != null) {
    if (__DEV__) {
      errorOnNodeKeyConstructorMismatch(node, existingKey);
    }
    node.__key = existingKey;
    return;
  }
  errorOnReadOnly();
  errorOnInfiniteTransforms();
  const editor = getActiveEditor();
  const editorState = getActiveEditorState();
  const key = generateRandomKey();
  editorState._nodeMap.set(key, node);
  if ($isElementNode(node)) {
    editor._dirtyElements.set(key, true);
  } else {
    editor._dirtyLeaves.add(key);
  }
  editor._cloneNotNeeded.add(key);
  editor._dirtyType = HAS_DIRTY_NODES;
  node.__key = key;
}
function errorOnNodeKeyConstructorMismatch(node, existingKey) {
  const editorState = internalGetActiveEditorState();
  if (!editorState) {
    return;
  }
  const existingNode = editorState._nodeMap.get(existingKey);
  if (existingNode && existingNode.constructor !== node.constructor) {
    if (node.constructor.name !== existingNode.constructor.name) {
      invariant(
        false,
        "Lexical node with constructor %s attempted to re-use key from node in active editor state with constructor %s. Keys must not be re-used when the type is changed.",
        node.constructor.name,
        existingNode.constructor.name
      );
    } else {
      invariant(
        false,
        "Lexical node with constructor %s attempted to re-use key from node in active editor state with different constructor with the same name (possibly due to invalid Hot Module Replacement). Keys must not be re-used when the type is changed.",
        node.constructor.name
      );
    }
  }
}
function internalMarkParentElementsAsDirty(parentKey, nodeMap, dirtyElements) {
  let nextParentKey = parentKey;
  while (nextParentKey !== null) {
    if (dirtyElements.has(nextParentKey)) {
      return;
    }
    const node = nodeMap.get(nextParentKey);
    if (node === void 0) {
      break;
    }
    dirtyElements.set(nextParentKey, false);
    nextParentKey = node.__parent;
  }
}
function removeFromParent(node) {
  const oldParent = node.getParent();
  if (oldParent !== null) {
    const writableNode = node.getWritable();
    const writableParent = oldParent.getWritable();
    const prevSibling = node.getPreviousSibling();
    const nextSibling = node.getNextSibling();
    if (prevSibling === null) {
      if (nextSibling !== null) {
        const writableNextSibling = nextSibling.getWritable();
        writableParent.__first = nextSibling.__key;
        writableNextSibling.__prev = null;
      } else {
        writableParent.__first = null;
      }
    } else {
      const writablePrevSibling = prevSibling.getWritable();
      if (nextSibling !== null) {
        const writableNextSibling = nextSibling.getWritable();
        writableNextSibling.__prev = writablePrevSibling.__key;
        writablePrevSibling.__next = writableNextSibling.__key;
      } else {
        writablePrevSibling.__next = null;
      }
      writableNode.__prev = null;
    }
    if (nextSibling === null) {
      if (prevSibling !== null) {
        const writablePrevSibling = prevSibling.getWritable();
        writableParent.__last = prevSibling.__key;
        writablePrevSibling.__next = null;
      } else {
        writableParent.__last = null;
      }
    } else {
      const writableNextSibling = nextSibling.getWritable();
      if (prevSibling !== null) {
        const writablePrevSibling = prevSibling.getWritable();
        writablePrevSibling.__next = writableNextSibling.__key;
        writableNextSibling.__prev = writablePrevSibling.__key;
      } else {
        writableNextSibling.__prev = null;
      }
      writableNode.__next = null;
    }
    writableParent.__size--;
    writableNode.__parent = null;
  }
}
function internalMarkNodeAsDirty(node) {
  errorOnInfiniteTransforms();
  const latest = node.getLatest();
  const parent = latest.__parent;
  const editorState = getActiveEditorState();
  const editor = getActiveEditor();
  const nodeMap = editorState._nodeMap;
  const dirtyElements = editor._dirtyElements;
  if (parent !== null) {
    internalMarkParentElementsAsDirty(parent, nodeMap, dirtyElements);
  }
  const key = latest.__key;
  editor._dirtyType = HAS_DIRTY_NODES;
  if ($isElementNode(node)) {
    dirtyElements.set(key, true);
  } else {
    editor._dirtyLeaves.add(key);
  }
}
function internalMarkSiblingsAsDirty(node) {
  const previousNode = node.getPreviousSibling();
  const nextNode = node.getNextSibling();
  if (previousNode !== null) {
    internalMarkNodeAsDirty(previousNode);
  }
  if (nextNode !== null) {
    internalMarkNodeAsDirty(nextNode);
  }
}
function $setCompositionKey(compositionKey) {
  errorOnReadOnly();
  const editor = getActiveEditor();
  const previousCompositionKey = editor._compositionKey;
  if (compositionKey !== previousCompositionKey) {
    editor._compositionKey = compositionKey;
    if (previousCompositionKey !== null) {
      const node = $getNodeByKey(previousCompositionKey);
      if (node !== null) {
        node.getWritable();
      }
    }
    if (compositionKey !== null) {
      const node = $getNodeByKey(compositionKey);
      if (node !== null) {
        node.getWritable();
      }
    }
  }
}
function $getCompositionKey() {
  if (isCurrentlyReadOnlyMode()) {
    return null;
  }
  const editor = getActiveEditor();
  return editor._compositionKey;
}
function $getNodeByKey(key, _editorState) {
  const editorState = _editorState || getActiveEditorState();
  const node = editorState._nodeMap.get(key);
  if (node === void 0) {
    return null;
  }
  return node;
}
function $getNodeFromDOMNode(dom, editorState) {
  const editor = getActiveEditor();
  const key = dom[`__lexicalKey_${editor._key}`];
  if (key !== void 0) {
    return $getNodeByKey(key, editorState);
  }
  return null;
}
function $getNearestNodeFromDOMNode(startingDOM, editorState) {
  let dom = startingDOM;
  while (dom != null) {
    const node = $getNodeFromDOMNode(dom, editorState);
    if (node !== null) {
      return node;
    }
    dom = getParentElement(dom);
  }
  return null;
}
function cloneDecorators(editor) {
  const currentDecorators = editor._decorators;
  const pendingDecorators = Object.assign({}, currentDecorators);
  editor._pendingDecorators = pendingDecorators;
  return pendingDecorators;
}
function getEditorStateTextContent(editorState) {
  return editorState.read(() => $getRoot().getTextContent());
}
function markAllNodesAsDirty(editor, type) {
  updateEditor(
    editor,
    () => {
      const editorState = getActiveEditorState();
      if (editorState.isEmpty()) {
        return;
      }
      if (type === "root") {
        $getRoot().markDirty();
        return;
      }
      const nodeMap = editorState._nodeMap;
      for (const [, node] of nodeMap) {
        node.markDirty();
      }
    },
    editor._pendingEditorState === null ? {
      tag: "history-merge"
    } : void 0
  );
}
function $getRoot() {
  return internalGetRoot(getActiveEditorState());
}
function internalGetRoot(editorState) {
  return editorState._nodeMap.get("root");
}
function $setSelection(selection) {
  errorOnReadOnly();
  const editorState = getActiveEditorState();
  if (selection !== null) {
    if (__DEV__) {
      if (Object.isFrozen(selection)) {
        invariant(
          false,
          "$setSelection called on frozen selection object. Ensure selection is cloned before passing in."
        );
      }
    }
    selection.dirty = true;
    selection.setCachedNodes(null);
  }
  editorState._selection = selection;
}
function $flushMutations2() {
  errorOnReadOnly();
  const editor = getActiveEditor();
  $flushRootMutations(editor);
}
function $getNodeFromDOM(dom) {
  const editor = getActiveEditor();
  const nodeKey = getNodeKeyFromDOM(dom, editor);
  if (nodeKey === null) {
    const rootElement = editor.getRootElement();
    if (dom === rootElement) {
      return $getNodeByKey("root");
    }
    return null;
  }
  return $getNodeByKey(nodeKey);
}
function getTextNodeOffset(node, moveSelectionToEnd) {
  return moveSelectionToEnd ? node.getTextContentSize() : 0;
}
function getNodeKeyFromDOM(dom, editor) {
  let node = dom;
  while (node != null) {
    const key = node[`__lexicalKey_${editor._key}`];
    if (key !== void 0) {
      return key;
    }
    node = getParentElement(node);
  }
  return null;
}
function doesContainGrapheme(str) {
  return /[\uD800-\uDBFF][\uDC00-\uDFFF]/g.test(str);
}
function getEditorsToPropagate(editor) {
  const editorsToPropagate = [];
  let currentEditor = editor;
  while (currentEditor !== null) {
    editorsToPropagate.push(currentEditor);
    currentEditor = currentEditor._parentEditor;
  }
  return editorsToPropagate;
}
function createUID() {
  return Math.random().toString(36).replace(/[^a-z]+/g, "").substr(0, 5);
}
function getAnchorTextFromDOM(anchorNode) {
  if (anchorNode.nodeType === DOM_TEXT_TYPE) {
    return anchorNode.nodeValue;
  }
  return null;
}
function $updateSelectedTextFromDOM(isCompositionEnd, editor, data) {
  const domSelection = getDOMSelection(editor._window);
  if (domSelection === null) {
    return;
  }
  const anchorNode = domSelection.anchorNode;
  let { anchorOffset, focusOffset } = domSelection;
  if (anchorNode !== null) {
    let textContent = getAnchorTextFromDOM(anchorNode);
    const node = $getNearestNodeFromDOMNode(anchorNode);
    if (textContent !== null && $isTextNode(node)) {
      if (textContent === COMPOSITION_SUFFIX && data) {
        const offset = data.length;
        textContent = data;
        anchorOffset = offset;
        focusOffset = offset;
      }
      if (textContent !== null) {
        $updateTextNodeFromDOMContent(
          node,
          textContent,
          anchorOffset,
          focusOffset,
          isCompositionEnd
        );
      }
    }
  }
}
function $updateTextNodeFromDOMContent(textNode, textContent, anchorOffset, focusOffset, compositionEnd) {
  let node = textNode;
  if (node.isAttached() && (compositionEnd || !node.isDirty())) {
    const isComposing = node.isComposing();
    let normalizedTextContent = textContent;
    if ((isComposing || compositionEnd) && textContent[textContent.length - 1] === COMPOSITION_SUFFIX) {
      normalizedTextContent = textContent.slice(0, -1);
    }
    const prevTextContent = node.getTextContent();
    if (compositionEnd || normalizedTextContent !== prevTextContent) {
      if (normalizedTextContent === "") {
        $setCompositionKey(null);
        if (!IS_SAFARI && !IS_IOS && !IS_APPLE_WEBKIT) {
          const editor = getActiveEditor();
          setTimeout(() => {
            editor.update(() => {
              if (node.isAttached()) {
                node.remove();
              }
            });
          }, 20);
        } else {
          node.remove();
        }
        return;
      }
      const parent = node.getParent();
      const prevSelection = $getPreviousSelection();
      const prevTextContentSize = node.getTextContentSize();
      const compositionKey = $getCompositionKey();
      const nodeKey = node.getKey();
      if (node.isToken() || compositionKey !== null && nodeKey === compositionKey && !isComposing || // Check if character was added at the start or boundaries when not insertable, and we need
      // to clear this input from occurring as that action wasn't permitted.
      $isRangeSelection(prevSelection) && (parent !== null && !parent.canInsertTextBefore() && prevSelection.anchor.offset === 0 || prevSelection.anchor.key === textNode.__key && prevSelection.anchor.offset === 0 && !node.canInsertTextBefore() && !isComposing || prevSelection.focus.key === textNode.__key && prevSelection.focus.offset === prevTextContentSize && !node.canInsertTextAfter() && !isComposing)) {
        node.markDirty();
        return;
      }
      const selection = $getSelection();
      if (!$isRangeSelection(selection) || anchorOffset === null || focusOffset === null) {
        node.setTextContent(normalizedTextContent);
        return;
      }
      selection.setTextNodeRange(node, anchorOffset, node, focusOffset);
      if (node.isSegmented()) {
        const originalTextContent = node.getTextContent();
        const replacement = $createTextNode(originalTextContent);
        node.replace(replacement);
        node = replacement;
      }
      node.setTextContent(normalizedTextContent);
    }
  }
}
function $previousSiblingDoesNotAcceptText(node) {
  const previousSibling = node.getPreviousSibling();
  return ($isTextNode(previousSibling) || $isElementNode(previousSibling) && previousSibling.isInline()) && !previousSibling.canInsertTextAfter();
}
function $shouldInsertTextAfterOrBeforeTextNode(selection, node) {
  if (node.isSegmented()) {
    return true;
  }
  if (!selection.isCollapsed()) {
    return false;
  }
  const offset = selection.anchor.offset;
  const parent = node.getParentOrThrow();
  const isToken = node.isToken();
  if (offset === 0) {
    return !node.canInsertTextBefore() || !parent.canInsertTextBefore() && !node.isComposing() || isToken || $previousSiblingDoesNotAcceptText(node);
  } else if (offset === node.getTextContentSize()) {
    return !node.canInsertTextAfter() || !parent.canInsertTextAfter() && !node.isComposing() || isToken;
  } else {
    return false;
  }
}
function isTab(key, altKey, ctrlKey, metaKey) {
  return key === "Tab" && !altKey && !ctrlKey && !metaKey;
}
function isBold(key, altKey, metaKey, ctrlKey) {
  return key.toLowerCase() === "b" && !altKey && controlOrMeta(metaKey, ctrlKey);
}
function isItalic(key, altKey, metaKey, ctrlKey) {
  return key.toLowerCase() === "i" && !altKey && controlOrMeta(metaKey, ctrlKey);
}
function isUnderline(key, altKey, metaKey, ctrlKey) {
  return key.toLowerCase() === "u" && !altKey && controlOrMeta(metaKey, ctrlKey);
}
function isParagraph(key, shiftKey) {
  return isReturn(key) && !shiftKey;
}
function isLineBreak(key, shiftKey) {
  return isReturn(key) && shiftKey;
}
function isOpenLineBreak(key, ctrlKey) {
  return IS_APPLE && ctrlKey && key.toLowerCase() === "o";
}
function isDeleteWordBackward(key, altKey, ctrlKey) {
  return isBackspace(key) && (IS_APPLE ? altKey : ctrlKey);
}
function isDeleteWordForward(key, altKey, ctrlKey) {
  return isDelete(key) && (IS_APPLE ? altKey : ctrlKey);
}
function isDeleteLineBackward(key, metaKey) {
  return IS_APPLE && metaKey && isBackspace(key);
}
function isDeleteLineForward(key, metaKey) {
  return IS_APPLE && metaKey && isDelete(key);
}
function isDeleteBackward(key, altKey, metaKey, ctrlKey) {
  if (IS_APPLE) {
    if (altKey || metaKey) {
      return false;
    }
    return isBackspace(key) || key.toLowerCase() === "h" && ctrlKey;
  }
  if (ctrlKey || altKey || metaKey) {
    return false;
  }
  return isBackspace(key);
}
function isDeleteForward(key, ctrlKey, shiftKey, altKey, metaKey) {
  if (IS_APPLE) {
    if (shiftKey || altKey || metaKey) {
      return false;
    }
    return isDelete(key) || key.toLowerCase() === "d" && ctrlKey;
  }
  if (ctrlKey || altKey || metaKey) {
    return false;
  }
  return isDelete(key);
}
function isUndo(key, shiftKey, metaKey, ctrlKey) {
  return key.toLowerCase() === "z" && !shiftKey && controlOrMeta(metaKey, ctrlKey);
}
function isRedo(key, shiftKey, metaKey, ctrlKey) {
  if (IS_APPLE) {
    return key.toLowerCase() === "z" && metaKey && shiftKey;
  }
  return key.toLowerCase() === "y" && ctrlKey || key.toLowerCase() === "z" && ctrlKey && shiftKey;
}
function isCopy(key, shiftKey, metaKey, ctrlKey) {
  if (shiftKey) {
    return false;
  }
  if (key.toLowerCase() === "c") {
    return IS_APPLE ? metaKey : ctrlKey;
  }
  return false;
}
function isCut(key, shiftKey, metaKey, ctrlKey) {
  if (shiftKey) {
    return false;
  }
  if (key.toLowerCase() === "x") {
    return IS_APPLE ? metaKey : ctrlKey;
  }
  return false;
}
function isArrowLeft(key) {
  return key === "ArrowLeft";
}
function isArrowRight(key) {
  return key === "ArrowRight";
}
function isArrowUp(key) {
  return key === "ArrowUp";
}
function isArrowDown(key) {
  return key === "ArrowDown";
}
function isMoveBackward(key, ctrlKey, altKey, metaKey) {
  return isArrowLeft(key) && !ctrlKey && !metaKey && !altKey;
}
function isMoveToStart(key, ctrlKey, shiftKey, altKey, metaKey) {
  return isArrowLeft(key) && !altKey && !shiftKey && (ctrlKey || metaKey);
}
function isMoveForward(key, ctrlKey, altKey, metaKey) {
  return isArrowRight(key) && !ctrlKey && !metaKey && !altKey;
}
function isMoveToEnd(key, ctrlKey, shiftKey, altKey, metaKey) {
  return isArrowRight(key) && !altKey && !shiftKey && (ctrlKey || metaKey);
}
function isMoveUp(key, ctrlKey, metaKey) {
  return isArrowUp(key) && !ctrlKey && !metaKey;
}
function isMoveDown(key, ctrlKey, metaKey) {
  return isArrowDown(key) && !ctrlKey && !metaKey;
}
function isModifier(ctrlKey, shiftKey, altKey, metaKey) {
  return ctrlKey || shiftKey || altKey || metaKey;
}
function isSpace(key) {
  return key === " ";
}
function controlOrMeta(metaKey, ctrlKey) {
  if (IS_APPLE) {
    return metaKey;
  }
  return ctrlKey;
}
function isReturn(key) {
  return key === "Enter";
}
function isBackspace(key) {
  return key === "Backspace";
}
function isEscape(key) {
  return key === "Escape";
}
function isDelete(key) {
  return key === "Delete";
}
function isSelectAll(key, metaKey, ctrlKey) {
  return key.toLowerCase() === "a" && controlOrMeta(metaKey, ctrlKey);
}
function $selectAll() {
  const root = $getRoot();
  const selection = root.select(0, root.getChildrenSize());
  $setSelection($normalizeSelection(selection));
}
function getCachedClassNameArray(classNamesTheme, classNameThemeType) {
  if (classNamesTheme.__lexicalClassNameCache === void 0) {
    classNamesTheme.__lexicalClassNameCache = {};
  }
  const classNamesCache = classNamesTheme.__lexicalClassNameCache;
  const cachedClassNames = classNamesCache[classNameThemeType];
  if (cachedClassNames !== void 0) {
    return cachedClassNames;
  }
  const classNames = classNamesTheme[classNameThemeType];
  if (typeof classNames === "string") {
    const classNamesArr = normalizeClassNames(classNames);
    classNamesCache[classNameThemeType] = classNamesArr;
    return classNamesArr;
  }
  return classNames;
}
function setMutatedNode(mutatedNodes2, registeredNodes, mutationListeners, node, mutation) {
  if (mutationListeners.size === 0) {
    return;
  }
  const nodeType = node.__type;
  const nodeKey = node.__key;
  const registeredNode = registeredNodes.get(nodeType);
  if (registeredNode === void 0) {
    invariant(false, "Type %s not in registeredNodes", nodeType);
  }
  const klass = registeredNode.klass;
  let mutatedNodesByType = mutatedNodes2.get(klass);
  if (mutatedNodesByType === void 0) {
    mutatedNodesByType = /* @__PURE__ */ new Map();
    mutatedNodes2.set(klass, mutatedNodesByType);
  }
  const prevMutation = mutatedNodesByType.get(nodeKey);
  const isMove = prevMutation === "destroyed" && mutation === "created";
  if (prevMutation === void 0 || isMove) {
    mutatedNodesByType.set(nodeKey, isMove ? "updated" : mutation);
  }
}
function resolveElement(element, isBackward, focusOffset) {
  const parent = element.getParent();
  let offset = focusOffset;
  let block = element;
  if (parent !== null) {
    if (isBackward && focusOffset === 0) {
      offset = block.getIndexWithinParent();
      block = parent;
    } else if (!isBackward && focusOffset === block.getChildrenSize()) {
      offset = block.getIndexWithinParent() + 1;
      block = parent;
    }
  }
  return block.getChildAtIndex(isBackward ? offset - 1 : offset);
}
function $getAdjacentNode(focus, isBackward) {
  const focusOffset = focus.offset;
  if (focus.type === "element") {
    const block = focus.getNode();
    return resolveElement(block, isBackward, focusOffset);
  } else {
    const focusNode = focus.getNode();
    if (isBackward && focusOffset === 0 || !isBackward && focusOffset === focusNode.getTextContentSize()) {
      const possibleNode = isBackward ? focusNode.getPreviousSibling() : focusNode.getNextSibling();
      if (possibleNode === null) {
        return resolveElement(
          focusNode.getParentOrThrow(),
          isBackward,
          focusNode.getIndexWithinParent() + (isBackward ? 0 : 1)
        );
      }
      return possibleNode;
    }
  }
  return null;
}
function isFirefoxClipboardEvents(editor) {
  const event = getWindow(editor).event;
  const inputType = event && event.inputType;
  return inputType === "insertFromPaste" || inputType === "insertFromPasteAsQuotation";
}
function dispatchCommand(editor, command, payload) {
  return triggerCommandListeners(editor, command, payload);
}
function $textContentRequiresDoubleLinebreakAtEnd(node) {
  return !$isRootNode(node) && !node.isLastChild() && !node.isInline();
}
function getElementByKeyOrThrow(editor, key) {
  const element = editor._keyToDOMMap.get(key);
  if (element === void 0) {
    invariant(
      false,
      "Reconciliation: could not find DOM element for node key %s",
      key
    );
  }
  return element;
}
function getParentElement(node) {
  const parentElement = node.assignedSlot || node.parentElement;
  return parentElement !== null && parentElement.nodeType === 11 ? parentElement.host : parentElement;
}
function scrollIntoViewIfNeeded(editor, selectionRect, rootElement) {
  const doc = rootElement.ownerDocument;
  const defaultView = doc.defaultView;
  if (defaultView === null) {
    return;
  }
  let { top: currentTop, bottom: currentBottom } = selectionRect;
  let targetTop = 0;
  let targetBottom = 0;
  let element = rootElement;
  while (element !== null) {
    const isBodyElement = element === doc.body;
    if (isBodyElement) {
      targetTop = 0;
      targetBottom = getWindow(editor).innerHeight;
    } else {
      const targetRect = element.getBoundingClientRect();
      targetTop = targetRect.top;
      targetBottom = targetRect.bottom;
    }
    let diff = 0;
    if (currentTop < targetTop) {
      diff = -(targetTop - currentTop);
    } else if (currentBottom > targetBottom) {
      diff = currentBottom - targetBottom;
    }
    if (diff !== 0) {
      if (isBodyElement) {
        defaultView.scrollBy(0, diff);
      } else {
        const scrollTop = element.scrollTop;
        element.scrollTop += diff;
        const yOffset = element.scrollTop - scrollTop;
        currentTop -= yOffset;
        currentBottom -= yOffset;
      }
    }
    if (isBodyElement) {
      break;
    }
    element = getParentElement(element);
  }
}
function $maybeMoveChildrenSelectionToParent(parentNode) {
  const selection = $getSelection();
  if (!$isRangeSelection(selection) || !$isElementNode(parentNode)) {
    return selection;
  }
  const { anchor, focus } = selection;
  const anchorNode = anchor.getNode();
  const focusNode = focus.getNode();
  if ($hasAncestor(anchorNode, parentNode)) {
    anchor.set(parentNode.__key, 0, "element");
  }
  if ($hasAncestor(focusNode, parentNode)) {
    focus.set(parentNode.__key, 0, "element");
  }
  return selection;
}
function $hasAncestor(child, targetNode) {
  let parent = child.getParent();
  while (parent !== null) {
    if (parent.is(targetNode)) {
      return true;
    }
    parent = parent.getParent();
  }
  return false;
}
function getDefaultView(domElem) {
  const ownerDoc = domElem.ownerDocument;
  return ownerDoc && ownerDoc.defaultView || null;
}
function getWindow(editor) {
  const windowObj = editor._window;
  if (windowObj === null) {
    invariant(false, "window object not found");
  }
  return windowObj;
}
function $getNearestRootOrShadowRoot(node) {
  let parent = node.getParentOrThrow();
  while (parent !== null) {
    if ($isRootOrShadowRoot(parent)) {
      return parent;
    }
    parent = parent.getParentOrThrow();
  }
  return parent;
}
var ShadowRootNodeBrand = Symbol.for(
  "@lexical/ShadowRootNodeBrand"
);
function $isRootOrShadowRoot(node) {
  return $isRootNode(node) || $isElementNode(node) && node.isShadowRoot();
}
function $applyNodeReplacement(node) {
  const editor = getActiveEditor();
  const nodeType = node.constructor.getType();
  const registeredNode = editor._nodes.get(nodeType);
  if (registeredNode === void 0) {
    invariant(
      false,
      '$initializeNode failed. Ensure node has been registered to the editor. You can do this by passing the node class via the "nodes" array in the editor config.'
    );
  }
  const replaceFunc = registeredNode.replace;
  if (replaceFunc !== null) {
    const replacementNode = replaceFunc(node);
    if (!(replacementNode instanceof node.constructor)) {
      invariant(
        false,
        "$initializeNode failed. Ensure replacement node is a subclass of the original node."
      );
    }
    return replacementNode;
  }
  return node;
}
function errorOnInsertTextNodeOnRoot(node, insertNode) {
  const parentNode = node.getParent();
  if ($isRootNode(parentNode) && !$isElementNode(insertNode) && !$isDecoratorNode(insertNode)) {
    invariant(
      false,
      "Only element or decorator nodes can be inserted in to the root node"
    );
  }
}
function createBlockCursorElement(editorConfig) {
  const theme2 = editorConfig.theme;
  const element = document.createElement("div");
  element.contentEditable = "false";
  element.setAttribute("data-lexical-cursor", "true");
  let blockCursorTheme = theme2.blockCursor;
  if (blockCursorTheme !== void 0) {
    if (typeof blockCursorTheme === "string") {
      const classNamesArr = normalizeClassNames(blockCursorTheme);
      blockCursorTheme = theme2.blockCursor = classNamesArr;
    }
    if (blockCursorTheme !== void 0) {
      element.classList.add(...blockCursorTheme);
    }
  }
  return element;
}
function needsBlockCursor(node) {
  return ($isDecoratorNode(node) || $isElementNode(node) && !node.canBeEmpty()) && !node.isInline();
}
function removeDOMBlockCursorElement(blockCursorElement, editor, rootElement) {
  rootElement.style.removeProperty("caret-color");
  editor._blockCursorElement = null;
  const parentElement = blockCursorElement.parentElement;
  if (parentElement !== null) {
    parentElement.removeChild(blockCursorElement);
  }
}
function updateDOMBlockCursorElement(editor, rootElement, nextSelection) {
  let blockCursorElement = editor._blockCursorElement;
  if ($isRangeSelection(nextSelection) && nextSelection.isCollapsed() && nextSelection.anchor.type === "element" && rootElement.contains(document.activeElement)) {
    const anchor = nextSelection.anchor;
    const elementNode = anchor.getNode();
    const offset = anchor.offset;
    const elementNodeSize = elementNode.getChildrenSize();
    let isBlockCursor = false;
    let insertBeforeElement = null;
    if (offset === elementNodeSize) {
      const child = elementNode.getChildAtIndex(offset - 1);
      if (needsBlockCursor(child)) {
        isBlockCursor = true;
      }
    } else {
      const child = elementNode.getChildAtIndex(offset);
      if (needsBlockCursor(child)) {
        const sibling = child.getPreviousSibling();
        if (sibling === null || needsBlockCursor(sibling)) {
          isBlockCursor = true;
          insertBeforeElement = editor.getElementByKey(
            child.__key
          );
        }
      }
    }
    if (isBlockCursor) {
      const elementDOM = editor.getElementByKey(
        elementNode.__key
      );
      if (blockCursorElement === null) {
        editor._blockCursorElement = blockCursorElement = createBlockCursorElement(editor._config);
      }
      rootElement.style.caretColor = "transparent";
      if (insertBeforeElement === null) {
        elementDOM.appendChild(blockCursorElement);
      } else {
        elementDOM.insertBefore(blockCursorElement, insertBeforeElement);
      }
      return;
    }
  }
  if (blockCursorElement !== null) {
    removeDOMBlockCursorElement(blockCursorElement, editor, rootElement);
  }
}
function getDOMSelection(targetWindow) {
  return !CAN_USE_DOM ? null : (targetWindow || window).getSelection();
}
function isHTMLAnchorElement(x) {
  return isHTMLElement(x) && x.tagName === "A";
}
function isHTMLElement(x) {
  return x.nodeType === 1;
}
function isInlineDomNode(node) {
  const inlineNodes = new RegExp(
    /^(a|abbr|acronym|b|cite|code|del|em|i|ins|kbd|label|output|q|ruby|s|samp|span|strong|sub|sup|time|u|tt|var|#text)$/,
    "i"
  );
  return node.nodeName.match(inlineNodes) !== null;
}
function isBlockDomNode(node) {
  const blockNodes = new RegExp(
    /^(address|article|aside|blockquote|canvas|dd|div|dl|dt|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|header|hr|li|main|nav|noscript|ol|p|pre|section|table|td|tfoot|ul|video)$/,
    "i"
  );
  return node.nodeName.match(blockNodes) !== null;
}
function INTERNAL_$isBlock(node) {
  if ($isRootNode(node) || $isDecoratorNode(node) && !node.isInline()) {
    return true;
  }
  if (!$isElementNode(node) || $isRootOrShadowRoot(node)) {
    return false;
  }
  const firstChild = node.getFirstChild();
  const isLeafElement = firstChild === null || $isLineBreakNode(firstChild) || $isTextNode(firstChild) || firstChild.isInline();
  return !node.isInline() && node.canBeEmpty() !== false && isLeafElement;
}
function $getAncestor(node, predicate) {
  let parent = node;
  while (parent !== null && parent.getParent() !== null && !predicate(parent)) {
    parent = parent.getParentOrThrow();
  }
  return predicate(parent) ? parent : null;
}
function $getEditor() {
  return getActiveEditor();
}
var cachedNodeMaps = /* @__PURE__ */ new WeakMap();
var EMPTY_TYPE_TO_NODE_MAP = /* @__PURE__ */ new Map();
function getCachedTypeToNodeMap(editorState) {
  if (!editorState._readOnly && editorState.isEmpty()) {
    return EMPTY_TYPE_TO_NODE_MAP;
  }
  invariant(
    editorState._readOnly,
    "getCachedTypeToNodeMap called with a writable EditorState"
  );
  let typeToNodeMap = cachedNodeMaps.get(editorState);
  if (!typeToNodeMap) {
    typeToNodeMap = /* @__PURE__ */ new Map();
    cachedNodeMaps.set(editorState, typeToNodeMap);
    for (const [nodeKey, node] of editorState._nodeMap) {
      const nodeType = node.__type;
      let nodeMap = typeToNodeMap.get(nodeType);
      if (!nodeMap) {
        nodeMap = /* @__PURE__ */ new Map();
        typeToNodeMap.set(nodeType, nodeMap);
      }
      nodeMap.set(nodeKey, node);
    }
  }
  return typeToNodeMap;
}
function $cloneWithProperties(latestNode) {
  const constructor = latestNode.constructor;
  const mutableNode = constructor.clone(latestNode);
  mutableNode.afterCloneFrom(latestNode);
  if (__DEV__) {
    invariant(
      mutableNode.__key === latestNode.__key,
      "$cloneWithProperties: %s.clone(node) (with type '%s') did not return a node with the same key, make sure to specify node.__key as the last argument to the constructor",
      constructor.name,
      constructor.getType()
    );
    invariant(
      mutableNode.__parent === latestNode.__parent && mutableNode.__next === latestNode.__next && mutableNode.__prev === latestNode.__prev,
      "$cloneWithProperties: %s.clone(node) (with type '%s') overrided afterCloneFrom but did not call super.afterCloneFrom(prevNode)",
      constructor.name,
      constructor.getType()
    );
  }
  return mutableNode;
}

// resources/js/wysiwyg/lexical/core/LexicalGC.ts
function $garbageCollectDetachedDecorators(editor, pendingEditorState) {
  const currentDecorators = editor._decorators;
  const pendingDecorators = editor._pendingDecorators;
  let decorators = pendingDecorators || currentDecorators;
  const nodeMap = pendingEditorState._nodeMap;
  let key;
  for (key in decorators) {
    if (!nodeMap.has(key)) {
      if (decorators === currentDecorators) {
        decorators = cloneDecorators(editor);
      }
      delete decorators[key];
    }
  }
}
function $garbageCollectDetachedDeepChildNodes(node, parentKey, prevNodeMap, nodeMap, nodeMapDelete, dirtyNodes) {
  let child = node.getFirstChild();
  while (child !== null) {
    const childKey = child.__key;
    if (child.__parent === parentKey) {
      if ($isElementNode(child)) {
        $garbageCollectDetachedDeepChildNodes(
          child,
          childKey,
          prevNodeMap,
          nodeMap,
          nodeMapDelete,
          dirtyNodes
        );
      }
      if (!prevNodeMap.has(childKey)) {
        dirtyNodes.delete(childKey);
      }
      nodeMapDelete.push(childKey);
    }
    child = child.getNextSibling();
  }
}
function $garbageCollectDetachedNodes(prevEditorState, editorState, dirtyLeaves, dirtyElements) {
  const prevNodeMap = prevEditorState._nodeMap;
  const nodeMap = editorState._nodeMap;
  const nodeMapDelete = [];
  for (const [nodeKey] of dirtyElements) {
    const node = nodeMap.get(nodeKey);
    if (node !== void 0) {
      if (!node.isAttached()) {
        if ($isElementNode(node)) {
          $garbageCollectDetachedDeepChildNodes(
            node,
            nodeKey,
            prevNodeMap,
            nodeMap,
            nodeMapDelete,
            dirtyElements
          );
        }
        if (!prevNodeMap.has(nodeKey)) {
          dirtyElements.delete(nodeKey);
        }
        nodeMapDelete.push(nodeKey);
      }
    }
  }
  for (const nodeKey of nodeMapDelete) {
    nodeMap.delete(nodeKey);
  }
  for (const nodeKey of dirtyLeaves) {
    const node = nodeMap.get(nodeKey);
    if (node !== void 0 && !node.isAttached()) {
      if (!prevNodeMap.has(nodeKey)) {
        dirtyLeaves.delete(nodeKey);
      }
      nodeMap.delete(nodeKey);
    }
  }
}

// resources/js/wysiwyg/lexical/core/LexicalReconciler.ts
var subTreeTextContent = "";
var subTreeTextFormat = null;
var subTreeTextStyle = "";
var editorTextContent = "";
var activeEditorConfig;
var activeEditor;
var activeEditorNodes;
var treatAllNodesAsDirty = false;
var activeEditorStateReadOnly = false;
var activeMutationListeners;
var activeDirtyElements;
var activeDirtyLeaves;
var activePrevNodeMap;
var activeNextNodeMap;
var activePrevKeyToDOMMap;
var mutatedNodes;
function destroyNode(key, parentDOM) {
  const node = activePrevNodeMap.get(key);
  if (parentDOM !== null) {
    const dom = getPrevElementByKeyOrThrow(key);
    if (dom.parentNode === parentDOM) {
      parentDOM.removeChild(dom);
    }
  }
  if (!activeNextNodeMap.has(key)) {
    activeEditor._keyToDOMMap.delete(key);
  }
  if ($isElementNode(node)) {
    const children = createChildrenArray(node, activePrevNodeMap);
    destroyChildren(children, 0, children.length - 1, null);
  }
  if (node !== void 0) {
    setMutatedNode(
      mutatedNodes,
      activeEditorNodes,
      activeMutationListeners,
      node,
      "destroyed"
    );
  }
}
function destroyChildren(children, _startIndex, endIndex, dom) {
  let startIndex = _startIndex;
  for (; startIndex <= endIndex; ++startIndex) {
    const child = children[startIndex];
    if (child !== void 0) {
      destroyNode(child, dom);
    }
  }
}
function $createNode(key, parentDOM, insertDOM) {
  const node = activeNextNodeMap.get(key);
  if (node === void 0) {
    invariant(false, "createNode: node does not exist in nodeMap");
  }
  const dom = node.createDOM(activeEditorConfig, activeEditor);
  storeDOMWithKey(key, dom, activeEditor);
  if ($isTextNode(node)) {
    dom.setAttribute("data-lexical-text", "true");
  } else if ($isDecoratorNode(node)) {
    dom.setAttribute("data-lexical-decorator", "true");
  }
  if ($isElementNode(node)) {
    const childrenSize = node.__size;
    if (childrenSize !== 0) {
      const endIndex = childrenSize - 1;
      const children = createChildrenArray(node, activeNextNodeMap);
      $createChildren(children, node, 0, endIndex, dom, null);
    }
    if (!node.isInline()) {
      reconcileElementTerminatingLineBreak(null, node, dom);
    }
    if ($textContentRequiresDoubleLinebreakAtEnd(node)) {
      subTreeTextContent += DOUBLE_LINE_BREAK;
      editorTextContent += DOUBLE_LINE_BREAK;
    }
  } else {
    const text = node.getTextContent();
    if ($isDecoratorNode(node)) {
      const decorator = node.decorate(activeEditor, activeEditorConfig);
      if (decorator !== null) {
        reconcileDecorator(key, decorator);
      }
      dom.contentEditable = "false";
    }
    subTreeTextContent += text;
    editorTextContent += text;
  }
  if (parentDOM !== null) {
    const inserted = node?.insertDOMIntoParent(dom, parentDOM);
    if (!inserted) {
      if (insertDOM != null) {
        parentDOM.insertBefore(dom, insertDOM);
      } else {
        const possibleLineBreak = parentDOM.__lexicalLineBreak;
        if (possibleLineBreak != null) {
          parentDOM.insertBefore(dom, possibleLineBreak);
        } else {
          parentDOM.appendChild(dom);
        }
      }
    }
  }
  if (__DEV__) {
    Object.freeze(node);
  }
  setMutatedNode(
    mutatedNodes,
    activeEditorNodes,
    activeMutationListeners,
    node,
    "created"
  );
  return dom;
}
function $createChildren(children, element, _startIndex, endIndex, dom, insertDOM) {
  const previousSubTreeTextContent = subTreeTextContent;
  subTreeTextContent = "";
  let startIndex = _startIndex;
  for (; startIndex <= endIndex; ++startIndex) {
    $createNode(children[startIndex], dom, insertDOM);
    const node = activeNextNodeMap.get(children[startIndex]);
    if (node !== null && $isTextNode(node)) {
      if (subTreeTextFormat === null) {
        subTreeTextFormat = node.getFormat();
      }
      if (subTreeTextStyle === "") {
        subTreeTextStyle = node.getStyle();
      }
    }
  }
  if ($textContentRequiresDoubleLinebreakAtEnd(element)) {
    subTreeTextContent += DOUBLE_LINE_BREAK;
  }
  dom.__lexicalTextContent = subTreeTextContent;
  subTreeTextContent = previousSubTreeTextContent + subTreeTextContent;
}
function isLastChildLineBreakOrDecorator(childKey, nodeMap) {
  const node = nodeMap.get(childKey);
  return $isLineBreakNode(node) || $isDecoratorNode(node) && node.isInline();
}
function reconcileElementTerminatingLineBreak(prevElement, nextElement, dom) {
  const prevLineBreak = prevElement !== null && (prevElement.__size === 0 || isLastChildLineBreakOrDecorator(
    prevElement.__last,
    activePrevNodeMap
  ));
  const nextLineBreak = nextElement.__size === 0 || isLastChildLineBreakOrDecorator(
    nextElement.__last,
    activeNextNodeMap
  );
  if (prevLineBreak) {
    if (!nextLineBreak) {
      const element = dom.__lexicalLineBreak;
      if (element != null) {
        try {
          dom.removeChild(element);
        } catch (error) {
          if (typeof error === "object" && error != null) {
            const msg = `${error.toString()} Parent: ${dom.tagName}, child: ${element.tagName}.`;
            throw new Error(msg);
          } else {
            throw error;
          }
        }
      }
      dom.__lexicalLineBreak = null;
    }
  } else if (nextLineBreak) {
    const element = document.createElement("br");
    dom.__lexicalLineBreak = element;
    dom.appendChild(element);
  }
}
function reconcileParagraphFormat(element) {
  if ($isParagraphNode(element) && subTreeTextFormat != null && !activeEditorStateReadOnly) {
    element.setTextStyle(subTreeTextStyle);
  }
}
function reconcileParagraphStyle(element) {
  if ($isParagraphNode(element) && subTreeTextStyle !== "" && subTreeTextStyle !== element.__textStyle && !activeEditorStateReadOnly) {
    element.setTextStyle(subTreeTextStyle);
  }
}
function $reconcileChildrenWithDirection(prevElement, nextElement, dom) {
  subTreeTextFormat = null;
  subTreeTextStyle = "";
  $reconcileChildren(prevElement, nextElement, dom);
  reconcileParagraphFormat(nextElement);
  reconcileParagraphStyle(nextElement);
}
function createChildrenArray(element, nodeMap) {
  const children = [];
  let nodeKey = element.__first;
  while (nodeKey !== null) {
    const node = nodeMap.get(nodeKey);
    if (node === void 0) {
      invariant(false, "createChildrenArray: node does not exist in nodeMap");
    }
    children.push(nodeKey);
    nodeKey = node.__next;
  }
  return children;
}
function $reconcileChildren(prevElement, nextElement, dom) {
  const previousSubTreeTextContent = subTreeTextContent;
  const prevChildrenSize = prevElement.__size;
  const nextChildrenSize = nextElement.__size;
  subTreeTextContent = "";
  if (prevChildrenSize === 1 && nextChildrenSize === 1) {
    const prevFirstChildKey = prevElement.__first;
    const nextFrstChildKey = nextElement.__first;
    if (prevFirstChildKey === nextFrstChildKey) {
      $reconcileNode(prevFirstChildKey, dom);
    } else {
      const lastDOM = getPrevElementByKeyOrThrow(prevFirstChildKey);
      const replacementDOM = $createNode(nextFrstChildKey, null, null);
      try {
        dom.replaceChild(replacementDOM, lastDOM);
      } catch (error) {
        if (typeof error === "object" && error != null) {
          const msg = `${error.toString()} Parent: ${dom.tagName}, new child: {tag: ${replacementDOM.tagName} key: ${nextFrstChildKey}}, old child: {tag: ${lastDOM.tagName}, key: ${prevFirstChildKey}}.`;
          throw new Error(msg);
        } else {
          throw error;
        }
      }
      destroyNode(prevFirstChildKey, null);
    }
    const nextChildNode = activeNextNodeMap.get(nextFrstChildKey);
    if ($isTextNode(nextChildNode)) {
      if (subTreeTextFormat === null) {
        subTreeTextFormat = nextChildNode.getFormat();
      }
      if (subTreeTextStyle === "") {
        subTreeTextStyle = nextChildNode.getStyle();
      }
    }
  } else {
    const prevChildren = createChildrenArray(prevElement, activePrevNodeMap);
    const nextChildren = createChildrenArray(nextElement, activeNextNodeMap);
    if (prevChildrenSize === 0) {
      if (nextChildrenSize !== 0) {
        $createChildren(
          nextChildren,
          nextElement,
          0,
          nextChildrenSize - 1,
          dom,
          null
        );
      }
    } else if (nextChildrenSize === 0) {
      if (prevChildrenSize !== 0) {
        const lexicalLineBreak = dom.__lexicalLineBreak;
        const canUseFastPath = lexicalLineBreak == null;
        destroyChildren(
          prevChildren,
          0,
          prevChildrenSize - 1,
          canUseFastPath ? null : dom
        );
        if (canUseFastPath) {
          dom.textContent = "";
        }
      }
    } else {
      $reconcileNodeChildren(
        nextElement,
        prevChildren,
        nextChildren,
        prevChildrenSize,
        nextChildrenSize,
        dom
      );
    }
  }
  if ($textContentRequiresDoubleLinebreakAtEnd(nextElement)) {
    subTreeTextContent += DOUBLE_LINE_BREAK;
  }
  dom.__lexicalTextContent = subTreeTextContent;
  subTreeTextContent = previousSubTreeTextContent + subTreeTextContent;
}
function $reconcileNode(key, parentDOM) {
  const prevNode = activePrevNodeMap.get(key);
  let nextNode = activeNextNodeMap.get(key);
  if (prevNode === void 0 || nextNode === void 0) {
    invariant(
      false,
      "reconcileNode: prevNode or nextNode does not exist in nodeMap"
    );
  }
  const isDirty = treatAllNodesAsDirty || activeDirtyLeaves.has(key) || activeDirtyElements.has(key);
  const dom = getElementByKeyOrThrow(activeEditor, key);
  if (prevNode === nextNode && !isDirty) {
    if ($isElementNode(prevNode)) {
      const previousSubTreeTextContent = dom.__lexicalTextContent;
      if (previousSubTreeTextContent !== void 0) {
        subTreeTextContent += previousSubTreeTextContent;
        editorTextContent += previousSubTreeTextContent;
      }
    } else {
      const text = prevNode.getTextContent();
      editorTextContent += text;
      subTreeTextContent += text;
    }
    return dom;
  }
  if (prevNode !== nextNode && isDirty) {
    setMutatedNode(
      mutatedNodes,
      activeEditorNodes,
      activeMutationListeners,
      nextNode,
      "updated"
    );
  }
  if (nextNode.updateDOM(prevNode, dom, activeEditorConfig)) {
    const replacementDOM = $createNode(key, null, null);
    if (parentDOM === null) {
      invariant(false, "reconcileNode: parentDOM is null");
    }
    parentDOM.replaceChild(replacementDOM, dom);
    destroyNode(key, null);
    return replacementDOM;
  }
  if ($isElementNode(prevNode) && $isElementNode(nextNode)) {
    if (isDirty) {
      $reconcileChildrenWithDirection(prevNode, nextNode, dom);
      if (!$isRootNode(nextNode) && !nextNode.isInline()) {
        reconcileElementTerminatingLineBreak(prevNode, nextNode, dom);
      }
    }
    if ($textContentRequiresDoubleLinebreakAtEnd(nextNode)) {
      subTreeTextContent += DOUBLE_LINE_BREAK;
      editorTextContent += DOUBLE_LINE_BREAK;
    }
  } else {
    const text = nextNode.getTextContent();
    if ($isDecoratorNode(nextNode)) {
      const decorator = nextNode.decorate(activeEditor, activeEditorConfig);
      if (decorator !== null) {
        reconcileDecorator(key, decorator);
      }
    }
    subTreeTextContent += text;
    editorTextContent += text;
  }
  if (!activeEditorStateReadOnly && $isRootNode(nextNode) && nextNode.__cachedText !== editorTextContent) {
    const nextRootNode = nextNode.getWritable();
    nextRootNode.__cachedText = editorTextContent;
    nextNode = nextRootNode;
  }
  if (__DEV__) {
    Object.freeze(nextNode);
  }
  return dom;
}
function reconcileDecorator(key, decorator) {
  let pendingDecorators = activeEditor._pendingDecorators;
  const currentDecorators = activeEditor._decorators;
  if (pendingDecorators === null) {
    if (currentDecorators[key] === decorator) {
      return;
    }
    pendingDecorators = cloneDecorators(activeEditor);
  }
  pendingDecorators[key] = decorator;
}
function getFirstChild(element) {
  return element.firstChild;
}
function getNextSibling(element) {
  let nextSibling = element.nextSibling;
  if (nextSibling !== null && nextSibling === activeEditor._blockCursorElement) {
    nextSibling = nextSibling.nextSibling;
  }
  return nextSibling;
}
function $reconcileNodeChildren(nextElement, prevChildren, nextChildren, prevChildrenLength, nextChildrenLength, dom) {
  const prevEndIndex = prevChildrenLength - 1;
  const nextEndIndex = nextChildrenLength - 1;
  let prevChildrenSet;
  let nextChildrenSet;
  let siblingDOM = getFirstChild(dom);
  let prevIndex = 0;
  let nextIndex = 0;
  while (prevIndex <= prevEndIndex && nextIndex <= nextEndIndex) {
    const prevKey = prevChildren[prevIndex];
    const nextKey = nextChildren[nextIndex];
    if (prevKey === nextKey) {
      siblingDOM = getNextSibling($reconcileNode(nextKey, dom));
      prevIndex++;
      nextIndex++;
    } else {
      if (prevChildrenSet === void 0) {
        prevChildrenSet = new Set(prevChildren);
      }
      if (nextChildrenSet === void 0) {
        nextChildrenSet = new Set(nextChildren);
      }
      const nextHasPrevKey = nextChildrenSet.has(prevKey);
      const prevHasNextKey = prevChildrenSet.has(nextKey);
      if (!nextHasPrevKey) {
        siblingDOM = getNextSibling(getPrevElementByKeyOrThrow(prevKey));
        destroyNode(prevKey, dom);
        prevIndex++;
      } else if (!prevHasNextKey) {
        $createNode(nextKey, dom, siblingDOM);
        nextIndex++;
      } else {
        const childDOM = getElementByKeyOrThrow(activeEditor, nextKey);
        if (childDOM === siblingDOM) {
          siblingDOM = getNextSibling($reconcileNode(nextKey, dom));
        } else {
          if (siblingDOM != null) {
            dom.insertBefore(childDOM, siblingDOM);
          } else {
            dom.appendChild(childDOM);
          }
          $reconcileNode(nextKey, dom);
        }
        prevIndex++;
        nextIndex++;
      }
    }
    const node = activeNextNodeMap.get(nextKey);
    if (node !== null && $isTextNode(node)) {
      if (subTreeTextFormat === null) {
        subTreeTextFormat = node.getFormat();
      }
      if (subTreeTextStyle === "") {
        subTreeTextStyle = node.getStyle();
      }
    }
  }
  const appendNewChildren = prevIndex > prevEndIndex;
  const removeOldChildren = nextIndex > nextEndIndex;
  if (appendNewChildren && !removeOldChildren) {
    const previousNode = nextChildren[nextEndIndex + 1];
    const insertDOM = previousNode === void 0 ? null : activeEditor.getElementByKey(previousNode);
    $createChildren(
      nextChildren,
      nextElement,
      nextIndex,
      nextEndIndex,
      dom,
      insertDOM
    );
  } else if (removeOldChildren && !appendNewChildren) {
    destroyChildren(prevChildren, prevIndex, prevEndIndex, dom);
  }
}
function $reconcileRoot(prevEditorState, nextEditorState, editor, dirtyType, dirtyElements, dirtyLeaves) {
  subTreeTextContent = "";
  editorTextContent = "";
  treatAllNodesAsDirty = dirtyType === FULL_RECONCILE;
  activeEditor = editor;
  activeEditorConfig = editor._config;
  activeEditorNodes = editor._nodes;
  activeMutationListeners = activeEditor._listeners.mutation;
  activeDirtyElements = dirtyElements;
  activeDirtyLeaves = dirtyLeaves;
  activePrevNodeMap = prevEditorState._nodeMap;
  activeNextNodeMap = nextEditorState._nodeMap;
  activeEditorStateReadOnly = nextEditorState._readOnly;
  activePrevKeyToDOMMap = new Map(editor._keyToDOMMap);
  const currentMutatedNodes = /* @__PURE__ */ new Map();
  mutatedNodes = currentMutatedNodes;
  $reconcileNode("root", null);
  activeEditor = void 0;
  activeEditorNodes = void 0;
  activeDirtyElements = void 0;
  activeDirtyLeaves = void 0;
  activePrevNodeMap = void 0;
  activeNextNodeMap = void 0;
  activeEditorConfig = void 0;
  activePrevKeyToDOMMap = void 0;
  mutatedNodes = void 0;
  return currentMutatedNodes;
}
function storeDOMWithKey(key, dom, editor) {
  const keyToDOMMap = editor._keyToDOMMap;
  dom["__lexicalKey_" + editor._key] = key;
  keyToDOMMap.set(key, dom);
}
function getPrevElementByKeyOrThrow(key) {
  const element = activePrevKeyToDOMMap.get(key);
  if (element === void 0) {
    invariant(
      false,
      "Reconciliation: could not find DOM element for node key %s",
      key
    );
  }
  return element;
}

// resources/js/wysiwyg/lexical/core/LexicalEvents.ts
var PASS_THROUGH_COMMAND = Object.freeze({});
var ANDROID_COMPOSITION_LATENCY = 30;
var rootElementEvents = [
  ["keydown", onKeyDown],
  ["pointerdown", onPointerDown],
  ["compositionstart", onCompositionStart],
  ["compositionend", onCompositionEnd],
  ["input", onInput],
  ["click", onClick],
  ["cut", PASS_THROUGH_COMMAND],
  ["copy", PASS_THROUGH_COMMAND],
  ["dragstart", PASS_THROUGH_COMMAND],
  ["dragover", PASS_THROUGH_COMMAND],
  ["dragend", PASS_THROUGH_COMMAND],
  ["paste", PASS_THROUGH_COMMAND],
  ["focus", PASS_THROUGH_COMMAND],
  ["blur", PASS_THROUGH_COMMAND],
  ["drop", PASS_THROUGH_COMMAND]
];
if (CAN_USE_BEFORE_INPUT) {
  rootElementEvents.push([
    "beforeinput",
    (event, editor) => onBeforeInput(event, editor)
  ]);
}
var lastKeyDownTimeStamp = 0;
var lastKeyCode = null;
var lastBeforeInputInsertTextTimeStamp = 0;
var unprocessedBeforeInputData = null;
var rootElementsRegistered = /* @__PURE__ */ new WeakMap();
var isSelectionChangeFromDOMUpdate = false;
var isSelectionChangeFromMouseDown = false;
var isInsertLineBreak = false;
var isFirefoxEndingComposition = false;
var collapsedSelectionFormat = [
  0,
  "",
  0,
  "root",
  0
];
function $shouldPreventDefaultAndInsertText(selection, domTargetRange, text, timeStamp, isBeforeInput) {
  const anchor = selection.anchor;
  const focus = selection.focus;
  const anchorNode = anchor.getNode();
  const editor = getActiveEditor();
  const domSelection = getDOMSelection(editor._window);
  const domAnchorNode = domSelection !== null ? domSelection.anchorNode : null;
  const anchorKey = anchor.key;
  const backingAnchorElement = editor.getElementByKey(anchorKey);
  const textLength = text.length;
  return anchorKey !== focus.key || // If we're working with a non-text node.
  !$isTextNode(anchorNode) || // If we are replacing a range with a single character or grapheme, and not composing.
  (!isBeforeInput && (!CAN_USE_BEFORE_INPUT || // We check to see if there has been
  // a recent beforeinput event for "textInput". If there has been one in the last
  // 50ms then we proceed as normal. However, if there is not, then this is likely
  // a dangling `input` event caused by execCommand('insertText').
  lastBeforeInputInsertTextTimeStamp < timeStamp + 50) || anchorNode.isDirty() && textLength < 2 || doesContainGrapheme(text)) && anchor.offset !== focus.offset && !anchorNode.isComposing() || // Any non standard text node.
  $isTokenOrSegmented(anchorNode) || // If the text length is more than a single character and we're either
  // dealing with this in "beforeinput" or where the node has already recently
  // been changed (thus is dirty).
  anchorNode.isDirty() && textLength > 1 || // If the DOM selection element is not the same as the backing node during beforeinput.
  (isBeforeInput || !CAN_USE_BEFORE_INPUT) && backingAnchorElement !== null && !anchorNode.isComposing() && domAnchorNode !== getDOMTextNode(backingAnchorElement) || // If TargetRange is not the same as the DOM selection; browser trying to edit random parts
  // of the editor.
  domSelection !== null && domTargetRange !== null && (!domTargetRange.collapsed || domTargetRange.startContainer !== domSelection.anchorNode || domTargetRange.startOffset !== domSelection.anchorOffset) || // Check if we're changing from bold to italics, or some other format.
  anchorNode.getFormat() !== selection.format || anchorNode.getStyle() !== selection.style || // One last set of heuristics to check against.
  $shouldInsertTextAfterOrBeforeTextNode(selection, anchorNode);
}
function shouldSkipSelectionChange(domNode, offset) {
  return domNode !== null && domNode.nodeValue !== null && domNode.nodeType === DOM_TEXT_TYPE && offset !== 0 && offset !== domNode.nodeValue.length;
}
function onSelectionChange(domSelection, editor, isActive) {
  const {
    anchorNode: anchorDOM,
    anchorOffset,
    focusNode: focusDOM,
    focusOffset
  } = domSelection;
  if (isSelectionChangeFromDOMUpdate) {
    isSelectionChangeFromDOMUpdate = false;
    if (shouldSkipSelectionChange(anchorDOM, anchorOffset) && shouldSkipSelectionChange(focusDOM, focusOffset)) {
      return;
    }
  }
  updateEditor(editor, () => {
    if (!isActive) {
      $setSelection(null);
      return;
    }
    if (!isSelectionWithinEditor(editor, anchorDOM, focusDOM)) {
      return;
    }
    const selection = $getSelection();
    if ($isRangeSelection(selection)) {
      const anchor = selection.anchor;
      const anchorNode = anchor.getNode();
      if (selection.isCollapsed()) {
        if (domSelection.type === "Range" && domSelection.anchorNode === domSelection.focusNode) {
          selection.dirty = true;
        }
        const windowEvent = getWindow(editor).event;
        const currentTimeStamp = windowEvent ? windowEvent.timeStamp : performance.now();
        const [lastFormat, lastStyle, lastOffset, lastKey, timeStamp] = collapsedSelectionFormat;
        const root = $getRoot();
        const isRootTextContentEmpty = editor.isComposing() === false && root.getTextContent() === "";
        if (currentTimeStamp < timeStamp + 200 && anchor.offset === lastOffset && anchor.key === lastKey) {
          selection.format = lastFormat;
          selection.style = lastStyle;
        } else {
          if (anchor.type === "text") {
            invariant(
              $isTextNode(anchorNode),
              "Point.getNode() must return TextNode when type is text"
            );
            selection.format = anchorNode.getFormat();
            selection.style = anchorNode.getStyle();
          } else if (anchor.type === "element" && !isRootTextContentEmpty) {
            const lastNode = anchor.getNode();
            selection.style = "";
            if (lastNode instanceof ParagraphNode && lastNode.getChildrenSize() === 0) {
              selection.format = lastNode.getTextFormat();
              selection.style = lastNode.getTextStyle();
            } else {
              selection.format = 0;
            }
          }
        }
      } else {
        const anchorKey = anchor.key;
        const focus = selection.focus;
        const focusKey = focus.key;
        const nodes = selection.getNodes();
        const nodesLength = nodes.length;
        const isBackward = selection.isBackward();
        const startOffset = isBackward ? focusOffset : anchorOffset;
        const endOffset = isBackward ? anchorOffset : focusOffset;
        const startKey = isBackward ? focusKey : anchorKey;
        const endKey = isBackward ? anchorKey : focusKey;
        let combinedFormat = IS_ALL_FORMATTING;
        let hasTextNodes = false;
        for (let i = 0; i < nodesLength; i++) {
          const node = nodes[i];
          const textContentSize = node.getTextContentSize();
          if ($isTextNode(node) && textContentSize !== 0 && // Exclude empty text nodes at boundaries resulting from user's selection
          !(i === 0 && node.__key === startKey && startOffset === textContentSize || i === nodesLength - 1 && node.__key === endKey && endOffset === 0)) {
            hasTextNodes = true;
            combinedFormat &= node.getFormat();
            if (combinedFormat === 0) {
              break;
            }
          }
        }
        selection.format = hasTextNodes ? combinedFormat : 0;
      }
    }
    dispatchCommand(editor, SELECTION_CHANGE_COMMAND, void 0);
  });
}
function onClick(event, editor) {
  updateEditor(editor, () => {
    const selection = $getSelection();
    const domSelection = getDOMSelection(editor._window);
    const lastSelection = $getPreviousSelection();
    if (domSelection) {
      if ($isRangeSelection(selection)) {
        const anchor = selection.anchor;
        const anchorNode = anchor.getNode();
        if (anchor.type === "element" && anchor.offset === 0 && selection.isCollapsed() && !$isRootNode(anchorNode) && $getRoot().getChildrenSize() === 1 && anchorNode.getTopLevelElementOrThrow().isEmpty() && lastSelection !== null && selection.is(lastSelection)) {
          domSelection.removeAllRanges();
          selection.dirty = true;
        } else if (event.detail === 3 && !selection.isCollapsed()) {
          const focus = selection.focus;
          const focusNode = focus.getNode();
          if (anchorNode !== focusNode) {
            if ($isElementNode(anchorNode)) {
              anchorNode.select(0);
            } else {
              anchorNode.getParentOrThrow().select(0);
            }
          }
        }
      } else if (event.pointerType === "touch") {
        const domAnchorNode = domSelection.anchorNode;
        if (domAnchorNode !== null) {
          const nodeType = domAnchorNode.nodeType;
          if (nodeType === DOM_ELEMENT_TYPE || nodeType === DOM_TEXT_TYPE) {
            const newSelection = $internalCreateRangeSelection(
              lastSelection,
              domSelection,
              editor,
              event
            );
            $setSelection(newSelection);
          }
        }
      }
    }
    dispatchCommand(editor, CLICK_COMMAND, event);
  });
}
function onPointerDown(event, editor) {
  const target = event.target;
  const pointerType = event.pointerType;
  if (target instanceof Node && pointerType !== "touch") {
    updateEditor(editor, () => {
      if (!$isSelectionCapturedInDecorator(target)) {
        isSelectionChangeFromMouseDown = true;
      }
    });
  }
}
function getTargetRange(event) {
  if (!event.getTargetRanges) {
    return null;
  }
  const targetRanges = event.getTargetRanges();
  if (targetRanges.length === 0) {
    return null;
  }
  return targetRanges[0];
}
function $canRemoveText(anchorNode, focusNode) {
  return anchorNode !== focusNode || $isElementNode(anchorNode) || $isElementNode(focusNode) || !anchorNode.isToken() || !focusNode.isToken();
}
function isPossiblyAndroidKeyPress(timeStamp) {
  return lastKeyCode === "MediaLast" && timeStamp < lastKeyDownTimeStamp + ANDROID_COMPOSITION_LATENCY;
}
function onBeforeInput(event, editor) {
  const inputType = event.inputType;
  const targetRange = getTargetRange(event);
  if (inputType === "deleteCompositionText" || // If we're pasting in FF, we shouldn't get this event
  // as the `paste` event should have triggered, unless the
  // user has dom.event.clipboardevents.enabled disabled in
  // about:config. In that case, we need to process the
  // pasted content in the DOM mutation phase.
  IS_FIREFOX && isFirefoxClipboardEvents(editor)) {
    return;
  } else if (inputType === "insertCompositionText") {
    return;
  }
  updateEditor(editor, () => {
    const selection = $getSelection();
    if (inputType === "deleteContentBackward") {
      if (selection === null) {
        const prevSelection = $getPreviousSelection();
        if (!$isRangeSelection(prevSelection)) {
          return;
        }
        $setSelection(prevSelection.clone());
      }
      if ($isRangeSelection(selection)) {
        const isSelectionAnchorSameAsFocus = selection.anchor.key === selection.focus.key;
        if (isPossiblyAndroidKeyPress(event.timeStamp) && editor.isComposing() && isSelectionAnchorSameAsFocus) {
          $setCompositionKey(null);
          lastKeyDownTimeStamp = 0;
          setTimeout(() => {
            updateEditor(editor, () => {
              $setCompositionKey(null);
            });
          }, ANDROID_COMPOSITION_LATENCY);
          if ($isRangeSelection(selection)) {
            const anchorNode2 = selection.anchor.getNode();
            anchorNode2.markDirty();
            invariant(
              $isTextNode(anchorNode2),
              "Anchor node must be a TextNode"
            );
            selection.style = anchorNode2.getStyle();
          }
        } else {
          $setCompositionKey(null);
          event.preventDefault();
          const selectedNodeText = selection.anchor.getNode().getTextContent();
          const hasSelectedAllTextInNode = selection.anchor.offset === 0 && selection.focus.offset === selectedNodeText.length;
          const shouldLetBrowserHandleDelete = IS_ANDROID_CHROME && isSelectionAnchorSameAsFocus && !hasSelectedAllTextInNode;
          if (!shouldLetBrowserHandleDelete) {
            dispatchCommand(editor, DELETE_CHARACTER_COMMAND, true);
          }
        }
        return;
      }
    }
    if (!$isRangeSelection(selection)) {
      return;
    }
    const data = event.data;
    if (unprocessedBeforeInputData !== null) {
      $updateSelectedTextFromDOM(false, editor, unprocessedBeforeInputData);
    }
    if ((!selection.dirty || unprocessedBeforeInputData !== null) && selection.isCollapsed() && !$isRootNode(selection.anchor.getNode()) && targetRange !== null) {
      selection.applyDOMRange(targetRange);
    }
    unprocessedBeforeInputData = null;
    const anchor = selection.anchor;
    const focus = selection.focus;
    const anchorNode = anchor.getNode();
    const focusNode = focus.getNode();
    if (inputType === "insertText" || inputType === "insertTranspose") {
      if (data === "\n") {
        event.preventDefault();
        dispatchCommand(editor, INSERT_LINE_BREAK_COMMAND, false);
      } else if (data === DOUBLE_LINE_BREAK) {
        event.preventDefault();
        dispatchCommand(editor, INSERT_PARAGRAPH_COMMAND, void 0);
      } else if (data == null && event.dataTransfer) {
        const text = event.dataTransfer.getData("text/plain");
        event.preventDefault();
        selection.insertRawText(text);
      } else if (data != null && $shouldPreventDefaultAndInsertText(
        selection,
        targetRange,
        data,
        event.timeStamp,
        true
      )) {
        event.preventDefault();
        dispatchCommand(editor, CONTROLLED_TEXT_INSERTION_COMMAND, data);
      } else {
        unprocessedBeforeInputData = data;
      }
      lastBeforeInputInsertTextTimeStamp = event.timeStamp;
      return;
    }
    event.preventDefault();
    switch (inputType) {
      case "insertFromYank":
      case "insertFromDrop":
      case "insertReplacementText": {
        dispatchCommand(editor, CONTROLLED_TEXT_INSERTION_COMMAND, event);
        break;
      }
      case "insertFromComposition": {
        $setCompositionKey(null);
        dispatchCommand(editor, CONTROLLED_TEXT_INSERTION_COMMAND, event);
        break;
      }
      case "insertLineBreak": {
        $setCompositionKey(null);
        dispatchCommand(editor, INSERT_LINE_BREAK_COMMAND, false);
        break;
      }
      case "insertParagraph": {
        $setCompositionKey(null);
        if (isInsertLineBreak && !IS_IOS) {
          isInsertLineBreak = false;
          dispatchCommand(editor, INSERT_LINE_BREAK_COMMAND, false);
        } else {
          dispatchCommand(editor, INSERT_PARAGRAPH_COMMAND, void 0);
        }
        break;
      }
      case "insertFromPaste":
      case "insertFromPasteAsQuotation": {
        dispatchCommand(editor, PASTE_COMMAND, event);
        break;
      }
      case "deleteByComposition": {
        if ($canRemoveText(anchorNode, focusNode)) {
          dispatchCommand(editor, REMOVE_TEXT_COMMAND, event);
        }
        break;
      }
      case "deleteByDrag":
      case "deleteByCut": {
        dispatchCommand(editor, REMOVE_TEXT_COMMAND, event);
        break;
      }
      case "deleteContent": {
        dispatchCommand(editor, DELETE_CHARACTER_COMMAND, false);
        break;
      }
      case "deleteWordBackward": {
        dispatchCommand(editor, DELETE_WORD_COMMAND, true);
        break;
      }
      case "deleteWordForward": {
        dispatchCommand(editor, DELETE_WORD_COMMAND, false);
        break;
      }
      case "deleteHardLineBackward":
      case "deleteSoftLineBackward": {
        dispatchCommand(editor, DELETE_LINE_COMMAND, true);
        break;
      }
      case "deleteContentForward":
      case "deleteHardLineForward":
      case "deleteSoftLineForward": {
        dispatchCommand(editor, DELETE_LINE_COMMAND, false);
        break;
      }
      case "formatStrikeThrough": {
        dispatchCommand(editor, FORMAT_TEXT_COMMAND, "strikethrough");
        break;
      }
      case "formatBold": {
        dispatchCommand(editor, FORMAT_TEXT_COMMAND, "bold");
        break;
      }
      case "formatItalic": {
        dispatchCommand(editor, FORMAT_TEXT_COMMAND, "italic");
        break;
      }
      case "formatUnderline": {
        dispatchCommand(editor, FORMAT_TEXT_COMMAND, "underline");
        break;
      }
      case "historyUndo": {
        dispatchCommand(editor, UNDO_COMMAND, void 0);
        break;
      }
      case "historyRedo": {
        dispatchCommand(editor, REDO_COMMAND, void 0);
        break;
      }
      default:
    }
  });
}
function onInput(event, editor) {
  event.stopPropagation();
  updateEditor(editor, () => {
    const selection = $getSelection();
    const data = event.data;
    const targetRange = getTargetRange(event);
    if (data != null && $isRangeSelection(selection) && $shouldPreventDefaultAndInsertText(
      selection,
      targetRange,
      data,
      event.timeStamp,
      false
    )) {
      if (isFirefoxEndingComposition) {
        $onCompositionEndImpl(editor, data);
        isFirefoxEndingComposition = false;
      }
      const anchor = selection.anchor;
      const anchorNode = anchor.getNode();
      const domSelection = getDOMSelection(editor._window);
      if (domSelection === null) {
        return;
      }
      const isBackward = selection.isBackward();
      const startOffset = isBackward ? selection.anchor.offset : selection.focus.offset;
      const endOffset = isBackward ? selection.focus.offset : selection.anchor.offset;
      if (!CAN_USE_BEFORE_INPUT || selection.isCollapsed() || !$isTextNode(anchorNode) || domSelection.anchorNode === null || anchorNode.getTextContent().slice(0, startOffset) + data + anchorNode.getTextContent().slice(startOffset + endOffset) !== getAnchorTextFromDOM(domSelection.anchorNode)) {
        dispatchCommand(editor, CONTROLLED_TEXT_INSERTION_COMMAND, data);
      }
      const textLength = data.length;
      if (IS_FIREFOX && textLength > 1 && event.inputType === "insertCompositionText" && !editor.isComposing()) {
        selection.anchor.offset -= textLength;
      }
      if (!IS_SAFARI && !IS_IOS && !IS_APPLE_WEBKIT && editor.isComposing()) {
        lastKeyDownTimeStamp = 0;
        $setCompositionKey(null);
      }
    } else {
      const characterData = data !== null ? data : void 0;
      $updateSelectedTextFromDOM(false, editor, characterData);
      if (isFirefoxEndingComposition) {
        $onCompositionEndImpl(editor, data || void 0);
        isFirefoxEndingComposition = false;
      }
    }
    $flushMutations2();
  });
  unprocessedBeforeInputData = null;
}
function onCompositionStart(event, editor) {
  updateEditor(editor, () => {
    const selection = $getSelection();
    if ($isRangeSelection(selection) && !editor.isComposing()) {
      const anchor = selection.anchor;
      const node = selection.anchor.getNode();
      $setCompositionKey(anchor.key);
      if (
        // If it has been 30ms since the last keydown, then we should
        // apply the empty space heuristic. We can't do this for Safari,
        // as the keydown fires after composition start.
        event.timeStamp < lastKeyDownTimeStamp + ANDROID_COMPOSITION_LATENCY || // FF has issues around composing multibyte characters, so we also
        // need to invoke the empty space heuristic below.
        anchor.type === "element" || !selection.isCollapsed() || $isTextNode(node) && node.getStyle() !== selection.style
      ) {
        dispatchCommand(
          editor,
          CONTROLLED_TEXT_INSERTION_COMMAND,
          COMPOSITION_START_CHAR
        );
      }
    }
  });
}
function $onCompositionEndImpl(editor, data) {
  const compositionKey = editor._compositionKey;
  $setCompositionKey(null);
  if (compositionKey !== null && data != null) {
    if (data === "") {
      const node = $getNodeByKey(compositionKey);
      const textNode = getDOMTextNode(editor.getElementByKey(compositionKey));
      if (textNode !== null && textNode.nodeValue !== null && $isTextNode(node)) {
        $updateTextNodeFromDOMContent(
          node,
          textNode.nodeValue,
          null,
          null,
          true
        );
      }
      return;
    }
    if (data[data.length - 1] === "\n") {
      const selection = $getSelection();
      if ($isRangeSelection(selection)) {
        const focus = selection.focus;
        selection.anchor.set(focus.key, focus.offset, focus.type);
        dispatchCommand(editor, KEY_ENTER_COMMAND, null);
        return;
      }
    }
  }
  $updateSelectedTextFromDOM(true, editor, data);
}
function onCompositionEnd(event, editor) {
  if (IS_FIREFOX) {
    isFirefoxEndingComposition = true;
  } else {
    updateEditor(editor, () => {
      $onCompositionEndImpl(editor, event.data);
    });
  }
}
function onKeyDown(event, editor) {
  lastKeyDownTimeStamp = event.timeStamp;
  lastKeyCode = event.key;
  if (editor.isComposing()) {
    return;
  }
  const { key, shiftKey, ctrlKey, metaKey, altKey } = event;
  if (dispatchCommand(editor, KEY_DOWN_COMMAND, event)) {
    return;
  }
  if (key == null) {
    return;
  }
  if (isMoveForward(key, ctrlKey, altKey, metaKey)) {
    dispatchCommand(editor, KEY_ARROW_RIGHT_COMMAND, event);
  } else if (isMoveToEnd(key, ctrlKey, shiftKey, altKey, metaKey)) {
    dispatchCommand(editor, MOVE_TO_END, event);
  } else if (isMoveBackward(key, ctrlKey, altKey, metaKey)) {
    dispatchCommand(editor, KEY_ARROW_LEFT_COMMAND, event);
  } else if (isMoveToStart(key, ctrlKey, shiftKey, altKey, metaKey)) {
    dispatchCommand(editor, MOVE_TO_START, event);
  } else if (isMoveUp(key, ctrlKey, metaKey)) {
    dispatchCommand(editor, KEY_ARROW_UP_COMMAND, event);
  } else if (isMoveDown(key, ctrlKey, metaKey)) {
    dispatchCommand(editor, KEY_ARROW_DOWN_COMMAND, event);
  } else if (isLineBreak(key, shiftKey)) {
    isInsertLineBreak = true;
    dispatchCommand(editor, KEY_ENTER_COMMAND, event);
  } else if (isSpace(key)) {
    dispatchCommand(editor, KEY_SPACE_COMMAND, event);
  } else if (isOpenLineBreak(key, ctrlKey)) {
    event.preventDefault();
    isInsertLineBreak = true;
    dispatchCommand(editor, INSERT_LINE_BREAK_COMMAND, true);
  } else if (isParagraph(key, shiftKey)) {
    isInsertLineBreak = false;
    dispatchCommand(editor, KEY_ENTER_COMMAND, event);
  } else if (isDeleteBackward(key, altKey, metaKey, ctrlKey)) {
    if (isBackspace(key)) {
      dispatchCommand(editor, KEY_BACKSPACE_COMMAND, event);
    } else {
      event.preventDefault();
      dispatchCommand(editor, DELETE_CHARACTER_COMMAND, true);
    }
  } else if (isEscape(key)) {
    dispatchCommand(editor, KEY_ESCAPE_COMMAND, event);
  } else if (isDeleteForward(key, ctrlKey, shiftKey, altKey, metaKey)) {
    if (isDelete(key)) {
      dispatchCommand(editor, KEY_DELETE_COMMAND, event);
    } else {
      event.preventDefault();
      dispatchCommand(editor, DELETE_CHARACTER_COMMAND, false);
    }
  } else if (isDeleteWordBackward(key, altKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, DELETE_WORD_COMMAND, true);
  } else if (isDeleteWordForward(key, altKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, DELETE_WORD_COMMAND, false);
  } else if (isDeleteLineBackward(key, metaKey)) {
    event.preventDefault();
    dispatchCommand(editor, DELETE_LINE_COMMAND, true);
  } else if (isDeleteLineForward(key, metaKey)) {
    event.preventDefault();
    dispatchCommand(editor, DELETE_LINE_COMMAND, false);
  } else if (isBold(key, altKey, metaKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, FORMAT_TEXT_COMMAND, "bold");
  } else if (isUnderline(key, altKey, metaKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, FORMAT_TEXT_COMMAND, "underline");
  } else if (isItalic(key, altKey, metaKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, FORMAT_TEXT_COMMAND, "italic");
  } else if (isTab(key, altKey, ctrlKey, metaKey)) {
    dispatchCommand(editor, KEY_TAB_COMMAND, event);
  } else if (isUndo(key, shiftKey, metaKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, UNDO_COMMAND, void 0);
  } else if (isRedo(key, shiftKey, metaKey, ctrlKey)) {
    event.preventDefault();
    dispatchCommand(editor, REDO_COMMAND, void 0);
  } else {
    const prevSelection = editor._editorState._selection;
    if ($isNodeSelection(prevSelection)) {
      if (isCopy(key, shiftKey, metaKey, ctrlKey)) {
        event.preventDefault();
        dispatchCommand(editor, COPY_COMMAND, event);
      } else if (isCut(key, shiftKey, metaKey, ctrlKey)) {
        event.preventDefault();
        dispatchCommand(editor, CUT_COMMAND, event);
      } else if (isSelectAll(key, metaKey, ctrlKey)) {
        event.preventDefault();
        dispatchCommand(editor, SELECT_ALL_COMMAND, event);
      }
    } else if (!IS_FIREFOX && isSelectAll(key, metaKey, ctrlKey)) {
      event.preventDefault();
      dispatchCommand(editor, SELECT_ALL_COMMAND, event);
    }
  }
  if (isModifier(ctrlKey, shiftKey, altKey, metaKey)) {
    dispatchCommand(editor, KEY_MODIFIER_COMMAND, event);
  }
}
function getRootElementRemoveHandles(rootElement) {
  let eventHandles = rootElement.__lexicalEventHandles;
  if (eventHandles === void 0) {
    eventHandles = [];
    rootElement.__lexicalEventHandles = eventHandles;
  }
  return eventHandles;
}
var activeNestedEditorsMap = /* @__PURE__ */ new Map();
function onDocumentSelectionChange(event) {
  const target = event.target;
  const targetWindow = target == null ? null : target.nodeType === 9 ? target.defaultView : target.ownerDocument.defaultView;
  const domSelection = getDOMSelection(targetWindow);
  if (domSelection === null) {
    return;
  }
  const nextActiveEditor = getNearestEditorFromDOMNode(domSelection.anchorNode);
  if (nextActiveEditor === null) {
    return;
  }
  if (isSelectionChangeFromMouseDown) {
    isSelectionChangeFromMouseDown = false;
    updateEditor(nextActiveEditor, () => {
      const lastSelection = $getPreviousSelection();
      const domAnchorNode = domSelection.anchorNode;
      if (domAnchorNode === null) {
        return;
      }
      const nodeType = domAnchorNode.nodeType;
      if (nodeType !== DOM_ELEMENT_TYPE && nodeType !== DOM_TEXT_TYPE) {
        return;
      }
      const newSelection = $internalCreateRangeSelection(
        lastSelection,
        domSelection,
        nextActiveEditor,
        event
      );
      $setSelection(newSelection);
    });
  }
  const editors = getEditorsToPropagate(nextActiveEditor);
  const rootEditor = editors[editors.length - 1];
  const rootEditorKey = rootEditor._key;
  const activeNestedEditor = activeNestedEditorsMap.get(rootEditorKey);
  const prevActiveEditor = activeNestedEditor || rootEditor;
  if (prevActiveEditor !== nextActiveEditor) {
    onSelectionChange(domSelection, prevActiveEditor, false);
  }
  onSelectionChange(domSelection, nextActiveEditor, true);
  if (nextActiveEditor !== rootEditor) {
    activeNestedEditorsMap.set(rootEditorKey, nextActiveEditor);
  } else if (activeNestedEditor) {
    activeNestedEditorsMap.delete(rootEditorKey);
  }
}
function stopLexicalPropagation(event) {
  event._lexicalHandled = true;
}
function hasStoppedLexicalPropagation(event) {
  const stopped = event._lexicalHandled === true;
  return stopped;
}
function addRootElementEvents(rootElement, editor) {
  const doc = rootElement.ownerDocument;
  const documentRootElementsCount = rootElementsRegistered.get(doc);
  if (documentRootElementsCount === void 0 || documentRootElementsCount < 1) {
    doc.addEventListener("selectionchange", onDocumentSelectionChange);
  }
  rootElementsRegistered.set(doc, (documentRootElementsCount || 0) + 1);
  rootElement.__lexicalEditor = editor;
  const removeHandles = getRootElementRemoveHandles(rootElement);
  for (let i = 0; i < rootElementEvents.length; i++) {
    const [eventName, onEvent] = rootElementEvents[i];
    const eventHandler = typeof onEvent === "function" ? (event) => {
      if (hasStoppedLexicalPropagation(event)) {
        return;
      }
      stopLexicalPropagation(event);
      if (editor.isEditable() || eventName === "click") {
        onEvent(event, editor);
      }
    } : (event) => {
      if (hasStoppedLexicalPropagation(event)) {
        return;
      }
      stopLexicalPropagation(event);
      const isEditable = editor.isEditable();
      switch (eventName) {
        case "cut":
          return isEditable && dispatchCommand(editor, CUT_COMMAND, event);
        case "copy":
          return dispatchCommand(
            editor,
            COPY_COMMAND,
            event
          );
        case "paste":
          return isEditable && dispatchCommand(
            editor,
            PASTE_COMMAND,
            event
          );
        case "dragstart":
          return isEditable && dispatchCommand(editor, DRAGSTART_COMMAND, event);
        case "dragover":
          return isEditable && dispatchCommand(editor, DRAGOVER_COMMAND, event);
        case "dragend":
          return isEditable && dispatchCommand(editor, DRAGEND_COMMAND, event);
        case "focus":
          return isEditable && dispatchCommand(editor, FOCUS_COMMAND, event);
        case "blur": {
          return isEditable && dispatchCommand(editor, BLUR_COMMAND, event);
        }
        case "drop":
          return isEditable && dispatchCommand(editor, DROP_COMMAND, event);
      }
    };
    rootElement.addEventListener(eventName, eventHandler);
    removeHandles.push(() => {
      rootElement.removeEventListener(eventName, eventHandler);
    });
  }
}
function removeRootElementEvents(rootElement) {
  const doc = rootElement.ownerDocument;
  const documentRootElementsCount = rootElementsRegistered.get(doc);
  invariant(
    documentRootElementsCount !== void 0,
    "Root element not registered"
  );
  const newCount = documentRootElementsCount - 1;
  invariant(newCount >= 0, "Root element count less than 0");
  rootElementsRegistered.set(doc, newCount);
  if (newCount === 0) {
    doc.removeEventListener("selectionchange", onDocumentSelectionChange);
  }
  const editor = getEditorPropertyFromDOMNode(rootElement);
  if (isLexicalEditor(editor)) {
    cleanActiveNestedEditorsMap(editor);
    rootElement.__lexicalEditor = null;
  } else if (editor) {
    invariant(
      false,
      "Attempted to remove event handlers from a node that does not belong to this build of Lexical"
    );
  }
  const removeHandles = getRootElementRemoveHandles(rootElement);
  for (let i = 0; i < removeHandles.length; i++) {
    removeHandles[i]();
  }
  rootElement.__lexicalEventHandles = [];
}
function cleanActiveNestedEditorsMap(editor) {
  if (editor._parentEditor !== null) {
    const editors = getEditorsToPropagate(editor);
    const rootEditor = editors[editors.length - 1];
    const rootEditorKey = rootEditor._key;
    if (activeNestedEditorsMap.get(rootEditorKey) === editor) {
      activeNestedEditorsMap.delete(rootEditorKey);
    }
  } else {
    activeNestedEditorsMap.delete(editor._key);
  }
}
function markSelectionChangeFromDOMUpdate() {
  isSelectionChangeFromDOMUpdate = true;
}
function markCollapsedSelectionFormat(format, style, offset, key, timeStamp) {
  collapsedSelectionFormat = [format, style, offset, key, timeStamp];
}

// resources/js/wysiwyg/lexical/core/LexicalNode.ts
function $removeNode(nodeToRemove, restoreSelection, preserveEmptyParent) {
  errorOnReadOnly();
  const key = nodeToRemove.__key;
  const parent = nodeToRemove.getParent();
  if (parent === null) {
    return;
  }
  const selection = $maybeMoveChildrenSelectionToParent(nodeToRemove);
  let selectionMoved = false;
  if ($isRangeSelection(selection) && restoreSelection) {
    const anchor = selection.anchor;
    const focus = selection.focus;
    if (anchor.key === key) {
      moveSelectionPointToSibling(
        anchor,
        nodeToRemove,
        parent,
        nodeToRemove.getPreviousSibling(),
        nodeToRemove.getNextSibling()
      );
      selectionMoved = true;
    }
    if (focus.key === key) {
      moveSelectionPointToSibling(
        focus,
        nodeToRemove,
        parent,
        nodeToRemove.getPreviousSibling(),
        nodeToRemove.getNextSibling()
      );
      selectionMoved = true;
    }
  } else if ($isNodeSelection(selection) && restoreSelection && nodeToRemove.isSelected()) {
    nodeToRemove.selectPrevious();
  }
  if ($isRangeSelection(selection) && restoreSelection && !selectionMoved) {
    const index = nodeToRemove.getIndexWithinParent();
    removeFromParent(nodeToRemove);
    $updateElementSelectionOnCreateDeleteNode(selection, parent, index, -1);
  } else {
    removeFromParent(nodeToRemove);
  }
  if (!preserveEmptyParent && !$isRootOrShadowRoot(parent) && !parent.canBeEmpty() && parent.isEmpty()) {
    $removeNode(parent, restoreSelection);
  }
  if (restoreSelection && $isRootNode(parent) && parent.isEmpty()) {
    parent.selectEnd();
  }
}
var LexicalNode = class {
  constructor(key) {
    /** @internal */
    __publicField(this, "__type");
    /** @internal */
    //@ts-ignore We set the key in the constructor.
    __publicField(this, "__key");
    /** @internal */
    __publicField(this, "__parent");
    /** @internal */
    __publicField(this, "__prev");
    /** @internal */
    __publicField(this, "__next");
    this.__type = this.constructor.getType();
    this.__parent = null;
    this.__prev = null;
    this.__next = null;
    $setNodeKey(this, key);
    if (__DEV__) {
      if (this.__type !== "root") {
        errorOnReadOnly();
        errorOnTypeKlassMismatch(this.__type, this.constructor);
      }
    }
  }
  // Flow doesn't support abstract classes unfortunately, so we can't _force_
  // subclasses of Node to implement statics. All subclasses of Node should have
  // a static getType and clone method though. We define getType and clone here so we can call it
  // on any  Node, and we throw this error by default since the subclass should provide
  // their own implementation.
  /**
   * Returns the string type of this node. Every node must
   * implement this and it MUST BE UNIQUE amongst nodes registered
   * on the editor.
   *
   */
  static getType() {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .getType().",
      this.name
    );
  }
  /**
   * Clones this node, creating a new node with a different key
   * and adding it to the EditorState (but not attaching it anywhere!). All nodes must
   * implement this method.
   *
   */
  static clone(_data) {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .clone().",
      this.name
    );
  }
  /**
   * Perform any state updates on the clone of prevNode that are not already
   * handled by the constructor call in the static clone method. If you have
   * state to update in your clone that is not handled directly by the
   * constructor, it is advisable to override this method but it is required
   * to include a call to `super.afterCloneFrom(prevNode)` in your
   * implementation. This is only intended to be called by
   * {@link $cloneWithProperties} function or via a super call.
   *
   * @example
   * ```ts
   * class ClassesTextNode extends TextNode {
   *   // Not shown: static getType, static importJSON, exportJSON, createDOM, updateDOM
   *   __classes = new Set<string>();
   *   static clone(node: ClassesTextNode): ClassesTextNode {
   *     // The inherited TextNode constructor is used here, so
   *     // classes is not set by this method.
   *     return new ClassesTextNode(node.__text, node.__key);
   *   }
   *   afterCloneFrom(node: this): void {
   *     // This calls TextNode.afterCloneFrom and LexicalNode.afterCloneFrom
   *     // for necessary state updates
   *     super.afterCloneFrom(node);
   *     this.__addClasses(node.__classes);
   *   }
   *   // This method is a private implementation detail, it is not
   *   // suitable for the public API because it does not call getWritable
   *   __addClasses(classNames: Iterable<string>): this {
   *     for (const className of classNames) {
   *       this.__classes.add(className);
   *     }
   *     return this;
   *   }
   *   addClass(...classNames: string[]): this {
   *     return this.getWritable().__addClasses(classNames);
   *   }
   *   removeClass(...classNames: string[]): this {
   *     const node = this.getWritable();
   *     for (const className of classNames) {
   *       this.__classes.delete(className);
   *     }
   *     return this;
   *   }
   *   getClasses(): Set<string> {
   *     return this.getLatest().__classes;
   *   }
   * }
   * ```
   *
   */
  afterCloneFrom(prevNode) {
    this.__parent = prevNode.__parent;
    this.__next = prevNode.__next;
    this.__prev = prevNode.__prev;
  }
  // Getters and Traversers
  /**
   * Returns the string type of this node.
   */
  getType() {
    return this.__type;
  }
  isInline() {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .isInline().",
      this.constructor.name
    );
  }
  /**
   * Returns true if there is a path between this node and the RootNode, false otherwise.
   * This is a way of determining if the node is "attached" EditorState. Unattached nodes
   * won't be reconciled and will ultimatelt be cleaned up by the Lexical GC.
   */
  isAttached() {
    let nodeKey = this.__key;
    while (nodeKey !== null) {
      if (nodeKey === "root") {
        return true;
      }
      const node = $getNodeByKey(nodeKey);
      if (node === null) {
        break;
      }
      nodeKey = node.__parent;
    }
    return false;
  }
  /**
   * Returns true if this node is contained within the provided Selection., false otherwise.
   * Relies on the algorithms implemented in {@link BaseSelection.getNodes} to determine
   * what's included.
   *
   * @param selection - The selection that we want to determine if the node is in.
   */
  isSelected(selection) {
    const targetSelection = selection || $getSelection();
    if (targetSelection == null) {
      return false;
    }
    const isSelected = targetSelection.getNodes().some((n) => n.__key === this.__key);
    if ($isTextNode(this)) {
      return isSelected;
    }
    const isElementRangeSelection = $isRangeSelection(targetSelection) && targetSelection.anchor.type === "element" && targetSelection.focus.type === "element";
    if (isElementRangeSelection) {
      if (targetSelection.isCollapsed()) {
        return false;
      }
      const parentNode = this.getParent();
      if ($isDecoratorNode(this) && this.isInline() && parentNode) {
        const firstPoint = targetSelection.isBackward() ? targetSelection.focus : targetSelection.anchor;
        const firstElement = firstPoint.getNode();
        if (firstPoint.offset === firstElement.getChildrenSize() && firstElement.is(parentNode) && firstElement.getLastChildOrThrow().is(this)) {
          return false;
        }
      }
    }
    return isSelected;
  }
  /**
   * Returns this nodes key.
   */
  getKey() {
    return this.__key;
  }
  /**
   * Returns the zero-based index of this node within the parent.
   */
  getIndexWithinParent() {
    const parent = this.getParent();
    if (parent === null) {
      return -1;
    }
    let node = parent.getFirstChild();
    let index = 0;
    while (node !== null) {
      if (this.is(node)) {
        return index;
      }
      index++;
      node = node.getNextSibling();
    }
    return -1;
  }
  /**
   * Returns the parent of this node, or null if none is found.
   */
  getParent() {
    const parent = this.getLatest().__parent;
    if (parent === null) {
      return null;
    }
    return $getNodeByKey(parent);
  }
  /**
   * Returns the parent of this node, or throws if none is found.
   */
  getParentOrThrow() {
    const parent = this.getParent();
    if (parent === null) {
      invariant(false, "Expected node %s to have a parent.", this.__key);
    }
    return parent;
  }
  /**
   * Returns the highest (in the EditorState tree)
   * non-root ancestor of this node, or null if none is found. See {@link lexical!$isRootOrShadowRoot}
   * for more information on which Elements comprise "roots".
   */
  getTopLevelElement() {
    let node = this;
    while (node !== null) {
      const parent = node.getParent();
      if ($isRootOrShadowRoot(parent)) {
        invariant(
          $isElementNode(node) || node === this && $isDecoratorNode(node),
          "Children of root nodes must be elements or decorators"
        );
        return node;
      }
      node = parent;
    }
    return null;
  }
  /**
   * Returns the highest (in the EditorState tree)
   * non-root ancestor of this node, or throws if none is found. See {@link lexical!$isRootOrShadowRoot}
   * for more information on which Elements comprise "roots".
   */
  getTopLevelElementOrThrow() {
    const parent = this.getTopLevelElement();
    if (parent === null) {
      invariant(
        false,
        "Expected node %s to have a top parent element.",
        this.__key
      );
    }
    return parent;
  }
  /**
   * Returns a list of the every ancestor of this node,
   * all the way up to the RootNode.
   *
   */
  getParents() {
    const parents = [];
    let node = this.getParent();
    while (node !== null) {
      parents.push(node);
      node = node.getParent();
    }
    return parents;
  }
  /**
   * Returns a list of the keys of every ancestor of this node,
   * all the way up to the RootNode.
   *
   */
  getParentKeys() {
    const parents = [];
    let node = this.getParent();
    while (node !== null) {
      parents.push(node.__key);
      node = node.getParent();
    }
    return parents;
  }
  /**
   * Returns the "previous" siblings - that is, the node that comes
   * before this one in the same parent.
   *
   */
  getPreviousSibling() {
    const self = this.getLatest();
    const prevKey = self.__prev;
    return prevKey === null ? null : $getNodeByKey(prevKey);
  }
  /**
   * Returns the "previous" siblings - that is, the nodes that come between
   * this one and the first child of it's parent, inclusive.
   *
   */
  getPreviousSiblings() {
    const siblings = [];
    const parent = this.getParent();
    if (parent === null) {
      return siblings;
    }
    let node = parent.getFirstChild();
    while (node !== null) {
      if (node.is(this)) {
        break;
      }
      siblings.push(node);
      node = node.getNextSibling();
    }
    return siblings;
  }
  /**
   * Returns the "next" siblings - that is, the node that comes
   * after this one in the same parent
   *
   */
  getNextSibling() {
    const self = this.getLatest();
    const nextKey = self.__next;
    return nextKey === null ? null : $getNodeByKey(nextKey);
  }
  /**
   * Returns all "next" siblings - that is, the nodes that come between this
   * one and the last child of it's parent, inclusive.
   *
   */
  getNextSiblings() {
    const siblings = [];
    let node = this.getNextSibling();
    while (node !== null) {
      siblings.push(node);
      node = node.getNextSibling();
    }
    return siblings;
  }
  /**
   * Returns the closest common ancestor of this node and the provided one or null
   * if one cannot be found.
   *
   * @param node - the other node to find the common ancestor of.
   */
  getCommonAncestor(node) {
    const a = this.getParents();
    const b = node.getParents();
    if ($isElementNode(this)) {
      a.unshift(this);
    }
    if ($isElementNode(node)) {
      b.unshift(node);
    }
    const aLength = a.length;
    const bLength = b.length;
    if (aLength === 0 || bLength === 0 || a[aLength - 1] !== b[bLength - 1]) {
      return null;
    }
    const bSet = new Set(b);
    for (let i = 0; i < aLength; i++) {
      const ancestor = a[i];
      if (bSet.has(ancestor)) {
        return ancestor;
      }
    }
    return null;
  }
  /**
   * Returns true if the provided node is the exact same one as this node, from Lexical's perspective.
   * Always use this instead of referential equality.
   *
   * @param object - the node to perform the equality comparison on.
   */
  is(object) {
    if (object == null) {
      return false;
    }
    return this.__key === object.__key;
  }
  /**
   * Returns true if this node logical precedes the target node in the editor state.
   *
   * @param targetNode - the node we're testing to see if it's after this one.
   */
  isBefore(targetNode) {
    if (this === targetNode) {
      return false;
    }
    if (targetNode.isParentOf(this)) {
      return true;
    }
    if (this.isParentOf(targetNode)) {
      return false;
    }
    const commonAncestor = this.getCommonAncestor(targetNode);
    let indexA = 0;
    let indexB = 0;
    let node = this;
    while (true) {
      const parent = node.getParentOrThrow();
      if (parent === commonAncestor) {
        indexA = node.getIndexWithinParent();
        break;
      }
      node = parent;
    }
    node = targetNode;
    while (true) {
      const parent = node.getParentOrThrow();
      if (parent === commonAncestor) {
        indexB = node.getIndexWithinParent();
        break;
      }
      node = parent;
    }
    return indexA < indexB;
  }
  /**
   * Returns true if this node is the parent of the target node, false otherwise.
   *
   * @param targetNode - the would-be child node.
   */
  isParentOf(targetNode) {
    const key = this.__key;
    if (key === targetNode.__key) {
      return false;
    }
    let node = targetNode;
    while (node !== null) {
      if (node.__key === key) {
        return true;
      }
      node = node.getParent();
    }
    return false;
  }
  // TO-DO: this function can be simplified a lot
  /**
   * Returns a list of nodes that are between this node and
   * the target node in the EditorState.
   *
   * @param targetNode - the node that marks the other end of the range of nodes to be returned.
   */
  getNodesBetween(targetNode) {
    const isBefore = this.isBefore(targetNode);
    const nodes = [];
    const visited = /* @__PURE__ */ new Set();
    let node = this;
    while (true) {
      if (node === null) {
        break;
      }
      const key = node.__key;
      if (!visited.has(key)) {
        visited.add(key);
        nodes.push(node);
      }
      if (node === targetNode) {
        break;
      }
      const child = $isElementNode(node) ? isBefore ? node.getFirstChild() : node.getLastChild() : null;
      if (child !== null) {
        node = child;
        continue;
      }
      const nextSibling = isBefore ? node.getNextSibling() : node.getPreviousSibling();
      if (nextSibling !== null) {
        node = nextSibling;
        continue;
      }
      const parent = node.getParentOrThrow();
      if (!visited.has(parent.__key)) {
        nodes.push(parent);
      }
      if (parent === targetNode) {
        break;
      }
      let parentSibling = null;
      let ancestor = parent;
      do {
        if (ancestor === null) {
          invariant(false, "getNodesBetween: ancestor is null");
        }
        parentSibling = isBefore ? ancestor.getNextSibling() : ancestor.getPreviousSibling();
        ancestor = ancestor.getParent();
        if (ancestor !== null) {
          if (parentSibling === null && !visited.has(ancestor.__key)) {
            nodes.push(ancestor);
          }
        } else {
          break;
        }
      } while (parentSibling === null);
      node = parentSibling;
    }
    if (!isBefore) {
      nodes.reverse();
    }
    return nodes;
  }
  /**
   * Returns true if this node has been marked dirty during this update cycle.
   *
   */
  isDirty() {
    const editor = getActiveEditor();
    const dirtyLeaves = editor._dirtyLeaves;
    return dirtyLeaves !== null && dirtyLeaves.has(this.__key);
  }
  /**
   * Returns the latest version of the node from the active EditorState.
   * This is used to avoid getting values from stale node references.
   *
   */
  getLatest() {
    const latest = $getNodeByKey(this.__key);
    if (latest === null) {
      invariant(
        false,
        "Lexical node does not exist in active editor state. Avoid using the same node references between nested closures from editorState.read/editor.update."
      );
    }
    return latest;
  }
  /**
   * Returns a mutable version of the node using {@link $cloneWithProperties}
   * if necessary. Will throw an error if called outside of a Lexical Editor
   * {@link LexicalEditor.update} callback.
   *
   */
  getWritable() {
    errorOnReadOnly();
    const editorState = getActiveEditorState();
    const editor = getActiveEditor();
    const nodeMap = editorState._nodeMap;
    const key = this.__key;
    const latestNode = this.getLatest();
    const cloneNotNeeded = editor._cloneNotNeeded;
    const selection = $getSelection();
    if (selection !== null) {
      selection.setCachedNodes(null);
    }
    if (cloneNotNeeded.has(key)) {
      internalMarkNodeAsDirty(latestNode);
      return latestNode;
    }
    const mutableNode = $cloneWithProperties(latestNode);
    cloneNotNeeded.add(key);
    internalMarkNodeAsDirty(mutableNode);
    nodeMap.set(key, mutableNode);
    return mutableNode;
  }
  /**
   * Returns the text content of the node. Override this for
   * custom nodes that should have a representation in plain text
   * format (for copy + paste, for example)
   *
   */
  getTextContent() {
    return "";
  }
  /**
   * Returns the length of the string produced by calling getTextContent on this node.
   *
   */
  getTextContentSize() {
    return this.getTextContent().length;
  }
  // View
  /**
   * Called during the reconciliation process to determine which nodes
   * to insert into the DOM for this Lexical Node.
   *
   * This method must return exactly one HTMLElement. Nested elements are not supported.
   *
   * Do not attempt to update the Lexical EditorState during this phase of the update lifecyle.
   *
   * @param _config - allows access to things like the EditorTheme (to apply classes) during reconciliation.
   * @param _editor - allows access to the editor for context during reconciliation.
   *
   * */
  createDOM(_config, _editor) {
    invariant(false, "createDOM: base method not extended");
  }
  /**
   * Called when a node changes and should update the DOM
   * in whatever way is necessary to make it align with any changes that might
   * have happened during the update.
   *
   * Returning "true" here will cause lexical to unmount and recreate the DOM node
   * (by calling createDOM). You would need to do this if the element tag changes,
   * for instance.
   *
   * */
  updateDOM(_prevNode, _dom, _config) {
    invariant(false, "updateDOM: base method not extended");
  }
  /**
   * Controls how the this node is serialized to HTML. This is important for
   * copy and paste between Lexical and non-Lexical editors, or Lexical editors with different namespaces,
   * in which case the primary transfer format is HTML. It's also important if you're serializing
   * to HTML for any other reason via {@link @lexical/html!$generateHtmlFromNodes}. You could
   * also use this method to build your own HTML renderer.
   *
   * */
  exportDOM(editor) {
    const element = this.createDOM(editor._config, editor);
    return { element };
  }
  /**
   * Controls how the this node is serialized to JSON. This is important for
   * copy and paste between Lexical editors sharing the same namespace. It's also important
   * if you're serializing to JSON for persistent storage somewhere.
   * See [Serialization & Deserialization](https://lexical.dev/docs/concepts/serialization#lexical---html).
   *
   * */
  exportJSON() {
    invariant(false, "exportJSON: base method not extended");
  }
  /**
   * Controls how the this node is deserialized from JSON. This is usually boilerplate,
   * but provides an abstraction between the node implementation and serialized interface that can
   * be important if you ever make breaking changes to a node schema (by adding or removing properties).
   * See [Serialization & Deserialization](https://lexical.dev/docs/concepts/serialization#lexical---html).
   *
   * */
  static importJSON(_serializedNode) {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .importJSON().",
      this.name
    );
  }
  /**
   * @experimental
   *
   * Registers the returned function as a transform on the node during
   * Editor initialization. Most such use cases should be addressed via
   * the {@link LexicalEditor.registerNodeTransform} API.
   *
   * Experimental - use at your own risk.
   */
  static transform() {
    return null;
  }
  // Setters and mutators
  /**
   * Removes this LexicalNode from the EditorState. If the node isn't re-inserted
   * somewhere, the Lexical garbage collector will eventually clean it up.
   *
   * @param preserveEmptyParent - If falsy, the node's parent will be removed if
   * it's empty after the removal operation. This is the default behavior, subject to
   * other node heuristics such as {@link ElementNode#canBeEmpty}
   * */
  remove(preserveEmptyParent) {
    $removeNode(this, true, preserveEmptyParent);
  }
  /**
   * Replaces this LexicalNode with the provided node, optionally transferring the children
   * of the replaced node to the replacing node.
   *
   * @param replaceWith - The node to replace this one with.
   * @param includeChildren - Whether or not to transfer the children of this node to the replacing node.
   * */
  replace(replaceWith, includeChildren) {
    errorOnReadOnly();
    let selection = $getSelection();
    if (selection !== null) {
      selection = selection.clone();
    }
    errorOnInsertTextNodeOnRoot(this, replaceWith);
    const self = this.getLatest();
    const toReplaceKey = this.__key;
    const key = replaceWith.__key;
    const writableReplaceWith = replaceWith.getWritable();
    const writableParent = this.getParentOrThrow().getWritable();
    const size = writableParent.__size;
    removeFromParent(writableReplaceWith);
    const prevSibling = self.getPreviousSibling();
    const nextSibling = self.getNextSibling();
    const prevKey = self.__prev;
    const nextKey = self.__next;
    const parentKey = self.__parent;
    $removeNode(self, false, true);
    if (prevSibling === null) {
      writableParent.__first = key;
    } else {
      const writablePrevSibling = prevSibling.getWritable();
      writablePrevSibling.__next = key;
    }
    writableReplaceWith.__prev = prevKey;
    if (nextSibling === null) {
      writableParent.__last = key;
    } else {
      const writableNextSibling = nextSibling.getWritable();
      writableNextSibling.__prev = key;
    }
    writableReplaceWith.__next = nextKey;
    writableReplaceWith.__parent = parentKey;
    writableParent.__size = size;
    if (includeChildren) {
      invariant(
        $isElementNode(this) && $isElementNode(writableReplaceWith),
        "includeChildren should only be true for ElementNodes"
      );
      this.getChildren().forEach((child) => {
        writableReplaceWith.append(child);
      });
    }
    if ($isRangeSelection(selection)) {
      $setSelection(selection);
      const anchor = selection.anchor;
      const focus = selection.focus;
      if (anchor.key === toReplaceKey) {
        $moveSelectionPointToEnd(anchor, writableReplaceWith);
      }
      if (focus.key === toReplaceKey) {
        $moveSelectionPointToEnd(focus, writableReplaceWith);
      }
    }
    if ($getCompositionKey() === toReplaceKey) {
      $setCompositionKey(key);
    }
    return writableReplaceWith;
  }
  /**
   * Inserts a node after this LexicalNode (as the next sibling).
   *
   * @param nodeToInsert - The node to insert after this one.
   * @param restoreSelection - Whether or not to attempt to resolve the
   * selection to the appropriate place after the operation is complete.
   * */
  insertAfter(nodeToInsert, restoreSelection = true) {
    errorOnReadOnly();
    errorOnInsertTextNodeOnRoot(this, nodeToInsert);
    const writableSelf = this.getWritable();
    const writableNodeToInsert = nodeToInsert.getWritable();
    const oldParent = writableNodeToInsert.getParent();
    const selection = $getSelection();
    let elementAnchorSelectionOnNode = false;
    let elementFocusSelectionOnNode = false;
    if (oldParent !== null) {
      const oldIndex = nodeToInsert.getIndexWithinParent();
      removeFromParent(writableNodeToInsert);
      if ($isRangeSelection(selection)) {
        const oldParentKey = oldParent.__key;
        const anchor = selection.anchor;
        const focus = selection.focus;
        elementAnchorSelectionOnNode = anchor.type === "element" && anchor.key === oldParentKey && anchor.offset === oldIndex + 1;
        elementFocusSelectionOnNode = focus.type === "element" && focus.key === oldParentKey && focus.offset === oldIndex + 1;
      }
    }
    const nextSibling = this.getNextSibling();
    const writableParent = this.getParentOrThrow().getWritable();
    const insertKey = writableNodeToInsert.__key;
    const nextKey = writableSelf.__next;
    if (nextSibling === null) {
      writableParent.__last = insertKey;
    } else {
      const writableNextSibling = nextSibling.getWritable();
      writableNextSibling.__prev = insertKey;
    }
    writableParent.__size++;
    writableSelf.__next = insertKey;
    writableNodeToInsert.__next = nextKey;
    writableNodeToInsert.__prev = writableSelf.__key;
    writableNodeToInsert.__parent = writableSelf.__parent;
    if (restoreSelection && $isRangeSelection(selection)) {
      const index = this.getIndexWithinParent();
      $updateElementSelectionOnCreateDeleteNode(
        selection,
        writableParent,
        index + 1
      );
      const writableParentKey = writableParent.__key;
      if (elementAnchorSelectionOnNode) {
        selection.anchor.set(writableParentKey, index + 2, "element");
      }
      if (elementFocusSelectionOnNode) {
        selection.focus.set(writableParentKey, index + 2, "element");
      }
    }
    return nodeToInsert;
  }
  /**
   * Inserts a node before this LexicalNode (as the previous sibling).
   *
   * @param nodeToInsert - The node to insert before this one.
   * @param restoreSelection - Whether or not to attempt to resolve the
   * selection to the appropriate place after the operation is complete.
   * */
  insertBefore(nodeToInsert, restoreSelection = true) {
    errorOnReadOnly();
    errorOnInsertTextNodeOnRoot(this, nodeToInsert);
    const writableSelf = this.getWritable();
    const writableNodeToInsert = nodeToInsert.getWritable();
    const insertKey = writableNodeToInsert.__key;
    removeFromParent(writableNodeToInsert);
    const prevSibling = this.getPreviousSibling();
    const writableParent = this.getParentOrThrow().getWritable();
    const prevKey = writableSelf.__prev;
    const index = this.getIndexWithinParent();
    if (prevSibling === null) {
      writableParent.__first = insertKey;
    } else {
      const writablePrevSibling = prevSibling.getWritable();
      writablePrevSibling.__next = insertKey;
    }
    writableParent.__size++;
    writableSelf.__prev = insertKey;
    writableNodeToInsert.__prev = prevKey;
    writableNodeToInsert.__next = writableSelf.__key;
    writableNodeToInsert.__parent = writableSelf.__parent;
    const selection = $getSelection();
    if (restoreSelection && $isRangeSelection(selection)) {
      const parent = this.getParentOrThrow();
      $updateElementSelectionOnCreateDeleteNode(selection, parent, index);
    }
    return nodeToInsert;
  }
  /**
   * Whether or not this node has a required parent. Used during copy + paste operations
   * to normalize nodes that would otherwise be orphaned. For example, ListItemNodes without
   * a ListNode parent or TextNodes with a ParagraphNode parent.
   *
   * */
  isParentRequired() {
    return false;
  }
  /**
   * The creation logic for any required parent. Should be implemented if {@link isParentRequired} returns true.
   *
   * */
  createParentElementNode() {
    return $createParagraphNode();
  }
  selectStart() {
    return this.selectPrevious();
  }
  selectEnd() {
    return this.selectNext(0, 0);
  }
  /**
   * Moves selection to the previous sibling of this node, at the specified offsets.
   *
   * @param anchorOffset - The anchor offset for selection.
   * @param focusOffset -  The focus offset for selection
   * */
  selectPrevious(anchorOffset, focusOffset) {
    errorOnReadOnly();
    const prevSibling = this.getPreviousSibling();
    const parent = this.getParentOrThrow();
    if (prevSibling === null) {
      return parent.select(0, 0);
    }
    if ($isElementNode(prevSibling)) {
      return prevSibling.select();
    } else if (!$isTextNode(prevSibling)) {
      const index = prevSibling.getIndexWithinParent() + 1;
      return parent.select(index, index);
    }
    return prevSibling.select(anchorOffset, focusOffset);
  }
  /**
   * Moves selection to the next sibling of this node, at the specified offsets.
   *
   * @param anchorOffset - The anchor offset for selection.
   * @param focusOffset -  The focus offset for selection
   * */
  selectNext(anchorOffset, focusOffset) {
    errorOnReadOnly();
    const nextSibling = this.getNextSibling();
    const parent = this.getParentOrThrow();
    if (nextSibling === null) {
      return parent.select();
    }
    if ($isElementNode(nextSibling)) {
      return nextSibling.select(0, 0);
    } else if (!$isTextNode(nextSibling)) {
      const index = nextSibling.getIndexWithinParent();
      return parent.select(index, index);
    }
    return nextSibling.select(anchorOffset, focusOffset);
  }
  /**
   * Marks a node dirty, triggering transforms and
   * forcing it to be reconciled during the update cycle.
   *
   * */
  markDirty() {
    this.getWritable();
  }
  /**
   * Insert the DOM of this node into that of the parent.
   * Allows this node to implement custom DOM attachment logic.
   * Boolean result indicates if the insertion was handled by the function.
   * A true return value prevents default insertion logic from taking place.
   */
  insertDOMIntoParent(nodeDOM, parentDOM) {
    return false;
  }
};
// eslint-disable-next-line @typescript-eslint/no-explicit-any
__publicField(LexicalNode, "importDOM");
function errorOnTypeKlassMismatch(type, klass) {
  const registeredNode = getActiveEditor()._nodes.get(type);
  if (registeredNode === void 0) {
    invariant(
      false,
      "Create node: Attempted to create node %s that was not configured to be used on the editor.",
      klass.name
    );
  }
  const editorKlass = registeredNode.klass;
  if (editorKlass !== klass) {
    invariant(
      false,
      "Create node: Type %s in node %s does not match registered node %s with the same type",
      type,
      klass.name,
      editorKlass.name
    );
  }
}
function insertRangeAfter(node, firstToInsert, lastToInsert) {
  const lastToInsert2 = lastToInsert || firstToInsert.getParentOrThrow().getLastChild();
  let current = firstToInsert;
  const nodesToInsert = [firstToInsert];
  while (current !== lastToInsert2) {
    if (!current.getNextSibling()) {
      invariant(
        false,
        "insertRangeAfter: lastToInsert must be a later sibling of firstToInsert"
      );
    }
    current = current.getNextSibling();
    nodesToInsert.push(current);
  }
  let currentNode = node;
  for (const nodeToInsert of nodesToInsert) {
    currentNode = currentNode.insertAfter(nodeToInsert);
  }
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalLineBreakNode.ts
var LineBreakNode2 = class _LineBreakNode extends LexicalNode {
  static getType() {
    return "linebreak";
  }
  static clone(node) {
    return new _LineBreakNode(node.__key);
  }
  constructor(key) {
    super(key);
  }
  getTextContent() {
    return "\n";
  }
  createDOM() {
    return document.createElement("br");
  }
  updateDOM() {
    return false;
  }
  static importDOM() {
    return {
      br: (node) => {
        if (isOnlyChildInBlockNode(node) || isLastChildInBlockNode(node)) {
          return null;
        }
        return {
          conversion: $convertLineBreakElement,
          priority: 0
        };
      }
    };
  }
  static importJSON(serializedLineBreakNode) {
    return $createLineBreakNode();
  }
  exportJSON() {
    return {
      type: "linebreak",
      version: 1
    };
  }
};
function $convertLineBreakElement(node) {
  return { node: $createLineBreakNode() };
}
function $createLineBreakNode() {
  return $applyNodeReplacement(new LineBreakNode2());
}
function $isLineBreakNode(node) {
  return node instanceof LineBreakNode2;
}
function isOnlyChildInBlockNode(node) {
  const parentElement = node.parentElement;
  if (parentElement !== null && isBlockDomNode(parentElement)) {
    const firstChild = parentElement.firstChild;
    if (firstChild === node || firstChild.nextSibling === node && isWhitespaceDomTextNode(firstChild)) {
      const lastChild = parentElement.lastChild;
      if (lastChild === node || lastChild.previousSibling === node && isWhitespaceDomTextNode(lastChild)) {
        return true;
      }
    }
  }
  return false;
}
function isLastChildInBlockNode(node) {
  const parentElement = node.parentElement;
  if (parentElement !== null && isBlockDomNode(parentElement)) {
    const firstChild = parentElement.firstChild;
    if (firstChild === node || firstChild.nextSibling === node && isWhitespaceDomTextNode(firstChild)) {
      return false;
    }
    const lastChild = parentElement.lastChild;
    if (lastChild === node || lastChild.previousSibling === node && isWhitespaceDomTextNode(lastChild)) {
      return true;
    }
  }
  return false;
}
function isWhitespaceDomTextNode(node) {
  return node.nodeType === DOM_TEXT_TYPE && /^( |\t|\r?\n)+$/.test(node.textContent || "");
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalTextNode.ts
function getElementOuterTag(node, format) {
  if (format & IS_CODE) {
    return "code";
  }
  if (format & IS_HIGHLIGHT) {
    return "mark";
  }
  if (format & IS_SUBSCRIPT) {
    return "sub";
  }
  if (format & IS_SUPERSCRIPT) {
    return "sup";
  }
  return null;
}
function getElementInnerTag(node, format) {
  if (format & IS_BOLD) {
    return "strong";
  }
  if (format & IS_ITALIC) {
    return "em";
  }
  return "span";
}
function setTextThemeClassNames(tag, prevFormat, nextFormat, dom, textClassNames) {
  const domClassList = dom.classList;
  let classNames = getCachedClassNameArray(textClassNames, "base");
  if (classNames !== void 0) {
    domClassList.add(...classNames);
  }
  classNames = getCachedClassNameArray(
    textClassNames,
    "underlineStrikethrough"
  );
  let hasUnderlineStrikethrough = false;
  const prevUnderlineStrikethrough = prevFormat & IS_UNDERLINE && prevFormat & IS_STRIKETHROUGH;
  const nextUnderlineStrikethrough = nextFormat & IS_UNDERLINE && nextFormat & IS_STRIKETHROUGH;
  if (classNames !== void 0) {
    if (nextUnderlineStrikethrough) {
      hasUnderlineStrikethrough = true;
      if (!prevUnderlineStrikethrough) {
        domClassList.add(...classNames);
      }
    } else if (prevUnderlineStrikethrough) {
      domClassList.remove(...classNames);
    }
  }
  for (const key in TEXT_TYPE_TO_FORMAT) {
    const format = key;
    const flag = TEXT_TYPE_TO_FORMAT[format];
    classNames = getCachedClassNameArray(textClassNames, key);
    if (classNames !== void 0) {
      if (nextFormat & flag) {
        if (hasUnderlineStrikethrough && (key === "underline" || key === "strikethrough")) {
          if (prevFormat & flag) {
            domClassList.remove(...classNames);
          }
          continue;
        }
        if ((prevFormat & flag) === 0 || prevUnderlineStrikethrough && key === "underline" || key === "strikethrough") {
          domClassList.add(...classNames);
        }
      } else if (prevFormat & flag) {
        domClassList.remove(...classNames);
      }
    }
  }
}
function diffComposedText(a, b) {
  const aLength = a.length;
  const bLength = b.length;
  let left = 0;
  let right = 0;
  while (left < aLength && left < bLength && a[left] === b[left]) {
    left++;
  }
  while (right + left < aLength && right + left < bLength && a[aLength - right - 1] === b[bLength - right - 1]) {
    right++;
  }
  return [left, aLength - left - right, b.slice(left, bLength - right)];
}
function setTextContent(nextText, dom, node) {
  const firstChild = dom.firstChild;
  const isComposing = node.isComposing();
  const suffix = isComposing ? COMPOSITION_SUFFIX : "";
  const text = nextText + suffix;
  if (firstChild == null) {
    dom.textContent = text;
  } else {
    const nodeValue = firstChild.nodeValue;
    if (nodeValue !== text) {
      if (isComposing || IS_FIREFOX) {
        const [index, remove, insert] = diffComposedText(
          nodeValue,
          text
        );
        if (remove !== 0) {
          firstChild.deleteData(index, remove);
        }
        firstChild.insertData(index, insert);
      } else {
        firstChild.nodeValue = text;
      }
    }
  }
}
function createTextInnerDOM(innerDOM, node, innerTag, format, text, config) {
  setTextContent(text, innerDOM, node);
  const theme2 = config.theme;
  const textClassNames = theme2.text;
  if (textClassNames !== void 0) {
    setTextThemeClassNames(innerTag, 0, format, innerDOM, textClassNames);
  }
}
function wrapElementWith(element, tag) {
  const el3 = document.createElement(tag);
  el3.appendChild(element);
  return el3;
}
var TextNode = class _TextNode extends LexicalNode {
  constructor(text, key) {
    super(key);
    __publicField(this, "__text");
    /** @internal */
    __publicField(this, "__format");
    /** @internal */
    __publicField(this, "__style");
    /** @internal */
    __publicField(this, "__mode");
    /** @internal */
    __publicField(this, "__detail");
    this.__text = text;
    this.__format = 0;
    this.__style = "";
    this.__mode = 0;
    this.__detail = 0;
  }
  static getType() {
    return "text";
  }
  static clone(node) {
    return new _TextNode(node.__text, node.__key);
  }
  afterCloneFrom(prevNode) {
    super.afterCloneFrom(prevNode);
    this.__format = prevNode.__format;
    this.__style = prevNode.__style;
    this.__mode = prevNode.__mode;
    this.__detail = prevNode.__detail;
  }
  /**
   * Returns a 32-bit integer that represents the TextFormatTypes currently applied to the
   * TextNode. You probably don't want to use this method directly - consider using TextNode.hasFormat instead.
   *
   * @returns a number representing the format of the text node.
   */
  getFormat() {
    const self = this.getLatest();
    return self.__format;
  }
  /**
   * Returns a 32-bit integer that represents the TextDetailTypes currently applied to the
   * TextNode. You probably don't want to use this method directly - consider using TextNode.isDirectionless
   * or TextNode.isUnmergeable instead.
   *
   * @returns a number representing the detail of the text node.
   */
  getDetail() {
    const self = this.getLatest();
    return self.__detail;
  }
  /**
   * Returns the mode (TextModeType) of the TextNode, which may be "normal", "token", or "segmented"
   *
   * @returns TextModeType.
   */
  getMode() {
    const self = this.getLatest();
    return TEXT_TYPE_TO_MODE[self.__mode];
  }
  /**
   * Returns the styles currently applied to the node. This is analogous to CSSText in the DOM.
   *
   * @returns CSSText-like string of styles applied to the underlying DOM node.
   */
  getStyle() {
    const self = this.getLatest();
    return self.__style;
  }
  /**
   * Returns whether or not the node is in "token" mode. TextNodes in token mode can be navigated through character-by-character
   * with a RangeSelection, but are deleted as a single entity (not invdividually by character).
   *
   * @returns true if the node is in token mode, false otherwise.
   */
  isToken() {
    const self = this.getLatest();
    return self.__mode === IS_TOKEN;
  }
  /**
   *
   * @returns true if Lexical detects that an IME or other 3rd-party script is attempting to
   * mutate the TextNode, false otherwise.
   */
  isComposing() {
    return this.__key === $getCompositionKey();
  }
  /**
   * Returns whether or not the node is in "segemented" mode. TextNodes in segemented mode can be navigated through character-by-character
   * with a RangeSelection, but are deleted in space-delimited "segments".
   *
   * @returns true if the node is in segmented mode, false otherwise.
   */
  isSegmented() {
    const self = this.getLatest();
    return self.__mode === IS_SEGMENTED;
  }
  /**
   * Returns whether or not the node is "directionless". Directionless nodes don't respect changes between RTL and LTR modes.
   *
   * @returns true if the node is directionless, false otherwise.
   */
  isDirectionless() {
    const self = this.getLatest();
    return (self.__detail & IS_DIRECTIONLESS) !== 0;
  }
  /**
   * Returns whether or not the node is unmergeable. In some scenarios, Lexical tries to merge
   * adjacent TextNodes into a single TextNode. If a TextNode is unmergeable, this won't happen.
   *
   * @returns true if the node is unmergeable, false otherwise.
   */
  isUnmergeable() {
    const self = this.getLatest();
    return (self.__detail & IS_UNMERGEABLE) !== 0;
  }
  /**
   * Returns whether or not the node has the provided format applied. Use this with the human-readable TextFormatType
   * string values to get the format of a TextNode.
   *
   * @param type - the TextFormatType to check for.
   *
   * @returns true if the node has the provided format, false otherwise.
   */
  hasFormat(type) {
    const formatFlag = TEXT_TYPE_TO_FORMAT[type];
    return (this.getFormat() & formatFlag) !== 0;
  }
  /**
   * Returns whether or not the node is simple text. Simple text is defined as a TextNode that has the string type "text"
   * (i.e., not a subclass) and has no mode applied to it (i.e., not segmented or token).
   *
   * @returns true if the node is simple text, false otherwise.
   */
  isSimpleText() {
    return this.__type === "text" && this.__mode === 0;
  }
  /**
   * Returns the text content of the node as a string.
   *
   * @returns a string representing the text content of the node.
   */
  getTextContent() {
    const self = this.getLatest();
    return self.__text;
  }
  /**
   * Returns the format flags applied to the node as a 32-bit integer.
   *
   * @returns a number representing the TextFormatTypes applied to the node.
   */
  getFormatFlags(type, alignWithFormat) {
    const self = this.getLatest();
    const format = self.__format;
    return toggleTextFormatType(format, type, alignWithFormat);
  }
  /**
   *
   * @returns true if the text node supports font styling, false otherwise.
   */
  canHaveFormat() {
    return true;
  }
  // View
  createDOM(config, editor) {
    const format = this.__format;
    const outerTag = getElementOuterTag(this, format);
    const innerTag = getElementInnerTag(this, format);
    const tag = outerTag === null ? innerTag : outerTag;
    const dom = document.createElement(tag);
    let innerDOM = dom;
    if (this.hasFormat("code")) {
      dom.setAttribute("spellcheck", "false");
    }
    if (outerTag !== null) {
      innerDOM = document.createElement(innerTag);
      dom.appendChild(innerDOM);
    }
    const text = this.__text;
    createTextInnerDOM(innerDOM, this, innerTag, format, text, config);
    const style = this.__style;
    if (style !== "") {
      dom.style.cssText = style;
    }
    return dom;
  }
  updateDOM(prevNode, dom, config) {
    const nextText = this.__text;
    const prevFormat = prevNode.__format;
    const nextFormat = this.__format;
    const prevOuterTag = getElementOuterTag(this, prevFormat);
    const nextOuterTag = getElementOuterTag(this, nextFormat);
    const prevInnerTag = getElementInnerTag(this, prevFormat);
    const nextInnerTag = getElementInnerTag(this, nextFormat);
    const prevTag = prevOuterTag === null ? prevInnerTag : prevOuterTag;
    const nextTag = nextOuterTag === null ? nextInnerTag : nextOuterTag;
    if (prevTag !== nextTag) {
      return true;
    }
    if (prevOuterTag === nextOuterTag && prevInnerTag !== nextInnerTag) {
      const prevInnerDOM = dom.firstChild;
      if (prevInnerDOM == null) {
        invariant(false, "updateDOM: prevInnerDOM is null or undefined");
      }
      const nextInnerDOM = document.createElement(nextInnerTag);
      createTextInnerDOM(
        nextInnerDOM,
        this,
        nextInnerTag,
        nextFormat,
        nextText,
        config
      );
      dom.replaceChild(nextInnerDOM, prevInnerDOM);
      return false;
    }
    let innerDOM = dom;
    if (nextOuterTag !== null) {
      if (prevOuterTag !== null) {
        innerDOM = dom.firstChild;
        if (innerDOM == null) {
          invariant(false, "updateDOM: innerDOM is null or undefined");
        }
      }
    }
    setTextContent(nextText, innerDOM, this);
    const theme2 = config.theme;
    const textClassNames = theme2.text;
    if (textClassNames !== void 0 && prevFormat !== nextFormat) {
      setTextThemeClassNames(
        nextInnerTag,
        prevFormat,
        nextFormat,
        innerDOM,
        textClassNames
      );
    }
    const prevStyle = prevNode.__style;
    const nextStyle = this.__style;
    if (prevStyle !== nextStyle) {
      dom.style.cssText = nextStyle;
    }
    return false;
  }
  static importDOM() {
    return {
      "#text": () => ({
        conversion: $convertTextDOMNode,
        priority: 0
      }),
      b: () => ({
        conversion: convertBringAttentionToElement,
        priority: 0
      }),
      code: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      em: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      i: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      s: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      span: () => ({
        conversion: convertSpanElement,
        priority: 0
      }),
      strong: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      sub: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      sup: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      }),
      u: () => ({
        conversion: convertTextFormatElement,
        priority: 0
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createTextNode(serializedNode.text);
    node.setFormat(serializedNode.format);
    node.setDetail(serializedNode.detail);
    node.setMode(serializedNode.mode);
    node.setStyle(serializedNode.style);
    return node;
  }
  // This improves Lexical's basic text output in copy+paste plus
  // for headless mode where people might use Lexical to generate
  // HTML content and not have the ability to use CSS classes.
  exportDOM(editor) {
    let { element } = super.exportDOM(editor);
    const originalElementName = (element?.nodeName || "").toLowerCase();
    invariant(
      element !== null && isHTMLElement(element),
      "Expected TextNode createDOM to always return a HTMLElement"
    );
    const text = this.getTextContent();
    if (/^\s|\s$/.test(text)) {
      element.style.whiteSpace = "pre-wrap";
    }
    for (const className of Array.from(element.classList.values())) {
      if (className.startsWith("editor-theme-")) {
        element.classList.remove(className);
      }
    }
    if (element.classList.length === 0) {
      element.removeAttribute("class");
    }
    if (element.nodeName === "SPAN" && !element.getAttribute("style")) {
      element = document.createTextNode(text);
    }
    if (this.hasFormat("bold") && originalElementName !== "strong") {
      element = wrapElementWith(element, "strong");
    }
    if (this.hasFormat("italic")) {
      element = wrapElementWith(element, "em");
    }
    if (this.hasFormat("strikethrough")) {
      element = wrapElementWith(element, "s");
    }
    if (this.hasFormat("underline")) {
      element = wrapElementWith(element, "u");
    }
    return {
      element
    };
  }
  exportJSON() {
    return {
      detail: this.getDetail(),
      format: this.getFormat(),
      mode: this.getMode(),
      style: this.getStyle(),
      text: this.getTextContent(),
      type: "text",
      version: 1
    };
  }
  // Mutators
  selectionTransform(prevSelection, nextSelection) {
    return;
  }
  /**
   * Sets the node format to the provided TextFormatType or 32-bit integer. Note that the TextFormatType
   * version of the argument can only specify one format and doing so will remove all other formats that
   * may be applied to the node. For toggling behavior, consider using {@link TextNode.toggleFormat}
   *
   * @param format - TextFormatType or 32-bit integer representing the node format.
   *
   * @returns this TextNode.
   * // TODO 0.12 This should just be a `string`.
   */
  setFormat(format) {
    const self = this.getWritable();
    self.__format = typeof format === "string" ? TEXT_TYPE_TO_FORMAT[format] : format;
    return self;
  }
  /**
   * Sets the node detail to the provided TextDetailType or 32-bit integer. Note that the TextDetailType
   * version of the argument can only specify one detail value and doing so will remove all other detail values that
   * may be applied to the node. For toggling behavior, consider using {@link TextNode.toggleDirectionless}
   * or {@link TextNode.toggleUnmergeable}
   *
   * @param detail - TextDetailType or 32-bit integer representing the node detail.
   *
   * @returns this TextNode.
   * // TODO 0.12 This should just be a `string`.
   */
  setDetail(detail) {
    const self = this.getWritable();
    self.__detail = typeof detail === "string" ? DETAIL_TYPE_TO_DETAIL[detail] : detail;
    return self;
  }
  /**
   * Sets the node style to the provided CSSText-like string. Set this property as you
   * would an HTMLElement style attribute to apply inline styles to the underlying DOM Element.
   *
   * @param style - CSSText to be applied to the underlying HTMLElement.
   *
   * @returns this TextNode.
   */
  setStyle(style) {
    const self = this.getWritable();
    self.__style = style;
    return self;
  }
  /**
   * Applies the provided format to this TextNode if it's not present. Removes it if it's present.
   * The subscript and superscript formats are mutually exclusive.
   * Prefer using this method to turn specific formats on and off.
   *
   * @param type - TextFormatType to toggle.
   *
   * @returns this TextNode.
   */
  toggleFormat(type) {
    const format = this.getFormat();
    const newFormat = toggleTextFormatType(format, type, null);
    return this.setFormat(newFormat);
  }
  /**
   * Toggles the directionless detail value of the node. Prefer using this method over setDetail.
   *
   * @returns this TextNode.
   */
  toggleDirectionless() {
    const self = this.getWritable();
    self.__detail ^= IS_DIRECTIONLESS;
    return self;
  }
  /**
   * Toggles the unmergeable detail value of the node. Prefer using this method over setDetail.
   *
   * @returns this TextNode.
   */
  toggleUnmergeable() {
    const self = this.getWritable();
    self.__detail ^= IS_UNMERGEABLE;
    return self;
  }
  /**
   * Sets the mode of the node.
   *
   * @returns this TextNode.
   */
  setMode(type) {
    const mode = TEXT_MODE_TO_TYPE[type];
    if (this.__mode === mode) {
      return this;
    }
    const self = this.getWritable();
    self.__mode = mode;
    return self;
  }
  /**
   * Sets the text content of the node.
   *
   * @param text - the string to set as the text value of the node.
   *
   * @returns this TextNode.
   */
  setTextContent(text) {
    if (this.__text === text) {
      return this;
    }
    const self = this.getWritable();
    self.__text = text;
    return self;
  }
  /**
   * Sets the current Lexical selection to be a RangeSelection with anchor and focus on this TextNode at the provided offsets.
   *
   * @param _anchorOffset - the offset at which the Selection anchor will be placed.
   * @param _focusOffset - the offset at which the Selection focus will be placed.
   *
   * @returns the new RangeSelection.
   */
  select(_anchorOffset, _focusOffset) {
    errorOnReadOnly();
    let anchorOffset = _anchorOffset;
    let focusOffset = _focusOffset;
    const selection = $getSelection();
    const text = this.getTextContent();
    const key = this.__key;
    if (typeof text === "string") {
      const lastOffset = text.length;
      if (anchorOffset === void 0) {
        anchorOffset = lastOffset;
      }
      if (focusOffset === void 0) {
        focusOffset = lastOffset;
      }
    } else {
      anchorOffset = 0;
      focusOffset = 0;
    }
    if (!$isRangeSelection(selection)) {
      return $internalMakeRangeSelection(
        key,
        anchorOffset,
        key,
        focusOffset,
        "text",
        "text"
      );
    } else {
      const compositionKey = $getCompositionKey();
      if (compositionKey === selection.anchor.key || compositionKey === selection.focus.key) {
        $setCompositionKey(key);
      }
      selection.setTextNodeRange(this, anchorOffset, this, focusOffset);
    }
    return selection;
  }
  selectStart() {
    return this.select(0, 0);
  }
  selectEnd() {
    const size = this.getTextContentSize();
    return this.select(size, size);
  }
  /**
   * Inserts the provided text into this TextNode at the provided offset, deleting the number of characters
   * specified. Can optionally calculate a new selection after the operation is complete.
   *
   * @param offset - the offset at which the splice operation should begin.
   * @param delCount - the number of characters to delete, starting from the offset.
   * @param newText - the text to insert into the TextNode at the offset.
   * @param moveSelection - optional, whether or not to move selection to the end of the inserted substring.
   *
   * @returns this TextNode.
   */
  spliceText(offset, delCount, newText, moveSelection) {
    const writableSelf = this.getWritable();
    const text = writableSelf.__text;
    const handledTextLength = newText.length;
    let index = offset;
    if (index < 0) {
      index = handledTextLength + index;
      if (index < 0) {
        index = 0;
      }
    }
    const selection = $getSelection();
    if (moveSelection && $isRangeSelection(selection)) {
      const newOffset = offset + handledTextLength;
      selection.setTextNodeRange(
        writableSelf,
        newOffset,
        writableSelf,
        newOffset
      );
    }
    const updatedText = text.slice(0, index) + newText + text.slice(index + delCount);
    writableSelf.__text = updatedText;
    return writableSelf;
  }
  /**
   * This method is meant to be overriden by TextNode subclasses to control the behavior of those nodes
   * when a user event would cause text to be inserted before them in the editor. If true, Lexical will attempt
   * to insert text into this node. If false, it will insert the text in a new sibling node.
   *
   * @returns true if text can be inserted before the node, false otherwise.
   */
  canInsertTextBefore() {
    return true;
  }
  /**
   * This method is meant to be overriden by TextNode subclasses to control the behavior of those nodes
   * when a user event would cause text to be inserted after them in the editor. If true, Lexical will attempt
   * to insert text into this node. If false, it will insert the text in a new sibling node.
   *
   * @returns true if text can be inserted after the node, false otherwise.
   */
  canInsertTextAfter() {
    return true;
  }
  /**
   * Splits this TextNode at the provided character offsets, forming new TextNodes from the substrings
   * formed by the split, and inserting those new TextNodes into the editor, replacing the one that was split.
   *
   * @param splitOffsets - rest param of the text content character offsets at which this node should be split.
   *
   * @returns an Array containing the newly-created TextNodes.
   */
  splitText(...splitOffsets) {
    errorOnReadOnly();
    const self = this.getLatest();
    const textContent = self.getTextContent();
    const key = self.__key;
    const compositionKey = $getCompositionKey();
    const offsetsSet = new Set(splitOffsets);
    const parts = [];
    const textLength = textContent.length;
    let string = "";
    for (let i = 0; i < textLength; i++) {
      if (string !== "" && offsetsSet.has(i)) {
        parts.push(string);
        string = "";
      }
      string += textContent[i];
    }
    if (string !== "") {
      parts.push(string);
    }
    const partsLength = parts.length;
    if (partsLength === 0) {
      return [];
    } else if (parts[0] === textContent) {
      return [self];
    }
    const firstPart = parts[0];
    const parent = self.getParent();
    let writableNode;
    const format = self.getFormat();
    const style = self.getStyle();
    const detail = self.__detail;
    let hasReplacedSelf = false;
    if (self.isSegmented()) {
      writableNode = $createTextNode(firstPart);
      writableNode.__format = format;
      writableNode.__style = style;
      writableNode.__detail = detail;
      hasReplacedSelf = true;
    } else {
      writableNode = self.getWritable();
      writableNode.__text = firstPart;
    }
    const selection = $getSelection();
    const splitNodes = [writableNode];
    let textSize = firstPart.length;
    for (let i = 1; i < partsLength; i++) {
      const part = parts[i];
      const partSize = part.length;
      const sibling = $createTextNode(part).getWritable();
      sibling.__format = format;
      sibling.__style = style;
      sibling.__detail = detail;
      const siblingKey = sibling.__key;
      const nextTextSize = textSize + partSize;
      if ($isRangeSelection(selection)) {
        const anchor = selection.anchor;
        const focus = selection.focus;
        if (anchor.key === key && anchor.type === "text" && anchor.offset > textSize && anchor.offset <= nextTextSize) {
          anchor.key = siblingKey;
          anchor.offset -= textSize;
          selection.dirty = true;
        }
        if (focus.key === key && focus.type === "text" && focus.offset > textSize && focus.offset <= nextTextSize) {
          focus.key = siblingKey;
          focus.offset -= textSize;
          selection.dirty = true;
        }
      }
      if (compositionKey === key) {
        $setCompositionKey(siblingKey);
      }
      textSize = nextTextSize;
      splitNodes.push(sibling);
    }
    if (parent !== null) {
      internalMarkSiblingsAsDirty(this);
      const writableParent = parent.getWritable();
      const insertionIndex = this.getIndexWithinParent();
      if (hasReplacedSelf) {
        writableParent.splice(insertionIndex, 0, splitNodes);
        this.remove();
      } else {
        writableParent.splice(insertionIndex, 1, splitNodes);
      }
      if ($isRangeSelection(selection)) {
        $updateElementSelectionOnCreateDeleteNode(
          selection,
          parent,
          insertionIndex,
          partsLength - 1
        );
      }
    }
    return splitNodes;
  }
  /**
   * Merges the target TextNode into this TextNode, removing the target node.
   *
   * @param target - the TextNode to merge into this one.
   *
   * @returns this TextNode.
   */
  mergeWithSibling(target) {
    const isBefore = target === this.getPreviousSibling();
    if (!isBefore && target !== this.getNextSibling()) {
      invariant(
        false,
        "mergeWithSibling: sibling must be a previous or next sibling"
      );
    }
    const key = this.__key;
    const targetKey = target.__key;
    const text = this.__text;
    const textLength = text.length;
    const compositionKey = $getCompositionKey();
    if (compositionKey === targetKey) {
      $setCompositionKey(key);
    }
    const selection = $getSelection();
    if ($isRangeSelection(selection)) {
      const anchor = selection.anchor;
      const focus = selection.focus;
      if (anchor !== null && anchor.key === targetKey) {
        adjustPointOffsetForMergedSibling(
          anchor,
          isBefore,
          key,
          target,
          textLength
        );
        selection.dirty = true;
      }
      if (focus !== null && focus.key === targetKey) {
        adjustPointOffsetForMergedSibling(
          focus,
          isBefore,
          key,
          target,
          textLength
        );
        selection.dirty = true;
      }
    }
    const targetText = target.__text;
    const newText = isBefore ? targetText + text : text + targetText;
    this.setTextContent(newText);
    const writableSelf = this.getWritable();
    target.remove();
    return writableSelf;
  }
  /**
   * This method is meant to be overriden by TextNode subclasses to control the behavior of those nodes
   * when used with the registerLexicalTextEntity function. If you're using registerLexicalTextEntity, the
   * node class that you create and replace matched text with should return true from this method.
   *
   * @returns true if the node is to be treated as a "text entity", false otherwise.
   */
  isTextEntity() {
    return false;
  }
};
function convertSpanElement(domNode) {
  const span = domNode;
  const style = span.style;
  return {
    forChild: applyTextFormatFromStyle(style),
    node: null
  };
}
function convertBringAttentionToElement(domNode) {
  const b = domNode;
  const hasNormalFontWeight = b.style.fontWeight === "normal";
  return {
    forChild: applyTextFormatFromStyle(
      b.style,
      hasNormalFontWeight ? void 0 : "bold"
    ),
    node: null
  };
}
var preParentCache = /* @__PURE__ */ new WeakMap();
function isNodePre(node) {
  return node.nodeName === "PRE" || node.nodeType === DOM_ELEMENT_TYPE && node.style !== void 0 && node.style.whiteSpace !== void 0 && node.style.whiteSpace.startsWith("pre");
}
function findParentPreDOMNode(node) {
  let cached;
  let parent = node.parentNode;
  const visited = [node];
  while (parent !== null && (cached = preParentCache.get(parent)) === void 0 && !isNodePre(parent)) {
    visited.push(parent);
    parent = parent.parentNode;
  }
  const resultNode = cached === void 0 ? parent : cached;
  for (let i = 0; i < visited.length; i++) {
    preParentCache.set(visited[i], resultNode);
  }
  return resultNode;
}
function $convertTextDOMNode(domNode) {
  const domNode_ = domNode;
  const parentDom = domNode.parentElement;
  invariant(
    parentDom !== null,
    "Expected parentElement of Text not to be null"
  );
  let textContent = domNode_.textContent || "";
  if (findParentPreDOMNode(domNode_) !== null) {
    const parts = textContent.split(/(\r?\n|\t)/);
    const nodes = [];
    const length = parts.length;
    for (let i = 0; i < length; i++) {
      const part = parts[i];
      if (part === "\n" || part === "\r\n") {
        nodes.push($createLineBreakNode());
      } else if (part === "	") {
        nodes.push($createTabNode());
      } else if (part !== "") {
        nodes.push($createTextNode(part));
      }
    }
    return { node: nodes };
  }
  textContent = textContent.replace(/\r/g, "").replace(/[ \t\n]+/g, " ");
  if (textContent === "") {
    return { node: null };
  }
  if (textContent[0] === " ") {
    let previousText = domNode_;
    let isStartOfLine = true;
    while (previousText !== null && (previousText = findTextInLine(previousText, false)) !== null) {
      const previousTextContent = previousText.textContent || "";
      if (previousTextContent.length > 0) {
        if (/[ \t\n]$/.test(previousTextContent)) {
          textContent = textContent.slice(1);
        }
        isStartOfLine = false;
        break;
      }
    }
    if (isStartOfLine) {
      textContent = textContent.slice(1);
    }
  }
  if (textContent[textContent.length - 1] === " ") {
    let nextText = domNode_;
    let isEndOfLine = true;
    while (nextText !== null && (nextText = findTextInLine(nextText, true)) !== null) {
      const nextTextContent = (nextText.textContent || "").replace(
        /^( |\t|\r?\n)+/,
        ""
      );
      if (nextTextContent.length > 0) {
        isEndOfLine = false;
        break;
      }
    }
    if (isEndOfLine) {
      textContent = textContent.slice(0, textContent.length - 1);
    }
  }
  if (textContent === "") {
    return { node: null };
  }
  return { node: $createTextNode(textContent) };
}
function findTextInLine(text, forward) {
  let node = text;
  while (true) {
    let sibling;
    while ((sibling = forward ? node.nextSibling : node.previousSibling) === null) {
      const parentElement = node.parentElement;
      if (parentElement === null) {
        return null;
      }
      node = parentElement;
    }
    node = sibling;
    if (node.nodeType === DOM_ELEMENT_TYPE) {
      const display = node.style.display;
      if (display === "" && !isInlineDomNode(node) || display !== "" && !display.startsWith("inline")) {
        return null;
      }
    }
    let descendant = node;
    while ((descendant = forward ? node.firstChild : node.lastChild) !== null) {
      node = descendant;
    }
    if (node.nodeType === DOM_TEXT_TYPE) {
      return node;
    } else if (node.nodeName === "BR") {
      return null;
    }
  }
}
var nodeNameToTextFormat = {
  code: "code",
  em: "italic",
  i: "italic",
  s: "strikethrough",
  strong: "bold",
  sub: "subscript",
  sup: "superscript",
  u: "underline"
};
function convertTextFormatElement(domNode) {
  const format = nodeNameToTextFormat[domNode.nodeName.toLowerCase()];
  if (format === "code" && domNode.closest("pre")) {
    return { node: null };
  }
  if (format === void 0) {
    return { node: null };
  }
  return {
    forChild: applyTextFormatFromStyle(domNode.style, format),
    node: null
  };
}
function $createTextNode(text = "") {
  return $applyNodeReplacement(new TextNode(text));
}
function $isTextNode(node) {
  return node instanceof TextNode;
}
function applyTextFormatFromStyle(style, shouldApply) {
  const fontWeight = style.fontWeight;
  const textDecoration = style.textDecoration.split(" ");
  const hasBoldFontWeight = fontWeight === "700" || fontWeight === "bold";
  const hasLinethroughTextDecoration = textDecoration.includes("line-through");
  const hasItalicFontStyle = style.fontStyle === "italic";
  const hasUnderlineTextDecoration = textDecoration.includes("underline");
  const verticalAlign = style.verticalAlign;
  const color = style.color;
  const backgroundColor = style.backgroundColor;
  return (lexicalNode) => {
    if (!$isTextNode(lexicalNode)) {
      return lexicalNode;
    }
    if (hasBoldFontWeight && !lexicalNode.hasFormat("bold")) {
      lexicalNode.toggleFormat("bold");
    }
    if (hasLinethroughTextDecoration && !lexicalNode.hasFormat("strikethrough")) {
      lexicalNode.toggleFormat("strikethrough");
    }
    if (hasItalicFontStyle && !lexicalNode.hasFormat("italic")) {
      lexicalNode.toggleFormat("italic");
    }
    if (hasUnderlineTextDecoration && !lexicalNode.hasFormat("underline")) {
      lexicalNode.toggleFormat("underline");
    }
    if (verticalAlign === "sub" && !lexicalNode.hasFormat("subscript")) {
      lexicalNode.toggleFormat("subscript");
    }
    if (verticalAlign === "super" && !lexicalNode.hasFormat("superscript")) {
      lexicalNode.toggleFormat("superscript");
    }
    let style2 = lexicalNode.getStyle();
    if (color) {
      style2 += `color: ${color};`;
    }
    if (backgroundColor && backgroundColor !== "transparent") {
      style2 += `background-color: ${backgroundColor};`;
    }
    if (style2) {
      lexicalNode.setStyle(style2);
    }
    if (shouldApply && !lexicalNode.hasFormat(shouldApply)) {
      lexicalNode.toggleFormat(shouldApply);
    }
    return lexicalNode;
  };
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalTabNode.ts
var TabNode = class _TabNode extends TextNode {
  static getType() {
    return "tab";
  }
  static clone(node) {
    return new _TabNode(node.__key);
  }
  afterCloneFrom(prevNode) {
    super.afterCloneFrom(prevNode);
    this.__text = prevNode.__text;
  }
  constructor(key) {
    super("	", key);
    this.__detail = IS_UNMERGEABLE;
  }
  static importDOM() {
    return null;
  }
  static importJSON(serializedTabNode) {
    const node = $createTabNode();
    node.setFormat(serializedTabNode.format);
    node.setStyle(serializedTabNode.style);
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "tab",
      version: 1
    };
  }
  setTextContent(_text) {
    invariant(false, "TabNode does not support setTextContent");
  }
  setDetail(_detail) {
    invariant(false, "TabNode does not support setDetail");
  }
  setMode(_type) {
    invariant(false, "TabNode does not support setMode");
  }
  canInsertTextBefore() {
    return false;
  }
  canInsertTextAfter() {
    return false;
  }
};
function $createTabNode() {
  return $applyNodeReplacement(new TabNode());
}
function $isTabNode(node) {
  return node instanceof TabNode;
}

// resources/js/wysiwyg/lexical/utils/mergeRegister.ts
function mergeRegister(...func) {
  return () => {
    for (let i = func.length - 1; i >= 0; i--) {
      func[i]();
    }
    func.length = 0;
  };
}

// resources/js/wysiwyg/lexical/selection/constants.ts
var CSS_TO_STYLES = /* @__PURE__ */ new Map();

// resources/js/wysiwyg/lexical/selection/utils.ts
function getStyleObjectFromRawCSS(css) {
  const styleObject = {};
  const styles = css.split(";");
  for (const style of styles) {
    if (style !== "") {
      const [key, value] = style.split(/:([^]+)/);
      if (key && value) {
        styleObject[key.trim()] = value.trim();
      }
    }
  }
  return styleObject;
}
function getStyleObjectFromCSS(css) {
  let value = CSS_TO_STYLES.get(css);
  if (value === void 0) {
    value = getStyleObjectFromRawCSS(css);
    CSS_TO_STYLES.set(css, value);
  }
  if (__DEV__) {
    Object.freeze(value);
  }
  return value;
}
function getCSSFromStyleObject(styles) {
  let css = "";
  for (const style in styles) {
    if (style) {
      css += `${style}: ${styles[style]};`;
    }
  }
  return css;
}

// resources/js/wysiwyg/lexical/selection/lexical-node.ts
function $sliceSelectedTextNodeContent(selection, textNode) {
  const anchorAndFocus = selection.getStartEndPoints();
  if (textNode.isSelected(selection) && !textNode.isSegmented() && !textNode.isToken() && anchorAndFocus !== null) {
    const [anchor, focus] = anchorAndFocus;
    const isBackward = selection.isBackward();
    const anchorNode = anchor.getNode();
    const focusNode = focus.getNode();
    const isAnchor = textNode.is(anchorNode);
    const isFocus = textNode.is(focusNode);
    if (isAnchor || isFocus) {
      const [anchorOffset, focusOffset] = $getCharacterOffsets(selection);
      const isSame = anchorNode.is(focusNode);
      const isFirst = textNode.is(isBackward ? focusNode : anchorNode);
      const isLast = textNode.is(isBackward ? anchorNode : focusNode);
      let startOffset = 0;
      let endOffset = void 0;
      if (isSame) {
        startOffset = anchorOffset > focusOffset ? focusOffset : anchorOffset;
        endOffset = anchorOffset > focusOffset ? anchorOffset : focusOffset;
      } else if (isFirst) {
        const offset = isBackward ? focusOffset : anchorOffset;
        startOffset = offset;
        endOffset = void 0;
      } else if (isLast) {
        const offset = isBackward ? anchorOffset : focusOffset;
        startOffset = 0;
        endOffset = offset;
      }
      textNode.__text = textNode.__text.slice(startOffset, endOffset);
      return textNode;
    }
  }
  return textNode;
}
function $addNodeStyle(node) {
  const CSSText = node.getStyle();
  const styles = getStyleObjectFromRawCSS(CSSText);
  CSS_TO_STYLES.set(CSSText, styles);
}
function $patchStyle(target, patch) {
  const prevStyles = getStyleObjectFromCSS(
    "getStyle" in target ? target.getStyle() : target.style
  );
  const newStyles = Object.entries(patch).reduce(
    (styles, [key, value]) => {
      if (typeof value === "function") {
        styles[key] = value(prevStyles[key], target);
      } else if (value === null) {
        delete styles[key];
      } else {
        styles[key] = value;
      }
      return styles;
    },
    { ...prevStyles }
  );
  const newCSSText = getCSSFromStyleObject(newStyles);
  target.setStyle(newCSSText);
  CSS_TO_STYLES.set(newCSSText, newStyles);
}
function $patchStyleText(selection, patch) {
  const selectedNodes = selection.getNodes();
  const selectedNodesLength = selectedNodes.length;
  const anchorAndFocus = selection.getStartEndPoints();
  if (anchorAndFocus === null) {
    return;
  }
  const [anchor, focus] = anchorAndFocus;
  const lastIndex = selectedNodesLength - 1;
  let firstNode = selectedNodes[0];
  let lastNode = selectedNodes[lastIndex];
  if (selection.isCollapsed() && $isRangeSelection(selection)) {
    $patchStyle(selection, patch);
    return;
  }
  const firstNodeText = firstNode.getTextContent();
  const firstNodeTextLength = firstNodeText.length;
  const focusOffset = focus.offset;
  let anchorOffset = anchor.offset;
  const isBefore = anchor.isBefore(focus);
  let startOffset = isBefore ? anchorOffset : focusOffset;
  let endOffset = isBefore ? focusOffset : anchorOffset;
  const startType = isBefore ? anchor.type : focus.type;
  const endType = isBefore ? focus.type : anchor.type;
  const endKey = isBefore ? focus.key : anchor.key;
  if ($isTextNode(firstNode) && startOffset === firstNodeTextLength) {
    const nextSibling = firstNode.getNextSibling();
    if ($isTextNode(nextSibling)) {
      anchorOffset = 0;
      startOffset = 0;
      firstNode = nextSibling;
    }
  }
  if (selectedNodes.length === 1) {
    if ($isTextNode(firstNode) && firstNode.canHaveFormat()) {
      startOffset = startType === "element" ? 0 : anchorOffset > focusOffset ? focusOffset : anchorOffset;
      endOffset = endType === "element" ? firstNodeTextLength : anchorOffset > focusOffset ? anchorOffset : focusOffset;
      if (startOffset === endOffset) {
        return;
      }
      if ($isTokenOrSegmented(firstNode) || startOffset === 0 && endOffset === firstNodeTextLength) {
        $patchStyle(firstNode, patch);
        firstNode.select(startOffset, endOffset);
      } else {
        const splitNodes = firstNode.splitText(startOffset, endOffset);
        const replacement = startOffset === 0 ? splitNodes[0] : splitNodes[1];
        $patchStyle(replacement, patch);
        replacement.select(0, endOffset - startOffset);
      }
    }
  } else {
    if ($isTextNode(firstNode) && startOffset < firstNode.getTextContentSize() && firstNode.canHaveFormat()) {
      if (startOffset !== 0 && !$isTokenOrSegmented(firstNode)) {
        firstNode = firstNode.splitText(startOffset)[1];
        startOffset = 0;
        if (isBefore) {
          anchor.set(firstNode.getKey(), startOffset, "text");
        } else {
          focus.set(firstNode.getKey(), startOffset, "text");
        }
      }
      $patchStyle(firstNode, patch);
    }
    if ($isTextNode(lastNode) && lastNode.canHaveFormat()) {
      const lastNodeText = lastNode.getTextContent();
      const lastNodeTextLength = lastNodeText.length;
      if (lastNode.__key !== endKey && endOffset !== 0) {
        endOffset = lastNodeTextLength;
      }
      if (endOffset !== lastNodeTextLength && !$isTokenOrSegmented(lastNode)) {
        [lastNode] = lastNode.splitText(endOffset);
      }
      if (endOffset !== 0 || endType === "element") {
        $patchStyle(lastNode, patch);
      }
    }
    for (let i = 1; i < lastIndex; i++) {
      const selectedNode = selectedNodes[i];
      const selectedNodeKey = selectedNode.getKey();
      if ($isTextNode(selectedNode) && selectedNode.canHaveFormat() && selectedNodeKey !== firstNode.getKey() && selectedNodeKey !== lastNode.getKey() && !selectedNode.isToken()) {
        $patchStyle(selectedNode, patch);
      }
    }
  }
}

// resources/js/wysiwyg/lexical/selection/range-selection.ts
function $setBlocksType(selection, createElement) {
  if (selection === null) {
    return;
  }
  const anchorAndFocus = selection.getStartEndPoints();
  const anchor = anchorAndFocus ? anchorAndFocus[0] : null;
  if (anchor !== null && anchor.key === "root") {
    const element = createElement();
    const root = $getRoot();
    const firstChild = root.getFirstChild();
    if (firstChild) {
      firstChild.replace(element, true);
    } else {
      root.append(element);
    }
    return;
  }
  const nodes = selection.getNodes();
  const firstSelectedBlock = anchor !== null ? $getAncestor2(anchor.getNode(), INTERNAL_$isBlock2) : false;
  if (firstSelectedBlock && nodes.indexOf(firstSelectedBlock) === -1) {
    nodes.push(firstSelectedBlock);
  }
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    if (!INTERNAL_$isBlock2(node)) {
      continue;
    }
    invariant($isElementNode(node), "Expected block node to be an ElementNode");
    const targetElement = createElement();
    node.replace(targetElement, true);
  }
}
function $shouldOverrideDefaultCharacterSelection(selection, isBackward) {
  const possibleNode = $getAdjacentNode(selection.focus, isBackward);
  return $isDecoratorNode(possibleNode) && !possibleNode.isIsolated() || $isElementNode(possibleNode) && !possibleNode.isInline() && !possibleNode.canBeEmpty();
}
function $moveCaretSelection(selection, isHoldingShift, isBackward, granularity) {
  selection.modify(isHoldingShift ? "extend" : "move", isBackward, granularity);
}
function $isParentElementRTL(selection) {
  const anchorNode = selection.anchor.getNode();
  const parent = $isRootNode(anchorNode) ? anchorNode : anchorNode.getParentOrThrow();
  return parent.getDirection() === "rtl";
}
function $moveCharacter(selection, isHoldingShift, isBackward) {
  const isRTL = $isParentElementRTL(selection);
  $moveCaretSelection(
    selection,
    isHoldingShift,
    isBackward ? !isRTL : isRTL,
    "character"
  );
}
function $getNodeStyleValueForProperty(node, styleProperty, defaultValue) {
  const css = node.getStyle();
  const styleObject = getStyleObjectFromCSS(css);
  if (styleObject !== null) {
    return styleObject[styleProperty] || defaultValue;
  }
  return defaultValue;
}
function $getSelectionStyleValueForProperty(selection, styleProperty, defaultValue = "") {
  let styleValue = null;
  const nodes = selection.getNodes();
  const anchor = selection.anchor;
  const focus = selection.focus;
  const isBackward = selection.isBackward();
  const endOffset = isBackward ? focus.offset : anchor.offset;
  const endNode = isBackward ? focus.getNode() : anchor.getNode();
  if ($isRangeSelection(selection) && selection.isCollapsed() && selection.style !== "") {
    const css = selection.style;
    const styleObject = getStyleObjectFromCSS(css);
    if (styleObject !== null && styleProperty in styleObject) {
      return styleObject[styleProperty];
    }
  }
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    if (i !== 0 && endOffset === 0 && node.is(endNode)) {
      continue;
    }
    if ($isTextNode(node)) {
      const nodeStyleValue = $getNodeStyleValueForProperty(
        node,
        styleProperty,
        defaultValue
      );
      if (styleValue === null) {
        styleValue = nodeStyleValue;
      } else if (styleValue !== nodeStyleValue) {
        styleValue = "";
        break;
      }
    }
  }
  return styleValue === null ? defaultValue : styleValue;
}
function INTERNAL_$isBlock2(node) {
  if ($isDecoratorNode(node)) {
    return false;
  }
  if (!$isElementNode(node) || $isRootOrShadowRoot(node)) {
    return false;
  }
  const firstChild = node.getFirstChild();
  const isLeafElement = firstChild === null || $isLineBreakNode(firstChild) || $isTextNode(firstChild) || firstChild.isInline();
  return !node.isInline() && node.canBeEmpty() !== false && isLeafElement;
}
function $getAncestor2(node, predicate) {
  let parent = node;
  while (parent !== null && parent.getParent() !== null && !predicate(parent)) {
    parent = parent.getParentOrThrow();
  }
  return predicate(parent) ? parent : null;
}

// resources/js/wysiwyg/lexical/utils/index.ts
function addClassNamesToElement(element, ...classNames) {
  const classesToAdd = normalizeClassNames(...classNames);
  if (classesToAdd.length > 0) {
    element.classList.add(...classesToAdd);
  }
}
function removeClassNamesFromElement(element, ...classNames) {
  const classesToRemove = normalizeClassNames(...classNames);
  if (classesToRemove.length > 0) {
    element.classList.remove(...classesToRemove);
  }
}
function $getNearestNodeOfType(node, klass) {
  let parent = node;
  while (parent != null) {
    if (parent instanceof klass) {
      return parent;
    }
    parent = parent.getParent();
  }
  return null;
}
function $getNearestBlockElementAncestorOrThrow(startNode) {
  const blockNode = $findMatchingParent(
    startNode,
    (node) => $isElementNode(node) && !node.isInline()
  );
  if (!$isElementNode(blockNode)) {
    invariant(
      false,
      "Expected node %s to have closest block element node.",
      startNode.__key
    );
  }
  return blockNode;
}
var $findMatchingParent = (startingNode, findFn) => {
  let curr = startingNode;
  while (curr !== $getRoot() && curr != null) {
    if (findFn(curr)) {
      return curr;
    }
    curr = curr.getParent();
  }
  return null;
};
function objectKlassEquals(object, objectClass) {
  return object !== null ? Object.getPrototypeOf(object).constructor.name === objectClass.name : false;
}

// resources/js/wysiwyg/lexical/html/index.ts
function $generateNodesFromDOM(editor, dom) {
  const elements = dom.body ? dom.body.childNodes : [];
  let lexicalNodes = [];
  const allArtificialNodes = [];
  for (let i = 0; i < elements.length; i++) {
    const element = elements[i];
    if (!IGNORE_TAGS.has(element.nodeName)) {
      const lexicalNode = $createNodesFromDOM(
        element,
        editor,
        allArtificialNodes,
        false
      );
      if (lexicalNode !== null) {
        lexicalNodes = lexicalNodes.concat(lexicalNode);
      }
    }
  }
  $unwrapArtificalNodes(allArtificialNodes);
  return lexicalNodes;
}
function $generateHtmlFromNodes(editor, selection) {
  if (typeof document === "undefined" || typeof window === "undefined" && typeof global.window === "undefined") {
    throw new Error(
      "To use $generateHtmlFromNodes in headless mode please initialize a headless browser implementation such as JSDom before calling this function."
    );
  }
  const container = document.createElement("div");
  const root = $getRoot();
  const topLevelChildren = root.getChildren();
  for (let i = 0; i < topLevelChildren.length; i++) {
    const topLevelNode = topLevelChildren[i];
    $appendNodesToHTML(editor, topLevelNode, container, selection);
  }
  const nodeCode = [];
  for (const node of container.childNodes) {
    if ("outerHTML" in node) {
      nodeCode.push(node.outerHTML);
    } else {
      const wrap = document.createElement("div");
      wrap.appendChild(node.cloneNode(true));
      nodeCode.push(wrap.innerHTML);
    }
  }
  return nodeCode.join("\n");
}
function $appendNodesToHTML(editor, currentNode, parentElement, selection = null) {
  let shouldInclude = selection !== null ? currentNode.isSelected(selection) : true;
  const shouldExclude = $isElementNode(currentNode) && currentNode.excludeFromCopy("html");
  let target = currentNode;
  if (selection !== null) {
    let clone = $cloneWithProperties(currentNode);
    clone = $isTextNode(clone) && selection !== null ? $sliceSelectedTextNodeContent(selection, clone) : clone;
    target = clone;
  }
  const children = $isElementNode(target) ? target.getChildren() : [];
  const registeredNode = editor._nodes.get(target.getType());
  let exportOutput;
  if (registeredNode && registeredNode.exportDOM !== void 0) {
    exportOutput = registeredNode.exportDOM(editor, target);
  } else {
    exportOutput = target.exportDOM(editor);
  }
  const { element, after } = exportOutput;
  if (!element) {
    return false;
  }
  const fragment = document.createDocumentFragment();
  for (let i = 0; i < children.length; i++) {
    const childNode = children[i];
    const shouldIncludeChild = $appendNodesToHTML(
      editor,
      childNode,
      fragment,
      selection
    );
    if (!shouldInclude && $isElementNode(currentNode) && shouldIncludeChild && currentNode.extractWithChild(childNode, selection, "html")) {
      shouldInclude = true;
    }
  }
  if (shouldInclude && !shouldExclude) {
    if (isHTMLElement(element)) {
      element.append(fragment);
    }
    parentElement.append(element);
    if (after) {
      const newElement = after.call(target, element);
      if (newElement) {
        element.replaceWith(newElement);
      }
    }
  } else {
    parentElement.append(fragment);
  }
  return shouldInclude;
}
function getConversionFunction(domNode, editor) {
  const { nodeName } = domNode;
  const cachedConversions = editor._htmlConversions.get(nodeName.toLowerCase());
  let currentConversion = null;
  if (cachedConversions !== void 0) {
    for (const cachedConversion of cachedConversions) {
      const domConversion = cachedConversion(domNode);
      if (domConversion !== null && (currentConversion === null || (currentConversion.priority || 0) < (domConversion.priority || 0))) {
        currentConversion = domConversion;
      }
    }
  }
  return currentConversion !== null ? currentConversion.conversion : null;
}
var IGNORE_TAGS = /* @__PURE__ */ new Set(["STYLE", "SCRIPT"]);
function $createNodesFromDOM(node, editor, allArtificialNodes, hasBlockAncestorLexicalNode, forChildMap = /* @__PURE__ */ new Map(), parentLexicalNode) {
  let lexicalNodes = [];
  if (IGNORE_TAGS.has(node.nodeName)) {
    return lexicalNodes;
  }
  let currentLexicalNode = null;
  const transformFunction = getConversionFunction(node, editor);
  const transformOutput = transformFunction ? transformFunction(node) : null;
  let postTransform = null;
  if (transformOutput !== null) {
    postTransform = transformOutput.after;
    const transformNodes = transformOutput.node;
    if (transformNodes === "ignore") {
      return lexicalNodes;
    }
    currentLexicalNode = Array.isArray(transformNodes) ? transformNodes[transformNodes.length - 1] : transformNodes;
    if (currentLexicalNode !== null) {
      for (const [, forChildFunction] of forChildMap) {
        currentLexicalNode = forChildFunction(
          currentLexicalNode,
          parentLexicalNode
        );
        if (!currentLexicalNode) {
          break;
        }
      }
      if (currentLexicalNode) {
        lexicalNodes.push(
          ...Array.isArray(transformNodes) ? transformNodes : [currentLexicalNode]
        );
      }
    }
    if (transformOutput.forChild != null) {
      forChildMap.set(node.nodeName, transformOutput.forChild);
    }
  }
  const children = node.childNodes;
  let childLexicalNodes = [];
  const hasBlockAncestorLexicalNodeForChildren = currentLexicalNode != null && $isRootOrShadowRoot(currentLexicalNode) ? false : currentLexicalNode != null && $isBlockElementNode(currentLexicalNode) || hasBlockAncestorLexicalNode;
  for (let i = 0; i < children.length; i++) {
    childLexicalNodes.push(
      ...$createNodesFromDOM(
        children[i],
        editor,
        allArtificialNodes,
        hasBlockAncestorLexicalNodeForChildren,
        new Map(forChildMap),
        currentLexicalNode
      )
    );
  }
  if (postTransform != null) {
    childLexicalNodes = postTransform(childLexicalNodes);
  }
  if (isBlockDomNode(node)) {
    if (!hasBlockAncestorLexicalNodeForChildren) {
      childLexicalNodes = wrapContinuousInlines(
        node,
        childLexicalNodes,
        $createParagraphNode
      );
    } else {
      childLexicalNodes = wrapContinuousInlines(node, childLexicalNodes, () => {
        const artificialNode = new ArtificialNode__DO_NOT_USE();
        allArtificialNodes.push(artificialNode);
        return artificialNode;
      });
    }
  }
  if (currentLexicalNode == null) {
    if (childLexicalNodes.length > 0) {
      lexicalNodes = lexicalNodes.concat(childLexicalNodes);
    } else {
      if (isBlockDomNode(node) && isDomNodeBetweenTwoInlineNodes(node)) {
        lexicalNodes = lexicalNodes.concat($createLineBreakNode());
      }
    }
  } else {
    if ($isElementNode(currentLexicalNode)) {
      currentLexicalNode.append(...childLexicalNodes);
    }
  }
  return lexicalNodes;
}
function wrapContinuousInlines(domNode, nodes, createWrapperFn) {
  const out = [];
  let continuousInlines = [];
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    if ($isBlockElementNode(node)) {
      out.push(node);
    } else {
      continuousInlines.push(node);
      if (i === nodes.length - 1 || i < nodes.length - 1 && $isBlockElementNode(nodes[i + 1])) {
        const wrapper = createWrapperFn();
        wrapper.append(...continuousInlines);
        out.push(wrapper);
        continuousInlines = [];
      }
    }
  }
  return out;
}
function $unwrapArtificalNodes(allArtificialNodes) {
  for (const node of allArtificialNodes) {
    if (node.getNextSibling() instanceof ArtificialNode__DO_NOT_USE) {
      node.insertAfter($createLineBreakNode());
    }
  }
  for (const node of allArtificialNodes) {
    const children = node.getChildren();
    for (const child of children) {
      node.insertBefore(child);
    }
    node.remove();
  }
}
function isDomNodeBetweenTwoInlineNodes(node) {
  if (node.nextSibling == null || node.previousSibling == null) {
    return false;
  }
  return isInlineDomNode(node.nextSibling) && isInlineDomNode(node.previousSibling);
}

// resources/js/wysiwyg/utils/dom.ts
function el(tag, attrs = {}, children = []) {
  const el3 = document.createElement(tag);
  const attrKeys = Object.keys(attrs);
  for (const attr of attrKeys) {
    if (attrs[attr] !== null) {
      el3.setAttribute(attr, attrs[attr]);
    }
  }
  for (const child of children) {
    if (typeof child === "string") {
      el3.append(document.createTextNode(child));
    } else {
      el3.append(child);
    }
  }
  return el3;
}
function htmlToDom(html) {
  const parser = new DOMParser();
  return parser.parseFromString(html, "text/html");
}
function formatSizeValue(size, defaultSuffix = "px") {
  if (typeof size === "number" || /^-?\d+$/.test(size)) {
    return `${size}${defaultSuffix}`;
  }
  return size;
}
function sizeToPixels(size) {
  if (/^-?\d+$/.test(size)) {
    return Number(size);
  }
  if (/^-?\d+\.\d+$/.test(size)) {
    return Math.round(Number(size));
  }
  if (/^-?\d+px\s*$/.test(size)) {
    return Number(size.trim().replace("px", ""));
  }
  return 0;
}
function extractStyleMapFromElement(element) {
  const styleText = element.getAttribute("style") || "";
  return styleStringToStyleMap(styleText);
}
function styleStringToStyleMap(styleText) {
  const map = /* @__PURE__ */ new Map();
  const rules = styleText.split(";");
  for (const rule of rules) {
    const [name, value] = rule.split(":");
    if (!name || !value) {
      continue;
    }
    map.set(name.trim().toLowerCase(), value.trim());
  }
  return map;
}
function styleMapToStyleString(map) {
  const parts = [];
  for (const [style, value] of map.entries()) {
    parts.push(`${style}:${value}`);
  }
  return parts.join(";");
}
function setOrRemoveAttribute(element, name, value) {
  if (value) {
    element.setAttribute(name, value);
  } else {
    element.removeAttribute(name);
  }
}

// resources/js/wysiwyg/utils/nodes.ts
function wrapTextNodes(nodes) {
  return nodes.map((node) => {
    if ($isTextNode(node)) {
      const paragraph2 = $createParagraphNode();
      paragraph2.append(node);
      return paragraph2;
    }
    return node;
  });
}
function $htmlToBlockNodes(editor, html) {
  const dom = htmlToDom(html);
  const nodes = $generateNodesFromDOM(editor, dom);
  return wrapTextNodes(nodes);
}
function $getParentOfType(node, matcher) {
  for (const parent of node.getParents()) {
    if (matcher(parent)) {
      return parent;
    }
  }
  return null;
}
function $getAllNodesOfType(matcher, root) {
  if (!root) {
    root = $getRoot();
  }
  const matches = [];
  for (const child of root.getChildren()) {
    if (matcher(child)) {
      matches.push(child);
    }
    if ($isElementNode(child)) {
      matches.push(...$getAllNodesOfType(matcher, child));
    }
  }
  return matches;
}
function $getNearestBlockNodeForCoords(editor, x, y) {
  const rootNodes = $getRoot().getChildren();
  for (const node of rootNodes) {
    const nodeDom = editor.getElementByKey(node.__key);
    if (!nodeDom) {
      continue;
    }
    const bounds = nodeDom.getBoundingClientRect();
    if (y <= bounds.bottom) {
      return node;
    }
  }
  return null;
}
function $getNearestNodeBlockParent(node) {
  const isBlockNode = (node2) => {
    return ($isElementNode(node2) || $isDecoratorNode(node2)) && !node2.isInline() && !$isRootNode(node2);
  };
  if (isBlockNode(node)) {
    return node;
  }
  return $findMatchingParent(node, isBlockNode);
}
function $sortNodes(nodes) {
  const idChain = [];
  const addIds = (n) => {
    for (const child of n.getChildren()) {
      idChain.push(child.getKey());
      if ($isElementNode(child)) {
        addIds(child);
      }
    }
  };
  const root = $getRoot();
  addIds(root);
  const sorted = Array.from(nodes);
  sorted.sort((a, b) => {
    const aIndex = idChain.indexOf(a.getKey());
    const bIndex = idChain.indexOf(b.getKey());
    return aIndex - bIndex;
  });
  return sorted;
}
function $selectOrCreateAdjacent(node, after) {
  const nearestBlock = $getNearestNodeBlockParent(node) || node;
  let target = after ? nearestBlock.getNextSibling() : nearestBlock.getPreviousSibling();
  if (!target) {
    target = $createParagraphNode();
    if (after) {
      nearestBlock.insertAfter(target);
    } else {
      nearestBlock.insertBefore(target);
    }
  }
  return after ? target.selectStart() : target.selectEnd();
}
function nodeHasAlignment(node) {
  return "__alignment" in node;
}
function nodeHasInset(node) {
  return "__inset" in node;
}

// resources/js/wysiwyg/utils/selection.ts
var lastSelectionByEditor = /* @__PURE__ */ new WeakMap();
function getLastSelection2(editor) {
  return lastSelectionByEditor.get(editor) || null;
}
function setLastSelection(editor, selection) {
  lastSelectionByEditor.set(editor, selection);
}
function $selectionContainsNodeType(selection, matcher) {
  return $getNodeFromSelection(selection, matcher) !== null;
}
function $getNodeFromSelection(selection, matcher) {
  if (!selection) {
    return null;
  }
  for (const node of selection.getNodes()) {
    if (matcher(node)) {
      return node;
    }
    const matchedParent = $getParentOfType(node, matcher);
    if (matchedParent) {
      return matchedParent;
    }
  }
  return null;
}
function $getTextNodeFromSelection(selection) {
  return $getNodeFromSelection(selection, $isTextNode);
}
function $selectionContainsTextFormat(selection, format) {
  if (!selection) {
    return false;
  }
  const nodes = selection.getNodes();
  for (const node of nodes) {
    if ($isTextNode(node) && node.hasFormat(format)) {
      return true;
    }
  }
  if (nodes.length === 1 && $isParagraphNode(nodes[0]) && nodes[0].hasTextFormat(format)) {
    return true;
  }
  return false;
}
function $toggleSelectionBlockNodeType(matcher, creator) {
  const selection = $getSelection();
  const blockElement = selection ? $getNearestBlockElementAncestorOrThrow(selection.getNodes()[0]) : null;
  if (selection && matcher(blockElement)) {
    $setBlocksType(selection, $createParagraphNode);
  } else {
    $setBlocksType(selection, creator);
  }
}
function $insertNewBlockNodeAtSelection(node, insertAfter = true) {
  $insertNewBlockNodesAtSelection([node], insertAfter);
}
function $insertNewBlockNodesAtSelection(nodes, insertAfter = true) {
  const selectionNodes = $getSelection()?.getNodes() || [];
  const blockElement = selectionNodes.length > 0 ? $getNearestNodeBlockParent(selectionNodes[0]) : null;
  if (blockElement) {
    if (insertAfter) {
      for (let i = nodes.length - 1; i >= 0; i--) {
        blockElement.insertAfter(nodes[i]);
      }
    } else {
      for (const node of nodes) {
        blockElement.insertBefore(node);
      }
    }
  } else {
    $getRoot().append(...nodes);
  }
}
function $selectSingleNode(node) {
  const nodeSelection = $createNodeSelection();
  nodeSelection.add(node.getKey());
  $setSelection(nodeSelection);
}
function getFirstTextNodeInNodes(nodes) {
  for (const node of nodes) {
    if ($isTextNode(node)) {
      return node;
    }
    if ($isElementNode(node)) {
      const children = node.getChildren();
      const textNode = getFirstTextNodeInNodes(children);
      if (textNode !== null) {
        return textNode;
      }
    }
  }
  return null;
}
function getLastTextNodeInNodes(nodes) {
  const revNodes = [...nodes].reverse();
  for (const node of revNodes) {
    if ($isTextNode(node)) {
      return node;
    }
    if ($isElementNode(node)) {
      const children = [...node.getChildren()].reverse();
      const textNode = getLastTextNodeInNodes(children);
      if (textNode !== null) {
        return textNode;
      }
    }
  }
  return null;
}
function $selectNodes(nodes) {
  if (nodes.length === 0) {
    return;
  }
  const selection = $createRangeSelection();
  const firstText = getFirstTextNodeInNodes(nodes);
  const lastText = getLastTextNodeInNodes(nodes);
  if (firstText && lastText) {
    selection.setTextNodeRange(firstText, 0, lastText, lastText.getTextContentSize() || 0);
    $setSelection(selection);
  }
}
function $toggleSelection(editor) {
  const lastSelection = getLastSelection2(editor);
  if (lastSelection) {
    window.requestAnimationFrame(() => {
      editor.update(() => {
        $setSelection(lastSelection.clone());
      });
    });
  }
}
function $selectionContainsNode(selection, node) {
  if (!selection) {
    return false;
  }
  const key = node.getKey();
  for (const node2 of selection.getNodes()) {
    if (node2.getKey() === key) {
      return true;
    }
  }
  return false;
}
function $selectionContainsAlignment(selection, alignment) {
  const nodes = [
    ...selection?.getNodes() || [],
    ...$getBlockElementNodesInSelection(selection)
  ];
  for (const node of nodes) {
    if (nodeHasAlignment(node) && node.getAlignment() === alignment) {
      return true;
    }
  }
  return false;
}
function $selectionContainsDirection(selection, direction) {
  const nodes = [
    ...selection?.getNodes() || [],
    ...$getBlockElementNodesInSelection(selection)
  ];
  for (const node of nodes) {
    if ($isBlockElementNode(node) && node.getDirection() === direction) {
      return true;
    }
  }
  return false;
}
function $getBlockElementNodesInSelection(selection) {
  if (!selection) {
    return [];
  }
  const blockNodes = /* @__PURE__ */ new Map();
  for (const node of selection.getNodes()) {
    const blockElement = $getNearestNodeBlockParent(node);
    if ($isElementNode(blockElement)) {
      blockNodes.set(blockElement.getKey(), blockElement);
    }
  }
  return Array.from(blockNodes.values());
}

// resources/js/wysiwyg/lexical/core/LexicalSelection.ts
var Point2 = class {
  constructor(key, offset, type) {
    __publicField(this, "key");
    __publicField(this, "offset");
    __publicField(this, "type");
    __publicField(this, "_selection");
    this._selection = null;
    this.key = key;
    this.offset = offset;
    this.type = type;
  }
  is(point) {
    return this.key === point.key && this.offset === point.offset && this.type === point.type;
  }
  isBefore(b) {
    let aNode = this.getNode();
    let bNode = b.getNode();
    const aOffset = this.offset;
    const bOffset = b.offset;
    if ($isElementNode(aNode)) {
      const aNodeDescendant = aNode.getDescendantByIndex(aOffset);
      aNode = aNodeDescendant != null ? aNodeDescendant : aNode;
    }
    if ($isElementNode(bNode)) {
      const bNodeDescendant = bNode.getDescendantByIndex(bOffset);
      bNode = bNodeDescendant != null ? bNodeDescendant : bNode;
    }
    if (aNode === bNode) {
      return aOffset < bOffset;
    }
    return aNode.isBefore(bNode);
  }
  getNode() {
    const key = this.key;
    const node = $getNodeByKey(key);
    if (node === null) {
      invariant(false, "Point.getNode: node not found");
    }
    return node;
  }
  set(key, offset, type) {
    const selection = this._selection;
    const oldKey = this.key;
    this.key = key;
    this.offset = offset;
    this.type = type;
    if (!isCurrentlyReadOnlyMode()) {
      if ($getCompositionKey() === oldKey) {
        $setCompositionKey(key);
      }
      if (selection !== null) {
        selection.setCachedNodes(null);
        selection.dirty = true;
      }
    }
  }
};
function $createPoint(key, offset, type) {
  return new Point2(key, offset, type);
}
function selectPointOnNode(point, node) {
  let key = node.__key;
  let offset = point.offset;
  let type = "element";
  if ($isTextNode(node)) {
    type = "text";
    const textContentLength = node.getTextContentSize();
    if (offset > textContentLength) {
      offset = textContentLength;
    }
  } else if (!$isElementNode(node)) {
    const nextSibling = node.getNextSibling();
    if ($isTextNode(nextSibling)) {
      key = nextSibling.__key;
      offset = 0;
      type = "text";
    } else {
      const parentNode = node.getParent();
      if (parentNode) {
        key = parentNode.__key;
        offset = node.getIndexWithinParent() + 1;
      }
    }
  }
  point.set(key, offset, type);
}
function $moveSelectionPointToEnd(point, node) {
  if ($isElementNode(node)) {
    const lastNode = node.getLastDescendant();
    if ($isElementNode(lastNode) || $isTextNode(lastNode)) {
      selectPointOnNode(point, lastNode);
    } else {
      selectPointOnNode(point, node);
    }
  } else {
    selectPointOnNode(point, node);
  }
}
function $transferStartingElementPointToTextPoint(start, end, format, style) {
  const element = start.getNode();
  const placementNode = element.getChildAtIndex(start.offset);
  const textNode = $createTextNode();
  const target = $isRootNode(element) ? $createParagraphNode().append(textNode) : textNode;
  textNode.setFormat(format);
  textNode.setStyle(style);
  if (placementNode === null) {
    element.append(target);
  } else {
    placementNode.insertBefore(target);
  }
  if (start.is(end)) {
    end.set(textNode.__key, 0, "text");
  }
  start.set(textNode.__key, 0, "text");
}
function $setPointValues(point, key, offset, type) {
  point.key = key;
  point.offset = offset;
  point.type = type;
}
var NodeSelection = class _NodeSelection {
  constructor(objects) {
    __publicField(this, "_nodes");
    __publicField(this, "_cachedNodes");
    __publicField(this, "dirty");
    this._cachedNodes = null;
    this._nodes = objects;
    this.dirty = false;
  }
  getCachedNodes() {
    return this._cachedNodes;
  }
  setCachedNodes(nodes) {
    this._cachedNodes = nodes;
  }
  is(selection) {
    if (!$isNodeSelection(selection)) {
      return false;
    }
    const a = this._nodes;
    const b = selection._nodes;
    return a.size === b.size && Array.from(a).every((key) => b.has(key));
  }
  isCollapsed() {
    return false;
  }
  isBackward() {
    return false;
  }
  getStartEndPoints() {
    return null;
  }
  add(key) {
    this.dirty = true;
    this._nodes.add(key);
    this._cachedNodes = null;
  }
  delete(key) {
    this.dirty = true;
    this._nodes.delete(key);
    this._cachedNodes = null;
  }
  clear() {
    this.dirty = true;
    this._nodes.clear();
    this._cachedNodes = null;
  }
  has(key) {
    return this._nodes.has(key);
  }
  clone() {
    return new _NodeSelection(new Set(this._nodes));
  }
  extract() {
    return this.getNodes();
  }
  insertRawText(text) {
  }
  insertText() {
  }
  insertNodes(nodes) {
    const selectedNodes = this.getNodes();
    const selectedNodesLength = selectedNodes.length;
    const lastSelectedNode = selectedNodes[selectedNodesLength - 1];
    let selectionAtEnd;
    if ($isTextNode(lastSelectedNode)) {
      selectionAtEnd = lastSelectedNode.select();
    } else {
      const index = lastSelectedNode.getIndexWithinParent() + 1;
      selectionAtEnd = lastSelectedNode.getParentOrThrow().select(index, index);
    }
    selectionAtEnd.insertNodes(nodes);
    for (let i = 0; i < selectedNodesLength; i++) {
      selectedNodes[i].remove();
    }
  }
  getNodes() {
    const cachedNodes = this._cachedNodes;
    if (cachedNodes !== null) {
      return cachedNodes;
    }
    const objects = this._nodes;
    const nodes = [];
    for (const object of objects) {
      const node = $getNodeByKey(object);
      if (node !== null) {
        nodes.push(node);
      }
    }
    if (!isCurrentlyReadOnlyMode()) {
      this._cachedNodes = nodes;
    }
    return nodes;
  }
  getTextContent() {
    const nodes = this.getNodes();
    let textContent = "";
    for (let i = 0; i < nodes.length; i++) {
      textContent += nodes[i].getTextContent();
    }
    return textContent;
  }
};
function $isRangeSelection(x) {
  return x instanceof RangeSelection4;
}
var RangeSelection4 = class _RangeSelection {
  constructor(anchor, focus, format, style) {
    __publicField(this, "format");
    __publicField(this, "style");
    __publicField(this, "anchor");
    __publicField(this, "focus");
    __publicField(this, "_cachedNodes");
    __publicField(this, "dirty");
    this.anchor = anchor;
    this.focus = focus;
    anchor._selection = this;
    focus._selection = this;
    this._cachedNodes = null;
    this.format = format;
    this.style = style;
    this.dirty = false;
  }
  getCachedNodes() {
    return this._cachedNodes;
  }
  setCachedNodes(nodes) {
    this._cachedNodes = nodes;
  }
  /**
   * Used to check if the provided selections is equal to this one by value,
   * inluding anchor, focus, format, and style properties.
   * @param selection - the Selection to compare this one to.
   * @returns true if the Selections are equal, false otherwise.
   */
  is(selection) {
    if (!$isRangeSelection(selection)) {
      return false;
    }
    return this.anchor.is(selection.anchor) && this.focus.is(selection.focus) && this.format === selection.format && this.style === selection.style;
  }
  /**
   * Returns whether the Selection is "collapsed", meaning the anchor and focus are
   * the same node and have the same offset.
   *
   * @returns true if the Selection is collapsed, false otherwise.
   */
  isCollapsed() {
    return this.anchor.is(this.focus);
  }
  /**
   * Gets all the nodes in the Selection. Uses caching to make it generally suitable
   * for use in hot paths.
   *
   * @returns an Array containing all the nodes in the Selection
   */
  getNodes() {
    const cachedNodes = this._cachedNodes;
    if (cachedNodes !== null) {
      return cachedNodes;
    }
    const anchor = this.anchor;
    const focus = this.focus;
    const isBefore = anchor.isBefore(focus);
    const firstPoint = isBefore ? anchor : focus;
    const lastPoint = isBefore ? focus : anchor;
    let firstNode = firstPoint.getNode();
    let lastNode = lastPoint.getNode();
    const startOffset = firstPoint.offset;
    const endOffset = lastPoint.offset;
    if ($isElementNode(firstNode)) {
      const firstNodeDescendant = firstNode.getDescendantByIndex(startOffset);
      firstNode = firstNodeDescendant != null ? firstNodeDescendant : firstNode;
    }
    if ($isElementNode(lastNode)) {
      let lastNodeDescendant = lastNode.getDescendantByIndex(endOffset);
      if (lastNodeDescendant !== null && lastNodeDescendant !== firstNode && lastNode.getChildAtIndex(endOffset) === lastNodeDescendant) {
        lastNodeDescendant = lastNodeDescendant.getPreviousSibling();
      }
      lastNode = lastNodeDescendant != null ? lastNodeDescendant : lastNode;
    }
    let nodes;
    if (firstNode.is(lastNode)) {
      if ($isElementNode(firstNode) && firstNode.getChildrenSize() > 0) {
        nodes = [];
      } else {
        nodes = [firstNode];
      }
    } else {
      nodes = firstNode.getNodesBetween(lastNode);
    }
    if (!isCurrentlyReadOnlyMode()) {
      this._cachedNodes = nodes;
    }
    return nodes;
  }
  /**
   * Sets this Selection to be of type "text" at the provided anchor and focus values.
   *
   * @param anchorNode - the anchor node to set on the Selection
   * @param anchorOffset - the offset to set on the Selection
   * @param focusNode - the focus node to set on the Selection
   * @param focusOffset - the focus offset to set on the Selection
   */
  setTextNodeRange(anchorNode, anchorOffset, focusNode, focusOffset) {
    $setPointValues(this.anchor, anchorNode.__key, anchorOffset, "text");
    $setPointValues(this.focus, focusNode.__key, focusOffset, "text");
    this._cachedNodes = null;
    this.dirty = true;
  }
  /**
   * Gets the (plain) text content of all the nodes in the selection.
   *
   * @returns a string representing the text content of all the nodes in the Selection
   */
  getTextContent() {
    const nodes = this.getNodes();
    if (nodes.length === 0) {
      return "";
    }
    const firstNode = nodes[0];
    const lastNode = nodes[nodes.length - 1];
    const anchor = this.anchor;
    const focus = this.focus;
    const isBefore = anchor.isBefore(focus);
    const [anchorOffset, focusOffset] = $getCharacterOffsets(this);
    let textContent = "";
    let prevWasElement = true;
    for (let i = 0; i < nodes.length; i++) {
      const node = nodes[i];
      if ($isElementNode(node) && !node.isInline()) {
        if (!prevWasElement) {
          textContent += "\n";
        }
        if (node.isEmpty()) {
          prevWasElement = false;
        } else {
          prevWasElement = true;
        }
      } else {
        prevWasElement = false;
        if ($isTextNode(node)) {
          let text = node.getTextContent();
          if (node === firstNode) {
            if (node === lastNode) {
              if (anchor.type !== "element" || focus.type !== "element" || focus.offset === anchor.offset) {
                text = anchorOffset < focusOffset ? text.slice(anchorOffset, focusOffset) : text.slice(focusOffset, anchorOffset);
              }
            } else {
              text = isBefore ? text.slice(anchorOffset) : text.slice(focusOffset);
            }
          } else if (node === lastNode) {
            text = isBefore ? text.slice(0, focusOffset) : text.slice(0, anchorOffset);
          }
          textContent += text;
        } else if (($isDecoratorNode(node) || $isLineBreakNode(node)) && (node !== lastNode || !this.isCollapsed())) {
          textContent += node.getTextContent();
        }
      }
    }
    return textContent;
  }
  /**
   * Attempts to map a DOM selection range onto this Lexical Selection,
   * setting the anchor, focus, and type accordingly
   *
   * @param range a DOM Selection range conforming to the StaticRange interface.
   */
  applyDOMRange(range) {
    const editor = getActiveEditor();
    const currentEditorState = editor.getEditorState();
    const lastSelection = currentEditorState._selection;
    const resolvedSelectionPoints = $internalResolveSelectionPoints(
      range.startContainer,
      range.startOffset,
      range.endContainer,
      range.endOffset,
      editor,
      lastSelection
    );
    if (resolvedSelectionPoints === null) {
      return;
    }
    const [anchorPoint, focusPoint] = resolvedSelectionPoints;
    $setPointValues(
      this.anchor,
      anchorPoint.key,
      anchorPoint.offset,
      anchorPoint.type
    );
    $setPointValues(
      this.focus,
      focusPoint.key,
      focusPoint.offset,
      focusPoint.type
    );
    this._cachedNodes = null;
  }
  /**
   * Creates a new RangeSelection, copying over all the property values from this one.
   *
   * @returns a new RangeSelection with the same property values as this one.
   */
  clone() {
    const anchor = this.anchor;
    const focus = this.focus;
    const selection = new _RangeSelection(
      $createPoint(anchor.key, anchor.offset, anchor.type),
      $createPoint(focus.key, focus.offset, focus.type),
      this.format,
      this.style
    );
    return selection;
  }
  /**
   * Toggles the provided format on all the TextNodes in the Selection.
   *
   * @param format a string TextFormatType to toggle on the TextNodes in the selection
   */
  toggleFormat(format) {
    this.format = toggleTextFormatType(this.format, format, null);
    this.dirty = true;
  }
  /**
   * Sets the value of the style property on the Selection
   *
   * @param style - the style to set at the value of the style property.
   */
  setStyle(style) {
    this.style = style;
    this.dirty = true;
  }
  /**
   * Returns whether the provided TextFormatType is present on the Selection. This will be true if any node in the Selection
   * has the specified format.
   *
   * @param type the TextFormatType to check for.
   * @returns true if the provided format is currently toggled on on the Selection, false otherwise.
   */
  hasFormat(type) {
    const formatFlag = TEXT_TYPE_TO_FORMAT[type];
    return (this.format & formatFlag) !== 0;
  }
  /**
   * Attempts to insert the provided text into the EditorState at the current Selection.
   * converts tabs, newlines, and carriage returns into LexicalNodes.
   *
   * @param text the text to insert into the Selection
   */
  insertRawText(text) {
    const parts = text.split(/(\r?\n|\t)/);
    const nodes = [];
    const length = parts.length;
    for (let i = 0; i < length; i++) {
      const part = parts[i];
      if (part === "\n" || part === "\r\n") {
        nodes.push($createLineBreakNode());
      } else if (part === "	") {
        nodes.push($createTabNode());
      } else {
        nodes.push($createTextNode(part));
      }
    }
    this.insertNodes(nodes);
  }
  /**
   * Attempts to insert the provided text into the EditorState at the current Selection as a new
   * Lexical TextNode, according to a series of insertion heuristics based on the selection type and position.
   *
   * @param text the text to insert into the Selection
   */
  insertText(text) {
    const anchor = this.anchor;
    const focus = this.focus;
    const format = this.format;
    const style = this.style;
    let firstPoint = anchor;
    let endPoint = focus;
    if (!this.isCollapsed() && focus.isBefore(anchor)) {
      firstPoint = focus;
      endPoint = anchor;
    }
    if (firstPoint.type === "element") {
      $transferStartingElementPointToTextPoint(
        firstPoint,
        endPoint,
        format,
        style
      );
    }
    const startOffset = firstPoint.offset;
    let endOffset = endPoint.offset;
    const selectedNodes = this.getNodes();
    const selectedNodesLength = selectedNodes.length;
    let firstNode = selectedNodes[0];
    if (!$isTextNode(firstNode)) {
      invariant(false, "insertText: first node is not a text node");
    }
    const firstNodeText = firstNode.getTextContent();
    const firstNodeTextLength = firstNodeText.length;
    const firstNodeParent = firstNode.getParentOrThrow();
    const lastIndex = selectedNodesLength - 1;
    let lastNode = selectedNodes[lastIndex];
    if (selectedNodesLength === 1 && endPoint.type === "element") {
      endOffset = firstNodeTextLength;
      endPoint.set(firstPoint.key, endOffset, "text");
    }
    if (this.isCollapsed() && startOffset === firstNodeTextLength && (firstNode.isSegmented() || firstNode.isToken() || !firstNode.canInsertTextAfter() || !firstNodeParent.canInsertTextAfter() && firstNode.getNextSibling() === null)) {
      let nextSibling = firstNode.getNextSibling();
      if (!$isTextNode(nextSibling) || !nextSibling.canInsertTextBefore() || $isTokenOrSegmented(nextSibling)) {
        nextSibling = $createTextNode();
        nextSibling.setFormat(format);
        nextSibling.setStyle(style);
        if (!firstNodeParent.canInsertTextAfter()) {
          firstNodeParent.insertAfter(nextSibling);
        } else {
          firstNode.insertAfter(nextSibling);
        }
      }
      nextSibling.select(0, 0);
      firstNode = nextSibling;
      if (text !== "") {
        this.insertText(text);
        return;
      }
    } else if (this.isCollapsed() && startOffset === 0 && (firstNode.isSegmented() || firstNode.isToken() || !firstNode.canInsertTextBefore() || !firstNodeParent.canInsertTextBefore() && firstNode.getPreviousSibling() === null)) {
      let prevSibling = firstNode.getPreviousSibling();
      if (!$isTextNode(prevSibling) || $isTokenOrSegmented(prevSibling)) {
        prevSibling = $createTextNode();
        prevSibling.setFormat(format);
        if (!firstNodeParent.canInsertTextBefore()) {
          firstNodeParent.insertBefore(prevSibling);
        } else {
          firstNode.insertBefore(prevSibling);
        }
      }
      prevSibling.select();
      firstNode = prevSibling;
      if (text !== "") {
        this.insertText(text);
        return;
      }
    } else if (firstNode.isSegmented() && startOffset !== firstNodeTextLength) {
      const textNode = $createTextNode(firstNode.getTextContent());
      textNode.setFormat(format);
      firstNode.replace(textNode);
      firstNode = textNode;
    } else if (!this.isCollapsed() && text !== "") {
      const lastNodeParent = lastNode.getParent();
      if (!firstNodeParent.canInsertTextBefore() || !firstNodeParent.canInsertTextAfter() || $isElementNode(lastNodeParent) && (!lastNodeParent.canInsertTextBefore() || !lastNodeParent.canInsertTextAfter())) {
        this.insertText("");
        $normalizeSelectionPointsForBoundaries(this.anchor, this.focus, null);
        this.insertText(text);
        return;
      }
    }
    if (selectedNodesLength === 1) {
      if (firstNode.isToken()) {
        const textNode = $createTextNode(text);
        textNode.select();
        firstNode.replace(textNode);
        return;
      }
      const firstNodeFormat = firstNode.getFormat();
      const firstNodeStyle = firstNode.getStyle();
      if (startOffset === endOffset && (firstNodeFormat !== format || firstNodeStyle !== style)) {
        if (firstNode.getTextContent() === "") {
          firstNode.setFormat(format);
          firstNode.setStyle(style);
        } else {
          const textNode = $createTextNode(text);
          textNode.setFormat(format);
          textNode.setStyle(style);
          textNode.select();
          if (startOffset === 0) {
            firstNode.insertBefore(textNode, false);
          } else {
            const [targetNode] = firstNode.splitText(startOffset);
            targetNode.insertAfter(textNode, false);
          }
          if (textNode.isComposing() && this.anchor.type === "text") {
            this.anchor.offset -= text.length;
          }
          return;
        }
      } else if ($isTabNode(firstNode)) {
        const textNode = $createTextNode(text);
        textNode.setFormat(format);
        textNode.setStyle(style);
        textNode.select();
        firstNode.replace(textNode);
        return;
      }
      const delCount = endOffset - startOffset;
      firstNode = firstNode.spliceText(startOffset, delCount, text, true);
      if (firstNode.getTextContent() === "") {
        firstNode.remove();
      } else if (this.anchor.type === "text") {
        if (firstNode.isComposing()) {
          this.anchor.offset -= text.length;
        } else {
          this.format = firstNodeFormat;
          this.style = firstNodeStyle;
        }
      }
    } else {
      const markedNodeKeysForKeep = /* @__PURE__ */ new Set([
        ...firstNode.getParentKeys(),
        ...lastNode.getParentKeys()
      ]);
      const firstElement = $isElementNode(firstNode) ? firstNode : firstNode.getParentOrThrow();
      let lastElement = $isElementNode(lastNode) ? lastNode : lastNode.getParentOrThrow();
      let lastElementChild = lastNode;
      if (!firstElement.is(lastElement) && lastElement.isInline()) {
        do {
          lastElementChild = lastElement;
          lastElement = lastElement.getParentOrThrow();
        } while (lastElement.isInline());
      }
      if (endPoint.type === "text" && (endOffset !== 0 || lastNode.getTextContent() === "") || endPoint.type === "element" && lastNode.getIndexWithinParent() < endOffset) {
        if ($isTextNode(lastNode) && !lastNode.isToken() && endOffset !== lastNode.getTextContentSize()) {
          if (lastNode.isSegmented()) {
            const textNode = $createTextNode(lastNode.getTextContent());
            lastNode.replace(textNode);
            lastNode = textNode;
          }
          if (!$isRootNode(endPoint.getNode()) && endPoint.type === "text") {
            lastNode = lastNode.spliceText(0, endOffset, "");
          }
          markedNodeKeysForKeep.add(lastNode.__key);
        } else {
          const lastNodeParent = lastNode.getParentOrThrow();
          if (!lastNodeParent.canBeEmpty() && lastNodeParent.getChildrenSize() === 1) {
            lastNodeParent.remove();
          } else {
            lastNode.remove();
          }
        }
      } else {
        markedNodeKeysForKeep.add(lastNode.__key);
      }
      const lastNodeChildren = lastElement.getChildren();
      const selectedNodesSet = new Set(selectedNodes);
      const firstAndLastElementsAreEqual = firstElement.is(lastElement);
      const insertionTarget = firstElement.isInline() && firstNode.getNextSibling() === null ? firstElement : firstNode;
      for (let i = lastNodeChildren.length - 1; i >= 0; i--) {
        const lastNodeChild = lastNodeChildren[i];
        if (lastNodeChild.is(firstNode) || $isElementNode(lastNodeChild) && lastNodeChild.isParentOf(firstNode)) {
          break;
        }
        if (lastNodeChild.isAttached()) {
          if (!selectedNodesSet.has(lastNodeChild) || lastNodeChild.is(lastElementChild)) {
            if (!firstAndLastElementsAreEqual) {
              insertionTarget.insertAfter(lastNodeChild, false);
            }
          } else {
            lastNodeChild.remove();
          }
        }
      }
      if (!firstAndLastElementsAreEqual) {
        let parent = lastElement;
        let lastRemovedParent = null;
        while (parent !== null) {
          const children = parent.getChildren();
          const childrenLength = children.length;
          if (childrenLength === 0 || children[childrenLength - 1].is(lastRemovedParent)) {
            markedNodeKeysForKeep.delete(parent.__key);
            lastRemovedParent = parent;
          }
          parent = parent.getParent();
        }
      }
      if (!firstNode.isToken()) {
        firstNode = firstNode.spliceText(
          startOffset,
          firstNodeTextLength - startOffset,
          text,
          true
        );
        if (firstNode.getTextContent() === "") {
          firstNode.remove();
        } else if (firstNode.isComposing() && this.anchor.type === "text") {
          this.anchor.offset -= text.length;
        }
      } else if (startOffset === firstNodeTextLength) {
        firstNode.select();
      } else {
        const textNode = $createTextNode(text);
        textNode.select();
        firstNode.replace(textNode);
      }
      for (let i = 1; i < selectedNodesLength; i++) {
        const selectedNode = selectedNodes[i];
        const key = selectedNode.__key;
        if (!markedNodeKeysForKeep.has(key)) {
          selectedNode.remove();
        }
      }
    }
  }
  /**
   * Removes the text in the Selection, adjusting the EditorState accordingly.
   */
  removeText() {
    this.insertText("");
  }
  /**
   * Applies the provided format to the TextNodes in the Selection, splitting or
   * merging nodes as necessary.
   *
   * @param formatType the format type to apply to the nodes in the Selection.
   */
  formatText(formatType) {
    if (this.isCollapsed()) {
      this.toggleFormat(formatType);
      $setCompositionKey(null);
      return;
    }
    const selectedNodes = this.getNodes();
    const selectedTextNodes = [];
    for (const selectedNode of selectedNodes) {
      if ($isTextNode(selectedNode)) {
        selectedTextNodes.push(selectedNode);
      }
    }
    const selectedTextNodesLength = selectedTextNodes.length;
    if (selectedTextNodesLength === 0) {
      this.toggleFormat(formatType);
      $setCompositionKey(null);
      return;
    }
    const anchor = this.anchor;
    const focus = this.focus;
    const isBackward = this.isBackward();
    const startPoint = isBackward ? focus : anchor;
    const endPoint = isBackward ? anchor : focus;
    let firstIndex = 0;
    let firstNode = selectedTextNodes[0];
    let startOffset = startPoint.type === "element" ? 0 : startPoint.offset;
    if (startPoint.type === "text" && startOffset === firstNode.getTextContentSize()) {
      firstIndex = 1;
      firstNode = selectedTextNodes[1];
      startOffset = 0;
    }
    if (firstNode == null) {
      return;
    }
    const firstNextFormat = firstNode.getFormatFlags(formatType, null);
    const lastIndex = selectedTextNodesLength - 1;
    let lastNode = selectedTextNodes[lastIndex];
    const endOffset = endPoint.type === "text" ? endPoint.offset : lastNode.getTextContentSize();
    if (firstNode.is(lastNode)) {
      if (startOffset === endOffset) {
        return;
      }
      if ($isTokenOrSegmented(firstNode) || startOffset === 0 && endOffset === firstNode.getTextContentSize()) {
        firstNode.setFormat(firstNextFormat);
      } else {
        const splitNodes = firstNode.splitText(startOffset, endOffset);
        const replacement = startOffset === 0 ? splitNodes[0] : splitNodes[1];
        replacement.setFormat(firstNextFormat);
        if (startPoint.type === "text") {
          startPoint.set(replacement.__key, 0, "text");
        }
        if (endPoint.type === "text") {
          endPoint.set(replacement.__key, endOffset - startOffset, "text");
        }
      }
      this.format = firstNextFormat;
      return;
    }
    if (startOffset !== 0 && !$isTokenOrSegmented(firstNode)) {
      [, firstNode] = firstNode.splitText(startOffset);
      startOffset = 0;
    }
    firstNode.setFormat(firstNextFormat);
    const lastNextFormat = lastNode.getFormatFlags(formatType, firstNextFormat);
    if (endOffset > 0) {
      if (endOffset !== lastNode.getTextContentSize() && !$isTokenOrSegmented(lastNode)) {
        [lastNode] = lastNode.splitText(endOffset);
      }
      lastNode.setFormat(lastNextFormat);
    }
    for (let i = firstIndex + 1; i < lastIndex; i++) {
      const textNode = selectedTextNodes[i];
      const nextFormat = textNode.getFormatFlags(formatType, lastNextFormat);
      textNode.setFormat(nextFormat);
    }
    if (startPoint.type === "text") {
      startPoint.set(firstNode.__key, startOffset, "text");
    }
    if (endPoint.type === "text") {
      endPoint.set(lastNode.__key, endOffset, "text");
    }
    this.format = firstNextFormat | lastNextFormat;
  }
  /**
   * Attempts to "intelligently" insert an arbitrary list of Lexical nodes into the EditorState at the
   * current Selection according to a set of heuristics that determine how surrounding nodes
   * should be changed, replaced, or moved to accomodate the incoming ones.
   *
   * @param nodes - the nodes to insert
   */
  insertNodes(nodes) {
    if (nodes.length === 0) {
      return;
    }
    if (this.anchor.key === "root") {
      this.insertParagraph();
      const selection = $getSelection();
      invariant(
        $isRangeSelection(selection),
        "Expected RangeSelection after insertParagraph"
      );
      return selection.insertNodes(nodes);
    }
    const firstPoint = this.isBackward() ? this.focus : this.anchor;
    const firstBlock = $getAncestor(firstPoint.getNode(), INTERNAL_$isBlock);
    const last = nodes[nodes.length - 1];
    if ("__language" in firstBlock && $isElementNode(firstBlock)) {
      if ("__language" in nodes[0]) {
        this.insertText(nodes[0].getTextContent());
      } else {
        const index = $removeTextAndSplitBlock(this);
        firstBlock.splice(index, 0, nodes);
        last.selectEnd();
      }
      return;
    }
    const notInline = (node) => ($isElementNode(node) || $isDecoratorNode(node)) && !node.isInline();
    if (!nodes.some(notInline)) {
      invariant(
        $isElementNode(firstBlock),
        "Expected 'firstBlock' to be an ElementNode"
      );
      const index = $removeTextAndSplitBlock(this);
      firstBlock.splice(index, 0, nodes);
      last.selectEnd();
      return;
    }
    const blocksParent = $wrapInlineNodes(nodes);
    const nodeToSelect = blocksParent.getLastDescendant();
    const blocks = blocksParent.getChildren();
    const isMergeable = (node) => $isElementNode(node) && INTERNAL_$isBlock(node) && !node.isEmpty() && $isElementNode(firstBlock) && (!firstBlock.isEmpty() || firstBlock.canMergeWhenEmpty());
    const shouldInsert = !$isElementNode(firstBlock) || !firstBlock.isEmpty();
    const insertedParagraph = shouldInsert ? this.insertParagraph() : null;
    const lastToInsert = blocks[blocks.length - 1];
    let firstToInsert = blocks[0];
    if (isMergeable(firstToInsert)) {
      invariant(
        $isElementNode(firstBlock),
        "Expected 'firstBlock' to be an ElementNode"
      );
      firstBlock.append(...firstToInsert.getChildren());
      firstToInsert = blocks[1];
    }
    if (firstToInsert) {
      insertRangeAfter(firstBlock, firstToInsert);
    }
    const lastInsertedBlock = $getAncestor(nodeToSelect, INTERNAL_$isBlock);
    if (insertedParagraph && $isElementNode(lastInsertedBlock) && (insertedParagraph.canMergeWhenEmpty() || INTERNAL_$isBlock(lastToInsert))) {
      lastInsertedBlock.append(...insertedParagraph.getChildren());
      insertedParagraph.remove();
    }
    if ($isElementNode(firstBlock) && firstBlock.isEmpty()) {
      firstBlock.remove();
    }
    nodeToSelect.selectEnd();
    const lastChild = $isElementNode(firstBlock) ? firstBlock.getLastChild() : null;
    if ($isLineBreakNode(lastChild) && lastInsertedBlock !== firstBlock) {
      lastChild.remove();
    }
  }
  /**
   * Inserts a new ParagraphNode into the EditorState at the current Selection
   *
   * @returns the newly inserted node.
   */
  insertParagraph() {
    if (this.anchor.key === "root") {
      const paragraph2 = $createParagraphNode();
      $getRoot().splice(this.anchor.offset, 0, [paragraph2]);
      paragraph2.select();
      return paragraph2;
    }
    const index = $removeTextAndSplitBlock(this);
    const block = $getAncestor(this.anchor.getNode(), INTERNAL_$isBlock);
    invariant($isElementNode(block), "Expected ancestor to be an ElementNode");
    const firstToAppend = block.getChildAtIndex(index);
    const nodesToInsert = firstToAppend ? [firstToAppend, ...firstToAppend.getNextSiblings()] : [];
    const newBlock = block.insertNewAfter(this, false);
    if (newBlock) {
      newBlock.append(...nodesToInsert);
      newBlock.selectStart();
      return newBlock;
    }
    return null;
  }
  /**
   * Inserts a logical linebreak, which may be a new LineBreakNode or a new ParagraphNode, into the EditorState at the
   * current Selection.
   */
  insertLineBreak(selectStart) {
    const lineBreak = $createLineBreakNode();
    this.insertNodes([lineBreak]);
    if (selectStart) {
      const parent = lineBreak.getParentOrThrow();
      const index = lineBreak.getIndexWithinParent();
      parent.select(index, index);
    }
  }
  /**
   * Extracts the nodes in the Selection, splitting nodes where necessary
   * to get offset-level precision.
   *
   * @returns The nodes in the Selection
   */
  extract() {
    const selectedNodes = this.getNodes();
    const selectedNodesLength = selectedNodes.length;
    const lastIndex = selectedNodesLength - 1;
    const anchor = this.anchor;
    const focus = this.focus;
    let firstNode = selectedNodes[0];
    let lastNode = selectedNodes[lastIndex];
    const [anchorOffset, focusOffset] = $getCharacterOffsets(this);
    if (selectedNodesLength === 0) {
      return [];
    } else if (selectedNodesLength === 1) {
      if ($isTextNode(firstNode) && !this.isCollapsed()) {
        const startOffset = anchorOffset > focusOffset ? focusOffset : anchorOffset;
        const endOffset = anchorOffset > focusOffset ? anchorOffset : focusOffset;
        const splitNodes = firstNode.splitText(startOffset, endOffset);
        const node = startOffset === 0 ? splitNodes[0] : splitNodes[1];
        return node != null ? [node] : [];
      }
      return [firstNode];
    }
    const isBefore = anchor.isBefore(focus);
    if ($isTextNode(firstNode)) {
      const startOffset = isBefore ? anchorOffset : focusOffset;
      if (startOffset === firstNode.getTextContentSize()) {
        selectedNodes.shift();
      } else if (startOffset !== 0) {
        [, firstNode] = firstNode.splitText(startOffset);
        selectedNodes[0] = firstNode;
      }
    }
    if ($isTextNode(lastNode)) {
      const lastNodeText = lastNode.getTextContent();
      const lastNodeTextLength = lastNodeText.length;
      const endOffset = isBefore ? focusOffset : anchorOffset;
      if (endOffset === 0) {
        selectedNodes.pop();
      } else if (endOffset !== lastNodeTextLength) {
        [lastNode] = lastNode.splitText(endOffset);
        selectedNodes[lastIndex] = lastNode;
      }
    }
    return selectedNodes;
  }
  /**
   * Modifies the Selection according to the parameters and a set of heuristics that account for
   * various node types. Can be used to safely move or extend selection by one logical "unit" without
   * dealing explicitly with all the possible node types.
   *
   * @param alter the type of modification to perform
   * @param isBackward whether or not selection is backwards
   * @param granularity the granularity at which to apply the modification
   */
  modify(alter, isBackward, granularity) {
    const focus = this.focus;
    const anchor = this.anchor;
    const collapse = alter === "move";
    const possibleNode = $getAdjacentNode(focus, isBackward);
    if ($isDecoratorNode(possibleNode) && !possibleNode.isIsolated()) {
      if (collapse && possibleNode.isKeyboardSelectable()) {
        const nodeSelection = $createNodeSelection();
        nodeSelection.add(possibleNode.__key);
        $setSelection(nodeSelection);
        return;
      }
      const sibling = isBackward ? possibleNode.getPreviousSibling() : possibleNode.getNextSibling();
      if (!$isTextNode(sibling)) {
        const parent = possibleNode.getParentOrThrow();
        let offset;
        let elementKey;
        if ($isElementNode(sibling)) {
          elementKey = sibling.__key;
          offset = isBackward ? sibling.getChildrenSize() : 0;
        } else {
          offset = possibleNode.getIndexWithinParent();
          elementKey = parent.__key;
          if (!isBackward) {
            offset++;
          }
        }
        focus.set(elementKey, offset, "element");
        if (collapse) {
          anchor.set(elementKey, offset, "element");
        }
        return;
      } else {
        const siblingKey = sibling.__key;
        const offset = isBackward ? sibling.getTextContent().length : 0;
        focus.set(siblingKey, offset, "text");
        if (collapse) {
          anchor.set(siblingKey, offset, "text");
        }
        return;
      }
    }
    const editor = getActiveEditor();
    const domSelection = getDOMSelection(editor._window);
    if (!domSelection) {
      return;
    }
    const blockCursorElement = editor._blockCursorElement;
    const rootElement = editor._rootElement;
    if (rootElement !== null && blockCursorElement !== null && $isElementNode(possibleNode) && !possibleNode.isInline() && !possibleNode.canBeEmpty()) {
      removeDOMBlockCursorElement(blockCursorElement, editor, rootElement);
    }
    moveNativeSelection(
      domSelection,
      alter,
      isBackward ? "backward" : "forward",
      granularity
    );
    if (domSelection.rangeCount > 0) {
      const range = domSelection.getRangeAt(0);
      const anchorNode = this.anchor.getNode();
      const root = $isRootNode(anchorNode) ? anchorNode : $getNearestRootOrShadowRoot(anchorNode);
      this.applyDOMRange(range);
      this.dirty = true;
      if (!collapse) {
        const nodes = this.getNodes();
        const validNodes = [];
        let shrinkSelection = false;
        for (let i = 0; i < nodes.length; i++) {
          const nextNode = nodes[i];
          if ($hasAncestor(nextNode, root)) {
            validNodes.push(nextNode);
          } else {
            shrinkSelection = true;
          }
        }
        if (shrinkSelection && validNodes.length > 0) {
          if (isBackward) {
            const firstValidNode = validNodes[0];
            if ($isElementNode(firstValidNode)) {
              firstValidNode.selectStart();
            } else {
              firstValidNode.getParentOrThrow().selectStart();
            }
          } else {
            const lastValidNode = validNodes[validNodes.length - 1];
            if ($isElementNode(lastValidNode)) {
              lastValidNode.selectEnd();
            } else {
              lastValidNode.getParentOrThrow().selectEnd();
            }
          }
        }
        if (domSelection.anchorNode !== range.startContainer || domSelection.anchorOffset !== range.startOffset) {
          $swapPoints(this);
        }
      }
    }
  }
  /**
   * Helper for handling forward character and word deletion that prevents element nodes
   * like a table, columns layout being destroyed
   *
   * @param anchor the anchor
   * @param anchorNode the anchor node in the selection
   * @param isBackward whether or not selection is backwards
   */
  forwardDeletion(anchor, anchorNode, isBackward) {
    if (!isBackward && // Delete forward handle case
    (anchor.type === "element" && $isElementNode(anchorNode) && anchor.offset === anchorNode.getChildrenSize() || anchor.type === "text" && anchor.offset === anchorNode.getTextContentSize())) {
      const parent = anchorNode.getParent();
      const nextSibling = anchorNode.getNextSibling() || (parent === null ? null : parent.getNextSibling());
      if ($isElementNode(nextSibling) && nextSibling.isShadowRoot()) {
        return true;
      }
    }
    return false;
  }
  /**
   * Performs one logical character deletion operation on the EditorState based on the current Selection.
   * Handles different node types.
   *
   * @param isBackward whether or not the selection is backwards.
   */
  deleteCharacter(isBackward) {
    const wasCollapsed = this.isCollapsed();
    if (this.isCollapsed()) {
      const anchor = this.anchor;
      let anchorNode = anchor.getNode();
      if (this.forwardDeletion(anchor, anchorNode, isBackward)) {
        return;
      }
      const focus = this.focus;
      const possibleNode = $getAdjacentNode(focus, isBackward);
      if ($isDecoratorNode(possibleNode) && !possibleNode.isIsolated()) {
        if (possibleNode.isKeyboardSelectable() && $isElementNode(anchorNode) && anchorNode.getChildrenSize() === 0) {
          anchorNode.remove();
          const nodeSelection = $createNodeSelection();
          nodeSelection.add(possibleNode.__key);
          $setSelection(nodeSelection);
        } else {
          possibleNode.remove();
          const editor = getActiveEditor();
          editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
        }
        return;
      } else if (!isBackward && $isElementNode(possibleNode) && $isElementNode(anchorNode) && anchorNode.isEmpty()) {
        anchorNode.remove();
        possibleNode.selectStart();
        return;
      }
      this.modify("extend", isBackward, "character");
      if (!this.isCollapsed()) {
        const focusNode = focus.type === "text" ? focus.getNode() : null;
        anchorNode = anchor.type === "text" ? anchor.getNode() : null;
        if (focusNode !== null && focusNode.isSegmented()) {
          const offset = focus.offset;
          const textContentSize = focusNode.getTextContentSize();
          if (focusNode.is(anchorNode) || isBackward && offset !== textContentSize || !isBackward && offset !== 0) {
            $removeSegment(focusNode, isBackward, offset);
            return;
          }
        } else if (anchorNode !== null && anchorNode.isSegmented()) {
          const offset = anchor.offset;
          const textContentSize = anchorNode.getTextContentSize();
          if (anchorNode.is(focusNode) || isBackward && offset !== 0 || !isBackward && offset !== textContentSize) {
            $removeSegment(anchorNode, isBackward, offset);
            return;
          }
        }
        $updateCaretSelectionForUnicodeCharacter(this, isBackward);
      } else if (isBackward && anchor.offset === 0) {
        const element = anchor.type === "element" ? anchor.getNode() : anchor.getNode().getParentOrThrow();
        if (element.collapseAtStart(this)) {
          return;
        }
      }
    }
    this.removeText();
    if (isBackward && !wasCollapsed && this.isCollapsed() && this.anchor.type === "element" && this.anchor.offset === 0) {
      const anchorNode = this.anchor.getNode();
      if (anchorNode.isEmpty() && $isRootNode(anchorNode.getParent()) && anchorNode.getIndexWithinParent() === 0) {
        anchorNode.collapseAtStart(this);
      }
    }
  }
  /**
   * Performs one logical line deletion operation on the EditorState based on the current Selection.
   * Handles different node types.
   *
   * @param isBackward whether or not the selection is backwards.
   */
  deleteLine(isBackward) {
    if (this.isCollapsed()) {
      const anchorIsElement = this.anchor.type === "element";
      if (anchorIsElement) {
        this.insertText(" ");
      }
      this.modify("extend", isBackward, "lineboundary");
      const endPoint = isBackward ? this.focus : this.anchor;
      if (endPoint.offset === 0) {
        this.modify("extend", isBackward, "character");
      }
      if (anchorIsElement) {
        const startPoint = isBackward ? this.anchor : this.focus;
        startPoint.set(startPoint.key, startPoint.offset + 1, startPoint.type);
      }
    }
    this.removeText();
  }
  /**
   * Performs one logical word deletion operation on the EditorState based on the current Selection.
   * Handles different node types.
   *
   * @param isBackward whether or not the selection is backwards.
   */
  deleteWord(isBackward) {
    if (this.isCollapsed()) {
      const anchor = this.anchor;
      const anchorNode = anchor.getNode();
      if (this.forwardDeletion(anchor, anchorNode, isBackward)) {
        return;
      }
      this.modify("extend", isBackward, "word");
    }
    this.removeText();
  }
  /**
   * Returns whether the Selection is "backwards", meaning the focus
   * logically precedes the anchor in the EditorState.
   * @returns true if the Selection is backwards, false otherwise.
   */
  isBackward() {
    return this.focus.isBefore(this.anchor);
  }
  getStartEndPoints() {
    return [this.anchor, this.focus];
  }
};
function $isNodeSelection(x) {
  return x instanceof NodeSelection;
}
function getCharacterOffset(point) {
  const offset = point.offset;
  if (point.type === "text") {
    return offset;
  }
  const parent = point.getNode();
  return offset === parent.getChildrenSize() ? parent.getTextContent().length : 0;
}
function $getCharacterOffsets(selection) {
  const anchorAndFocus = selection.getStartEndPoints();
  if (anchorAndFocus === null) {
    return [0, 0];
  }
  const [anchor, focus] = anchorAndFocus;
  if (anchor.type === "element" && focus.type === "element" && anchor.key === focus.key && anchor.offset === focus.offset) {
    return [0, 0];
  }
  return [getCharacterOffset(anchor), getCharacterOffset(focus)];
}
function $swapPoints(selection) {
  const focus = selection.focus;
  const anchor = selection.anchor;
  const anchorKey = anchor.key;
  const anchorOffset = anchor.offset;
  const anchorType = anchor.type;
  $setPointValues(anchor, focus.key, focus.offset, focus.type);
  $setPointValues(focus, anchorKey, anchorOffset, anchorType);
  selection._cachedNodes = null;
}
function moveNativeSelection(domSelection, alter, direction, granularity) {
  domSelection.modify(alter, direction, granularity);
}
function $updateCaretSelectionForUnicodeCharacter(selection, isBackward) {
  const anchor = selection.anchor;
  const focus = selection.focus;
  const anchorNode = anchor.getNode();
  const focusNode = focus.getNode();
  if (anchorNode === focusNode && anchor.type === "text" && focus.type === "text") {
    const anchorOffset = anchor.offset;
    const focusOffset = focus.offset;
    const isBefore = anchorOffset < focusOffset;
    const startOffset = isBefore ? anchorOffset : focusOffset;
    const endOffset = isBefore ? focusOffset : anchorOffset;
    const characterOffset = endOffset - 1;
    if (startOffset !== characterOffset) {
      const text = anchorNode.getTextContent().slice(startOffset, endOffset);
      if (!doesContainGrapheme(text)) {
        if (isBackward) {
          focus.offset = characterOffset;
        } else {
          anchor.offset = characterOffset;
        }
      }
    }
  } else {
  }
}
function $removeSegment(node, isBackward, offset) {
  const textNode = node;
  const textContent = textNode.getTextContent();
  const split = textContent.split(/(?=\s)/g);
  const splitLength = split.length;
  let segmentOffset = 0;
  let restoreOffset = 0;
  for (let i = 0; i < splitLength; i++) {
    const text = split[i];
    const isLast = i === splitLength - 1;
    restoreOffset = segmentOffset;
    segmentOffset += text.length;
    if (isBackward && segmentOffset === offset || segmentOffset > offset || isLast) {
      split.splice(i, 1);
      if (isLast) {
        restoreOffset = void 0;
      }
      break;
    }
  }
  const nextTextContent = split.join("").trim();
  if (nextTextContent === "") {
    textNode.remove();
  } else {
    textNode.setTextContent(nextTextContent);
    textNode.select(restoreOffset, restoreOffset);
  }
}
function shouldResolveAncestor(resolvedElement, resolvedOffset, lastPoint) {
  const parent = resolvedElement.getParent();
  return lastPoint === null || parent === null || !parent.canBeEmpty() || parent !== lastPoint.getNode();
}
function $internalResolveSelectionPoint(dom, offset, lastPoint, editor) {
  let resolvedOffset = offset;
  let resolvedNode;
  if (dom.nodeType === DOM_ELEMENT_TYPE) {
    let moveSelectionToEnd = false;
    const childNodes = dom.childNodes;
    const childNodesLength = childNodes.length;
    const blockCursorElement = editor._blockCursorElement;
    if (resolvedOffset === childNodesLength) {
      moveSelectionToEnd = true;
      resolvedOffset = childNodesLength - 1;
    }
    let childDOM = childNodes[resolvedOffset];
    let hasBlockCursor = false;
    if (childDOM === blockCursorElement) {
      childDOM = childNodes[resolvedOffset + 1];
      hasBlockCursor = true;
    } else if (blockCursorElement !== null) {
      const blockCursorElementParent = blockCursorElement.parentNode;
      if (dom === blockCursorElementParent) {
        const blockCursorOffset = Array.prototype.indexOf.call(
          blockCursorElementParent.children,
          blockCursorElement
        );
        if (offset > blockCursorOffset) {
          resolvedOffset--;
        }
      }
    }
    resolvedNode = $getNodeFromDOM(childDOM);
    if ($isTextNode(resolvedNode)) {
      resolvedOffset = getTextNodeOffset(resolvedNode, moveSelectionToEnd);
    } else {
      let resolvedElement = $getNodeFromDOM(dom);
      if (resolvedElement === null) {
        return null;
      }
      if ($isElementNode(resolvedElement)) {
        resolvedOffset = Math.min(
          resolvedElement.getChildrenSize(),
          resolvedOffset
        );
        let child = resolvedElement.getChildAtIndex(resolvedOffset);
        if ($isElementNode(child) && shouldResolveAncestor(child, resolvedOffset, lastPoint)) {
          const descendant = moveSelectionToEnd ? child.getLastDescendant() : child.getFirstDescendant();
          if (descendant === null) {
            resolvedElement = child;
          } else {
            child = descendant;
            resolvedElement = $isElementNode(child) ? child : child.getParentOrThrow();
          }
          resolvedOffset = 0;
        }
        if ($isTextNode(child)) {
          resolvedNode = child;
          resolvedElement = null;
          resolvedOffset = getTextNodeOffset(child, moveSelectionToEnd);
        } else if (child !== resolvedElement && moveSelectionToEnd && !hasBlockCursor) {
          resolvedOffset++;
        }
      } else {
        const index = resolvedElement.getIndexWithinParent();
        if (offset === 0 && $isDecoratorNode(resolvedElement) && $getNodeFromDOM(dom) === resolvedElement) {
          resolvedOffset = index;
        } else {
          resolvedOffset = index + 1;
        }
        resolvedElement = resolvedElement.getParentOrThrow();
      }
      if ($isElementNode(resolvedElement)) {
        return $createPoint(resolvedElement.__key, resolvedOffset, "element");
      }
    }
  } else {
    resolvedNode = $getNodeFromDOM(dom);
  }
  if (!$isTextNode(resolvedNode)) {
    return null;
  }
  return $createPoint(resolvedNode.__key, resolvedOffset, "text");
}
function resolveSelectionPointOnBoundary(point, isBackward, isCollapsed) {
  const offset = point.offset;
  const node = point.getNode();
  if (offset === 0) {
    const prevSibling = node.getPreviousSibling();
    const parent = node.getParent();
    if (!isBackward) {
      if ($isElementNode(prevSibling) && !isCollapsed && prevSibling.isInline()) {
        point.key = prevSibling.__key;
        point.offset = prevSibling.getChildrenSize();
        point.type = "element";
      } else if ($isTextNode(prevSibling)) {
        point.key = prevSibling.__key;
        point.offset = prevSibling.getTextContent().length;
      }
    } else if ((isCollapsed || !isBackward) && prevSibling === null && $isElementNode(parent) && parent.isInline()) {
      const parentSibling = parent.getPreviousSibling();
      if ($isTextNode(parentSibling)) {
        point.key = parentSibling.__key;
        point.offset = parentSibling.getTextContent().length;
      }
    }
  } else if (offset === node.getTextContent().length) {
    const nextSibling = node.getNextSibling();
    const parent = node.getParent();
    if (isBackward && $isElementNode(nextSibling) && nextSibling.isInline()) {
      point.key = nextSibling.__key;
      point.offset = 0;
      point.type = "element";
    } else if ((isCollapsed || isBackward) && nextSibling === null && $isElementNode(parent) && parent.isInline() && !parent.canInsertTextAfter()) {
      const parentSibling = parent.getNextSibling();
      if ($isTextNode(parentSibling)) {
        point.key = parentSibling.__key;
        point.offset = 0;
      }
    }
  }
}
function $normalizeSelectionPointsForBoundaries(anchor, focus, lastSelection) {
  if (anchor.type === "text" && focus.type === "text") {
    const isBackward = anchor.isBefore(focus);
    const isCollapsed = anchor.is(focus);
    resolveSelectionPointOnBoundary(anchor, isBackward, isCollapsed);
    resolveSelectionPointOnBoundary(focus, !isBackward, isCollapsed);
    if (isCollapsed) {
      focus.key = anchor.key;
      focus.offset = anchor.offset;
      focus.type = anchor.type;
    }
    const editor = getActiveEditor();
    if (editor.isComposing() && editor._compositionKey !== anchor.key && $isRangeSelection(lastSelection)) {
      const lastAnchor = lastSelection.anchor;
      const lastFocus = lastSelection.focus;
      $setPointValues(
        anchor,
        lastAnchor.key,
        lastAnchor.offset,
        lastAnchor.type
      );
      $setPointValues(focus, lastFocus.key, lastFocus.offset, lastFocus.type);
    }
  }
}
function $internalResolveSelectionPoints(anchorDOM, anchorOffset, focusDOM, focusOffset, editor, lastSelection) {
  if (anchorDOM === null || focusDOM === null || !isSelectionWithinEditor(editor, anchorDOM, focusDOM)) {
    return null;
  }
  const resolvedAnchorPoint = $internalResolveSelectionPoint(
    anchorDOM,
    anchorOffset,
    $isRangeSelection(lastSelection) ? lastSelection.anchor : null,
    editor
  );
  if (resolvedAnchorPoint === null) {
    return null;
  }
  const resolvedFocusPoint = $internalResolveSelectionPoint(
    focusDOM,
    focusOffset,
    $isRangeSelection(lastSelection) ? lastSelection.focus : null,
    editor
  );
  if (resolvedFocusPoint === null) {
    return null;
  }
  if (resolvedAnchorPoint.type === "element" && resolvedFocusPoint.type === "element") {
    const anchorNode = $getNodeFromDOM(anchorDOM);
    const focusNode = $getNodeFromDOM(focusDOM);
    if ($isDecoratorNode(anchorNode) && $isDecoratorNode(focusNode)) {
      return null;
    }
  }
  $normalizeSelectionPointsForBoundaries(
    resolvedAnchorPoint,
    resolvedFocusPoint,
    lastSelection
  );
  return [resolvedAnchorPoint, resolvedFocusPoint];
}
function $isBlockElementNode(node) {
  return $isElementNode(node) && !node.isInline();
}
function $internalMakeRangeSelection(anchorKey, anchorOffset, focusKey, focusOffset, anchorType, focusType) {
  const editorState = getActiveEditorState();
  const selection = new RangeSelection4(
    $createPoint(anchorKey, anchorOffset, anchorType),
    $createPoint(focusKey, focusOffset, focusType),
    0,
    ""
  );
  selection.dirty = true;
  editorState._selection = selection;
  return selection;
}
function $createRangeSelection() {
  const anchor = $createPoint("root", 0, "element");
  const focus = $createPoint("root", 0, "element");
  return new RangeSelection4(anchor, focus, 0, "");
}
function $createNodeSelection() {
  return new NodeSelection(/* @__PURE__ */ new Set());
}
function $internalCreateSelection(editor) {
  const currentEditorState = editor.getEditorState();
  const lastSelection = currentEditorState._selection;
  const domSelection = getDOMSelection(editor._window);
  if ($isRangeSelection(lastSelection) || lastSelection == null) {
    return $internalCreateRangeSelection(
      lastSelection,
      domSelection,
      editor,
      null
    );
  }
  return lastSelection.clone();
}
function $createRangeSelectionFromDom(domSelection, editor) {
  return $internalCreateRangeSelection(null, domSelection, editor, null);
}
function $internalCreateRangeSelection(lastSelection, domSelection, editor, event) {
  const windowObj = editor._window;
  if (windowObj === null) {
    return null;
  }
  const windowEvent = event || windowObj.event;
  const eventType = windowEvent ? windowEvent.type : void 0;
  const isSelectionChange = eventType === "selectionchange";
  const useDOMSelection = !getIsProcessingMutations() && (isSelectionChange || eventType === "beforeinput" || eventType === "compositionstart" || eventType === "compositionend" || eventType === "click" && windowEvent && windowEvent.detail === 3 || eventType === "drop" || eventType === void 0);
  let anchorDOM, focusDOM, anchorOffset, focusOffset;
  if (!$isRangeSelection(lastSelection) || useDOMSelection) {
    if (domSelection === null) {
      return null;
    }
    anchorDOM = domSelection.anchorNode;
    focusDOM = domSelection.focusNode;
    anchorOffset = domSelection.anchorOffset;
    focusOffset = domSelection.focusOffset;
    if (isSelectionChange && $isRangeSelection(lastSelection) && !isSelectionWithinEditor(editor, anchorDOM, focusDOM)) {
      return lastSelection.clone();
    }
  } else {
    return lastSelection.clone();
  }
  const resolvedSelectionPoints = $internalResolveSelectionPoints(
    anchorDOM,
    anchorOffset,
    focusDOM,
    focusOffset,
    editor,
    lastSelection
  );
  if (resolvedSelectionPoints === null) {
    return null;
  }
  const [resolvedAnchorPoint, resolvedFocusPoint] = resolvedSelectionPoints;
  return new RangeSelection4(
    resolvedAnchorPoint,
    resolvedFocusPoint,
    !$isRangeSelection(lastSelection) ? 0 : lastSelection.format,
    !$isRangeSelection(lastSelection) ? "" : lastSelection.style
  );
}
function $getSelection() {
  const editorState = getActiveEditorState();
  return editorState._selection;
}
function $getPreviousSelection() {
  const editor = getActiveEditor();
  return editor._editorState._selection;
}
function $updateElementSelectionOnCreateDeleteNode(selection, parentNode, nodeOffset, times = 1) {
  const anchor = selection.anchor;
  const focus = selection.focus;
  const anchorNode = anchor.getNode();
  const focusNode = focus.getNode();
  if (!parentNode.is(anchorNode) && !parentNode.is(focusNode)) {
    return;
  }
  const parentKey = parentNode.__key;
  if (selection.isCollapsed()) {
    const selectionOffset = anchor.offset;
    if (nodeOffset <= selectionOffset && times > 0 || nodeOffset < selectionOffset && times < 0) {
      const newSelectionOffset = Math.max(0, selectionOffset + times);
      anchor.set(parentKey, newSelectionOffset, "element");
      focus.set(parentKey, newSelectionOffset, "element");
      $updateSelectionResolveTextNodes(selection);
    }
  } else {
    const isBackward = selection.isBackward();
    const firstPoint = isBackward ? focus : anchor;
    const firstPointNode = firstPoint.getNode();
    const lastPoint = isBackward ? anchor : focus;
    const lastPointNode = lastPoint.getNode();
    if (parentNode.is(firstPointNode)) {
      const firstPointOffset = firstPoint.offset;
      if (nodeOffset <= firstPointOffset && times > 0 || nodeOffset < firstPointOffset && times < 0) {
        firstPoint.set(
          parentKey,
          Math.max(0, firstPointOffset + times),
          "element"
        );
      }
    }
    if (parentNode.is(lastPointNode)) {
      const lastPointOffset = lastPoint.offset;
      if (nodeOffset <= lastPointOffset && times > 0 || nodeOffset < lastPointOffset && times < 0) {
        lastPoint.set(
          parentKey,
          Math.max(0, lastPointOffset + times),
          "element"
        );
      }
    }
  }
  $updateSelectionResolveTextNodes(selection);
}
function $updateSelectionResolveTextNodes(selection) {
  const anchor = selection.anchor;
  const anchorOffset = anchor.offset;
  const focus = selection.focus;
  const focusOffset = focus.offset;
  const anchorNode = anchor.getNode();
  const focusNode = focus.getNode();
  if (selection.isCollapsed()) {
    if (!$isElementNode(anchorNode)) {
      return;
    }
    const childSize = anchorNode.getChildrenSize();
    const anchorOffsetAtEnd = anchorOffset >= childSize;
    const child = anchorOffsetAtEnd ? anchorNode.getChildAtIndex(childSize - 1) : anchorNode.getChildAtIndex(anchorOffset);
    if ($isTextNode(child)) {
      let newOffset = 0;
      if (anchorOffsetAtEnd) {
        newOffset = child.getTextContentSize();
      }
      anchor.set(child.__key, newOffset, "text");
      focus.set(child.__key, newOffset, "text");
    }
    return;
  }
  if ($isElementNode(anchorNode)) {
    const childSize = anchorNode.getChildrenSize();
    const anchorOffsetAtEnd = anchorOffset >= childSize;
    const child = anchorOffsetAtEnd ? anchorNode.getChildAtIndex(childSize - 1) : anchorNode.getChildAtIndex(anchorOffset);
    if ($isTextNode(child)) {
      let newOffset = 0;
      if (anchorOffsetAtEnd) {
        newOffset = child.getTextContentSize();
      }
      anchor.set(child.__key, newOffset, "text");
    }
  }
  if ($isElementNode(focusNode)) {
    const childSize = focusNode.getChildrenSize();
    const focusOffsetAtEnd = focusOffset >= childSize;
    const child = focusOffsetAtEnd ? focusNode.getChildAtIndex(childSize - 1) : focusNode.getChildAtIndex(focusOffset);
    if ($isTextNode(child)) {
      let newOffset = 0;
      if (focusOffsetAtEnd) {
        newOffset = child.getTextContentSize();
      }
      focus.set(child.__key, newOffset, "text");
    }
  }
}
function applySelectionTransforms(nextEditorState, editor) {
  const prevEditorState = editor.getEditorState();
  const prevSelection = prevEditorState._selection;
  const nextSelection = nextEditorState._selection;
  if ($isRangeSelection(nextSelection)) {
    const anchor = nextSelection.anchor;
    const focus = nextSelection.focus;
    let anchorNode;
    if (anchor.type === "text") {
      anchorNode = anchor.getNode();
      anchorNode.selectionTransform(prevSelection, nextSelection);
    }
    if (focus.type === "text") {
      const focusNode = focus.getNode();
      if (anchorNode !== focusNode) {
        focusNode.selectionTransform(prevSelection, nextSelection);
      }
    }
  }
}
function moveSelectionPointToSibling(point, node, parent, prevSibling, nextSibling) {
  let siblingKey = null;
  let offset = 0;
  let type = null;
  if (prevSibling !== null) {
    siblingKey = prevSibling.__key;
    if ($isTextNode(prevSibling)) {
      offset = prevSibling.getTextContentSize();
      type = "text";
    } else if ($isElementNode(prevSibling)) {
      offset = prevSibling.getChildrenSize();
      type = "element";
    }
  } else {
    if (nextSibling !== null) {
      siblingKey = nextSibling.__key;
      if ($isTextNode(nextSibling)) {
        type = "text";
      } else if ($isElementNode(nextSibling)) {
        type = "element";
      }
    }
  }
  if (siblingKey !== null && type !== null) {
    point.set(siblingKey, offset, type);
  } else {
    offset = node.getIndexWithinParent();
    if (offset === -1) {
      offset = parent.getChildrenSize();
    }
    point.set(parent.__key, offset, "element");
  }
}
function adjustPointOffsetForMergedSibling(point, isBefore, key, target, textLength) {
  if (point.type === "text") {
    point.key = key;
    if (!isBefore) {
      point.offset += textLength;
    }
  } else if (point.offset > target.getIndexWithinParent()) {
    point.offset -= 1;
  }
}
function updateDOMSelection(prevSelection, nextSelection, editor, domSelection, tags, rootElement, nodeCount) {
  const anchorDOMNode = domSelection.anchorNode;
  const focusDOMNode = domSelection.focusNode;
  const anchorOffset = domSelection.anchorOffset;
  const focusOffset = domSelection.focusOffset;
  const activeElement = document.activeElement;
  if (tags.has("collaboration") && activeElement !== rootElement || activeElement !== null && isSelectionCapturedInDecoratorInput(activeElement)) {
    return;
  }
  if (!$isRangeSelection(nextSelection)) {
    if (activeElement !== null && domSelection.isCollapsed && focusDOMNode instanceof Node) {
      const node = $getNearestNodeFromDOMNode(focusDOMNode);
      if ($isDecoratorNode(node)) {
        domSelection.removeAllRanges();
        $selectSingleNode(node);
        return;
      }
    }
    if (prevSelection !== null && isSelectionWithinEditor(editor, anchorDOMNode, focusDOMNode)) {
      domSelection.removeAllRanges();
    }
    return;
  }
  const anchor = nextSelection.anchor;
  const focus = nextSelection.focus;
  const anchorKey = anchor.key;
  const focusKey = focus.key;
  const anchorDOM = getElementByKeyOrThrow(editor, anchorKey);
  const focusDOM = getElementByKeyOrThrow(editor, focusKey);
  const nextAnchorOffset = anchor.offset;
  const nextFocusOffset = focus.offset;
  const nextFormat = nextSelection.format;
  const nextStyle = nextSelection.style;
  const isCollapsed = nextSelection.isCollapsed();
  let nextAnchorNode = anchorDOM;
  let nextFocusNode = focusDOM;
  let anchorFormatOrStyleChanged = false;
  if (anchor.type === "text") {
    nextAnchorNode = getDOMTextNode(anchorDOM);
    const anchorNode = anchor.getNode();
    anchorFormatOrStyleChanged = anchorNode.getFormat() !== nextFormat || anchorNode.getStyle() !== nextStyle;
  } else if ($isRangeSelection(prevSelection) && prevSelection.anchor.type === "text") {
    anchorFormatOrStyleChanged = true;
  }
  if (focus.type === "text") {
    nextFocusNode = getDOMTextNode(focusDOM);
  }
  if (nextAnchorNode === null || nextFocusNode === null) {
    return;
  }
  if (isCollapsed && (prevSelection === null || anchorFormatOrStyleChanged || $isRangeSelection(prevSelection) && (prevSelection.format !== nextFormat || prevSelection.style !== nextStyle))) {
    markCollapsedSelectionFormat(
      nextFormat,
      nextStyle,
      nextAnchorOffset,
      anchorKey,
      performance.now()
    );
  }
  if (anchorOffset === nextAnchorOffset && focusOffset === nextFocusOffset && anchorDOMNode === nextAnchorNode && focusDOMNode === nextFocusNode && // Badly interpreted range selection when collapsed - #1482
  !(domSelection.type === "Range" && isCollapsed)) {
    if (activeElement === null || !rootElement.contains(activeElement)) {
      rootElement.focus({
        preventScroll: true
      });
    }
    if (anchor.type !== "element") {
      return;
    }
  }
  try {
    domSelection.setBaseAndExtent(
      nextAnchorNode,
      nextAnchorOffset,
      nextFocusNode,
      nextFocusOffset
    );
  } catch (error) {
    if (__DEV__) {
      console.warn(error);
    }
  }
  if (!tags.has("skip-scroll-into-view") && nextSelection.isCollapsed() && rootElement !== null && rootElement === document.activeElement) {
    const selectionTarget = nextSelection instanceof RangeSelection4 && nextSelection.anchor.type === "element" ? nextAnchorNode.childNodes[nextAnchorOffset] || null : domSelection.rangeCount > 0 ? domSelection.getRangeAt(0) : null;
    if (selectionTarget !== null) {
      let selectionRect;
      if (selectionTarget instanceof Text) {
        const range = document.createRange();
        range.selectNode(selectionTarget);
        selectionRect = range.getBoundingClientRect();
      } else {
        selectionRect = selectionTarget.getBoundingClientRect();
      }
      scrollIntoViewIfNeeded(editor, selectionRect, rootElement);
    }
  }
  markSelectionChangeFromDOMUpdate();
}
function $insertNodes(nodes) {
  let selection = $getSelection() || $getPreviousSelection();
  if (selection === null) {
    selection = $getRoot().selectEnd();
  }
  selection.insertNodes(nodes);
}
function $removeTextAndSplitBlock(selection) {
  let selection_ = selection;
  if (!selection.isCollapsed()) {
    selection_.removeText();
  }
  const newSelection = $getSelection();
  if ($isRangeSelection(newSelection)) {
    selection_ = newSelection;
  }
  invariant(
    $isRangeSelection(selection_),
    "Unexpected dirty selection to be null"
  );
  const anchor = selection_.anchor;
  let node = anchor.getNode();
  let offset = anchor.offset;
  while (!INTERNAL_$isBlock(node)) {
    [node, offset] = $splitNodeAtPoint(node, offset);
  }
  return offset;
}
function $splitNodeAtPoint(node, offset) {
  const parent = node.getParent();
  if (!parent) {
    const paragraph2 = $createParagraphNode();
    $getRoot().append(paragraph2);
    paragraph2.select();
    return [$getRoot(), 0];
  }
  if ($isTextNode(node)) {
    const split = node.splitText(offset);
    if (split.length === 0) {
      return [parent, node.getIndexWithinParent()];
    }
    const x = offset === 0 ? 0 : 1;
    const index = split[0].getIndexWithinParent() + x;
    return [parent, index];
  }
  if (!$isElementNode(node) || offset === 0) {
    return [parent, node.getIndexWithinParent()];
  }
  const firstToAppend = node.getChildAtIndex(offset);
  if (firstToAppend) {
    const insertPoint = new RangeSelection4(
      $createPoint(node.__key, offset, "element"),
      $createPoint(node.__key, offset, "element"),
      0,
      ""
    );
    const newElement = node.insertNewAfter(insertPoint);
    if (newElement) {
      newElement.append(firstToAppend, ...firstToAppend.getNextSiblings());
    }
  }
  return [parent, node.getIndexWithinParent() + 1];
}
function $wrapInlineNodes(nodes) {
  const virtualRoot = $createParagraphNode();
  let currentBlock = null;
  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    const isLineBreakNode = $isLineBreakNode(node);
    if (isLineBreakNode || $isDecoratorNode(node) && node.isInline() || $isElementNode(node) && node.isInline() || $isTextNode(node) || node.isParentRequired()) {
      if (currentBlock === null) {
        currentBlock = node.createParentElementNode();
        virtualRoot.append(currentBlock);
        if (isLineBreakNode) {
          continue;
        }
      }
      if (currentBlock !== null) {
        currentBlock.append(node);
      }
    } else {
      virtualRoot.append(node);
      currentBlock = null;
    }
  }
  return virtualRoot;
}

// resources/js/wysiwyg/lexical/core/LexicalUpdates.ts
var activeEditorState = null;
var activeEditor2 = null;
var isReadOnlyMode = false;
var isAttemptingToRecoverFromReconcilerError = false;
var infiniteTransformCount = 0;
var observerOptions = {
  characterData: true,
  childList: true,
  subtree: true
};
function isCurrentlyReadOnlyMode() {
  return isReadOnlyMode || activeEditorState !== null && activeEditorState._readOnly;
}
function errorOnReadOnly() {
  if (isReadOnlyMode) {
    invariant(false, "Cannot use method in read-only mode.");
  }
}
function errorOnInfiniteTransforms() {
  if (infiniteTransformCount > 99) {
    invariant(
      false,
      "One or more transforms are endlessly triggering additional transforms. May have encountered infinite recursion caused by transforms that have their preconditions too lose and/or conflict with each other."
    );
  }
}
function getActiveEditorState() {
  if (activeEditorState === null) {
    invariant(
      false,
      "Unable to find an active editor state. State helpers or node methods can only be used synchronously during the callback of editor.update(), editor.read(), or editorState.read().%s",
      collectBuildInformation()
    );
  }
  return activeEditorState;
}
function getActiveEditor() {
  if (activeEditor2 === null) {
    invariant(
      false,
      "Unable to find an active editor. This method can only be used synchronously during the callback of editor.update() or editor.read().%s",
      collectBuildInformation()
    );
  }
  return activeEditor2;
}
function collectBuildInformation() {
  let compatibleEditors = 0;
  const incompatibleEditors = /* @__PURE__ */ new Set();
  const thisVersion = LexicalEditor.version;
  if (typeof window !== "undefined") {
    for (const node of document.querySelectorAll("[contenteditable]")) {
      const editor = getEditorPropertyFromDOMNode(node);
      if (isLexicalEditor(editor)) {
        compatibleEditors++;
      } else if (editor) {
        let version = String(
          editor.constructor.version || "<0.17.1"
        );
        if (version === thisVersion) {
          version += " (separately built, likely a bundler configuration issue)";
        }
        incompatibleEditors.add(version);
      }
    }
  }
  let output = ` Detected on the page: ${compatibleEditors} compatible editor(s) with version ${thisVersion}`;
  if (incompatibleEditors.size) {
    output += ` and incompatible editors with versions ${Array.from(
      incompatibleEditors
    ).join(", ")}`;
  }
  return output;
}
function internalGetActiveEditor() {
  return activeEditor2;
}
function internalGetActiveEditorState() {
  return activeEditorState;
}
function $applyTransforms(editor, node, transformsCache) {
  const type = node.__type;
  const registeredNode = getRegisteredNodeOrThrow(editor, type);
  let transformsArr = transformsCache.get(type);
  if (transformsArr === void 0) {
    transformsArr = Array.from(registeredNode.transforms);
    transformsCache.set(type, transformsArr);
  }
  const transformsArrLength = transformsArr.length;
  for (let i = 0; i < transformsArrLength; i++) {
    transformsArr[i](node);
    if (!node.isAttached()) {
      break;
    }
  }
}
function $isNodeValidForTransform(node, compositionKey) {
  return node !== void 0 && // We don't want to transform nodes being composed
  node.__key !== compositionKey && node.isAttached();
}
function $normalizeAllDirtyTextNodes(editorState, editor) {
  const dirtyLeaves = editor._dirtyLeaves;
  const nodeMap = editorState._nodeMap;
  for (const nodeKey of dirtyLeaves) {
    const node = nodeMap.get(nodeKey);
    if ($isTextNode(node) && node.isAttached() && node.isSimpleText() && !node.isUnmergeable()) {
      $normalizeTextNode(node);
    }
  }
}
function $applyAllTransforms(editorState, editor) {
  const dirtyLeaves = editor._dirtyLeaves;
  const dirtyElements = editor._dirtyElements;
  const nodeMap = editorState._nodeMap;
  const compositionKey = $getCompositionKey();
  const transformsCache = /* @__PURE__ */ new Map();
  let untransformedDirtyLeaves = dirtyLeaves;
  let untransformedDirtyLeavesLength = untransformedDirtyLeaves.size;
  let untransformedDirtyElements = dirtyElements;
  let untransformedDirtyElementsLength = untransformedDirtyElements.size;
  while (untransformedDirtyLeavesLength > 0 || untransformedDirtyElementsLength > 0) {
    if (untransformedDirtyLeavesLength > 0) {
      editor._dirtyLeaves = /* @__PURE__ */ new Set();
      for (const nodeKey of untransformedDirtyLeaves) {
        const node = nodeMap.get(nodeKey);
        if ($isTextNode(node) && node.isAttached() && node.isSimpleText() && !node.isUnmergeable()) {
          $normalizeTextNode(node);
        }
        if (node !== void 0 && $isNodeValidForTransform(node, compositionKey)) {
          $applyTransforms(editor, node, transformsCache);
        }
        dirtyLeaves.add(nodeKey);
      }
      untransformedDirtyLeaves = editor._dirtyLeaves;
      untransformedDirtyLeavesLength = untransformedDirtyLeaves.size;
      if (untransformedDirtyLeavesLength > 0) {
        infiniteTransformCount++;
        continue;
      }
    }
    editor._dirtyLeaves = /* @__PURE__ */ new Set();
    editor._dirtyElements = /* @__PURE__ */ new Map();
    for (const currentUntransformedDirtyElement of untransformedDirtyElements) {
      const nodeKey = currentUntransformedDirtyElement[0];
      const intentionallyMarkedAsDirty = currentUntransformedDirtyElement[1];
      if (nodeKey !== "root" && !intentionallyMarkedAsDirty) {
        continue;
      }
      const node = nodeMap.get(nodeKey);
      if (node !== void 0 && $isNodeValidForTransform(node, compositionKey)) {
        $applyTransforms(editor, node, transformsCache);
      }
      dirtyElements.set(nodeKey, intentionallyMarkedAsDirty);
    }
    untransformedDirtyLeaves = editor._dirtyLeaves;
    untransformedDirtyLeavesLength = untransformedDirtyLeaves.size;
    untransformedDirtyElements = editor._dirtyElements;
    untransformedDirtyElementsLength = untransformedDirtyElements.size;
    infiniteTransformCount++;
  }
  editor._dirtyLeaves = dirtyLeaves;
  editor._dirtyElements = dirtyElements;
}
function $parseSerializedNode(serializedNode) {
  const internalSerializedNode = serializedNode;
  return $parseSerializedNodeImpl(
    internalSerializedNode,
    getActiveEditor()._nodes
  );
}
function $parseSerializedNodeImpl(serializedNode, registeredNodes) {
  const type = serializedNode.type;
  const registeredNode = registeredNodes.get(type);
  if (registeredNode === void 0) {
    invariant(false, 'parseEditorState: type "%s" + not found', type);
  }
  const nodeClass = registeredNode.klass;
  if (serializedNode.type !== nodeClass.getType()) {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .importJSON().",
      nodeClass.name
    );
  }
  const node = nodeClass.importJSON(serializedNode);
  const children = serializedNode.children;
  if ($isElementNode(node) && Array.isArray(children)) {
    for (let i = 0; i < children.length; i++) {
      const serializedJSONChildNode = children[i];
      const childNode = $parseSerializedNodeImpl(
        serializedJSONChildNode,
        registeredNodes
      );
      node.append(childNode);
    }
  }
  return node;
}
function parseEditorState(serializedEditorState, editor, updateFn) {
  const editorState = createEmptyEditorState();
  const previousActiveEditorState = activeEditorState;
  const previousReadOnlyMode = isReadOnlyMode;
  const previousActiveEditor = activeEditor2;
  const previousDirtyElements = editor._dirtyElements;
  const previousDirtyLeaves = editor._dirtyLeaves;
  const previousCloneNotNeeded = editor._cloneNotNeeded;
  const previousDirtyType = editor._dirtyType;
  editor._dirtyElements = /* @__PURE__ */ new Map();
  editor._dirtyLeaves = /* @__PURE__ */ new Set();
  editor._cloneNotNeeded = /* @__PURE__ */ new Set();
  editor._dirtyType = 0;
  activeEditorState = editorState;
  isReadOnlyMode = false;
  activeEditor2 = editor;
  try {
    const registeredNodes = editor._nodes;
    const serializedNode = serializedEditorState.root;
    $parseSerializedNodeImpl(serializedNode, registeredNodes);
    if (updateFn) {
      updateFn();
    }
    editorState._readOnly = true;
    if (__DEV__) {
      handleDEVOnlyPendingUpdateGuarantees(editorState);
    }
  } catch (error) {
    if (error instanceof Error) {
      editor._onError(error);
    }
  } finally {
    editor._dirtyElements = previousDirtyElements;
    editor._dirtyLeaves = previousDirtyLeaves;
    editor._cloneNotNeeded = previousCloneNotNeeded;
    editor._dirtyType = previousDirtyType;
    activeEditorState = previousActiveEditorState;
    isReadOnlyMode = previousReadOnlyMode;
    activeEditor2 = previousActiveEditor;
  }
  return editorState;
}
function readEditorState(editor, editorState, callbackFn) {
  const previousActiveEditorState = activeEditorState;
  const previousReadOnlyMode = isReadOnlyMode;
  const previousActiveEditor = activeEditor2;
  activeEditorState = editorState;
  isReadOnlyMode = true;
  activeEditor2 = editor;
  try {
    return callbackFn();
  } finally {
    activeEditorState = previousActiveEditorState;
    isReadOnlyMode = previousReadOnlyMode;
    activeEditor2 = previousActiveEditor;
  }
}
function handleDEVOnlyPendingUpdateGuarantees(pendingEditorState) {
  const nodeMap = pendingEditorState._nodeMap;
  nodeMap.set = () => {
    throw new Error("Cannot call set() on a frozen Lexical node map");
  };
  nodeMap.clear = () => {
    throw new Error("Cannot call clear() on a frozen Lexical node map");
  };
  nodeMap.delete = () => {
    throw new Error("Cannot call delete() on a frozen Lexical node map");
  };
}
function $commitPendingUpdates(editor, recoveryEditorState) {
  const pendingEditorState = editor._pendingEditorState;
  const rootElement = editor._rootElement;
  const shouldSkipDOM = editor._headless || rootElement === null;
  if (pendingEditorState === null) {
    return;
  }
  const currentEditorState = editor._editorState;
  const currentSelection = currentEditorState._selection;
  const pendingSelection = pendingEditorState._selection;
  const needsUpdate = editor._dirtyType !== NO_DIRTY_NODES;
  const previousActiveEditorState = activeEditorState;
  const previousReadOnlyMode = isReadOnlyMode;
  const previousActiveEditor = activeEditor2;
  const previouslyUpdating = editor._updating;
  const observer = editor._observer;
  let mutatedNodes2 = null;
  editor._pendingEditorState = null;
  editor._editorState = pendingEditorState;
  if (!shouldSkipDOM && needsUpdate && observer !== null) {
    activeEditor2 = editor;
    activeEditorState = pendingEditorState;
    isReadOnlyMode = false;
    editor._updating = true;
    try {
      const dirtyType = editor._dirtyType;
      const dirtyElements2 = editor._dirtyElements;
      const dirtyLeaves2 = editor._dirtyLeaves;
      observer.disconnect();
      mutatedNodes2 = $reconcileRoot(
        currentEditorState,
        pendingEditorState,
        editor,
        dirtyType,
        dirtyElements2,
        dirtyLeaves2
      );
    } catch (error) {
      if (error instanceof Error) {
        editor._onError(error);
      }
      if (!isAttemptingToRecoverFromReconcilerError) {
        resetEditor(editor, null, rootElement, pendingEditorState);
        initMutationObserver(editor);
        editor._dirtyType = FULL_RECONCILE;
        isAttemptingToRecoverFromReconcilerError = true;
        $commitPendingUpdates(editor, currentEditorState);
        isAttemptingToRecoverFromReconcilerError = false;
      } else {
        throw error;
      }
      return;
    } finally {
      observer.observe(rootElement, observerOptions);
      editor._updating = previouslyUpdating;
      activeEditorState = previousActiveEditorState;
      isReadOnlyMode = previousReadOnlyMode;
      activeEditor2 = previousActiveEditor;
    }
  }
  if (!pendingEditorState._readOnly) {
    pendingEditorState._readOnly = true;
    if (__DEV__) {
      handleDEVOnlyPendingUpdateGuarantees(pendingEditorState);
      if ($isRangeSelection(pendingSelection)) {
        Object.freeze(pendingSelection.anchor);
        Object.freeze(pendingSelection.focus);
      }
      Object.freeze(pendingSelection);
    }
  }
  const dirtyLeaves = editor._dirtyLeaves;
  const dirtyElements = editor._dirtyElements;
  const normalizedNodes = editor._normalizedNodes;
  const tags = editor._updateTags;
  const deferred = editor._deferred;
  const nodeCount = pendingEditorState._nodeMap.size;
  if (needsUpdate) {
    editor._dirtyType = NO_DIRTY_NODES;
    editor._cloneNotNeeded.clear();
    editor._dirtyLeaves = /* @__PURE__ */ new Set();
    editor._dirtyElements = /* @__PURE__ */ new Map();
    editor._normalizedNodes = /* @__PURE__ */ new Set();
    editor._updateTags = /* @__PURE__ */ new Set();
  }
  $garbageCollectDetachedDecorators(editor, pendingEditorState);
  const domSelection = shouldSkipDOM ? null : getDOMSelection(editor._window);
  if (editor._editable && // domSelection will be null in headless
  domSelection !== null && (needsUpdate || pendingSelection === null || pendingSelection.dirty)) {
    activeEditor2 = editor;
    activeEditorState = pendingEditorState;
    try {
      if (observer !== null) {
        observer.disconnect();
      }
      if (needsUpdate || pendingSelection === null || pendingSelection.dirty) {
        const blockCursorElement = editor._blockCursorElement;
        if (blockCursorElement !== null) {
          removeDOMBlockCursorElement(
            blockCursorElement,
            editor,
            rootElement
          );
        }
        updateDOMSelection(
          currentSelection,
          pendingSelection,
          editor,
          domSelection,
          tags,
          rootElement,
          nodeCount
        );
      }
      updateDOMBlockCursorElement(
        editor,
        rootElement,
        pendingSelection
      );
      if (observer !== null) {
        observer.observe(rootElement, observerOptions);
      }
    } finally {
      activeEditor2 = previousActiveEditor;
      activeEditorState = previousActiveEditorState;
    }
  }
  if (mutatedNodes2 !== null) {
    triggerMutationListeners(
      editor,
      mutatedNodes2,
      tags,
      dirtyLeaves,
      currentEditorState
    );
  }
  if (!$isRangeSelection(pendingSelection) && pendingSelection !== null && (currentSelection === null || !currentSelection.is(pendingSelection))) {
    editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
  }
  const pendingDecorators = editor._pendingDecorators;
  if (pendingDecorators !== null) {
    editor._decorators = pendingDecorators;
    editor._pendingDecorators = null;
    triggerListeners("decorator", editor, true, pendingDecorators);
  }
  triggerTextContentListeners(
    editor,
    recoveryEditorState || currentEditorState,
    pendingEditorState
  );
  triggerListeners("update", editor, true, {
    dirtyElements,
    dirtyLeaves,
    editorState: pendingEditorState,
    normalizedNodes,
    prevEditorState: recoveryEditorState || currentEditorState,
    tags
  });
  triggerDeferredUpdateCallbacks(editor, deferred);
  $triggerEnqueuedUpdates(editor);
}
function triggerTextContentListeners(editor, currentEditorState, pendingEditorState) {
  const currentTextContent = getEditorStateTextContent(currentEditorState);
  const latestTextContent = getEditorStateTextContent(pendingEditorState);
  if (currentTextContent !== latestTextContent) {
    triggerListeners("textcontent", editor, true, latestTextContent);
  }
}
function triggerMutationListeners(editor, mutatedNodes2, updateTags, dirtyLeaves, prevEditorState) {
  const listeners = Array.from(editor._listeners.mutation);
  const listenersLength = listeners.length;
  for (let i = 0; i < listenersLength; i++) {
    const [listener, klass] = listeners[i];
    const mutatedNodesByType = mutatedNodes2.get(klass);
    if (mutatedNodesByType !== void 0) {
      listener(mutatedNodesByType, {
        dirtyLeaves,
        prevEditorState,
        updateTags
      });
    }
  }
}
function triggerListeners(type, editor, isCurrentlyEnqueuingUpdates, ...payload) {
  const previouslyUpdating = editor._updating;
  editor._updating = isCurrentlyEnqueuingUpdates;
  try {
    const listeners = Array.from(editor._listeners[type]);
    for (let i = 0; i < listeners.length; i++) {
      listeners[i].apply(null, payload);
    }
  } finally {
    editor._updating = previouslyUpdating;
  }
}
function triggerCommandListeners(editor, type, payload) {
  if (editor._updating === false || activeEditor2 !== editor) {
    let returnVal = false;
    editor.update(() => {
      returnVal = triggerCommandListeners(editor, type, payload);
    });
    return returnVal;
  }
  const editors = getEditorsToPropagate(editor);
  for (let i = 4; i >= 0; i--) {
    for (let e = 0; e < editors.length; e++) {
      const currentEditor = editors[e];
      const commandListeners = currentEditor._commands;
      const listenerInPriorityOrder = commandListeners.get(type);
      if (listenerInPriorityOrder !== void 0) {
        const listenersSet = listenerInPriorityOrder[i];
        if (listenersSet !== void 0) {
          const listeners = Array.from(listenersSet);
          const listenersLength = listeners.length;
          for (let j = 0; j < listenersLength; j++) {
            if (listeners[j](payload, editor) === true) {
              return true;
            }
          }
        }
      }
    }
  }
  return false;
}
function $triggerEnqueuedUpdates(editor) {
  const queuedUpdates = editor._updates;
  if (queuedUpdates.length !== 0) {
    const queuedUpdate = queuedUpdates.shift();
    if (queuedUpdate) {
      const [updateFn, options] = queuedUpdate;
      $beginUpdate(editor, updateFn, options);
    }
  }
}
function triggerDeferredUpdateCallbacks(editor, deferred) {
  editor._deferred = [];
  if (deferred.length !== 0) {
    const previouslyUpdating = editor._updating;
    editor._updating = true;
    try {
      for (let i = 0; i < deferred.length; i++) {
        deferred[i]();
      }
    } finally {
      editor._updating = previouslyUpdating;
    }
  }
}
function processNestedUpdates(editor, initialSkipTransforms) {
  const queuedUpdates = editor._updates;
  let skipTransforms = initialSkipTransforms || false;
  while (queuedUpdates.length !== 0) {
    const queuedUpdate = queuedUpdates.shift();
    if (queuedUpdate) {
      const [nextUpdateFn, options] = queuedUpdate;
      let onUpdate;
      let tag;
      if (options !== void 0) {
        onUpdate = options.onUpdate;
        tag = options.tag;
        if (options.skipTransforms) {
          skipTransforms = true;
        }
        if (options.discrete) {
          const pendingEditorState = editor._pendingEditorState;
          invariant(
            pendingEditorState !== null,
            "Unexpected empty pending editor state on discrete nested update"
          );
          pendingEditorState._flushSync = true;
        }
        if (onUpdate) {
          editor._deferred.push(onUpdate);
        }
        if (tag) {
          editor._updateTags.add(tag);
        }
      }
      nextUpdateFn();
    }
  }
  return skipTransforms;
}
function $beginUpdate(editor, updateFn, options) {
  const updateTags = editor._updateTags;
  let onUpdate;
  let tag;
  let skipTransforms = false;
  let discrete = false;
  if (options !== void 0) {
    onUpdate = options.onUpdate;
    tag = options.tag;
    if (tag != null) {
      updateTags.add(tag);
    }
    skipTransforms = options.skipTransforms || false;
    discrete = options.discrete || false;
  }
  if (onUpdate) {
    editor._deferred.push(onUpdate);
  }
  const currentEditorState = editor._editorState;
  let pendingEditorState = editor._pendingEditorState;
  let editorStateWasCloned = false;
  if (pendingEditorState === null || pendingEditorState._readOnly) {
    pendingEditorState = editor._pendingEditorState = cloneEditorState(
      pendingEditorState || currentEditorState
    );
    editorStateWasCloned = true;
  }
  pendingEditorState._flushSync = discrete;
  const previousActiveEditorState = activeEditorState;
  const previousReadOnlyMode = isReadOnlyMode;
  const previousActiveEditor = activeEditor2;
  const previouslyUpdating = editor._updating;
  activeEditorState = pendingEditorState;
  isReadOnlyMode = false;
  editor._updating = true;
  activeEditor2 = editor;
  try {
    if (editorStateWasCloned) {
      if (editor._headless) {
        if (currentEditorState._selection !== null) {
          pendingEditorState._selection = currentEditorState._selection.clone();
        }
      } else {
        pendingEditorState._selection = $internalCreateSelection(editor);
      }
    }
    const startingCompositionKey = editor._compositionKey;
    updateFn();
    skipTransforms = processNestedUpdates(editor, skipTransforms);
    applySelectionTransforms(pendingEditorState, editor);
    if (editor._dirtyType !== NO_DIRTY_NODES) {
      if (skipTransforms) {
        $normalizeAllDirtyTextNodes(pendingEditorState, editor);
      } else {
        $applyAllTransforms(pendingEditorState, editor);
      }
      processNestedUpdates(editor);
      $garbageCollectDetachedNodes(
        currentEditorState,
        pendingEditorState,
        editor._dirtyLeaves,
        editor._dirtyElements
      );
    }
    const endingCompositionKey = editor._compositionKey;
    if (startingCompositionKey !== endingCompositionKey) {
      pendingEditorState._flushSync = true;
    }
    const pendingSelection = pendingEditorState._selection;
    if ($isRangeSelection(pendingSelection)) {
      const pendingNodeMap = pendingEditorState._nodeMap;
      const anchorKey = pendingSelection.anchor.key;
      const focusKey = pendingSelection.focus.key;
      if (pendingNodeMap.get(anchorKey) === void 0 || pendingNodeMap.get(focusKey) === void 0) {
        invariant(
          false,
          "updateEditor: selection has been lost because the previously selected nodes have been removed and selection wasn't moved to another node. Ensure selection changes after removing/replacing a selected node."
        );
      }
    } else if ($isNodeSelection(pendingSelection)) {
      if (pendingSelection._nodes.size === 0) {
        pendingEditorState._selection = null;
      }
    }
  } catch (error) {
    if (error instanceof Error) {
      editor._onError(error);
    }
    editor._pendingEditorState = currentEditorState;
    editor._dirtyType = FULL_RECONCILE;
    editor._cloneNotNeeded.clear();
    editor._dirtyLeaves = /* @__PURE__ */ new Set();
    editor._dirtyElements.clear();
    $commitPendingUpdates(editor);
    return;
  } finally {
    activeEditorState = previousActiveEditorState;
    isReadOnlyMode = previousReadOnlyMode;
    activeEditor2 = previousActiveEditor;
    editor._updating = previouslyUpdating;
    infiniteTransformCount = 0;
  }
  const shouldUpdate = editor._dirtyType !== NO_DIRTY_NODES || editorStateHasDirtySelection(pendingEditorState, editor);
  if (shouldUpdate) {
    if (pendingEditorState._flushSync) {
      pendingEditorState._flushSync = false;
      $commitPendingUpdates(editor);
    } else if (editorStateWasCloned) {
      scheduleMicroTask(() => {
        $commitPendingUpdates(editor);
      });
    }
  } else {
    pendingEditorState._flushSync = false;
    if (editorStateWasCloned) {
      updateTags.clear();
      editor._deferred = [];
      editor._pendingEditorState = null;
    }
  }
}
function updateEditor(editor, updateFn, options) {
  if (editor._updating) {
    editor._updates.push([updateFn, options]);
  } else {
    $beginUpdate(editor, updateFn, options);
  }
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalElementNode.ts
var ElementNode8 = class extends LexicalNode {
  constructor(key) {
    super(key);
    /** @internal */
    __publicField(this, "__first");
    /** @internal */
    __publicField(this, "__last");
    /** @internal */
    __publicField(this, "__size");
    /** @internal */
    __publicField(this, "__style");
    /** @internal */
    __publicField(this, "__dir");
    this.__first = null;
    this.__last = null;
    this.__size = 0;
    this.__style = "";
    this.__dir = null;
  }
  afterCloneFrom(prevNode) {
    super.afterCloneFrom(prevNode);
    this.__first = prevNode.__first;
    this.__last = prevNode.__last;
    this.__size = prevNode.__size;
    this.__style = prevNode.__style;
    this.__dir = prevNode.__dir;
  }
  getStyle() {
    const self = this.getLatest();
    return self.__style;
  }
  getChildren() {
    const children = [];
    let child = this.getFirstChild();
    while (child !== null) {
      children.push(child);
      child = child.getNextSibling();
    }
    return children;
  }
  getChildrenKeys() {
    const children = [];
    let child = this.getFirstChild();
    while (child !== null) {
      children.push(child.__key);
      child = child.getNextSibling();
    }
    return children;
  }
  getChildrenSize() {
    const self = this.getLatest();
    return self.__size;
  }
  isEmpty() {
    return this.getChildrenSize() === 0;
  }
  isDirty() {
    const editor = getActiveEditor();
    const dirtyElements = editor._dirtyElements;
    return dirtyElements !== null && dirtyElements.has(this.__key);
  }
  isLastChild() {
    const self = this.getLatest();
    const parentLastChild = this.getParentOrThrow().getLastChild();
    return parentLastChild !== null && parentLastChild.is(self);
  }
  getAllTextNodes() {
    const textNodes = [];
    let child = this.getFirstChild();
    while (child !== null) {
      if ($isTextNode(child)) {
        textNodes.push(child);
      }
      if ($isElementNode(child)) {
        const subChildrenNodes = child.getAllTextNodes();
        textNodes.push(...subChildrenNodes);
      }
      child = child.getNextSibling();
    }
    return textNodes;
  }
  getFirstDescendant() {
    let node = this.getFirstChild();
    while ($isElementNode(node)) {
      const child = node.getFirstChild();
      if (child === null) {
        break;
      }
      node = child;
    }
    return node;
  }
  getLastDescendant() {
    let node = this.getLastChild();
    while ($isElementNode(node)) {
      const child = node.getLastChild();
      if (child === null) {
        break;
      }
      node = child;
    }
    return node;
  }
  getDescendantByIndex(index) {
    const children = this.getChildren();
    const childrenLength = children.length;
    if (index >= childrenLength) {
      const resolvedNode2 = children[childrenLength - 1];
      return $isElementNode(resolvedNode2) && resolvedNode2.getLastDescendant() || resolvedNode2 || null;
    }
    const resolvedNode = children[index];
    return $isElementNode(resolvedNode) && resolvedNode.getFirstDescendant() || resolvedNode || null;
  }
  getFirstChild() {
    const self = this.getLatest();
    const firstKey = self.__first;
    return firstKey === null ? null : $getNodeByKey(firstKey);
  }
  getFirstChildOrThrow() {
    const firstChild = this.getFirstChild();
    if (firstChild === null) {
      invariant(false, "Expected node %s to have a first child.", this.__key);
    }
    return firstChild;
  }
  getLastChild() {
    const self = this.getLatest();
    const lastKey = self.__last;
    return lastKey === null ? null : $getNodeByKey(lastKey);
  }
  getLastChildOrThrow() {
    const lastChild = this.getLastChild();
    if (lastChild === null) {
      invariant(false, "Expected node %s to have a last child.", this.__key);
    }
    return lastChild;
  }
  getChildAtIndex(index) {
    const size = this.getChildrenSize();
    let node;
    let i;
    if (index < size / 2) {
      node = this.getFirstChild();
      i = 0;
      while (node !== null && i <= index) {
        if (i === index) {
          return node;
        }
        node = node.getNextSibling();
        i++;
      }
      return null;
    }
    node = this.getLastChild();
    i = size - 1;
    while (node !== null && i >= index) {
      if (i === index) {
        return node;
      }
      node = node.getPreviousSibling();
      i--;
    }
    return null;
  }
  getTextContent() {
    let textContent = "";
    const children = this.getChildren();
    const childrenLength = children.length;
    for (let i = 0; i < childrenLength; i++) {
      const child = children[i];
      textContent += child.getTextContent();
      if ($isElementNode(child) && i !== childrenLength - 1 && !child.isInline()) {
        textContent += DOUBLE_LINE_BREAK;
      }
    }
    return textContent;
  }
  getTextContentSize() {
    let textContentSize = 0;
    const children = this.getChildren();
    const childrenLength = children.length;
    for (let i = 0; i < childrenLength; i++) {
      const child = children[i];
      textContentSize += child.getTextContentSize();
      if ($isElementNode(child) && i !== childrenLength - 1 && !child.isInline()) {
        textContentSize += DOUBLE_LINE_BREAK.length;
      }
    }
    return textContentSize;
  }
  getDirection() {
    const self = this.getLatest();
    return self.__dir;
  }
  // Mutators
  select(_anchorOffset, _focusOffset) {
    errorOnReadOnly();
    const selection = $getSelection();
    let anchorOffset = _anchorOffset;
    let focusOffset = _focusOffset;
    const childrenCount = this.getChildrenSize();
    if (!this.canBeEmpty()) {
      if (_anchorOffset === 0 && _focusOffset === 0) {
        const firstChild = this.getFirstChild();
        if ($isTextNode(firstChild) || $isElementNode(firstChild)) {
          return firstChild.select(0, 0);
        }
      } else if ((_anchorOffset === void 0 || _anchorOffset === childrenCount) && (_focusOffset === void 0 || _focusOffset === childrenCount)) {
        const lastChild = this.getLastChild();
        if ($isTextNode(lastChild) || $isElementNode(lastChild)) {
          return lastChild.select();
        }
      }
    }
    if (anchorOffset === void 0) {
      anchorOffset = childrenCount;
    }
    if (focusOffset === void 0) {
      focusOffset = childrenCount;
    }
    const key = this.__key;
    if (!$isRangeSelection(selection)) {
      return $internalMakeRangeSelection(
        key,
        anchorOffset,
        key,
        focusOffset,
        "element",
        "element"
      );
    } else {
      selection.anchor.set(key, anchorOffset, "element");
      selection.focus.set(key, focusOffset, "element");
      selection.dirty = true;
    }
    return selection;
  }
  selectStart() {
    const firstNode = this.getFirstDescendant();
    return firstNode ? firstNode.selectStart() : this.select();
  }
  selectEnd() {
    const lastNode = this.getLastDescendant();
    return lastNode ? lastNode.selectEnd() : this.select();
  }
  clear() {
    const writableSelf = this.getWritable();
    const children = this.getChildren();
    children.forEach((child) => child.remove());
    return writableSelf;
  }
  append(...nodesToAppend) {
    return this.splice(this.getChildrenSize(), 0, nodesToAppend);
  }
  setDirection(direction) {
    const self = this.getWritable();
    self.__dir = direction;
    return self;
  }
  setStyle(style) {
    const self = this.getWritable();
    self.__style = style || "";
    return this;
  }
  splice(start, deleteCount, nodesToInsert) {
    const nodesToInsertLength = nodesToInsert.length;
    const oldSize = this.getChildrenSize();
    const writableSelf = this.getWritable();
    const writableSelfKey = writableSelf.__key;
    const nodesToInsertKeys = [];
    const nodesToRemoveKeys = [];
    const nodeAfterRange = this.getChildAtIndex(start + deleteCount);
    let nodeBeforeRange = null;
    let newSize = oldSize - deleteCount + nodesToInsertLength;
    if (start !== 0) {
      if (start === oldSize) {
        nodeBeforeRange = this.getLastChild();
      } else {
        const node = this.getChildAtIndex(start);
        if (node !== null) {
          nodeBeforeRange = node.getPreviousSibling();
        }
      }
    }
    if (deleteCount > 0) {
      let nodeToDelete = nodeBeforeRange === null ? this.getFirstChild() : nodeBeforeRange.getNextSibling();
      for (let i = 0; i < deleteCount; i++) {
        if (nodeToDelete === null) {
          invariant(false, "splice: sibling not found");
        }
        const nextSibling = nodeToDelete.getNextSibling();
        const nodeKeyToDelete = nodeToDelete.__key;
        const writableNodeToDelete = nodeToDelete.getWritable();
        removeFromParent(writableNodeToDelete);
        nodesToRemoveKeys.push(nodeKeyToDelete);
        nodeToDelete = nextSibling;
      }
    }
    let prevNode = nodeBeforeRange;
    for (let i = 0; i < nodesToInsertLength; i++) {
      const nodeToInsert = nodesToInsert[i];
      if (prevNode !== null && nodeToInsert.is(prevNode)) {
        nodeBeforeRange = prevNode = prevNode.getPreviousSibling();
      }
      const writableNodeToInsert = nodeToInsert.getWritable();
      if (writableNodeToInsert.__parent === writableSelfKey) {
        newSize--;
      }
      removeFromParent(writableNodeToInsert);
      const nodeKeyToInsert = nodeToInsert.__key;
      if (prevNode === null) {
        writableSelf.__first = nodeKeyToInsert;
        writableNodeToInsert.__prev = null;
      } else {
        const writablePrevNode = prevNode.getWritable();
        writablePrevNode.__next = nodeKeyToInsert;
        writableNodeToInsert.__prev = writablePrevNode.__key;
      }
      if (nodeToInsert.__key === writableSelfKey) {
        invariant(false, "append: attempting to append self");
      }
      writableNodeToInsert.__parent = writableSelfKey;
      nodesToInsertKeys.push(nodeKeyToInsert);
      prevNode = nodeToInsert;
    }
    if (start + deleteCount === oldSize) {
      if (prevNode !== null) {
        const writablePrevNode = prevNode.getWritable();
        writablePrevNode.__next = null;
        writableSelf.__last = prevNode.__key;
      }
    } else if (nodeAfterRange !== null) {
      const writableNodeAfterRange = nodeAfterRange.getWritable();
      if (prevNode !== null) {
        const writablePrevNode = prevNode.getWritable();
        writableNodeAfterRange.__prev = prevNode.__key;
        writablePrevNode.__next = nodeAfterRange.__key;
      } else {
        writableNodeAfterRange.__prev = null;
      }
    }
    writableSelf.__size = newSize;
    if (nodesToRemoveKeys.length) {
      const selection = $getSelection();
      if ($isRangeSelection(selection)) {
        const nodesToRemoveKeySet = new Set(nodesToRemoveKeys);
        const nodesToInsertKeySet = new Set(nodesToInsertKeys);
        const { anchor, focus } = selection;
        if (isPointRemoved(anchor, nodesToRemoveKeySet, nodesToInsertKeySet)) {
          moveSelectionPointToSibling(
            anchor,
            anchor.getNode(),
            this,
            nodeBeforeRange,
            nodeAfterRange
          );
        }
        if (isPointRemoved(focus, nodesToRemoveKeySet, nodesToInsertKeySet)) {
          moveSelectionPointToSibling(
            focus,
            focus.getNode(),
            this,
            nodeBeforeRange,
            nodeAfterRange
          );
        }
        if (newSize === 0 && !this.canBeEmpty() && !$isRootOrShadowRoot(this)) {
          this.remove();
        }
      }
    }
    return writableSelf;
  }
  // JSON serialization
  exportJSON() {
    return {
      children: [],
      direction: this.getDirection(),
      type: "element",
      version: 1
    };
  }
  // These are intended to be extends for specific element heuristics.
  insertNewAfter(selection, restoreSelection) {
    return null;
  }
  canIndent() {
    return true;
  }
  /*
   * This method controls the behavior of a the node during backwards
   * deletion (i.e., backspace) when selection is at the beginning of
   * the node (offset 0)
   */
  collapseAtStart(selection) {
    return false;
  }
  excludeFromCopy(destination) {
    return false;
  }
  /** @deprecated @internal */
  canReplaceWith(replacement) {
    return true;
  }
  /** @deprecated @internal */
  canInsertAfter(node) {
    return true;
  }
  canBeEmpty() {
    return true;
  }
  canInsertTextBefore() {
    return true;
  }
  canInsertTextAfter() {
    return true;
  }
  isInline() {
    return false;
  }
  // A shadow root is a Node that behaves like RootNode. The shadow root (and RootNode) mark the
  // end of the hiercharchy, most implementations should treat it as there's nothing (upwards)
  // beyond this point. For example, node.getTopLevelElement(), when performed inside a TableCellNode
  // will return the immediate first child underneath TableCellNode instead of RootNode.
  isShadowRoot() {
    return false;
  }
  /** @deprecated @internal */
  canMergeWith(node) {
    return false;
  }
  extractWithChild(child, selection, destination) {
    return false;
  }
  /**
   * Determines whether this node, when empty, can merge with a first block
   * of nodes being inserted.
   *
   * This method is specifically called in {@link RangeSelection.insertNodes}
   * to determine merging behavior during nodes insertion.
   *
   * @example
   * // In a ListItemNode or QuoteNode implementation:
   * canMergeWhenEmpty(): true {
   *  return true;
   * }
   */
  canMergeWhenEmpty() {
    return false;
  }
};
function $isElementNode(node) {
  return node instanceof ElementNode8;
}
function isPointRemoved(point, nodesToRemoveKeySet, nodesToInsertKeySet) {
  let node = point.getNode();
  while (node) {
    const nodeKey = node.__key;
    if (nodesToRemoveKeySet.has(nodeKey) && !nodesToInsertKeySet.has(nodeKey)) {
      return true;
    }
    node = node.getParent();
  }
  return false;
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalDecoratorNode.ts
var DecoratorNode3 = class extends LexicalNode {
  constructor(key) {
    super(key);
  }
  /**
   * The returned value is added to the LexicalEditor._decorators
   */
  decorate(editor, config) {
    invariant(false, "decorate: base method not extended");
  }
  isIsolated() {
    return false;
  }
  isInline() {
    return true;
  }
  isKeyboardSelectable() {
    return true;
  }
};
function $isDecoratorNode(node) {
  return node instanceof DecoratorNode3;
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalRootNode.ts
var RootNode = class _RootNode extends ElementNode8 {
  constructor() {
    super("root");
    /** @internal */
    __publicField(this, "__cachedText");
    this.__cachedText = null;
  }
  static getType() {
    return "root";
  }
  static clone() {
    return new _RootNode();
  }
  getTopLevelElementOrThrow() {
    invariant(
      false,
      "getTopLevelElementOrThrow: root nodes are not top level elements"
    );
  }
  getTextContent() {
    const cachedText = this.__cachedText;
    if (isCurrentlyReadOnlyMode() || getActiveEditor()._dirtyType === NO_DIRTY_NODES) {
      if (cachedText !== null) {
        return cachedText;
      }
    }
    return super.getTextContent();
  }
  remove() {
    invariant(false, "remove: cannot be called on root nodes");
  }
  replace(node) {
    invariant(false, "replace: cannot be called on root nodes");
  }
  insertBefore(nodeToInsert) {
    invariant(false, "insertBefore: cannot be called on root nodes");
  }
  insertAfter(nodeToInsert) {
    invariant(false, "insertAfter: cannot be called on root nodes");
  }
  // View
  updateDOM(prevNode, dom) {
    return false;
  }
  // Mutate
  append(...nodesToAppend) {
    for (let i = 0; i < nodesToAppend.length; i++) {
      const node = nodesToAppend[i];
      if (!$isElementNode(node) && !$isDecoratorNode(node)) {
        invariant(
          false,
          "rootNode.append: Only element or decorator nodes can be appended to the root node"
        );
      }
    }
    return super.append(...nodesToAppend);
  }
  static importJSON(serializedNode) {
    const node = $getRoot();
    node.setDirection(serializedNode.direction);
    return node;
  }
  exportJSON() {
    return {
      children: [],
      direction: this.getDirection(),
      type: "root",
      version: 1
    };
  }
  collapseAtStart() {
    return true;
  }
};
function $createRootNode() {
  return new RootNode();
}
function $isRootNode(node) {
  return node instanceof RootNode;
}

// resources/js/wysiwyg/lexical/core/LexicalEditorState.ts
function editorStateHasDirtySelection(editorState, editor) {
  const currentSelection = editor.getEditorState()._selection;
  const pendingSelection = editorState._selection;
  if (pendingSelection !== null) {
    if (pendingSelection.dirty || !pendingSelection.is(currentSelection)) {
      return true;
    }
  } else if (currentSelection !== null) {
    return true;
  }
  return false;
}
function cloneEditorState(current) {
  return new EditorState3(new Map(current._nodeMap));
}
function createEmptyEditorState() {
  return new EditorState3(/* @__PURE__ */ new Map([["root", $createRootNode()]]));
}
function exportNodeToJSON(node) {
  const serializedNode = node.exportJSON();
  const nodeClass = node.constructor;
  if (serializedNode.type !== nodeClass.getType()) {
    invariant(
      false,
      "LexicalNode: Node %s does not match the serialized type. Check if .exportJSON() is implemented and it is returning the correct type.",
      nodeClass.name
    );
  }
  if ($isElementNode(node)) {
    const serializedChildren = serializedNode.children;
    if (!Array.isArray(serializedChildren)) {
      invariant(
        false,
        "LexicalNode: Node %s is an element but .exportJSON() does not have a children array.",
        nodeClass.name
      );
    }
    const children = node.getChildren();
    for (let i = 0; i < children.length; i++) {
      const child = children[i];
      const serializedChildNode = exportNodeToJSON(child);
      serializedChildren.push(serializedChildNode);
    }
  }
  return serializedNode;
}
var EditorState3 = class _EditorState {
  constructor(nodeMap, selection) {
    __publicField(this, "_nodeMap");
    __publicField(this, "_selection");
    __publicField(this, "_flushSync");
    __publicField(this, "_readOnly");
    this._nodeMap = nodeMap;
    this._selection = selection || null;
    this._flushSync = false;
    this._readOnly = false;
  }
  isEmpty() {
    return this._nodeMap.size === 1 && this._selection === null;
  }
  read(callbackFn, options) {
    return readEditorState(
      options && options.editor || null,
      this,
      callbackFn
    );
  }
  clone(selection) {
    const editorState = new _EditorState(
      this._nodeMap,
      selection === void 0 ? this._selection : selection
    );
    editorState._readOnly = true;
    return editorState;
  }
  toJSON() {
    return readEditorState(null, this, () => ({
      root: exportNodeToJSON($getRoot())
    }));
  }
};

// resources/js/wysiwyg/lexical/core/nodes/ArtificialNode.ts
var ArtificialNode__DO_NOT_USE = class extends ElementNode8 {
  static getType() {
    return "artificial";
  }
  createDOM(config) {
    const dom = document.createElement("div");
    return dom;
  }
};

// resources/js/wysiwyg/lexical/core/nodes/common.ts
var validAlignments = ["left", "right", "center", "justify"];
function extractAlignmentFromElement(element) {
  const textAlignStyle = element.style.textAlign || "";
  if (validAlignments.includes(textAlignStyle)) {
    return textAlignStyle;
  }
  if (element.classList.contains("align-left")) {
    return "left";
  } else if (element.classList.contains("align-right")) {
    return "right";
  } else if (element.classList.contains("align-center")) {
    return "center";
  } else if (element.classList.contains("align-justify")) {
    return "justify";
  }
  return "";
}
function extractInsetFromElement(element) {
  const elemPadding = element.style.paddingLeft || "0";
  return sizeToPixels(elemPadding);
}
function extractDirectionFromElement(element) {
  const elemDir = (element.dir || "").toLowerCase();
  if (elemDir === "rtl" || elemDir === "ltr") {
    return elemDir;
  }
  return null;
}
function setCommonBlockPropsFromElement(element, node) {
  if (element.id) {
    node.setId(element.id);
  }
  node.setAlignment(extractAlignmentFromElement(element));
  node.setInset(extractInsetFromElement(element));
  node.setDirection(extractDirectionFromElement(element));
}
function commonPropertiesDifferent(nodeA, nodeB) {
  return nodeA.__id !== nodeB.__id || nodeA.__alignment !== nodeB.__alignment || nodeA.__inset !== nodeB.__inset || nodeA.__dir !== nodeB.__dir;
}
function applyCommonPropertyChanges(prevNode, currentNode, element) {
  if (prevNode.__id !== currentNode.__id) {
    element.setAttribute("id", currentNode.__id);
  }
  if (prevNode.__alignment !== currentNode.__alignment) {
    for (const alignment of validAlignments) {
      element.classList.remove("align-" + alignment);
    }
    if (currentNode.__alignment) {
      element.classList.add("align-" + currentNode.__alignment);
    }
  }
  if (prevNode.__inset !== currentNode.__inset) {
    if (currentNode.__inset) {
      element.style.paddingLeft = `${currentNode.__inset}px`;
    } else {
      element.style.removeProperty("paddingLeft");
    }
  }
  if (prevNode.__dir !== currentNode.__dir) {
    if (currentNode.__dir) {
      element.dir = currentNode.__dir;
    } else {
      element.removeAttribute("dir");
    }
  }
}
function updateElementWithCommonBlockProps(element, node) {
  if (node.__id) {
    element.setAttribute("id", node.__id);
  }
  if (node.__alignment) {
    element.classList.add("align-" + node.__alignment);
  }
  if (node.__inset) {
    element.style.paddingLeft = `${node.__inset}px`;
  }
  if (node.__dir) {
    element.dir = node.__dir;
  }
}
function deserializeCommonBlockNode(serializedNode, node) {
  node.setId(serializedNode.id);
  node.setAlignment(serializedNode.alignment);
  node.setInset(serializedNode.inset);
  node.setDirection(serializedNode.direction);
}

// resources/js/wysiwyg/lexical/core/nodes/CommonBlockNode.ts
var CommonBlockNode = class extends ElementNode8 {
  constructor() {
    super(...arguments);
    __publicField(this, "__id", "");
    __publicField(this, "__alignment", "");
    __publicField(this, "__inset", 0);
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  setAlignment(alignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }
  getAlignment() {
    const self = this.getLatest();
    return self.__alignment;
  }
  setInset(size) {
    const self = this.getWritable();
    self.__inset = size;
  }
  getInset() {
    const self = this.getLatest();
    return self.__inset;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      id: this.__id,
      alignment: this.__alignment,
      inset: this.__inset
    };
  }
};
function copyCommonBlockProperties(from, to) {
  to.__alignment = from.__alignment;
  to.__inset = from.__inset;
}

// resources/js/wysiwyg/lexical/core/nodes/LexicalParagraphNode.ts
var ParagraphNode = class _ParagraphNode extends CommonBlockNode {
  constructor(key) {
    super(key);
    /** @internal */
    __publicField(this, "__textFormat");
    __publicField(this, "__textStyle");
    this.__textFormat = 0;
    this.__textStyle = "";
  }
  static getType() {
    return "paragraph";
  }
  getTextFormat() {
    const self = this.getLatest();
    return self.__textFormat;
  }
  setTextFormat(type) {
    const self = this.getWritable();
    self.__textFormat = type;
    return self;
  }
  hasTextFormat(type) {
    const formatFlag = TEXT_TYPE_TO_FORMAT[type];
    return (this.getTextFormat() & formatFlag) !== 0;
  }
  getTextStyle() {
    const self = this.getLatest();
    return self.__textStyle;
  }
  setTextStyle(style) {
    const self = this.getWritable();
    self.__textStyle = style;
    return self;
  }
  static clone(node) {
    return new _ParagraphNode(node.__key);
  }
  afterCloneFrom(prevNode) {
    super.afterCloneFrom(prevNode);
    this.__textFormat = prevNode.__textFormat;
    this.__textStyle = prevNode.__textStyle;
    copyCommonBlockProperties(prevNode, this);
  }
  // View
  createDOM(config) {
    const dom = document.createElement("p");
    const classNames = getCachedClassNameArray(config.theme, "paragraph");
    if (classNames !== void 0) {
      const domClassList = dom.classList;
      domClassList.add(...classNames);
    }
    updateElementWithCommonBlockProps(dom, this);
    return dom;
  }
  updateDOM(prevNode, dom, config) {
    return commonPropertiesDifferent(prevNode, this);
  }
  static importDOM() {
    return {
      p: (node) => ({
        conversion: $convertParagraphElement,
        priority: 0
      })
    };
  }
  exportDOM(editor) {
    const { element } = super.exportDOM(editor);
    if (element && isHTMLElement(element)) {
      if (this.isEmpty()) {
        element.append(document.createElement("br"));
      }
    }
    return {
      element
    };
  }
  static importJSON(serializedNode) {
    const node = $createParagraphNode();
    deserializeCommonBlockNode(serializedNode, node);
    node.setTextFormat(serializedNode.textFormat);
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      textFormat: this.getTextFormat(),
      textStyle: this.getTextStyle(),
      type: "paragraph",
      version: 1
    };
  }
  // Mutation
  insertNewAfter(rangeSelection, restoreSelection) {
    const newElement = $createParagraphNode();
    newElement.setTextFormat(rangeSelection.format);
    newElement.setTextStyle(rangeSelection.style);
    const direction = this.getDirection();
    newElement.setDirection(direction);
    newElement.setStyle(this.getTextStyle());
    this.insertAfter(newElement, restoreSelection);
    return newElement;
  }
  collapseAtStart() {
    const children = this.getChildren();
    if (children.length === 0 || $isTextNode(children[0]) && children[0].getTextContent().trim() === "") {
      const nextSibling = this.getNextSibling();
      if (nextSibling !== null) {
        this.selectNext();
        this.remove();
        return true;
      }
      const prevSibling = this.getPreviousSibling();
      if (prevSibling !== null) {
        this.selectPrevious();
        this.remove();
        return true;
      }
    }
    return false;
  }
};
function $convertParagraphElement(element) {
  const node = $createParagraphNode();
  setCommonBlockPropsFromElement(element, node);
  return { node };
}
function $createParagraphNode() {
  return $applyNodeReplacement(new ParagraphNode());
}
function $isParagraphNode(node) {
  return node instanceof ParagraphNode;
}

// resources/js/wysiwyg/lexical/core/LexicalEditor.ts
var DEFAULT_SKIP_INITIALIZATION = true;
var COMMAND_PRIORITY_EDITOR = 0;
var COMMAND_PRIORITY_LOW = 1;
var COMMAND_PRIORITY_NORMAL = 2;
var COMMAND_PRIORITY_HIGH = 3;
var COMMAND_PRIORITY_CRITICAL = 4;
function resetEditor(editor, prevRootElement, nextRootElement, pendingEditorState) {
  const keyNodeMap = editor._keyToDOMMap;
  keyNodeMap.clear();
  editor._editorState = createEmptyEditorState();
  editor._pendingEditorState = pendingEditorState;
  editor._compositionKey = null;
  editor._dirtyType = NO_DIRTY_NODES;
  editor._cloneNotNeeded.clear();
  editor._dirtyLeaves = /* @__PURE__ */ new Set();
  editor._dirtyElements.clear();
  editor._normalizedNodes = /* @__PURE__ */ new Set();
  editor._updateTags = /* @__PURE__ */ new Set();
  editor._updates = [];
  editor._blockCursorElement = null;
  const observer = editor._observer;
  if (observer !== null) {
    observer.disconnect();
    editor._observer = null;
  }
  if (prevRootElement !== null) {
    prevRootElement.textContent = "";
  }
  if (nextRootElement !== null) {
    nextRootElement.textContent = "";
    keyNodeMap.set("root", nextRootElement);
  }
}
function initializeConversionCache(nodes, additionalConversions) {
  const conversionCache = /* @__PURE__ */ new Map();
  const handledConversions = /* @__PURE__ */ new Set();
  const addConversionsToCache = (map) => {
    Object.keys(map).forEach((key) => {
      let currentCache = conversionCache.get(key);
      if (currentCache === void 0) {
        currentCache = [];
        conversionCache.set(key, currentCache);
      }
      currentCache.push(map[key]);
    });
  };
  nodes.forEach((node) => {
    const importDOM = node.klass.importDOM;
    if (importDOM == null || handledConversions.has(importDOM)) {
      return;
    }
    handledConversions.add(importDOM);
    const map = importDOM.call(node.klass);
    if (map !== null) {
      addConversionsToCache(map);
    }
  });
  if (additionalConversions) {
    addConversionsToCache(additionalConversions);
  }
  return conversionCache;
}
function createEditor(editorConfig) {
  const config = editorConfig || {};
  const activeEditor3 = internalGetActiveEditor();
  const theme2 = config.theme || {};
  const parentEditor = editorConfig === void 0 ? activeEditor3 : config.parentEditor || null;
  const disableEvents = config.disableEvents || false;
  const editorState = createEmptyEditorState();
  const namespace = config.namespace || (parentEditor !== null ? parentEditor._config.namespace : createUID());
  const initialEditorState = config.editorState;
  const nodes = [
    RootNode,
    TextNode,
    LineBreakNode2,
    TabNode,
    ParagraphNode,
    ArtificialNode__DO_NOT_USE,
    ...config.nodes || []
  ];
  const { onError, html } = config;
  const isEditable = config.editable !== void 0 ? config.editable : true;
  let registeredNodes;
  if (editorConfig === void 0 && activeEditor3 !== null) {
    registeredNodes = activeEditor3._nodes;
  } else {
    registeredNodes = /* @__PURE__ */ new Map();
    for (let i = 0; i < nodes.length; i++) {
      let klass = nodes[i];
      let replace = null;
      let replaceWithKlass = null;
      if (typeof klass !== "function") {
        const options = klass;
        klass = options.replace;
        replace = options.with;
        replaceWithKlass = options.withKlass || null;
      }
      if (__DEV__) {
        const nodeType = Object.prototype.hasOwnProperty.call(klass, "getType") && klass.getType();
        const name = klass.name;
        if (replaceWithKlass) {
          invariant(
            replaceWithKlass.prototype instanceof klass,
            "%s doesn't extend the %s",
            replaceWithKlass.name,
            name
          );
        }
        if (name !== "RootNode" && nodeType !== "root" && nodeType !== "artificial") {
          const proto = klass.prototype;
          ["getType", "clone"].forEach((method) => {
            if (!klass.hasOwnProperty(method)) {
              console.warn(`${name} must implement static "${method}" method`);
            }
          });
          if (
            // eslint-disable-next-line no-prototype-builtins
            !klass.hasOwnProperty("importDOM") && // eslint-disable-next-line no-prototype-builtins
            klass.hasOwnProperty("exportDOM")
          ) {
            console.warn(
              `${name} should implement "importDOM" if using a custom "exportDOM" method to ensure HTML serialization (important for copy & paste) works as expected`
            );
          }
          if (proto instanceof DecoratorNode3) {
            if (!proto.hasOwnProperty("decorate")) {
              console.warn(
                `${proto.constructor.name} must implement "decorate" method`
              );
            }
          }
          if (
            // eslint-disable-next-line no-prototype-builtins
            !klass.hasOwnProperty("importJSON")
          ) {
            console.warn(
              `${name} should implement "importJSON" method to ensure JSON and default HTML serialization works as expected`
            );
          }
          if (
            // eslint-disable-next-line no-prototype-builtins
            !proto.hasOwnProperty("exportJSON")
          ) {
            console.warn(
              `${name} should implement "exportJSON" method to ensure JSON and default HTML serialization works as expected`
            );
          }
        }
      }
      const type = klass.getType();
      const transform = klass.transform();
      const transforms = /* @__PURE__ */ new Set();
      if (transform !== null) {
        transforms.add(transform);
      }
      registeredNodes.set(type, {
        exportDOM: html && html.export ? html.export.get(klass) : void 0,
        klass,
        replace,
        replaceWithKlass,
        transforms
      });
    }
  }
  const editor = new LexicalEditor(
    editorState,
    parentEditor,
    registeredNodes,
    {
      disableEvents,
      namespace,
      theme: theme2
    },
    onError ? onError : console.error,
    initializeConversionCache(registeredNodes, html ? html.import : void 0),
    isEditable
  );
  if (initialEditorState !== void 0) {
    editor._pendingEditorState = initialEditorState;
    editor._dirtyType = FULL_RECONCILE;
  }
  return editor;
}
var LexicalEditor = class {
  /** @internal */
  constructor(editorState, parentEditor, nodes, config, onError, htmlConversions, editable) {
    __publicField(this, "constructor");
    /** @internal */
    __publicField(this, "_headless");
    /** @internal */
    __publicField(this, "_parentEditor");
    /** @internal */
    __publicField(this, "_rootElement");
    /** @internal */
    __publicField(this, "_editorState");
    /** @internal */
    __publicField(this, "_pendingEditorState");
    /** @internal */
    __publicField(this, "_compositionKey");
    /** @internal */
    __publicField(this, "_deferred");
    /** @internal */
    __publicField(this, "_keyToDOMMap");
    /** @internal */
    __publicField(this, "_updates");
    /** @internal */
    __publicField(this, "_updating");
    /** @internal */
    __publicField(this, "_listeners");
    /** @internal */
    __publicField(this, "_commands");
    /** @internal */
    __publicField(this, "_nodes");
    /** @internal */
    __publicField(this, "_decorators");
    /** @internal */
    __publicField(this, "_pendingDecorators");
    /** @internal */
    __publicField(this, "_config");
    /** @internal */
    __publicField(this, "_dirtyType");
    /** @internal */
    __publicField(this, "_cloneNotNeeded");
    /** @internal */
    __publicField(this, "_dirtyLeaves");
    /** @internal */
    __publicField(this, "_dirtyElements");
    /** @internal */
    __publicField(this, "_normalizedNodes");
    /** @internal */
    __publicField(this, "_updateTags");
    /** @internal */
    __publicField(this, "_observer");
    /** @internal */
    __publicField(this, "_key");
    /** @internal */
    __publicField(this, "_onError");
    /** @internal */
    __publicField(this, "_htmlConversions");
    /** @internal */
    __publicField(this, "_window");
    /** @internal */
    __publicField(this, "_editable");
    /** @internal */
    __publicField(this, "_blockCursorElement");
    this._parentEditor = parentEditor;
    this._rootElement = null;
    this._editorState = editorState;
    this._pendingEditorState = null;
    this._compositionKey = null;
    this._deferred = [];
    this._keyToDOMMap = /* @__PURE__ */ new Map();
    this._updates = [];
    this._updating = false;
    this._listeners = {
      decorator: /* @__PURE__ */ new Set(),
      editable: /* @__PURE__ */ new Set(),
      mutation: /* @__PURE__ */ new Map(),
      root: /* @__PURE__ */ new Set(),
      textcontent: /* @__PURE__ */ new Set(),
      update: /* @__PURE__ */ new Set()
    };
    this._commands = /* @__PURE__ */ new Map();
    this._config = config;
    this._nodes = nodes;
    this._decorators = {};
    this._pendingDecorators = null;
    this._dirtyType = NO_DIRTY_NODES;
    this._cloneNotNeeded = /* @__PURE__ */ new Set();
    this._dirtyLeaves = /* @__PURE__ */ new Set();
    this._dirtyElements = /* @__PURE__ */ new Map();
    this._normalizedNodes = /* @__PURE__ */ new Set();
    this._updateTags = /* @__PURE__ */ new Set();
    this._observer = null;
    this._key = createUID();
    this._onError = onError;
    this._htmlConversions = htmlConversions;
    this._editable = editable;
    this._headless = parentEditor !== null && parentEditor._headless;
    this._window = null;
    this._blockCursorElement = null;
  }
  /**
   *
   * @returns true if the editor is currently in "composition" mode due to receiving input
   * through an IME, or 3P extension, for example. Returns false otherwise.
   */
  isComposing() {
    return this._compositionKey != null;
  }
  /**
   * Registers a listener for Editor update event. Will trigger the provided callback
   * each time the editor goes through an update (via {@link LexicalEditor.update}) until the
   * teardown function is called.
   *
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerUpdateListener(listener) {
    const listenerSetOrMap = this._listeners.update;
    listenerSetOrMap.add(listener);
    return () => {
      listenerSetOrMap.delete(listener);
    };
  }
  /**
   * Registers a listener for for when the editor changes between editable and non-editable states.
   * Will trigger the provided callback each time the editor transitions between these states until the
   * teardown function is called.
   *
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerEditableListener(listener) {
    const listenerSetOrMap = this._listeners.editable;
    listenerSetOrMap.add(listener);
    return () => {
      listenerSetOrMap.delete(listener);
    };
  }
  /**
   * Registers a listener for when the editor's decorator object changes. The decorator object contains
   * all DecoratorNode keys -> their decorated value. This is primarily used with external UI frameworks.
   *
   * Will trigger the provided callback each time the editor transitions between these states until the
   * teardown function is called.
   *
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerDecoratorListener(listener) {
    const listenerSetOrMap = this._listeners.decorator;
    listenerSetOrMap.add(listener);
    return () => {
      listenerSetOrMap.delete(listener);
    };
  }
  /**
   * Registers a listener for when Lexical commits an update to the DOM and the text content of
   * the editor changes from the previous state of the editor. If the text content is the
   * same between updates, no notifications to the listeners will happen.
   *
   * Will trigger the provided callback each time the editor transitions between these states until the
   * teardown function is called.
   *
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerTextContentListener(listener) {
    const listenerSetOrMap = this._listeners.textcontent;
    listenerSetOrMap.add(listener);
    return () => {
      listenerSetOrMap.delete(listener);
    };
  }
  /**
   * Registers a listener for when the editor's root DOM element (the content editable
   * Lexical attaches to) changes. This is primarily used to attach event listeners to the root
   *  element. The root listener function is executed directly upon registration and then on
   * any subsequent update.
   *
   * Will trigger the provided callback each time the editor transitions between these states until the
   * teardown function is called.
   *
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerRootListener(listener) {
    const listenerSetOrMap = this._listeners.root;
    listener(this._rootElement, null);
    listenerSetOrMap.add(listener);
    return () => {
      listener(null, this._rootElement);
      listenerSetOrMap.delete(listener);
    };
  }
  /**
   * Registers a listener that will trigger anytime the provided command
   * is dispatched, subject to priority. Listeners that run at a higher priority can "intercept"
   * commands and prevent them from propagating to other handlers by returning true.
   *
   * Listeners registered at the same priority level will run deterministically in the order of registration.
   *
   * @param command - the command that will trigger the callback.
   * @param listener - the function that will execute when the command is dispatched.
   * @param priority - the relative priority of the listener. 0 | 1 | 2 | 3 | 4
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerCommand(command, listener, priority) {
    if (priority === void 0) {
      invariant(false, 'Listener for type "command" requires a "priority".');
    }
    const commandsMap = this._commands;
    if (!commandsMap.has(command)) {
      commandsMap.set(command, [
        /* @__PURE__ */ new Set(),
        /* @__PURE__ */ new Set(),
        /* @__PURE__ */ new Set(),
        /* @__PURE__ */ new Set(),
        /* @__PURE__ */ new Set()
      ]);
    }
    const listenersInPriorityOrder = commandsMap.get(command);
    if (listenersInPriorityOrder === void 0) {
      invariant(
        false,
        "registerCommand: Command %s not found in command map",
        String(command)
      );
    }
    const listeners = listenersInPriorityOrder[priority];
    listeners.add(listener);
    return () => {
      listeners.delete(listener);
      if (listenersInPriorityOrder.every(
        (listenersSet) => listenersSet.size === 0
      )) {
        commandsMap.delete(command);
      }
    };
  }
  /**
   * Registers a listener that will run when a Lexical node of the provided class is
   * mutated. The listener will receive a list of nodes along with the type of mutation
   * that was performed on each: created, destroyed, or updated.
   *
   * One common use case for this is to attach DOM event listeners to the underlying DOM nodes as Lexical nodes are created.
   * {@link LexicalEditor.getElementByKey} can be used for this.
   *
   * If any existing nodes are in the DOM, and skipInitialization is not true, the listener
   * will be called immediately with an updateTag of 'registerMutationListener' where all
   * nodes have the 'created' NodeMutation. This can be controlled with the skipInitialization option
   * (default is currently true for backwards compatibility in 0.16.x but will change to false in 0.17.0).
   *
   * @param klass - The class of the node that you want to listen to mutations on.
   * @param listener - The logic you want to run when the node is mutated.
   * @param options - see {@link MutationListenerOptions}
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerMutationListener(klass, listener, options) {
    const klassToMutate = this.resolveRegisteredNodeAfterReplacements(
      this.getRegisteredNode(klass)
    ).klass;
    const mutations = this._listeners.mutation;
    mutations.set(listener, klassToMutate);
    const skipInitialization = options && options.skipInitialization;
    if (!(skipInitialization === void 0 ? DEFAULT_SKIP_INITIALIZATION : skipInitialization)) {
      this.initializeMutationListener(listener, klassToMutate);
    }
    return () => {
      mutations.delete(listener);
    };
  }
  /** @internal */
  getRegisteredNode(klass) {
    const registeredNode = this._nodes.get(klass.getType());
    if (registeredNode === void 0) {
      invariant(
        false,
        "Node %s has not been registered. Ensure node has been passed to createEditor.",
        klass.name
      );
    }
    return registeredNode;
  }
  /** @internal */
  resolveRegisteredNodeAfterReplacements(registeredNode) {
    while (registeredNode.replaceWithKlass) {
      registeredNode = this.getRegisteredNode(registeredNode.replaceWithKlass);
    }
    return registeredNode;
  }
  /** @internal */
  initializeMutationListener(listener, klass) {
    const prevEditorState = this._editorState;
    const nodeMap = getCachedTypeToNodeMap(prevEditorState).get(
      klass.getType()
    );
    if (!nodeMap) {
      return;
    }
    const nodeMutationMap = /* @__PURE__ */ new Map();
    for (const k of nodeMap.keys()) {
      nodeMutationMap.set(k, "created");
    }
    if (nodeMutationMap.size > 0) {
      listener(nodeMutationMap, {
        dirtyLeaves: /* @__PURE__ */ new Set(),
        prevEditorState,
        updateTags: /* @__PURE__ */ new Set(["registerMutationListener"])
      });
    }
  }
  /** @internal */
  registerNodeTransformToKlass(klass, listener) {
    const registeredNode = this.getRegisteredNode(klass);
    registeredNode.transforms.add(listener);
    return registeredNode;
  }
  /**
   * Registers a listener that will run when a Lexical node of the provided class is
   * marked dirty during an update. The listener will continue to run as long as the node
   * is marked dirty. There are no guarantees around the order of transform execution!
   *
   * Watch out for infinite loops. See [Node Transforms](https://lexical.dev/docs/concepts/transforms)
   * @param klass - The class of the node that you want to run transforms on.
   * @param listener - The logic you want to run when the node is updated.
   * @returns a teardown function that can be used to cleanup the listener.
   */
  registerNodeTransform(klass, listener) {
    const registeredNode = this.registerNodeTransformToKlass(klass, listener);
    const registeredNodes = [registeredNode];
    const replaceWithKlass = registeredNode.replaceWithKlass;
    if (replaceWithKlass != null) {
      const registeredReplaceWithNode = this.registerNodeTransformToKlass(
        replaceWithKlass,
        listener
      );
      registeredNodes.push(registeredReplaceWithNode);
    }
    markAllNodesAsDirty(this, klass.getType());
    return () => {
      registeredNodes.forEach(
        (node) => node.transforms.delete(listener)
      );
    };
  }
  /**
   * Used to assert that a certain node is registered, usually by plugins to ensure nodes that they
   * depend on have been registered.
   * @returns True if the editor has registered the provided node type, false otherwise.
   */
  hasNode(node) {
    return this._nodes.has(node.getType());
  }
  /**
   * Used to assert that certain nodes are registered, usually by plugins to ensure nodes that they
   * depend on have been registered.
   * @returns True if the editor has registered all of the provided node types, false otherwise.
   */
  hasNodes(nodes) {
    return nodes.every(this.hasNode.bind(this));
  }
  /**
   * Dispatches a command of the specified type with the specified payload.
   * This triggers all command listeners (set by {@link LexicalEditor.registerCommand})
   * for this type, passing them the provided payload.
   * @param type - the type of command listeners to trigger.
   * @param payload - the data to pass as an argument to the command listeners.
   */
  dispatchCommand(type, payload) {
    return dispatchCommand(this, type, payload);
  }
  /**
   * Gets a map of all decorators in the editor.
   * @returns A mapping of call decorator keys to their decorated content
   */
  getDecorators() {
    return this._decorators;
  }
  /**
   *
   * @returns the current root element of the editor. If you want to register
   * an event listener, do it via {@link LexicalEditor.registerRootListener}, since
   * this reference may not be stable.
   */
  getRootElement() {
    return this._rootElement;
  }
  /**
   * Gets the key of the editor
   * @returns The editor key
   */
  getKey() {
    return this._key;
  }
  /**
   * Imperatively set the root contenteditable element that Lexical listens
   * for events on.
   */
  setRootElement(nextRootElement) {
    const prevRootElement = this._rootElement;
    if (nextRootElement !== prevRootElement) {
      const classNames = getCachedClassNameArray(this._config.theme, "root");
      const pendingEditorState = this._pendingEditorState || this._editorState;
      this._rootElement = nextRootElement;
      resetEditor(this, prevRootElement, nextRootElement, pendingEditorState);
      if (prevRootElement !== null) {
        if (!this._config.disableEvents) {
          removeRootElementEvents(prevRootElement);
        }
        if (classNames != null) {
          prevRootElement.classList.remove(...classNames);
        }
      }
      if (nextRootElement !== null) {
        const windowObj = getDefaultView(nextRootElement);
        const style = nextRootElement.style;
        style.userSelect = "text";
        style.whiteSpace = "pre-wrap";
        style.wordBreak = "break-word";
        nextRootElement.setAttribute("data-lexical-editor", "true");
        this._window = windowObj;
        this._dirtyType = FULL_RECONCILE;
        initMutationObserver(this);
        this._updateTags.add("history-merge");
        $commitPendingUpdates(this);
        if (!this._config.disableEvents) {
          addRootElementEvents(nextRootElement, this);
        }
        if (classNames != null) {
          nextRootElement.classList.add(...classNames);
        }
      } else {
        this._editorState = pendingEditorState;
        this._pendingEditorState = null;
        this._window = null;
      }
      triggerListeners("root", this, false, nextRootElement, prevRootElement);
    }
  }
  /**
   * Gets the underlying HTMLElement associated with the LexicalNode for the given key.
   * @returns the HTMLElement rendered by the LexicalNode associated with the key.
   * @param key - the key of the LexicalNode.
   */
  getElementByKey(key) {
    return this._keyToDOMMap.get(key) || null;
  }
  /**
   * Gets the active editor state.
   * @returns The editor state
   */
  getEditorState() {
    return this._editorState;
  }
  /**
   * Imperatively set the EditorState. Triggers reconciliation like an update.
   * @param editorState - the state to set the editor
   * @param options - options for the update.
   */
  setEditorState(editorState, options) {
    if (editorState.isEmpty()) {
      invariant(
        false,
        "setEditorState: the editor state is empty. Ensure the editor state's root node never becomes empty."
      );
    }
    $flushRootMutations(this);
    const pendingEditorState = this._pendingEditorState;
    const tags = this._updateTags;
    const tag = options !== void 0 ? options.tag : null;
    if (pendingEditorState !== null && !pendingEditorState.isEmpty()) {
      if (tag != null) {
        tags.add(tag);
      }
      $commitPendingUpdates(this);
    }
    this._pendingEditorState = editorState;
    this._dirtyType = FULL_RECONCILE;
    this._dirtyElements.set("root", false);
    this._compositionKey = null;
    if (tag != null) {
      tags.add(tag);
    }
    $commitPendingUpdates(this);
  }
  /**
   * Parses a SerializedEditorState (usually produced by {@link EditorState.toJSON}) and returns
   * and EditorState object that can be, for example, passed to {@link LexicalEditor.setEditorState}. Typically,
   * deserialization from JSON stored in a database uses this method.
   * @param maybeStringifiedEditorState
   * @param updateFn
   * @returns
   */
  parseEditorState(maybeStringifiedEditorState, updateFn) {
    const serializedEditorState = typeof maybeStringifiedEditorState === "string" ? JSON.parse(maybeStringifiedEditorState) : maybeStringifiedEditorState;
    return parseEditorState(serializedEditorState, this, updateFn);
  }
  /**
   * Executes a read of the editor's state, with the
   * editor context available (useful for exporting and read-only DOM
   * operations). Much like update, but prevents any mutation of the
   * editor's state. Any pending updates will be flushed immediately before
   * the read.
   * @param callbackFn - A function that has access to read-only editor state.
   */
  read(callbackFn) {
    $commitPendingUpdates(this);
    return this.getEditorState().read(callbackFn, { editor: this });
  }
  /**
   * Executes an update to the editor state. The updateFn callback is the ONLY place
   * where Lexical editor state can be safely mutated.
   * @param updateFn - A function that has access to writable editor state.
   * @param options - A bag of options to control the behavior of the update.
   * @param options.onUpdate - A function to run once the update is complete.
   * Useful for synchronizing updates in some cases.
   * @param options.skipTransforms - Setting this to true will suppress all node
   * transforms for this update cycle.
   * @param options.tag - A tag to identify this update, in an update listener, for instance.
   * Some tags are reserved by the core and control update behavior in different ways.
   * @param options.discrete - If true, prevents this update from being batched, forcing it to
   * run synchronously.
   */
  update(updateFn, options) {
    updateEditor(this, updateFn, options);
  }
  /**
   * Helper to run the update and commitUpdates methods in a single call.
   */
  updateAndCommit(updateFn, options) {
    this.update(updateFn, options);
    this.commitUpdates();
  }
  /**
   * Focuses the editor
   * @param callbackFn - A function to run after the editor is focused.
   * @param options - A bag of options
   * @param options.defaultSelection - Where to move selection when the editor is
   * focused. Can be rootStart, rootEnd, or undefined. Defaults to rootEnd.
   */
  focus(callbackFn, options = {}) {
    const rootElement = this._rootElement;
    if (rootElement !== null) {
      rootElement.setAttribute("autocapitalize", "off");
      updateEditor(
        this,
        () => {
          const selection = $getSelection();
          const root = $getRoot();
          if (selection !== null) {
            selection.dirty = true;
          } else if (root.getChildrenSize() !== 0) {
            if (options.defaultSelection === "rootStart") {
              root.selectStart();
            } else {
              root.selectEnd();
            }
          }
        },
        {
          onUpdate: () => {
            rootElement.removeAttribute("autocapitalize");
            if (callbackFn) {
              callbackFn();
            }
          },
          tag: "focus"
        }
      );
      if (this._pendingEditorState === null) {
        rootElement.removeAttribute("autocapitalize");
      }
    }
  }
  /**
   * Commits any currently pending updates scheduled for the editor.
   */
  commitUpdates() {
    $commitPendingUpdates(this);
  }
  /**
   * Removes focus from the editor.
   */
  blur() {
    const rootElement = this._rootElement;
    if (rootElement !== null) {
      rootElement.blur();
    }
    const domSelection = getDOMSelection(this._window);
    if (domSelection !== null) {
      domSelection.removeAllRanges();
    }
  }
  /**
   * Returns true if the editor is editable, false otherwise.
   * @returns True if the editor is editable, false otherwise.
   */
  isEditable() {
    return this._editable;
  }
  /**
   * Sets the editable property of the editor. When false, the
   * editor will not listen for user events on the underling contenteditable.
   * @param editable - the value to set the editable mode to.
   */
  setEditable(editable) {
    if (this._editable !== editable) {
      this._editable = editable;
      triggerListeners("editable", this, true, editable);
    }
  }
  /**
   * Returns a JSON-serializable javascript object NOT a JSON string.
   * You still must call JSON.stringify (or something else) to turn the
   * state into a string you can transfer over the wire and store in a database.
   *
   * See {@link LexicalNode.exportJSON}
   *
   * @returns A JSON-serializable javascript object
   */
  toJSON() {
    return {
      editorState: this._editorState.toJSON()
    };
  }
};
/** The version with build identifiers for this editor (since 0.17.1) */
__publicField(LexicalEditor, "version");
LexicalEditor.version = "0.17.1";

// resources/js/wysiwyg/lexical/history/index.ts
var HISTORY_MERGE = 0;
var HISTORY_PUSH = 1;
var DISCARD_HISTORY_CANDIDATE = 2;
var OTHER = 0;
var COMPOSING_CHARACTER = 1;
var INSERT_CHARACTER_AFTER_SELECTION = 2;
var DELETE_CHARACTER_BEFORE_SELECTION = 3;
var DELETE_CHARACTER_AFTER_SELECTION = 4;
function getDirtyNodes(editorState, dirtyLeaves, dirtyElements) {
  const nodeMap = editorState._nodeMap;
  const nodes = [];
  for (const dirtyLeafKey of dirtyLeaves) {
    const dirtyLeaf = nodeMap.get(dirtyLeafKey);
    if (dirtyLeaf !== void 0) {
      nodes.push(dirtyLeaf);
    }
  }
  for (const [dirtyElementKey, intentionallyMarkedAsDirty] of dirtyElements) {
    if (!intentionallyMarkedAsDirty) {
      continue;
    }
    const dirtyElement = nodeMap.get(dirtyElementKey);
    if (dirtyElement !== void 0 && !$isRootNode(dirtyElement)) {
      nodes.push(dirtyElement);
    }
  }
  return nodes;
}
function getChangeType(prevEditorState, nextEditorState, dirtyLeavesSet, dirtyElementsSet, isComposing) {
  if (prevEditorState === null || dirtyLeavesSet.size === 0 && dirtyElementsSet.size === 0 && !isComposing) {
    return OTHER;
  }
  const nextSelection = nextEditorState._selection;
  const prevSelection = prevEditorState._selection;
  if (isComposing) {
    return COMPOSING_CHARACTER;
  }
  if (!$isRangeSelection(nextSelection) || !$isRangeSelection(prevSelection) || !prevSelection.isCollapsed() || !nextSelection.isCollapsed()) {
    return OTHER;
  }
  const dirtyNodes = getDirtyNodes(
    nextEditorState,
    dirtyLeavesSet,
    dirtyElementsSet
  );
  if (dirtyNodes.length === 0) {
    return OTHER;
  }
  if (dirtyNodes.length > 1) {
    const nextNodeMap = nextEditorState._nodeMap;
    const nextAnchorNode = nextNodeMap.get(nextSelection.anchor.key);
    const prevAnchorNode = nextNodeMap.get(prevSelection.anchor.key);
    if (nextAnchorNode && prevAnchorNode && !prevEditorState._nodeMap.has(nextAnchorNode.__key) && $isTextNode(nextAnchorNode) && nextAnchorNode.__text.length === 1 && nextSelection.anchor.offset === 1) {
      return INSERT_CHARACTER_AFTER_SELECTION;
    }
    return OTHER;
  }
  const nextDirtyNode = dirtyNodes[0];
  const prevDirtyNode = prevEditorState._nodeMap.get(nextDirtyNode.__key);
  if (!$isTextNode(prevDirtyNode) || !$isTextNode(nextDirtyNode) || prevDirtyNode.__mode !== nextDirtyNode.__mode) {
    return OTHER;
  }
  const prevText = prevDirtyNode.__text;
  const nextText = nextDirtyNode.__text;
  if (prevText === nextText) {
    return OTHER;
  }
  const nextAnchor = nextSelection.anchor;
  const prevAnchor = prevSelection.anchor;
  if (nextAnchor.key !== prevAnchor.key || nextAnchor.type !== "text") {
    return OTHER;
  }
  const nextAnchorOffset = nextAnchor.offset;
  const prevAnchorOffset = prevAnchor.offset;
  const textDiff = nextText.length - prevText.length;
  if (textDiff === 1 && prevAnchorOffset === nextAnchorOffset - 1) {
    return INSERT_CHARACTER_AFTER_SELECTION;
  }
  if (textDiff === -1 && prevAnchorOffset === nextAnchorOffset + 1) {
    return DELETE_CHARACTER_BEFORE_SELECTION;
  }
  if (textDiff === -1 && prevAnchorOffset === nextAnchorOffset) {
    return DELETE_CHARACTER_AFTER_SELECTION;
  }
  return OTHER;
}
function isTextNodeUnchanged(key, prevEditorState, nextEditorState) {
  const prevNode = prevEditorState._nodeMap.get(key);
  const nextNode = nextEditorState._nodeMap.get(key);
  const prevSelection = prevEditorState._selection;
  const nextSelection = nextEditorState._selection;
  const isDeletingLine = $isRangeSelection(prevSelection) && $isRangeSelection(nextSelection) && prevSelection.anchor.type === "element" && prevSelection.focus.type === "element" && nextSelection.anchor.type === "text" && nextSelection.focus.type === "text";
  if (!isDeletingLine && $isTextNode(prevNode) && $isTextNode(nextNode) && prevNode.__parent === nextNode.__parent) {
    return JSON.stringify(prevEditorState.read(() => prevNode.exportJSON())) === JSON.stringify(nextEditorState.read(() => nextNode.exportJSON()));
  }
  return false;
}
function createMergeActionGetter(editor, delay) {
  let prevChangeTime = Date.now();
  let prevChangeType = OTHER;
  return (prevEditorState, nextEditorState, currentHistoryEntry, dirtyLeaves, dirtyElements, tags) => {
    const changeTime = Date.now();
    if (tags.has("historic")) {
      prevChangeType = OTHER;
      prevChangeTime = changeTime;
      return DISCARD_HISTORY_CANDIDATE;
    }
    const changeType = getChangeType(
      prevEditorState,
      nextEditorState,
      dirtyLeaves,
      dirtyElements,
      editor.isComposing()
    );
    const mergeAction = (() => {
      const isSameEditor = currentHistoryEntry === null || currentHistoryEntry.editor === editor;
      const shouldPushHistory = tags.has("history-push");
      const shouldMergeHistory = !shouldPushHistory && isSameEditor && tags.has("history-merge");
      if (shouldMergeHistory) {
        return HISTORY_MERGE;
      }
      if (prevEditorState === null) {
        return HISTORY_PUSH;
      }
      const selection = nextEditorState._selection;
      const hasDirtyNodes = dirtyLeaves.size > 0 || dirtyElements.size > 0;
      if (!hasDirtyNodes) {
        if (selection !== null) {
          return HISTORY_MERGE;
        }
        return DISCARD_HISTORY_CANDIDATE;
      }
      if (shouldPushHistory === false && changeType !== OTHER && changeType === prevChangeType && changeTime < prevChangeTime + delay && isSameEditor) {
        return HISTORY_MERGE;
      }
      if (dirtyLeaves.size === 1) {
        const dirtyLeafKey = Array.from(dirtyLeaves)[0];
        if (isTextNodeUnchanged(dirtyLeafKey, prevEditorState, nextEditorState)) {
          return HISTORY_MERGE;
        }
      }
      return HISTORY_PUSH;
    })();
    prevChangeTime = changeTime;
    prevChangeType = changeType;
    return mergeAction;
  };
}
function redo(editor, historyState) {
  const redoStack = historyState.redoStack;
  const undoStack = historyState.undoStack;
  if (redoStack.length !== 0) {
    const current = historyState.current;
    if (current !== null) {
      undoStack.push(current);
      editor.dispatchCommand(CAN_UNDO_COMMAND, true);
    }
    const historyStateEntry = redoStack.pop();
    if (redoStack.length === 0) {
      editor.dispatchCommand(CAN_REDO_COMMAND, false);
    }
    historyState.current = historyStateEntry || null;
    if (historyStateEntry) {
      historyStateEntry.editor.setEditorState(historyStateEntry.editorState, {
        tag: "historic"
      });
    }
  }
}
function undo(editor, historyState) {
  const redoStack = historyState.redoStack;
  const undoStack = historyState.undoStack;
  const undoStackLength = undoStack.length;
  if (undoStackLength !== 0) {
    const current = historyState.current;
    const historyStateEntry = undoStack.pop();
    if (current !== null) {
      redoStack.push(current);
      editor.dispatchCommand(CAN_REDO_COMMAND, true);
    }
    if (undoStack.length === 0) {
      editor.dispatchCommand(CAN_UNDO_COMMAND, false);
    }
    historyState.current = historyStateEntry || null;
    if (historyStateEntry) {
      historyStateEntry.editor.setEditorState(historyStateEntry.editorState, {
        tag: "historic"
      });
    }
  }
}
function clearHistory(historyState) {
  historyState.undoStack = [];
  historyState.redoStack = [];
  historyState.current = null;
}
function registerHistory(editor, historyState, delay) {
  const getMergeAction = createMergeActionGetter(editor, delay);
  const applyChange = ({
    editorState,
    prevEditorState,
    dirtyLeaves,
    dirtyElements,
    tags
  }) => {
    const current = historyState.current;
    const redoStack = historyState.redoStack;
    const undoStack = historyState.undoStack;
    const currentEditorState = current === null ? null : current.editorState;
    if (current !== null && editorState === currentEditorState) {
      return;
    }
    const mergeAction = getMergeAction(
      prevEditorState,
      editorState,
      current,
      dirtyLeaves,
      dirtyElements,
      tags
    );
    if (mergeAction === HISTORY_PUSH) {
      if (redoStack.length !== 0) {
        historyState.redoStack = [];
        editor.dispatchCommand(CAN_REDO_COMMAND, false);
      }
      if (current !== null) {
        undoStack.push({
          ...current
        });
        editor.dispatchCommand(CAN_UNDO_COMMAND, true);
      }
    } else if (mergeAction === DISCARD_HISTORY_CANDIDATE) {
      return;
    }
    historyState.current = {
      editor,
      editorState
    };
  };
  const unregister = mergeRegister(
    editor.registerCommand(
      UNDO_COMMAND,
      () => {
        undo(editor, historyState);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      REDO_COMMAND,
      () => {
        redo(editor, historyState);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      CLEAR_EDITOR_COMMAND,
      () => {
        clearHistory(historyState);
        return false;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      CLEAR_HISTORY_COMMAND,
      () => {
        clearHistory(historyState);
        editor.dispatchCommand(CAN_REDO_COMMAND, false);
        editor.dispatchCommand(CAN_UNDO_COMMAND, false);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerUpdateListener(applyChange)
  );
  return unregister;
}
function createEmptyHistoryState() {
  return {
    current: null,
    redoStack: [],
    undoStack: []
  };
}

// resources/js/wysiwyg/lexical/clipboard/clipboard.ts
var getDOMSelection2 = (targetWindow) => CAN_USE_DOM ? (targetWindow || window).getSelection() : null;
function $getHtmlContent(editor, selection = $getSelection()) {
  if (selection == null) {
    invariant(false, "Expected valid LexicalSelection");
  }
  if ($isRangeSelection(selection) && selection.isCollapsed() || selection.getNodes().length === 0) {
    return "";
  }
  return $generateHtmlFromNodes(editor, selection);
}
function $getLexicalContent(editor, selection = $getSelection()) {
  if (selection == null) {
    invariant(false, "Expected valid LexicalSelection");
  }
  if ($isRangeSelection(selection) && selection.isCollapsed() || selection.getNodes().length === 0) {
    return null;
  }
  return JSON.stringify($generateJSONFromSelectedNodes(editor, selection));
}
function $insertDataTransferForRichText(dataTransfer, selection, editor) {
  const lexicalString = dataTransfer.getData("application/x-lexical-editor");
  if (lexicalString) {
    try {
      const payload = JSON.parse(lexicalString);
      if (payload.namespace === editor._config.namespace && Array.isArray(payload.nodes)) {
        const nodes = $generateNodesFromSerializedNodes(payload.nodes);
        return $insertGeneratedNodes(editor, nodes, selection);
      }
    } catch {
    }
  }
  const htmlString = dataTransfer.getData("text/html");
  if (htmlString) {
    try {
      const parser = new DOMParser();
      const dom = parser.parseFromString(htmlString, "text/html");
      const nodes = $generateNodesFromDOM(editor, dom);
      return $insertGeneratedNodes(editor, nodes, selection);
    } catch {
    }
  }
  const text = dataTransfer.getData("text/plain") || dataTransfer.getData("text/uri-list");
  if (text != null) {
    if ($isRangeSelection(selection)) {
      const parts = text.split(/(\r?\n|\t)/);
      if (parts[parts.length - 1] === "") {
        parts.pop();
      }
      for (let i = 0; i < parts.length; i++) {
        const currentSelection = $getSelection();
        if ($isRangeSelection(currentSelection)) {
          const part = parts[i];
          if (part === "\n" || part === "\r\n") {
            currentSelection.insertParagraph();
          } else if (part === "	") {
            currentSelection.insertNodes([$createTabNode()]);
          } else {
            currentSelection.insertText(part);
          }
        }
      }
    } else {
      selection.insertRawText(text);
    }
  }
}
function $insertGeneratedNodes(editor, nodes, selection) {
  if (!editor.dispatchCommand(SELECTION_INSERT_CLIPBOARD_NODES_COMMAND, {
    nodes,
    selection
  })) {
    selection.insertNodes(nodes);
  }
  return;
}
function exportNodeToJSON2(node) {
  const serializedNode = node.exportJSON();
  const nodeClass = node.constructor;
  if (serializedNode.type !== nodeClass.getType()) {
    invariant(
      false,
      "LexicalNode: Node %s does not implement .exportJSON().",
      nodeClass.name
    );
  }
  if ($isElementNode(node)) {
    const serializedChildren = serializedNode.children;
    if (!Array.isArray(serializedChildren)) {
      invariant(
        false,
        "LexicalNode: Node %s is an element but .exportJSON() does not have a children array.",
        nodeClass.name
      );
    }
  }
  return serializedNode;
}
function $appendNodesToJSON(editor, selection, currentNode, targetArray = []) {
  let shouldInclude = selection !== null ? currentNode.isSelected(selection) : true;
  const shouldExclude = $isElementNode(currentNode) && currentNode.excludeFromCopy("html");
  let target = currentNode;
  if (selection !== null) {
    let clone = $cloneWithProperties(currentNode);
    clone = $isTextNode(clone) && selection !== null ? $sliceSelectedTextNodeContent(selection, clone) : clone;
    target = clone;
  }
  const children = $isElementNode(target) ? target.getChildren() : [];
  const serializedNode = exportNodeToJSON2(target);
  if ($isTextNode(target)) {
    const text = target.__text;
    if (text.length > 0) {
      serializedNode.text = text;
    } else {
      shouldInclude = false;
    }
  }
  for (let i = 0; i < children.length; i++) {
    const childNode = children[i];
    const shouldIncludeChild = $appendNodesToJSON(
      editor,
      selection,
      childNode,
      serializedNode.children
    );
    if (!shouldInclude && $isElementNode(currentNode) && shouldIncludeChild && currentNode.extractWithChild(childNode, selection, "clone")) {
      shouldInclude = true;
    }
  }
  if (shouldInclude && !shouldExclude) {
    targetArray.push(serializedNode);
  } else if (Array.isArray(serializedNode.children)) {
    for (let i = 0; i < serializedNode.children.length; i++) {
      const serializedChildNode = serializedNode.children[i];
      targetArray.push(serializedChildNode);
    }
  }
  return shouldInclude;
}
function $generateJSONFromSelectedNodes(editor, selection) {
  const nodes = [];
  const root = $getRoot();
  const topLevelChildren = root.getChildren();
  for (let i = 0; i < topLevelChildren.length; i++) {
    const topLevelNode = topLevelChildren[i];
    $appendNodesToJSON(editor, selection, topLevelNode, nodes);
  }
  return {
    namespace: editor._config.namespace,
    nodes
  };
}
function $generateNodesFromSerializedNodes(serializedNodes) {
  const nodes = [];
  for (let i = 0; i < serializedNodes.length; i++) {
    const serializedNode = serializedNodes[i];
    const node = $parseSerializedNode(serializedNode);
    if ($isTextNode(node)) {
      $addNodeStyle(node);
    }
    nodes.push(node);
  }
  return nodes;
}
var EVENT_LATENCY = 50;
var clipboardEventTimeout = null;
async function copyToClipboard(editor, event, data) {
  if (clipboardEventTimeout !== null) {
    return false;
  }
  if (event !== null) {
    return new Promise((resolve, reject) => {
      editor.update(() => {
        resolve($copyToClipboardEvent(editor, event, data));
      });
    });
  }
  const rootElement = editor.getRootElement();
  const windowDocument = editor._window == null ? window.document : editor._window.document;
  const domSelection = getDOMSelection2(editor._window);
  if (rootElement === null || domSelection === null) {
    return false;
  }
  const element = windowDocument.createElement("span");
  element.style.cssText = "position: fixed; top: -1000px;";
  element.append(windowDocument.createTextNode("#"));
  rootElement.append(element);
  const range = new Range();
  range.setStart(element, 0);
  range.setEnd(element, 1);
  domSelection.removeAllRanges();
  domSelection.addRange(range);
  return new Promise((resolve, reject) => {
    const removeListener = editor.registerCommand(
      COPY_COMMAND,
      (secondEvent) => {
        if (objectKlassEquals(secondEvent, ClipboardEvent)) {
          removeListener();
          if (clipboardEventTimeout !== null) {
            window.clearTimeout(clipboardEventTimeout);
            clipboardEventTimeout = null;
          }
          resolve(
            $copyToClipboardEvent(editor, secondEvent, data)
          );
        }
        return true;
      },
      COMMAND_PRIORITY_CRITICAL
    );
    clipboardEventTimeout = window.setTimeout(() => {
      removeListener();
      clipboardEventTimeout = null;
      resolve(false);
    }, EVENT_LATENCY);
    windowDocument.execCommand("copy");
    element.remove();
  });
}
function $copyToClipboardEvent(editor, event, data) {
  if (data === void 0) {
    const domSelection = getDOMSelection2(editor._window);
    if (!domSelection) {
      return false;
    }
    const anchorDOM = domSelection.anchorNode;
    const focusDOM = domSelection.focusNode;
    if (anchorDOM !== null && focusDOM !== null && !isSelectionWithinEditor(editor, anchorDOM, focusDOM)) {
      return false;
    }
    const selection = $getSelection();
    if (selection === null) {
      return false;
    }
    data = $getClipboardDataFromSelection(selection);
  }
  event.preventDefault();
  const clipboardData = event.clipboardData;
  if (clipboardData === null) {
    return false;
  }
  setLexicalClipboardDataTransfer(clipboardData, data);
  return true;
}
var clipboardDataFunctions = [
  ["text/html", $getHtmlContent],
  ["application/x-lexical-editor", $getLexicalContent]
];
function $getClipboardDataFromSelection(selection = $getSelection()) {
  const clipboardData = {
    "text/plain": selection ? selection.getTextContent() : ""
  };
  if (selection) {
    const editor = $getEditor();
    for (const [mimeType, $editorFn] of clipboardDataFunctions) {
      const v = $editorFn(editor, selection);
      if (v !== null) {
        clipboardData[mimeType] = v;
      }
    }
  }
  return clipboardData;
}
function setLexicalClipboardDataTransfer(clipboardData, data) {
  for (const k in data) {
    const v = data[k];
    if (v !== void 0) {
      clipboardData.setData(k, v);
    }
  }
}

// resources/js/wysiwyg/lexical/core/shared/caretFromPoint.ts
function caretFromPoint(x, y) {
  if (typeof document.caretRangeFromPoint !== "undefined") {
    const range = document.caretRangeFromPoint(x, y);
    if (range === null) {
      return null;
    }
    return {
      node: range.startContainer,
      offset: range.startOffset
    };
  } else if (document.caretPositionFromPoint !== "undefined") {
    const range = document.caretPositionFromPoint(x, y);
    if (range === null) {
      return null;
    }
    return {
      node: range.offsetNode,
      offset: range.offset
    };
  } else {
    return null;
  }
}

// resources/js/wysiwyg/lexical/rich-text/index.ts
var DRAG_DROP_PASTE = createCommand(
  "DRAG_DROP_PASTE_FILE"
);
function onPasteForRichText(event, editor) {
  event.preventDefault();
  editor.update(
    () => {
      const selection = $getSelection();
      const clipboardData = objectKlassEquals(event, InputEvent) || objectKlassEquals(event, KeyboardEvent) ? null : event.clipboardData;
      if (clipboardData != null && selection !== null) {
        $insertDataTransferForRichText(clipboardData, selection, editor);
      }
    },
    {
      tag: "paste"
    }
  );
}
async function onCutForRichText(event, editor) {
  await copyToClipboard(
    editor,
    objectKlassEquals(event, ClipboardEvent) ? event : null
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
function eventFiles(event) {
  let dataTransfer = null;
  if (objectKlassEquals(event, DragEvent)) {
    dataTransfer = event.dataTransfer;
  } else if (objectKlassEquals(event, ClipboardEvent)) {
    dataTransfer = event.clipboardData;
  }
  if (dataTransfer === null) {
    return [false, [], false];
  }
  const types = dataTransfer.types;
  const hasFiles = types.includes("Files");
  const hasContent = types.includes("text/html") || types.includes("text/plain");
  return [hasFiles, Array.from(dataTransfer.files), hasContent];
}
function $isTargetWithinDecorator(target) {
  const node = $getNearestNodeFromDOMNode(target);
  return $isDecoratorNode(node);
}
function $isSelectionAtEndOfRoot(selection) {
  const focus = selection.focus;
  return focus.key === "root" && focus.offset === $getRoot().getChildrenSize();
}
function registerRichText(editor) {
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
      0
    ),
    editor.registerCommand(
      DELETE_CHARACTER_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteCharacter(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      DELETE_WORD_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteWord(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      DELETE_LINE_COMMAND,
      (isBackward) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.deleteLine(isBackward);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      CONTROLLED_TEXT_INSERTION_COMMAND,
      (eventOrText) => {
        const selection = $getSelection();
        if (typeof eventOrText === "string") {
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
      COMMAND_PRIORITY_EDITOR
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      FORMAT_TEXT_COMMAND,
      (format) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.formatText(format);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      INSERT_LINE_BREAK_COMMAND,
      (selectStart) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        selection.insertLineBreak(selectStart);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      INSERT_TAB_COMMAND,
      () => {
        $insertNodes([$createTabNode()]);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_ARROW_UP_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection) && !$isTargetWithinDecorator(event.target)) {
          const nodes = selection.getNodes();
          if (nodes.length > 0) {
            nodes[0].selectPrevious();
            return true;
          }
        } else if ($isRangeSelection(selection)) {
          const possibleNode = $getAdjacentNode(selection.focus, true);
          if (!event.shiftKey && $isDecoratorNode(possibleNode) && !possibleNode.isIsolated() && !possibleNode.isInline()) {
            possibleNode.selectPrevious();
            event.preventDefault();
            return true;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_ARROW_DOWN_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection)) {
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
          if (!event.shiftKey && $isDecoratorNode(possibleNode) && !possibleNode.isIsolated() && !possibleNode.isInline()) {
            possibleNode.selectNext();
            event.preventDefault();
            return true;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_ARROW_LEFT_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection)) {
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_ARROW_RIGHT_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isNodeSelection(selection) && !$isTargetWithinDecorator(event.target)) {
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_BACKSPACE_COMMAND,
      (event) => {
        if ($isTargetWithinDecorator(event.target)) {
          return false;
        }
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        event.preventDefault();
        return editor.dispatchCommand(DELETE_CHARACTER_COMMAND, true);
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_DELETE_COMMAND,
      (event) => {
        if ($isTargetWithinDecorator(event.target)) {
          return false;
        }
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        event.preventDefault();
        return editor.dispatchCommand(DELETE_CHARACTER_COMMAND, false);
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      KEY_ENTER_COMMAND,
      (event) => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection)) {
          return false;
        }
        if (event !== null) {
          if ((IS_IOS || IS_SAFARI || IS_APPLE_WEBKIT) && CAN_USE_BEFORE_INPUT) {
            return false;
          }
          event.preventDefault();
          if (event.shiftKey) {
            return editor.dispatchCommand(INSERT_LINE_BREAK_COMMAND, false);
          }
        }
        return editor.dispatchCommand(INSERT_PARAGRAPH_COMMAND, void 0);
      },
      COMMAND_PRIORITY_EDITOR
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      DROP_COMMAND,
      (event) => {
        const [, files] = eventFiles(event);
        if (files.length > 0) {
          const x = event.clientX;
          const y = event.clientY;
          const eventRange = caretFromPoint(x, y);
          if (eventRange !== null) {
            const { offset: domOffset, node: domNode } = eventRange;
            const node = $getNearestNodeFromDOMNode(domNode);
            if (node !== null) {
              const selection2 = $createRangeSelection();
              if ($isTextNode(node)) {
                selection2.anchor.set(node.getKey(), domOffset, "text");
                selection2.focus.set(node.getKey(), domOffset, "text");
              } else {
                const parentKey = node.getParentOrThrow().getKey();
                const offset = node.getIndexWithinParent() + 1;
                selection2.anchor.set(parentKey, offset, "element");
                selection2.focus.set(parentKey, offset, "element");
              }
              const normalizedSelection = $normalizeSelection(selection2);
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
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      DRAGSTART_COMMAND,
      (event) => {
        const [isFileTransfer] = eventFiles(event);
        const selection = $getSelection();
        if (isFileTransfer && !$isRangeSelection(selection)) {
          return false;
        }
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
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
            event.preventDefault();
          }
        }
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      SELECT_ALL_COMMAND,
      () => {
        $selectAll();
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      COPY_COMMAND,
      (event) => {
        copyToClipboard(
          editor,
          objectKlassEquals(event, ClipboardEvent) ? event : null
        );
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      CUT_COMMAND,
      (event) => {
        onCutForRichText(event, editor);
        return true;
      },
      COMMAND_PRIORITY_EDITOR
    ),
    editor.registerCommand(
      PASTE_COMMAND,
      (event) => {
        const [, files, hasTextContent] = eventFiles(event);
        if (files.length > 0 && !hasTextContent) {
          editor.dispatchCommand(DRAG_DROP_PASTE, files);
          return true;
        }
        if (isSelectionCapturedInDecoratorInput(event.target)) {
          return false;
        }
        const selection = $getSelection();
        if (selection !== null) {
          onPasteForRichText(event, editor);
          return true;
        }
        return false;
      },
      COMMAND_PRIORITY_EDITOR
    )
  );
  return removeListener;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalCalloutNode.ts
var CalloutNode = class _CalloutNode extends ElementNode8 {
  constructor(category, key) {
    super(key);
    __publicField(this, "__id", "");
    __publicField(this, "__category", "info");
    __publicField(this, "__alignment", "");
    __publicField(this, "__inset", 0);
    this.__category = category;
  }
  static getType() {
    return "callout";
  }
  static clone(node) {
    const newNode = new _CalloutNode(node.__category, node.__key);
    newNode.__id = node.__id;
    newNode.__alignment = node.__alignment;
    newNode.__inset = node.__inset;
    return newNode;
  }
  setCategory(category) {
    const self = this.getWritable();
    self.__category = category;
  }
  getCategory() {
    const self = this.getLatest();
    return self.__category;
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  setAlignment(alignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }
  getAlignment() {
    const self = this.getLatest();
    return self.__alignment;
  }
  setInset(size) {
    const self = this.getWritable();
    self.__inset = size;
  }
  getInset() {
    const self = this.getLatest();
    return self.__inset;
  }
  createDOM(_config, _editor) {
    const element = document.createElement("p");
    element.classList.add("callout", this.__category || "");
    updateElementWithCommonBlockProps(element, this);
    return element;
  }
  updateDOM(prevNode) {
    return prevNode.__category !== this.__category || commonPropertiesDifferent(prevNode, this);
  }
  insertNewAfter(selection, restoreSelection) {
    const anchorOffset = selection ? selection.anchor.offset : 0;
    const newElement = anchorOffset === this.getTextContentSize() || !selection ? $createParagraphNode() : $createCalloutNode(this.__category);
    newElement.setDirection(this.getDirection());
    this.insertAfter(newElement, restoreSelection);
    if (anchorOffset === 0 && !this.isEmpty() && selection) {
      const paragraph2 = $createParagraphNode();
      paragraph2.select();
      this.replace(paragraph2, true);
    }
    return newElement;
  }
  static importDOM() {
    return {
      p(node) {
        if (node.classList.contains("callout")) {
          return {
            conversion: (element) => {
              let category = "info";
              const categories = ["info", "success", "warning", "danger"];
              for (const c of categories) {
                if (element.classList.contains(c)) {
                  category = c;
                  break;
                }
              }
              const node2 = new _CalloutNode(category);
              setCommonBlockPropsFromElement(element, node2);
              return {
                node: node2
              };
            },
            priority: 3
          };
        }
        return null;
      }
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "callout",
      version: 1,
      category: this.__category,
      id: this.__id,
      alignment: this.__alignment,
      inset: this.__inset
    };
  }
  static importJSON(serializedNode) {
    const node = $createCalloutNode(serializedNode.category);
    deserializeCommonBlockNode(serializedNode, node);
    return node;
  }
};
function $createCalloutNode(category = "info") {
  return new CalloutNode(category);
}
function $isCalloutNode(node) {
  return node instanceof CalloutNode;
}
function $isCalloutNodeOfCategory(node, category = "info") {
  return node instanceof CalloutNode && node.getCategory() === category;
}

// resources/js/wysiwyg/lexical/link/index.ts
var LinkNode = class _LinkNode extends ElementNode8 {
  constructor(url, attributes = {}, key) {
    super(key);
    /** @internal */
    __publicField(this, "__url");
    /** @internal */
    __publicField(this, "__target");
    /** @internal */
    __publicField(this, "__rel");
    /** @internal */
    __publicField(this, "__title");
    const { target = null, rel = null, title = null } = attributes;
    this.__url = url;
    this.__target = target;
    this.__rel = rel;
    this.__title = title;
  }
  static getType() {
    return "link";
  }
  static clone(node) {
    return new _LinkNode(
      node.__url,
      { rel: node.__rel, target: node.__target, title: node.__title },
      node.__key
    );
  }
  createDOM(config) {
    const element = document.createElement("a");
    element.href = this.__url;
    if (this.__target !== null) {
      element.target = this.__target;
    }
    if (this.__rel !== null) {
      element.rel = this.__rel;
    }
    if (this.__title !== null) {
      element.title = this.__title;
    }
    addClassNamesToElement(element, config.theme.link);
    return element;
  }
  updateDOM(prevNode, anchor, config) {
    if (anchor instanceof HTMLAnchorElement) {
      const url = this.__url;
      const target = this.__target;
      const rel = this.__rel;
      const title = this.__title;
      if (url !== prevNode.__url) {
        anchor.href = url;
      }
      if (target !== prevNode.__target) {
        if (target) {
          anchor.target = target;
        } else {
          anchor.removeAttribute("target");
        }
      }
      if (rel !== prevNode.__rel) {
        if (rel) {
          anchor.rel = rel;
        } else {
          anchor.removeAttribute("rel");
        }
      }
      if (title !== prevNode.__title) {
        if (title) {
          anchor.title = title;
        } else {
          anchor.removeAttribute("title");
        }
      }
    }
    return false;
  }
  static importDOM() {
    return {
      a: (node) => ({
        conversion: $convertAnchorElement,
        priority: 1
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createLinkNode(serializedNode.url, {
      rel: serializedNode.rel,
      target: serializedNode.target,
      title: serializedNode.title
    });
    node.setDirection(serializedNode.direction);
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      rel: this.getRel(),
      target: this.getTarget(),
      title: this.getTitle(),
      type: "link",
      url: this.getURL(),
      version: 1
    };
  }
  getURL() {
    return this.getLatest().__url;
  }
  setURL(url) {
    const writable = this.getWritable();
    writable.__url = url;
  }
  getTarget() {
    return this.getLatest().__target;
  }
  setTarget(target) {
    const writable = this.getWritable();
    writable.__target = target;
  }
  getRel() {
    return this.getLatest().__rel;
  }
  setRel(rel) {
    const writable = this.getWritable();
    writable.__rel = rel;
  }
  getTitle() {
    return this.getLatest().__title;
  }
  setTitle(title) {
    const writable = this.getWritable();
    writable.__title = title;
  }
  insertNewAfter(_, restoreSelection = true) {
    const linkNode = $createLinkNode(this.__url, {
      rel: this.__rel,
      target: this.__target,
      title: this.__title
    });
    this.insertAfter(linkNode, restoreSelection);
    return linkNode;
  }
  canInsertTextBefore() {
    return false;
  }
  canInsertTextAfter() {
    return false;
  }
  canBeEmpty() {
    return false;
  }
  isInline() {
    return true;
  }
  extractWithChild(child, selection, destination) {
    if (!$isRangeSelection(selection)) {
      return false;
    }
    const anchorNode = selection.anchor.getNode();
    const focusNode = selection.focus.getNode();
    return this.isParentOf(anchorNode) && this.isParentOf(focusNode) && selection.getTextContent().length > 0;
  }
  isEmailURI() {
    return this.__url.startsWith("mailto:");
  }
  isWebSiteURI() {
    return this.__url.startsWith("https://") || this.__url.startsWith("http://");
  }
};
function $convertAnchorElement(domNode) {
  let node = null;
  if (isHTMLAnchorElement(domNode)) {
    const content = domNode.textContent;
    if (content !== null && content !== "" || domNode.children.length > 0) {
      node = $createLinkNode(domNode.getAttribute("href") || "", {
        rel: domNode.getAttribute("rel"),
        target: domNode.getAttribute("target"),
        title: domNode.getAttribute("title")
      });
    }
  }
  return { node };
}
function $createLinkNode(url, attributes) {
  return $applyNodeReplacement(new LinkNode(url, attributes));
}
function $isLinkNode(node) {
  return node instanceof LinkNode;
}
var TOGGLE_LINK_COMMAND = createCommand("TOGGLE_LINK_COMMAND");

// resources/js/wysiwyg/lexical/rich-text/LexicalImageNode.ts
var ImageNode = class _ImageNode extends ElementNode8 {
  constructor(src, options, key) {
    super(key);
    __publicField(this, "__src", "");
    __publicField(this, "__alt", "");
    __publicField(this, "__width", 0);
    __publicField(this, "__height", 0);
    __publicField(this, "__alignment", "");
    this.__src = src;
    if (options.alt) {
      this.__alt = options.alt;
    }
    if (options.width) {
      this.__width = options.width;
    }
    if (options.height) {
      this.__height = options.height;
    }
  }
  static getType() {
    return "image";
  }
  static clone(node) {
    const newNode = new _ImageNode(node.__src, {
      alt: node.__alt,
      width: node.__width,
      height: node.__height
    }, node.__key);
    newNode.__alignment = node.__alignment;
    return newNode;
  }
  setSrc(src) {
    const self = this.getWritable();
    self.__src = src;
  }
  getSrc() {
    const self = this.getLatest();
    return self.__src;
  }
  setAltText(altText) {
    const self = this.getWritable();
    self.__alt = altText;
  }
  getAltText() {
    const self = this.getLatest();
    return self.__alt;
  }
  setHeight(height) {
    const self = this.getWritable();
    self.__height = height;
  }
  getHeight() {
    const self = this.getLatest();
    return self.__height;
  }
  setWidth(width) {
    const self = this.getWritable();
    self.__width = width;
  }
  getWidth() {
    const self = this.getLatest();
    return self.__width;
  }
  setAlignment(alignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }
  getAlignment() {
    const self = this.getLatest();
    return self.__alignment;
  }
  isInline() {
    return true;
  }
  createDOM(_config, _editor) {
    const element = document.createElement("img");
    element.setAttribute("src", this.__src);
    if (this.__width) {
      element.setAttribute("width", String(this.__width));
    }
    if (this.__height) {
      element.setAttribute("height", String(this.__height));
    }
    if (this.__alt) {
      element.setAttribute("alt", this.__alt);
    }
    if (this.__alignment) {
      element.classList.add("align-" + this.__alignment);
    }
    element.addEventListener("click", (e) => {
      _editor.update(() => {
        this.select();
      });
    });
    return element;
  }
  updateDOM(prevNode, dom) {
    if (prevNode.__src !== this.__src) {
      dom.setAttribute("src", this.__src);
    }
    if (prevNode.__width !== this.__width) {
      if (this.__width) {
        dom.setAttribute("width", String(this.__width));
      } else {
        dom.removeAttribute("width");
      }
    }
    if (prevNode.__height !== this.__height) {
      if (this.__height) {
        dom.setAttribute("height", String(this.__height));
      } else {
        dom.removeAttribute("height");
      }
    }
    if (prevNode.__alt !== this.__alt) {
      if (this.__alt) {
        dom.setAttribute("alt", String(this.__alt));
      } else {
        dom.removeAttribute("alt");
      }
    }
    if (prevNode.__alignment !== this.__alignment) {
      if (prevNode.__alignment) {
        dom.classList.remove("align-" + prevNode.__alignment);
      }
      if (this.__alignment) {
        dom.classList.add("align-" + this.__alignment);
      }
    }
    return false;
  }
  static importDOM() {
    return {
      img(node) {
        return {
          conversion: (element) => {
            const src = element.getAttribute("src") || "";
            const options = {
              alt: element.getAttribute("alt") || "",
              height: Number.parseInt(element.getAttribute("height") || "0"),
              width: Number.parseInt(element.getAttribute("width") || "0")
            };
            const node2 = new _ImageNode(src, options);
            node2.setAlignment(extractAlignmentFromElement(element));
            return { node: node2 };
          },
          priority: 3
        };
      }
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "image",
      version: 1,
      src: this.__src,
      alt: this.__alt,
      height: this.__height,
      width: this.__width,
      alignment: this.__alignment
    };
  }
  static importJSON(serializedNode) {
    const node = $createImageNode(serializedNode.src, {
      alt: serializedNode.alt,
      width: serializedNode.width,
      height: serializedNode.height
    });
    node.setAlignment(serializedNode.alignment);
    return node;
  }
};
function $createImageNode(src, options = {}) {
  return new ImageNode(src, options);
}
function $isImageNode(node) {
  return node instanceof ImageNode;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalDetailsNode.ts
var DetailsNode = class _DetailsNode extends ElementNode8 {
  constructor() {
    super(...arguments);
    __publicField(this, "__id", "");
    __publicField(this, "__summary", "");
    __publicField(this, "__open", false);
  }
  static getType() {
    return "details";
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  setSummary(summary) {
    const self = this.getWritable();
    self.__summary = summary;
  }
  getSummary() {
    const self = this.getLatest();
    return self.__summary;
  }
  setOpen(open) {
    const self = this.getWritable();
    self.__open = open;
  }
  getOpen() {
    const self = this.getLatest();
    return self.__open;
  }
  static clone(node) {
    const newNode = new _DetailsNode(node.__key);
    newNode.__id = node.__id;
    newNode.__dir = node.__dir;
    newNode.__summary = node.__summary;
    newNode.__open = node.__open;
    return newNode;
  }
  createDOM(_config, _editor) {
    const el3 = document.createElement("details");
    if (this.__id) {
      el3.setAttribute("id", this.__id);
    }
    if (this.__dir) {
      el3.setAttribute("dir", this.__dir);
    }
    if (this.__open) {
      el3.setAttribute("open", "true");
    }
    const summary = document.createElement("summary");
    summary.textContent = this.__summary;
    summary.setAttribute("contenteditable", "false");
    summary.addEventListener("click", (event) => {
      event.preventDefault();
      _editor.update(() => {
        this.select();
      });
    });
    el3.append(summary);
    return el3;
  }
  updateDOM(prevNode, dom) {
    if (prevNode.__open !== this.__open) {
      dom.toggleAttribute("open", this.__open);
    }
    return prevNode.__id !== this.__id || prevNode.__dir !== this.__dir || prevNode.__summary !== this.__summary;
  }
  static importDOM() {
    return {
      details(node) {
        return {
          conversion: (element) => {
            const node2 = new _DetailsNode();
            if (element.id) {
              node2.setId(element.id);
            }
            if (element.dir) {
              node2.setDirection(extractDirectionFromElement(element));
            }
            const summaryElem = Array.from(element.children).find((e) => e.nodeName === "SUMMARY");
            node2.setSummary(summaryElem?.textContent || "");
            return { node: node2 };
          },
          priority: 3
        };
      },
      summary(node) {
        return {
          conversion: (element) => {
            return { node: "ignore" };
          },
          priority: 3
        };
      }
    };
  }
  exportDOM(editor) {
    const element = this.createDOM(editor._config, editor);
    const editable = element.querySelectorAll("[contenteditable]");
    for (const elem of editable) {
      elem.removeAttribute("contenteditable");
    }
    element.removeAttribute("open");
    return { element };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "details",
      version: 1,
      id: this.__id,
      summary: this.__summary
    };
  }
  static importJSON(serializedNode) {
    const node = $createDetailsNode();
    node.setId(serializedNode.id);
    node.setDirection(serializedNode.direction);
    return node;
  }
};
function $createDetailsNode() {
  return new DetailsNode();
}
function $isDetailsNode(node) {
  return node instanceof DetailsNode;
}

// resources/js/wysiwyg/lexical/list/utils.ts
function $getListDepth(listNode) {
  let depth = 1;
  let parent = listNode.getParent();
  while (parent != null) {
    if ($isListItemNode(parent)) {
      const parentList = parent.getParent();
      if ($isListNode(parentList)) {
        depth++;
        parent = parentList.getParent();
        continue;
      }
      invariant(false, "A ListItemNode must have a ListNode for a parent.");
    }
    return depth;
  }
  return depth;
}
function $getTopListNode(listItem) {
  let list = listItem.getParent();
  if (!$isListNode(list)) {
    invariant(false, "A ListItemNode must have a ListNode for a parent.");
  }
  let parent = list;
  while (parent !== null) {
    parent = parent.getParent();
    if ($isListNode(parent)) {
      list = parent;
    }
  }
  return list;
}
function $getAllListItems(node) {
  let listItemNodes = [];
  const listChildren = node.getChildren().filter($isListItemNode);
  for (let i = 0; i < listChildren.length; i++) {
    const listItemNode = listChildren[i];
    const firstChild = listItemNode.getFirstChild();
    if ($isListNode(firstChild)) {
      listItemNodes = listItemNodes.concat($getAllListItems(firstChild));
    } else {
      listItemNodes.push(listItemNode);
    }
  }
  return listItemNodes;
}
var NestedListNodeBrand = Symbol.for(
  "@lexical/NestedListNodeBrand"
);
function isNestedListNode(node) {
  return $isListItemNode(node) && $isListNode(node.getFirstChild());
}
function $wrapInListItem(node) {
  const listItemWrapper = $createListItemNode();
  return listItemWrapper.append(node);
}

// resources/js/wysiwyg/lexical/list/formatList.ts
function $isSelectingEmptyListItem(anchorNode, nodes) {
  return $isListItemNode(anchorNode) && (nodes.length === 0 || nodes.length === 1 && anchorNode.is(nodes[0]) && anchorNode.getChildrenSize() === 0);
}
function insertList(editor, listType) {
  editor.update(() => {
    const selection = $getSelection();
    if (selection !== null) {
      const nodes = selection.getNodes();
      if ($isRangeSelection(selection)) {
        const anchorAndFocus = selection.getStartEndPoints();
        invariant(
          anchorAndFocus !== null,
          "insertList: anchor should be defined"
        );
        const [anchor] = anchorAndFocus;
        const anchorNode = anchor.getNode();
        const anchorNodeParent = anchorNode.getParent();
        if ($isSelectingEmptyListItem(anchorNode, nodes)) {
          const list = $createListNode(listType);
          if ($isRootOrShadowRoot(anchorNodeParent)) {
            anchorNode.replace(list);
            const listItem = $createListItemNode();
            list.append(listItem);
          } else if ($isListItemNode(anchorNode)) {
            const parent = anchorNode.getParentOrThrow();
            append(list, parent.getChildren());
            parent.replace(list);
          }
          return;
        }
      }
      const handled = /* @__PURE__ */ new Set();
      for (let i = 0; i < nodes.length; i++) {
        const node = nodes[i];
        if ($isElementNode(node) && node.isEmpty() && !$isListItemNode(node) && !handled.has(node.getKey())) {
          $createListOrMerge(node, listType);
          continue;
        }
        if ($isLeafNode(node)) {
          let parent = node.getParent();
          while (parent != null) {
            const parentKey = parent.getKey();
            if ($isListNode(parent)) {
              if (!handled.has(parentKey)) {
                const newListNode = $createListNode(listType);
                append(newListNode, parent.getChildren());
                parent.replace(newListNode);
                handled.add(parentKey);
              }
              break;
            } else {
              const nextParent = parent.getParent();
              if ($isRootOrShadowRoot(nextParent) && !handled.has(parentKey)) {
                handled.add(parentKey);
                $createListOrMerge(parent, listType);
                break;
              }
              parent = nextParent;
            }
          }
        }
      }
    }
  });
}
function append(node, nodesToAppend) {
  node.splice(node.getChildrenSize(), 0, nodesToAppend);
}
function $createListOrMerge(node, listType) {
  if ($isListNode(node)) {
    return node;
  }
  const previousSibling = node.getPreviousSibling();
  const nextSibling = node.getNextSibling();
  const listItem = $createListItemNode();
  append(listItem, node.getChildren());
  if ($isListNode(previousSibling) && listType === previousSibling.getListType()) {
    previousSibling.append(listItem);
    node.remove();
    if ($isListNode(nextSibling) && listType === nextSibling.getListType()) {
      append(previousSibling, nextSibling.getChildren());
      nextSibling.remove();
    }
    return previousSibling;
  } else if ($isListNode(nextSibling) && listType === nextSibling.getListType()) {
    nextSibling.getFirstChildOrThrow().insertBefore(listItem);
    node.remove();
    return nextSibling;
  } else {
    const list = $createListNode(listType);
    list.append(listItem);
    node.replace(list);
    return list;
  }
}
function mergeLists(list1, list2) {
  const listItem1 = list1.getLastChild();
  const listItem2 = list2.getFirstChild();
  if (listItem1 && listItem2 && isNestedListNode(listItem1) && isNestedListNode(listItem2)) {
    mergeLists(listItem1.getFirstChild(), listItem2.getFirstChild());
    listItem2.remove();
  }
  const toMerge = list2.getChildren();
  if (toMerge.length > 0) {
    list1.append(...toMerge);
  }
  list2.remove();
}
function removeList(editor) {
  editor.update(() => {
    const selection = $getSelection();
    if ($isRangeSelection(selection)) {
      const listNodes = /* @__PURE__ */ new Set();
      const nodes = selection.getNodes();
      const anchorNode = selection.anchor.getNode();
      if ($isSelectingEmptyListItem(anchorNode, nodes)) {
        listNodes.add($getTopListNode(anchorNode));
      } else {
        for (let i = 0; i < nodes.length; i++) {
          const node = nodes[i];
          if ($isLeafNode(node)) {
            const listItemNode = $getNearestNodeOfType(node, ListItemNode2);
            if (listItemNode != null) {
              listNodes.add($getTopListNode(listItemNode));
            }
          }
        }
      }
      for (const listNode of listNodes) {
        let insertionPoint = listNode;
        const listItems = $getAllListItems(listNode);
        for (const listItemNode of listItems) {
          const paragraph2 = $createParagraphNode();
          append(paragraph2, listItemNode.getChildren());
          insertionPoint.insertAfter(paragraph2);
          insertionPoint = paragraph2;
          if (listItemNode.__key === selection.anchor.key) {
            selection.anchor.set(paragraph2.getKey(), 0, "element");
          }
          if (listItemNode.__key === selection.focus.key) {
            selection.focus.set(paragraph2.getKey(), 0, "element");
          }
          listItemNode.remove();
        }
        listNode.remove();
      }
    }
  });
}
function updateChildrenListItemValue(list) {
  const isNotChecklist = list.getListType() !== "check";
  let value = list.getStart();
  for (const child of list.getChildren()) {
    if ($isListItemNode(child)) {
      if (child.getValue() !== value) {
        child.setValue(value);
      }
      if (isNotChecklist && child.getLatest().__checked != null) {
        child.setChecked(void 0);
      }
      if (!$isListNode(child.getFirstChild())) {
        value++;
      }
    }
  }
}
function mergeNextSiblingListIfSameType(list) {
  const nextSibling = list.getNextSibling();
  if ($isListNode(nextSibling) && list.getListType() === nextSibling.getListType()) {
    mergeLists(list, nextSibling);
  }
}

// resources/js/wysiwyg/lexical/list/LexicalListItemNode.ts
var ListItemNode2 = class _ListItemNode extends ElementNode8 {
  constructor(value, checked, key) {
    super(key);
    /** @internal */
    __publicField(this, "__value");
    /** @internal */
    __publicField(this, "__checked");
    this.__value = value === void 0 ? 1 : value;
    this.__checked = checked;
  }
  static getType() {
    return "listitem";
  }
  static clone(node) {
    return new _ListItemNode(node.__value, node.__checked, node.__key);
  }
  createDOM(config) {
    const element = document.createElement("li");
    const parent = this.getParent();
    if ($isListNode(parent) && parent.getListType() === "check") {
      updateListItemChecked(element, this);
    }
    element.value = this.__value;
    if ($hasNestedListWithoutLabel(this)) {
      element.style.listStyle = "none";
    }
    return element;
  }
  updateDOM(prevNode, dom, config) {
    const parent = this.getParent();
    if ($isListNode(parent) && parent.getListType() === "check") {
      updateListItemChecked(dom, this);
    }
    dom.style.listStyle = $hasNestedListWithoutLabel(this) ? "none" : "";
    dom.value = this.__value;
    return false;
  }
  static transform() {
    return (node) => {
      invariant($isListItemNode(node), "node is not a ListItemNode");
      if (node.__checked == null) {
        return;
      }
      const parent = node.getParent();
      if ($isListNode(parent)) {
        if (parent.getListType() !== "check" && node.getChecked() != null) {
          node.setChecked(void 0);
        }
      }
    };
  }
  static importDOM() {
    return {
      li: () => ({
        conversion: $convertListItemElement,
        priority: 0
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createListItemNode();
    node.setChecked(serializedNode.checked);
    node.setValue(serializedNode.value);
    node.setDirection(serializedNode.direction);
    return node;
  }
  exportDOM(editor) {
    const element = this.createDOM(editor._config);
    if (element.classList.contains("task-list-item")) {
      const input = el("input", {
        type: "checkbox",
        disabled: "disabled"
      });
      if (element.hasAttribute("checked")) {
        input.setAttribute("checked", "checked");
        element.removeAttribute("checked");
      }
      element.prepend(input);
    }
    return {
      element
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      checked: this.getChecked(),
      type: "listitem",
      value: this.getValue(),
      version: 1
    };
  }
  append(...nodes) {
    for (let i = 0; i < nodes.length; i++) {
      const node = nodes[i];
      if ($isElementNode(node) && this.canMergeWith(node)) {
        const children = node.getChildren();
        this.append(...children);
        node.remove();
      } else {
        super.append(node);
      }
    }
    return this;
  }
  replace(replaceWithNode, includeChildren) {
    if ($isListItemNode(replaceWithNode)) {
      return super.replace(replaceWithNode);
    }
    const list = this.getParentOrThrow();
    if (!$isListNode(list)) {
      return replaceWithNode;
    }
    if (list.__first === this.getKey()) {
      list.insertBefore(replaceWithNode);
    } else if (list.__last === this.getKey()) {
      list.insertAfter(replaceWithNode);
    } else {
      const newList = $createListNode(list.getListType());
      let nextSibling = this.getNextSibling();
      while (nextSibling) {
        const nodeToAppend = nextSibling;
        nextSibling = nextSibling.getNextSibling();
        newList.append(nodeToAppend);
      }
      list.insertAfter(replaceWithNode);
      replaceWithNode.insertAfter(newList);
    }
    if (includeChildren) {
      invariant(
        $isElementNode(replaceWithNode),
        "includeChildren should only be true for ElementNodes"
      );
      this.getChildren().forEach((child) => {
        replaceWithNode.append(child);
      });
    }
    this.remove();
    if (list.getChildrenSize() === 0) {
      list.remove();
    }
    return replaceWithNode;
  }
  insertAfter(node, restoreSelection = true) {
    const listNode = this.getParentOrThrow();
    if (!$isListNode(listNode)) {
      invariant(
        false,
        "insertAfter: list node is not parent of list item node"
      );
    }
    if ($isListItemNode(node)) {
      return super.insertAfter(node, restoreSelection);
    }
    const siblings = this.getNextSiblings();
    listNode.insertAfter(node, restoreSelection);
    if (siblings.length !== 0) {
      const newListNode = $createListNode(listNode.getListType());
      siblings.forEach((sibling) => newListNode.append(sibling));
      node.insertAfter(newListNode, restoreSelection);
    }
    return node;
  }
  remove(preserveEmptyParent) {
    const prevSibling = this.getPreviousSibling();
    const nextSibling = this.getNextSibling();
    super.remove(preserveEmptyParent);
    if (prevSibling && nextSibling && isNestedListNode(prevSibling) && isNestedListNode(nextSibling)) {
      mergeLists(prevSibling.getFirstChild(), nextSibling.getFirstChild());
      nextSibling.remove();
    }
  }
  insertNewAfter(_, restoreSelection = true) {
    if (this.getTextContent().trim() === "" && this.isLastChild()) {
      const list = this.getParentOrThrow();
      const parentListItem = list.getParent();
      if ($isListItemNode(parentListItem)) {
        parentListItem.insertAfter(this);
        this.selectStart();
        return null;
      } else {
        const paragraph2 = $createParagraphNode();
        list.insertAfter(paragraph2, restoreSelection);
        this.remove();
        return paragraph2;
      }
    }
    const newElement = $createListItemNode(
      this.__checked == null ? void 0 : false
    );
    this.insertAfter(newElement, restoreSelection);
    return newElement;
  }
  collapseAtStart(selection) {
    const paragraph2 = $createParagraphNode();
    const children = this.getChildren();
    children.forEach((child) => paragraph2.append(child));
    const listNode = this.getParentOrThrow();
    const listNodeParent = listNode.getParentOrThrow();
    const isIndented = $isListItemNode(listNodeParent);
    if (listNode.getChildrenSize() === 1) {
      if (isIndented) {
        listNode.remove();
        listNodeParent.select();
      } else {
        listNode.insertBefore(paragraph2);
        listNode.remove();
        const anchor = selection.anchor;
        const focus = selection.focus;
        const key = paragraph2.getKey();
        if (anchor.type === "element" && anchor.getNode().is(this)) {
          anchor.set(key, anchor.offset, "element");
        }
        if (focus.type === "element" && focus.getNode().is(this)) {
          focus.set(key, focus.offset, "element");
        }
      }
    } else {
      listNode.insertBefore(paragraph2);
      this.remove();
    }
    return true;
  }
  getValue() {
    const self = this.getLatest();
    return self.__value;
  }
  setValue(value) {
    const self = this.getWritable();
    self.__value = value;
  }
  getChecked() {
    const self = this.getLatest();
    let listType;
    const parent = this.getParent();
    if ($isListNode(parent)) {
      listType = parent.getListType();
    }
    return listType === "check" ? Boolean(self.__checked) : void 0;
  }
  setChecked(checked) {
    const self = this.getWritable();
    self.__checked = checked;
  }
  toggleChecked() {
    this.setChecked(!this.__checked);
  }
  /** @deprecated @internal */
  canInsertAfter(node) {
    return $isListItemNode(node);
  }
  /** @deprecated @internal */
  canReplaceWith(replacement) {
    return $isListItemNode(replacement);
  }
  canMergeWith(node) {
    return $isParagraphNode(node) || $isListItemNode(node);
  }
  extractWithChild(child, selection) {
    if (!$isRangeSelection(selection)) {
      return false;
    }
    const anchorNode = selection.anchor.getNode();
    const focusNode = selection.focus.getNode();
    return this.isParentOf(anchorNode) && this.isParentOf(focusNode) && this.getTextContent().length === selection.getTextContent().length;
  }
  isParentRequired() {
    return true;
  }
  createParentElementNode() {
    return $createListNode("bullet");
  }
  canMergeWhenEmpty() {
    return true;
  }
};
function $hasNestedListWithoutLabel(node) {
  const children = node.getChildren();
  let hasLabel = false;
  let hasNestedList = false;
  for (const child of children) {
    if ($isListNode(child)) {
      hasNestedList = true;
    } else if (child.getTextContent().trim().length > 0) {
      hasLabel = true;
    }
  }
  return hasNestedList && !hasLabel;
}
function updateListItemChecked(dom, listItemNode) {
  const shouldBeTaskItem = !$isListNode(listItemNode.getFirstChild());
  dom.classList.toggle("task-list-item", shouldBeTaskItem);
  if (listItemNode.__checked) {
    dom.setAttribute("checked", "checked");
  } else {
    dom.removeAttribute("checked");
  }
}
function $convertListItemElement(domNode) {
  const isGitHubCheckList = domNode.classList.contains("task-list-item");
  if (isGitHubCheckList) {
    for (const child of domNode.children) {
      if (child.tagName === "INPUT") {
        return $convertCheckboxInput(child);
      }
    }
  }
  const ariaCheckedAttr = domNode.getAttribute("aria-checked");
  const checked = ariaCheckedAttr === "true" ? true : ariaCheckedAttr === "false" ? false : void 0;
  return { node: $createListItemNode(checked) };
}
function $convertCheckboxInput(domNode) {
  const isCheckboxInput = domNode.getAttribute("type") === "checkbox";
  if (!isCheckboxInput) {
    return { node: null };
  }
  const checked = domNode.hasAttribute("checked");
  return { node: $createListItemNode(checked) };
}
function $createListItemNode(checked) {
  return $applyNodeReplacement(new ListItemNode2(void 0, checked));
}
function $isListItemNode(node) {
  return node instanceof ListItemNode2;
}

// resources/js/wysiwyg/lexical/list/LexicalListNode.ts
var ListNode3 = class _ListNode extends ElementNode8 {
  constructor(listType, start, key) {
    super(key);
    /** @internal */
    __publicField(this, "__tag");
    /** @internal */
    __publicField(this, "__start");
    /** @internal */
    __publicField(this, "__listType");
    /** @internal */
    __publicField(this, "__id", "");
    const _listType = TAG_TO_LIST_TYPE[listType] || listType;
    this.__listType = _listType;
    this.__tag = _listType === "number" ? "ol" : "ul";
    this.__start = start;
  }
  static getType() {
    return "list";
  }
  static clone(node) {
    const newNode = new _ListNode(node.__listType, node.__start, node.__key);
    newNode.__id = node.__id;
    newNode.__dir = node.__dir;
    return newNode;
  }
  getTag() {
    return this.__tag;
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  setListType(type) {
    const writable = this.getWritable();
    writable.__listType = type;
    writable.__tag = type === "number" ? "ol" : "ul";
  }
  getListType() {
    return this.__listType;
  }
  getStart() {
    return this.__start;
  }
  // View
  createDOM(config, _editor) {
    const tag = this.__tag;
    const dom = document.createElement(tag);
    if (this.__start !== 1) {
      dom.setAttribute("start", String(this.__start));
    }
    dom.__lexicalListType = this.__listType;
    $setListThemeClassNames(dom, config.theme, this);
    if (this.__id) {
      dom.setAttribute("id", this.__id);
    }
    if (this.__dir) {
      dom.setAttribute("dir", this.__dir);
    }
    return dom;
  }
  updateDOM(prevNode, dom, config) {
    if (prevNode.__tag !== this.__tag || prevNode.__dir !== this.__dir || prevNode.__id !== this.__id) {
      return true;
    }
    $setListThemeClassNames(dom, config.theme, this);
    return false;
  }
  static transform() {
    return (node) => {
      invariant($isListNode(node), "node is not a ListNode");
      mergeNextSiblingListIfSameType(node);
      updateChildrenListItemValue(node);
    };
  }
  static importDOM() {
    return {
      ol: () => ({
        conversion: $convertListNode,
        priority: 0
      }),
      ul: () => ({
        conversion: $convertListNode,
        priority: 0
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createListNode(serializedNode.listType, serializedNode.start);
    node.setId(serializedNode.id);
    node.setDirection(serializedNode.direction);
    return node;
  }
  exportDOM(editor) {
    const { element } = super.exportDOM(editor);
    if (element && isHTMLElement(element)) {
      if (this.__start !== 1) {
        element.setAttribute("start", String(this.__start));
      }
      if (this.__listType === "check") {
        element.setAttribute("__lexicalListType", "check");
      }
    }
    return {
      element
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      listType: this.getListType(),
      start: this.getStart(),
      tag: this.getTag(),
      type: "list",
      version: 1,
      id: this.__id
    };
  }
  canBeEmpty() {
    return false;
  }
  canIndent() {
    return false;
  }
  append(...nodesToAppend) {
    for (let i = 0; i < nodesToAppend.length; i++) {
      const currentNode = nodesToAppend[i];
      if ($isListItemNode(currentNode)) {
        super.append(currentNode);
      } else {
        const listItemNode = $createListItemNode();
        if ($isListNode(currentNode)) {
          listItemNode.append(currentNode);
        } else if ($isElementNode(currentNode)) {
          const textNode = $createTextNode(currentNode.getTextContent());
          listItemNode.append(textNode);
        } else {
          listItemNode.append(currentNode);
        }
        super.append(listItemNode);
      }
    }
    return this;
  }
  extractWithChild(child) {
    return $isListItemNode(child);
  }
};
function $setListThemeClassNames(dom, editorThemeClasses, node) {
  const classesToAdd = [];
  const classesToRemove = [];
  const listTheme = editorThemeClasses.list;
  if (listTheme !== void 0) {
    const listLevelsClassNames = listTheme[`${node.__tag}Depth`] || [];
    const listDepth = $getListDepth(node) - 1;
    const normalizedListDepth = listDepth % listLevelsClassNames.length;
    const listLevelClassName = listLevelsClassNames[normalizedListDepth];
    const listClassName = listTheme[node.__tag];
    let nestedListClassName;
    const nestedListTheme = listTheme.nested;
    const checklistClassName = listTheme.checklist;
    if (nestedListTheme !== void 0 && nestedListTheme.list) {
      nestedListClassName = nestedListTheme.list;
    }
    if (listClassName !== void 0) {
      classesToAdd.push(listClassName);
    }
    if (checklistClassName !== void 0 && node.__listType === "check") {
      classesToAdd.push(checklistClassName);
    }
    if (listLevelClassName !== void 0) {
      classesToAdd.push(...normalizeClassNames(listLevelClassName));
      for (let i = 0; i < listLevelsClassNames.length; i++) {
        if (i !== normalizedListDepth) {
          classesToRemove.push(node.__tag + i);
        }
      }
    }
    if (nestedListClassName !== void 0) {
      const nestedListItemClasses = normalizeClassNames(nestedListClassName);
      if (listDepth > 1) {
        classesToAdd.push(...nestedListItemClasses);
      } else {
        classesToRemove.push(...nestedListItemClasses);
      }
    }
  }
  if (classesToRemove.length > 0) {
    removeClassNamesFromElement(dom, ...classesToRemove);
  }
  if (classesToAdd.length > 0) {
    addClassNamesToElement(dom, ...classesToAdd);
  }
}
function $normalizeChildren(nodes) {
  const normalizedListItems = [];
  for (const node of nodes) {
    if ($isListItemNode(node)) {
      normalizedListItems.push(node);
    } else {
      normalizedListItems.push($wrapInListItem(node));
    }
  }
  return normalizedListItems;
}
function isDomChecklist(domNode) {
  if (domNode.getAttribute("__lexicallisttype") === "check" || // is github checklist
  domNode.classList.contains("contains-task-list")) {
    return true;
  }
  for (const child of domNode.childNodes) {
    if (!isHTMLElement(child)) {
      continue;
    }
    if (child.hasAttribute("aria-checked")) {
      return true;
    }
    if (child.classList.contains("task-list-item")) {
      return true;
    }
    if (child.firstElementChild && child.firstElementChild.matches('input[type="checkbox"]')) {
      return true;
    }
  }
  return false;
}
function $convertListNode(domNode) {
  const nodeName = domNode.nodeName.toLowerCase();
  let node = null;
  if (nodeName === "ol") {
    const start = domNode.start;
    node = $createListNode("number", start);
  } else if (nodeName === "ul") {
    if (isDomChecklist(domNode)) {
      node = $createListNode("check");
    } else {
      node = $createListNode("bullet");
    }
  }
  if (domNode.id && node) {
    node.setId(domNode.id);
  }
  if (domNode.dir && node) {
    node.setDirection(extractDirectionFromElement(domNode));
  }
  return {
    after: $normalizeChildren,
    node
  };
}
var TAG_TO_LIST_TYPE = {
  ol: "number",
  ul: "bullet"
};
function $createListNode(listType, start = 1) {
  return $applyNodeReplacement(new ListNode3(listType, start));
}
function $isListNode(node) {
  return node instanceof ListNode3;
}

// resources/js/wysiwyg/lexical/list/index.ts
var INSERT_UNORDERED_LIST_COMMAND = createCommand("INSERT_UNORDERED_LIST_COMMAND");
var INSERT_ORDERED_LIST_COMMAND = createCommand(
  "INSERT_ORDERED_LIST_COMMAND"
);
var INSERT_CHECK_LIST_COMMAND = createCommand(
  "INSERT_CHECK_LIST_COMMAND"
);
var REMOVE_LIST_COMMAND = createCommand(
  "REMOVE_LIST_COMMAND"
);

// resources/js/wysiwyg/lexical/table/LexicalTableCellNode.ts
var TableCellHeaderStates = {
  BOTH: 3,
  COLUMN: 2,
  NO_STATUS: 0,
  ROW: 1
};
var TableCellNode = class _TableCellNode extends ElementNode8 {
  constructor(headerState = TableCellHeaderStates.NO_STATUS, colSpan = 1, width, key) {
    super(key);
    /** @internal */
    __publicField(this, "__colSpan");
    /** @internal */
    __publicField(this, "__rowSpan");
    /** @internal */
    __publicField(this, "__headerState");
    /** @internal */
    __publicField(this, "__width");
    /** @internal */
    __publicField(this, "__backgroundColor");
    /** @internal */
    __publicField(this, "__styles", /* @__PURE__ */ new Map());
    /** @internal */
    __publicField(this, "__alignment", "");
    this.__colSpan = colSpan;
    this.__rowSpan = 1;
    this.__headerState = headerState;
    this.__width = width;
    this.__backgroundColor = null;
  }
  static getType() {
    return "tablecell";
  }
  static clone(node) {
    const cellNode = new _TableCellNode(
      node.__headerState,
      node.__colSpan,
      node.__width,
      node.__key
    );
    cellNode.__rowSpan = node.__rowSpan;
    cellNode.__backgroundColor = node.__backgroundColor;
    cellNode.__styles = new Map(node.__styles);
    cellNode.__alignment = node.__alignment;
    return cellNode;
  }
  static importDOM() {
    return {
      td: (node) => ({
        conversion: $convertTableCellNodeElement,
        priority: 0
      }),
      th: (node) => ({
        conversion: $convertTableCellNodeElement,
        priority: 0
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createTableCellNode(
      serializedNode.headerState,
      serializedNode.colSpan,
      serializedNode.width
    );
    if (serializedNode.rowSpan) {
      node.setRowSpan(serializedNode.rowSpan);
    }
    node.setStyles(new Map(Object.entries(serializedNode.styles)));
    node.setAlignment(serializedNode.alignment);
    return node;
  }
  createDOM(config) {
    const element = document.createElement(
      this.getTag()
    );
    if (this.__width) {
      element.style.width = `${this.__width}px`;
    }
    if (this.__colSpan > 1) {
      element.colSpan = this.__colSpan;
    }
    if (this.__rowSpan > 1) {
      element.rowSpan = this.__rowSpan;
    }
    if (this.__backgroundColor !== null) {
      element.style.backgroundColor = this.__backgroundColor;
    }
    addClassNamesToElement(
      element,
      config.theme.tableCell,
      this.hasHeader() && config.theme.tableCellHeader
    );
    for (const [name, value] of this.__styles.entries()) {
      element.style.setProperty(name, value);
    }
    if (this.__alignment) {
      element.classList.add("align-" + this.__alignment);
    }
    return element;
  }
  exportDOM(editor) {
    const { element } = super.exportDOM(editor);
    return {
      element
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      backgroundColor: this.getBackgroundColor(),
      colSpan: this.__colSpan,
      headerState: this.__headerState,
      rowSpan: this.__rowSpan,
      type: "tablecell",
      width: this.getWidth(),
      styles: Object.fromEntries(this.__styles),
      alignment: this.__alignment
    };
  }
  getColSpan() {
    return this.__colSpan;
  }
  setColSpan(colSpan) {
    this.getWritable().__colSpan = colSpan;
    return this;
  }
  getRowSpan() {
    return this.__rowSpan;
  }
  setRowSpan(rowSpan) {
    this.getWritable().__rowSpan = rowSpan;
    return this;
  }
  getTag() {
    return this.hasHeader() ? "th" : "td";
  }
  setHeaderStyles(headerState) {
    const self = this.getWritable();
    self.__headerState = headerState;
    return this.__headerState;
  }
  getHeaderStyles() {
    return this.getLatest().__headerState;
  }
  setWidth(width) {
    const self = this.getWritable();
    self.__width = width;
    return this.__width;
  }
  getWidth() {
    return this.getLatest().__width;
  }
  clearWidth() {
    const self = this.getWritable();
    self.__width = void 0;
  }
  getStyles() {
    const self = this.getLatest();
    return new Map(self.__styles);
  }
  setStyles(styles) {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }
  setAlignment(alignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }
  getAlignment() {
    const self = this.getLatest();
    return self.__alignment;
  }
  updateTag(tag) {
    const isHeader = tag.toLowerCase() === "th";
    const state = isHeader ? TableCellHeaderStates.ROW : TableCellHeaderStates.NO_STATUS;
    const self = this.getWritable();
    self.__headerState = state;
  }
  getBackgroundColor() {
    return this.getLatest().__backgroundColor;
  }
  setBackgroundColor(newBackgroundColor) {
    this.getWritable().__backgroundColor = newBackgroundColor;
  }
  toggleHeaderStyle(headerStateToToggle) {
    const self = this.getWritable();
    if ((self.__headerState & headerStateToToggle) === headerStateToToggle) {
      self.__headerState -= headerStateToToggle;
    } else {
      self.__headerState += headerStateToToggle;
    }
    return self;
  }
  hasHeaderState(headerState) {
    return (this.getHeaderStyles() & headerState) === headerState;
  }
  hasHeader() {
    return this.getLatest().__headerState !== TableCellHeaderStates.NO_STATUS;
  }
  updateDOM(prevNode) {
    return prevNode.__headerState !== this.__headerState || prevNode.__width !== this.__width || prevNode.__colSpan !== this.__colSpan || prevNode.__rowSpan !== this.__rowSpan || prevNode.__backgroundColor !== this.__backgroundColor || prevNode.__styles !== this.__styles || prevNode.__alignment !== this.__alignment;
  }
  isShadowRoot() {
    return true;
  }
  collapseAtStart() {
    return true;
  }
  canBeEmpty() {
    return false;
  }
  canIndent() {
    return false;
  }
};
function $convertTableCellNodeElement(domNode) {
  const domNode_ = domNode;
  const nodeName = domNode.nodeName.toLowerCase();
  let width = void 0;
  const PIXEL_VALUE_REG_EXP = /^(\d+(?:\.\d+)?)px$/;
  if (PIXEL_VALUE_REG_EXP.test(domNode_.style.width)) {
    width = parseFloat(domNode_.style.width);
  }
  const tableCellNode = $createTableCellNode(
    nodeName === "th" ? TableCellHeaderStates.ROW : TableCellHeaderStates.NO_STATUS,
    domNode_.colSpan,
    width
  );
  tableCellNode.__rowSpan = domNode_.rowSpan;
  const style = domNode_.style;
  const textDecoration = style.textDecoration.split(" ");
  const hasBoldFontWeight = style.fontWeight === "700" || style.fontWeight === "bold";
  const hasLinethroughTextDecoration = textDecoration.includes("line-through");
  const hasItalicFontStyle = style.fontStyle === "italic";
  const hasUnderlineTextDecoration = textDecoration.includes("underline");
  if (domNode instanceof HTMLElement) {
    const styleMap = extractStyleMapFromElement(domNode);
    styleMap.delete("background-color");
    tableCellNode.setStyles(styleMap);
    tableCellNode.setAlignment(extractAlignmentFromElement(domNode));
  }
  const background = style.backgroundColor || null;
  if (background) {
    tableCellNode.setBackgroundColor(background);
  }
  return {
    after: (childLexicalNodes) => {
      if (childLexicalNodes.length === 0) {
        childLexicalNodes.push($createParagraphNode());
      }
      return childLexicalNodes;
    },
    forChild: (lexicalNode, parentLexicalNode) => {
      if ($isTableCellNode(parentLexicalNode) && !$isElementNode(lexicalNode)) {
        const paragraphNode = $createParagraphNode();
        if ($isLineBreakNode(lexicalNode) && lexicalNode.getTextContent() === "\n") {
          return null;
        }
        if ($isTextNode(lexicalNode)) {
          if (hasBoldFontWeight) {
            lexicalNode.toggleFormat("bold");
          }
          if (hasLinethroughTextDecoration) {
            lexicalNode.toggleFormat("strikethrough");
          }
          if (hasItalicFontStyle) {
            lexicalNode.toggleFormat("italic");
          }
          if (hasUnderlineTextDecoration) {
            lexicalNode.toggleFormat("underline");
          }
        }
        paragraphNode.append(lexicalNode);
        return paragraphNode;
      }
      return lexicalNode;
    },
    node: tableCellNode
  };
}
function $createTableCellNode(headerState = TableCellHeaderStates.NO_STATUS, colSpan = 1, width) {
  return $applyNodeReplacement(new TableCellNode(headerState, colSpan, width));
}
function $isTableCellNode(node) {
  return node instanceof TableCellNode;
}

// resources/js/wysiwyg/lexical/table/LexicalTableCommands.ts
var INSERT_TABLE_COMMAND = createCommand("INSERT_TABLE_COMMAND");

// resources/js/wysiwyg/lexical/table/LexicalTableRowNode.ts
var TableRowNode = class _TableRowNode extends ElementNode8 {
  constructor(key) {
    super(key);
    /** @internal */
    __publicField(this, "__height");
    /** @internal */
    __publicField(this, "__styles", /* @__PURE__ */ new Map());
  }
  static getType() {
    return "tablerow";
  }
  static clone(node) {
    const newNode = new _TableRowNode(node.__key);
    newNode.__styles = new Map(node.__styles);
    return newNode;
  }
  static importDOM() {
    return {
      tr: (node) => ({
        conversion: $convertTableRowElement,
        priority: 0
      })
    };
  }
  static importJSON(serializedNode) {
    const node = $createTableRowNode();
    node.setStyles(new Map(Object.entries(serializedNode.styles)));
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "tablerow",
      version: 1,
      styles: Object.fromEntries(this.__styles),
      height: this.__height || 0
    };
  }
  createDOM(config) {
    const element = document.createElement("tr");
    if (this.__height) {
      element.style.height = `${this.__height}px`;
    }
    for (const [name, value] of this.__styles.entries()) {
      element.style.setProperty(name, value);
    }
    addClassNamesToElement(element, config.theme.tableRow);
    return element;
  }
  isShadowRoot() {
    return true;
  }
  getStyles() {
    const self = this.getLatest();
    return new Map(self.__styles);
  }
  setStyles(styles) {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }
  setHeight(height) {
    const self = this.getWritable();
    self.__height = height;
    return this.__height;
  }
  getHeight() {
    return this.getLatest().__height;
  }
  updateDOM(prevNode) {
    return prevNode.__height !== this.__height || prevNode.__styles !== this.__styles;
  }
  canBeEmpty() {
    return false;
  }
  canIndent() {
    return false;
  }
};
function $convertTableRowElement(domNode) {
  const rowNode = $createTableRowNode();
  const domNode_ = domNode;
  const height = sizeToPixels(domNode_.style.height);
  rowNode.setHeight(height);
  if (domNode instanceof HTMLElement) {
    rowNode.setStyles(extractStyleMapFromElement(domNode));
  }
  return { node: rowNode };
}
function $createTableRowNode() {
  return $applyNodeReplacement(new TableRowNode());
}
function $isTableRowNode(node) {
  return node instanceof TableRowNode;
}

// resources/js/wysiwyg/lexical/table/LexicalCaptionNode.ts
var CaptionNode = class _CaptionNode extends ElementNode8 {
  static getType() {
    return "caption";
  }
  static clone(node) {
    return new _CaptionNode(node.__key);
  }
  createDOM(_config, _editor) {
    return document.createElement("caption");
  }
  updateDOM(_prevNode, _dom, _config) {
    return false;
  }
  isParentRequired() {
    return true;
  }
  canBeEmpty() {
    return false;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "caption",
      version: 1
    };
  }
  insertDOMIntoParent(nodeDOM, parentDOM) {
    parentDOM.insertBefore(nodeDOM, parentDOM.firstChild);
    return true;
  }
  static importJSON(serializedNode) {
    return $createCaptionNode();
  }
  static importDOM() {
    return {
      caption: (node) => ({
        conversion(domNode) {
          return {
            node: $createCaptionNode()
          };
        },
        priority: 0
      })
    };
  }
};
function $createCaptionNode() {
  return new CaptionNode();
}
function $isCaptionNode(node) {
  return node instanceof CaptionNode;
}
function $tableHasCaption(table2) {
  for (const child of table2.getChildren()) {
    if ($isCaptionNode(child)) {
      return true;
    }
  }
  return false;
}
function $addCaptionToTable(table2, text = "") {
  const caption = $createCaptionNode();
  const textNode = $createTextNode(text || " ");
  caption.append(textNode);
  table2.append(caption);
}

// resources/js/wysiwyg/lexical/table/LexicalTableUtils.ts
function $createTableNodeWithDimensions(rowCount, columnCount, includeHeaders = true) {
  const tableNode = $createTableNode();
  for (let iRow = 0; iRow < rowCount; iRow++) {
    const tableRowNode = $createTableRowNode();
    for (let iColumn = 0; iColumn < columnCount; iColumn++) {
      let headerState = TableCellHeaderStates.NO_STATUS;
      if (typeof includeHeaders === "object") {
        if (iRow === 0 && includeHeaders.rows) {
          headerState |= TableCellHeaderStates.ROW;
        }
        if (iColumn === 0 && includeHeaders.columns) {
          headerState |= TableCellHeaderStates.COLUMN;
        }
      } else if (includeHeaders) {
        if (iRow === 0) {
          headerState |= TableCellHeaderStates.ROW;
        }
        if (iColumn === 0) {
          headerState |= TableCellHeaderStates.COLUMN;
        }
      }
      const tableCellNode = $createTableCellNode(headerState);
      const paragraphNode = $createParagraphNode();
      paragraphNode.append($createTextNode());
      tableCellNode.append(paragraphNode);
      tableRowNode.append(tableCellNode);
    }
    tableNode.append(tableRowNode);
  }
  return tableNode;
}
var getHeaderState = (currentState, possibleState) => {
  if (currentState === TableCellHeaderStates.BOTH || currentState === possibleState) {
    return possibleState;
  }
  return TableCellHeaderStates.NO_STATUS;
};
function $insertTableRow__EXPERIMENTAL(insertAfter = true) {
  const selection = $getSelection();
  invariant(
    $isRangeSelection(selection) || $isTableSelection(selection),
    "Expected a RangeSelection or TableSelection"
  );
  const focus = selection.focus.getNode();
  const [focusCell, , grid] = $getNodeTriplet(focus);
  const [gridMap, focusCellMap] = $computeTableMap(grid, focusCell, focusCell);
  const columnCount = gridMap[0].length;
  const { startRow: focusStartRow } = focusCellMap;
  if (insertAfter) {
    const focusEndRow = focusStartRow + focusCell.__rowSpan - 1;
    const focusEndRowMap = gridMap[focusEndRow];
    const newRow = $createTableRowNode();
    for (let i = 0; i < columnCount; i++) {
      const { cell, startRow } = focusEndRowMap[i];
      if (startRow + cell.__rowSpan - 1 <= focusEndRow) {
        const currentCell = focusEndRowMap[i].cell;
        const currentCellHeaderState = currentCell.__headerState;
        const headerState = getHeaderState(
          currentCellHeaderState,
          TableCellHeaderStates.COLUMN
        );
        newRow.append(
          $createTableCellNode(headerState).append($createParagraphNode())
        );
      } else {
        cell.setRowSpan(cell.__rowSpan + 1);
      }
    }
    const focusEndRowNode = grid.getChildAtIndex(focusEndRow);
    invariant(
      $isTableRowNode(focusEndRowNode),
      "focusEndRow is not a TableRowNode"
    );
    focusEndRowNode.insertAfter(newRow);
  } else {
    const focusStartRowMap = gridMap[focusStartRow];
    const newRow = $createTableRowNode();
    for (let i = 0; i < columnCount; i++) {
      const { cell, startRow } = focusStartRowMap[i];
      if (startRow === focusStartRow) {
        const currentCell = focusStartRowMap[i].cell;
        const currentCellHeaderState = currentCell.__headerState;
        const headerState = getHeaderState(
          currentCellHeaderState,
          TableCellHeaderStates.COLUMN
        );
        newRow.append(
          $createTableCellNode(headerState).append($createParagraphNode())
        );
      } else {
        cell.setRowSpan(cell.__rowSpan + 1);
      }
    }
    const focusStartRowNode = grid.getChildAtIndex(focusStartRow);
    invariant(
      $isTableRowNode(focusStartRowNode),
      "focusEndRow is not a TableRowNode"
    );
    focusStartRowNode.insertBefore(newRow);
  }
}
function $insertTableColumn__EXPERIMENTAL(insertAfter = true) {
  const selection = $getSelection();
  invariant(
    $isRangeSelection(selection) || $isTableSelection(selection),
    "Expected a RangeSelection or TableSelection"
  );
  const anchor = selection.anchor.getNode();
  const focus = selection.focus.getNode();
  const [anchorCell] = $getNodeTriplet(anchor);
  const [focusCell, , grid] = $getNodeTriplet(focus);
  const [gridMap, focusCellMap, anchorCellMap] = $computeTableMap(
    grid,
    focusCell,
    anchorCell
  );
  const rowCount = gridMap.length;
  const startColumn = insertAfter ? Math.max(focusCellMap.startColumn, anchorCellMap.startColumn) : Math.min(focusCellMap.startColumn, anchorCellMap.startColumn);
  const insertAfterColumn = insertAfter ? startColumn + focusCell.__colSpan - 1 : startColumn - 1;
  const gridFirstChild = grid.getFirstChild();
  invariant(
    $isTableRowNode(gridFirstChild),
    "Expected firstTable child to be a row"
  );
  let firstInsertedCell = null;
  function $createTableCellNodeForInsertTableColumn(headerState = TableCellHeaderStates.NO_STATUS) {
    const cell = $createTableCellNode(headerState).append(
      $createParagraphNode()
    );
    if (firstInsertedCell === null) {
      firstInsertedCell = cell;
    }
    return cell;
  }
  let loopRow = gridFirstChild;
  rowLoop: for (let i = 0; i < rowCount; i++) {
    if (i !== 0) {
      const currentRow = loopRow.getNextSibling();
      invariant(
        $isTableRowNode(currentRow),
        "Expected row nextSibling to be a row"
      );
      loopRow = currentRow;
    }
    const rowMap = gridMap[i];
    const currentCellHeaderState = rowMap[insertAfterColumn < 0 ? 0 : insertAfterColumn].cell.__headerState;
    const headerState = getHeaderState(
      currentCellHeaderState,
      TableCellHeaderStates.ROW
    );
    if (insertAfterColumn < 0) {
      $insertFirst(
        loopRow,
        $createTableCellNodeForInsertTableColumn(headerState)
      );
      continue;
    }
    const {
      cell: currentCell,
      startColumn: currentStartColumn,
      startRow: currentStartRow
    } = rowMap[insertAfterColumn];
    if (currentStartColumn + currentCell.__colSpan - 1 <= insertAfterColumn) {
      let insertAfterCell = currentCell;
      let insertAfterCellRowStart = currentStartRow;
      let prevCellIndex = insertAfterColumn;
      while (insertAfterCellRowStart !== i && insertAfterCell.__rowSpan > 1) {
        prevCellIndex -= currentCell.__colSpan;
        if (prevCellIndex >= 0) {
          const { cell: cell_, startRow: startRow_ } = rowMap[prevCellIndex];
          insertAfterCell = cell_;
          insertAfterCellRowStart = startRow_;
        } else {
          loopRow.append($createTableCellNodeForInsertTableColumn(headerState));
          continue rowLoop;
        }
      }
      insertAfterCell.insertAfter(
        $createTableCellNodeForInsertTableColumn(headerState)
      );
    } else {
      currentCell.setColSpan(currentCell.__colSpan + 1);
    }
  }
  if (firstInsertedCell !== null) {
    $moveSelectionToCell(firstInsertedCell);
  }
}
function $deleteTableRow__EXPERIMENTAL() {
  const selection = $getSelection();
  invariant(
    $isRangeSelection(selection) || $isTableSelection(selection),
    "Expected a RangeSelection or TableSelection"
  );
  const anchor = selection.anchor.getNode();
  const focus = selection.focus.getNode();
  const [anchorCell, , grid] = $getNodeTriplet(anchor);
  const [focusCell] = $getNodeTriplet(focus);
  const [gridMap, anchorCellMap, focusCellMap] = $computeTableMap(
    grid,
    anchorCell,
    focusCell
  );
  const { startRow: anchorStartRow } = anchorCellMap;
  const { startRow: focusStartRow } = focusCellMap;
  const focusEndRow = focusStartRow + focusCell.__rowSpan - 1;
  if (gridMap.length === focusEndRow - anchorStartRow + 1) {
    grid.remove();
    return;
  }
  const columnCount = gridMap[0].length;
  const nextRow = gridMap[focusEndRow + 1];
  const nextRowNode = grid.getChildAtIndex(
    focusEndRow + 1
  );
  for (let row = focusEndRow; row >= anchorStartRow; row--) {
    for (let column = columnCount - 1; column >= 0; column--) {
      const {
        cell,
        startRow: cellStartRow,
        startColumn: cellStartColumn
      } = gridMap[row][column];
      if (cellStartColumn !== column) {
        continue;
      }
      if (row === anchorStartRow && cellStartRow < anchorStartRow) {
        cell.setRowSpan(cell.__rowSpan - (cellStartRow - anchorStartRow));
      }
      if (cellStartRow >= anchorStartRow && cellStartRow + cell.__rowSpan - 1 > focusEndRow) {
        cell.setRowSpan(cell.__rowSpan - (focusEndRow - cellStartRow + 1));
        invariant(nextRowNode !== null, "Expected nextRowNode not to be null");
        if (column === 0) {
          $insertFirst(nextRowNode, cell);
        } else {
          const { cell: previousCell } = nextRow[column - 1];
          previousCell.insertAfter(cell);
        }
      }
    }
    const rowNode = grid.getChildAtIndex(row);
    invariant(
      $isTableRowNode(rowNode),
      "Expected GridNode childAtIndex(%s) to be RowNode",
      String(row)
    );
    rowNode.remove();
  }
  if (nextRow !== void 0) {
    const { cell } = nextRow[0];
    $moveSelectionToCell(cell);
  } else {
    const previousRow = gridMap[anchorStartRow - 1];
    const { cell } = previousRow[0];
    $moveSelectionToCell(cell);
  }
}
function $deleteTableColumn__EXPERIMENTAL() {
  const selection = $getSelection();
  invariant(
    $isRangeSelection(selection) || $isTableSelection(selection),
    "Expected a RangeSelection or TableSelection"
  );
  const anchor = selection.anchor.getNode();
  const focus = selection.focus.getNode();
  const [anchorCell, , grid] = $getNodeTriplet(anchor);
  const [focusCell] = $getNodeTriplet(focus);
  const [gridMap, anchorCellMap, focusCellMap] = $computeTableMap(
    grid,
    anchorCell,
    focusCell
  );
  const { startColumn: anchorStartColumn } = anchorCellMap;
  const { startRow: focusStartRow, startColumn: focusStartColumn } = focusCellMap;
  const startColumn = Math.min(anchorStartColumn, focusStartColumn);
  const endColumn = Math.max(
    anchorStartColumn + anchorCell.__colSpan - 1,
    focusStartColumn + focusCell.__colSpan - 1
  );
  const selectedColumnCount = endColumn - startColumn + 1;
  const columnCount = gridMap[0].length;
  if (columnCount === endColumn - startColumn + 1) {
    grid.selectPrevious();
    grid.remove();
    return;
  }
  const rowCount = gridMap.length;
  for (let row = 0; row < rowCount; row++) {
    for (let column = startColumn; column <= endColumn; column++) {
      const { cell, startColumn: cellStartColumn } = gridMap[row][column];
      if (cellStartColumn < startColumn) {
        if (column === startColumn) {
          const overflowLeft = startColumn - cellStartColumn;
          cell.setColSpan(
            cell.__colSpan - // Possible overflow right too
            Math.min(selectedColumnCount, cell.__colSpan - overflowLeft)
          );
        }
      } else if (cellStartColumn + cell.__colSpan - 1 > endColumn) {
        if (column === endColumn) {
          const inSelectedArea = endColumn - cellStartColumn + 1;
          cell.setColSpan(cell.__colSpan - inSelectedArea);
        }
      } else {
        cell.remove();
      }
    }
  }
  const focusRowMap = gridMap[focusStartRow];
  const nextColumn = anchorStartColumn > focusStartColumn ? focusRowMap[anchorStartColumn + anchorCell.__colSpan] : focusRowMap[focusStartColumn + focusCell.__colSpan];
  if (nextColumn !== void 0) {
    const { cell } = nextColumn;
    $moveSelectionToCell(cell);
  } else {
    const previousRow = focusStartColumn < anchorStartColumn ? focusRowMap[focusStartColumn - 1] : focusRowMap[anchorStartColumn - 1];
    const { cell } = previousRow;
    $moveSelectionToCell(cell);
  }
}
function $moveSelectionToCell(cell) {
  const firstDescendant = cell.getFirstDescendant();
  if (firstDescendant == null) {
    cell.selectStart();
  } else {
    firstDescendant.getParentOrThrow().selectStart();
  }
}
function $insertFirst(parent, node) {
  const firstChild = parent.getFirstChild();
  if (firstChild !== null) {
    firstChild.insertBefore(node);
  } else {
    parent.append(node);
  }
}
function $unmergeCell() {
  const selection = $getSelection();
  invariant(
    $isRangeSelection(selection) || $isTableSelection(selection),
    "Expected a RangeSelection or TableSelection"
  );
  const anchor = selection.anchor.getNode();
  const [cell, row, grid] = $getNodeTriplet(anchor);
  const colSpan = cell.__colSpan;
  const rowSpan = cell.__rowSpan;
  if (colSpan > 1) {
    for (let i = 1; i < colSpan; i++) {
      cell.insertAfter(
        $createTableCellNode(TableCellHeaderStates.NO_STATUS).append(
          $createParagraphNode()
        )
      );
    }
    cell.setColSpan(1);
  }
  if (rowSpan > 1) {
    const [map, cellMap] = $computeTableMap(grid, cell, cell);
    const { startColumn, startRow } = cellMap;
    let currentRowNode;
    for (let i = 1; i < rowSpan; i++) {
      const currentRow = startRow + i;
      const currentRowMap = map[currentRow];
      currentRowNode = (currentRowNode || row).getNextSibling();
      invariant(
        $isTableRowNode(currentRowNode),
        "Expected row next sibling to be a row"
      );
      let insertAfterCell = null;
      for (let column = 0; column < startColumn; column++) {
        const currentCellMap = currentRowMap[column];
        const currentCell = currentCellMap.cell;
        if (currentCellMap.startRow === currentRow) {
          insertAfterCell = currentCell;
        }
        if (currentCell.__colSpan > 1) {
          column += currentCell.__colSpan - 1;
        }
      }
      if (insertAfterCell === null) {
        for (let j = 0; j < colSpan; j++) {
          $insertFirst(
            currentRowNode,
            $createTableCellNode(TableCellHeaderStates.NO_STATUS).append(
              $createParagraphNode()
            )
          );
        }
      } else {
        for (let j = 0; j < colSpan; j++) {
          insertAfterCell.insertAfter(
            $createTableCellNode(TableCellHeaderStates.NO_STATUS).append(
              $createParagraphNode()
            )
          );
        }
      }
    }
    cell.setRowSpan(1);
  }
}
function $computeTableMap(grid, cellA, cellB) {
  const [tableMap, cellAValue, cellBValue] = $computeTableMapSkipCellCheck(
    grid,
    cellA,
    cellB
  );
  invariant(cellAValue !== null, "Anchor not found in Grid");
  invariant(cellBValue !== null, "Focus not found in Grid");
  return [tableMap, cellAValue, cellBValue];
}
function $computeTableMapSkipCellCheck(grid, cellA, cellB) {
  const tableMap = [];
  let cellAValue = null;
  let cellBValue = null;
  function write(startRow, startColumn, cell) {
    const value = {
      cell,
      startColumn,
      startRow
    };
    const rowSpan = cell.__rowSpan;
    const colSpan = cell.__colSpan;
    for (let i = 0; i < rowSpan; i++) {
      if (tableMap[startRow + i] === void 0) {
        tableMap[startRow + i] = [];
      }
      for (let j = 0; j < colSpan; j++) {
        tableMap[startRow + i][startColumn + j] = value;
      }
    }
    if (cellA !== null && cellA.is(cell)) {
      cellAValue = value;
    }
    if (cellB !== null && cellB.is(cell)) {
      cellBValue = value;
    }
  }
  function isEmpty(row, column) {
    return tableMap[row] === void 0 || tableMap[row][column] === void 0;
  }
  const gridChildren = grid.getChildren().filter((node) => !$isCaptionNode(node));
  for (let i = 0; i < gridChildren.length; i++) {
    const row = gridChildren[i];
    invariant(
      $isTableRowNode(row),
      "Expected GridNode children to be TableRowNode"
    );
    const rowChildren = row.getChildren();
    let j = 0;
    for (const cell of rowChildren) {
      invariant(
        $isTableCellNode(cell),
        "Expected TableRowNode children to be TableCellNode"
      );
      while (!isEmpty(i, j)) {
        j++;
      }
      write(i, j, cell);
      j += cell.__colSpan;
    }
  }
  return [tableMap, cellAValue, cellBValue];
}
function $getNodeTriplet(source3) {
  let cell;
  if (source3 instanceof TableCellNode) {
    cell = source3;
  } else if ("__type" in source3) {
    const cell_ = $findMatchingParent(source3, $isTableCellNode);
    invariant(
      $isTableCellNode(cell_),
      "Expected to find a parent TableCellNode"
    );
    cell = cell_;
  } else {
    const cell_ = $findMatchingParent(source3.getNode(), $isTableCellNode);
    invariant(
      $isTableCellNode(cell_),
      "Expected to find a parent TableCellNode"
    );
    cell = cell_;
  }
  const row = cell.getParent();
  invariant(
    $isTableRowNode(row),
    "Expected TableCellNode to have a parent TableRowNode"
  );
  const grid = row.getParent();
  invariant(
    $isTableNode(grid),
    "Expected TableRowNode to have a parent GridNode"
  );
  return [cell, row, grid];
}
function $getTableCellNodeRect(tableCellNode) {
  const [cellNode, , gridNode] = $getNodeTriplet(tableCellNode);
  const rows = gridNode.getChildren();
  const rowCount = rows.length;
  const columnCount = rows[0].getChildren().length;
  const cellMatrix = new Array(rowCount);
  for (let i = 0; i < rowCount; i++) {
    cellMatrix[i] = new Array(columnCount);
  }
  for (let rowIndex = 0; rowIndex < rowCount; rowIndex++) {
    const row = rows[rowIndex];
    const cells = row.getChildren();
    let columnIndex = 0;
    for (let cellIndex = 0; cellIndex < cells.length; cellIndex++) {
      while (cellMatrix[rowIndex][columnIndex]) {
        columnIndex++;
      }
      const cell = cells[cellIndex];
      const rowSpan = cell.__rowSpan || 1;
      const colSpan = cell.__colSpan || 1;
      for (let i = 0; i < rowSpan; i++) {
        for (let j = 0; j < colSpan; j++) {
          cellMatrix[rowIndex + i][columnIndex + j] = cell;
        }
      }
      if (cellNode === cell) {
        return {
          colSpan,
          columnIndex,
          rowIndex,
          rowSpan
        };
      }
      columnIndex += colSpan;
    }
  }
  return null;
}

// resources/js/wysiwyg/lexical/table/LexicalTableSelection.ts
var TableSelection = class _TableSelection {
  constructor(tableKey, anchor, focus) {
    __publicField(this, "tableKey");
    __publicField(this, "anchor");
    __publicField(this, "focus");
    __publicField(this, "_cachedNodes");
    __publicField(this, "dirty");
    this.anchor = anchor;
    this.focus = focus;
    anchor._selection = this;
    focus._selection = this;
    this._cachedNodes = null;
    this.dirty = false;
    this.tableKey = tableKey;
  }
  getStartEndPoints() {
    return [this.anchor, this.focus];
  }
  /**
   * Returns whether the Selection is "backwards", meaning the focus
   * logically precedes the anchor in the EditorState.
   * @returns true if the Selection is backwards, false otherwise.
   */
  isBackward() {
    return this.focus.isBefore(this.anchor);
  }
  getCachedNodes() {
    return this._cachedNodes;
  }
  setCachedNodes(nodes) {
    this._cachedNodes = nodes;
  }
  is(selection) {
    if (!$isTableSelection(selection)) {
      return false;
    }
    return this.tableKey === selection.tableKey && this.anchor.is(selection.anchor) && this.focus.is(selection.focus);
  }
  set(tableKey, anchorCellKey, focusCellKey) {
    this.dirty = true;
    this.tableKey = tableKey;
    this.anchor.key = anchorCellKey;
    this.focus.key = focusCellKey;
    this._cachedNodes = null;
  }
  clone() {
    return new _TableSelection(this.tableKey, this.anchor, this.focus);
  }
  isCollapsed() {
    return false;
  }
  extract() {
    return this.getNodes();
  }
  insertRawText(text) {
  }
  insertText() {
  }
  insertNodes(nodes) {
    const focusNode = this.focus.getNode();
    invariant(
      $isElementNode(focusNode),
      "Expected TableSelection focus to be an ElementNode"
    );
    const selection = $normalizeSelection(
      focusNode.select(0, focusNode.getChildrenSize())
    );
    selection.insertNodes(nodes);
  }
  // TODO Deprecate this method. It's confusing when used with colspan|rowspan
  getShape() {
    const anchorCellNode = $getNodeByKey(this.anchor.key);
    invariant(
      $isTableCellNode(anchorCellNode),
      "Expected TableSelection anchor to be (or a child of) TableCellNode"
    );
    const anchorCellNodeRect = $getTableCellNodeRect(anchorCellNode);
    invariant(
      anchorCellNodeRect !== null,
      "getCellRect: expected to find AnchorNode"
    );
    const focusCellNode = $getNodeByKey(this.focus.key);
    invariant(
      $isTableCellNode(focusCellNode),
      "Expected TableSelection focus to be (or a child of) TableCellNode"
    );
    const focusCellNodeRect = $getTableCellNodeRect(focusCellNode);
    invariant(
      focusCellNodeRect !== null,
      "getCellRect: expected to find focusCellNode"
    );
    const startX = Math.min(
      anchorCellNodeRect.columnIndex,
      focusCellNodeRect.columnIndex
    );
    const stopX = Math.max(
      anchorCellNodeRect.columnIndex,
      focusCellNodeRect.columnIndex
    );
    const startY = Math.min(
      anchorCellNodeRect.rowIndex,
      focusCellNodeRect.rowIndex
    );
    const stopY = Math.max(
      anchorCellNodeRect.rowIndex,
      focusCellNodeRect.rowIndex
    );
    return {
      fromX: Math.min(startX, stopX),
      fromY: Math.min(startY, stopY),
      toX: Math.max(startX, stopX),
      toY: Math.max(startY, stopY)
    };
  }
  getNodes() {
    const cachedNodes = this._cachedNodes;
    if (cachedNodes !== null) {
      return cachedNodes;
    }
    const anchorNode = this.anchor.getNode();
    const focusNode = this.focus.getNode();
    const anchorCell = $findMatchingParent(anchorNode, $isTableCellNode);
    const focusCell = $findMatchingParent(focusNode, $isTableCellNode);
    invariant(
      $isTableCellNode(anchorCell),
      "Expected TableSelection anchor to be (or a child of) TableCellNode"
    );
    invariant(
      $isTableCellNode(focusCell),
      "Expected TableSelection focus to be (or a child of) TableCellNode"
    );
    const anchorRow = anchorCell.getParent();
    invariant(
      $isTableRowNode(anchorRow),
      "Expected anchorCell to have a parent TableRowNode"
    );
    const tableNode = anchorRow.getParent();
    invariant(
      $isTableNode(tableNode),
      "Expected tableNode to have a parent TableNode"
    );
    const focusCellGrid = focusCell.getParents()[1];
    if (focusCellGrid !== tableNode) {
      if (!tableNode.isParentOf(focusCell)) {
        const gridParent = tableNode.getParent();
        invariant(gridParent != null, "Expected gridParent to have a parent");
        this.set(this.tableKey, gridParent.getKey(), focusCell.getKey());
      } else {
        const focusCellParent = focusCellGrid.getParent();
        invariant(
          focusCellParent != null,
          "Expected focusCellParent to have a parent"
        );
        this.set(this.tableKey, focusCell.getKey(), focusCellParent.getKey());
      }
      return this.getNodes();
    }
    const [map, cellAMap, cellBMap] = $computeTableMap(
      tableNode,
      anchorCell,
      focusCell
    );
    let minColumn = Math.min(cellAMap.startColumn, cellBMap.startColumn);
    let minRow = Math.min(cellAMap.startRow, cellBMap.startRow);
    let maxColumn = Math.max(
      cellAMap.startColumn + cellAMap.cell.__colSpan - 1,
      cellBMap.startColumn + cellBMap.cell.__colSpan - 1
    );
    let maxRow = Math.max(
      cellAMap.startRow + cellAMap.cell.__rowSpan - 1,
      cellBMap.startRow + cellBMap.cell.__rowSpan - 1
    );
    let exploredMinColumn = minColumn;
    let exploredMinRow = minRow;
    let exploredMaxColumn = minColumn;
    let exploredMaxRow = minRow;
    function expandBoundary(mapValue) {
      const {
        cell,
        startColumn: cellStartColumn,
        startRow: cellStartRow
      } = mapValue;
      minColumn = Math.min(minColumn, cellStartColumn);
      minRow = Math.min(minRow, cellStartRow);
      maxColumn = Math.max(maxColumn, cellStartColumn + cell.__colSpan - 1);
      maxRow = Math.max(maxRow, cellStartRow + cell.__rowSpan - 1);
    }
    while (minColumn < exploredMinColumn || minRow < exploredMinRow || maxColumn > exploredMaxColumn || maxRow > exploredMaxRow) {
      if (minColumn < exploredMinColumn) {
        const rowDiff = exploredMaxRow - exploredMinRow;
        const previousColumn = exploredMinColumn - 1;
        for (let i = 0; i <= rowDiff; i++) {
          expandBoundary(map[exploredMinRow + i][previousColumn]);
        }
        exploredMinColumn = previousColumn;
      }
      if (minRow < exploredMinRow) {
        const columnDiff = exploredMaxColumn - exploredMinColumn;
        const previousRow = exploredMinRow - 1;
        for (let i = 0; i <= columnDiff; i++) {
          expandBoundary(map[previousRow][exploredMinColumn + i]);
        }
        exploredMinRow = previousRow;
      }
      if (maxColumn > exploredMaxColumn) {
        const rowDiff = exploredMaxRow - exploredMinRow;
        const nextColumn = exploredMaxColumn + 1;
        for (let i = 0; i <= rowDiff; i++) {
          expandBoundary(map[exploredMinRow + i][nextColumn]);
        }
        exploredMaxColumn = nextColumn;
      }
      if (maxRow > exploredMaxRow) {
        const columnDiff = exploredMaxColumn - exploredMinColumn;
        const nextRow = exploredMaxRow + 1;
        for (let i = 0; i <= columnDiff; i++) {
          expandBoundary(map[nextRow][exploredMinColumn + i]);
        }
        exploredMaxRow = nextRow;
      }
    }
    const nodes = [tableNode];
    let lastRow = null;
    for (let i = minRow; i <= maxRow; i++) {
      for (let j = minColumn; j <= maxColumn; j++) {
        const { cell } = map[i][j];
        const currentRow = cell.getParent();
        invariant(
          $isTableRowNode(currentRow),
          "Expected TableCellNode parent to be a TableRowNode"
        );
        if (currentRow !== lastRow) {
          nodes.push(currentRow);
        }
        nodes.push(cell, ...$getChildrenRecursively(cell));
        lastRow = currentRow;
      }
    }
    if (!isCurrentlyReadOnlyMode()) {
      this._cachedNodes = nodes;
    }
    return nodes;
  }
  getTextContent() {
    const nodes = this.getNodes().filter((node) => $isTableCellNode(node));
    let textContent = "";
    for (let i = 0; i < nodes.length; i++) {
      const node = nodes[i];
      const row = node.__parent;
      const nextRow = (nodes[i + 1] || {}).__parent;
      textContent += node.getTextContent() + (nextRow !== row ? "\n" : "	");
    }
    return textContent;
  }
};
function $isTableSelection(x) {
  return x instanceof TableSelection;
}
function $createTableSelection() {
  const anchor = $createPoint("root", 0, "element");
  const focus = $createPoint("root", 0, "element");
  return new TableSelection("root", anchor, focus);
}
function $getChildrenRecursively(node) {
  const nodes = [];
  const stack = [node];
  while (stack.length > 0) {
    const currentNode = stack.pop();
    invariant(
      currentNode !== void 0,
      "Stack.length > 0; can't be undefined"
    );
    if ($isElementNode(currentNode)) {
      stack.unshift(...currentNode.getChildren());
    }
    if (currentNode !== node) {
      nodes.push(currentNode);
    }
  }
  return nodes;
}

// resources/js/wysiwyg/lexical/table/LexicalTableObserver.ts
var TableObserver = class {
  constructor(editor, tableNodeKey) {
    __publicField(this, "focusX");
    __publicField(this, "focusY");
    __publicField(this, "listenersToRemove");
    __publicField(this, "table");
    __publicField(this, "isHighlightingCells");
    __publicField(this, "anchorX");
    __publicField(this, "anchorY");
    __publicField(this, "tableNodeKey");
    __publicField(this, "anchorCell");
    __publicField(this, "focusCell");
    __publicField(this, "anchorCellNodeKey");
    __publicField(this, "focusCellNodeKey");
    __publicField(this, "editor");
    __publicField(this, "tableSelection");
    __publicField(this, "hasHijackedSelectionStyles");
    __publicField(this, "isSelecting");
    this.isHighlightingCells = false;
    this.anchorX = -1;
    this.anchorY = -1;
    this.focusX = -1;
    this.focusY = -1;
    this.listenersToRemove = /* @__PURE__ */ new Set();
    this.tableNodeKey = tableNodeKey;
    this.editor = editor;
    this.table = {
      columns: 0,
      domRows: [],
      rows: 0
    };
    this.tableSelection = null;
    this.anchorCellNodeKey = null;
    this.focusCellNodeKey = null;
    this.anchorCell = null;
    this.focusCell = null;
    this.hasHijackedSelectionStyles = false;
    this.trackTable();
    this.isSelecting = false;
  }
  getTable() {
    return this.table;
  }
  removeListeners() {
    Array.from(this.listenersToRemove).forEach(
      (removeListener) => removeListener()
    );
  }
  trackTable() {
    const observer = new MutationObserver((records) => {
      this.editor.update(() => {
        let gridNeedsRedraw = false;
        for (let i = 0; i < records.length; i++) {
          const record = records[i];
          const target = record.target;
          const nodeName = target.nodeName;
          if (nodeName === "TABLE" || nodeName === "TBODY" || nodeName === "THEAD" || nodeName === "TR") {
            gridNeedsRedraw = true;
            break;
          }
        }
        if (!gridNeedsRedraw) {
          return;
        }
        const tableElement = this.editor.getElementByKey(this.tableNodeKey);
        if (!tableElement) {
          throw new Error("Expected to find TableElement in DOM");
        }
        this.table = getTable(tableElement);
      });
    });
    this.editor.update(() => {
      const tableElement = this.editor.getElementByKey(this.tableNodeKey);
      if (!tableElement) {
        throw new Error("Expected to find TableElement in DOM");
      }
      this.table = getTable(tableElement);
      observer.observe(tableElement, {
        attributes: true,
        childList: true,
        subtree: true
      });
    });
  }
  clearHighlight() {
    const editor = this.editor;
    this.isHighlightingCells = false;
    this.anchorX = -1;
    this.anchorY = -1;
    this.focusX = -1;
    this.focusY = -1;
    this.tableSelection = null;
    this.anchorCellNodeKey = null;
    this.focusCellNodeKey = null;
    this.anchorCell = null;
    this.focusCell = null;
    this.hasHijackedSelectionStyles = false;
    this.enableHighlightStyle();
    editor.update(() => {
      const tableNode = $getNodeByKey(this.tableNodeKey);
      if (!$isTableNode(tableNode)) {
        throw new Error("Expected TableNode.");
      }
      const tableElement = editor.getElementByKey(this.tableNodeKey);
      if (!tableElement) {
        throw new Error("Expected to find TableElement in DOM");
      }
      const grid = getTable(tableElement);
      $updateDOMForSelection(editor, grid, null);
      $setSelection(null);
      editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
    });
  }
  enableHighlightStyle() {
    const editor = this.editor;
    editor.update(() => {
      const tableElement = editor.getElementByKey(this.tableNodeKey);
      if (!tableElement) {
        throw new Error("Expected to find TableElement in DOM");
      }
      removeClassNamesFromElement(
        tableElement,
        editor._config.theme.tableSelection
      );
      tableElement.classList.remove("disable-selection");
      this.hasHijackedSelectionStyles = false;
    });
  }
  disableHighlightStyle() {
    const editor = this.editor;
    editor.update(() => {
      const tableElement = editor.getElementByKey(this.tableNodeKey);
      if (!tableElement) {
        throw new Error("Expected to find TableElement in DOM");
      }
      addClassNamesToElement(tableElement, editor._config.theme.tableSelection);
      this.hasHijackedSelectionStyles = true;
    });
  }
  updateTableTableSelection(selection) {
    if (selection !== null && selection.tableKey === this.tableNodeKey) {
      const editor = this.editor;
      this.tableSelection = selection;
      this.isHighlightingCells = true;
      this.disableHighlightStyle();
      $updateDOMForSelection(editor, this.table, this.tableSelection);
    } else if (selection == null) {
      this.clearHighlight();
    } else {
      this.tableNodeKey = selection.tableKey;
      this.updateTableTableSelection(selection);
    }
  }
  setFocusCellForSelection(cell, ignoreStart = false) {
    const editor = this.editor;
    editor.update(() => {
      const tableNode = $getNodeByKey(this.tableNodeKey);
      if (!$isTableNode(tableNode)) {
        throw new Error("Expected TableNode.");
      }
      const tableElement = editor.getElementByKey(this.tableNodeKey);
      if (!tableElement) {
        throw new Error("Expected to find TableElement in DOM");
      }
      const cellX = cell.x;
      const cellY = cell.y;
      this.focusCell = cell;
      if (this.anchorCell !== null) {
        const domSelection = getDOMSelection3(editor._window);
        if (domSelection) {
          domSelection.setBaseAndExtent(
            this.anchorCell.elem,
            0,
            this.focusCell.elem,
            0
          );
        }
      }
      if (!this.isHighlightingCells && (this.anchorX !== cellX || this.anchorY !== cellY || ignoreStart)) {
        this.isHighlightingCells = true;
        this.disableHighlightStyle();
      } else if (cellX === this.focusX && cellY === this.focusY) {
        return;
      }
      this.focusX = cellX;
      this.focusY = cellY;
      if (this.isHighlightingCells) {
        const focusTableCellNode = $getNearestNodeFromDOMNode(cell.elem);
        if (this.tableSelection != null && this.anchorCellNodeKey != null && $isTableCellNode(focusTableCellNode) && tableNode.is($findTableNode(focusTableCellNode))) {
          const focusNodeKey = focusTableCellNode.getKey();
          this.tableSelection = this.tableSelection.clone() || $createTableSelection();
          this.focusCellNodeKey = focusNodeKey;
          this.tableSelection.set(
            this.tableNodeKey,
            this.anchorCellNodeKey,
            this.focusCellNodeKey
          );
          $setSelection(this.tableSelection);
          editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
          $updateDOMForSelection(editor, this.table, this.tableSelection);
        }
      }
    });
  }
  setAnchorCellForSelection(cell) {
    this.isHighlightingCells = false;
    this.anchorCell = cell;
    this.anchorX = cell.x;
    this.anchorY = cell.y;
    this.editor.update(() => {
      const anchorTableCellNode = $getNearestNodeFromDOMNode(cell.elem);
      if ($isTableCellNode(anchorTableCellNode)) {
        const anchorNodeKey = anchorTableCellNode.getKey();
        this.tableSelection = this.tableSelection != null ? this.tableSelection.clone() : $createTableSelection();
        this.anchorCellNodeKey = anchorNodeKey;
      }
    });
  }
  formatCells(type) {
    this.editor.update(() => {
      const selection = $getSelection();
      if (!$isTableSelection(selection)) {
        invariant(false, "Expected grid selection");
      }
      const formatSelection = $createRangeSelection();
      const anchor = formatSelection.anchor;
      const focus = formatSelection.focus;
      selection.getNodes().forEach((cellNode) => {
        if ($isTableCellNode(cellNode) && cellNode.getTextContentSize() !== 0) {
          anchor.set(cellNode.getKey(), 0, "element");
          focus.set(cellNode.getKey(), cellNode.getChildrenSize(), "element");
          formatSelection.formatText(type);
        }
      });
      $setSelection(selection);
      this.editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
    });
  }
  clearText() {
    const editor = this.editor;
    editor.update(() => {
      const tableNode = $getNodeByKey(this.tableNodeKey);
      if (!$isTableNode(tableNode)) {
        throw new Error("Expected TableNode.");
      }
      const selection = $getSelection();
      if (!$isTableSelection(selection)) {
        invariant(false, "Expected grid selection");
      }
      const selectedNodes = selection.getNodes().filter($isTableCellNode);
      if (selectedNodes.length === this.table.columns * this.table.rows) {
        tableNode.selectPrevious();
        tableNode.remove();
        const rootNode = $getRoot();
        rootNode.selectStart();
        return;
      }
      selectedNodes.forEach((cellNode) => {
        if ($isElementNode(cellNode)) {
          const paragraphNode = $createParagraphNode();
          const textNode = $createTextNode();
          paragraphNode.append(textNode);
          cellNode.append(paragraphNode);
          cellNode.getChildren().forEach((child) => {
            if (child !== paragraphNode) {
              child.remove();
            }
          });
        }
      });
      $updateDOMForSelection(editor, this.table, null);
      $setSelection(null);
      editor.dispatchCommand(SELECTION_CHANGE_COMMAND, void 0);
    });
  }
};

// resources/js/wysiwyg/lexical/table/LexicalTableSelectionHelpers.ts
var LEXICAL_ELEMENT_KEY = "__lexicalTableSelection";
var getDOMSelection3 = (targetWindow) => CAN_USE_DOM ? (targetWindow || window).getSelection() : null;
var isMouseDownOnEvent = (event) => {
  return (event.buttons & 1) === 1;
};
function applyTableHandlers(tableNode, tableElement, editor, hasTabHandler) {
  const rootElement = editor.getRootElement();
  if (rootElement === null) {
    throw new Error("No root element.");
  }
  const tableObserver = new TableObserver(editor, tableNode.getKey());
  const editorWindow = editor._window || window;
  attachTableObserverToTableElement(tableElement, tableObserver);
  const createMouseHandlers = () => {
    const onMouseUp = () => {
      tableObserver.isSelecting = false;
      editorWindow.removeEventListener("mouseup", onMouseUp);
      editorWindow.removeEventListener("mousemove", onMouseMove);
    };
    const onMouseMove = (moveEvent) => {
      setTimeout(() => {
        if (!isMouseDownOnEvent(moveEvent) && tableObserver.isSelecting) {
          tableObserver.isSelecting = false;
          editorWindow.removeEventListener("mouseup", onMouseUp);
          editorWindow.removeEventListener("mousemove", onMouseMove);
          return;
        }
        const focusCell = getDOMCellFromTarget(moveEvent.target);
        if (focusCell !== null && (tableObserver.anchorX !== focusCell.x || tableObserver.anchorY !== focusCell.y)) {
          moveEvent.preventDefault();
          tableObserver.setFocusCellForSelection(focusCell);
        }
      }, 0);
    };
    return { onMouseMove, onMouseUp };
  };
  tableElement.addEventListener("mousedown", (event) => {
    setTimeout(() => {
      if (event.button !== 0) {
        return;
      }
      if (!editorWindow) {
        return;
      }
      const anchorCell = getDOMCellFromTarget(event.target);
      if (anchorCell !== null) {
        stopEvent(event);
        tableObserver.setAnchorCellForSelection(anchorCell);
      }
      const { onMouseUp, onMouseMove } = createMouseHandlers();
      tableObserver.isSelecting = true;
      editorWindow.addEventListener("mouseup", onMouseUp);
      editorWindow.addEventListener("mousemove", onMouseMove);
    }, 0);
  });
  const mouseDownCallback = (event) => {
    if (event.button !== 0) {
      return;
    }
    editor.update(() => {
      const selection = $getSelection();
      const target = event.target;
      if ($isTableSelection(selection) && selection.tableKey === tableObserver.tableNodeKey && rootElement.contains(target)) {
        tableObserver.clearHighlight();
      }
    });
  };
  editorWindow.addEventListener("mousedown", mouseDownCallback);
  tableObserver.listenersToRemove.add(
    () => editorWindow.removeEventListener("mousedown", mouseDownCallback)
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_ARROW_DOWN_COMMAND,
      (event) => $handleArrowKey(editor, event, "down", tableNode, tableObserver),
      COMMAND_PRIORITY_HIGH
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_ARROW_UP_COMMAND,
      (event) => $handleArrowKey(editor, event, "up", tableNode, tableObserver),
      COMMAND_PRIORITY_HIGH
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_ARROW_LEFT_COMMAND,
      (event) => $handleArrowKey(editor, event, "backward", tableNode, tableObserver),
      COMMAND_PRIORITY_HIGH
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_ARROW_RIGHT_COMMAND,
      (event) => $handleArrowKey(editor, event, "forward", tableNode, tableObserver),
      COMMAND_PRIORITY_HIGH
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_ESCAPE_COMMAND,
      (event) => {
        const selection = $getSelection();
        if ($isTableSelection(selection)) {
          const focusCellNode = $findMatchingParent(
            selection.focus.getNode(),
            $isTableCellNode
          );
          if ($isTableCellNode(focusCellNode)) {
            stopEvent(event);
            focusCellNode.selectEnd();
            return true;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_HIGH
    )
  );
  const deleteTextHandler = (command) => () => {
    const selection = $getSelection();
    if (!$isSelectionInTable(selection, tableNode)) {
      return false;
    }
    if ($isTableSelection(selection)) {
      tableObserver.clearText();
      return true;
    } else if ($isRangeSelection(selection)) {
      const tableCellNode = $findMatchingParent(
        selection.anchor.getNode(),
        (n) => $isTableCellNode(n)
      );
      if (!$isTableCellNode(tableCellNode)) {
        return false;
      }
      const anchorNode = selection.anchor.getNode();
      const focusNode = selection.focus.getNode();
      const isAnchorInside = tableNode.isParentOf(anchorNode);
      const isFocusInside = tableNode.isParentOf(focusNode);
      const selectionContainsPartialTable = isAnchorInside && !isFocusInside || isFocusInside && !isAnchorInside;
      if (selectionContainsPartialTable) {
        tableObserver.clearText();
        return true;
      }
      const nearestElementNode = $findMatchingParent(
        selection.anchor.getNode(),
        (n) => $isElementNode(n)
      );
      const topLevelCellElementNode = nearestElementNode && $findMatchingParent(
        nearestElementNode,
        (n) => $isElementNode(n) && $isTableCellNode(n.getParent())
      );
      if (!$isElementNode(topLevelCellElementNode) || !$isElementNode(nearestElementNode)) {
        return false;
      }
      if (command === DELETE_LINE_COMMAND && topLevelCellElementNode.getPreviousSibling() === null) {
        return true;
      }
    }
    return false;
  };
  [DELETE_WORD_COMMAND, DELETE_LINE_COMMAND, DELETE_CHARACTER_COMMAND].forEach(
    (command) => {
      tableObserver.listenersToRemove.add(
        editor.registerCommand(
          command,
          deleteTextHandler(command),
          COMMAND_PRIORITY_CRITICAL
        )
      );
    }
  );
  const $deleteCellHandler = (event) => {
    const selection = $getSelection();
    if (!$isSelectionInTable(selection, tableNode)) {
      const nodes = selection ? selection.getNodes() : null;
      if (nodes) {
        const table2 = nodes.find(
          (node) => $isTableNode(node) && node.getKey() === tableObserver.tableNodeKey
        );
        if ($isTableNode(table2)) {
          const parentNode = table2.getParent();
          if (!parentNode) {
            return false;
          }
          table2.remove();
        }
      }
      return false;
    }
    if ($isTableSelection(selection)) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      tableObserver.clearText();
      return true;
    } else if ($isRangeSelection(selection)) {
      const tableCellNode = $findMatchingParent(
        selection.anchor.getNode(),
        (n) => $isTableCellNode(n)
      );
      if (!$isTableCellNode(tableCellNode)) {
        return false;
      }
    }
    return false;
  };
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_BACKSPACE_COMMAND,
      $deleteCellHandler,
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      KEY_DELETE_COMMAND,
      $deleteCellHandler,
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      CUT_COMMAND,
      (event) => {
        const selection = $getSelection();
        if (selection) {
          if (!($isTableSelection(selection) || $isRangeSelection(selection))) {
            return false;
          }
          void copyToClipboard(
            editor,
            objectKlassEquals(event, ClipboardEvent) ? event : null,
            $getClipboardDataFromSelection(selection)
          );
          const intercepted = $deleteCellHandler(event);
          if ($isRangeSelection(selection)) {
            selection.removeText();
          }
          return intercepted;
        }
        return false;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      FORMAT_TEXT_COMMAND,
      (payload) => {
        const selection = $getSelection();
        if (!$isSelectionInTable(selection, tableNode)) {
          return false;
        }
        if ($isTableSelection(selection)) {
          tableObserver.formatCells(payload);
          return true;
        } else if ($isRangeSelection(selection)) {
          const tableCellNode = $findMatchingParent(
            selection.anchor.getNode(),
            (n) => $isTableCellNode(n)
          );
          if (!$isTableCellNode(tableCellNode)) {
            return false;
          }
        }
        return false;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      CONTROLLED_TEXT_INSERTION_COMMAND,
      (payload) => {
        const selection = $getSelection();
        if (!$isSelectionInTable(selection, tableNode)) {
          return false;
        }
        if ($isTableSelection(selection)) {
          tableObserver.clearHighlight();
          return false;
        } else if ($isRangeSelection(selection)) {
          const tableCellNode = $findMatchingParent(
            selection.anchor.getNode(),
            (n) => $isTableCellNode(n)
          );
          if (!$isTableCellNode(tableCellNode)) {
            return false;
          }
          if (typeof payload === "string") {
            const edgePosition = $getTableEdgeCursorPosition(
              editor,
              selection,
              tableNode
            );
            if (edgePosition) {
              $insertParagraphAtTableEdge(edgePosition, tableNode, [
                $createTextNode(payload)
              ]);
              return true;
            }
          }
        }
        return false;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  if (hasTabHandler) {
    tableObserver.listenersToRemove.add(
      editor.registerCommand(
        KEY_TAB_COMMAND,
        (event) => {
          const selection = $getSelection();
          if (!$isRangeSelection(selection) || !selection.isCollapsed() || !$isSelectionInTable(selection, tableNode)) {
            return false;
          }
          const tableCellNode = $findCellNode(selection.anchor.getNode());
          if (tableCellNode === null) {
            return false;
          }
          stopEvent(event);
          const currentCords = tableNode.getCordsFromCellNode(
            tableCellNode,
            tableObserver.table
          );
          selectTableNodeInDirection(
            tableObserver,
            tableNode,
            currentCords.x,
            currentCords.y,
            !event.shiftKey ? "forward" : "backward"
          );
          return true;
        },
        COMMAND_PRIORITY_CRITICAL
      )
    );
  }
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      FOCUS_COMMAND,
      (payload) => {
        return tableNode.isSelected();
      },
      COMMAND_PRIORITY_HIGH
    )
  );
  function getObserverCellFromCellNode(tableCellNode) {
    const currentCords = tableNode.getCordsFromCellNode(
      tableCellNode,
      tableObserver.table
    );
    return tableNode.getDOMCellFromCordsOrThrow(
      currentCords.x,
      currentCords.y,
      tableObserver.table
    );
  }
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      SELECTION_INSERT_CLIPBOARD_NODES_COMMAND,
      (selectionPayload) => {
        const { nodes, selection } = selectionPayload;
        const anchorAndFocus = selection.getStartEndPoints();
        const isTableSelection = $isTableSelection(selection);
        const isRangeSelection = $isRangeSelection(selection);
        const isSelectionInsideOfGrid = isRangeSelection && $findMatchingParent(
          selection.anchor.getNode(),
          (n) => $isTableCellNode(n)
        ) !== null && $findMatchingParent(
          selection.focus.getNode(),
          (n) => $isTableCellNode(n)
        ) !== null || isTableSelection;
        if (nodes.length !== 1 || !$isTableNode(nodes[0]) || !isSelectionInsideOfGrid || anchorAndFocus === null) {
          return false;
        }
        const [anchor] = anchorAndFocus;
        const newGrid = nodes[0];
        const newGridRows = newGrid.getChildren();
        const newColumnCount = newGrid.getFirstChildOrThrow().getChildrenSize();
        const newRowCount = newGrid.getChildrenSize();
        const gridCellNode = $findMatchingParent(
          anchor.getNode(),
          (n) => $isTableCellNode(n)
        );
        const gridRowNode = gridCellNode && $findMatchingParent(gridCellNode, (n) => $isTableRowNode(n));
        const gridNode = gridRowNode && $findMatchingParent(gridRowNode, (n) => $isTableNode(n));
        if (!$isTableCellNode(gridCellNode) || !$isTableRowNode(gridRowNode) || !$isTableNode(gridNode)) {
          return false;
        }
        const startY = gridRowNode.getIndexWithinParent();
        const stopY = Math.min(
          gridNode.getChildrenSize() - 1,
          startY + newRowCount - 1
        );
        const startX = gridCellNode.getIndexWithinParent();
        const stopX = Math.min(
          gridRowNode.getChildrenSize() - 1,
          startX + newColumnCount - 1
        );
        const fromX = Math.min(startX, stopX);
        const fromY = Math.min(startY, stopY);
        const toX = Math.max(startX, stopX);
        const toY = Math.max(startY, stopY);
        const gridRowNodes = gridNode.getChildren();
        let newRowIdx = 0;
        for (let r = fromY; r <= toY; r++) {
          const currentGridRowNode = gridRowNodes[r];
          if (!$isTableRowNode(currentGridRowNode)) {
            return false;
          }
          const newGridRowNode = newGridRows[newRowIdx];
          if (!$isTableRowNode(newGridRowNode)) {
            return false;
          }
          const gridCellNodes = currentGridRowNode.getChildren();
          const newGridCellNodes = newGridRowNode.getChildren();
          let newColumnIdx = 0;
          for (let c = fromX; c <= toX; c++) {
            const currentGridCellNode = gridCellNodes[c];
            if (!$isTableCellNode(currentGridCellNode)) {
              return false;
            }
            const newGridCellNode = newGridCellNodes[newColumnIdx];
            if (!$isTableCellNode(newGridCellNode)) {
              return false;
            }
            const originalChildren = currentGridCellNode.getChildren();
            newGridCellNode.getChildren().forEach((child) => {
              if ($isTextNode(child)) {
                const paragraphNode = $createParagraphNode();
                paragraphNode.append(child);
                currentGridCellNode.append(child);
              } else {
                currentGridCellNode.append(child);
              }
            });
            originalChildren.forEach((n) => n.remove());
            newColumnIdx++;
          }
          newRowIdx++;
        }
        return true;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      SELECTION_CHANGE_COMMAND,
      () => {
        const selection = $getSelection();
        const prevSelection = $getPreviousSelection();
        if ($isRangeSelection(selection)) {
          const { anchor, focus } = selection;
          const anchorNode = anchor.getNode();
          const focusNode = focus.getNode();
          const anchorCellNode = $findCellNode(anchorNode);
          const focusCellNode = $findCellNode(focusNode);
          const isAnchorInside = !!(anchorCellNode && tableNode.is($findTableNode(anchorCellNode)));
          const isFocusInside = !!(focusCellNode && tableNode.is($findTableNode(focusCellNode)));
          const isPartialyWithinTable = isAnchorInside !== isFocusInside;
          const isWithinTable = isAnchorInside && isFocusInside;
          const isBackward = selection.isBackward();
          if (isPartialyWithinTable) {
            const newSelection = selection.clone();
            if (isFocusInside) {
              const [tableMap] = $computeTableMap(
                tableNode,
                focusCellNode,
                focusCellNode
              );
              const firstCell = tableMap[0][0].cell;
              const lastCell = tableMap[tableMap.length - 1].at(-1).cell;
              newSelection.focus.set(
                isBackward ? firstCell.getKey() : lastCell.getKey(),
                isBackward ? firstCell.getChildrenSize() : lastCell.getChildrenSize(),
                "element"
              );
            }
            $setSelection(newSelection);
            $addHighlightStyleToTable(editor, tableObserver);
          } else if (isWithinTable) {
            if (!anchorCellNode.is(focusCellNode)) {
              tableObserver.setAnchorCellForSelection(
                getObserverCellFromCellNode(anchorCellNode)
              );
              tableObserver.setFocusCellForSelection(
                getObserverCellFromCellNode(focusCellNode),
                true
              );
              if (!tableObserver.isSelecting) {
                setTimeout(() => {
                  const { onMouseUp, onMouseMove } = createMouseHandlers();
                  tableObserver.isSelecting = true;
                  editorWindow.addEventListener("mouseup", onMouseUp);
                  editorWindow.addEventListener("mousemove", onMouseMove);
                }, 0);
              }
            }
          }
        } else if (selection && $isTableSelection(selection) && selection.is(prevSelection) && selection.tableKey === tableNode.getKey()) {
          const domSelection = getDOMSelection3(editor._window);
          if (domSelection && domSelection.anchorNode && domSelection.focusNode) {
            const focusNode = $getNearestNodeFromDOMNode(
              domSelection.focusNode
            );
            const isFocusOutside = focusNode && !tableNode.is($findTableNode(focusNode));
            const anchorNode = $getNearestNodeFromDOMNode(
              domSelection.anchorNode
            );
            const isAnchorInside = anchorNode && tableNode.is($findTableNode(anchorNode));
            if (isFocusOutside && isAnchorInside && domSelection.rangeCount > 0) {
              const newSelection = $createRangeSelectionFromDom(
                domSelection,
                editor
              );
              if (newSelection) {
                newSelection.anchor.set(
                  tableNode.getKey(),
                  selection.isBackward() ? tableNode.getChildrenSize() : 0,
                  "element"
                );
                domSelection.removeAllRanges();
                $setSelection(newSelection);
              }
            }
          }
        }
        if (selection && !selection.is(prevSelection) && ($isTableSelection(selection) || $isTableSelection(prevSelection)) && tableObserver.tableSelection && !tableObserver.tableSelection.is(prevSelection)) {
          if ($isTableSelection(selection) && selection.tableKey === tableObserver.tableNodeKey) {
            tableObserver.updateTableTableSelection(selection);
          } else if (!$isTableSelection(selection) && $isTableSelection(prevSelection) && prevSelection.tableKey === tableObserver.tableNodeKey) {
            tableObserver.updateTableTableSelection(null);
          }
          return false;
        }
        if (tableObserver.hasHijackedSelectionStyles && !tableNode.isSelected()) {
          $removeHighlightStyleToTable(editor, tableObserver);
        } else if (!tableObserver.hasHijackedSelectionStyles && tableNode.isSelected()) {
          $addHighlightStyleToTable(editor, tableObserver);
        }
        return false;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  tableObserver.listenersToRemove.add(
    editor.registerCommand(
      INSERT_PARAGRAPH_COMMAND,
      () => {
        const selection = $getSelection();
        if (!$isRangeSelection(selection) || !selection.isCollapsed() || !$isSelectionInTable(selection, tableNode)) {
          return false;
        }
        const edgePosition = $getTableEdgeCursorPosition(
          editor,
          selection,
          tableNode
        );
        if (edgePosition) {
          $insertParagraphAtTableEdge(edgePosition, tableNode);
          return true;
        }
        return false;
      },
      COMMAND_PRIORITY_CRITICAL
    )
  );
  return tableObserver;
}
function attachTableObserverToTableElement(tableElement, tableObserver) {
  tableElement[LEXICAL_ELEMENT_KEY] = tableObserver;
}
function getDOMCellFromTarget(node) {
  let currentNode = node;
  while (currentNode != null) {
    const nodeName = currentNode.nodeName;
    if (nodeName === "TD" || nodeName === "TH") {
      const cell = currentNode._cell;
      if (cell === void 0) {
        return null;
      }
      return cell;
    }
    currentNode = currentNode.parentNode;
  }
  return null;
}
function getTable(tableElement) {
  const domRows = [];
  const grid = {
    columns: 0,
    domRows,
    rows: 0
  };
  let currentNode = tableElement.firstChild;
  let x = 0;
  let y = 0;
  domRows.length = 0;
  while (currentNode != null) {
    const nodeName = currentNode.nodeName;
    if (nodeName === "COLGROUP" || nodeName === "CAPTION") {
      currentNode = currentNode.nextSibling;
      continue;
    }
    if (nodeName === "TD" || nodeName === "TH") {
      const elem = currentNode;
      const cell = {
        elem,
        hasBackgroundColor: elem.style.backgroundColor !== "",
        highlighted: false,
        x,
        y
      };
      currentNode._cell = cell;
      let row = domRows[y];
      if (row === void 0) {
        row = domRows[y] = [];
      }
      row[x] = cell;
    } else {
      const child = currentNode.firstChild;
      if (child != null) {
        currentNode = child;
        continue;
      }
    }
    const sibling = currentNode.nextSibling;
    if (sibling != null) {
      x++;
      currentNode = sibling;
      continue;
    }
    const parent = currentNode.parentNode;
    if (parent != null) {
      const parentSibling = parent.nextSibling;
      if (parentSibling == null) {
        break;
      }
      y++;
      x = 0;
      currentNode = parentSibling;
    }
  }
  grid.columns = x + 1;
  grid.rows = y + 1;
  return grid;
}
function $updateDOMForSelection(editor, table2, selection) {
  const selectedCellNodes = new Set(selection ? selection.getNodes() : []);
  $forEachTableCell(table2, (cell, lexicalNode) => {
    const elem = cell.elem;
    if (selectedCellNodes.has(lexicalNode)) {
      cell.highlighted = true;
      $addHighlightToDOM(editor, cell);
    } else {
      cell.highlighted = false;
      $removeHighlightFromDOM(editor, cell);
      if (!elem.getAttribute("style")) {
        elem.removeAttribute("style");
      }
    }
  });
}
function $forEachTableCell(grid, cb) {
  const { domRows } = grid;
  for (let y = 0; y < domRows.length; y++) {
    const row = domRows[y];
    if (!row) {
      continue;
    }
    for (let x = 0; x < row.length; x++) {
      const cell = row[x];
      if (!cell) {
        continue;
      }
      const lexicalNode = $getNearestNodeFromDOMNode(cell.elem);
      if (lexicalNode !== null) {
        cb(cell, lexicalNode, {
          x,
          y
        });
      }
    }
  }
}
function $addHighlightStyleToTable(editor, tableSelection) {
  tableSelection.disableHighlightStyle();
  $forEachTableCell(tableSelection.table, (cell) => {
    cell.highlighted = true;
    $addHighlightToDOM(editor, cell);
  });
}
function $removeHighlightStyleToTable(editor, tableObserver) {
  tableObserver.enableHighlightStyle();
  $forEachTableCell(tableObserver.table, (cell) => {
    const elem = cell.elem;
    cell.highlighted = false;
    $removeHighlightFromDOM(editor, cell);
    if (!elem.getAttribute("style")) {
      elem.removeAttribute("style");
    }
  });
}
var selectTableNodeInDirection = (tableObserver, tableNode, x, y, direction) => {
  const isForward = direction === "forward";
  switch (direction) {
    case "backward":
    case "forward":
      if (x !== (isForward ? tableObserver.table.columns - 1 : 0)) {
        selectTableCellNode(
          tableNode.getCellNodeFromCordsOrThrow(
            x + (isForward ? 1 : -1),
            y,
            tableObserver.table
          ),
          isForward
        );
      } else {
        if (y !== (isForward ? tableObserver.table.rows - 1 : 0)) {
          selectTableCellNode(
            tableNode.getCellNodeFromCordsOrThrow(
              isForward ? 0 : tableObserver.table.columns - 1,
              y + (isForward ? 1 : -1),
              tableObserver.table
            ),
            isForward
          );
        } else if (!isForward) {
          tableNode.selectPrevious();
        } else {
          tableNode.selectNext();
        }
      }
      return true;
    case "up":
      if (y !== 0) {
        selectTableCellNode(
          tableNode.getCellNodeFromCordsOrThrow(x, y - 1, tableObserver.table),
          false
        );
      } else {
        $selectOrCreateAdjacent(tableNode, false);
      }
      return true;
    case "down":
      if (y !== tableObserver.table.rows - 1) {
        selectTableCellNode(
          tableNode.getCellNodeFromCordsOrThrow(x, y + 1, tableObserver.table),
          true
        );
      } else {
        $selectOrCreateAdjacent(tableNode, true);
      }
      return true;
    default:
      return false;
  }
};
var adjustFocusNodeInDirection = (tableObserver, tableNode, x, y, direction) => {
  const isForward = direction === "forward";
  switch (direction) {
    case "backward":
    case "forward":
      if (x !== (isForward ? tableObserver.table.columns - 1 : 0)) {
        tableObserver.setFocusCellForSelection(
          tableNode.getDOMCellFromCordsOrThrow(
            x + (isForward ? 1 : -1),
            y,
            tableObserver.table
          )
        );
      }
      return true;
    case "up":
      if (y !== 0) {
        tableObserver.setFocusCellForSelection(
          tableNode.getDOMCellFromCordsOrThrow(x, y - 1, tableObserver.table)
        );
        return true;
      } else {
        return false;
      }
    case "down":
      if (y !== tableObserver.table.rows - 1) {
        tableObserver.setFocusCellForSelection(
          tableNode.getDOMCellFromCordsOrThrow(x, y + 1, tableObserver.table)
        );
        return true;
      } else {
        return false;
      }
    default:
      return false;
  }
};
function $isSelectionInTable(selection, tableNode) {
  if ($isRangeSelection(selection) || $isTableSelection(selection)) {
    const isAnchorInside = tableNode.isParentOf(selection.anchor.getNode());
    const isFocusInside = tableNode.isParentOf(selection.focus.getNode());
    return isAnchorInside && isFocusInside;
  }
  return false;
}
function selectTableCellNode(tableCell, fromStart) {
  if (fromStart) {
    tableCell.selectStart();
  } else {
    tableCell.selectEnd();
  }
}
var BROWSER_BLUE_RGB = "172,206,247";
function $addHighlightToDOM(editor, cell) {
  const element = cell.elem;
  const node = $getNearestNodeFromDOMNode(element);
  invariant(
    $isTableCellNode(node),
    "Expected to find LexicalNode from Table Cell DOMNode"
  );
  const backgroundColor = node.getBackgroundColor();
  if (backgroundColor === null) {
    element.style.setProperty("background-color", `rgb(${BROWSER_BLUE_RGB})`);
  } else {
    element.style.setProperty(
      "background-image",
      `linear-gradient(to right, rgba(${BROWSER_BLUE_RGB},0.85), rgba(${BROWSER_BLUE_RGB},0.85))`
    );
  }
  element.style.setProperty("caret-color", "transparent");
}
function $removeHighlightFromDOM(editor, cell) {
  const element = cell.elem;
  const node = $getNearestNodeFromDOMNode(element);
  invariant(
    $isTableCellNode(node),
    "Expected to find LexicalNode from Table Cell DOMNode"
  );
  const backgroundColor = node.getBackgroundColor();
  if (backgroundColor === null) {
    element.style.removeProperty("background-color");
  }
  element.style.removeProperty("background-image");
  element.style.removeProperty("caret-color");
}
function $findCellNode(node) {
  const cellNode = $findMatchingParent(node, $isTableCellNode);
  return $isTableCellNode(cellNode) ? cellNode : null;
}
function $findTableNode(node) {
  const tableNode = $findMatchingParent(node, $isTableNode);
  return $isTableNode(tableNode) ? tableNode : null;
}
function $handleArrowKey(editor, event, direction, tableNode, tableObserver) {
  if ((direction === "up" || direction === "down") && isTypeaheadMenuInView(editor)) {
    return false;
  }
  const selection = $getSelection();
  if (!$isSelectionInTable(selection, tableNode)) {
    if ($isRangeSelection(selection)) {
      if (selection.isCollapsed() && direction === "backward") {
        const anchorType = selection.anchor.type;
        const anchorOffset = selection.anchor.offset;
        if (anchorType !== "element" && !(anchorType === "text" && anchorOffset === 0)) {
          return false;
        }
        const anchorNode = selection.anchor.getNode();
        if (!anchorNode) {
          return false;
        }
        const parentNode = $findMatchingParent(
          anchorNode,
          (n) => $isElementNode(n) && !n.isInline()
        );
        if (!parentNode) {
          return false;
        }
        const siblingNode = parentNode.getPreviousSibling();
        if (!siblingNode || !$isTableNode(siblingNode)) {
          return false;
        }
        stopEvent(event);
        siblingNode.selectEnd();
        return true;
      } else if (event.shiftKey && (direction === "up" || direction === "down")) {
        const focusNode = selection.focus.getNode();
        if ($isRootOrShadowRoot(focusNode)) {
          const selectedNode = selection.getNodes()[0];
          if (selectedNode) {
            const tableCellNode = $findMatchingParent(
              selectedNode,
              $isTableCellNode
            );
            if (tableCellNode && tableNode.isParentOf(tableCellNode)) {
              const firstDescendant = tableNode.getFirstDescendant();
              const lastDescendant = tableNode.getLastDescendant();
              if (!firstDescendant || !lastDescendant) {
                return false;
              }
              const [firstCellNode] = $getNodeTriplet(firstDescendant);
              const [lastCellNode] = $getNodeTriplet(lastDescendant);
              const firstCellCoords = tableNode.getCordsFromCellNode(
                firstCellNode,
                tableObserver.table
              );
              const lastCellCoords = tableNode.getCordsFromCellNode(
                lastCellNode,
                tableObserver.table
              );
              const firstCellDOM = tableNode.getDOMCellFromCordsOrThrow(
                firstCellCoords.x,
                firstCellCoords.y,
                tableObserver.table
              );
              const lastCellDOM = tableNode.getDOMCellFromCordsOrThrow(
                lastCellCoords.x,
                lastCellCoords.y,
                tableObserver.table
              );
              tableObserver.setAnchorCellForSelection(firstCellDOM);
              tableObserver.setFocusCellForSelection(lastCellDOM, true);
              return true;
            }
          }
          return false;
        } else {
          const focusParentNode = $findMatchingParent(
            focusNode,
            (n) => $isElementNode(n) && !n.isInline()
          );
          if (!focusParentNode) {
            return false;
          }
          const sibling = direction === "down" ? focusParentNode.getNextSibling() : focusParentNode.getPreviousSibling();
          if ($isTableNode(sibling) && tableObserver.tableNodeKey === sibling.getKey()) {
            const firstDescendant = sibling.getFirstDescendant();
            const lastDescendant = sibling.getLastDescendant();
            if (!firstDescendant || !lastDescendant) {
              return false;
            }
            const [firstCellNode] = $getNodeTriplet(firstDescendant);
            const [lastCellNode] = $getNodeTriplet(lastDescendant);
            const newSelection = selection.clone();
            newSelection.focus.set(
              (direction === "up" ? firstCellNode : lastCellNode).getKey(),
              direction === "up" ? 0 : lastCellNode.getChildrenSize(),
              "element"
            );
            $setSelection(newSelection);
            return true;
          }
        }
      }
    }
    return false;
  }
  if ($isRangeSelection(selection) && selection.isCollapsed()) {
    const { anchor, focus } = selection;
    const anchorCellNode = $findMatchingParent(
      anchor.getNode(),
      $isTableCellNode
    );
    const focusCellNode = $findMatchingParent(
      focus.getNode(),
      $isTableCellNode
    );
    if (!$isTableCellNode(anchorCellNode) || !anchorCellNode.is(focusCellNode)) {
      return false;
    }
    const anchorCellTable = $findTableNode(anchorCellNode);
    if (anchorCellTable !== tableNode && anchorCellTable != null) {
      const anchorCellTableElement = editor.getElementByKey(
        anchorCellTable.getKey()
      );
      if (anchorCellTableElement != null) {
        tableObserver.table = getTable(anchorCellTableElement);
        return $handleArrowKey(
          editor,
          event,
          direction,
          anchorCellTable,
          tableObserver
        );
      }
    }
    if (direction === "backward" || direction === "forward") {
      const anchorType = anchor.type;
      const anchorOffset = anchor.offset;
      const anchorNode = anchor.getNode();
      if (!anchorNode) {
        return false;
      }
      const selectedNodes = selection.getNodes();
      if (selectedNodes.length === 1 && $isDecoratorNode(selectedNodes[0])) {
        return false;
      }
      if (isExitingTableAnchor(anchorType, anchorOffset, anchorNode, direction)) {
        return $handleTableExit(event, anchorNode, tableNode, direction);
      }
      return false;
    }
    const anchorCellDom = editor.getElementByKey(anchorCellNode.__key);
    const anchorDOM = editor.getElementByKey(anchor.key);
    if (anchorDOM == null || anchorCellDom == null) {
      return false;
    }
    let edgeSelectionRect;
    if (anchor.type === "element") {
      edgeSelectionRect = anchorDOM.getBoundingClientRect();
    } else {
      const domSelection = window.getSelection();
      if (domSelection === null || domSelection.rangeCount === 0) {
        return false;
      }
      const range = domSelection.getRangeAt(0);
      edgeSelectionRect = range.getBoundingClientRect();
    }
    const edgeChild = direction === "up" ? anchorCellNode.getFirstChild() : anchorCellNode.getLastChild();
    if (edgeChild == null) {
      return false;
    }
    const edgeChildDOM = editor.getElementByKey(edgeChild.__key);
    if (edgeChildDOM == null) {
      return false;
    }
    const edgeRect = edgeChildDOM.getBoundingClientRect();
    const isExiting = direction === "up" ? edgeRect.top > edgeSelectionRect.top - edgeSelectionRect.height : edgeSelectionRect.bottom + edgeSelectionRect.height > edgeRect.bottom;
    if (isExiting) {
      stopEvent(event);
      const cords = tableNode.getCordsFromCellNode(
        anchorCellNode,
        tableObserver.table
      );
      if (event.shiftKey) {
        const cell = tableNode.getDOMCellFromCordsOrThrow(
          cords.x,
          cords.y,
          tableObserver.table
        );
        tableObserver.setAnchorCellForSelection(cell);
        tableObserver.setFocusCellForSelection(cell, true);
      } else {
        return selectTableNodeInDirection(
          tableObserver,
          tableNode,
          cords.x,
          cords.y,
          direction
        );
      }
      return true;
    }
  } else if ($isTableSelection(selection)) {
    const { anchor, focus } = selection;
    const anchorCellNode = $findMatchingParent(
      anchor.getNode(),
      $isTableCellNode
    );
    const focusCellNode = $findMatchingParent(
      focus.getNode(),
      $isTableCellNode
    );
    const [tableNodeFromSelection] = selection.getNodes();
    const tableElement = editor.getElementByKey(
      tableNodeFromSelection.getKey()
    );
    if (!$isTableCellNode(anchorCellNode) || !$isTableCellNode(focusCellNode) || !$isTableNode(tableNodeFromSelection) || tableElement == null) {
      return false;
    }
    tableObserver.updateTableTableSelection(selection);
    const grid = getTable(tableElement);
    const cordsAnchor = tableNode.getCordsFromCellNode(anchorCellNode, grid);
    const anchorCell = tableNode.getDOMCellFromCordsOrThrow(
      cordsAnchor.x,
      cordsAnchor.y,
      grid
    );
    tableObserver.setAnchorCellForSelection(anchorCell);
    stopEvent(event);
    if (event.shiftKey) {
      const cords = tableNode.getCordsFromCellNode(focusCellNode, grid);
      return adjustFocusNodeInDirection(
        tableObserver,
        tableNodeFromSelection,
        cords.x,
        cords.y,
        direction
      );
    } else {
      focusCellNode.selectEnd();
    }
    return true;
  }
  return false;
}
function stopEvent(event) {
  event.preventDefault();
  event.stopImmediatePropagation();
  event.stopPropagation();
}
function isTypeaheadMenuInView(editor) {
  const root = editor.getRootElement();
  if (!root) {
    return false;
  }
  return root.hasAttribute("aria-controls") && root.getAttribute("aria-controls") === "typeahead-menu";
}
function isExitingTableAnchor(type, offset, anchorNode, direction) {
  return isExitingTableElementAnchor(type, anchorNode, direction) || $isExitingTableTextAnchor(type, offset, anchorNode, direction);
}
function isExitingTableElementAnchor(type, anchorNode, direction) {
  return type === "element" && (direction === "backward" ? anchorNode.getPreviousSibling() === null : anchorNode.getNextSibling() === null);
}
function $isExitingTableTextAnchor(type, offset, anchorNode, direction) {
  const parentNode = $findMatchingParent(
    anchorNode,
    (n) => $isElementNode(n) && !n.isInline()
  );
  if (!parentNode) {
    return false;
  }
  const hasValidOffset = direction === "backward" ? offset === 0 : offset === anchorNode.getTextContentSize();
  return type === "text" && hasValidOffset && (direction === "backward" ? parentNode.getPreviousSibling() === null : parentNode.getNextSibling() === null);
}
function $handleTableExit(event, anchorNode, tableNode, direction) {
  const anchorCellNode = $findMatchingParent(anchorNode, $isTableCellNode);
  if (!$isTableCellNode(anchorCellNode)) {
    return false;
  }
  const [tableMap, cellValue] = $computeTableMap(
    tableNode,
    anchorCellNode,
    anchorCellNode
  );
  if (!isExitingCell(tableMap, cellValue, direction)) {
    return false;
  }
  const toNode = $getExitingToNode(anchorNode, direction, tableNode);
  if (!toNode || $isTableNode(toNode)) {
    return false;
  }
  stopEvent(event);
  if (direction === "backward") {
    toNode.selectEnd();
  } else {
    toNode.selectStart();
  }
  return true;
}
function isExitingCell(tableMap, cellValue, direction) {
  const firstCell = tableMap[0][0];
  const lastCell = tableMap[tableMap.length - 1][tableMap[0].length - 1];
  const { startColumn, startRow } = cellValue;
  return direction === "backward" ? startColumn === firstCell.startColumn && startRow === firstCell.startRow : startColumn === lastCell.startColumn && startRow === lastCell.startRow;
}
function $getExitingToNode(anchorNode, direction, tableNode) {
  const parentNode = $findMatchingParent(
    anchorNode,
    (n) => $isElementNode(n) && !n.isInline()
  );
  if (!parentNode) {
    return void 0;
  }
  const anchorSibling = direction === "backward" ? parentNode.getPreviousSibling() : parentNode.getNextSibling();
  return anchorSibling && $isTableNode(anchorSibling) ? anchorSibling : direction === "backward" ? tableNode.getPreviousSibling() : tableNode.getNextSibling();
}
function $insertParagraphAtTableEdge(edgePosition, tableNode, children) {
  const paragraphNode = $createParagraphNode();
  if (edgePosition === "first") {
    tableNode.insertBefore(paragraphNode);
  } else {
    tableNode.insertAfter(paragraphNode);
  }
  paragraphNode.append(...children || []);
  paragraphNode.selectEnd();
}
function $getTableEdgeCursorPosition(editor, selection, tableNode) {
  const tableNodeParent = tableNode.getParent();
  if (!tableNodeParent) {
    return void 0;
  }
  const tableNodeParentDOM = editor.getElementByKey(tableNodeParent.getKey());
  if (!tableNodeParentDOM) {
    return void 0;
  }
  const domSelection = window.getSelection();
  if (!domSelection || domSelection.anchorNode !== tableNodeParentDOM) {
    return void 0;
  }
  const anchorCellNode = $findMatchingParent(
    selection.anchor.getNode(),
    (n) => $isTableCellNode(n)
  );
  if (!anchorCellNode) {
    return void 0;
  }
  const parentTable = $findMatchingParent(
    anchorCellNode,
    (n) => $isTableNode(n)
  );
  if (!$isTableNode(parentTable) || !parentTable.is(tableNode)) {
    return void 0;
  }
  const [tableMap, cellValue] = $computeTableMap(
    tableNode,
    anchorCellNode,
    anchorCellNode
  );
  const firstCell = tableMap[0][0];
  const lastCell = tableMap[tableMap.length - 1][tableMap[0].length - 1];
  const { startRow, startColumn } = cellValue;
  const isAtFirstCell = startRow === firstCell.startRow && startColumn === firstCell.startColumn;
  const isAtLastCell = startRow === lastCell.startRow && startColumn === lastCell.startColumn;
  if (isAtFirstCell) {
    return "first";
  } else if (isAtLastCell) {
    return "last";
  } else {
    return void 0;
  }
}

// resources/js/wysiwyg/utils/table-map.ts
var TableMap = class {
  constructor(table2) {
    __publicField(this, "rowCount", 0);
    __publicField(this, "columnCount", 0);
    // Represents an array (rows*columns in length) of cell nodes from top-left to
    // bottom right. Cells may repeat where merged and covering multiple spaces.
    __publicField(this, "cells", []);
    this.buildCellMap(table2);
  }
  buildCellMap(table2) {
    const rowsAndCells = [];
    const setCell = (x, y, cell) => {
      if (typeof rowsAndCells[y] === "undefined") {
        rowsAndCells[y] = [];
      }
      rowsAndCells[y][x] = cell;
    };
    const cellFilled = (x, y) => !!(rowsAndCells[y] && rowsAndCells[y][x]);
    const rowNodes = table2.getChildren().filter((r) => $isTableRowNode(r));
    for (let rowIndex = 0; rowIndex < rowNodes.length; rowIndex++) {
      const rowNode = rowNodes[rowIndex];
      const cellNodes = rowNode.getChildren().filter((c) => $isTableCellNode(c));
      let targetColIndex = 0;
      for (let cellIndex = 0; cellIndex < cellNodes.length; cellIndex++) {
        const cellNode = cellNodes[cellIndex];
        const colspan = cellNode.getColSpan() || 1;
        const rowSpan = cellNode.getRowSpan() || 1;
        for (let x = targetColIndex; x < targetColIndex + colspan; x++) {
          for (let y = rowIndex; y < rowIndex + rowSpan; y++) {
            while (cellFilled(x, y)) {
              targetColIndex += 1;
              x += 1;
            }
            setCell(x, y, cellNode);
          }
        }
        targetColIndex += colspan;
      }
    }
    this.rowCount = rowsAndCells.length;
    this.columnCount = Math.max(...rowsAndCells.map((r) => r.length));
    const cells = [];
    let lastCell = rowsAndCells[0][0];
    for (let y = 0; y < this.rowCount; y++) {
      for (let x = 0; x < this.columnCount; x++) {
        if (!rowsAndCells[y] || !rowsAndCells[y][x]) {
          cells.push(lastCell);
        } else {
          cells.push(rowsAndCells[y][x]);
          lastCell = rowsAndCells[y][x];
        }
      }
    }
    this.cells = cells;
  }
  getCellAtPosition(x, y) {
    const position = y * this.columnCount + x;
    if (position >= this.cells.length) {
      throw new Error(`TableMap Error: Attempted to get cell ${position + 1} of ${this.cells.length}`);
    }
    return this.cells[position];
  }
  getCellsInRange(range) {
    const minX = Math.max(Math.min(range.fromX, range.toX), 0);
    const maxX = Math.min(Math.max(range.fromX, range.toX), this.columnCount - 1);
    const minY = Math.max(Math.min(range.fromY, range.toY), 0);
    const maxY = Math.min(Math.max(range.fromY, range.toY), this.rowCount - 1);
    const cells = /* @__PURE__ */ new Set();
    for (let y = minY; y <= maxY; y++) {
      for (let x = minX; x <= maxX; x++) {
        cells.add(this.getCellAtPosition(x, y));
      }
    }
    return [...cells.values()];
  }
  getCellsInColumn(columnIndex) {
    return this.getCellsInRange({
      fromX: columnIndex,
      toX: columnIndex,
      fromY: 0,
      toY: this.rowCount - 1
    });
  }
  getRangeForCell(cell) {
    let range = null;
    const cellKey = cell.getKey();
    for (let y = 0; y < this.rowCount; y++) {
      for (let x = 0; x < this.columnCount; x++) {
        const index = y * this.columnCount + x;
        const lCell = this.cells[index];
        if (lCell.getKey() === cellKey) {
          if (range === null) {
            range = { fromX: x, toX: x, fromY: y, toY: y };
          } else {
            range.fromX = Math.min(range.fromX, x);
            range.toX = Math.max(range.toX, x);
            range.fromY = Math.min(range.fromY, y);
            range.toY = Math.max(range.toY, y);
          }
        }
      }
    }
    return range;
  }
};

// resources/js/wysiwyg/utils/tables.ts
function $getTableFromCell(cell) {
  return $getParentOfType(cell, $isTableNode);
}
function getTableColumnWidths(table2) {
  const maxColRow = getMaxColRowFromTable(table2);
  const colGroup = table2.querySelector("colgroup");
  let widths = [];
  if (colGroup && (colGroup.childElementCount === maxColRow?.childElementCount || !maxColRow)) {
    widths = extractWidthsFromRow(colGroup);
  }
  if (widths.filter(Boolean).length === 0 && maxColRow) {
    widths = extractWidthsFromRow(maxColRow);
  }
  return widths;
}
function getMaxColRowFromTable(table2) {
  const rows = table2.querySelectorAll("tr");
  let maxColCount = 0;
  let maxColRow = null;
  for (const row of rows) {
    if (row.childElementCount > maxColCount) {
      maxColRow = row;
      maxColCount = row.childElementCount;
    }
  }
  return maxColRow;
}
function extractWidthsFromRow(row) {
  return [...row.children].map((child) => extractWidthFromElement(child));
}
function extractWidthFromElement(element) {
  let width = element.style.width || element.getAttribute("width");
  if (width && !Number.isNaN(Number(width))) {
    width = width + "px";
  }
  return width || "";
}
function $setTableColumnWidth(node, columnIndex, width) {
  const rows = node.getChildren();
  let maxCols = 0;
  for (const row of rows) {
    const cellCount = row.getChildren().length;
    if (cellCount > maxCols) {
      maxCols = cellCount;
    }
  }
  let colWidths = node.getColWidths();
  if (colWidths.length === 0 || colWidths.length < maxCols) {
    colWidths = Array(maxCols).fill("");
  }
  if (columnIndex + 1 > colWidths.length) {
    console.error(`Attempted to set table column width for column [${columnIndex}] but only ${colWidths.length} columns found`);
  }
  colWidths[columnIndex] = formatSizeValue(width);
  node.setColWidths(colWidths);
}
function $getTableColumnWidth(editor, node, columnIndex) {
  const colWidths = node.getColWidths();
  if (colWidths.length > columnIndex && colWidths[columnIndex].endsWith("px")) {
    return Number(colWidths[columnIndex].replace("px", ""));
  }
  const table2 = editor.getElementByKey(node.__key);
  if (table2) {
    const maxColRow = getMaxColRowFromTable(table2);
    if (maxColRow && maxColRow.children.length > columnIndex) {
      const cell = maxColRow.children[columnIndex];
      return cell.clientWidth;
    }
  }
  return 0;
}
function $getCellColumnIndex(node) {
  const row = node.getParent();
  if (!$isTableRowNode(row)) {
    return -1;
  }
  let index = 0;
  const cells = row.getChildren();
  for (const cell of cells) {
    let colSpan = cell.getColSpan() || 1;
    index += colSpan;
    if (cell.getKey() === node.getKey()) {
      break;
    }
  }
  return index - 1;
}
function $setTableCellColumnWidth(cell, width) {
  const table2 = $getTableFromCell(cell);
  const index = $getCellColumnIndex(cell);
  if (table2 && index >= 0) {
    $setTableColumnWidth(table2, index, width);
  }
}
function $getTableCellColumnWidth(editor, cell) {
  const table2 = $getTableFromCell(cell);
  const index = $getCellColumnIndex(cell);
  if (!table2) {
    return "";
  }
  const widths = table2.getColWidths();
  return widths.length > index ? widths[index] : "";
}
function buildColgroupFromTableWidths(colWidths) {
  if (colWidths.length === 0) {
    return null;
  }
  const colgroup = el("colgroup");
  for (const width of colWidths) {
    const col = el("col");
    if (width) {
      col.style.width = width;
    }
    colgroup.append(col);
  }
  return colgroup;
}
function $getTableCellsFromSelection(selection) {
  if ($isTableSelection(selection)) {
    const nodes = selection.getNodes();
    return nodes.filter((n) => $isTableCellNode(n));
  }
  const cell = $getNodeFromSelection(selection, $isTableCellNode);
  return cell ? [cell] : [];
}
function $mergeTableCellsInSelection(selection) {
  const selectionShape = selection.getShape();
  const cells = $getTableCellsFromSelection(selection);
  if (cells.length === 0) {
    return;
  }
  const table2 = $getTableFromCell(cells[0]);
  if (!table2) {
    return;
  }
  const tableMap = new TableMap(table2);
  const headCell = tableMap.getCellAtPosition(selectionShape.toX, selectionShape.toY);
  if (!headCell) {
    return;
  }
  const fixedToX = selectionShape.toX + ((headCell.getColSpan() || 1) - 1);
  const fixedToY = selectionShape.toY + ((headCell.getRowSpan() || 1) - 1);
  const mergeCells2 = tableMap.getCellsInRange({
    fromX: selectionShape.fromX,
    fromY: selectionShape.fromY,
    toX: fixedToX,
    toY: fixedToY
  });
  if (mergeCells2.length === 0) {
    return;
  }
  const firstCell = mergeCells2[0];
  const newWidth = Math.abs(selectionShape.fromX - fixedToX) + 1;
  const newHeight = Math.abs(selectionShape.fromY - fixedToY) + 1;
  for (let i = 1; i < mergeCells2.length; i++) {
    const mergeCell = mergeCells2[i];
    firstCell.append(...mergeCell.getChildren());
    mergeCell.remove();
  }
  firstCell.setColSpan(newWidth);
  firstCell.setRowSpan(newHeight);
}
function $getTableRowsFromSelection(selection) {
  const cells = $getTableCellsFromSelection(selection);
  const rowsByKey = {};
  for (const cell of cells) {
    const row = cell.getParent();
    if ($isTableRowNode(row)) {
      rowsByKey[row.getKey()] = row;
    }
  }
  return Object.values(rowsByKey);
}
function $getTableFromSelection(selection) {
  const cells = $getTableCellsFromSelection(selection);
  if (cells.length === 0) {
    return null;
  }
  const table2 = $getParentOfType(cells[0], $isTableNode);
  if ($isTableNode(table2)) {
    return table2;
  }
  return null;
}
function $clearTableSizes(table2) {
  table2.setColWidths([]);
  for (const row of table2.getChildren()) {
    if (!$isTableRowNode(row)) {
      continue;
    }
    const rowStyles = row.getStyles();
    rowStyles.delete("height");
    rowStyles.delete("width");
    row.setStyles(rowStyles);
    const cells = row.getChildren().filter((c) => $isTableCellNode(c));
    for (const cell of cells) {
      const cellStyles = cell.getStyles();
      cellStyles.delete("height");
      cellStyles.delete("width");
      cell.setStyles(cellStyles);
      cell.clearWidth();
    }
  }
}
function $clearTableFormatting(table2) {
  table2.setColWidths([]);
  table2.setStyles(/* @__PURE__ */ new Map());
  for (const row of table2.getChildren()) {
    if (!$isTableRowNode(row)) {
      continue;
    }
    row.setStyles(/* @__PURE__ */ new Map());
    const cells = row.getChildren().filter((c) => $isTableCellNode(c));
    for (const cell of cells) {
      cell.setStyles(/* @__PURE__ */ new Map());
      cell.setBackgroundColor(null);
      cell.clearWidth();
    }
  }
}
function $forEachTableCell2(table2, callback) {
  outer: for (const row of table2.getChildren()) {
    if (!$isTableRowNode(row)) {
      continue;
    }
    const cells = row.getChildren();
    for (const cell of cells) {
      if (!$isTableCellNode(cell)) {
        return;
      }
      const result = callback(cell);
      if (result === false) {
        break outer;
      }
    }
  }
}
function $getCellPaddingForTable(table2) {
  let padding = null;
  $forEachTableCell2(table2, (cell) => {
    const cellPadding = cell.getStyles().get("padding") || "";
    if (padding === null) {
      padding = cellPadding;
    }
    if (cellPadding !== padding) {
      padding = null;
      return false;
    }
  });
  return padding || "";
}

// resources/js/wysiwyg/lexical/table/LexicalTableNode.ts
var TableNode4 = class _TableNode extends CommonBlockNode {
  constructor(key) {
    super(key);
    __publicField(this, "__colWidths", []);
    __publicField(this, "__styles", /* @__PURE__ */ new Map());
  }
  static getType() {
    return "table";
  }
  static clone(node) {
    const newNode = new _TableNode(node.__key);
    copyCommonBlockProperties(node, newNode);
    newNode.__colWidths = [...node.__colWidths];
    newNode.__styles = new Map(node.__styles);
    return newNode;
  }
  static importDOM() {
    return {
      table: (_node) => ({
        conversion: $convertTableElement,
        priority: 1
      })
    };
  }
  static importJSON(_serializedNode) {
    const node = $createTableNode();
    deserializeCommonBlockNode(_serializedNode, node);
    node.setColWidths(_serializedNode.colWidths);
    node.setStyles(new Map(Object.entries(_serializedNode.styles)));
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "table",
      version: 1,
      colWidths: this.__colWidths,
      styles: Object.fromEntries(this.__styles)
    };
  }
  createDOM(config, editor) {
    const tableElement = document.createElement("table");
    addClassNamesToElement(tableElement, config.theme.table);
    updateElementWithCommonBlockProps(tableElement, this);
    const colWidths = this.getColWidths();
    const colgroup = buildColgroupFromTableWidths(colWidths);
    if (colgroup) {
      tableElement.append(colgroup);
    }
    for (const [name, value] of this.__styles.entries()) {
      tableElement.style.setProperty(name, value);
    }
    return tableElement;
  }
  updateDOM(_prevNode, dom) {
    applyCommonPropertyChanges(_prevNode, this, dom);
    if (this.__colWidths.join(":") !== _prevNode.__colWidths.join(":")) {
      const existingColGroup = Array.from(dom.children).find((child) => child.nodeName === "COLGROUP");
      const newColGroup = buildColgroupFromTableWidths(this.__colWidths);
      if (existingColGroup) {
        existingColGroup.remove();
      }
      if (newColGroup) {
        dom.prepend(newColGroup);
      }
    }
    if (Array.from(this.__styles.values()).join(":") !== Array.from(_prevNode.__styles.values()).join(":")) {
      dom.style.cssText = "";
      for (const [name, value] of this.__styles.entries()) {
        dom.style.setProperty(name, value);
      }
    }
    return false;
  }
  exportDOM(editor) {
    return {
      ...super.exportDOM(editor),
      after: (tableElement) => {
        if (!tableElement) {
          return;
        }
        const newElement = tableElement.cloneNode();
        const tBody = document.createElement("tbody");
        if (isHTMLElement(tableElement)) {
          for (const child of Array.from(tableElement.children)) {
            if (child.nodeName === "TR") {
              tBody.append(child);
            } else if (child.nodeName === "CAPTION") {
              newElement.insertBefore(child, newElement.firstChild);
            } else {
              newElement.append(child);
            }
          }
        }
        newElement.append(tBody);
        return newElement;
      }
    };
  }
  canBeEmpty() {
    return false;
  }
  isShadowRoot() {
    return true;
  }
  setColWidths(widths) {
    const self = this.getWritable();
    self.__colWidths = widths;
  }
  getColWidths() {
    const self = this.getLatest();
    return [...self.__colWidths];
  }
  getStyles() {
    const self = this.getLatest();
    return new Map(self.__styles);
  }
  setStyles(styles) {
    const self = this.getWritable();
    self.__styles = new Map(styles);
  }
  getCordsFromCellNode(tableCellNode, table2) {
    const { rows, domRows } = table2;
    for (let y = 0; y < rows; y++) {
      const row = domRows[y];
      if (row == null) {
        continue;
      }
      const x = row.findIndex((cell) => {
        if (!cell) {
          return;
        }
        const { elem } = cell;
        const cellNode = $getNearestNodeFromDOMNode(elem);
        return cellNode === tableCellNode;
      });
      if (x !== -1) {
        return { x, y };
      }
    }
    throw new Error("Cell not found in table.");
  }
  getDOMCellFromCords(x, y, table2) {
    const { domRows } = table2;
    const row = domRows[y];
    if (row == null) {
      return null;
    }
    const index = x < row.length ? x : row.length - 1;
    const cell = row[index];
    if (cell == null) {
      return null;
    }
    return cell;
  }
  getDOMCellFromCordsOrThrow(x, y, table2) {
    const cell = this.getDOMCellFromCords(x, y, table2);
    if (!cell) {
      throw new Error("Cell not found at cords.");
    }
    return cell;
  }
  getCellNodeFromCords(x, y, table2) {
    const cell = this.getDOMCellFromCords(x, y, table2);
    if (cell == null) {
      return null;
    }
    const node = $getNearestNodeFromDOMNode(cell.elem);
    if ($isTableCellNode(node)) {
      return node;
    }
    return null;
  }
  getCellNodeFromCordsOrThrow(x, y, table2) {
    const node = this.getCellNodeFromCords(x, y, table2);
    if (!node) {
      throw new Error("Node at cords not TableCellNode.");
    }
    return node;
  }
  canSelectBefore() {
    return true;
  }
  canIndent() {
    return false;
  }
};
function $convertTableElement(element) {
  const node = $createTableNode();
  setCommonBlockPropsFromElement(element, node);
  const colWidths = getTableColumnWidths(element);
  node.setColWidths(colWidths);
  node.setStyles(extractStyleMapFromElement(element));
  return { node };
}
function $createTableNode() {
  return $applyNodeReplacement(new TableNode4());
}
function $isTableNode(node) {
  return node instanceof TableNode4;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalHorizontalRuleNode.ts
var HorizontalRuleNode = class _HorizontalRuleNode extends ElementNode8 {
  constructor() {
    super(...arguments);
    __publicField(this, "__id", "");
  }
  static getType() {
    return "horizontal-rule";
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  static clone(node) {
    const newNode = new _HorizontalRuleNode(node.__key);
    newNode.__id = node.__id;
    return newNode;
  }
  createDOM(_config, _editor) {
    const el3 = document.createElement("hr");
    if (this.__id) {
      el3.setAttribute("id", this.__id);
    }
    return el3;
  }
  updateDOM(prevNode, dom) {
    return prevNode.__id !== this.__id;
  }
  static importDOM() {
    return {
      hr(node) {
        return {
          conversion: (element) => {
            const node2 = new _HorizontalRuleNode();
            if (element.id) {
              node2.setId(element.id);
            }
            return { node: node2 };
          },
          priority: 3
        };
      }
    };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "horizontal-rule",
      version: 1,
      id: this.__id
    };
  }
  static importJSON(serializedNode) {
    const node = $createHorizontalRuleNode();
    node.setId(serializedNode.id);
    return node;
  }
};
function $createHorizontalRuleNode() {
  return new HorizontalRuleNode();
}
function $isHorizontalRuleNode(node) {
  return node instanceof HorizontalRuleNode;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalCodeBlockNode.ts
var getLanguageFromClassList = (classes) => {
  const langClasses = classes.split(" ").filter((cssClass) => cssClass.startsWith("language-"));
  return (langClasses[0] || "").replace("language-", "");
};
var CodeBlockNode = class _CodeBlockNode extends DecoratorNode3 {
  constructor(language = "", code2 = "", key) {
    super(key);
    __publicField(this, "__id", "");
    __publicField(this, "__language", "");
    __publicField(this, "__code", "");
    this.__language = language;
    this.__code = code2;
  }
  static getType() {
    return "code-block";
  }
  static clone(node) {
    const newNode = new _CodeBlockNode(node.__language, node.__code, node.__key);
    newNode.__id = node.__id;
    return newNode;
  }
  setLanguage(language) {
    const self = this.getWritable();
    self.__language = language;
  }
  getLanguage() {
    const self = this.getLatest();
    return self.__language;
  }
  setCode(code2) {
    const self = this.getWritable();
    self.__code = code2;
  }
  getCode() {
    const self = this.getLatest();
    return self.__code;
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  decorate(editor, config) {
    return {
      type: "code",
      getNode: () => this
    };
  }
  isInline() {
    return false;
  }
  isIsolated() {
    return true;
  }
  createDOM(_config, _editor) {
    const codeBlock2 = el("pre", {
      id: this.__id || null
    }, [
      el("code", {
        class: this.__language ? `language-${this.__language}` : null
      }, [this.__code])
    ]);
    return el("div", { class: "editor-code-block-wrap" }, [codeBlock2]);
  }
  updateDOM(prevNode, dom) {
    const code2 = dom.querySelector("code");
    if (!code2) return false;
    if (prevNode.__language !== this.__language) {
      code2.className = this.__language ? `language-${this.__language}` : "";
    }
    if (prevNode.__id !== this.__id) {
      dom.setAttribute("id", this.__id);
    }
    if (prevNode.__code !== this.__code) {
      code2.textContent = this.__code;
    }
    return false;
  }
  exportDOM(editor) {
    const dom = this.createDOM(editor._config, editor);
    return {
      element: dom.querySelector("pre")
    };
  }
  static importDOM() {
    return {
      pre(node) {
        return {
          conversion: (element) => {
            const codeEl = element.querySelector("code");
            const language = getLanguageFromClassList(element.className) || codeEl && getLanguageFromClassList(codeEl.className) || "";
            const code2 = codeEl ? (codeEl.textContent || "").trim() : (element.textContent || "").trim();
            const node2 = $createCodeBlockNode(language, code2);
            if (element.id) {
              node2.setId(element.id);
            }
            return {
              node: node2,
              after(childNodes) {
                return [];
              }
            };
          },
          priority: 3
        };
      }
    };
  }
  exportJSON() {
    return {
      type: "code-block",
      version: 1,
      id: this.__id,
      language: this.__language,
      code: this.__code
    };
  }
  static importJSON(serializedNode) {
    const node = $createCodeBlockNode(serializedNode.language, serializedNode.code);
    node.setId(serializedNode.id || "");
    return node;
  }
};
function $createCodeBlockNode(language = "", code2 = "") {
  return new CodeBlockNode(language, code2);
}
function $isCodeBlockNode(node) {
  return node instanceof CodeBlockNode;
}
function $openCodeEditorForNode(editor, node) {
  const code2 = node.getCode();
  const language = node.getLanguage();
  const codeEditor = window.$components.first("code-editor");
  codeEditor.open(code2, language, "ltr", (newCode, newLang) => {
    editor.update(() => {
      node.setCode(newCode);
      node.setLanguage(newLang);
    });
  }, () => {
  });
}

// resources/js/wysiwyg/lexical/rich-text/LexicalDiagramNode.ts
var DiagramNode = class _DiagramNode extends DecoratorNode3 {
  constructor(drawingId, drawingUrl, key) {
    super(key);
    __publicField(this, "__id", "");
    __publicField(this, "__drawingId", "");
    __publicField(this, "__drawingUrl", "");
    this.__drawingId = drawingId;
    this.__drawingUrl = drawingUrl;
  }
  static getType() {
    return "diagram";
  }
  static clone(node) {
    const newNode = new _DiagramNode(node.__drawingId, node.__drawingUrl);
    newNode.__id = node.__id;
    return newNode;
  }
  setDrawingIdAndUrl(drawingId, drawingUrl) {
    const self = this.getWritable();
    self.__drawingUrl = drawingUrl;
    self.__drawingId = drawingId;
  }
  getDrawingIdAndUrl() {
    const self = this.getLatest();
    return {
      id: self.__drawingId,
      url: self.__drawingUrl
    };
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  decorate(editor, config) {
    return {
      type: "diagram",
      getNode: () => this
    };
  }
  isInline() {
    return false;
  }
  isIsolated() {
    return true;
  }
  createDOM(_config, _editor) {
    return el("div", {
      id: this.__id || null,
      "drawio-diagram": this.__drawingId
    }, [
      el("img", { src: this.__drawingUrl })
    ]);
  }
  updateDOM(prevNode, dom) {
    const img = dom.querySelector("img");
    if (!img) return false;
    if (prevNode.__id !== this.__id) {
      dom.setAttribute("id", this.__id);
    }
    if (prevNode.__drawingUrl !== this.__drawingUrl) {
      img.setAttribute("src", this.__drawingUrl);
    }
    if (prevNode.__drawingId !== this.__drawingId) {
      dom.setAttribute("drawio-diagram", this.__drawingId);
    }
    return false;
  }
  static importDOM() {
    return {
      div(node) {
        if (!node.hasAttribute("drawio-diagram")) {
          return null;
        }
        return {
          conversion: (element) => {
            const img = element.querySelector("img");
            const drawingUrl = img?.getAttribute("src") || "";
            const drawingId = element.getAttribute("drawio-diagram") || "";
            const node2 = $createDiagramNode(drawingId, drawingUrl);
            if (element.id) {
              node2.setId(element.id);
            }
            return { node: node2 };
          },
          priority: 3
        };
      }
    };
  }
  exportJSON() {
    return {
      type: "diagram",
      version: 1,
      id: this.__id,
      drawingId: this.__drawingId,
      drawingUrl: this.__drawingUrl
    };
  }
  static importJSON(serializedNode) {
    const node = $createDiagramNode(serializedNode.drawingId, serializedNode.drawingUrl);
    node.setId(serializedNode.id || "");
    return node;
  }
};
function $createDiagramNode(drawingId = "", drawingUrl = "") {
  return new DiagramNode(drawingId, drawingUrl);
}

// resources/js/wysiwyg/lexical/rich-text/LexicalMediaNode.ts
var attributeAllowList = [
  "width",
  "height",
  "style",
  "title",
  "name",
  "src",
  "allow",
  "allowfullscreen",
  "loading",
  "sandbox",
  "type",
  "data",
  "controls",
  "autoplay",
  "controlslist",
  "loop",
  "muted",
  "playsinline",
  "poster",
  "preload"
];
function filterAttributes(attributes) {
  const filtered = {};
  for (const key of Object.keys(attributes)) {
    if (attributeAllowList.includes(key)) {
      filtered[key] = attributes[key];
    }
  }
  return filtered;
}
function removeStyleFromAttributes(attributes, styleName) {
  const attrCopy = Object.assign({}, attributes);
  if (!attributes.style) {
    return attrCopy;
  }
  const map = styleStringToStyleMap(attributes.style);
  map.delete(styleName);
  attrCopy.style = styleMapToStyleString(map);
  return attrCopy;
}
function domElementToNode(tag, element) {
  const node = $createMediaNode(tag);
  const attributes = {};
  for (const attribute of element.attributes) {
    attributes[attribute.name] = attribute.value;
  }
  node.setAttributes(attributes);
  const sources = [];
  if (tag === "video" || tag === "audio") {
    for (const child of element.children) {
      if (child.tagName === "SOURCE") {
        const src = child.getAttribute("src");
        const type = child.getAttribute("type");
        if (src && type) {
          sources.push({ src, type });
        }
      }
    }
    node.setSources(sources);
  }
  setCommonBlockPropsFromElement(element, node);
  return node;
}
var MediaNode = class _MediaNode extends ElementNode8 {
  constructor(tag, key) {
    super(key);
    __publicField(this, "__id", "");
    __publicField(this, "__alignment", "");
    __publicField(this, "__tag");
    __publicField(this, "__attributes", {});
    __publicField(this, "__sources", []);
    __publicField(this, "__inset", 0);
    this.__tag = tag;
  }
  static getType() {
    return "media";
  }
  static clone(node) {
    const newNode = new _MediaNode(node.__tag, node.__key);
    newNode.__attributes = Object.assign({}, node.__attributes);
    newNode.__sources = node.__sources.map((s) => Object.assign({}, s));
    newNode.__id = node.__id;
    newNode.__alignment = node.__alignment;
    newNode.__inset = node.__inset;
    return newNode;
  }
  setTag(tag) {
    const self = this.getWritable();
    self.__tag = tag;
  }
  getTag() {
    const self = this.getLatest();
    return self.__tag;
  }
  setAttributes(attributes) {
    const self = this.getWritable();
    self.__attributes = filterAttributes(attributes);
  }
  getAttributes() {
    const self = this.getLatest();
    return Object.assign({}, self.__attributes);
  }
  setSources(sources) {
    const self = this.getWritable();
    self.__sources = sources;
  }
  getSources() {
    const self = this.getLatest();
    return self.__sources.map((s) => Object.assign({}, s));
  }
  setSrc(src) {
    const attrs = this.getAttributes();
    const sources = this.getSources();
    if (this.__tag === "object") {
      attrs.data = src;
    }
    if (this.__tag === "video" && sources.length > 0) {
      sources[0].src = src;
      delete attrs.src;
      if (sources.length > 1) {
        sources.splice(1, sources.length - 1);
      }
      this.setSources(sources);
    } else {
      attrs.src = src;
    }
    this.setAttributes(attrs);
  }
  setWidthAndHeight(width, height) {
    let attrs = Object.assign(
      this.getAttributes(),
      { width, height }
    );
    attrs = removeStyleFromAttributes(attrs, "width");
    attrs = removeStyleFromAttributes(attrs, "height");
    this.setAttributes(attrs);
  }
  setId(id) {
    const self = this.getWritable();
    self.__id = id;
  }
  getId() {
    const self = this.getLatest();
    return self.__id;
  }
  setAlignment(alignment) {
    const self = this.getWritable();
    self.__alignment = alignment;
  }
  getAlignment() {
    const self = this.getLatest();
    return self.__alignment;
  }
  setInset(size) {
    const self = this.getWritable();
    self.__inset = size;
  }
  getInset() {
    const self = this.getLatest();
    return self.__inset;
  }
  setHeight(height) {
    if (!height) {
      return;
    }
    const attrs = Object.assign(this.getAttributes(), { height });
    this.setAttributes(removeStyleFromAttributes(attrs, "height"));
  }
  getHeight() {
    const self = this.getLatest();
    return sizeToPixels(self.__attributes.height || "0");
  }
  setWidth(width) {
    const existingAttrs = this.getAttributes();
    const attrs = Object.assign(existingAttrs, { width });
    this.setAttributes(removeStyleFromAttributes(attrs, "width"));
  }
  getWidth() {
    const self = this.getLatest();
    return sizeToPixels(self.__attributes.width || "0");
  }
  isInline() {
    return true;
  }
  isParentRequired() {
    return true;
  }
  createInnerDOM() {
    const sources = this.__tag === "video" || this.__tag === "audio" ? this.__sources : [];
    const sourceEls = sources.map((source3) => el("source", source3));
    const element = el(this.__tag, this.__attributes, sourceEls);
    updateElementWithCommonBlockProps(element, this);
    return element;
  }
  createDOM(_config, _editor) {
    const media3 = this.createInnerDOM();
    return el("span", {
      class: media3.className + " editor-media-wrap"
    }, [media3]);
  }
  updateDOM(prevNode, dom) {
    if (prevNode.__tag !== this.__tag) {
      return true;
    }
    if (JSON.stringify(prevNode.__sources) !== JSON.stringify(this.__sources)) {
      return true;
    }
    if (JSON.stringify(prevNode.__attributes) !== JSON.stringify(this.__attributes)) {
      return true;
    }
    const mediaEl = dom.firstElementChild;
    if (prevNode.__id !== this.__id) {
      setOrRemoveAttribute(mediaEl, "id", this.__id);
    }
    if (prevNode.__alignment !== this.__alignment) {
      if (prevNode.__alignment) {
        dom.classList.remove(`align-${prevNode.__alignment}`);
        mediaEl.classList.remove(`align-${prevNode.__alignment}`);
      }
      if (this.__alignment) {
        dom.classList.add(`align-${this.__alignment}`);
        mediaEl.classList.add(`align-${this.__alignment}`);
      }
    }
    if (prevNode.__inset !== this.__inset) {
      dom.style.paddingLeft = `${this.__inset}px`;
    }
    return false;
  }
  static importDOM() {
    const buildConverter = (tag) => {
      return (node) => {
        return {
          conversion: (element) => {
            return {
              node: domElementToNode(tag, element)
            };
          },
          priority: 3
        };
      };
    };
    return {
      iframe: buildConverter("iframe"),
      embed: buildConverter("embed"),
      object: buildConverter("object"),
      video: buildConverter("video"),
      audio: buildConverter("audio")
    };
  }
  exportDOM(editor) {
    const element = this.createInnerDOM();
    return { element };
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "media",
      version: 1,
      id: this.__id,
      alignment: this.__alignment,
      inset: this.__inset,
      tag: this.__tag,
      attributes: this.__attributes,
      sources: this.__sources
    };
  }
  static importJSON(serializedNode) {
    const node = $createMediaNode(serializedNode.tag);
    deserializeCommonBlockNode(serializedNode, node);
    return node;
  }
};
function $createMediaNode(tag) {
  return new MediaNode(tag);
}
function $createMediaNodeFromHtml(html) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(`<body>${html}</body>`, "text/html");
  const el3 = doc.body.children[0];
  if (!(el3 instanceof HTMLElement)) {
    return null;
  }
  const tag = el3.tagName.toLowerCase();
  const validTypes = ["embed", "iframe", "video", "audio", "object"];
  if (!validTypes.includes(tag)) {
    return null;
  }
  return domElementToNode(tag, el3);
}
var urlPatterns = [
  {
    regex: /.*?youtu\.be\/([\w\-_\?&=.]+)/i,
    w: 560,
    h: 314,
    url: "https://www.youtube.com/embed/$1"
  },
  {
    regex: /.*youtube\.com(.+)v=([^&]+)(&([a-z0-9&=\-_]+))?.*/i,
    w: 560,
    h: 314,
    url: "https://www.youtube.com/embed/$2?$4"
  },
  {
    regex: /.*youtube.com\/embed\/([a-z0-9\?&=\-_]+).*/i,
    w: 560,
    h: 314,
    url: "https://www.youtube.com/embed/$1"
  }
];
var videoExtensions = ["mp4", "mpeg", "m4v", "m4p", "mov"];
var audioExtensions = ["3gp", "aac", "flac", "mp3", "m4a", "ogg", "wav", "webm"];
var iframeExtensions = ["html", "htm", "php", "asp", "aspx", ""];
function $createMediaNodeFromSrc(src) {
  for (const pattern of urlPatterns) {
    const match = src.match(pattern.regex);
    if (match) {
      const newSrc = src.replace(pattern.regex, pattern.url);
      const node2 = new MediaNode("iframe");
      node2.setSrc(newSrc);
      node2.setHeight(pattern.h);
      node2.setWidth(pattern.w);
      return node2;
    }
  }
  let nodeTag = "iframe";
  const srcEnd = src.split("?")[0].split("/").pop() || "";
  const srcEndSplit = srcEnd.split(".");
  const extension = (srcEndSplit.length > 1 ? srcEndSplit[srcEndSplit.length - 1] : "").toLowerCase();
  if (videoExtensions.includes(extension)) {
    nodeTag = "video";
  } else if (audioExtensions.includes(extension)) {
    nodeTag = "audio";
  } else if (extension && !iframeExtensions.includes(extension)) {
    nodeTag = "embed";
  }
  const node = new MediaNode(nodeTag);
  node.setSrc(src);
  return node;
}
function $isMediaNode(node) {
  return node instanceof MediaNode;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalHeadingNode.ts
var HeadingNode = class _HeadingNode extends CommonBlockNode {
  constructor(tag, key) {
    super(key);
    /** @internal */
    __publicField(this, "__tag");
    this.__tag = tag;
  }
  static getType() {
    return "heading";
  }
  static clone(node) {
    const clone = new _HeadingNode(node.__tag, node.__key);
    copyCommonBlockProperties(node, clone);
    return clone;
  }
  getTag() {
    return this.__tag;
  }
  // View
  createDOM(config) {
    const tag = this.__tag;
    const element = document.createElement(tag);
    const theme2 = config.theme;
    const classNames = theme2.heading;
    if (classNames !== void 0) {
      const className = classNames[tag];
      addClassNamesToElement(element, className);
    }
    updateElementWithCommonBlockProps(element, this);
    return element;
  }
  updateDOM(prevNode, dom) {
    return commonPropertiesDifferent(prevNode, this);
  }
  static importDOM() {
    return {
      h1: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      }),
      h2: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      }),
      h3: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      }),
      h4: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      }),
      h5: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      }),
      h6: (node) => ({
        conversion: $convertHeadingElement,
        priority: 0
      })
    };
  }
  exportDOM(editor) {
    const { element } = super.exportDOM(editor);
    if (element && isHTMLElement(element)) {
      if (this.isEmpty()) {
        element.append(document.createElement("br"));
      }
    }
    return {
      element
    };
  }
  static importJSON(serializedNode) {
    const node = $createHeadingNode(serializedNode.tag);
    deserializeCommonBlockNode(serializedNode, node);
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      tag: this.getTag(),
      type: "heading",
      version: 1
    };
  }
  // Mutation
  insertNewAfter(selection, restoreSelection = true) {
    const anchorOffet = selection ? selection.anchor.offset : 0;
    const lastDesc = this.getLastDescendant();
    const isAtEnd = !lastDesc || selection && selection.anchor.key === lastDesc.getKey() && anchorOffet === lastDesc.getTextContentSize();
    const newElement = isAtEnd || !selection ? $createParagraphNode() : $createHeadingNode(this.getTag());
    const direction = this.getDirection();
    newElement.setDirection(direction);
    this.insertAfter(newElement, restoreSelection);
    if (anchorOffet === 0 && !this.isEmpty() && selection) {
      const paragraph2 = $createParagraphNode();
      paragraph2.select();
      this.replace(paragraph2, true);
    }
    return newElement;
  }
  collapseAtStart() {
    const newElement = !this.isEmpty() ? $createHeadingNode(this.getTag()) : $createParagraphNode();
    const children = this.getChildren();
    children.forEach((child) => newElement.append(child));
    this.replace(newElement);
    return true;
  }
  extractWithChild() {
    return true;
  }
};
function $convertHeadingElement(element) {
  const nodeName = element.nodeName.toLowerCase();
  let node = null;
  if (nodeName === "h1" || nodeName === "h2" || nodeName === "h3" || nodeName === "h4" || nodeName === "h5" || nodeName === "h6") {
    node = $createHeadingNode(nodeName);
    setCommonBlockPropsFromElement(element, node);
  }
  return { node };
}
function $createHeadingNode(headingTag) {
  return $applyNodeReplacement(new HeadingNode(headingTag));
}
function $isHeadingNode(node) {
  return node instanceof HeadingNode;
}

// resources/js/wysiwyg/lexical/rich-text/LexicalQuoteNode.ts
var QuoteNode = class _QuoteNode extends CommonBlockNode {
  static getType() {
    return "quote";
  }
  static clone(node) {
    const clone = new _QuoteNode(node.__key);
    copyCommonBlockProperties(node, clone);
    return clone;
  }
  constructor(key) {
    super(key);
  }
  // View
  createDOM(config) {
    const element = document.createElement("blockquote");
    addClassNamesToElement(element, config.theme.quote);
    updateElementWithCommonBlockProps(element, this);
    return element;
  }
  updateDOM(prevNode, dom) {
    return commonPropertiesDifferent(prevNode, this);
  }
  static importDOM() {
    return {
      blockquote: (node) => ({
        conversion: $convertBlockquoteElement,
        priority: 0
      })
    };
  }
  exportDOM(editor) {
    const { element } = super.exportDOM(editor);
    if (element && isHTMLElement(element)) {
      if (this.isEmpty()) {
        element.append(document.createElement("br"));
      }
    }
    return {
      element
    };
  }
  static importJSON(serializedNode) {
    const node = $createQuoteNode();
    deserializeCommonBlockNode(serializedNode, node);
    return node;
  }
  exportJSON() {
    return {
      ...super.exportJSON(),
      type: "quote"
    };
  }
  // Mutation
  insertNewAfter(_, restoreSelection) {
    const newBlock = $createParagraphNode();
    const direction = this.getDirection();
    newBlock.setDirection(direction);
    this.insertAfter(newBlock, restoreSelection);
    return newBlock;
  }
  collapseAtStart() {
    const paragraph2 = $createParagraphNode();
    const children = this.getChildren();
    children.forEach((child) => paragraph2.append(child));
    this.replace(paragraph2);
    return true;
  }
  canMergeWhenEmpty() {
    return true;
  }
};
function $createQuoteNode() {
  return $applyNodeReplacement(new QuoteNode());
}
function $isQuoteNode(node) {
  return node instanceof QuoteNode;
}
function $convertBlockquoteElement(element) {
  const node = $createQuoteNode();
  setCommonBlockPropsFromElement(element, node);
  return { node };
}

// resources/js/wysiwyg/nodes.ts
function getNodesForPageEditor() {
  return [
    CalloutNode,
    HeadingNode,
    QuoteNode,
    ListNode3,
    ListItemNode2,
    TableNode4,
    TableRowNode,
    TableCellNode,
    CaptionNode,
    ImageNode,
    // TODO - Alignment
    HorizontalRuleNode,
    DetailsNode,
    CodeBlockNode,
    DiagramNode,
    MediaNode,
    // TODO - Alignment
    ParagraphNode,
    LinkNode
  ];
}
function getNodesForBasicEditor() {
  return [
    ListNode3,
    ListItemNode2,
    ParagraphNode,
    LinkNode
  ];
}
function registerCommonNodeMutationListeners(context) {
  const decorated = [ImageNode, CodeBlockNode, DiagramNode];
  const decorationDestroyListener = (mutations) => {
    for (let [nodeKey, mutation] of mutations) {
      if (mutation === "destroyed") {
        const decorator = context.manager.getDecoratorByNodeKey(nodeKey);
        if (decorator) {
          decorator.teardown();
        }
      }
    }
  };
  for (let decoratedNode of decorated) {
    context.editor.registerMutationListener(decoratedNode, (mutations) => decorationDestroyListener(mutations));
  }
}

// resources/js/wysiwyg/ui/framework/core.ts
function isUiBuilderDefinition(object) {
  return "build" in object;
}
var EditorUiElement = class {
  constructor() {
    __publicField(this, "dom", null);
    __publicField(this, "context", null);
    __publicField(this, "abortController", new AbortController());
  }
  setContext(context) {
    this.context = context;
  }
  getContext() {
    if (this.context === null) {
      throw new Error("Attempted to use EditorUIContext before it has been set");
    }
    return this.context;
  }
  getDOMElement() {
    if (!this.dom) {
      this.dom = this.buildDOM();
    }
    return this.dom;
  }
  rebuildDOM() {
    const newDOM = this.buildDOM();
    this.dom?.replaceWith(newDOM);
    this.dom = newDOM;
    return this.dom;
  }
  trans(text) {
    return this.getContext().translate(text);
  }
  updateState(state) {
    return;
  }
  emitEvent(name, data = {}) {
    if (this.dom) {
      this.dom.dispatchEvent(new CustomEvent("editor::" + name, { detail: data, bubbles: true }));
    }
  }
  onEvent(name, callback, listenTarget = null) {
    const target = listenTarget || this.dom;
    if (target) {
      target.addEventListener("editor::" + name, (event) => {
        callback(event.detail);
      }, { signal: this.abortController.signal });
    }
  }
  teardown() {
    if (this.dom && this.dom.isConnected) {
      this.dom.remove();
    }
    this.abortController.abort("teardown");
  }
};
var EditorContainerUiElement = class extends EditorUiElement {
  constructor(children) {
    super();
    __publicField(this, "children", []);
    this.children.push(...children);
  }
  buildDOM() {
    return el("div", {}, this.getChildren().map((child) => child.getDOMElement()));
  }
  getChildren() {
    return this.children;
  }
  addChildren(...children) {
    this.children.push(...children);
  }
  removeChildren(...children) {
    for (const child of children) {
      this.removeChild(child);
    }
  }
  removeChild(child) {
    const index = this.children.indexOf(child);
    if (index !== -1) {
      this.children.splice(index, 1);
    }
  }
  updateState(state) {
    for (const child of this.children) {
      child.updateState(state);
    }
  }
  setContext(context) {
    super.setContext(context);
    for (const child of this.getChildren()) {
      child.setContext(context);
    }
  }
  teardown() {
    for (const child of this.children) {
      child.teardown();
    }
    super.teardown();
  }
};
var EditorSimpleClassContainer = class extends EditorContainerUiElement {
  constructor(className, children) {
    super(children);
    __publicField(this, "className");
    this.className = className;
  }
  buildDOM() {
    return el("div", {
      class: this.className
    }, this.getChildren().map((child) => child.getDOMElement()));
  }
};

// resources/js/services/util.ts
function uniqueId() {
  const S4 = () => ((1 + Math.random()) * 65536 | 0).toString(16).substring(1);
  return `${S4() + S4()}-${S4()}-${S4()}-${S4()}-${S4()}${S4()}${S4()}`;
}
function uniqueIdSmall() {
  const S4 = () => ((1 + Math.random()) * 65536 | 0).toString(16).substring(1);
  return S4();
}

// resources/js/wysiwyg/ui/framework/forms.ts
var EditorFormField = class extends EditorUiElement {
  constructor(definition) {
    super();
    __publicField(this, "definition");
    this.definition = definition;
  }
  setValue(value) {
    const input = this.getDOMElement().querySelector("input,select,textarea");
    if (this.definition.type === "checkbox") {
      input.checked = Boolean(value);
    } else {
      input.value = value;
    }
    input.dispatchEvent(new Event("change"));
  }
  getName() {
    return this.definition.name;
  }
  buildDOM() {
    const id = `editor-form-field-${this.definition.name}-${Date.now()}`;
    let input;
    if (this.definition.type === "select") {
      const options = this.definition.valuesByLabel;
      const labels = Object.keys(options);
      const optionElems = labels.map((label) => el("option", { value: options[label] }, [this.trans(label)]));
      input = el("select", { id, name: this.definition.name, class: "editor-form-field-input" }, optionElems);
    } else if (this.definition.type === "textarea") {
      input = el("textarea", { id, name: this.definition.name, class: "editor-form-field-input" });
    } else if (this.definition.type === "checkbox") {
      input = el("input", { id, name: this.definition.name, type: "checkbox", class: "editor-form-field-input-checkbox", value: "true" });
    } else if (this.definition.type === "hidden") {
      input = el("input", { id, name: this.definition.name, type: "hidden" });
      return el("div", { hidden: "true" }, [input]);
    } else {
      input = el("input", { id, name: this.definition.name, class: "editor-form-field-input" });
    }
    return el("div", { class: "editor-form-field-wrapper" }, [
      el("label", { class: "editor-form-field-label", for: id }, [this.trans(this.definition.label)]),
      input
    ]);
  }
};
var EditorForm = class extends EditorContainerUiElement {
  constructor(definition) {
    let children = definition.fields.map((fieldDefinition) => {
      if (isUiBuilderDefinition(fieldDefinition)) {
        return fieldDefinition.build();
      }
      return new EditorFormField(fieldDefinition);
    });
    super(children);
    __publicField(this, "definition");
    __publicField(this, "onCancel", null);
    __publicField(this, "onSuccessfulSubmit", null);
    this.definition = definition;
  }
  setValues(values) {
    for (const name of Object.keys(values)) {
      const field = this.getFieldByName(name);
      if (field) {
        field.setValue(values[name]);
      }
    }
  }
  setOnCancel(callback) {
    this.onCancel = callback;
  }
  setOnSuccessfulSubmit(callback) {
    this.onSuccessfulSubmit = callback;
  }
  getFieldByName(name) {
    const search = (children) => {
      for (const child of children) {
        if (child instanceof EditorFormField && child.getName() === name) {
          return child;
        } else if (child instanceof EditorContainerUiElement) {
          const matchingChild = search(child.getChildren());
          if (matchingChild) {
            return matchingChild;
          }
        }
      }
      return null;
    };
    return search(this.getChildren());
  }
  buildDOM() {
    const cancelButton = el("button", { type: "button", class: "editor-form-action-secondary" }, [this.trans("Cancel")]);
    const form = el("form", {}, [
      ...this.children.map((child) => child.getDOMElement()),
      el("div", { class: "editor-form-actions" }, [
        cancelButton,
        el("button", { type: "submit", class: "editor-form-action-primary" }, [this.trans(this.definition.submitText)])
      ])
    ]);
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      const formData = new FormData(form);
      const result = await this.definition.action(formData, this.getContext());
      if (result && this.onSuccessfulSubmit) {
        this.onSuccessfulSubmit();
      }
    });
    cancelButton.addEventListener("click", (event) => {
      if (this.onCancel) {
        this.onCancel();
      }
    });
    return form;
  }
};
var EditorFormTab = class extends EditorContainerUiElement {
  constructor(definition) {
    const fields = definition.contents.map((fieldDef) => {
      if (isUiBuilderDefinition(fieldDef)) {
        return fieldDef.build();
      }
      return new EditorFormField(fieldDef);
    });
    super(fields);
    __publicField(this, "definition");
    __publicField(this, "fields");
    __publicField(this, "id");
    this.definition = definition;
    this.fields = fields;
    this.id = uniqueId();
  }
  getLabel() {
    return this.getContext().translate(this.definition.label);
  }
  getId() {
    return this.id;
  }
  buildDOM() {
    return el(
      "div",
      {
        class: "editor-form-tab-content",
        role: "tabpanel",
        id: `editor-tabpanel-${this.id}`,
        "aria-labelledby": `editor-tab-${this.id}`
      },
      this.fields.map((f) => f.getDOMElement())
    );
  }
};
var EditorFormTabs = class extends EditorContainerUiElement {
  constructor(definitions) {
    const tabs = definitions.map((d) => new EditorFormTab(d));
    super(tabs);
    __publicField(this, "definitions", []);
    __publicField(this, "tabs", []);
    this.definitions = definitions;
    this.tabs = tabs;
  }
  buildDOM() {
    const controls = [];
    const contents = [];
    const selectTab = (tabIndex) => {
      for (let i = 0; i < controls.length; i++) {
        controls[i].setAttribute("aria-selected", i === tabIndex ? "true" : "false");
      }
      for (let i = 0; i < contents.length; i++) {
        contents[i].hidden = !(i === tabIndex);
      }
    };
    for (const tab of this.tabs) {
      const button = el("button", {
        class: "editor-form-tab-control",
        type: "button",
        role: "tab",
        id: `editor-tab-${tab.getId()}`,
        "aria-controls": `editor-tabpanel-${tab.getId()}`
      }, [tab.getLabel()]);
      contents.push(tab.getDOMElement());
      controls.push(button);
      button.addEventListener("click", (event) => {
        selectTab(controls.indexOf(button));
      });
    }
    selectTab(0);
    return el("div", { class: "editor-form-tab-container" }, [
      el("div", { class: "editor-form-tab-controls" }, controls),
      el("div", { class: "editor-form-tab-contents" }, contents)
    ]);
  }
};

// resources/icons/close.svg
var close_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';

// resources/js/wysiwyg/ui/framework/modals.ts
var EditorFormModal = class extends EditorContainerUiElement {
  constructor(definition, key) {
    super([new EditorForm(definition.form)]);
    __publicField(this, "definition");
    __publicField(this, "key");
    this.definition = definition;
    this.key = key;
  }
  show(defaultValues) {
    const dom = this.getDOMElement();
    document.body.append(dom);
    const form = this.getForm();
    form.setValues(defaultValues);
    form.setOnCancel(this.hide.bind(this));
    form.setOnSuccessfulSubmit(this.hide.bind(this));
    this.getContext().manager.setModalActive(this.key, this);
  }
  hide() {
    this.getContext().manager.setModalInactive(this.key);
    this.teardown();
  }
  getForm() {
    return this.children[0];
  }
  buildDOM() {
    const closeButton = el("button", {
      class: "editor-modal-close",
      type: "button",
      title: this.trans("Close")
    });
    closeButton.innerHTML = close_default;
    closeButton.addEventListener("click", this.hide.bind(this));
    const modal = el("div", { class: "editor-modal editor-form-modal" }, [
      el("div", { class: "editor-modal-header" }, [
        el("div", { class: "editor-modal-title" }, [this.trans(this.definition.title)]),
        closeButton
      ]),
      el("div", { class: "editor-modal-body" }, [
        this.getForm().getDOMElement()
      ])
    ]);
    const wrapper = el("div", { class: "editor-modal-wrapper" }, [modal]);
    wrapper.addEventListener("click", (event) => {
      if (event.target && !modal.contains(event.target)) {
        this.hide();
      }
    });
    return wrapper;
  }
};

// resources/js/wysiwyg/ui/framework/toolbars.ts
var EditorContextToolbar = class extends EditorContainerUiElement {
  constructor(target, children) {
    super(children);
    __publicField(this, "target");
    this.target = target;
  }
  buildDOM() {
    return el("div", {
      class: "editor-context-toolbar"
    }, this.getChildren().map((child) => child.getDOMElement()));
  }
  updatePosition() {
    const editorBounds = this.getContext().scrollDOM.getBoundingClientRect();
    const targetBounds = this.target.getBoundingClientRect();
    const dom = this.getDOMElement();
    const domBounds = dom.getBoundingClientRect();
    const showing = targetBounds.bottom > editorBounds.top && targetBounds.top < editorBounds.bottom;
    dom.hidden = !showing;
    if (!this.target.isConnected) {
      this.getContext().manager.triggerFutureStateRefresh();
      return;
    } else if (!showing) {
      return;
    }
    const showAbove = targetBounds.bottom + 6 + domBounds.height > editorBounds.bottom;
    dom.classList.toggle("is-above", showAbove);
    const targetMid = targetBounds.left + targetBounds.width / 2;
    const targetLeft = targetMid - domBounds.width / 2;
    if (showAbove) {
      dom.style.top = targetBounds.top - 6 - domBounds.height + "px";
    } else {
      dom.style.top = targetBounds.bottom + 6 + "px";
    }
    dom.style.left = targetLeft + "px";
  }
  insert(children) {
    this.addChildren(...children);
    const dom = this.getDOMElement();
    dom.append(...children.map((child) => child.getDOMElement()));
  }
};

// resources/js/wysiwyg/ui/framework/helpers/dropdowns.ts
function positionMenu(menu, toggle, showAside) {
  const toggleRect = toggle.getBoundingClientRect();
  const menuBounds = menu.getBoundingClientRect();
  menu.style.position = "fixed";
  if (showAside) {
    let targetLeft = toggleRect.right;
    const isRightOOB = toggleRect.right + menuBounds.width > window.innerWidth;
    if (isRightOOB) {
      targetLeft = Math.max(toggleRect.left - menuBounds.width, 0);
    }
    menu.style.top = toggleRect.top + "px";
    menu.style.left = targetLeft + "px";
  } else {
    const isRightOOB = toggleRect.left + menuBounds.width > window.innerWidth;
    let targetLeft = toggleRect.left;
    if (isRightOOB) {
      targetLeft = Math.max(toggleRect.right - menuBounds.width, 0);
    }
    menu.style.top = toggleRect.bottom + "px";
    menu.style.left = targetLeft + "px";
  }
}
var DropDownManager = class {
  constructor() {
    __publicField(this, "dropdownOptions", /* @__PURE__ */ new WeakMap());
    __publicField(this, "openDropdowns", /* @__PURE__ */ new Set());
    this.onMenuMouseOver = this.onMenuMouseOver.bind(this);
    this.onWindowClick = this.onWindowClick.bind(this);
    window.addEventListener("click", this.onWindowClick);
  }
  teardown() {
    window.removeEventListener("click", this.onWindowClick);
  }
  onWindowClick(event) {
    const target = event.target;
    this.closeAllNotContainingElement(target);
  }
  closeAllNotContainingElement(element) {
    for (const menu of this.openDropdowns) {
      if (!menu.parentElement?.contains(element)) {
        this.closeDropdown(menu);
      }
    }
  }
  onMenuMouseOver(event) {
    const target = event.target;
    this.closeAllNotContainingElement(target);
  }
  /**
   * Close all open dropdowns.
   */
  closeAll() {
    for (const menu of this.openDropdowns) {
      this.closeDropdown(menu);
    }
  }
  closeDropdown(menu) {
    menu.hidden = true;
    menu.style.removeProperty("position");
    menu.style.removeProperty("left");
    menu.style.removeProperty("top");
    this.openDropdowns.delete(menu);
    menu.removeEventListener("mouseover", this.onMenuMouseOver);
    const onClose = this.getOptions(menu).onClose;
    if (onClose) {
      onClose();
    }
  }
  openDropdown(menu) {
    const { toggle, showAside, onOpen } = this.getOptions(menu);
    menu.hidden = false;
    positionMenu(menu, toggle, Boolean(showAside));
    this.openDropdowns.add(menu);
    menu.addEventListener("mouseover", this.onMenuMouseOver);
    if (onOpen) {
      onOpen();
    }
  }
  getOptions(menu) {
    const options = this.dropdownOptions.get(menu);
    if (!options) {
      throw new Error(`Can't find options for dropdown menu`);
    }
    return options;
  }
  /**
   * Add handling for a new dropdown.
   */
  handle(options) {
    const { menu, toggle, showOnHover } = options;
    this.dropdownOptions.set(menu, options);
    const toggleShowing = (event) => {
      menu.hasAttribute("hidden") ? this.openDropdown(menu) : this.closeDropdown(menu);
    };
    toggle.addEventListener("click", toggleShowing);
    if (showOnHover) {
      toggle.addEventListener("mouseenter", () => {
        this.openDropdown(menu);
      });
    }
  }
};

// resources/js/wysiwyg/ui/framework/manager.ts
var EditorUIManager = class {
  constructor() {
    __publicField(this, "dropdowns", new DropDownManager());
    __publicField(this, "modalDefinitionsByKey", {});
    __publicField(this, "activeModalsByKey", {});
    __publicField(this, "decoratorConstructorsByType", {});
    __publicField(this, "decoratorInstancesByNodeKey", {});
    __publicField(this, "context", null);
    __publicField(this, "toolbar", null);
    __publicField(this, "contextToolbarDefinitionsByKey", {});
    __publicField(this, "activeContextToolbars", []);
    __publicField(this, "selectionChangeHandlers", /* @__PURE__ */ new Set());
    __publicField(this, "domEventAbortController", new AbortController());
    __publicField(this, "teardownCallbacks", []);
  }
  setContext(context) {
    this.context = context;
    this.setupEventListeners();
    this.setupEditor(context.editor);
  }
  getContext() {
    if (this.context === null) {
      throw new Error(`Context attempted to be used without being set`);
    }
    return this.context;
  }
  triggerStateUpdateForElement(element) {
    element.updateState({
      selection: null,
      editor: this.getContext().editor
    });
  }
  registerModal(key, modalDefinition) {
    this.modalDefinitionsByKey[key] = modalDefinition;
  }
  createModal(key) {
    const modalDefinition = this.modalDefinitionsByKey[key];
    if (!modalDefinition) {
      throw new Error(`Attempted to show modal of key [${key}] but no modal registered for that key`);
    }
    const modal = new EditorFormModal(modalDefinition, key);
    modal.setContext(this.getContext());
    return modal;
  }
  setModalActive(key, modal) {
    this.activeModalsByKey[key] = modal;
  }
  setModalInactive(key) {
    delete this.activeModalsByKey[key];
  }
  getActiveModal(key) {
    return this.activeModalsByKey[key];
  }
  registerDecoratorType(type, decorator) {
    this.decoratorConstructorsByType[type] = decorator;
  }
  getDecorator(decoratorType, nodeKey) {
    if (this.decoratorInstancesByNodeKey[nodeKey]) {
      return this.decoratorInstancesByNodeKey[nodeKey];
    }
    const decoratorClass = this.decoratorConstructorsByType[decoratorType];
    if (!decoratorClass) {
      throw new Error(`Attempted to use decorator of type [${decoratorType}] but not decorator registered for that type`);
    }
    const decorator = new decoratorClass(nodeKey);
    this.decoratorInstancesByNodeKey[nodeKey] = decorator;
    return decorator;
  }
  getDecoratorByNodeKey(nodeKey) {
    return this.decoratorInstancesByNodeKey[nodeKey] || null;
  }
  setToolbar(toolbar) {
    if (this.toolbar) {
      this.toolbar.teardown();
    }
    this.toolbar = toolbar;
    toolbar.setContext(this.getContext());
    this.getContext().containerDOM.prepend(toolbar.getDOMElement());
  }
  registerContextToolbar(key, definition) {
    this.contextToolbarDefinitionsByKey[key] = definition;
  }
  triggerStateUpdate(update) {
    setLastSelection(update.editor, update.selection);
    this.toolbar?.updateState(update);
    this.updateContextToolbars(update);
    for (const toolbar of this.activeContextToolbars) {
      toolbar.updateState(update);
    }
    this.triggerSelectionChange(update.selection);
  }
  triggerStateRefresh() {
    const editor = this.getContext().editor;
    const update = {
      editor,
      selection: getLastSelection2(editor)
    };
    this.triggerStateUpdate(update);
    this.updateContextToolbars(update);
  }
  triggerFutureStateRefresh() {
    requestAnimationFrame(() => {
      this.getContext().editor.getEditorState().read(() => {
        this.triggerStateRefresh();
      });
    });
  }
  triggerSelectionChange(selection) {
    if (!selection) {
      return;
    }
    for (const handler of this.selectionChangeHandlers) {
      handler(selection);
    }
  }
  onSelectionChange(handler) {
    this.selectionChangeHandlers.add(handler);
  }
  offSelectionChange(handler) {
    this.selectionChangeHandlers.delete(handler);
  }
  triggerLayoutUpdate() {
    window.requestAnimationFrame(() => {
      for (const toolbar of this.activeContextToolbars) {
        toolbar.updatePosition();
      }
    });
  }
  getDefaultDirection() {
    return this.getContext().options.textDirection === "rtl" ? "rtl" : "ltr";
  }
  onTeardown(callback) {
    this.teardownCallbacks.push(callback);
  }
  teardown() {
    this.domEventAbortController.abort("teardown");
    for (const [_, modal] of Object.entries(this.activeModalsByKey)) {
      modal.teardown();
    }
    for (const [_, decorator] of Object.entries(this.decoratorInstancesByNodeKey)) {
      decorator.teardown();
    }
    if (this.toolbar) {
      this.toolbar.teardown();
    }
    for (const toolbar of this.activeContextToolbars) {
      toolbar.teardown();
    }
    this.dropdowns.teardown();
    for (const callback of this.teardownCallbacks) {
      callback();
    }
  }
  updateContextToolbars(update) {
    for (let i = this.activeContextToolbars.length - 1; i >= 0; i--) {
      const toolbar = this.activeContextToolbars[i];
      toolbar.teardown();
      this.activeContextToolbars.splice(i, 1);
    }
    const node = (update.selection?.getNodes() || [])[0] || null;
    if (!node) {
      return;
    }
    const element = update.editor.getElementByKey(node.getKey());
    if (!element) {
      return;
    }
    const toolbarKeys = Object.keys(this.contextToolbarDefinitionsByKey);
    const contentByTarget = /* @__PURE__ */ new Map();
    for (const key of toolbarKeys) {
      const definition = this.contextToolbarDefinitionsByKey[key];
      const matchingElem = element.closest(definition.selector) || element.querySelector(definition.selector);
      if (matchingElem) {
        const targetEl = definition.displayTargetLocator ? definition.displayTargetLocator(matchingElem) : matchingElem;
        if (!contentByTarget.has(targetEl)) {
          contentByTarget.set(targetEl, []);
        }
        contentByTarget.get(targetEl).push(...definition.content());
      }
    }
    for (const [target, contents] of contentByTarget) {
      const toolbar = new EditorContextToolbar(target, contents);
      toolbar.setContext(this.getContext());
      this.activeContextToolbars.push(toolbar);
      this.getContext().containerDOM.append(toolbar.getDOMElement());
      toolbar.updatePosition();
    }
  }
  setupEditor(editor) {
    const domDecorateListener = (decorators) => {
      editor.getEditorState().read(() => {
        const keys = Object.keys(decorators);
        for (const key of keys) {
          const decoratedEl = editor.getElementByKey(key);
          if (!decoratedEl) {
            continue;
          }
          const adapter = decorators[key];
          const decorator = this.getDecorator(adapter.type, key);
          decorator.setNode(adapter.getNode());
          const decoratorEl = decorator.render(this.getContext(), decoratedEl);
          if (decoratorEl) {
            decoratedEl.append(decoratorEl);
          }
        }
      });
    };
    editor.registerDecoratorListener(domDecorateListener);
    editor.registerUpdateListener(({ editorState, prevEditorState }) => {
      const selectionChange = !(prevEditorState._selection?.is(editorState._selection) || false);
      if (selectionChange) {
        editor.update(() => {
          const selection = $getSelection();
          this.triggerStateUpdate({
            editor,
            selection
          });
        });
      }
    });
  }
  setupEventListeners() {
    const layoutUpdate = this.triggerLayoutUpdate.bind(this);
    window.addEventListener("scroll", layoutUpdate, { capture: true, passive: true, signal: this.domEventAbortController.signal });
    window.addEventListener("resize", layoutUpdate, { passive: true, signal: this.domEventAbortController.signal });
  }
};

// resources/js/wysiwyg/ui/index.ts
function buildEditorUI(containerDOM, editor, options) {
  const editorDOM = el("div", {
    contenteditable: "true",
    class: `editor-content-area ${options.editorClass || ""}`
  });
  const scrollDOM = el("div", {
    class: "editor-content-wrap"
  }, [editorDOM]);
  containerDOM.append(scrollDOM);
  containerDOM.classList.add("editor-container");
  containerDOM.setAttribute("dir", options.textDirection);
  if (options.darkMode) {
    containerDOM.classList.add("editor-dark");
  }
  const manager = new EditorUIManager();
  const context = {
    editor,
    containerDOM,
    editorDOM,
    scrollDOM,
    manager,
    translate(text) {
      const translations = options.translations;
      return translations[text] || text;
    },
    error(error) {
      const message = error instanceof Error ? error.message : error;
      window.$events.error(message);
    },
    options
  };
  manager.setContext(context);
  return context;
}

// resources/js/wysiwyg/utils/actions.ts
function setEditorContentFromHtml(editor, html) {
  editor.update(() => {
    const root = $getRoot();
    for (const child of root.getChildren()) {
      child.remove(true);
    }
    const nodes = $htmlToBlockNodes(editor, html);
    root.append(...nodes);
  });
}
function appendHtmlToEditor(editor, html) {
  editor.update(() => {
    const root = $getRoot();
    const nodes = $htmlToBlockNodes(editor, html);
    root.append(...nodes);
  });
}
function prependHtmlToEditor(editor, html) {
  editor.update(() => {
    const root = $getRoot();
    const nodes = $htmlToBlockNodes(editor, html);
    let reference = root.getChildren()[0];
    for (let i = nodes.length - 1; i >= 0; i--) {
      if (reference) {
        reference.insertBefore(nodes[i]);
      } else {
        root.append(nodes[i]);
      }
      reference = nodes[i];
    }
  });
}
function insertHtmlIntoEditor(editor, html) {
  editor.update(() => {
    const selection = $getSelection();
    const nodes = $htmlToBlockNodes(editor, html);
    const reference = selection?.getNodes()[0];
    const referencesParents = reference?.getParents() || [];
    const topLevel = referencesParents[referencesParents.length - 1];
    if (topLevel && reference) {
      for (let i = nodes.length - 1; i >= 0; i--) {
        reference.insertAfter(nodes[i]);
      }
    }
  });
}
function getEditorContentAsHtml(editor) {
  return new Promise((resolve, reject) => {
    editor.getEditorState().read(() => {
      const html = $generateHtmlFromNodes(editor);
      resolve(html);
    });
  });
}
function focusEditor(editor) {
  editor.focus(() => {
  }, { defaultSelection: "rootStart" });
}

// resources/js/wysiwyg/ui/framework/helpers/mouse-drag-tracker.ts
var MouseDragTracker = class {
  constructor(container, dragTargetSelector, options) {
    __publicField(this, "container");
    __publicField(this, "dragTargetSelector");
    __publicField(this, "options");
    __publicField(this, "startX", 0);
    __publicField(this, "startY", 0);
    __publicField(this, "target", null);
    this.container = container;
    this.dragTargetSelector = dragTargetSelector;
    this.options = options;
    this.onMouseDown = this.onMouseDown.bind(this);
    this.onMouseMove = this.onMouseMove.bind(this);
    this.onMouseUp = this.onMouseUp.bind(this);
    this.container.addEventListener("mousedown", this.onMouseDown);
  }
  teardown() {
    this.container.removeEventListener("mousedown", this.onMouseDown);
    this.container.removeEventListener("mouseup", this.onMouseUp);
    this.container.removeEventListener("mousemove", this.onMouseMove);
  }
  onMouseDown(event) {
    this.target = event.target.closest(this.dragTargetSelector);
    if (!this.target) {
      return;
    }
    this.startX = event.screenX;
    this.startY = event.screenY;
    window.addEventListener("mousemove", this.onMouseMove);
    window.addEventListener("mouseup", this.onMouseUp);
    if (this.options.down) {
      this.options.down(event, this.target);
    }
  }
  onMouseMove(event) {
    if (this.options.move && this.target) {
      this.options.move(event, this.target, {
        x: event.screenX - this.startX,
        y: event.screenY - this.startY
      });
    }
  }
  onMouseUp(event) {
    window.removeEventListener("mousemove", this.onMouseMove);
    window.removeEventListener("mouseup", this.onMouseUp);
    if (this.options.up && this.target) {
      this.options.up(event, this.target, {
        x: event.screenX - this.startX,
        y: event.screenY - this.startY
      });
    }
  }
};

// resources/js/wysiwyg/ui/framework/helpers/table-resizer.ts
var TableResizer = class {
  constructor(editor, editScrollContainer) {
    __publicField(this, "editor");
    __publicField(this, "editScrollContainer");
    __publicField(this, "markerDom", null);
    __publicField(this, "mouseTracker", null);
    __publicField(this, "dragging", false);
    __publicField(this, "targetCell", null);
    __publicField(this, "xMarkerAtStart", false);
    __publicField(this, "yMarkerAtStart", false);
    __publicField(this, "activeInTable", false);
    this.editor = editor;
    this.editScrollContainer = editScrollContainer;
    this.setupListeners();
  }
  teardown() {
    this.editScrollContainer.removeEventListener("mousemove", this.onCellMouseMove);
    window.removeEventListener("scroll", this.onScrollOrResize, { capture: true });
    window.removeEventListener("resize", this.onScrollOrResize);
    if (this.mouseTracker) {
      this.mouseTracker.teardown();
    }
  }
  setupListeners() {
    this.onTableMouseOver = this.onTableMouseOver.bind(this);
    this.onCellMouseMove = this.onCellMouseMove.bind(this);
    this.onScrollOrResize = this.onScrollOrResize.bind(this);
    this.editScrollContainer.addEventListener("mouseover", this.onTableMouseOver, { passive: true });
    window.addEventListener("scroll", this.onScrollOrResize, { capture: true, passive: true });
    window.addEventListener("resize", this.onScrollOrResize, { passive: true });
  }
  onScrollOrResize() {
    this.updateCurrentMarkerTargetPosition();
  }
  onTableMouseOver(event) {
    if (this.dragging) {
      return;
    }
    const table2 = event.target.closest("table");
    if (table2 && !this.activeInTable) {
      this.editScrollContainer.addEventListener("mousemove", this.onCellMouseMove, { passive: true });
      this.onCellMouseMove(event);
      this.activeInTable = true;
    } else if (!table2 && this.activeInTable) {
      this.editScrollContainer.removeEventListener("mousemove", this.onCellMouseMove);
      this.hideMarkers();
      this.activeInTable = false;
    }
  }
  onCellMouseMove(event) {
    const cell = event.target.closest("td,th");
    if (!cell || this.dragging) {
      return;
    }
    const rect = cell.getBoundingClientRect();
    const midX = rect.left + rect.width / 2;
    const midY = rect.top + rect.height / 2;
    this.targetCell = cell;
    this.xMarkerAtStart = event.clientX <= midX;
    this.yMarkerAtStart = event.clientY <= midY;
    const xMarkerPos = this.xMarkerAtStart ? rect.left : rect.right;
    const yMarkerPos = this.yMarkerAtStart ? rect.top : rect.bottom;
    this.updateMarkersTo(cell, xMarkerPos, yMarkerPos);
  }
  updateMarkersTo(cell, xPos, yPos) {
    const markers = this.getMarkers();
    const table2 = cell.closest("table");
    const caption = table2.querySelector("caption");
    const tableRect = table2.getBoundingClientRect();
    const editBounds = this.editScrollContainer.getBoundingClientRect();
    let tableTop = tableRect.top;
    if (caption) {
      tableTop = caption.getBoundingClientRect().bottom;
    }
    const maxTop = Math.max(tableTop, editBounds.top);
    const maxBottom = Math.min(tableRect.bottom, editBounds.bottom);
    const maxHeight = maxBottom - maxTop;
    markers.x.style.left = xPos + "px";
    markers.x.style.top = maxTop + "px";
    markers.x.style.height = maxHeight + "px";
    markers.y.style.top = yPos + "px";
    markers.y.style.left = tableRect.left + "px";
    markers.y.style.width = tableRect.width + "px";
    markers.y.hidden = yPos < editBounds.top || yPos > editBounds.bottom;
    markers.x.hidden = tableRect.top > editBounds.bottom || tableRect.bottom < editBounds.top;
  }
  hideMarkers() {
    if (this.markerDom) {
      this.markerDom.x.hidden = true;
      this.markerDom.y.hidden = true;
    }
  }
  updateCurrentMarkerTargetPosition() {
    if (!this.targetCell) {
      return;
    }
    const rect = this.targetCell.getBoundingClientRect();
    const xMarkerPos = this.xMarkerAtStart ? rect.left : rect.right;
    const yMarkerPos = this.yMarkerAtStart ? rect.top : rect.bottom;
    this.updateMarkersTo(this.targetCell, xMarkerPos, yMarkerPos);
  }
  getMarkers() {
    if (!this.markerDom) {
      this.markerDom = {
        x: el("div", { class: "editor-table-marker editor-table-marker-column" }),
        y: el("div", { class: "editor-table-marker editor-table-marker-row" })
      };
      const wrapper = el("div", {
        class: "editor-table-marker-wrap"
      }, [this.markerDom.x, this.markerDom.y]);
      this.editScrollContainer.after(wrapper);
      this.watchMarkerMouseDrags(wrapper);
    }
    return this.markerDom;
  }
  watchMarkerMouseDrags(wrapper) {
    const _this = this;
    let markerStart = 0;
    let markerProp = "left";
    this.mouseTracker = new MouseDragTracker(wrapper, ".editor-table-marker", {
      down(event, marker) {
        marker.classList.add("active");
        _this.dragging = true;
        markerProp = marker.classList.contains("editor-table-marker-column") ? "left" : "top";
        markerStart = Number(marker.style[markerProp].replace("px", ""));
      },
      move(event, marker, distance) {
        marker.style[markerProp] = markerStart + distance[markerProp === "left" ? "x" : "y"] + "px";
      },
      up(event, marker, distance) {
        marker.classList.remove("active");
        marker.style.left = "0";
        marker.style.top = "0";
        _this.dragging = false;
        const parentTable = _this.targetCell?.closest("table");
        if (markerProp === "left" && _this.targetCell && parentTable) {
          let cellIndex = _this.getTargetCellColumnIndex();
          let change = distance.x;
          if (_this.xMarkerAtStart && cellIndex > 0) {
            cellIndex -= 1;
          } else if (_this.xMarkerAtStart && cellIndex === 0) {
            change = -change;
          }
          _this.editor.update(() => {
            const table2 = $getNearestNodeFromDOMNode(parentTable);
            if (table2 instanceof TableNode4) {
              const originalWidth = $getTableColumnWidth(_this.editor, table2, cellIndex);
              const newWidth = Math.max(originalWidth + change, 10);
              $setTableColumnWidth(table2, cellIndex, newWidth);
            }
          });
        }
        if (markerProp === "top" && _this.targetCell) {
          const cellElement = _this.targetCell;
          _this.editor.update(() => {
            const cellNode = $getNearestNodeFromDOMNode(cellElement);
            const rowNode = cellNode?.getParent();
            let rowIndex = rowNode?.getIndexWithinParent() || 0;
            let change = distance.y;
            if (_this.yMarkerAtStart && rowIndex > 0) {
              rowIndex -= 1;
            } else if (_this.yMarkerAtStart && rowIndex === 0) {
              change = -change;
            }
            const targetRow = rowNode?.getParent()?.getChildren()[rowIndex];
            if (targetRow instanceof TableRowNode) {
              const height = targetRow.getHeight() || 0;
              const newHeight = Math.max(height + change, 10);
              targetRow.setHeight(newHeight);
            }
          });
        }
      }
    });
  }
  getTargetCellColumnIndex() {
    const cell = this.targetCell;
    if (cell === null) {
      return -1;
    }
    let index = 0;
    const row = cell.parentElement;
    for (const rowCell of row?.children || []) {
      let size = Number(rowCell.getAttribute("colspan"));
      if (Number.isNaN(size) || size < 1) {
        size = 1;
      }
      index += size;
      if (rowCell === cell) {
        return index - 1;
      }
    }
    return -1;
  }
};
function registerTableResizer(editor, editScrollContainer) {
  const resizer = new TableResizer(editor, editScrollContainer);
  return () => {
    resizer.teardown();
  };
}

// resources/js/wysiwyg/services/common-events.ts
function getContentToInsert(eventContent) {
  return eventContent.html || "";
}
function listen(editor) {
  window.$events.listen("editor::replace", (eventContent) => {
    const html = getContentToInsert(eventContent);
    setEditorContentFromHtml(editor, html);
  });
  window.$events.listen("editor::append", (eventContent) => {
    const html = getContentToInsert(eventContent);
    appendHtmlToEditor(editor, html);
  });
  window.$events.listen("editor::prepend", (eventContent) => {
    const html = getContentToInsert(eventContent);
    prependHtmlToEditor(editor, html);
  });
  window.$events.listen("editor::insert", (eventContent) => {
    const html = getContentToInsert(eventContent);
    insertHtmlIntoEditor(editor, html);
  });
  window.$events.listen("editor::focus", () => {
    focusEditor(editor);
  });
  let changeFromLoading = true;
  editor.registerUpdateListener(({ dirtyElements, dirtyLeaves, editorState, prevEditorState }) => {
    if (dirtyElements.size > 0 || dirtyLeaves.size > 0) {
      if (changeFromLoading) {
        changeFromLoading = false;
      } else {
        window.$events.emit("editor-html-change", "");
      }
    }
  });
}

// resources/js/services/clipboard.ts
var Clipboard = class {
  constructor(clipboardData) {
    __publicField(this, "data");
    this.data = clipboardData;
  }
  /**
   * Check if the clipboard has any items.
   */
  hasItems() {
    return Boolean(this.data) && Boolean(this.data.types) && this.data.types.length > 0;
  }
  /**
   * Check if the given event has tabular-looking data in the clipboard.
   */
  containsTabularData() {
    const rtfData = this.data.getData("text/rtf");
    return !!rtfData && rtfData.includes("\\trowd");
  }
  /**
   * Get the images that are in the clipboard data.
   */
  getImages() {
    return this.getFiles().filter((f) => f.type.includes("image"));
  }
  /**
   * Get the files included in the clipboard data.
   */
  getFiles() {
    const { files } = this.data;
    return [...files];
  }
};

// resources/js/wysiwyg/utils/images.ts
function showImageManager(callback) {
  const imageManager = window.$components.first("image-manager");
  imageManager.show((image3) => {
    callback(image3);
  }, "gallery");
}
function $createLinkedImageNodeFromImageData(image3) {
  const url = image3.thumbs?.display || image3.url;
  const linkNode = $createLinkNode(url, { target: "_blank" });
  const imageNode = $createImageNode(url, {
    alt: image3.name
  });
  linkNode.append(imageNode);
  return linkNode;
}
async function uploadImageFile(file, pageId) {
  if (file === null || file.type.indexOf("image") !== 0) {
    throw new Error("Not an image file");
  }
  const remoteFilename = file.name || `image-${Date.now()}.png`;
  const formData = new FormData();
  formData.append("file", file, remoteFilename);
  formData.append("uploaded_to", pageId);
  const resp = await window.$http.post("/images/gallery", formData);
  return resp.data;
}

// resources/js/wysiwyg/services/drop-paste-handling.ts
function $getNodeFromMouseEvent(event, editor) {
  const x = event.clientX;
  const y = event.clientY;
  const dom = document.elementFromPoint(x, y);
  if (!dom) {
    return null;
  }
  return $getNearestBlockNodeForCoords(editor, event.clientX, event.clientY);
}
function $insertNodesAtEvent(nodes, event, editor) {
  const positionNode = $getNodeFromMouseEvent(event, editor);
  if (positionNode) {
    $selectSingleNode(positionNode);
  }
  $insertNewBlockNodesAtSelection(nodes, true);
  if (!$isDecoratorNode(positionNode) || !positionNode?.getTextContent()) {
    positionNode?.remove();
  }
}
async function insertTemplateToEditor(editor, templateId, event) {
  const resp = await window.$http.get(`/templates/${templateId}`);
  const data = resp.data || { html: "" };
  const html = data.html || "";
  editor.update(() => {
    const newNodes = $htmlToBlockNodes(editor, html);
    $insertNodesAtEvent(newNodes, event, editor);
  });
}
function handleMediaInsert(data, context) {
  const clipboard = new Clipboard(data);
  let handled = false;
  if (!clipboard.hasItems() || clipboard.containsTabularData()) {
    return handled;
  }
  const images = clipboard.getImages();
  if (images.length > 0) {
    handled = true;
  }
  context.editor.update(async () => {
    for (const imageFile of images) {
      const loadingImage = window.baseUrl("/loading.gif");
      const loadingNode = $createImageNode(loadingImage);
      const imageWrap = $createParagraphNode();
      imageWrap.append(loadingNode);
      $insertNodes([imageWrap]);
      try {
        const respData = await uploadImageFile(imageFile, context.options.pageId);
        const safeName = respData.name.replace(/"/g, "");
        context.editor.update(() => {
          const finalImage = $createImageNode(respData.thumbs?.display || "", {
            alt: safeName
          });
          const imageLink = $createLinkNode(respData.url, { target: "_blank" });
          imageLink.append(finalImage);
          loadingNode.replace(imageLink);
        });
      } catch (err) {
        context.editor.update(() => {
          loadingNode.remove(false);
        });
        window.$events.error(err?.data?.message || context.options.translations.imageUploadErrorText);
        console.error(err);
      }
    }
  });
  return handled;
}
function handleImageLinkInsert(data, context) {
  const regex = /https?:\/\/([^?#]*?)\.(png|jpeg|jpg|gif|webp|bmp|avif)/i;
  const text = data.getData("text/plain");
  if (text && regex.test(text)) {
    context.editor.update(() => {
      const image3 = $createImageNode(text);
      $insertNodes([image3]);
      image3.select();
    });
    return true;
  }
  return false;
}
function createDropListener(context) {
  const editor = context.editor;
  return (event) => {
    const templateId = event.dataTransfer?.getData("bookstack/template") || "";
    if (templateId) {
      insertTemplateToEditor(editor, templateId, event);
      event.preventDefault();
      event.stopPropagation();
      return true;
    }
    const html = event.dataTransfer?.getData("text/html") || "";
    if (html) {
      editor.update(() => {
        const newNodes = $htmlToBlockNodes(editor, html);
        $insertNodesAtEvent(newNodes, event, editor);
      });
      event.preventDefault();
      event.stopPropagation();
      return true;
    }
    if (event.dataTransfer) {
      const handled = handleMediaInsert(event.dataTransfer, context);
      if (handled) {
        event.preventDefault();
        event.stopPropagation();
        return true;
      }
    }
    return false;
  };
}
function createPasteListener(context) {
  return (event) => {
    if (!event.clipboardData) {
      return false;
    }
    const handled = handleImageLinkInsert(event.clipboardData, context) || handleMediaInsert(event.clipboardData, context);
    if (handled) {
      event.preventDefault();
    }
    return handled;
  };
}
function registerDropPasteHandling(context) {
  const dropListener = createDropListener(context);
  const pasteListener = createPasteListener(context);
  const unregisterDrop = context.editor.registerCommand(DROP_COMMAND, dropListener, COMMAND_PRIORITY_HIGH);
  const unregisterPaste = context.editor.registerCommand(PASTE_COMMAND, pasteListener, COMMAND_PRIORITY_HIGH);
  context.scrollDOM.addEventListener("drop", dropListener);
  return () => {
    unregisterDrop();
    unregisterPaste();
    context.scrollDOM.removeEventListener("drop", dropListener);
  };
}

// resources/js/wysiwyg/ui/framework/helpers/task-list-handler.ts
var TaskListHandler = class {
  constructor(editor, editorContainer) {
    __publicField(this, "editorContainer");
    __publicField(this, "editor");
    this.editor = editor;
    this.editorContainer = editorContainer;
    this.setupListeners();
  }
  setupListeners() {
    this.handleClick = this.handleClick.bind(this);
    this.editorContainer.addEventListener("click", this.handleClick);
  }
  handleClick(event) {
    const target = event.target;
    if (target instanceof HTMLElement && target.nodeName === "LI" && target.classList.contains("task-list-item")) {
      this.handleTaskListItemClick(target, event);
      event.preventDefault();
    }
  }
  handleTaskListItemClick(listItem, event) {
    const bounds = listItem.getBoundingClientRect();
    const withinBounds = event.clientX <= bounds.right && event.clientX >= bounds.left && event.clientY >= bounds.top && event.clientY <= bounds.bottom;
    if (withinBounds) {
      return;
    }
    this.editor.update(() => {
      const node = $getNearestNodeFromDOMNode(listItem);
      if ($isListItemNode(node)) {
        node.setChecked(!node.getChecked());
      }
    });
  }
  teardown() {
    this.editorContainer.removeEventListener("click", this.handleClick);
  }
};
function registerTaskListHandler(editor, editorContainer) {
  const handler = new TaskListHandler(editor, editorContainer);
  return () => {
    handler.teardown();
  };
}

// resources/js/wysiwyg/ui/framework/helpers/table-selection-handler.ts
var TableSelectionHandler = class {
  constructor(editor) {
    __publicField(this, "editor");
    __publicField(this, "tableSelections", /* @__PURE__ */ new Map());
    __publicField(this, "unregisterMutationListener", () => {
    });
    this.editor = editor;
    this.init();
  }
  init() {
    this.unregisterMutationListener = this.editor.registerMutationListener(TableNode4, (mutations) => {
      for (const [nodeKey, mutation] of mutations) {
        if (mutation === "created") {
          this.editor.getEditorState().read(() => {
            const tableNode = $getNodeByKey(nodeKey);
            if ($isTableNode(tableNode)) {
              this.initializeTableNode(tableNode);
            }
          });
        } else if (mutation === "destroyed") {
          const tableSelection = this.tableSelections.get(nodeKey);
          if (tableSelection !== void 0) {
            tableSelection.removeListeners();
            this.tableSelections.delete(nodeKey);
          }
        }
      }
    });
  }
  initializeTableNode(tableNode) {
    const nodeKey = tableNode.getKey();
    const tableElement = this.editor.getElementByKey(
      nodeKey
    );
    if (tableElement && !this.tableSelections.has(nodeKey)) {
      const tableSelection = applyTableHandlers(
        tableNode,
        tableElement,
        this.editor,
        true
      );
      this.tableSelections.set(nodeKey, tableSelection);
    }
  }
  teardown() {
    this.unregisterMutationListener();
    for (const [, tableSelection] of this.tableSelections) {
      tableSelection.removeListeners();
    }
  }
};
function registerTableSelectionHandler(editor) {
  const resizer = new TableSelectionHandler(editor);
  return () => {
    resizer.teardown();
  };
}

// resources/js/wysiwyg/utils/formats.ts
var $isHeaderNodeOfTag = (node, tag) => {
  return $isHeadingNode(node) && node.getTag() === tag;
};
function toggleSelectionAsHeading(editor, tag) {
  editor.update(() => {
    $toggleSelectionBlockNodeType(
      (node) => $isHeaderNodeOfTag(node, tag),
      () => $createHeadingNode(tag)
    );
  });
}
function toggleSelectionAsParagraph(editor) {
  editor.update(() => {
    $toggleSelectionBlockNodeType($isParagraphNode, $createParagraphNode);
  });
}
function toggleSelectionAsBlockquote(editor) {
  editor.update(() => {
    $toggleSelectionBlockNodeType($isQuoteNode, $createQuoteNode);
  });
}
function toggleSelectionAsList(editor, type) {
  editor.getEditorState().read(() => {
    const selection = $getSelection();
    const listSelected = $selectionContainsNodeType(selection, (node) => {
      return $isListNode(node) && node.getListType() === type;
    });
    if (listSelected) {
      removeList(editor);
    } else {
      insertList(editor, type);
    }
  });
}
function formatCodeBlock(editor) {
  editor.getEditorState().read(() => {
    const selection = $getSelection();
    const lastSelection = getLastSelection2(editor);
    const codeBlock2 = $getNodeFromSelection(lastSelection, $isCodeBlockNode);
    if (codeBlock2 === null) {
      editor.update(() => {
        const codeBlock3 = $createCodeBlockNode();
        codeBlock3.setCode(selection?.getTextContent() || "");
        const selectionNodes = $getBlockElementNodesInSelection(selection);
        const firstSelectionNode = selectionNodes[0];
        const extraNodes = selectionNodes.slice(1);
        if (firstSelectionNode) {
          firstSelectionNode.replace(codeBlock3);
          extraNodes.forEach((n) => n.remove());
        } else {
          $insertNewBlockNodeAtSelection(codeBlock3, true);
        }
        $openCodeEditorForNode(editor, codeBlock3);
        $selectSingleNode(codeBlock3);
      });
    } else {
      $openCodeEditorForNode(editor, codeBlock2);
    }
  });
}
function cycleSelectionCalloutFormats(editor) {
  editor.update(() => {
    const selection = $getSelection();
    const blocks = $getBlockElementNodesInSelection(selection);
    let created = false;
    for (const block of blocks) {
      if (!$isCalloutNode(block)) {
        block.replace($createCalloutNode("info"), true);
        created = true;
      }
    }
    if (created) {
      return;
    }
    const types = ["info", "warning", "danger", "success"];
    for (const block of blocks) {
      if ($isCalloutNode(block)) {
        const type = block.getCategory();
        const typeIndex = types.indexOf(type);
        const newIndex = (typeIndex + 1) % types.length;
        const newType = types[newIndex];
        block.setCategory(newType);
      }
    }
  });
}
function insertOrUpdateLink(editor, linkDetails) {
  editor.update(() => {
    const selection = $getSelection();
    let link3 = $getNodeFromSelection(selection, $isLinkNode);
    if ($isLinkNode(link3)) {
      link3.setURL(linkDetails.url);
      link3.setTarget(linkDetails.target);
      link3.setTitle(linkDetails.title);
    } else {
      link3 = $createLinkNode(linkDetails.url, {
        title: linkDetails.title,
        target: linkDetails.target
      });
      $insertNodes([link3]);
    }
    if ($isLinkNode(link3)) {
      for (const child of link3.getChildren()) {
        child.remove(true);
      }
      link3.append($createTextNode(linkDetails.text));
    }
  });
}

// resources/js/wysiwyg/ui/framework/blocks/action-field.ts
var EditorActionField = class extends EditorContainerUiElement {
  constructor(input, action) {
    super([input, action]);
    __publicField(this, "input");
    __publicField(this, "action");
    this.input = input;
    this.action = action;
  }
  buildDOM() {
    return el("div", {
      class: "editor-action-input-container"
    }, [
      this.input.getDOMElement(),
      this.action.getDOMElement()
    ]);
  }
};

// resources/js/wysiwyg/ui/framework/buttons.ts
var EditorButton = class extends EditorUiElement {
  constructor(definition) {
    super();
    __publicField(this, "definition");
    __publicField(this, "active", false);
    __publicField(this, "completedSetup", false);
    __publicField(this, "disabled", false);
    if (definition.action !== void 0) {
      this.definition = definition;
    } else {
      this.definition = {
        ...definition,
        action() {
          return false;
        },
        isActive: () => {
          return false;
        }
      };
    }
  }
  setContext(context) {
    super.setContext(context);
    if (this.definition.setup && !this.completedSetup) {
      this.definition.setup(context, this);
      this.completedSetup = true;
    }
  }
  buildDOM() {
    const label = this.getLabel();
    const format = this.definition.format || "small";
    const children = [];
    if (this.definition.icon || format === "long") {
      const icon = el("div", { class: "editor-button-icon" });
      icon.innerHTML = this.definition.icon || "";
      children.push(icon);
    }
    if (!this.definition.icon || format === "long") {
      const text = el("div", { class: "editor-button-text" }, [label]);
      children.push(text);
    }
    const button = el("button", {
      type: "button",
      class: `editor-button editor-button-${format}`,
      title: this.definition.icon ? label : null,
      disabled: this.disabled ? "true" : null
    }, children);
    button.addEventListener("click", this.onClick.bind(this));
    return button;
  }
  onClick() {
    const result = this.definition.action(this.getContext(), this);
    if (result instanceof Promise) {
      result.then((result2) => {
        if (result2 === false) {
          this.emitEvent("button-action");
        }
      });
    } else if (result !== false) {
      this.emitEvent("button-action");
    }
  }
  updateActiveState(selection) {
    const isActive = this.definition.isActive(selection, this.getContext());
    this.setActiveState(isActive);
  }
  updateDisabledState(selection) {
    if (this.definition.isDisabled) {
      const isDisabled = this.definition.isDisabled(selection, this.getContext());
      this.toggleDisabled(isDisabled);
    }
  }
  setActiveState(active) {
    this.active = active;
    this.dom?.classList.toggle("editor-button-active", this.active);
  }
  updateState(state) {
    this.updateActiveState(state.selection);
    this.updateDisabledState(state.selection);
  }
  isActive() {
    return this.active;
  }
  getLabel() {
    return this.trans(this.definition.label);
  }
  toggleDisabled(disabled) {
    this.disabled = disabled;
    if (disabled) {
      this.dom?.setAttribute("disabled", "true");
    } else {
      this.dom?.removeAttribute("disabled");
    }
  }
};

// resources/icons/editor/image-search.svg
var image_search_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h200v80H200v560h560v-214l80 80v134q0 33-23.5 56.5T760-120H200Zm40-160 120-160 90 120 120-160 150 200H240Zm622-144L738-548q-21 14-45 21t-51 7q-74 0-126-52.5T464-700q0-75 52.5-127.5T644-880q75 0 127.5 52.5T824-700q0 27-8 52t-20 46l122 122-56 56ZM644-600q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Z"/></svg>';

// resources/icons/search.svg
var search_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14"/><path fill="none" d="M0 0h24v24H0z"/></svg>';

// resources/js/wysiwyg/utils/links.ts
function showLinkSelector(callback, selectionText) {
  const selector = window.$components.first("entity-selector-popup");
  selector.show((entity) => callback(entity), {
    initialValue: selectionText,
    searchEndpoint: "/search/entity-selector",
    entityTypes: "page,book,chapter,bookshelf",
    entityPermission: "view"
  });
}

// resources/js/wysiwyg/ui/framework/blocks/link-field.ts
var LinkField = class extends EditorContainerUiElement {
  constructor(input) {
    super([input]);
    __publicField(this, "input");
    __publicField(this, "headerMap", /* @__PURE__ */ new Map());
    this.input = input;
  }
  buildDOM() {
    const listId = "editor-form-datalist-" + this.input.getName() + "-" + Date.now();
    const inputOuterDOM = this.input.getDOMElement();
    const inputFieldDOM = inputOuterDOM.querySelector("input");
    inputFieldDOM?.setAttribute("list", listId);
    inputFieldDOM?.setAttribute("autocomplete", "off");
    const datalist = el("datalist", { id: listId });
    const container = el("div", {
      class: "editor-link-field-container"
    }, [inputOuterDOM, datalist]);
    inputFieldDOM?.addEventListener("focusin", () => {
      this.updateDataList(datalist);
    });
    inputFieldDOM?.addEventListener("input", () => {
      const value = inputFieldDOM.value;
      const header = this.headerMap.get(value);
      if (header) {
        this.updateFormFromHeader(header);
      }
    });
    return container;
  }
  updateFormFromHeader(header) {
    this.getHeaderIdAndText(header).then(({ id, text }) => {
      const modal = this.getContext().manager.getActiveModal("link");
      if (modal) {
        modal.getForm().setValues({
          url: `#${id}`,
          text,
          title: text
        });
      }
    });
  }
  getHeaderIdAndText(header) {
    return new Promise((res) => {
      this.getContext().editor.update(() => {
        let id = header.getId();
        if (!id) {
          id = "header-" + uniqueIdSmall();
          header.setId(id);
        }
        const text = header.getTextContent();
        res({ id, text });
      });
    });
  }
  updateDataList(listEl) {
    this.getContext().editor.getEditorState().read(() => {
      const headers = $getAllNodesOfType($isHeadingNode);
      this.headerMap.clear();
      const listEls = [];
      for (const header of headers) {
        const key = "header-" + header.getKey();
        this.headerMap.set(key, header);
        listEls.push(el("option", {
          value: key,
          label: header.getTextContent().substring(0, 54)
        }));
      }
      listEl.innerHTML = "";
      listEl.append(...listEls);
    });
  }
};

// resources/js/wysiwyg/ui/defaults/forms/objects.ts
function $showImageForm(image3, context) {
  const imageModal = context.manager.createModal("image");
  const height = image3.getHeight();
  const width = image3.getWidth();
  const formData = {
    src: image3.getSrc(),
    alt: image3.getAltText(),
    height: height === 0 ? "" : String(height),
    width: width === 0 ? "" : String(width)
  };
  imageModal.show(formData);
}
var image = {
  submitText: "Apply",
  async action(formData, context) {
    context.editor.update(() => {
      const selection = getLastSelection2(context.editor);
      const selectedImage = $getNodeFromSelection(selection, $isImageNode);
      if ($isImageNode(selectedImage)) {
        selectedImage.setSrc(formData.get("src")?.toString() || "");
        selectedImage.setAltText(formData.get("alt")?.toString() || "");
        selectedImage.setWidth(Number(formData.get("width")?.toString() || "0"));
        selectedImage.setHeight(Number(formData.get("height")?.toString() || "0"));
      }
    });
    return true;
  },
  fields: [
    {
      build() {
        return new EditorActionField(
          new EditorFormField({
            label: "Source",
            name: "src",
            type: "text"
          }),
          new EditorButton({
            label: "Browse files",
            icon: image_search_default,
            action(context) {
              showImageManager((image3) => {
                const modal = context.manager.getActiveModal("image");
                if (modal) {
                  modal.getForm().setValues({
                    src: image3.thumbs?.display || image3.url,
                    alt: image3.name
                  });
                }
              });
            }
          })
        );
      }
    },
    {
      label: "Alternative description",
      name: "alt",
      type: "text"
    },
    {
      label: "Width",
      name: "width",
      type: "text"
    },
    {
      label: "Height",
      name: "height",
      type: "text"
    }
  ]
};
function $showLinkForm(link3, context) {
  const linkModal = context.manager.createModal("link");
  if (link3) {
    const formDefaults = {
      url: link3.getURL(),
      text: link3.getTextContent(),
      title: link3.getTitle() || "",
      target: link3.getTarget() || ""
    };
    context.editor.update(() => {
      const selection = $createNodeSelection();
      selection.add(link3.getKey());
      $setSelection(selection);
    });
    linkModal.show(formDefaults);
  } else {
    context.editor.getEditorState().read(() => {
      const selection = $getSelection();
      const text = selection?.getTextContent() || "";
      const formDefaults = { text };
      linkModal.show(formDefaults);
    });
  }
}
var link = {
  submitText: "Apply",
  async action(formData, context) {
    insertOrUpdateLink(context.editor, {
      url: formData.get("url")?.toString() || "",
      title: formData.get("title")?.toString() || "",
      target: formData.get("target")?.toString() || "",
      text: formData.get("text")?.toString() || ""
    });
    return true;
  },
  fields: [
    {
      build() {
        return new EditorActionField(
          new LinkField(new EditorFormField({
            label: "URL",
            name: "url",
            type: "text"
          })),
          new EditorButton({
            label: "Browse links",
            icon: search_default,
            action(context) {
              showLinkSelector((entity) => {
                const modal = context.manager.getActiveModal("link");
                if (modal) {
                  modal.getForm().setValues({
                    url: entity.link,
                    text: entity.name,
                    title: entity.name
                  });
                }
              });
            }
          })
        );
      }
    },
    {
      label: "Text to display",
      name: "text",
      type: "text"
    },
    {
      label: "Title",
      name: "title",
      type: "text"
    },
    {
      label: "Open link in...",
      name: "target",
      type: "select",
      valuesByLabel: {
        "Current window": "",
        "New window": "_blank"
      }
    }
  ]
};
function $showMediaForm(media3, context) {
  const mediaModal = context.manager.createModal("media");
  let formDefaults = {};
  if (media3) {
    const nodeAttrs = media3.getAttributes();
    const nodeDOM = media3.exportDOM(context.editor).element;
    const nodeHtml = nodeDOM instanceof HTMLElement ? nodeDOM.outerHTML : "";
    formDefaults = {
      src: nodeAttrs.src || nodeAttrs.data || media3.getSources()[0]?.src || "",
      width: nodeAttrs.width,
      height: nodeAttrs.height,
      embed: nodeHtml,
      // This is used so we can check for edits against the embed field on submit
      embed_check: nodeHtml
    };
  }
  mediaModal.show(formDefaults);
}
var media = {
  submitText: "Save",
  async action(formData, context) {
    const selectedNode = await new Promise((res, rej) => {
      context.editor.getEditorState().read(() => {
        const node = $getNodeFromSelection($getSelection(), $isMediaNode);
        res(node);
      });
    });
    const embedCode = (formData.get("embed") || "").toString().trim();
    const embedCheck = (formData.get("embed_check") || "").toString().trim();
    if (embedCode && embedCode !== embedCheck) {
      context.editor.update(() => {
        const node = $createMediaNodeFromHtml(embedCode);
        if (selectedNode && node) {
          selectedNode.replace(node);
        } else if (node) {
          $insertNodes([node]);
        }
      });
      return true;
    }
    context.editor.update(() => {
      const src = (formData.get("src") || "").toString().trim();
      const height = (formData.get("height") || "").toString().trim();
      const width = (formData.get("width") || "").toString().trim();
      if (selectedNode) {
        selectedNode.setSrc(src);
        selectedNode.setWidthAndHeight(width, height);
        context.manager.triggerFutureStateRefresh();
        return;
      }
      const node = $createMediaNodeFromSrc(src);
      if (width || height) {
        node.setWidthAndHeight(width, height);
      }
      $insertNodes([node]);
    });
    return true;
  },
  fields: [
    {
      build() {
        return new EditorFormTabs([
          {
            label: "General",
            contents: [
              {
                label: "Source",
                name: "src",
                type: "text"
              },
              {
                label: "Width",
                name: "width",
                type: "text"
              },
              {
                label: "Height",
                name: "height",
                type: "text"
              }
            ]
          },
          {
            label: "Embed",
            contents: [
              {
                label: "Paste your embed code below:",
                name: "embed",
                type: "textarea"
              },
              {
                label: "",
                name: "embed_check",
                type: "hidden"
              }
            ]
          }
        ]);
      }
    }
  ]
};
function $showDetailsForm(details3, context) {
  const linkModal = context.manager.createModal("details");
  if (!details3) {
    return;
  }
  linkModal.show({
    summary: details3.getSummary()
  });
}
var details = {
  submitText: "Save",
  async action(formData, context) {
    context.editor.update(() => {
      const node = $getNodeFromSelection($getSelection(), $isDetailsNode);
      const summary = (formData.get("summary") || "").toString().trim();
      if ($isDetailsNode(node)) {
        node.setSummary(summary);
      }
    });
    return true;
  },
  fields: [
    {
      label: "Toggle label",
      name: "summary",
      type: "text"
    }
  ]
};

// resources/js/wysiwyg/services/shortcuts.ts
function headerHandler(context, tag) {
  toggleSelectionAsHeading(context.editor, tag);
  context.manager.triggerFutureStateRefresh();
  return true;
}
function wrapFormatAction(formatAction) {
  return (editor, context) => {
    formatAction(editor);
    context.manager.triggerFutureStateRefresh();
    return true;
  };
}
function toggleInlineCode(editor) {
  editor.dispatchCommand(FORMAT_TEXT_COMMAND, "code");
  return true;
}
var actionsByKeys = {
  "meta+s": () => {
    window.$events.emit("editor-save-draft");
    return true;
  },
  "meta+enter": () => {
    window.$events.emit("editor-save-page");
    return true;
  },
  "meta+1": (editor, context) => headerHandler(context, "h2"),
  "meta+2": (editor, context) => headerHandler(context, "h3"),
  "meta+3": (editor, context) => headerHandler(context, "h4"),
  "meta+4": (editor, context) => headerHandler(context, "h5"),
  "meta+5": wrapFormatAction(toggleSelectionAsParagraph),
  "meta+d": wrapFormatAction(toggleSelectionAsParagraph),
  "meta+6": wrapFormatAction(toggleSelectionAsBlockquote),
  "meta+q": wrapFormatAction(toggleSelectionAsBlockquote),
  "meta+7": wrapFormatAction(formatCodeBlock),
  "meta+e": wrapFormatAction(formatCodeBlock),
  "meta+8": toggleInlineCode,
  "meta+shift+e": toggleInlineCode,
  "meta+9": wrapFormatAction(cycleSelectionCalloutFormats),
  "meta+o": wrapFormatAction((e) => toggleSelectionAsList(e, "number")),
  "meta+p": wrapFormatAction((e) => toggleSelectionAsList(e, "bullet")),
  "meta+k": (editor, context) => {
    editor.getEditorState().read(() => {
      const selectedLink = $getNodeFromSelection($getSelection(), $isLinkNode);
      $showLinkForm(selectedLink, context);
    });
    return true;
  },
  "meta+shift+k": (editor, context) => {
    showLinkSelector((entity) => {
      insertOrUpdateLink(editor, {
        text: entity.name,
        title: entity.link,
        target: "",
        url: entity.link
      });
    });
    return true;
  }
};
function createKeyDownListener(context) {
  return (event) => {
    const combo = keyboardEventToKeyComboString(event);
    if (actionsByKeys[combo]) {
      const handled = actionsByKeys[combo](context.editor, context);
      if (handled) {
        event.stopPropagation();
        event.preventDefault();
      }
    }
  };
}
function keyboardEventToKeyComboString(event) {
  const metaKeyPressed = isMac() ? event.metaKey : event.ctrlKey;
  const parts = [
    metaKeyPressed ? "meta" : "",
    event.shiftKey ? "shift" : "",
    event.key
  ];
  return parts.filter(Boolean).join("+").toLowerCase();
}
function isMac() {
  return window.navigator.userAgent.includes("Mac OS X");
}
function overrideDefaultCommands(editor) {
  editor.registerCommand(KEY_ENTER_COMMAND, (event) => {
    if (isMac()) {
      return event?.metaKey || false;
    }
    return event?.ctrlKey || false;
  }, COMMAND_PRIORITY_HIGH);
}
function registerShortcuts(context) {
  const listener = createKeyDownListener(context);
  overrideDefaultCommands(context.editor);
  return context.editor.registerRootListener((rootElement, prevRootElement) => {
    rootElement?.addEventListener("keydown", listener);
    prevRootElement?.removeEventListener("keydown", listener);
  });
}

// resources/js/wysiwyg/ui/framework/helpers/node-resizer.ts
function isNodeWithSize(node) {
  return $isImageNode(node) || $isMediaNode(node);
}
var NodeResizer = class {
  constructor(context) {
    __publicField(this, "context");
    __publicField(this, "resizerDOM", null);
    __publicField(this, "targetNode", null);
    __publicField(this, "scrollContainer");
    __publicField(this, "mouseTracker", null);
    __publicField(this, "activeSelection", "");
    __publicField(this, "loadAbortController", new AbortController());
    this.context = context;
    this.scrollContainer = context.scrollDOM;
    this.onSelectionChange = this.onSelectionChange.bind(this);
    this.onTargetDOMLoad = this.onTargetDOMLoad.bind(this);
    context.manager.onSelectionChange(this.onSelectionChange);
  }
  onSelectionChange(selection) {
    const nodes = selection?.getNodes() || [];
    if (this.activeSelection) {
      this.hide();
    }
    if (nodes.length === 1 && isNodeWithSize(nodes[0])) {
      const node = nodes[0];
      let nodeDOM = this.getTargetDOM(node);
      if (nodeDOM) {
        this.showForNode(node, nodeDOM);
      }
    }
  }
  getTargetDOM(targetNode) {
    if (targetNode == null) {
      return null;
    }
    let nodeDOM = this.context.editor.getElementByKey(targetNode.__key);
    if (nodeDOM && nodeDOM.nodeName === "SPAN") {
      nodeDOM = nodeDOM.firstElementChild;
    }
    return nodeDOM;
  }
  onTargetDOMLoad() {
    this.updateResizerPosition();
  }
  teardown() {
    this.context.manager.offSelectionChange(this.onSelectionChange);
    this.hide();
  }
  showForNode(node, targetDOM) {
    this.resizerDOM = this.buildDOM();
    this.targetNode = node;
    let ghost = el("span", { class: "editor-node-resizer-ghost" });
    if ($isImageNode(node)) {
      ghost = el("img", { src: targetDOM.getAttribute("src"), class: "editor-node-resizer-ghost" });
    }
    this.resizerDOM.append(ghost);
    this.context.scrollDOM.append(this.resizerDOM);
    this.updateResizerPosition();
    this.mouseTracker = this.setupTracker(this.resizerDOM, node, targetDOM);
    this.activeSelection = node.getKey();
    if (targetDOM.matches("img, embed, iframe, object")) {
      this.loadAbortController = new AbortController();
      targetDOM.addEventListener("load", this.onTargetDOMLoad, { signal: this.loadAbortController.signal });
    }
  }
  updateResizerPosition() {
    const targetDOM = this.getTargetDOM(this.targetNode);
    if (!this.resizerDOM || !targetDOM) {
      return;
    }
    const scrollAreaRect = this.scrollContainer.getBoundingClientRect();
    const nodeRect = targetDOM.getBoundingClientRect();
    const top = nodeRect.top - (scrollAreaRect.top - this.scrollContainer.scrollTop);
    const left = nodeRect.left - scrollAreaRect.left;
    this.resizerDOM.style.top = `${top}px`;
    this.resizerDOM.style.left = `${left}px`;
    this.resizerDOM.style.width = nodeRect.width + "px";
    this.resizerDOM.style.height = nodeRect.height + "px";
  }
  updateDOMSize(width, height) {
    if (!this.resizerDOM) {
      return;
    }
    this.resizerDOM.style.width = width + "px";
    this.resizerDOM.style.height = height + "px";
  }
  hide() {
    this.mouseTracker?.teardown();
    this.resizerDOM?.remove();
    this.targetNode = null;
    this.activeSelection = "";
    this.loadAbortController.abort();
  }
  buildDOM() {
    const handleClasses = ["nw", "ne", "se", "sw"];
    const handleElems = handleClasses.map((c) => {
      return el("div", { class: `editor-node-resizer-handle ${c}` });
    });
    return el("div", {
      class: "editor-node-resizer"
    }, handleElems);
  }
  setupTracker(container, node, nodeDOM) {
    let startingWidth = 0;
    let startingHeight = 0;
    let startingRatio = 0;
    let hasHeight = false;
    let _this = this;
    let flipXChange = false;
    let flipYChange = false;
    const calculateSize = (distance) => {
      let xChange = distance.x;
      if (flipXChange) {
        xChange = 0 - xChange;
      }
      let yChange = distance.y;
      if (flipYChange) {
        yChange = 0 - yChange;
      }
      const balancedChange = Math.sqrt(Math.pow(Math.abs(xChange), 2) + Math.pow(Math.abs(yChange), 2));
      const increase = xChange + yChange > 0;
      const directedChange = increase ? balancedChange : 0 - balancedChange;
      const newWidth = Math.max(5, Math.round(startingWidth + directedChange));
      const newHeight = Math.round(newWidth * startingRatio);
      return { width: newWidth, height: newHeight };
    };
    return new MouseDragTracker(container, ".editor-node-resizer-handle", {
      down(event, handle) {
        _this.resizerDOM?.classList.add("active");
        _this.context.editor.getEditorState().read(() => {
          const domRect = nodeDOM.getBoundingClientRect();
          startingWidth = node.getWidth() || domRect.width;
          startingHeight = node.getHeight() || domRect.height;
          if (node.getHeight()) {
            hasHeight = true;
          }
          startingRatio = startingHeight / startingWidth;
        });
        flipXChange = handle.classList.contains("nw") || handle.classList.contains("sw");
        flipYChange = handle.classList.contains("nw") || handle.classList.contains("ne");
      },
      move(event, handle, distance) {
        const size = calculateSize(distance);
        _this.updateDOMSize(size.width, size.height);
      },
      up(event, handle, distance) {
        const size = calculateSize(distance);
        _this.context.editor.update(() => {
          node.setWidth(size.width);
          node.setHeight(hasHeight ? size.height : 0);
        }, {
          onUpdate: () => {
            requestAnimationFrame(() => {
              _this.context.manager.triggerLayoutUpdate();
              _this.updateResizerPosition();
            });
          }
        });
        _this.resizerDOM?.classList.remove("active");
      }
    });
  }
};
function registerNodeResizer(context) {
  const resizer = new NodeResizer(context);
  return () => {
    resizer.teardown();
  };
}

// resources/js/wysiwyg/utils/lists.ts
function $nestListItem(node) {
  const list = node.getParent();
  if (!$isListNode(list)) {
    return node;
  }
  const nodeChildList = node.getChildren().filter((n) => $isListNode(n))[0] || null;
  const nodeChildItems = nodeChildList?.getChildren() || [];
  const listItems = list.getChildren();
  const nodeIndex = listItems.findIndex((n) => n.getKey() === node.getKey());
  const isFirst = nodeIndex === 0;
  const newListItem = $createListItemNode();
  const newList = $createListNode(list.getListType());
  newList.append(newListItem);
  newListItem.append(...node.getChildren());
  if (isFirst) {
    node.append(newList);
  } else {
    const prevListItem = listItems[nodeIndex - 1];
    prevListItem.append(newList);
    node.remove();
  }
  if (nodeChildList) {
    for (const child of nodeChildItems) {
      newListItem.insertAfter(child);
    }
    nodeChildList.remove();
  }
  return newListItem;
}
function $unnestListItem(node) {
  const list = node.getParent();
  const parentListItem = list?.getParent();
  const outerList = parentListItem?.getParent();
  if (!$isListNode(list) || !$isListNode(outerList) || !$isListItemNode(parentListItem)) {
    return node;
  }
  const laterSiblings = node.getNextSiblings();
  parentListItem.insertAfter(node);
  if (list.getChildren().length === 0) {
    list.remove();
  }
  if (laterSiblings.length > 0) {
    const childList = $createListNode(list.getListType());
    childList.append(...laterSiblings);
    node.append(childList);
  }
  if (list.getChildrenSize() === 0) {
    list.remove();
  }
  if (parentListItem.getChildren().length === 0) {
    parentListItem.remove();
  }
  return node;
}
function getListItemsForSelection(selection) {
  const nodes = selection?.getNodes() || [];
  let [start, end] = selection?.getStartEndPoints() || [null, null];
  const itemsToIgnore = /* @__PURE__ */ new Set();
  if (selection && start) {
    if (selection.isBackward() && end) {
      [end, start] = [start, end];
    }
    const startParents = start.getNode().getParents();
    let foundList = false;
    for (const parent of startParents) {
      if ($isListItemNode(parent)) {
        if (foundList) {
          itemsToIgnore.add(parent.getKey());
        } else {
          foundList = true;
        }
      }
    }
  }
  const listItemNodes = [];
  outer: for (const node of nodes) {
    if ($isListItemNode(node)) {
      if (!itemsToIgnore.has(node.getKey())) {
        listItemNodes.push(node);
      }
      continue;
    }
    const parents = node.getParents();
    for (const parent of parents) {
      if ($isListItemNode(parent)) {
        if (!itemsToIgnore.has(parent.getKey())) {
          listItemNodes.push(parent);
        }
        continue outer;
      }
    }
    listItemNodes.push(null);
  }
  return listItemNodes;
}
function $reduceDedupeListItems(listItems) {
  const listItemMap = {};
  for (const item of listItems) {
    if (item === null) {
      continue;
    }
    const key = item.getKey();
    if (typeof listItemMap[key] === "undefined") {
      listItemMap[key] = item;
    }
  }
  const items = Object.values(listItemMap);
  return $sortNodes(items);
}
function $setInsetForSelection(editor, change) {
  const selection = $getSelection();
  const selectionBounds = selection?.getStartEndPoints();
  const listItemsInSelection = getListItemsForSelection(selection);
  const isListSelection = listItemsInSelection.length > 0 && !listItemsInSelection.includes(null);
  if (isListSelection) {
    const alteredListItems = [];
    const listItems = $reduceDedupeListItems(listItemsInSelection);
    if (change > 0) {
      for (const listItem of listItems) {
        alteredListItems.push($nestListItem(listItem));
      }
    } else if (change < 0) {
      for (const listItem of [...listItems].reverse()) {
        alteredListItems.push($unnestListItem(listItem));
      }
      alteredListItems.reverse();
    }
    if (alteredListItems.length === 1 && selectionBounds) {
      const listItem = alteredListItems[0];
      let child = listItem.getChildren()[0];
      if (!child) {
        child = $createTextNode("");
        listItem.append(child);
      }
      child.select(selectionBounds[0].offset, selectionBounds[1].offset);
    } else {
      $selectNodes(alteredListItems);
    }
    return;
  }
  const elements = $getBlockElementNodesInSelection(selection);
  for (const node of elements) {
    if (nodeHasInset(node)) {
      const currentInset = node.getInset();
      const newInset = Math.min(Math.max(currentInset + change, 0), 500);
      node.setInset(newInset);
    }
  }
  $toggleSelection(editor);
}

// resources/js/services/http.ts
var HttpError = class extends Error {
  constructor(response, content) {
    super(response.statusText);
    __publicField(this, "data");
    __publicField(this, "headers");
    __publicField(this, "original");
    __publicField(this, "redirected");
    __publicField(this, "status");
    __publicField(this, "statusText");
    __publicField(this, "url");
    this.data = content;
    this.headers = response.headers;
    this.redirected = response.redirected;
    this.status = response.status;
    this.statusText = response.statusText;
    this.url = response.url;
    this.original = response;
  }
};

// node_modules/idb-keyval/dist/index.js
function promisifyRequest(request) {
  return new Promise((resolve, reject) => {
    request.oncomplete = request.onsuccess = () => resolve(request.result);
    request.onabort = request.onerror = () => reject(request.error);
  });
}
function createStore(dbName, storeName) {
  let dbp;
  const getDB = () => {
    if (dbp)
      return dbp;
    const request = indexedDB.open(dbName);
    request.onupgradeneeded = () => request.result.createObjectStore(storeName);
    dbp = promisifyRequest(request);
    dbp.then((db) => {
      db.onclose = () => dbp = void 0;
    }, () => {
    });
    return dbp;
  };
  return (txMode, callback) => getDB().then((db) => callback(db.transaction(storeName, txMode).objectStore(storeName)));
}
var defaultGetStoreFunc;
function defaultGetStore() {
  if (!defaultGetStoreFunc) {
    defaultGetStoreFunc = createStore("keyval-store", "keyval");
  }
  return defaultGetStoreFunc;
}
function get(key, customStore = defaultGetStore()) {
  return customStore("readonly", (store) => promisifyRequest(store.get(key)));
}
function set(key, value, customStore = defaultGetStore()) {
  return customStore("readwrite", (store) => {
    store.put(value, key);
    return promisifyRequest(store.transaction);
  });
}
function del(key, customStore = defaultGetStore()) {
  return customStore("readwrite", (store) => {
    store.delete(key);
    return promisifyRequest(store.transaction);
  });
}

// resources/js/services/drawio.ts
var iFrame = null;
var lastApprovedOrigin;
var onInit;
var onSave;
var saveBackupKey = "last-drawing-save";
function drawPostMessage(data) {
  iFrame?.contentWindow?.postMessage(JSON.stringify(data), lastApprovedOrigin);
}
function drawEventExport(message) {
  set(saveBackupKey, message.data);
  if (onSave) {
    onSave(message.data).then(() => {
      del(saveBackupKey);
    });
  }
}
function drawEventSave(message) {
  drawPostMessage({
    action: "export",
    format: "xmlpng",
    xml: message.xml,
    spin: "Updating drawing"
  });
}
function drawEventInit() {
  if (!onInit) return;
  onInit().then((xml) => {
    drawPostMessage({ action: "load", autosave: 1, xml });
  });
}
function drawEventConfigure() {
  const config = {};
  if (iFrame) {
    window.$events.emitPublic(iFrame, "editor-drawio::configure", { config });
    drawPostMessage({ action: "configure", config });
  }
}
function drawEventClose() {
  window.removeEventListener("message", drawReceive);
  if (iFrame) document.body.removeChild(iFrame);
}
function drawReceive(event) {
  if (!event.data || event.data.length < 1) return;
  if (event.origin !== lastApprovedOrigin) return;
  const message = JSON.parse(event.data);
  if (message.event === "init") {
    drawEventInit();
  } else if (message.event === "exit") {
    drawEventClose();
  } else if (message.event === "save") {
    drawEventSave(message);
  } else if (message.event === "export") {
    drawEventExport(message);
  } else if (message.event === "configure") {
    drawEventConfigure();
  }
}
async function attemptRestoreIfExists() {
  const backupVal = await get(saveBackupKey);
  const dialogEl = document.getElementById("unsaved-drawing-dialog");
  if (!dialogEl) {
    console.error("Missing expected unsaved-drawing dialog");
  }
  if (backupVal && dialogEl) {
    const dialog = window.$components.firstOnElement(dialogEl, "confirm-dialog");
    const restore = await dialog.show();
    if (restore) {
      onInit = async () => backupVal;
    }
  }
}
async function show(drawioUrl, onInitCallback, onSaveCallback) {
  onInit = onInitCallback;
  onSave = onSaveCallback;
  await attemptRestoreIfExists();
  iFrame = document.createElement("iframe");
  iFrame.setAttribute("frameborder", "0");
  window.addEventListener("message", drawReceive);
  iFrame.setAttribute("src", drawioUrl);
  iFrame.setAttribute("class", "fullscreen");
  iFrame.style.backgroundColor = "#FFFFFF";
  document.body.appendChild(iFrame);
  lastApprovedOrigin = new URL(drawioUrl).origin;
}
async function upload(imageData, pageUploadedToId) {
  const data = {
    image: imageData,
    uploaded_to: pageUploadedToId
  };
  const resp = await window.$http.post(window.baseUrl("/images/drawio"), data);
  return resp.data;
}
function close() {
  drawEventClose();
}
async function load(drawingId) {
  try {
    const resp = await window.$http.get(window.baseUrl(`/images/drawio/base64/${drawingId}`));
    const data = resp.data;
    return `data:image/png;base64,${data.content}`;
  } catch (error) {
    if (error instanceof HttpError) {
      window.$events.showResponseError(error);
    }
    close();
    throw error;
  }
}

// resources/js/wysiwyg/utils/diagrams.ts
function $isDiagramNode(node) {
  return node instanceof DiagramNode;
}
function handleUploadError(error, context) {
  if (error.status === 413) {
    window.$events.emit("error", context.options.translations.serverUploadLimitText || "");
  } else {
    window.$events.emit("error", context.options.translations.imageUploadErrorText || "");
  }
  console.error(error);
}
async function loadDiagramIdFromNode(editor, node) {
  const drawingId = await new Promise((res, rej) => {
    editor.getEditorState().read(() => {
      const { id: drawingId2 } = node.getDrawingIdAndUrl();
      res(drawingId2);
    });
  });
  return drawingId || "";
}
async function updateDrawingNodeFromData(context, node, pngData, isNew) {
  close();
  if (isNew) {
    const loadingImage = window.baseUrl("/loading.gif");
    context.editor.update(() => {
      node.setDrawingIdAndUrl("", loadingImage);
    });
  }
  try {
    const img = await upload(pngData, context.options.pageId);
    context.editor.update(() => {
      node.setDrawingIdAndUrl(String(img.id), img.url);
    });
  } catch (err) {
    if (err instanceof HttpError) {
      handleUploadError(err, context);
    }
    if (isNew) {
      context.editor.update(() => {
        node.remove();
      });
    }
    throw new Error(`Failed to save image with error: ${err}`);
  }
}
function $openDrawingEditorForNode(context, node) {
  let isNew = false;
  show(context.options.drawioUrl, async () => {
    const drawingId = await loadDiagramIdFromNode(context.editor, node);
    isNew = !drawingId;
    return isNew ? "" : load(drawingId);
  }, async (pngData) => {
    return updateDrawingNodeFromData(context, node, pngData, isNew);
  });
}
function showDiagramManager(callback) {
  const imageManager = window.$components.first("image-manager");
  imageManager.show((image3) => {
    callback(image3);
  }, "drawio");
}
function showDiagramManagerForInsert(context) {
  const selection = getLastSelection2(context.editor);
  showDiagramManager((image3) => {
    context.editor.update(() => {
      const diagramNode = $createDiagramNode(image3.id, image3.url);
      const selectedDiagram = $getNodeFromSelection(selection, $isDiagramNode);
      if ($isDiagramNode(selectedDiagram)) {
        selectedDiagram.replace(diagramNode);
      } else {
        $insertNodes([diagramNode]);
      }
    });
  });
}

// resources/js/wysiwyg/services/keyboard-handling.ts
function isSingleSelectedNode(nodes) {
  if (nodes.length === 1) {
    const node = nodes[0];
    if ($isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node) || $isDiagramNode(node)) {
      return true;
    }
  }
  return false;
}
function deleteSingleSelectedNode(editor) {
  const selectionNodes = getLastSelection2(editor)?.getNodes() || [];
  if (isSingleSelectedNode(selectionNodes)) {
    editor.update(() => {
      selectionNodes[0].remove();
    });
  }
}
function insertAdjacentToSingleSelectedNode(editor, event) {
  const selectionNodes = getLastSelection2(editor)?.getNodes() || [];
  if (isSingleSelectedNode(selectionNodes)) {
    const node = selectionNodes[0];
    const nearestBlock = $getNearestNodeBlockParent(node) || node;
    const insertBefore = event?.shiftKey === true;
    if (nearestBlock) {
      requestAnimationFrame(() => {
        editor.update(() => {
          const newParagraph = $createParagraphNode();
          if (insertBefore) {
            nearestBlock.insertBefore(newParagraph);
          } else {
            nearestBlock.insertAfter(newParagraph);
          }
          newParagraph.select();
        });
      });
      event?.preventDefault();
      return true;
    }
  }
  return false;
}
function focusAdjacentOrInsertForSingleSelectNode(editor, event, after = true) {
  const selectionNodes = getLastSelection2(editor)?.getNodes() || [];
  if (!isSingleSelectedNode(selectionNodes)) {
    return false;
  }
  event?.preventDefault();
  const node = selectionNodes[0];
  editor.update(() => {
    $selectOrCreateAdjacent(node, after);
  });
  return true;
}
function insertAfterDetails(editor, event) {
  const scenario = getDetailsScenario(editor);
  if (scenario === null || scenario.detailsSibling) {
    return false;
  }
  editor.update(() => {
    const newParagraph = $createParagraphNode();
    scenario.parentDetails.insertAfter(newParagraph);
    newParagraph.select();
  });
  event?.preventDefault();
  return true;
}
function moveAfterDetailsOnEmptyLine(editor, event) {
  const scenario = getDetailsScenario(editor);
  if (scenario === null) {
    return false;
  }
  if (scenario.parentBlock.getTextContent() !== "") {
    return false;
  }
  event?.preventDefault();
  const nextSibling = scenario.parentDetails.getNextSibling();
  editor.update(() => {
    if (nextSibling) {
      nextSibling.selectStart();
    } else {
      const newParagraph = $createParagraphNode();
      scenario.parentDetails.insertAfter(newParagraph);
      newParagraph.select();
    }
    scenario.parentBlock.remove();
  });
  return true;
}
function getDetailsScenario(editor) {
  const selection = getLastSelection2(editor);
  const firstNode = selection?.getNodes()[0];
  if (!firstNode) {
    return null;
  }
  const block = $getNearestNodeBlockParent(firstNode);
  const details3 = $getParentOfType(firstNode, $isDetailsNode);
  if (!$isDetailsNode(details3) || block === null) {
    return null;
  }
  if (block.getKey() !== details3.getLastChild()?.getKey()) {
    return null;
  }
  const nextSibling = details3.getNextSibling();
  return {
    parentDetails: details3,
    parentBlock: block,
    detailsSibling: nextSibling
  };
}
function $isSingleListItem(nodes) {
  if (nodes.length !== 1) {
    return false;
  }
  const node = nodes[0];
  return $isListItemNode(node) || $isListItemNode(node.getParent());
}
function handleInsetOnTab(editor, event) {
  const change = event?.shiftKey ? -40 : 40;
  const selection = $getSelection();
  const nodes = selection?.getNodes() || [];
  if (nodes.length > 1 || $isSingleListItem(nodes)) {
    editor.update(() => {
      $setInsetForSelection(editor, change);
    });
    event?.preventDefault();
    return true;
  }
  return false;
}
function registerKeyboardHandling(context) {
  const unregisterBackspace = context.editor.registerCommand(KEY_BACKSPACE_COMMAND, () => {
    deleteSingleSelectedNode(context.editor);
    return false;
  }, COMMAND_PRIORITY_LOW);
  const unregisterDelete = context.editor.registerCommand(KEY_DELETE_COMMAND, () => {
    deleteSingleSelectedNode(context.editor);
    return false;
  }, COMMAND_PRIORITY_LOW);
  const unregisterEnter = context.editor.registerCommand(KEY_ENTER_COMMAND, (event) => {
    return insertAdjacentToSingleSelectedNode(context.editor, event) || moveAfterDetailsOnEmptyLine(context.editor, event);
  }, COMMAND_PRIORITY_LOW);
  const unregisterTab = context.editor.registerCommand(KEY_TAB_COMMAND, (event) => {
    return handleInsetOnTab(context.editor, event);
  }, COMMAND_PRIORITY_LOW);
  const unregisterUp = context.editor.registerCommand(KEY_ARROW_UP_COMMAND, (event) => {
    return focusAdjacentOrInsertForSingleSelectNode(context.editor, event, false);
  }, COMMAND_PRIORITY_LOW);
  const unregisterDown = context.editor.registerCommand(KEY_ARROW_DOWN_COMMAND, (event) => {
    return insertAfterDetails(context.editor, event) || focusAdjacentOrInsertForSingleSelectNode(context.editor, event, true);
  }, COMMAND_PRIORITY_LOW);
  return () => {
    unregisterBackspace();
    unregisterDelete();
    unregisterEnter();
    unregisterTab();
    unregisterUp();
    unregisterDown();
  };
}

// resources/js/wysiwyg/services/auto-links.ts
function isLinkText(text) {
  const lower = text.toLowerCase();
  if (!lower.startsWith("http")) {
    return false;
  }
  const linkRegex = /(http|https):\/\/(\S+)\.\S+$/;
  return linkRegex.test(text);
}
function handlePotentialLinkEvent(node, selection, editor) {
  const selectionRange = selection.getStartEndPoints();
  if (!selectionRange) {
    return;
  }
  const cursorPoint = selectionRange[0].offset;
  const nodeText = node.getTextContent();
  const rTrimText = nodeText.slice(0, cursorPoint);
  const priorSpaceIndex = rTrimText.lastIndexOf(" ");
  const startIndex = priorSpaceIndex + 1;
  const textSegment = nodeText.slice(startIndex, cursorPoint);
  if (!isLinkText(textSegment)) {
    return;
  }
  editor.update(() => {
    const linkNode = $createLinkNode(textSegment);
    linkNode.append(new TextNode(textSegment));
    const splits = node.splitText(startIndex, cursorPoint);
    const targetIndex = startIndex > 0 ? 1 : 0;
    const targetText = splits[targetIndex];
    if (targetText) {
      targetText.replace(linkNode);
    }
  });
}
function registerAutoLinks(editor) {
  const handler = (payload) => {
    const selection = $getSelection();
    const textNode = $getTextNodeFromSelection(selection);
    if (textNode && selection) {
      handlePotentialLinkEvent(textNode, selection, editor);
    }
    return false;
  };
  const unregisterSpace = editor.registerCommand(KEY_SPACE_COMMAND, handler, COMMAND_PRIORITY_NORMAL);
  const unregisterEnter = editor.registerCommand(KEY_ENTER_COMMAND, handler, COMMAND_PRIORITY_NORMAL);
  return () => {
    unregisterSpace();
    unregisterEnter();
  };
}

// resources/js/wysiwyg/ui/framework/blocks/format-menu.ts
var EditorFormatMenu = class extends EditorContainerUiElement {
  buildDOM() {
    const childElements = this.getChildren().map((child) => child.getDOMElement());
    const menu = el("div", {
      class: "editor-format-menu-dropdown editor-dropdown-menu editor-dropdown-menu-vertical",
      hidden: "true"
    }, childElements);
    const toggle = el("button", {
      class: "editor-format-menu-toggle editor-button",
      type: "button"
    }, [this.trans("Formats")]);
    const wrapper = el("div", {
      class: "editor-format-menu editor-dropdown-menu-container"
    }, [toggle, menu]);
    this.getContext().manager.dropdowns.handle({ toggle, menu });
    this.onEvent("button-action", () => {
      this.getContext().manager.dropdowns.closeAll();
    }, wrapper);
    return wrapper;
  }
  updateState(state) {
    super.updateState(state);
    for (const child of this.children) {
      if (child instanceof EditorButton && child.isActive()) {
        this.updateToggleLabel(child.getLabel());
        return;
      }
      if (child instanceof EditorContainerUiElement) {
        for (const grandchild of child.getChildren()) {
          if (grandchild instanceof EditorButton && grandchild.isActive()) {
            this.updateToggleLabel(grandchild.getLabel());
            return;
          }
        }
      }
    }
    this.updateToggleLabel(this.trans("Formats"));
  }
  updateToggleLabel(text) {
    const button = this.getDOMElement().querySelector("button");
    if (button) {
      button.innerText = text;
    }
  }
};

// resources/js/wysiwyg/ui/framework/blocks/format-preview-button.ts
var FormatPreviewButton = class extends EditorButton {
  constructor(previewSampleElement, definition) {
    super(definition);
    __publicField(this, "previewSampleElement");
    this.previewSampleElement = previewSampleElement;
  }
  buildDOM() {
    const button = super.buildDOM();
    button.innerHTML = "";
    const preview = el("span", {
      class: "editor-button-format-preview"
    }, [this.getLabel()]);
    const stylesToApply = this.getStylesFromPreview();
    for (const style of Object.keys(stylesToApply)) {
      preview.style.setProperty(style, stylesToApply[style]);
    }
    button.append(preview);
    return button;
  }
  getStylesFromPreview() {
    const wrap = el("div", { style: "display: none", hidden: "true", class: "page-content" });
    const sampleClone = this.previewSampleElement.cloneNode();
    sampleClone.textContent = this.getLabel();
    wrap.append(sampleClone);
    document.body.append(wrap);
    const propertiesToFetch = ["color", "font-size", "background-color", "border-inline-start"];
    const propertiesToReturn = {};
    const computed = window.getComputedStyle(sampleClone);
    for (const property of propertiesToFetch) {
      propertiesToReturn[property] = computed.getPropertyValue(property);
    }
    wrap.remove();
    return propertiesToReturn;
  }
};

// resources/icons/chevron-right.svg
var chevron_right_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';

// resources/js/wysiwyg/ui/framework/blocks/menu-button.ts
var EditorMenuButton = class extends EditorButton {
  buildDOM() {
    const dom = super.buildDOM();
    const icon = el("div", { class: "editor-menu-button-icon" });
    icon.innerHTML = chevron_right_default;
    dom.append(icon);
    return dom;
  }
};

// resources/js/wysiwyg/ui/framework/blocks/dropdown-button.ts
var defaultOptions = {
  showOnHover: false,
  direction: "horizontal",
  showAside: void 0,
  hideOnAction: true,
  button: { label: "Menu" }
};
var EditorDropdownButton = class extends EditorContainerUiElement {
  constructor(options, children) {
    super(children);
    __publicField(this, "button");
    __publicField(this, "childItems");
    __publicField(this, "open", false);
    __publicField(this, "options");
    this.childItems = children;
    this.options = Object.assign({}, defaultOptions, options);
    if (options.button instanceof EditorButton) {
      this.button = options.button;
    } else {
      const type = options.button.format === "long" ? EditorMenuButton : EditorButton;
      this.button = new type({
        ...options.button,
        action() {
          return false;
        },
        isActive: () => {
          return this.open;
        }
      });
    }
    this.addChildren(this.button);
  }
  insertItems(...items) {
    this.addChildren(...items);
    this.childItems.push(...items);
  }
  buildDOM() {
    const button = this.button.getDOMElement();
    const childElements = this.childItems.map((child) => child.getDOMElement());
    const menu = el("div", {
      class: `editor-dropdown-menu editor-dropdown-menu-${this.options.direction}`,
      hidden: "true"
    }, childElements);
    const wrapper = el("div", {
      class: "editor-dropdown-menu-container"
    }, [button, menu]);
    this.getContext().manager.dropdowns.handle({
      toggle: button,
      menu,
      showOnHover: this.options.showOnHover,
      showAside: typeof this.options.showAside === "boolean" ? this.options.showAside : this.options.direction === "vertical",
      onOpen: () => {
        this.open = true;
        this.getContext().manager.triggerStateUpdateForElement(this.button);
      },
      onClose: () => {
        this.open = false;
        this.getContext().manager.triggerStateUpdateForElement(this.button);
      }
    });
    if (this.options.hideOnAction) {
      this.onEvent("button-action", () => {
        this.getContext().manager.dropdowns.closeAll();
      }, wrapper);
    }
    return wrapper;
  }
};

// resources/icons/editor/color-clear.svg
var color_clear_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M800-436q0 36-8 69t-22 63l-62-60q6-17 9-34.5t3-37.5q0-47-17.5-89T650-600L480-768l-88 86-56-56 144-142 226 222q44 42 69 99.5T800-436Zm-8 380L668-180q-41 29-88 44.5T480-120q-133 0-226.5-92.5T160-436q0-51 16-98t48-90L56-792l56-56 736 736-56 56ZM480-200q36 0 68.5-10t61.5-28L280-566q-21 32-30.5 64t-9.5 66q0 98 70 167t170 69Zm-37-204Zm110-116Z"/></svg>';

// resources/icons/editor/color-select.svg
var color_select_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 32.5-156t88-127Q256-817 330-848.5T488-880q80 0 151 27.5t124.5 76q53.5 48.5 85 115T880-518q0 115-70 176.5T640-280h-74q-9 0-12.5 5t-3.5 11q0 12 15 34.5t15 51.5q0 50-27.5 74T480-80Zm0-400Zm-220 40q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm120-160q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm200 0q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm120 160q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17ZM480-160q9 0 14.5-5t5.5-13q0-14-15-33t-15-57q0-42 29-67t71-25h70q66 0 113-38.5T800-518q0-121-92.5-201.5T488-800q-136 0-232 93t-96 227q0 133 93.5 226.5T480-160Z"/></svg>';

// resources/js/wysiwyg/ui/framework/blocks/color-picker.ts
var colorChoices = [
  "#000000",
  "#ffffff",
  "#BFEDD2",
  "#FBEEB8",
  "#F8CAC6",
  "#ECCAFA",
  "#C2E0F4",
  "#2DC26B",
  "#F1C40F",
  "#E03E2D",
  "#B96AD9",
  "#3598DB",
  "#169179",
  "#E67E23",
  "#BA372A",
  "#843FA1",
  "#236FA1",
  "#ECF0F1",
  "#CED4D9",
  "#95A5A6",
  "#7E8C8D",
  "#34495E"
];
var storageKey = "bs-lexical-custom-colors";
var EditorColorPicker = class extends EditorUiElement {
  constructor(callback) {
    super();
    __publicField(this, "callback");
    this.callback = callback;
  }
  buildDOM() {
    const id = uniqueIdSmall();
    const allChoices = [...colorChoices, ...this.getCustomColorChoices()];
    const colorOptions = allChoices.map((choice) => {
      return el("div", {
        class: "editor-color-select-option",
        style: `background-color: ${choice}`,
        "data-color": choice,
        "aria-label": choice
      });
    });
    const removeButton = el("div", {
      class: "editor-color-select-option",
      "data-color": "",
      title: this.getContext().translate("Remove color")
    }, []);
    removeButton.innerHTML = color_clear_default;
    colorOptions.push(removeButton);
    const selectButton = el("label", {
      class: "editor-color-select-option",
      for: `color-select-${id}`,
      "data-color": "",
      title: this.getContext().translate("Custom color")
    }, []);
    selectButton.innerHTML = color_select_default;
    colorOptions.push(selectButton);
    const input = el("input", { type: "color", hidden: "true", id: `color-select-${id}` });
    colorOptions.push(input);
    input.addEventListener("change", (e) => {
      if (input.value) {
        this.storeCustomColorChoice(input.value);
        this.setColor(input.value);
        this.rebuildDOM();
      }
    });
    const colorRows = [];
    for (let i = 0; i < colorOptions.length; i += 5) {
      const options = colorOptions.slice(i, i + 5);
      colorRows.push(el("div", {
        class: "editor-color-select-row"
      }, options));
    }
    const wrapper = el("div", {
      class: "editor-color-select"
    }, colorRows);
    wrapper.addEventListener("click", this.onClick.bind(this));
    return wrapper;
  }
  storeCustomColorChoice(color) {
    if (colorChoices.includes(color)) {
      return;
    }
    const customColors = this.getCustomColorChoices();
    if (customColors.includes(color)) {
      return;
    }
    customColors.push(color);
    window.localStorage.setItem(storageKey, JSON.stringify(customColors));
  }
  getCustomColorChoices() {
    return JSON.parse(window.localStorage.getItem(storageKey) || "[]");
  }
  onClick(event) {
    const colorEl = event.target.closest("[data-color]");
    if (!colorEl) return;
    const color = colorEl.dataset.color;
    this.setColor(color);
  }
  setColor(color) {
    this.callback(color, this.getContext());
  }
};

// resources/js/wysiwyg/ui/framework/blocks/table-creator.ts
var EditorTableCreator = class extends EditorUiElement {
  buildDOM() {
    const size = 10;
    const rows = [];
    const cells = [];
    for (let row = 1; row < size + 1; row++) {
      const rowCells = [];
      for (let column = 1; column < size + 1; column++) {
        const cell = el("div", {
          class: "editor-table-creator-cell",
          "data-rows": String(row),
          "data-columns": String(column)
        });
        rowCells.push(cell);
        cells.push(cell);
      }
      rows.push(el("div", {
        class: "editor-table-creator-row"
      }, rowCells));
    }
    const display = el("div", { class: "editor-table-creator-display" }, ["0 x 0"]);
    const grid = el("div", { class: "editor-table-creator-grid" }, rows);
    grid.addEventListener("mousemove", (event) => {
      const cell = event.target.closest(".editor-table-creator-cell");
      if (cell) {
        const row = Number(cell.dataset.rows || 0);
        const column = Number(cell.dataset.columns || 0);
        this.updateGridSelection(row, column, cells, display);
      }
    });
    grid.addEventListener("click", (event) => {
      const cell = event.target.closest(".editor-table-creator-cell");
      if (cell) {
        this.onCellClick(cell);
      }
    });
    grid.addEventListener("mouseleave", (event) => {
      this.updateGridSelection(0, 0, cells, display);
    });
    return el("div", {
      class: "editor-table-creator"
    }, [
      grid,
      display
    ]);
  }
  updateGridSelection(rows, columns, cells, display) {
    for (const cell of cells) {
      const active = Number(cell.dataset.rows) <= rows && Number(cell.dataset.columns) <= columns;
      cell.classList.toggle("active", active);
    }
    display.textContent = `${rows} x ${columns}`;
  }
  onCellClick(cell) {
    const rows = Number(cell.dataset.rows || 0);
    const columns = Number(cell.dataset.columns || 0);
    if (rows < 1 || columns < 1) {
      return;
    }
    const targetColWidth = Math.min(Math.round(840 / columns), 240);
    const colWidths = Array(columns).fill(targetColWidth + "px");
    this.getContext().editor.update(() => {
      const table2 = $createTableNodeWithDimensions(rows, columns, false);
      table2.setColWidths(colWidths);
      $insertNewBlockNodeAtSelection(table2);
    });
  }
};

// resources/js/wysiwyg/ui/framework/blocks/color-button.ts
var EditorColorButton = class extends EditorButton {
  constructor(definition, style) {
    super(definition);
    __publicField(this, "style");
    this.style = style;
  }
  getColorBar() {
    const colorBar = this.getDOMElement().querySelector("svg .editor-icon-color-bar");
    if (!colorBar) {
      throw new Error(`Could not find expected color bar in the icon for this ${this.definition.label} button`);
    }
    return colorBar;
  }
  updateState(state) {
    super.updateState(state);
    if ($isRangeSelection(state.selection)) {
      const value = $getSelectionStyleValueForProperty(state.selection, this.style);
      const colorBar = this.getColorBar();
      colorBar.setAttribute("fill", value);
    }
  }
};

// resources/icons/editor/more-horizontal.svg
var more_horizontal_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M240-400q-33 0-56.5-23.5T160-480q0-33 23.5-56.5T240-560q33 0 56.5 23.5T320-480q0 33-23.5 56.5T240-400Zm240 0q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm240 0q-33 0-56.5-23.5T640-480q0-33 23.5-56.5T720-560q33 0 56.5 23.5T800-480q0 33-23.5 56.5T720-400Z"/></svg>';

// resources/js/wysiwyg/ui/framework/blocks/overflow-container.ts
var EditorOverflowContainer = class extends EditorContainerUiElement {
  constructor(size, children) {
    super(children);
    __publicField(this, "size");
    __publicField(this, "overflowButton");
    __publicField(this, "content");
    this.size = size;
    this.content = children;
    this.overflowButton = new EditorDropdownButton({
      button: {
        label: "More",
        icon: more_horizontal_default
      },
      hideOnAction: false
    }, []);
    this.addChildren(this.overflowButton);
  }
  buildDOM() {
    const slicePosition = this.content.length > this.size ? this.size - 1 : this.size;
    const visibleChildren = this.content.slice(0, slicePosition);
    const invisibleChildren = this.content.slice(slicePosition);
    const visibleElements = visibleChildren.map((child) => child.getDOMElement());
    if (invisibleChildren.length > 0) {
      this.removeChildren(...invisibleChildren);
      this.overflowButton.insertItems(...invisibleChildren);
      visibleElements.push(this.overflowButton.getDOMElement());
    }
    return el("div", {
      class: "editor-overflow-container"
    }, visibleElements);
  }
};

// resources/icons/editor/table.svg
var table_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200Zm80-400h560v-160H200v160Zm213 200h134v-120H413v120Zm0 200h134v-120H413v120ZM200-400h133v-120H200v120Zm427 0h133v-120H627v120ZM200-200h133v-120H200v120Zm427 0h133v-120H627v120Z"/></svg>';

// resources/icons/editor/table-delete.svg
var table_delete_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 21a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14c0 1.1-.9 2-2 2zm0-2h14V5H5v14z"/><path d="m13.711 15.423-1.71-1.712-1.712 1.712c-1.14 1.14-2.852-.57-1.71-1.712l1.71-1.71-1.71-1.712c-1.143-1.142.568-2.853 1.71-1.71L12 10.288l1.711-1.71c1.141-1.142 2.852.57 1.712 1.71L13.71 12l1.626 1.626c1.345 1.345-.76 2.663-1.626 1.797z" style="fill-rule:nonzero;stroke-width:1.20992"/></svg>';

// resources/icons/editor/table-delete-column.svg
var table_delete_column_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14c1.1 0 2 .9 2 2zm-2 0V5h-4v2.2h-2V5h-2v2.2H9V5H5v14h4v-2.1h2V19h2v-2.1h2V19Z"/><path d="M14.829 10.585 13.415 12l1.414 1.414c.943.943-.472 2.357-1.414 1.414L12 13.414l-1.414 1.414c-.944.944-2.358-.47-1.414-1.414L10.586 12l-1.414-1.415c-.943-.942.471-2.357 1.414-1.414L12 10.585l1.344-1.343c1.111-1.112 2.2.627 1.485 1.343z" style="fill-rule:nonzero"/></svg>';

// resources/icons/editor/table-delete-row.svg
var table_delete_row_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 21a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14c0 1.1-.9 2-2 2zm0-2h14v-4h-2.2v-2H19v-2h-2.2V9H19V5H5v4h2.1v2H5v2h2.1v2H5Z"/><path d="M13.415 14.829 12 13.415l-1.414 1.414c-.943.943-2.357-.472-1.414-1.414L10.586 12l-1.414-1.414c-.944-.944.47-2.358 1.414-1.414L12 10.586l1.415-1.414c.942-.943 2.357.471 1.414 1.414L13.415 12l1.343 1.344c1.112 1.111-.627 2.2-1.343 1.485z" style="fill-rule:nonzero"/></svg>';

// resources/icons/editor/table-insert-column-after.svg
var table_insert_column_after_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 5h-5v14h5c1.235 0 1.234 2 0 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11c1.229 0 1.236 2 0 2zm-7 6V5H5v6zm0 8v-6H5v6zm11.076-6h-2v2c0 1.333-2 1.333-2 0v-2h-2c-1.335 0-1.335-2 0-2h2V9c0-1.333 2-1.333 2 0v2h1.9c1.572 0 1.113 2 .1 2z"/></svg>';

// resources/icons/editor/table-insert-column-before.svg
var table_insert_column_before_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8 19h5V5H8C6.764 5 6.766 3 8 3h11a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H8c-1.229 0-1.236-2 0-2zm7-6v6h4v-6zm0-8v6h4V5ZM3.924 11h2V9c0-1.333 2-1.333 2 0v2h2c1.335 0 1.335 2 0 2h-2v2c0 1.333-2 1.333-2 0v-2h-1.9c-1.572 0-1.113-2-.1-2z"/></svg>';

// resources/icons/editor/table-insert-row-above.svg
var table_insert_row_above_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 8v5h14V8c0-1.235 2-1.234 2 0v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8C3 6.77 5 6.764 5 8zm6 7H5v4h6zm8 0h-6v4h6zM13 3.924v2h2c1.333 0 1.333 2 0 2h-2v2c0 1.335-2 1.335-2 0v-2H9c-1.333 0-1.333-2 0-2h2v-1.9c0-1.572 2-1.113 2-.1z"/></svg>';

// resources/icons/editor/table-insert-row-below.svg
var table_insert_row_below_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 16v-5H5v5c0 1.235-2 1.234-2 0V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v11c0 1.229-2 1.236-2 0zm-6-7h6V5h-6zM5 9h6V5H5Zm6 11.076v-2H9c-1.333 0-1.333-2 0-2h2v-2c0-1.335 2-1.335 2 0v2h2c1.333 0 1.333 2 0 2h-2v1.9c0 1.572-2 1.113-2 .1z"/></svg>';

// resources/icons/editor/color-display.svg
var color_display_default = '<svg version="1.1" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">\n <defs>\n  <pattern id="pattern2" x="0.40000001" patternTransform="scale(200)" preserveAspectRatio="xMidYMid" xlink:href="#Checkerboard"/>\n  <pattern id="Checkerboard" width="2" height="2" fill="#b6b6b6" patternTransform="translate(0) scale(10)" patternUnits="userSpaceOnUse" preserveAspectRatio="xMidYMid">\n   <rect width="1" height="1"/>\n   <rect x="1" y="1" width="1" height="1"/>\n  </pattern>\n </defs>\n <rect class="editor-icon-color-display" x="103.53" y="-856.47" width="752.94" height="752.94" rx="47.059" ry="47.059" fill="url(#pattern2)" stroke="#666" stroke-linecap="square" stroke-linejoin="round" stroke-width="47.059"/>\n</svg>\n';

// resources/js/wysiwyg/ui/framework/blocks/color-field.ts
var EditorColorField = class extends EditorContainerUiElement {
  constructor(input) {
    super([]);
    __publicField(this, "input");
    __publicField(this, "pickerButton");
    this.input = input;
    this.pickerButton = new EditorDropdownButton({
      button: { icon: color_display_default, label: "Select color" }
    }, [
      new EditorColorPicker(this.onColorSelect.bind(this))
    ]);
    this.addChildren(this.pickerButton, this.input);
  }
  buildDOM() {
    const dom = this.input.getDOMElement();
    dom.append(this.pickerButton.getDOMElement());
    dom.classList.add("editor-color-field-container");
    const field = dom.querySelector("input");
    field.addEventListener("change", () => {
      this.setIconColor(field.value);
    });
    return dom;
  }
  onColorSelect(color, context) {
    this.input.setValue(color);
  }
  setIconColor(color) {
    const icon = this.getDOMElement().querySelector("svg .editor-icon-color-display");
    if (icon) {
      icon.setAttribute("fill", color || "url(#pattern2)");
    }
  }
};
function colorFieldBuilder(field) {
  return {
    build() {
      return new EditorColorField(new EditorFormField(field));
    }
  };
}

// resources/js/wysiwyg/ui/defaults/forms/tables.ts
var borderStyleInput = {
  label: "Border style",
  name: "border_style",
  type: "select",
  valuesByLabel: {
    "Select...": "",
    "Solid": "solid",
    "Dotted": "dotted",
    "Dashed": "dashed",
    "Double": "double",
    "Groove": "groove",
    "Ridge": "ridge",
    "Inset": "inset",
    "Outset": "outset",
    "None": "none",
    "Hidden": "hidden"
  }
};
var borderColorInput = {
  label: "Border color",
  name: "border_color",
  type: "text"
};
var backgroundColorInput = {
  label: "Background color",
  name: "background_color",
  type: "text"
};
var alignmentInput = {
  label: "Alignment",
  name: "align",
  type: "select",
  valuesByLabel: {
    "None": "",
    "Left": "left",
    "Center": "center",
    "Right": "right"
  }
};
function $showCellPropertiesForm(cell, context) {
  const styles = cell.getStyles();
  const modalForm = context.manager.createModal("cell_properties");
  modalForm.show({
    width: $getTableCellColumnWidth(context.editor, cell),
    height: styles.get("height") || "",
    type: cell.getTag(),
    h_align: cell.getAlignment(),
    v_align: styles.get("vertical-align") || "",
    border_width: styles.get("border-width") || "",
    border_style: styles.get("border-style") || "",
    border_color: styles.get("border-color") || "",
    background_color: cell.getBackgroundColor() || styles.get("background-color") || ""
  });
  return modalForm;
}
var cellProperties = {
  submitText: "Save",
  async action(formData, context) {
    context.editor.update(() => {
      const cells = $getTableCellsFromSelection($getSelection());
      for (const cell of cells) {
        const width = formData.get("width")?.toString() || "";
        $setTableCellColumnWidth(cell, width);
        cell.updateTag(formData.get("type")?.toString() || "");
        cell.setAlignment(formData.get("h_align")?.toString() || "");
        cell.setBackgroundColor(formData.get("background_color")?.toString() || "");
        const styles = cell.getStyles();
        styles.set("height", formatSizeValue(formData.get("height")?.toString() || ""));
        styles.set("vertical-align", formData.get("v_align")?.toString() || "");
        styles.set("border-width", formatSizeValue(formData.get("border_width")?.toString() || ""));
        styles.set("border-style", formData.get("border_style")?.toString() || "");
        styles.set("border-color", formData.get("border_color")?.toString() || "");
        cell.setStyles(styles);
      }
    });
    return true;
  },
  fields: [
    {
      build() {
        const generalFields = [
          {
            label: "Width",
            // Colgroup width
            name: "width",
            type: "text"
          },
          {
            label: "Height",
            // inline-style: height
            name: "height",
            type: "text"
          },
          {
            label: "Cell type",
            // element
            name: "type",
            type: "select",
            valuesByLabel: {
              "Cell": "td",
              "Header cell": "th"
            }
          },
          {
            ...alignmentInput,
            // class: 'align-right/left/center'
            label: "Horizontal align",
            name: "h_align"
          },
          {
            label: "Vertical align",
            // inline-style: vertical-align
            name: "v_align",
            type: "select",
            valuesByLabel: {
              "None": "",
              "Top": "top",
              "Middle": "middle",
              "Bottom": "bottom"
            }
          }
        ];
        const advancedFields = [
          {
            label: "Border width",
            // inline-style: border-width
            name: "border_width",
            type: "text"
          },
          borderStyleInput,
          // inline-style: border-style
          colorFieldBuilder(borderColorInput),
          colorFieldBuilder(backgroundColorInput)
        ];
        return new EditorFormTabs([
          {
            label: "General",
            contents: generalFields
          },
          {
            label: "Advanced",
            contents: advancedFields
          }
        ]);
      }
    }
  ]
};
function $showRowPropertiesForm(row, context) {
  const styles = row.getStyles();
  const modalForm = context.manager.createModal("row_properties");
  modalForm.show({
    height: styles.get("height") || "",
    border_style: styles.get("border-style") || "",
    border_color: styles.get("border-color") || "",
    background_color: styles.get("background-color") || ""
  });
  return modalForm;
}
var rowProperties = {
  submitText: "Save",
  async action(formData, context) {
    context.editor.update(() => {
      const rows = $getTableRowsFromSelection($getSelection());
      for (const row of rows) {
        const styles = row.getStyles();
        styles.set("height", formatSizeValue(formData.get("height")?.toString() || ""));
        styles.set("border-style", formData.get("border_style")?.toString() || "");
        styles.set("border-color", formData.get("border_color")?.toString() || "");
        styles.set("background-color", formData.get("background_color")?.toString() || "");
        row.setStyles(styles);
      }
    });
    return true;
  },
  fields: [
    // Removed fields:
    // Removed 'Row Type' as we don't currently support thead/tfoot elements
    //  TinyMCE would move rows up/down into these parents when set
    // Removed 'Alignment' since this was broken in our editor (applied alignment class to whole parent table)
    {
      label: "Height",
      // style on tr: height
      name: "height",
      type: "text"
    },
    borderStyleInput,
    // style on tr: height
    colorFieldBuilder(borderColorInput),
    colorFieldBuilder(backgroundColorInput)
  ]
};
function $showTablePropertiesForm(table2, context) {
  const styles = table2.getStyles();
  const modalForm = context.manager.createModal("table_properties");
  modalForm.show({
    width: styles.get("width") || "",
    height: styles.get("height") || "",
    cell_spacing: styles.get("cell-spacing") || "",
    cell_padding: $getCellPaddingForTable(table2),
    border_width: styles.get("border-width") || "",
    border_style: styles.get("border-style") || "",
    border_color: styles.get("border-color") || "",
    background_color: styles.get("background-color") || "",
    caption: $tableHasCaption(table2) ? "true" : "",
    align: table2.getAlignment()
  });
  return modalForm;
}
var tableProperties = {
  submitText: "Save",
  async action(formData, context) {
    context.editor.update(() => {
      const table2 = $getTableFromSelection($getSelection());
      if (!table2) {
        return;
      }
      const styles = table2.getStyles();
      styles.set("width", formatSizeValue(formData.get("width")?.toString() || ""));
      styles.set("height", formatSizeValue(formData.get("height")?.toString() || ""));
      styles.set("cell-spacing", formatSizeValue(formData.get("cell_spacing")?.toString() || ""));
      styles.set("border-width", formatSizeValue(formData.get("border_width")?.toString() || ""));
      styles.set("border-style", formData.get("border_style")?.toString() || "");
      styles.set("border-color", formData.get("border_color")?.toString() || "");
      styles.set("background-color", formData.get("background_color")?.toString() || "");
      table2.setStyles(styles);
      table2.setAlignment(formData.get("align"));
      const cellPadding = formData.get("cell_padding")?.toString() || "";
      if (cellPadding) {
        const cellPaddingFormatted = formatSizeValue(cellPadding);
        $forEachTableCell2(table2, (cell) => {
          const styles2 = cell.getStyles();
          styles2.set("padding", cellPaddingFormatted);
          cell.setStyles(styles2);
        });
      }
      const showCaption = Boolean(formData.get("caption")?.toString() || "");
      const hasCaption = $tableHasCaption(table2);
      if (showCaption && !hasCaption) {
        $addCaptionToTable(table2, context.translate("Caption"));
      } else if (!showCaption && hasCaption) {
        for (const child of table2.getChildren()) {
          if ($isCaptionNode(child)) {
            child.remove();
          }
        }
      }
    });
    return true;
  },
  fields: [
    {
      build() {
        const generalFields = [
          {
            label: "Width",
            // Style - width
            name: "width",
            type: "text"
          },
          {
            label: "Height",
            // Style - height
            name: "height",
            type: "text"
          },
          {
            label: "Cell spacing",
            // Style - border-spacing
            name: "cell_spacing",
            type: "text"
          },
          {
            label: "Cell padding",
            // Style - padding on child cells?
            name: "cell_padding",
            type: "text"
          },
          {
            label: "Border width",
            // Style - border-width
            name: "border_width",
            type: "text"
          },
          {
            label: "Show caption",
            // Caption element
            name: "caption",
            type: "checkbox"
          },
          alignmentInput
          // alignment class
        ];
        const advancedFields = [
          borderStyleInput,
          colorFieldBuilder(borderColorInput),
          colorFieldBuilder(backgroundColorInput)
        ];
        return new EditorFormTabs([
          {
            label: "General",
            contents: generalFields
          },
          {
            label: "Advanced",
            contents: advancedFields
          }
        ]);
      }
    }
  ]
};

// resources/js/wysiwyg/utils/node-clipboard.ts
function serializeNodeRecursive(node) {
  const childNodes = $isElementNode(node) ? node.getChildren() : [];
  return {
    node: node.exportJSON(),
    children: childNodes.map((n) => serializeNodeRecursive(n))
  };
}
function unserializeNodeRecursive(editor, { node, children }) {
  const instance = editor._nodes.get(node.type)?.klass.importJSON(node);
  if (!instance) {
    return null;
  }
  const childNodes = children.map((child) => unserializeNodeRecursive(editor, child));
  for (const child of childNodes) {
    if (child && $isElementNode(instance)) {
      instance.append(child);
    }
  }
  return instance;
}
var NodeClipboard = class {
  constructor() {
    __publicField(this, "store", []);
  }
  set(...nodes) {
    this.store.splice(0, this.store.length);
    for (const node of nodes) {
      this.store.push(serializeNodeRecursive(node));
    }
  }
  get(editor) {
    return this.store.map((json) => unserializeNodeRecursive(editor, json)).filter((node) => {
      return node !== null;
    });
  }
  size() {
    return this.store.length;
  }
};

// resources/js/wysiwyg/utils/table-copy-paste.ts
var rowClipboard = new NodeClipboard();
function isRowClipboardEmpty() {
  return rowClipboard.size() === 0;
}
function validateRowsToCopy(rows) {
  let commonRowSize = null;
  for (const row of rows) {
    const cells = row.getChildren().filter((n) => $isTableCellNode(n));
    let rowSize = 0;
    for (const cell of cells) {
      rowSize += cell.getColSpan() || 1;
      if (cell.getRowSpan() > 1) {
        throw Error("Cannot copy rows with merged cells");
      }
    }
    if (commonRowSize === null) {
      commonRowSize = rowSize;
    } else if (commonRowSize !== rowSize) {
      throw Error("Cannot copy rows with inconsistent sizes");
    }
  }
}
function validateRowsToPaste(rows, targetTable) {
  const tableColCount = new TableMap(targetTable).columnCount;
  for (const row of rows) {
    const cells = row.getChildren().filter((n) => $isTableCellNode(n));
    let rowSize = 0;
    for (const cell of cells) {
      rowSize += cell.getColSpan() || 1;
    }
    if (rowSize > tableColCount) {
      throw Error("Cannot paste rows that are wider than target table");
    }
    while (rowSize < tableColCount) {
      row.append($createTableCellNode());
      rowSize++;
    }
  }
}
function $cutSelectedRowsToClipboard() {
  const rows = $getTableRowsFromSelection($getSelection());
  validateRowsToCopy(rows);
  rowClipboard.set(...rows);
  for (const row of rows) {
    row.remove();
  }
}
function $copySelectedRowsToClipboard() {
  const rows = $getTableRowsFromSelection($getSelection());
  validateRowsToCopy(rows);
  rowClipboard.set(...rows);
}
function $pasteClipboardRowsBefore(editor) {
  const selection = $getSelection();
  const rows = $getTableRowsFromSelection(selection);
  const table2 = $getTableFromSelection(selection);
  const lastRow = rows[rows.length - 1];
  if (lastRow && table2) {
    const clipboardRows = rowClipboard.get(editor);
    validateRowsToPaste(clipboardRows, table2);
    for (const row of clipboardRows) {
      lastRow.insertBefore(row);
    }
  }
}
function $pasteClipboardRowsAfter(editor) {
  const selection = $getSelection();
  const rows = $getTableRowsFromSelection(selection);
  const table2 = $getTableFromSelection(selection);
  const lastRow = rows[rows.length - 1];
  if (lastRow && table2) {
    const clipboardRows = rowClipboard.get(editor).reverse();
    validateRowsToPaste(clipboardRows, table2);
    for (const row of clipboardRows) {
      lastRow.insertAfter(row);
    }
  }
}
var columnClipboard = [];
function setColumnClipboard(columns) {
  const newClipboards = columns.map((cells) => {
    const clipboard = new NodeClipboard();
    clipboard.set(...cells);
    return clipboard;
  });
  columnClipboard.splice(0, columnClipboard.length, ...newClipboards);
}
function isColumnClipboardEmpty() {
  return columnClipboard.length === 0;
}
function $getSelectionColumnRange(selection) {
  if ($isTableSelection(selection)) {
    const shape = selection.getShape();
    return { from: shape.fromX, to: shape.toX };
  }
  const cell = $getNodeFromSelection(selection, $isTableCellNode);
  const table2 = $getTableFromSelection(selection);
  if (!$isTableCellNode(cell) || !table2) {
    return null;
  }
  const map = new TableMap(table2);
  const range = map.getRangeForCell(cell);
  if (!range) {
    return null;
  }
  return { from: range.fromX, to: range.toX };
}
function $getTableColumnCellsFromSelection(range, table2) {
  const map = new TableMap(table2);
  const columns = [];
  for (let x = range.from; x <= range.to; x++) {
    const cells = map.getCellsInColumn(x);
    columns.push(cells);
  }
  return columns;
}
function validateColumnsToCopy(columns) {
  let commonColSize = null;
  for (const cells of columns) {
    let colSize = 0;
    for (const cell of cells) {
      colSize += cell.getRowSpan() || 1;
      if (cell.getColSpan() > 1) {
        throw Error("Cannot copy columns with merged cells");
      }
    }
    if (commonColSize === null) {
      commonColSize = colSize;
    } else if (commonColSize !== colSize) {
      throw Error("Cannot copy columns with inconsistent sizes");
    }
  }
}
function $cutSelectedColumnsToClipboard() {
  const selection = $getSelection();
  const range = $getSelectionColumnRange(selection);
  const table2 = $getTableFromSelection(selection);
  if (!range || !table2) {
    return;
  }
  const colWidths = table2.getColWidths();
  const columns = $getTableColumnCellsFromSelection(range, table2);
  validateColumnsToCopy(columns);
  setColumnClipboard(columns);
  for (const cells of columns) {
    for (const cell of cells) {
      cell.remove();
    }
  }
  const newWidths = [...colWidths].splice(range.from, range.to - range.from + 1);
  table2.setColWidths(newWidths);
}
function $copySelectedColumnsToClipboard() {
  const selection = $getSelection();
  const range = $getSelectionColumnRange(selection);
  const table2 = $getTableFromSelection(selection);
  if (!range || !table2) {
    return;
  }
  const columns = $getTableColumnCellsFromSelection(range, table2);
  validateColumnsToCopy(columns);
  setColumnClipboard(columns);
}
function validateColumnsToPaste(columns, targetTable) {
  const tableRowCount = new TableMap(targetTable).rowCount;
  for (const cells of columns) {
    let colSize = 0;
    for (const cell of cells) {
      colSize += cell.getRowSpan() || 1;
    }
    if (colSize > tableRowCount) {
      throw Error("Cannot paste columns that are taller than target table");
    }
    while (colSize < tableRowCount) {
      cells.push($createTableCellNode());
      colSize++;
    }
  }
}
function $pasteClipboardColumns(editor, isBefore) {
  const selection = $getSelection();
  const table2 = $getTableFromSelection(selection);
  const cells = $getTableCellsFromSelection(selection);
  const referenceCell = cells[isBefore ? 0 : cells.length - 1];
  if (!table2 || !referenceCell) {
    return;
  }
  const clipboardCols = columnClipboard.map((cb) => cb.get(editor));
  if (!isBefore) {
    clipboardCols.reverse();
  }
  validateColumnsToPaste(clipboardCols, table2);
  const map = new TableMap(table2);
  const cellRange = map.getRangeForCell(referenceCell);
  if (!cellRange) {
    return;
  }
  const colIndex = isBefore ? cellRange.fromX : cellRange.toX;
  const colWidths = table2.getColWidths();
  for (let y = 0; y < map.rowCount; y++) {
    const relCell = map.getCellAtPosition(colIndex, y);
    for (const cells2 of clipboardCols) {
      const newCell = cells2[y];
      if (isBefore) {
        relCell.insertBefore(newCell);
      } else {
        relCell.insertAfter(newCell);
      }
    }
  }
  const refWidth = colWidths[colIndex];
  const addedWidths = clipboardCols.map((_) => refWidth);
  colWidths.splice(isBefore ? colIndex : colIndex + 1, 0, ...addedWidths);
}
function $pasteClipboardColumnsBefore(editor) {
  $pasteClipboardColumns(editor, true);
}
function $pasteClipboardColumnsAfter(editor) {
  $pasteClipboardColumns(editor, false);
}

// resources/js/wysiwyg/ui/defaults/buttons/tables.ts
var neverActive = () => false;
var cellNotSelected = (selection) => !$selectionContainsNodeType(selection, $isTableCellNode);
var table = {
  label: "Table",
  icon: table_default
};
var tableProperties2 = {
  label: "Table properties",
  icon: table_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const table2 = $getTableFromSelection($getSelection());
      if ($isTableNode(table2)) {
        $showTablePropertiesForm(table2, context);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var clearTableFormatting = {
  label: "Clear table formatting",
  format: "long",
  action(context) {
    context.editor.update(() => {
      const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
      if (!$isTableCellNode(cell)) {
        return;
      }
      const table2 = $getParentOfType(cell, $isTableNode);
      if ($isTableNode(table2)) {
        $clearTableFormatting(table2);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var resizeTableToContents = {
  label: "Resize to contents",
  format: "long",
  action(context) {
    context.editor.update(() => {
      const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
      if (!$isTableCellNode(cell)) {
        return;
      }
      const table2 = $getParentOfType(cell, $isTableNode);
      if ($isTableNode(table2)) {
        $clearTableSizes(table2);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var deleteTable = {
  label: "Delete table",
  icon: table_delete_default,
  action(context) {
    context.editor.update(() => {
      const table2 = $getNodeFromSelection($getSelection(), $isTableNode);
      if (table2) {
        table2.remove();
      }
    });
  },
  isActive() {
    return false;
  }
};
var deleteTableMenuAction = {
  ...deleteTable,
  format: "long",
  isDisabled(selection) {
    return !$selectionContainsNodeType(selection, $isTableNode);
  }
};
var insertRowAbove = {
  label: "Insert row before",
  icon: table_insert_row_above_default,
  action(context) {
    context.editor.update(() => {
      $insertTableRow__EXPERIMENTAL(false);
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var insertRowBelow = {
  label: "Insert row after",
  icon: table_insert_row_below_default,
  action(context) {
    context.editor.update(() => {
      $insertTableRow__EXPERIMENTAL(true);
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var deleteRow = {
  label: "Delete row",
  icon: table_delete_row_default,
  action(context) {
    context.editor.update(() => {
      $deleteTableRow__EXPERIMENTAL();
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var rowProperties2 = {
  label: "Row properties",
  format: "long",
  action(context) {
    context.editor.getEditorState().read(() => {
      const rows = $getTableRowsFromSelection($getSelection());
      if ($isTableRowNode(rows[0])) {
        $showRowPropertiesForm(rows[0], context);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var cutRow = {
  label: "Cut row",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $cutSelectedRowsToClipboard();
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var copyRow = {
  label: "Copy row",
  format: "long",
  action(context) {
    context.editor.getEditorState().read(() => {
      try {
        $copySelectedRowsToClipboard();
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var pasteRowBefore = {
  label: "Paste row before",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $pasteClipboardRowsBefore(context.editor);
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: (selection) => cellNotSelected(selection) || isRowClipboardEmpty()
};
var pasteRowAfter = {
  label: "Paste row after",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $pasteClipboardRowsAfter(context.editor);
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: (selection) => cellNotSelected(selection) || isRowClipboardEmpty()
};
var cutColumn = {
  label: "Cut column",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $cutSelectedColumnsToClipboard();
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var copyColumn = {
  label: "Copy column",
  format: "long",
  action(context) {
    context.editor.getEditorState().read(() => {
      try {
        $copySelectedColumnsToClipboard();
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var pasteColumnBefore = {
  label: "Paste column before",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $pasteClipboardColumnsBefore(context.editor);
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: (selection) => cellNotSelected(selection) || isColumnClipboardEmpty()
};
var pasteColumnAfter = {
  label: "Paste column after",
  format: "long",
  action(context) {
    context.editor.update(() => {
      try {
        $pasteClipboardColumnsAfter(context.editor);
      } catch (e) {
        context.error(e);
      }
    });
  },
  isActive: neverActive,
  isDisabled: (selection) => cellNotSelected(selection) || isColumnClipboardEmpty()
};
var insertColumnBefore = {
  label: "Insert column before",
  icon: table_insert_column_before_default,
  action(context) {
    context.editor.update(() => {
      $insertTableColumn__EXPERIMENTAL(false);
    });
  },
  isActive() {
    return false;
  }
};
var insertColumnAfter = {
  label: "Insert column after",
  icon: table_insert_column_after_default,
  action(context) {
    context.editor.update(() => {
      $insertTableColumn__EXPERIMENTAL(true);
    });
  },
  isActive() {
    return false;
  }
};
var deleteColumn = {
  label: "Delete column",
  icon: table_delete_column_default,
  action(context) {
    context.editor.update(() => {
      $deleteTableColumn__EXPERIMENTAL();
    });
  },
  isActive() {
    return false;
  }
};
var cellProperties2 = {
  label: "Cell properties",
  format: "long",
  action(context) {
    context.editor.getEditorState().read(() => {
      const cell = $getNodeFromSelection($getSelection(), $isTableCellNode);
      if ($isTableCellNode(cell)) {
        $showCellPropertiesForm(cell, context);
      }
    });
  },
  isActive: neverActive,
  isDisabled: cellNotSelected
};
var mergeCells = {
  label: "Merge cells",
  format: "long",
  action(context) {
    context.editor.update(() => {
      const selection = $getSelection();
      if ($isTableSelection(selection)) {
        $mergeTableCellsInSelection(selection);
      }
    });
  },
  isActive: neverActive,
  isDisabled(selection) {
    return !$isTableSelection(selection);
  }
};
var splitCell = {
  label: "Split cell",
  format: "long",
  action(context) {
    context.editor.update(() => {
      $unmergeCell();
    });
  },
  isActive: neverActive,
  isDisabled(selection) {
    const cell = $getNodeFromSelection(selection, $isTableCellNode);
    if (cell) {
      const merged = cell.getRowSpan() > 1 || cell.getColSpan() > 1;
      return !merged;
    }
    return true;
  }
};

// resources/icons/editor/undo.svg
var undo_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M280-200v-80h284q63 0 109.5-40T720-420q0-60-46.5-100T564-560H312l104 104-56 56-200-200 200-200 56 56-104 104h252q97 0 166.5 63T800-420q0 94-69.5 157T564-200H280Z"/></svg>';

// resources/icons/editor/redo.svg
var redo_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" ><path d="M396-200q-97 0-166.5-63T160-420q0-94 69.5-157T396-640h252L544-744l56-56 200 200-200 200-56-56 104-104H396q-63 0-109.5 40T240-420q0 60 46.5 100T396-280h284v80H396Z"/></svg>';

// resources/icons/editor/source-view.svg
var source_view_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m384-336 56-58-86-86 86-86-56-58-144 144 144 144Zm192 0 144-144-144-144-56 58 86 86-86 86 56 58ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm280-590q13 0 21.5-8.5T510-820q0-13-8.5-21.5T480-850q-13 0-21.5 8.5T450-820q0 13 8.5 21.5T480-790ZM200-200v-560 560Z"/></svg>';

// resources/icons/editor/fullscreen.svg
var fullscreen_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-200h80v120h120v80H120Zm520 0v-80h120v-120h80v200H640ZM120-640v-200h200v80H200v120h-80Zm640 0v-120H640v-80h200v200h-80Z"/></svg>';

// resources/icons/editor/about.svg
var about_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>';

// resources/js/wysiwyg/ui/defaults/buttons/controls.ts
var undo2 = {
  label: "Undo",
  icon: undo_default,
  action(context) {
    context.editor.dispatchCommand(UNDO_COMMAND, void 0);
    context.manager.triggerFutureStateRefresh();
  },
  isActive(selection) {
    return false;
  },
  setup(context, button) {
    button.toggleDisabled(true);
    context.editor.registerCommand(CAN_UNDO_COMMAND, (payload) => {
      button.toggleDisabled(!payload);
      return false;
    }, COMMAND_PRIORITY_LOW);
  }
};
var redo2 = {
  label: "Redo",
  icon: redo_default,
  action(context) {
    context.editor.dispatchCommand(REDO_COMMAND, void 0);
    context.manager.triggerFutureStateRefresh();
  },
  isActive(selection) {
    return false;
  },
  setup(context, button) {
    button.toggleDisabled(true);
    context.editor.registerCommand(CAN_REDO_COMMAND, (payload) => {
      button.toggleDisabled(!payload);
      return false;
    }, COMMAND_PRIORITY_LOW);
  }
};
var source = {
  label: "Source code",
  icon: source_view_default,
  async action(context) {
    const modal = context.manager.createModal("source");
    const source3 = await getEditorContentAsHtml(context.editor);
    modal.show({ source: source3 });
  },
  isActive() {
    return false;
  }
};
var fullscreen = {
  label: "Fullscreen",
  icon: fullscreen_default,
  async action(context, button) {
    const isFullScreen = context.containerDOM.classList.contains("fullscreen");
    context.containerDOM.classList.toggle("fullscreen", !isFullScreen);
    context.containerDOM.closest("body").classList.toggle("editor-is-fullscreen", !isFullScreen);
    button.setActiveState(!isFullScreen);
  },
  isActive(selection, context) {
    return context.containerDOM.classList.contains("fullscreen");
  }
};
var about = {
  label: "About the editor",
  icon: about_default,
  async action(context, button) {
    const modal = context.manager.createModal("about");
    modal.show({});
  },
  isActive(selection, context) {
    return false;
  }
};

// resources/js/wysiwyg/ui/defaults/buttons/block-formats.ts
function buildCalloutButton(category, name) {
  return {
    label: name,
    action(context) {
      context.editor.update(() => {
        $toggleSelectionBlockNodeType(
          (node) => $isCalloutNodeOfCategory(node, category),
          () => $createCalloutNode(category)
        );
      });
    },
    isActive(selection) {
      return $selectionContainsNodeType(selection, (node) => $isCalloutNodeOfCategory(node, category));
    }
  };
}
var infoCallout = buildCalloutButton("info", "Info");
var dangerCallout = buildCalloutButton("danger", "Danger");
var warningCallout = buildCalloutButton("warning", "Warning");
var successCallout = buildCalloutButton("success", "Success");
var isHeaderNodeOfTag = (node, tag) => {
  return $isHeadingNode(node) && node.getTag() === tag;
};
function buildHeaderButton(tag, name) {
  return {
    label: name,
    action(context) {
      toggleSelectionAsHeading(context.editor, tag);
    },
    isActive(selection) {
      return $selectionContainsNodeType(selection, (node) => isHeaderNodeOfTag(node, tag));
    }
  };
}
var h2 = buildHeaderButton("h2", "Large Header");
var h3 = buildHeaderButton("h3", "Medium Header");
var h4 = buildHeaderButton("h4", "Small Header");
var h5 = buildHeaderButton("h5", "Tiny Header");
var blockquote = {
  label: "Blockquote",
  action(context) {
    toggleSelectionAsBlockquote(context.editor);
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isQuoteNode);
  }
};
var paragraph = {
  label: "Paragraph",
  action(context) {
    toggleSelectionAsParagraph(context.editor);
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isParagraphNode);
  }
};

// resources/icons/editor/bold.svg
var bold_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M272-200v-560h221q65 0 120 40t55 111q0 51-23 78.5T602-491q25 11 55.5 41t30.5 90q0 89-65 124.5T501-200H272Zm121-112h104q48 0 58.5-24.5T566-372q0-11-10.5-35.5T494-432H393v120Zm0-228h93q33 0 48-17t15-38q0-24-17-39t-44-15h-95v109Z"/></svg>';

// resources/icons/editor/italic.svg
var italic_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M200-200v-100h160l120-360H320v-100h400v100H580L460-300h140v100H200Z"/></svg>';

// resources/icons/editor/underlined.svg
var underlined_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M200-120v-80h560v80H200Zm280-160q-101 0-157-63t-56-167v-330h103v336q0 56 28 91t82 35q54 0 82-35t28-91v-336h103v330q0 104-56 167t-157 63Z"/></svg>';

// resources/icons/editor/text-color.svg
var text_color_default = '<svg version="1.1" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg"><path class="editor-icon-color-bar" d="m80-3e-6v-160h800v160z"/><path d="m220-280 210-560h100l210 560h-96l-50-144h-226l-52 144zm176-224h168l-82-232h-4z"/></svg>\n';

// resources/icons/editor/highlighter.svg
var highlighter_default = '<svg version="1.1" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg"><path class="editor-icon-color-bar" d="m80-2e-6v-160h800v160z"/><path d="m584-480-104-104-160 160 103 104zm-47-160 103 103 160-159-104-104zm-84-29 216 216-189 190c-16 16-34.833 24-56.5 24s-40.5-8-56.5-24l-27 23h-200l126-125c-16-16-24.333-35.167-25-57.5s7-41.5 23-57.5zm0 0 187-187c16-16 34.833-24 56.5-24s40.5 8 56.5 24l104 103c16 16 24 34.833 24 56.5s-8 40.5-24 56.5l-188 187z"/></svg>';

// resources/icons/editor/strikethrough.svg
var strikethrough_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M80-400v-80h800v80H80Zm340-160v-120H200v-120h560v120H540v120H420Zm0 400v-160h120v160H420Z"/></svg>';

// resources/icons/editor/superscript.svg
var superscript_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M760-600v-80q0-17 11.5-28.5T800-720h80v-40H760v-40h120q17 0 28.5 11.5T920-760v40q0 17-11.5 28.5T880-680h-80v40h120v40H760ZM235-160l185-291-172-269h106l124 200h4l123-200h107L539-451l186 291H618L482-377h-4L342-160H235Z"/></svg>';

// resources/icons/editor/subscript.svg
var subscript_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M760-160v-80q0-17 11.5-28.5T800-280h80v-40H760v-40h120q17 0 28.5 11.5T920-320v40q0 17-11.5 28.5T880-240h-80v40h120v40H760Zm-525-80 185-291-172-269h106l124 200h4l123-200h107L539-531l186 291H618L482-457h-4L342-240H235Z"/></svg>';

// resources/icons/editor/code.svg
var code_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M320-240 80-480l240-240 57 57-184 184 183 183-56 56Zm320 0-57-57 184-184-183-183 56-56 240 240-240 240Z"/></svg>';

// resources/icons/editor/format-clear.svg
var format_clear_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m528-546-93-93-121-121h486v120H568l-40 94ZM792-56 460-388l-80 188H249l119-280L56-792l56-56 736 736-56 56Z"/></svg>';

// resources/js/wysiwyg/ui/defaults/buttons/inline-formats.ts
function buildFormatButton(label, format, icon) {
  return {
    label,
    icon,
    action(context) {
      context.editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
    },
    isActive(selection) {
      return $selectionContainsTextFormat(selection, format);
    }
  };
}
var bold = buildFormatButton("Bold", "bold", bold_default);
var italic = buildFormatButton("Italic", "italic", italic_default);
var underline = buildFormatButton("Underline", "underline", underlined_default);
var textColor = { label: "Text color", icon: text_color_default };
var highlightColor = { label: "Highlight color", icon: highlighter_default };
function colorAction(context, property, color) {
  context.editor.update(() => {
    const selection = $getSelection();
    if (selection) {
      $patchStyleText(selection, { [property]: color || null });
    }
  });
}
var textColorAction = (color, context) => colorAction(context, "color", color);
var highlightColorAction = (color, context) => colorAction(context, "background-color", color);
var strikethrough = buildFormatButton("Strikethrough", "strikethrough", strikethrough_default);
var superscript = buildFormatButton("Superscript", "superscript", superscript_default);
var subscript = buildFormatButton("Subscript", "subscript", subscript_default);
var code = buildFormatButton("Inline code", "code", code_default);
var clearFormating = {
  label: "Clear formatting",
  icon: format_clear_default,
  action(context) {
    context.editor.update(() => {
      const selection = $getSelection();
      for (const node of selection?.getNodes() || []) {
        if ($isTextNode(node)) {
          node.setFormat(0);
          node.setStyle("");
        }
      }
    });
  },
  isActive() {
    return false;
  }
};

// resources/icons/editor/align-left.svg
var align_left_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-80h720v80H120Zm0-160v-80h480v80H120Zm0-160v-80h720v80H120Zm0-160v-80h480v80H120Zm0-160v-80h720v80H120Z"/></svg>';

// resources/icons/editor/align-center.svg
var align_center_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-80h720v80H120Zm160-160v-80h400v80H280ZM120-440v-80h720v80H120Zm160-160v-80h400v80H280ZM120-760v-80h720v80H120Z"/></svg>';

// resources/icons/editor/align-right.svg
var align_right_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-760v-80h720v80H120Zm240 160v-80h480v80H360ZM120-440v-80h720v80H120Zm240 160v-80h480v80H360ZM120-120v-80h720v80H120Z"/></svg>';

// resources/icons/editor/align-justify.svg
var align_justify_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-80h720v80H120Zm0-160v-80h720v80H120Zm0-160v-80h720v80H120Zm0-160v-80h720v80H120Zm0-160v-80h720v80H120Z"/></svg>';

// resources/icons/editor/direction-ltr.svg
var direction_ltr_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-800v400q0 17-11.5 28.5T400-360q-17 0-28.5-11.5T360-400v-160q-66 0-113-47t-47-113q0-66 47-113t113-47h280q17 0 28.5 11.5T680-840q0 17-11.5 28.5T640-800h-40v400q0 17-11.5 28.5T560-360q-17 0-28.5-11.5T520-400v-400h-80Zm-80 160v-160q-33 0-56.5 23.5T280-720q0 33 23.5 56.5T360-640Zm0-80Zm328 520H160q-17 0-28.5-11.5T120-240q0-17 11.5-28.5T160-280h528l-36-36q-11-11-11-28t11-28q11-11 28-11t28 11l104 104q12 12 12 28t-12 28L708-108q-11 11-28 11t-28-11q-11-11-11-28t11-28l36-36Z"/></svg>';

// resources/icons/editor/direction-rtl.svg
var direction_rtl_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M440-800v400q0 17-11.5 28.5T400-360q-17 0-28.5-11.5T360-400v-160q-66 0-113-47t-47-113q0-66 47-113t113-47h280q17 0 28.5 11.5T680-840q0 17-11.5 28.5T640-800h-40v400q0 17-11.5 28.5T560-360q-17 0-28.5-11.5T520-400v-400h-80ZM272-200l36 36q11 11 11 28t-11 28q-11 11-28 11t-28-11L148-212q-12-12-12-28t12-28l104-104q11-11 28-11t28 11q11 11 11 28t-11 28l-36 36h528q17 0 28.5 11.5T840-240q0 17-11.5 28.5T800-200H272Zm88-440v-160q-33 0-56.5 23.5T280-720q0 33 23.5 56.5T360-640Zm0-80Z"/></svg>';

// resources/js/wysiwyg/ui/defaults/buttons/alignments.ts
function setAlignmentForSelection(context, alignment) {
  const selection = getLastSelection2(context.editor);
  const selectionNodes = selection?.getNodes() || [];
  if (selectionNodes.length === 1 && $isElementNode(selectionNodes[0]) && selectionNodes[0].isInline() && nodeHasAlignment(selectionNodes[0])) {
    selectionNodes[0].setAlignment(alignment);
    $selectSingleNode(selectionNodes[0]);
    context.manager.triggerFutureStateRefresh();
    return;
  }
  const elements = $getBlockElementNodesInSelection(selection);
  const alignmentNodes = elements.filter((n) => nodeHasAlignment(n));
  const allAlreadyAligned = alignmentNodes.every((n) => n.getAlignment() === alignment);
  const newAlignment = allAlreadyAligned ? "" : alignment;
  for (const node of alignmentNodes) {
    node.setAlignment(newAlignment);
  }
  context.manager.triggerFutureStateRefresh();
}
function setDirectionForSelection(context, direction) {
  const selection = getLastSelection2(context.editor);
  const elements = $getBlockElementNodesInSelection(selection);
  for (const node of elements) {
    node.setDirection(direction);
  }
  context.manager.triggerFutureStateRefresh();
}
var alignLeft = {
  label: "Align left",
  icon: align_left_default,
  action(context) {
    context.editor.update(() => setAlignmentForSelection(context, "left"));
  },
  isActive(selection) {
    return $selectionContainsAlignment(selection, "left");
  }
};
var alignCenter = {
  label: "Align center",
  icon: align_center_default,
  action(context) {
    context.editor.update(() => setAlignmentForSelection(context, "center"));
  },
  isActive(selection) {
    return $selectionContainsAlignment(selection, "center");
  }
};
var alignRight = {
  label: "Align right",
  icon: align_right_default,
  action(context) {
    context.editor.update(() => setAlignmentForSelection(context, "right"));
  },
  isActive(selection) {
    return $selectionContainsAlignment(selection, "right");
  }
};
var alignJustify = {
  label: "Justify",
  icon: align_justify_default,
  action(context) {
    context.editor.update(() => setAlignmentForSelection(context, "justify"));
  },
  isActive(selection) {
    return $selectionContainsAlignment(selection, "justify");
  }
};
var directionLTR = {
  label: "Left to right",
  icon: direction_ltr_default,
  action(context) {
    context.editor.update(() => setDirectionForSelection(context, "ltr"));
  },
  isActive(selection) {
    return $selectionContainsDirection(selection, "ltr");
  }
};
var directionRTL = {
  label: "Right to left",
  icon: direction_rtl_default,
  action(context) {
    context.editor.update(() => setDirectionForSelection(context, "rtl"));
  },
  isActive(selection) {
    return $selectionContainsDirection(selection, "rtl");
  }
};

// resources/icons/editor/list-bullet.svg
var list_bullet_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M360-200v-80h480v80H360Zm0-240v-80h480v80H360Zm0-240v-80h480v80H360ZM200-160q-33 0-56.5-23.5T120-240q0-33 23.5-56.5T200-320q33 0 56.5 23.5T280-240q0 33-23.5 56.5T200-160Zm0-240q-33 0-56.5-23.5T120-480q0-33 23.5-56.5T200-560q33 0 56.5 23.5T280-480q0 33-23.5 56.5T200-400Zm0-240q-33 0-56.5-23.5T120-720q0-33 23.5-56.5T200-800q33 0 56.5 23.5T280-720q0 33-23.5 56.5T200-640Z"/></svg>';

// resources/icons/editor/list-numbered.svg
var list_numbered_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-80v-60h100v-30h-60v-60h60v-30H120v-60h120q17 0 28.5 11.5T280-280v40q0 17-11.5 28.5T240-200q17 0 28.5 11.5T280-160v40q0 17-11.5 28.5T240-80H120Zm0-280v-110q0-17 11.5-28.5T160-510h60v-30H120v-60h120q17 0 28.5 11.5T280-560v70q0 17-11.5 28.5T240-450h-60v30h100v60H120Zm60-280v-180h-60v-60h120v240h-60Zm180 440v-80h480v80H360Zm0-240v-80h480v80H360Zm0-240v-80h480v80H360Z"/></svg>';

// resources/icons/editor/list-check.svg
var list_check_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M222-200 80-342l56-56 85 85 170-170 56 57-225 226Zm0-320L80-662l56-56 85 85 170-170 56 57-225 226Zm298 240v-80h360v80H520Zm0-320v-80h360v80H520Z"/></svg>';

// resources/icons/editor/indent-increase.svg
var indent_increase_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-80h720v80H120Zm320-160v-80h400v80H440Zm0-160v-80h400v80H440Zm0-160v-80h400v80H440ZM120-760v-80h720v80H120Zm0 440v-320l160 160-160 160Z"/></svg>';

// resources/icons/editor/indent-decrease.svg
var indent_decrease_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M120-120v-80h720v80H120Zm320-160v-80h400v80H440Zm0-160v-80h400v80H440Zm0-160v-80h400v80H440ZM120-760v-80h720v80H120Zm160 440L120-480l160-160v320Z"/></svg>';

// resources/js/wysiwyg/ui/defaults/buttons/lists.ts
function buildListButton(label, type, icon) {
  return {
    label,
    icon,
    action(context) {
      toggleSelectionAsList(context.editor, type);
    },
    isActive(selection) {
      return $selectionContainsNodeType(selection, (node) => {
        return $isListNode(node) && node.getListType() === type;
      });
    }
  };
}
var bulletList = buildListButton("Bullet list", "bullet", list_bullet_default);
var numberList = buildListButton("Numbered list", "number", list_numbered_default);
var taskList = buildListButton("Task list", "check", list_check_default);
var indentIncrease = {
  label: "Increase indent",
  icon: indent_increase_default,
  action(context) {
    context.editor.update(() => {
      $setInsetForSelection(context.editor, 40);
    });
  },
  isActive() {
    return false;
  }
};
var indentDecrease = {
  label: "Decrease indent",
  icon: indent_decrease_default,
  action(context) {
    context.editor.update(() => {
      $setInsetForSelection(context.editor, -40);
    });
  },
  isActive() {
    return false;
  }
};

// resources/icons/editor/link.svg
var link_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M680-160v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM440-280H280q-83 0-141.5-58.5T80-480q0-83 58.5-141.5T280-680h160v80H280q-50 0-85 35t-35 85q0 50 35 85t85 35h160v80ZM320-440v-80h320v80H320Zm560-40h-80q0-50-35-85t-85-35H520v-80h160q83 0 141.5 58.5T880-480Z"/></svg>';

// resources/icons/editor/unlink.svg
var unlink_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m770-302-60-62q40-11 65-42.5t25-73.5q0-50-35-85t-85-35H520v-80h160q83 0 141.5 58.5T880-480q0 57-29.5 105T770-302ZM634-440l-80-80h86v80h-6ZM792-56 56-792l56-56 736 736-56 56ZM440-280H280q-83 0-141.5-58.5T80-480q0-69 42-123t108-71l74 74h-24q-50 0-85 35t-35 85q0 50 35 85t85 35h160v80ZM320-440v-80h65l79 80H320Z"/></svg>';

// resources/icons/editor/image.svg
var image_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h360v80H200v560h560v-360h80v360q0 33-23.5 56.5T760-120H200Zm480-480v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80ZM240-280h480L570-480 450-320l-90-120-120 160Zm-40-480v560-560Z"/></svg>';

// resources/icons/editor/horizontal-rule.svg
var horizontal_rule_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M160-440v-80h640v80H160Z"/></svg>';

// resources/icons/editor/code-block.svg
var code_block_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m384-336 56-57-87-87 87-87-56-57-144 144 144 144Zm192 0 144-144-144-144-56 57 87 87-87 87 56 57ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>';

// resources/icons/edit.svg
var edit_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83 3.75 3.75z"/><path fill="none" d="M0 0h24v24H0z"/></svg>';

// resources/icons/editor/diagram.svg
var diagram_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-60q-63 0-106.5-43.5T330-210q0-52 31-91.5t79-53.5v-85H200v-160H100v-280h280v280H280v80h400v-85q-48-14-79-53.5T570-750q0-63 43.5-106.5T720-900q63 0 106.5 43.5T870-750q0 52-31 91.5T760-605v165H520v85q48 14 79 53.5t31 91.5q0 63-43.5 106.5T480-60Zm240-620q29 0 49.5-20.5T790-750q0-29-20.5-49.5T720-820q-29 0-49.5 20.5T650-750q0 29 20.5 49.5T720-680Zm-540 0h120v-120H180v120Zm300 540q29 0 49.5-20.5T550-210q0-29-20.5-49.5T480-280q-29 0-49.5 20.5T410-210q0 29 20.5 49.5T480-140ZM240-740Zm480-10ZM480-210Z"/></svg>';

// resources/icons/editor/details.svg
var details_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-480H200v480Zm80-280v-80h400v80H280Zm0 160v-80h240v80H280Z"/></svg>';

// resources/icons/editor/details-toggle.svg
var details_toggle_default = '<svg viewbox="0 0 24 24"><path d="M8.12 19.3c.39.39 1.02.39 1.41 0L12 16.83l2.47 2.47c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41l-3.17-3.17c-.39-.39-1.02-.39-1.41 0l-3.17 3.17c-.4.38-.4 1.02-.01 1.41zm7.76-14.6c-.39-.39-1.02-.39-1.41 0L12 7.17 9.53 4.7c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.03 0 1.42l3.17 3.17c.39.39 1.02.39 1.41 0l3.17-3.17c.4-.39.4-1.03.01-1.42z"/></svg>\n';

// resources/icons/tag.svg
var tag_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m21.41 11.41-8.83-8.83c-.37-.37-.88-.58-1.41-.58H4c-1.1 0-2 .9-2 2v7.17c0 .53.21 1.04.59 1.41l8.83 8.83c.78.78 2.05.78 2.83 0l7.17-7.17c.78-.78.78-2.04-.01-2.83M6.5 8C5.67 8 5 7.33 5 6.5S5.67 5 6.5 5 8 5.67 8 6.5 7.33 8 6.5 8"/></svg>';

// resources/icons/editor/media.svg
var media_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m380-300 280-180-280-180v360ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>';

// resources/js/wysiwyg/ui/defaults/buttons/objects.ts
var link2 = {
  label: "Insert/edit link",
  icon: link_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const selectedLink = $getNodeFromSelection($getSelection(), $isLinkNode);
      $showLinkForm(selectedLink, context);
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isLinkNode);
  }
};
var unlink = {
  label: "Remove link",
  icon: unlink_default,
  action(context) {
    context.editor.update(() => {
      const selection = getLastSelection2(context.editor);
      const selectedLink = $getNodeFromSelection(selection, $isLinkNode);
      if (selectedLink) {
        const contents = selectedLink.getChildren().reverse();
        for (const child of contents) {
          selectedLink.insertAfter(child);
        }
        selectedLink.remove();
        contents[contents.length - 1].selectStart();
        context.manager.triggerFutureStateRefresh();
      }
    });
  },
  isActive(selection) {
    return false;
  }
};
var image2 = {
  label: "Insert/Edit Image",
  icon: image_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const selection = getLastSelection2(context.editor);
      const selectedImage = $getNodeFromSelection(selection, $isImageNode);
      if (selectedImage) {
        $showImageForm(selectedImage, context);
        return;
      }
      showImageManager((image3) => {
        context.editor.update(() => {
          const link3 = $createLinkedImageNodeFromImageData(image3);
          $insertNodes([link3]);
          link3.select();
        });
      });
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isImageNode);
  }
};
var horizontalRule = {
  label: "Insert horizontal line",
  icon: horizontal_rule_default,
  action(context) {
    context.editor.update(() => {
      $insertNewBlockNodeAtSelection($createHorizontalRuleNode(), false);
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isHorizontalRuleNode);
  }
};
var codeBlock = {
  label: "Insert code block",
  icon: code_block_default,
  action(context) {
    formatCodeBlock(context.editor);
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isCodeBlockNode);
  }
};
var editCodeBlock = Object.assign({}, codeBlock, {
  label: "Edit code block",
  icon: edit_default
});
var diagram = {
  label: "Insert/edit drawing",
  icon: diagram_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const selection = getLastSelection2(context.editor);
      const diagramNode = $getNodeFromSelection(selection, $isDiagramNode);
      if (diagramNode === null) {
        context.editor.update(() => {
          const diagram2 = $createDiagramNode();
          $insertNewBlockNodeAtSelection(diagram2, true);
          $openDrawingEditorForNode(context, diagram2);
          diagram2.selectStart();
        });
      } else {
        $openDrawingEditorForNode(context, diagramNode);
      }
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isDiagramNode);
  }
};
var diagramManager = {
  label: "Drawing manager",
  action(context) {
    showDiagramManagerForInsert(context);
  },
  isActive() {
    return false;
  }
};
var media2 = {
  label: "Insert/edit media",
  icon: media_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const selection = $getSelection();
      const selectedNode = $getNodeFromSelection(selection, $isMediaNode);
      $showMediaForm(selectedNode, context);
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isMediaNode);
  }
};
var details2 = {
  label: "Insert collapsible block",
  icon: details_default,
  action(context) {
    context.editor.update(() => {
      const selection = $getSelection();
      const detailsNode = $createDetailsNode();
      const selectionNodes = selection?.getNodes() || [];
      const topLevels = selectionNodes.map((n) => n.getTopLevelElement()).filter((n) => n !== null);
      const uniqueTopLevels = [...new Set(topLevels)];
      if (uniqueTopLevels.length > 0) {
        uniqueTopLevels[0].insertAfter(detailsNode);
      } else {
        $getRoot().append(detailsNode);
      }
      for (const node of uniqueTopLevels) {
        detailsNode.append(node);
      }
    });
  },
  isActive(selection) {
    return $selectionContainsNodeType(selection, $isDetailsNode);
  }
};
var detailsEditLabel = {
  label: "Edit label",
  icon: tag_default,
  action(context) {
    context.editor.getEditorState().read(() => {
      const details3 = $getNodeFromSelection($getSelection(), $isDetailsNode);
      if ($isDetailsNode(details3)) {
        $showDetailsForm(details3, context);
      }
    });
  },
  isActive(selection) {
    return false;
  }
};
var detailsToggle = {
  label: "Toggle open/closed",
  icon: details_toggle_default,
  action(context) {
    context.editor.update(() => {
      const details3 = $getNodeFromSelection($getSelection(), $isDetailsNode);
      if ($isDetailsNode(details3)) {
        details3.setOpen(!details3.getOpen());
        context.manager.triggerLayoutUpdate();
      }
    });
  },
  isActive(selection) {
    return false;
  }
};
var detailsUnwrap = {
  label: "Unwrap",
  icon: table_delete_default,
  action(context) {
    context.editor.update(() => {
      const details3 = $getNodeFromSelection($getSelection(), $isDetailsNode);
      if ($isDetailsNode(details3)) {
        const children = details3.getChildren();
        for (const child of children) {
          details3.insertBefore(child);
        }
        details3.remove();
        context.manager.triggerLayoutUpdate();
      }
    });
  },
  isActive(selection) {
    return false;
  }
};

// resources/icons/caret-down-large.svg
var caret_down_large_default = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m2 6.9159 10 10.168 10-10.168z" stroke-width="2.0168"/></svg>\n';

// resources/js/wysiwyg/ui/framework/blocks/button-with-menu.ts
var EditorButtonWithMenu = class extends EditorContainerUiElement {
  constructor(button, menuItems) {
    super([button]);
    __publicField(this, "button");
    __publicField(this, "dropdownButton");
    this.button = button;
    this.dropdownButton = new EditorDropdownButton({
      button: { label: "Menu", icon: caret_down_large_default },
      showOnHover: false,
      direction: "vertical",
      showAside: false
    }, menuItems);
    this.addChildren(this.dropdownButton);
  }
  buildDOM() {
    return el("div", {
      class: "editor-button-with-menu-container"
    }, [
      this.button.getDOMElement(),
      this.dropdownButton.getDOMElement()
    ]);
  }
};

// resources/js/wysiwyg/ui/framework/blocks/separator.ts
var EditorSeparator = class extends EditorUiElement {
  buildDOM() {
    return el("div", {
      class: "editor-separator"
    });
  }
};

// resources/js/wysiwyg/ui/defaults/toolbars.ts
function getMainEditorFullToolbar(context) {
  const inRtlMode = context.manager.getDefaultDirection() === "rtl";
  return new EditorSimpleClassContainer("editor-toolbar-main", [
    // History state
    new EditorOverflowContainer(2, [
      new EditorButton(undo2),
      new EditorButton(redo2)
    ]),
    // Block formats
    new EditorFormatMenu([
      new FormatPreviewButton(el("h2"), h2),
      new FormatPreviewButton(el("h3"), h3),
      new FormatPreviewButton(el("h4"), h4),
      new FormatPreviewButton(el("h5"), h5),
      new FormatPreviewButton(el("blockquote"), blockquote),
      new FormatPreviewButton(el("p"), paragraph),
      new EditorDropdownButton({ button: { label: "Callouts", format: "long" }, showOnHover: true, direction: "vertical" }, [
        new FormatPreviewButton(el("p", { class: "callout info" }), infoCallout),
        new FormatPreviewButton(el("p", { class: "callout success" }), successCallout),
        new FormatPreviewButton(el("p", { class: "callout warning" }), warningCallout),
        new FormatPreviewButton(el("p", { class: "callout danger" }), dangerCallout)
      ])
    ]),
    // Inline formats
    new EditorOverflowContainer(6, [
      new EditorButton(bold),
      new EditorButton(italic),
      new EditorButton(underline),
      new EditorDropdownButton({ button: new EditorColorButton(textColor, "color") }, [
        new EditorColorPicker(textColorAction)
      ]),
      new EditorDropdownButton({ button: new EditorColorButton(highlightColor, "background-color") }, [
        new EditorColorPicker(highlightColorAction)
      ]),
      new EditorButton(strikethrough),
      new EditorButton(superscript),
      new EditorButton(subscript),
      new EditorButton(code),
      new EditorButton(clearFormating)
    ]),
    // Alignment
    new EditorOverflowContainer(6, [
      new EditorButton(alignLeft),
      new EditorButton(alignCenter),
      new EditorButton(alignRight),
      new EditorButton(alignJustify),
      inRtlMode ? new EditorButton(directionLTR) : null,
      inRtlMode ? new EditorButton(directionRTL) : null
    ].filter((x) => x !== null)),
    // Lists
    new EditorOverflowContainer(3, [
      new EditorButton(bulletList),
      new EditorButton(numberList),
      new EditorButton(taskList),
      new EditorButton(indentDecrease),
      new EditorButton(indentIncrease)
    ]),
    // Insert types
    new EditorOverflowContainer(4, [
      new EditorButton(link2),
      new EditorDropdownButton({ button: table, direction: "vertical", showAside: false }, [
        new EditorDropdownButton({ button: { label: "Insert", format: "long" }, showOnHover: true, showAside: true }, [
          new EditorTableCreator()
        ]),
        new EditorSeparator(),
        new EditorDropdownButton({ button: { label: "Cell", format: "long" }, direction: "vertical", showOnHover: true }, [
          new EditorButton(cellProperties2),
          new EditorButton(mergeCells),
          new EditorButton(splitCell)
        ]),
        new EditorDropdownButton({ button: { label: "Row", format: "long" }, direction: "vertical", showOnHover: true }, [
          new EditorButton({ ...insertRowAbove, format: "long" }),
          new EditorButton({ ...insertRowBelow, format: "long" }),
          new EditorButton({ ...deleteRow, format: "long" }),
          new EditorButton(rowProperties2),
          new EditorSeparator(),
          new EditorButton(cutRow),
          new EditorButton(copyRow),
          new EditorButton(pasteRowBefore),
          new EditorButton(pasteRowAfter)
        ]),
        new EditorDropdownButton({ button: { label: "Column", format: "long" }, direction: "vertical", showOnHover: true }, [
          new EditorButton({ ...insertColumnBefore, format: "long" }),
          new EditorButton({ ...insertColumnAfter, format: "long" }),
          new EditorButton({ ...deleteColumn, format: "long" }),
          new EditorSeparator(),
          new EditorButton(cutColumn),
          new EditorButton(copyColumn),
          new EditorButton(pasteColumnBefore),
          new EditorButton(pasteColumnAfter)
        ]),
        new EditorSeparator(),
        new EditorButton({ ...tableProperties2, format: "long" }),
        new EditorButton(clearTableFormatting),
        new EditorButton(resizeTableToContents),
        new EditorButton(deleteTableMenuAction)
      ]),
      new EditorButton(image2),
      new EditorButton(horizontalRule),
      new EditorButton(codeBlock),
      new EditorButtonWithMenu(
        new EditorButton(diagram),
        [new EditorButton(diagramManager)]
      ),
      new EditorButton(media2),
      new EditorButton(details2)
    ]),
    // Meta elements
    new EditorOverflowContainer(3, [
      new EditorButton(source),
      new EditorButton(about),
      new EditorButton(fullscreen)
      // Test
      // new EditorButton({
      //     label: 'Test button',
      //     action(context: EditorUiContext) {
      //         context.editor.update(() => {
      //             // Do stuff
      //         });
      //     },
      //     isActive() {
      //         return false;
      //     }
      // })
    ])
  ]);
}
function getBasicEditorToolbar(context) {
  return new EditorSimpleClassContainer("editor-toolbar-main", [
    new EditorButton(bold),
    new EditorButton(italic),
    new EditorButton(link2),
    new EditorButton(bulletList),
    new EditorButton(numberList)
  ]);
}
var contextToolbars = {
  image: {
    selector: "img:not([drawio-diagram] img)",
    content: () => [new EditorButton(image2)]
  },
  media: {
    selector: ".editor-media-wrap",
    content: () => [new EditorButton(media2)]
  },
  link: {
    selector: "a",
    content() {
      return [
        new EditorButton(link2),
        new EditorButton(unlink)
      ];
    },
    displayTargetLocator(originalTarget) {
      const image3 = originalTarget.querySelector("img");
      return image3 || originalTarget;
    }
  },
  code: {
    selector: ".editor-code-block-wrap",
    content: () => [new EditorButton(editCodeBlock)]
  },
  table: {
    selector: "td,th",
    content() {
      return [
        new EditorOverflowContainer(2, [
          new EditorButton(tableProperties2),
          new EditorButton(deleteTable)
        ]),
        new EditorOverflowContainer(3, [
          new EditorButton(insertRowAbove),
          new EditorButton(insertRowBelow),
          new EditorButton(deleteRow)
        ]),
        new EditorOverflowContainer(3, [
          new EditorButton(insertColumnBefore),
          new EditorButton(insertColumnAfter),
          new EditorButton(deleteColumn)
        ])
      ];
    },
    displayTargetLocator(originalTarget) {
      return originalTarget.closest("table");
    }
  },
  details: {
    selector: "details",
    content() {
      return [
        new EditorButton(detailsEditLabel),
        new EditorButton(detailsToggle),
        new EditorButton(detailsUnwrap)
      ];
    }
  }
};

// resources/js/wysiwyg/ui/framework/blocks/external-content.ts
var ExternalContent = class extends EditorUiElement {
  constructor(url) {
    super();
    /**
     * The URL for HTML to be loaded from.
     */
    __publicField(this, "url", "");
    this.url = url;
  }
  buildDOM() {
    const wrapper = el("div", {
      class: "editor-external-content"
    });
    window.$http.get(this.url).then((resp) => {
      if (typeof resp.data === "string") {
        wrapper.innerHTML = resp.data;
      }
    });
    return wrapper;
  }
};

// resources/js/wysiwyg/ui/defaults/forms/controls.ts
var source2 = {
  submitText: "Save",
  async action(formData, context) {
    setEditorContentFromHtml(context.editor, formData.get("source")?.toString() || "");
    return true;
  },
  fields: [
    {
      label: "Source",
      name: "source",
      type: "textarea"
    }
  ]
};
var about2 = {
  submitText: "Close",
  async action() {
    return true;
  },
  fields: [
    {
      build() {
        return new ExternalContent("/help/wysiwyg");
      }
    }
  ]
};

// resources/js/wysiwyg/ui/defaults/modals.ts
var modals = {
  link: {
    title: "Insert/Edit Link",
    form: link
  },
  image: {
    title: "Insert/Edit Image",
    form: image
  },
  media: {
    title: "Insert/Edit Media",
    form: media
  },
  source: {
    title: "Source code",
    form: source2
  },
  cell_properties: {
    title: "Cell Properties",
    form: cellProperties
  },
  row_properties: {
    title: "Row Properties",
    form: rowProperties
  },
  table_properties: {
    title: "Table Properties",
    form: tableProperties
  },
  details: {
    title: "Edit collapsible block",
    form: details
  },
  about: {
    title: "About the WYSIWYG Editor",
    form: about2
  }
};

// resources/js/wysiwyg/ui/framework/decorator.ts
var EditorDecorator = class {
  constructor(context) {
    __publicField(this, "node", null);
    __publicField(this, "context");
    __publicField(this, "onDestroyCallbacks", []);
    this.context = context;
  }
  getNode() {
    if (!this.node) {
      throw new Error("Attempted to get use node without it being set");
    }
    return this.node;
  }
  setNode(node) {
    this.node = node;
  }
  /**
   * Register a callback to be ran on destroy of this decorator's node.
   */
  onDestroy(callback) {
    this.onDestroyCallbacks.push(callback);
  }
  /**
   * Destroy this decorator. Used for tear-down operations upon destruction
   * of the underlying node this decorator is attached to.
   */
  teardown() {
    for (const callback of this.onDestroyCallbacks) {
      callback();
    }
  }
};

// resources/js/wysiwyg/ui/decorators/code-block.ts
var CodeBlockDecorator = class extends EditorDecorator {
  constructor() {
    super(...arguments);
    __publicField(this, "completedSetup", false);
    __publicField(this, "latestCode", "");
    __publicField(this, "latestLanguage", "");
    // @ts-ignore
    __publicField(this, "editor", null);
  }
  setup(context, element) {
    const codeNode = this.getNode();
    const preEl = element.querySelector("pre");
    if (!preEl) {
      return;
    }
    if (preEl) {
      preEl.hidden = true;
    }
    this.latestCode = codeNode.__code;
    this.latestLanguage = codeNode.__language;
    const lines = this.latestCode.split("\n").length;
    const height = lines * 19.2 + 18 + 24;
    element.style.height = `${height}px`;
    const startTime = Date.now();
    element.addEventListener("click", (event) => {
      requestAnimationFrame(() => {
        context.editor.update(() => {
          $selectSingleNode(this.getNode());
        });
      });
    });
    element.addEventListener("dblclick", (event) => {
      context.editor.getEditorState().read(() => {
        $openCodeEditorForNode(context.editor, this.getNode());
      });
    });
    const selectionChange = (selection) => {
      element.classList.toggle("selected", $selectionContainsNode(selection, codeNode));
    };
    context.manager.onSelectionChange(selectionChange);
    this.onDestroy(() => {
      context.manager.offSelectionChange(selectionChange);
    });
    const renderEditor = (Code) => {
      this.editor = Code.wysiwygView(element, document, this.latestCode, this.latestLanguage);
      setTimeout(() => {
        element.style.height = "";
      }, 12);
    };
    window.importVersioned("code").then((Code) => {
      const timeout = Date.now() - startTime < 20 ? 20 : 0;
      setTimeout(() => renderEditor(Code), timeout);
    });
    this.completedSetup = true;
  }
  update() {
    const codeNode = this.getNode();
    const code2 = codeNode.getCode();
    const language = codeNode.getLanguage();
    if (this.latestCode === code2 && this.latestLanguage === language) {
      return;
    }
    this.latestLanguage = language;
    this.latestCode = code2;
    if (this.editor) {
      this.editor.setContent(code2);
      this.editor.setMode(language, code2);
    }
  }
  render(context, element) {
    if (this.completedSetup) {
      this.update();
    } else {
      this.setup(context, element);
    }
  }
};

// resources/js/wysiwyg/ui/decorators/diagram.ts
var DiagramDecorator = class extends EditorDecorator {
  constructor() {
    super(...arguments);
    __publicField(this, "completedSetup", false);
  }
  setup(context, element) {
    const diagramNode = this.getNode();
    element.classList.add("editor-diagram");
    context.editor.registerCommand(CLICK_COMMAND, (event) => {
      if (!element.contains(event.target)) {
        return false;
      }
      context.editor.update(() => {
        $selectSingleNode(this.getNode());
      });
      return true;
    }, COMMAND_PRIORITY_NORMAL);
    element.addEventListener("dblclick", (event) => {
      context.editor.getEditorState().read(() => {
        $openDrawingEditorForNode(context, this.getNode());
      });
    });
    const selectionChange = (selection) => {
      element.classList.toggle("selected", $selectionContainsNode(selection, diagramNode));
    };
    context.manager.onSelectionChange(selectionChange);
    this.onDestroy(() => {
      context.manager.offSelectionChange(selectionChange);
    });
    this.completedSetup = true;
  }
  update() {
  }
  render(context, element) {
    if (this.completedSetup) {
      this.update();
    } else {
      this.setup(context, element);
    }
  }
};

// resources/js/wysiwyg/services/mouse-handling.ts
function isHardToEscapeNode(node) {
  return $isDecoratorNode(node) || $isImageNode(node) || $isMediaNode(node) || $isDiagramNode(node) || $isTableNode(node);
}
function insertBelowLastNode(context, event) {
  const lastNode = $getRoot().getLastChild();
  if (!lastNode || !isHardToEscapeNode(lastNode)) {
    return false;
  }
  const lastNodeDom = context.editor.getElementByKey(lastNode.getKey());
  if (!lastNodeDom) {
    return false;
  }
  const nodeBounds = lastNodeDom.getBoundingClientRect();
  const isClickBelow = event.clientY > nodeBounds.bottom;
  if (isClickBelow) {
    context.editor.update(() => {
      const newNode = $createParagraphNode();
      $getRoot().append(newNode);
      newNode.select();
    });
    return true;
  }
  return false;
}
function registerMouseHandling(context) {
  const unregisterClick = context.editor.registerCommand(CLICK_COMMAND, (event) => {
    insertBelowLastNode(context, event);
    return false;
  }, COMMAND_PRIORITY_LOW);
  return () => {
    unregisterClick();
  };
}

// resources/js/wysiwyg/index.ts
var theme = {
  text: {
    bold: "editor-theme-bold",
    code: "editor-theme-code",
    italic: "editor-theme-italic",
    strikethrough: "editor-theme-strikethrough",
    subscript: "editor-theme-subscript",
    superscript: "editor-theme-superscript",
    underline: "editor-theme-underline",
    underlineStrikethrough: "editor-theme-underline-strikethrough"
  }
};
function createPageEditorInstance(container, htmlContent, options = {}) {
  const editor = createEditor({
    namespace: "BookStackPageEditor",
    nodes: getNodesForPageEditor(),
    onError: console.error,
    theme
  });
  const context = buildEditorUI(container, editor, {
    ...options,
    editorClass: "page-content"
  });
  editor.setRootElement(context.editorDOM);
  mergeRegister(
    registerRichText(editor),
    registerHistory(editor, createEmptyHistoryState(), 300),
    registerShortcuts(context),
    registerKeyboardHandling(context),
    registerMouseHandling(context),
    registerTableResizer(editor, context.scrollDOM),
    registerTableSelectionHandler(editor),
    registerTaskListHandler(editor, context.editorDOM),
    registerDropPasteHandling(context),
    registerNodeResizer(context),
    registerAutoLinks(editor)
  );
  context.manager.setToolbar(getMainEditorFullToolbar(context));
  for (const key of Object.keys(contextToolbars)) {
    context.manager.registerContextToolbar(key, contextToolbars[key]);
  }
  for (const key of Object.keys(modals)) {
    context.manager.registerModal(key, modals[key]);
  }
  context.manager.registerDecoratorType("code", CodeBlockDecorator);
  context.manager.registerDecoratorType("diagram", DiagramDecorator);
  listen(editor);
  setEditorContentFromHtml(editor, htmlContent);
  const debugView = document.getElementById("lexical-debug");
  if (debugView) {
    debugView.hidden = true;
    editor.registerUpdateListener(({ dirtyElements, dirtyLeaves, editorState, prevEditorState }) => {
      debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
    });
  }
  window.debugEditorState = () => {
    return editor.getEditorState().toJSON();
  };
  registerCommonNodeMutationListeners(context);
  return new SimpleWysiwygEditorInterface(context);
}
function createBasicEditorInstance(container, htmlContent, options = {}) {
  const editor = createEditor({
    namespace: "BookStackBasicEditor",
    nodes: getNodesForBasicEditor(),
    onError: console.error,
    theme
  });
  const context = buildEditorUI(container, editor, options);
  editor.setRootElement(context.editorDOM);
  const editorTeardown = mergeRegister(
    registerRichText(editor),
    registerHistory(editor, createEmptyHistoryState(), 300),
    registerShortcuts(context),
    registerAutoLinks(editor)
  );
  context.manager.setToolbar(getBasicEditorToolbar(context));
  context.manager.registerContextToolbar("link", contextToolbars.link);
  context.manager.registerModal("link", modals.link);
  context.manager.onTeardown(editorTeardown);
  setEditorContentFromHtml(editor, htmlContent);
  return new SimpleWysiwygEditorInterface(context);
}
var SimpleWysiwygEditorInterface = class {
  constructor(context) {
    __publicField(this, "context");
    __publicField(this, "onChangeListeners", []);
    __publicField(this, "editorListenerTeardown", null);
    this.context = context;
  }
  async getContentAsHtml() {
    return await getEditorContentAsHtml(this.context.editor);
  }
  onChange(listener) {
    this.onChangeListeners.push(listener);
    this.startListeningToChanges();
  }
  focus() {
    focusEditor(this.context.editor);
  }
  remove() {
    this.context.manager.teardown();
    this.context.containerDOM.remove();
    if (this.editorListenerTeardown) {
      this.editorListenerTeardown();
    }
  }
  startListeningToChanges() {
    if (this.editorListenerTeardown) {
      return;
    }
    this.editorListenerTeardown = this.context.editor.registerUpdateListener(() => {
      for (const listener of this.onChangeListeners) {
        listener();
      }
    });
  }
};
export {
  SimpleWysiwygEditorInterface,
  createBasicEditorInstance,
  createPageEditorInstance
};
//# sourceMappingURL=wysiwyg.js.map
