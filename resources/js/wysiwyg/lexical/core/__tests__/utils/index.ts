/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */

import {createHeadlessEditor} from '@lexical/headless';
import {AutoLinkNode, LinkNode} from '@lexical/link';
import {ListItemNode, ListNode} from '@lexical/list';

import {TableCellNode, TableNode, TableRowNode} from '@lexical/table';

import {
  $getSelection,
  $isRangeSelection,
  createEditor,
  DecoratorNode,
  EditorState,
  EditorThemeClasses,
  ElementNode,
  Klass,
  LexicalEditor,
  LexicalNode,
  RangeSelection,
  SerializedElementNode,
  SerializedLexicalNode,
  SerializedTextNode,
  TextNode,
} from 'lexical';

import {CreateEditorArgs, HTMLConfig, LexicalNodeReplacement,} from '../../LexicalEditor';
import {resetRandomKey} from '../../LexicalUtils';
import {HeadingNode} from "@lexical/rich-text/LexicalHeadingNode";
import {QuoteNode} from "@lexical/rich-text/LexicalQuoteNode";
import {DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {EditorUiContext} from "../../../../ui/framework/core";
import {EditorUIManager} from "../../../../ui/framework/manager";
import {ImageNode} from "@lexical/rich-text/LexicalImageNode";
import {MediaNode} from "@lexical/rich-text/LexicalMediaNode";

type TestEnv = {
  readonly container: HTMLDivElement;
  readonly editor: LexicalEditor;
  readonly outerHTML: string;
  readonly innerHTML: string;
};

/**
 * @deprecated - Consider using `createTestContext` instead within the test case.
 */
export function initializeUnitTest(
  runTests: (testEnv: TestEnv) => void,
  editorConfig: CreateEditorArgs = {namespace: 'test', theme: {}},
) {
  const testEnv = {
    _container: null as HTMLDivElement | null,
    _editor: null as LexicalEditor | null,
    get container() {
      if (!this._container) {
        throw new Error('testEnv.container not initialized.');
      }
      return this._container;
    },
    set container(container) {
      this._container = container;
    },
    get editor() {
      if (!this._editor) {
        throw new Error('testEnv.editor not initialized.');
      }
      return this._editor;
    },
    set editor(editor) {
      this._editor = editor;
    },
    get innerHTML() {
      return (this.container.firstChild as HTMLElement).innerHTML;
    },
    get outerHTML() {
      return this.container.innerHTML;
    },
    reset() {
      this._container = null;
      this._editor = null;
    },
  };

  beforeEach(async () => {
    resetRandomKey();

    testEnv.container = document.createElement('div');
    document.body.appendChild(testEnv.container);

    const editorEl = document.createElement('div');
    editorEl.setAttribute('contenteditable', 'true');
    testEnv.container.append(editorEl);

    const lexicalEditor = createTestEditor(editorConfig);
    lexicalEditor.setRootElement(editorEl);
    testEnv.editor = lexicalEditor;
  });

  afterEach(() => {
    document.body.removeChild(testEnv.container);
    testEnv.reset();
  });

  runTests(testEnv);
}

export function initializeClipboard() {
  Object.defineProperty(window, 'DragEvent', {
    value: class DragEvent {},
  });
  Object.defineProperty(window, 'ClipboardEvent', {
    value: class ClipboardEvent {},
  });
}

export type SerializedTestElementNode = SerializedElementNode;

export class TestElementNode extends ElementNode {
  static getType(): string {
    return 'test_block';
  }

  static clone(node: TestElementNode) {
    return new TestElementNode(node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestElementNode,
  ): TestInlineElementNode {
    const node = $createTestInlineElementNode();
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportJSON(): SerializedTestElementNode {
    return {
      ...super.exportJSON(),
      type: 'test_block',
      version: 1,
    };
  }

  createDOM() {
    return document.createElement('div');
  }

  updateDOM() {
    return false;
  }
}

export function $createTestElementNode(): TestElementNode {
  return new TestElementNode();
}

type SerializedTestTextNode = SerializedTextNode;

export class TestTextNode extends TextNode {
  static getType() {
    return 'test_text';
  }

  static clone(node: TestTextNode): TestTextNode {
    return new TestTextNode(node.__text, node.__key);
  }

  static importJSON(serializedNode: SerializedTestTextNode): TestTextNode {
    return new TestTextNode(serializedNode.text);
  }

  exportJSON(): SerializedTestTextNode {
    return {
      ...super.exportJSON(),
      type: 'test_text',
      version: 1,
    };
  }
}

export type SerializedTestInlineElementNode = SerializedElementNode;

export class TestInlineElementNode extends ElementNode {
  static getType(): string {
    return 'test_inline_block';
  }

  static clone(node: TestInlineElementNode) {
    return new TestInlineElementNode(node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestInlineElementNode,
  ): TestInlineElementNode {
    const node = $createTestInlineElementNode();
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportJSON(): SerializedTestInlineElementNode {
    return {
      ...super.exportJSON(),
      type: 'test_inline_block',
      version: 1,
    };
  }

  createDOM() {
    return document.createElement('a');
  }

  updateDOM() {
    return false;
  }

  isInline() {
    return true;
  }
}

export function $createTestInlineElementNode(): TestInlineElementNode {
  return new TestInlineElementNode();
}

export type SerializedTestShadowRootNode = SerializedElementNode;

export class TestShadowRootNode extends ElementNode {
  static getType(): string {
    return 'test_shadow_root';
  }

  static clone(node: TestShadowRootNode) {
    return new TestElementNode(node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestShadowRootNode,
  ): TestShadowRootNode {
    const node = $createTestShadowRootNode();
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportJSON(): SerializedTestShadowRootNode {
    return {
      ...super.exportJSON(),
      type: 'test_block',
      version: 1,
    };
  }

  createDOM() {
    return document.createElement('div');
  }

  updateDOM() {
    return false;
  }

  isShadowRoot() {
    return true;
  }
}

export function $createTestShadowRootNode(): TestShadowRootNode {
  return new TestShadowRootNode();
}

export type SerializedTestSegmentedNode = SerializedTextNode;

export class TestSegmentedNode extends TextNode {
  static getType(): string {
    return 'test_segmented';
  }

  static clone(node: TestSegmentedNode): TestSegmentedNode {
    return new TestSegmentedNode(node.__text, node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestSegmentedNode,
  ): TestSegmentedNode {
    const node = $createTestSegmentedNode(serializedNode.text);
    node.setFormat(serializedNode.format);
    node.setDetail(serializedNode.detail);
    node.setMode(serializedNode.mode);
    node.setStyle(serializedNode.style);
    return node;
  }

  exportJSON(): SerializedTestSegmentedNode {
    return {
      ...super.exportJSON(),
      type: 'test_segmented',
      version: 1,
    };
  }
}

export function $createTestSegmentedNode(text: string): TestSegmentedNode {
  return new TestSegmentedNode(text).setMode('segmented');
}

export type SerializedTestExcludeFromCopyElementNode = SerializedElementNode;

export class TestExcludeFromCopyElementNode extends ElementNode {
  static getType(): string {
    return 'test_exclude_from_copy_block';
  }

  static clone(node: TestExcludeFromCopyElementNode) {
    return new TestExcludeFromCopyElementNode(node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestExcludeFromCopyElementNode,
  ): TestExcludeFromCopyElementNode {
    const node = $createTestExcludeFromCopyElementNode();
    node.setDirection(serializedNode.direction);
    return node;
  }

  exportJSON(): SerializedTestExcludeFromCopyElementNode {
    return {
      ...super.exportJSON(),
      type: 'test_exclude_from_copy_block',
      version: 1,
    };
  }

  createDOM() {
    return document.createElement('div');
  }

  updateDOM() {
    return false;
  }

  excludeFromCopy() {
    return true;
  }
}

export function $createTestExcludeFromCopyElementNode(): TestExcludeFromCopyElementNode {
  return new TestExcludeFromCopyElementNode();
}

export type SerializedTestDecoratorNode = SerializedLexicalNode;

export class TestDecoratorNode extends DecoratorNode<HTMLElement> {
  static getType(): string {
    return 'test_decorator';
  }

  static clone(node: TestDecoratorNode) {
    return new TestDecoratorNode(node.__key);
  }

  static importJSON(
    serializedNode: SerializedTestDecoratorNode,
  ): TestDecoratorNode {
    return $createTestDecoratorNode();
  }

  exportJSON(): SerializedTestDecoratorNode {
    return {
      ...super.exportJSON(),
      type: 'test_decorator',
      version: 1,
    };
  }

  static importDOM() {
    return {
      'test-decorator': (domNode: HTMLElement) => {
        return {
          conversion: () => ({node: $createTestDecoratorNode()}),
        };
      },
    };
  }

  exportDOM() {
    return {
      element: document.createElement('test-decorator'),
    };
  }

  getTextContent() {
    return 'Hello world';
  }

  createDOM() {
    return document.createElement('span');
  }

  updateDOM() {
    return false;
  }

  decorate() {
    const decorator = document.createElement('span');
    decorator.textContent = 'Hello world';
    return decorator;
  }
}

export function $createTestDecoratorNode(): TestDecoratorNode {
  return new TestDecoratorNode();
}

const DEFAULT_NODES: NonNullable<ReadonlyArray<Klass<LexicalNode> | LexicalNodeReplacement>> = [
  HeadingNode,
  ListNode,
  ListItemNode,
  QuoteNode,
  TableNode,
  TableCellNode,
  TableRowNode,
  AutoLinkNode,
  LinkNode,
  DetailsNode,
  TestElementNode,
  TestSegmentedNode,
  TestExcludeFromCopyElementNode,
  TestDecoratorNode,
  TestInlineElementNode,
  TestShadowRootNode,
  TestTextNode,
];

export function createTestEditor(
  config: {
    namespace?: string;
    editorState?: EditorState;
    theme?: EditorThemeClasses;
    parentEditor?: LexicalEditor;
    nodes?: ReadonlyArray<Klass<LexicalNode> | LexicalNodeReplacement>;
    onError?: (error: Error) => void;
    disableEvents?: boolean;
    readOnly?: boolean;
    html?: HTMLConfig;
  } = {},
): LexicalEditor {
  const customNodes = config.nodes || [];
  const editor = createEditor({
    namespace: config.namespace,
    onError: (e) => {
      throw e;
    },
    ...config,
    nodes: DEFAULT_NODES.concat(customNodes),
  });

  return editor;
}

export function createTestHeadlessEditor(
  editorState?: EditorState,
): LexicalEditor {
  return createHeadlessEditor({
    editorState,
    onError: (error) => {
      throw error;
    },
  });
}

export function createTestContext(): EditorUiContext {

  const container = document.createElement('div');
  document.body.appendChild(container);

  const scrollWrap = document.createElement('div');
  const editorDOM = document.createElement('div');
  editorDOM.setAttribute('contenteditable', 'true');

  scrollWrap.append(editorDOM);
  container.append(scrollWrap);

  const editor = createTestEditor({
    namespace: 'testing',
    theme: {},
    nodes: [
        ImageNode,
        MediaNode,
    ]
  });

  editor.setRootElement(editorDOM);

  const context = {
    containerDOM: container,
    editor: editor,
    editorDOM: editorDOM,
    error(text: string | Error): void {
    },
    manager: new EditorUIManager(),
    options: {},
    scrollDOM: scrollWrap,
    translate(text: string): string {
      return "";
    }
  };

  context.manager.setContext(context);

  return context;
}

export function destroyFromContext(context: EditorUiContext) {
  context.containerDOM.remove();
}

export function $assertRangeSelection(selection: unknown): RangeSelection {
  if (!$isRangeSelection(selection)) {
    throw new Error(`Expected RangeSelection, got ${selection}`);
  }
  return selection;
}

export function invariant(cond?: boolean, message?: string): asserts cond {
  if (cond) {
    return;
  }
  throw new Error(`Invariant: ${message}`);
}

export class ClipboardDataMock {
  getData: jest.Mock<string, [string]>;
  setData: jest.Mock<void, [string, string]>;

  constructor() {
    this.getData = jest.fn();
    this.setData = jest.fn();
  }
}

export class DataTransferMock implements DataTransfer {
  _data: Map<string, string> = new Map();
  get dropEffect(): DataTransfer['dropEffect'] {
    throw new Error('Getter not implemented.');
  }
  get effectAllowed(): DataTransfer['effectAllowed'] {
    throw new Error('Getter not implemented.');
  }
  get files(): FileList {
    throw new Error('Getter not implemented.');
  }
  get items(): DataTransferItemList {
    throw new Error('Getter not implemented.');
  }
  get types(): ReadonlyArray<string> {
    return Array.from(this._data.keys());
  }
  clearData(dataType?: string): void {
    //
  }
  getData(dataType: string): string {
    return this._data.get(dataType) || '';
  }
  setData(dataType: string, data: string): void {
    this._data.set(dataType, data);
  }
  setDragImage(image: Element, x: number, y: number): void {
    //
  }
}

export class EventMock implements Event {
  get bubbles(): boolean {
    throw new Error('Getter not implemented.');
  }
  get cancelBubble(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get cancelable(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get composed(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get currentTarget(): EventTarget | null {
    throw new Error('Gettter not implemented.');
  }
  get defaultPrevented(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get eventPhase(): number {
    throw new Error('Gettter not implemented.');
  }
  get isTrusted(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get returnValue(): boolean {
    throw new Error('Gettter not implemented.');
  }
  get srcElement(): EventTarget | null {
    throw new Error('Gettter not implemented.');
  }
  get target(): EventTarget | null {
    throw new Error('Gettter not implemented.');
  }
  get timeStamp(): number {
    throw new Error('Gettter not implemented.');
  }
  get type(): string {
    throw new Error('Gettter not implemented.');
  }
  composedPath(): EventTarget[] {
    throw new Error('Method not implemented.');
  }
  initEvent(
    type: string,
    bubbles?: boolean | undefined,
    cancelable?: boolean | undefined,
  ): void {
    throw new Error('Method not implemented.');
  }
  stopImmediatePropagation(): void {
    return;
  }
  stopPropagation(): void {
    return;
  }
  NONE = 0 as const;
  CAPTURING_PHASE = 1 as const;
  AT_TARGET = 2 as const;
  BUBBLING_PHASE = 3 as const;
  preventDefault() {
    return;
  }
}

export class KeyboardEventMock extends EventMock implements KeyboardEvent {
  altKey = false;
  get charCode(): number {
    throw new Error('Getter not implemented.');
  }
  get code(): string {
    throw new Error('Getter not implemented.');
  }
  ctrlKey = false;
  get isComposing(): boolean {
    throw new Error('Getter not implemented.');
  }
  get key(): string {
    throw new Error('Getter not implemented.');
  }
  get keyCode(): number {
    throw new Error('Getter not implemented.');
  }
  get location(): number {
    throw new Error('Getter not implemented.');
  }
  metaKey = false;
  get repeat(): boolean {
    throw new Error('Getter not implemented.');
  }
  shiftKey = false;
  constructor(type: void | string) {
    super();
  }
  getModifierState(keyArg: string): boolean {
    throw new Error('Method not implemented.');
  }
  initKeyboardEvent(
    typeArg: string,
    bubblesArg?: boolean | undefined,
    cancelableArg?: boolean | undefined,
    viewArg?: Window | null | undefined,
    keyArg?: string | undefined,
    locationArg?: number | undefined,
    ctrlKey?: boolean | undefined,
    altKey?: boolean | undefined,
    shiftKey?: boolean | undefined,
    metaKey?: boolean | undefined,
  ): void {
    throw new Error('Method not implemented.');
  }
  DOM_KEY_LOCATION_STANDARD = 0 as const;
  DOM_KEY_LOCATION_LEFT = 1 as const;
  DOM_KEY_LOCATION_RIGHT = 2 as const;
  DOM_KEY_LOCATION_NUMPAD = 3 as const;
  get detail(): number {
    throw new Error('Getter not implemented.');
  }
  get view(): Window | null {
    throw new Error('Getter not implemented.');
  }
  get which(): number {
    throw new Error('Getter not implemented.');
  }
  initUIEvent(
    typeArg: string,
    bubblesArg?: boolean | undefined,
    cancelableArg?: boolean | undefined,
    viewArg?: Window | null | undefined,
    detailArg?: number | undefined,
  ): void {
    throw new Error('Method not implemented.');
  }
}

export function tabKeyboardEvent() {
  return new KeyboardEventMock('keydown');
}

export function shiftTabKeyboardEvent() {
  const keyboardEvent = new KeyboardEventMock('keydown');
  keyboardEvent.shiftKey = true;
  return keyboardEvent;
}

export function generatePermutations<T>(
  values: T[],
  maxLength = values.length,
): T[][] {
  if (maxLength > values.length) {
    throw new Error('maxLength over values.length');
  }
  const result: T[][] = [];
  const current: T[] = [];
  const seen = new Set();
  (function permutationsImpl() {
    if (current.length > maxLength) {
      return;
    }
    result.push(current.slice());
    for (let i = 0; i < values.length; i++) {
      const key = values[i];
      if (seen.has(key)) {
        continue;
      }
      seen.add(key);
      current.push(key);
      permutationsImpl();
      seen.delete(key);
      current.pop();
    }
  })();
  return result;
}

// This tag function is just used to trigger prettier auto-formatting.
// (https://prettier.io/blog/2020/08/24/2.1.0.html#api)
export function html(
  partials: TemplateStringsArray,
  ...params: string[]
): string {
  let output = '';
  for (let i = 0; i < partials.length; i++) {
    output += partials[i];
    if (i < partials.length - 1) {
      output += params[i];
    }
  }
  return output;
}

export function expectHtmlToBeEqual(expected: string, actual: string): void {
  expect(formatHtml(expected)).toBe(formatHtml(actual));
}

type nodeTextShape = {
  text: string;
};

type nodeShape = {
  type: string;
  children?: (nodeShape|nodeTextShape)[];
}

export function getNodeShape(node: SerializedLexicalNode): nodeShape|nodeTextShape {
  // @ts-ignore
  const children: SerializedLexicalNode[] = (node.children || []);

  const shape: nodeShape = {
    type: node.type,
  };

  if (shape.type === 'text') {
    // @ts-ignore
    return  {text: node.text}
  }

  if (children.length > 0) {
    shape.children = children.map(c => getNodeShape(c));
  }

  return shape;
}

export function expectNodeShapeToMatch(editor: LexicalEditor, expected: nodeShape[]) {
  const json = editor.getEditorState().toJSON();
  const shape = getNodeShape(json.root) as nodeShape;
  expect(shape.children).toMatchObject(expected);
}

/**
 * Expect a given prop within the JSON editor state structure to be the given value.
 * Uses dot notation for the provided `propPath`. Example:
 * 0.5.cat => First child, Sixth child, cat property
 */
export function expectEditorStateJSONPropToEqual(editor: LexicalEditor, propPath: string, expected: any) {
  let currentItem: any = editor.getEditorState().toJSON().root;
  let currentPath = [];
  const pathParts = propPath.split('.');

  for (const part of pathParts) {
    currentPath.push(part);
    const childAccess = Number.isInteger(Number(part)) && Array.isArray(currentItem.children);
    const target = childAccess ? currentItem.children : currentItem;

    if (typeof target[part] === 'undefined') {
      throw new Error(`Could not resolve editor state at path ${currentPath.join('.')}`)
    }
    currentItem = target[part];
  }

  expect(currentItem).toBe(expected);
}

function formatHtml(s: string): string {
  return s.replace(/>\s+</g, '><').replace(/\s*\n\s*/g, ' ').trim();
}

export function dispatchKeydownEventForNode(node: LexicalNode, editor: LexicalEditor, key: string) {
  const nodeDomEl = editor.getElementByKey(node.getKey());
  const event = new KeyboardEvent('keydown', {
    bubbles: true,
    cancelable: true,
    key,
  });
  nodeDomEl?.dispatchEvent(event);
  editor.commitUpdates();
}

export function dispatchKeydownEventForSelectedNode(editor: LexicalEditor, key: string) {
  editor.getEditorState().read((): void => {
    const node = $getSelection()?.getNodes()[0] || null;
    if (node) {
      dispatchKeydownEventForNode(node, editor, key);
    }
  });
}

export function dispatchEditorMouseClick(editor: LexicalEditor, clientX: number, clientY: number) {
  const dom = editor.getRootElement();
  if (!dom) {
    return;
  }

  const event = new MouseEvent('click', {
    clientX: clientX,
    clientY: clientY,
    bubbles: true,
    cancelable: true,
  });
  dom?.dispatchEvent(event);
  editor.commitUpdates();
}