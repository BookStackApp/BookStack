import {EditorFormModal, EditorFormModalDefinition} from "./modals";
import {EditorContainerUiElement, EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./core";
import {EditorDecorator, EditorDecoratorAdapter} from "./decorator";
import {$getSelection, BaseSelection, LexicalEditor} from "lexical";
import {DecoratorListener} from "lexical/LexicalEditor";
import type {NodeKey} from "lexical/LexicalNode";
import {EditorContextToolbar, EditorContextToolbarDefinition} from "./toolbars";
import {getLastSelection, setLastSelection} from "../../utils/selection";
import {DropDownManager} from "./helpers/dropdowns";

export type SelectionChangeHandler = (selection: BaseSelection|null) => void;

export class EditorUIManager {

    public dropdowns: DropDownManager = new DropDownManager();

    protected modalDefinitionsByKey: Record<string, EditorFormModalDefinition> = {};
    protected activeModalsByKey: Record<string, EditorFormModal> = {};
    protected decoratorConstructorsByType: Record<string, typeof EditorDecorator> = {};
    protected decoratorInstancesByNodeKey: Record<string, EditorDecorator> = {};
    protected context: EditorUiContext|null = null;
    protected toolbar: EditorContainerUiElement|null = null;
    protected contextToolbarDefinitionsByKey: Record<string, EditorContextToolbarDefinition> = {};
    protected activeContextToolbars: EditorContextToolbar[] = [];
    protected selectionChangeHandlers: Set<SelectionChangeHandler> = new Set();
    protected domEventAbortController = new AbortController();
    protected teardownCallbacks: (()=>void)[] = [];

    setContext(context: EditorUiContext) {
        this.context = context;
        this.setupEventListeners();
        this.setupEditor(context.editor);
    }

    getContext(): EditorUiContext {
        if (this.context === null) {
            throw new Error(`Context attempted to be used without being set`);
        }

        return this.context;
    }

    triggerStateUpdateForElement(element: EditorUiElement) {
        element.updateState({
            selection: null,
            editor: this.getContext().editor
        });
    }

    registerModal(key: string, modalDefinition: EditorFormModalDefinition) {
        this.modalDefinitionsByKey[key] = modalDefinition;
    }

    createModal(key: string): EditorFormModal {
        const modalDefinition = this.modalDefinitionsByKey[key];
        if (!modalDefinition) {
            throw new Error(`Attempted to show modal of key [${key}] but no modal registered for that key`);
        }

        const modal = new EditorFormModal(modalDefinition, key);
        modal.setContext(this.getContext());

        return modal;
    }

    setModalActive(key: string, modal: EditorFormModal): void {
        this.activeModalsByKey[key] = modal;
    }

    setModalInactive(key: string): void {
        delete this.activeModalsByKey[key];
    }

    getActiveModal(key: string): EditorFormModal|null {
        return this.activeModalsByKey[key];
    }

    registerDecoratorType(type: string, decorator: typeof EditorDecorator) {
        this.decoratorConstructorsByType[type] = decorator;
    }

    protected getDecorator(decoratorType: string, nodeKey: string): EditorDecorator {
        if (this.decoratorInstancesByNodeKey[nodeKey]) {
            return this.decoratorInstancesByNodeKey[nodeKey];
        }

        const decoratorClass = this.decoratorConstructorsByType[decoratorType];
        if (!decoratorClass) {
            throw new Error(`Attempted to use decorator of type [${decoratorType}] but not decorator registered for that type`);
        }

        // @ts-ignore
        const decorator = new decoratorClass(nodeKey);
        this.decoratorInstancesByNodeKey[nodeKey] = decorator;
        return decorator;
    }

    getDecoratorByNodeKey(nodeKey: string): EditorDecorator|null {
        return this.decoratorInstancesByNodeKey[nodeKey] || null;
    }

    setToolbar(toolbar: EditorContainerUiElement) {
        if (this.toolbar) {
            this.toolbar.teardown();
        }

        this.toolbar = toolbar;
        toolbar.setContext(this.getContext());
        this.getContext().containerDOM.prepend(toolbar.getDOMElement());
    }

    registerContextToolbar(key: string, definition: EditorContextToolbarDefinition) {
        this.contextToolbarDefinitionsByKey[key] = definition;
    }

    triggerStateUpdate(update: EditorUiStateUpdate): void {
        setLastSelection(update.editor, update.selection);
        this.toolbar?.updateState(update);
        this.updateContextToolbars(update);
        for (const toolbar of this.activeContextToolbars) {
            toolbar.updateState(update);
        }
        this.triggerSelectionChange(update.selection);
    }

    triggerStateRefresh(): void {
        const editor = this.getContext().editor;
        const update = {
            editor,
            selection: getLastSelection(editor),
        };

        this.triggerStateUpdate(update);
        this.updateContextToolbars(update);
    }

    triggerFutureStateRefresh(): void {
        requestAnimationFrame(() => {
            this.getContext().editor.getEditorState().read(() => {
                this.triggerStateRefresh();
            });
        });
    }

    protected triggerSelectionChange(selection: BaseSelection|null): void {
        if (!selection) {
            return;
        }

        for (const handler of this.selectionChangeHandlers) {
            handler(selection);
        }
    }

    onSelectionChange(handler: SelectionChangeHandler): void {
        this.selectionChangeHandlers.add(handler);
    }

    offSelectionChange(handler: SelectionChangeHandler): void {
        this.selectionChangeHandlers.delete(handler);
    }

    triggerLayoutUpdate(): void {
        window.requestAnimationFrame(() => {
            for (const toolbar of this.activeContextToolbars) {
                toolbar.updatePosition();
            }
        });
    }

    getDefaultDirection(): 'rtl' | 'ltr' {
        return this.getContext().options.textDirection === 'rtl' ? 'rtl' : 'ltr';
    }

    onTeardown(callback: () => void): void {
        this.teardownCallbacks.push(callback);
    }

    teardown(): void {
        this.domEventAbortController.abort('teardown');

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

    protected updateContextToolbars(update: EditorUiStateUpdate): void {
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
        const contentByTarget = new Map<HTMLElement, EditorUiElement[]>();
        for (const key of toolbarKeys) {
            const definition = this.contextToolbarDefinitionsByKey[key];
            const matchingElem = ((element.closest(definition.selector)) || (element.querySelector(definition.selector))) as HTMLElement|null;
            if (matchingElem) {
                const targetEl = definition.displayTargetLocator ? definition.displayTargetLocator(matchingElem) : matchingElem;
                if (!contentByTarget.has(targetEl)) {
                    contentByTarget.set(targetEl, [])
                }
                // @ts-ignore
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

    protected setupEditor(editor: LexicalEditor) {
        // Register our DOM decorate listener with the editor
        const domDecorateListener: DecoratorListener<EditorDecoratorAdapter> = (decorators: Record<NodeKey, EditorDecoratorAdapter>) => {
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
        }
        editor.registerDecoratorListener(domDecorateListener);

        // Watch for changes to update local state
        editor.registerUpdateListener(({editorState, prevEditorState}) => {
            // Watch for selection changes to update the UI on change
            // Used to be done via SELECTION_CHANGE_COMMAND but this would not always emit
            // for all selection changes, so this proved more reliable.
            const selectionChange = !(prevEditorState._selection?.is(editorState._selection) || false);
            if (selectionChange) {
                editor.update(() => {
                    const selection = $getSelection();
                    // console.log('manager::selection', selection);
                    this.triggerStateUpdate({
                        editor, selection,
                    });
                });
            }
        });
    }

    protected setupEventListeners() {
        const layoutUpdate = this.triggerLayoutUpdate.bind(this);
        window.addEventListener('scroll', layoutUpdate, {capture: true, passive: true, signal: this.domEventAbortController.signal});
        window.addEventListener('resize', layoutUpdate, {passive: true, signal: this.domEventAbortController.signal});
    }
}