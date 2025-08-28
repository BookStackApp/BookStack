import {createEditor} from 'lexical';
import {createEmptyHistoryState, registerHistory} from '@lexical/history';
import {registerRichText} from '@lexical/rich-text';
import {mergeRegister} from '@lexical/utils';
import {getNodesForBasicEditor, getNodesForPageEditor, registerCommonNodeMutationListeners} from './nodes';
import {buildEditorUI} from "./ui";
import {focusEditor, getEditorContentAsHtml, setEditorContentFromHtml} from "./utils/actions";
import {registerTableResizer} from "./ui/framework/helpers/table-resizer";
import {EditorUiContext} from "./ui/framework/core";
import {listen as listenToCommonEvents} from "./services/common-events";
import {registerDropPasteHandling} from "./services/drop-paste-handling";
import {registerTaskListHandler} from "./ui/framework/helpers/task-list-handler";
import {registerTableSelectionHandler} from "./ui/framework/helpers/table-selection-handler";
import {registerShortcuts} from "./services/shortcuts";
import {registerNodeResizer} from "./ui/framework/helpers/node-resizer";
import {registerKeyboardHandling} from "./services/keyboard-handling";
import {registerAutoLinks} from "./services/auto-links";
import {contextToolbars, getBasicEditorToolbar, getMainEditorFullToolbar} from "./ui/defaults/toolbars";
import {modals} from "./ui/defaults/modals";
import {CodeBlockDecorator} from "./ui/decorators/code-block";
import {DiagramDecorator} from "./ui/decorators/diagram";
import {registerMouseHandling} from "./services/mouse-handling";
import {registerSelectionHandling} from "./services/selection-handling";

const theme = {
    text: {
        bold: 'editor-theme-bold',
        code: 'editor-theme-code',
        italic: 'editor-theme-italic',
        strikethrough: 'editor-theme-strikethrough',
        subscript: 'editor-theme-subscript',
        superscript: 'editor-theme-superscript',
        underline: 'editor-theme-underline',
        underlineStrikethrough: 'editor-theme-underline-strikethrough',
    }
};

export function createPageEditorInstance(container: HTMLElement, htmlContent: string, options: Record<string, any> = {}): SimpleWysiwygEditorInterface {
    const editor = createEditor({
        namespace: 'BookStackPageEditor',
        nodes: getNodesForPageEditor(),
        onError: console.error,
        theme: theme,
    });
    const context: EditorUiContext = buildEditorUI(container, editor, {
        ...options,
        editorClass: 'page-content',
    });
    editor.setRootElement(context.editorDOM);

    mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
        registerShortcuts(context),
        registerKeyboardHandling(context),
        registerMouseHandling(context),
        registerSelectionHandling(context),
        registerTableResizer(editor, context.scrollDOM),
        registerTableSelectionHandler(editor),
        registerTaskListHandler(editor, context.editorDOM),
        registerDropPasteHandling(context),
        registerNodeResizer(context),
        registerAutoLinks(editor),
    );

    // Register toolbars, modals & decorators
    context.manager.setToolbar(getMainEditorFullToolbar(context));
    for (const key of Object.keys(contextToolbars)) {
        context.manager.registerContextToolbar(key, contextToolbars[key]);
    }
    for (const key of Object.keys(modals)) {
        context.manager.registerModal(key, modals[key]);
    }
    context.manager.registerDecoratorType('code', CodeBlockDecorator);
    context.manager.registerDecoratorType('diagram', DiagramDecorator);

    listenToCommonEvents(editor);
    setEditorContentFromHtml(editor, htmlContent);

    const debugView = document.getElementById('lexical-debug');
    if (debugView) {
        debugView.hidden = true;
        editor.registerUpdateListener(({dirtyElements, dirtyLeaves, editorState, prevEditorState}) => {
            // Debug logic
            // console.log('editorState', editorState.toJSON());
            debugView.textContent = JSON.stringify(editorState.toJSON(), null, 2);
        });
    }

    // @ts-ignore
    window.debugEditorState = () => {
        return editor.getEditorState().toJSON();
    };

    registerCommonNodeMutationListeners(context);

    return new SimpleWysiwygEditorInterface(context);
}

export function createBasicEditorInstance(container: HTMLElement, htmlContent: string, options: Record<string, any> = {}): SimpleWysiwygEditorInterface {
    const editor = createEditor({
        namespace: 'BookStackBasicEditor',
        nodes: getNodesForBasicEditor(),
        onError: console.error,
        theme: theme,
    });
    const context: EditorUiContext = buildEditorUI(container, editor, options);
    editor.setRootElement(context.editorDOM);

    const editorTeardown = mergeRegister(
        registerRichText(editor),
        registerHistory(editor, createEmptyHistoryState(), 300),
        registerShortcuts(context),
        registerAutoLinks(editor),
    );

    // Register toolbars, modals & decorators
    context.manager.setToolbar(getBasicEditorToolbar(context));
    context.manager.registerContextToolbar('link', contextToolbars.link);
    context.manager.registerModal('link', modals.link);
    context.manager.onTeardown(editorTeardown);

    setEditorContentFromHtml(editor, htmlContent);

    return new SimpleWysiwygEditorInterface(context);
}

export class SimpleWysiwygEditorInterface {
    protected context: EditorUiContext;
    protected onChangeListeners: (() => void)[] = [];
    protected editorListenerTeardown: (() => void)|null = null;

    constructor(context: EditorUiContext) {
        this.context = context;
    }

    async getContentAsHtml(): Promise<string> {
        return await getEditorContentAsHtml(this.context.editor);
    }

    onChange(listener: () => void) {
        this.onChangeListeners.push(listener);
        this.startListeningToChanges();
    }

    focus(): void {
        focusEditor(this.context.editor);
    }

    remove() {
        this.context.manager.teardown();
        this.context.containerDOM.remove();
        if (this.editorListenerTeardown) {
            this.editorListenerTeardown();
        }
    }

    protected startListeningToChanges(): void {
        if (this.editorListenerTeardown) {
            return;
        }

        this.editorListenerTeardown = this.context.editor.registerUpdateListener(() => {
             for (const listener of this.onChangeListeners) {
                 listener();
             }
        });
    }
}