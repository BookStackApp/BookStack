import {EditorFormModal, EditorFormModalDefinition} from "./modals";
import {EditorUiContext} from "./core";
import {EditorDecorator} from "./decorator";


export class EditorUIManager {

    protected modalDefinitionsByKey: Record<string, EditorFormModalDefinition> = {};
    protected decoratorConstructorsByType: Record<string, typeof EditorDecorator> = {};
    protected decoratorInstancesByNodeKey: Record<string, EditorDecorator> = {};
    protected context: EditorUiContext|null = null;

    setContext(context: EditorUiContext) {
        this.context = context;
    }

    getContext(): EditorUiContext {
        if (this.context === null) {
            throw new Error(`Context attempted to be used without being set`);
        }

        return this.context;
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

    getDecorator(decoratorType: string, nodeKey: string): EditorDecorator {
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
}