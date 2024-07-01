import {EditorFormModal, EditorFormModalDefinition} from "./modals";
import {EditorContainerUiElement, EditorUiContext, EditorUiElement, EditorUiStateUpdate} from "./core";
import {EditorDecorator, EditorDecoratorAdapter} from "./decorator";
import {$getSelection, COMMAND_PRIORITY_LOW, LexicalEditor, SELECTION_CHANGE_COMMAND} from "lexical";
import {DecoratorListener} from "lexical/LexicalEditor";
import type {NodeKey} from "lexical/LexicalNode";
import {EditorContextToolbar, EditorContextToolbarDefinition} from "./toolbars";

export class EditorUIManager {

    protected modalDefinitionsByKey: Record<string, EditorFormModalDefinition> = {};
    protected decoratorConstructorsByType: Record<string, typeof EditorDecorator> = {};
    protected decoratorInstancesByNodeKey: Record<string, EditorDecorator> = {};
    protected context: EditorUiContext|null = null;
    protected toolbar: EditorContainerUiElement|null = null;
    protected contextToolbarDefinitionsByKey: Record<string, EditorContextToolbarDefinition> = {};
    protected activeContextToolbars: EditorContextToolbar[] = [];

    setContext(context: EditorUiContext) {
        this.context = context;
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

        const modal = new EditorFormModal(modalDefinition);
        modal.setContext(this.getContext());

        return modal;
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

    setToolbar(toolbar: EditorContainerUiElement) {
        if (this.toolbar) {
            this.toolbar.getDOMElement().remove();
        }

        this.toolbar = toolbar;
        toolbar.setContext(this.getContext());
        this.getContext().containerDOM.prepend(toolbar.getDOMElement());
    }

    registerContextToolbar(key: string, definition: EditorContextToolbarDefinition) {
        this.contextToolbarDefinitionsByKey[key] = definition;
    }

    protected triggerStateUpdate(update: EditorUiStateUpdate): void {
        const context = this.getContext();
        context.lastSelection = update.selection;
        this.toolbar?.updateState(update);
        this.updateContextToolbars(update);
        for (const toolbar of this.activeContextToolbars) {
            toolbar.updateState(update);
        }
        // console.log('selection update', update.selection);
    }

    triggerStateRefresh(): void {
        this.triggerStateUpdate({
            editor: this.getContext().editor,
            selection: this.getContext().lastSelection,
        });
    }

    protected updateContextToolbars(update: EditorUiStateUpdate): void {
        for (const toolbar of this.activeContextToolbars) {
            toolbar.empty();
            toolbar.getDOMElement().remove();
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
                contentByTarget.get(targetEl).push(...definition.content);
            }
        }

        for (const [target, contents] of contentByTarget) {
            const toolbar = new EditorContextToolbar(contents);
            toolbar.setContext(this.getContext());
            this.activeContextToolbars.push(toolbar);

            this.getContext().containerDOM.append(toolbar.getDOMElement());
            toolbar.attachTo(target);
        }
    }

    protected setupEditor(editor: LexicalEditor) {
        // Update button states on editor selection change
        editor.registerCommand(SELECTION_CHANGE_COMMAND, () => {
            this.triggerStateUpdate({
                editor: editor,
                selection: $getSelection(),
            });
            return false;
        }, COMMAND_PRIORITY_LOW);

        // Register our DOM decorate listener with the editor
        const domDecorateListener: DecoratorListener<EditorDecoratorAdapter> = (decorators: Record<NodeKey, EditorDecoratorAdapter>) => {
            const keys = Object.keys(decorators);
            for (const key of keys) {
                const decoratedEl = editor.getElementByKey(key);
                const adapter = decorators[key];
                const decorator = this.getDecorator(adapter.type, key);
                decorator.setNode(adapter.getNode());
                const decoratorEl = decorator.render(this.getContext());
                if (decoratedEl) {
                    decoratedEl.append(decoratorEl);
                }
            }
        }
        editor.registerDecoratorListener(domDecorateListener);
    }
}