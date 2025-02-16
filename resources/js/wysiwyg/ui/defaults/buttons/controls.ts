import {EditorButton, EditorButtonDefinition} from "../../framework/buttons";
import undoIcon from "@icons/editor/undo.svg";
import {EditorUiContext} from "../../framework/core";
import {
    BaseSelection,
    CAN_REDO_COMMAND,
    CAN_UNDO_COMMAND,
    COMMAND_PRIORITY_LOW,
    REDO_COMMAND,
    UNDO_COMMAND
} from "lexical";
import redoIcon from "@icons/editor/redo.svg";
import sourceIcon from "@icons/editor/source-view.svg";
import fullscreenIcon from "@icons/editor/fullscreen.svg";
import aboutIcon from "@icons/editor/about.svg";
import {getEditorContentAsHtml} from "../../../utils/actions";

export const undo: EditorButtonDefinition = {
    label: 'Undo',
    icon: undoIcon,
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(UNDO_COMMAND, undefined);
        context.manager.triggerFutureStateRefresh();
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    },
    setup(context: EditorUiContext, button: EditorButton) {
        button.toggleDisabled(true);

        context.editor.registerCommand(CAN_UNDO_COMMAND, (payload: boolean): boolean => {
            button.toggleDisabled(!payload)
            return false;
        }, COMMAND_PRIORITY_LOW);
    }
}

export const redo: EditorButtonDefinition = {
    label: 'Redo',
    icon: redoIcon,
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(REDO_COMMAND, undefined);
        context.manager.triggerFutureStateRefresh();
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    },
    setup(context: EditorUiContext, button: EditorButton) {
        button.toggleDisabled(true);

        context.editor.registerCommand(CAN_REDO_COMMAND, (payload: boolean): boolean => {
            button.toggleDisabled(!payload)
            return false;
        }, COMMAND_PRIORITY_LOW);
    }
}


export const source: EditorButtonDefinition = {
    label: 'Source code',
    icon: sourceIcon,
    async action(context: EditorUiContext) {
        const modal = context.manager.createModal('source');
        const source = await getEditorContentAsHtml(context.editor);
        modal.show({source});
    },
    isActive() {
        return false;
    }
};

export const fullscreen: EditorButtonDefinition = {
    label: 'Fullscreen',
    icon: fullscreenIcon,
    async action(context: EditorUiContext, button: EditorButton) {
        const isFullScreen = context.containerDOM.classList.contains('fullscreen');
        context.containerDOM.classList.toggle('fullscreen', !isFullScreen);
        (context.containerDOM.closest('body') as HTMLElement).classList.toggle('editor-is-fullscreen', !isFullScreen);
        button.setActiveState(!isFullScreen);
    },
    isActive(selection, context: EditorUiContext) {
        return context.containerDOM.classList.contains('fullscreen');
    }
};

export const about: EditorButtonDefinition = {
    label: 'About the editor',
    icon: aboutIcon,
    async action(context: EditorUiContext, button: EditorButton) {
        const modal = context.manager.createModal('about');
        modal.show({});
    },
    isActive(selection, context: EditorUiContext) {
        return false;
    }
};