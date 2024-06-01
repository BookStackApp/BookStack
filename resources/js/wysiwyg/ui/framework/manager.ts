import {EditorFormModal, EditorFormModalDefinition} from "./modals";
import {EditorUiContext} from "./core";


export class EditorUIManager {

    protected modalDefinitionsByKey: Record<string, EditorFormModalDefinition> = {};
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
            console.error(`Attempted to show modal of key [${key}] but no modal registered for that key`);
        }

        const modal = new EditorFormModal(modalDefinition);
        modal.setContext(this.getContext());

        return modal;
    }

}