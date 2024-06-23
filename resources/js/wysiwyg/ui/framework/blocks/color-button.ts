import {EditorBasicButtonDefinition, EditorButton} from "../buttons";
import {EditorUiStateUpdate} from "../core";
import {$isRangeSelection} from "lexical";
import {$getSelectionStyleValueForProperty} from "@lexical/selection";

export class EditorColorButton extends EditorButton {
    protected style: string;

    constructor(definition: EditorBasicButtonDefinition, style: string) {
        super(definition);

        this.style = style;
    }

    getColorBar(): HTMLElement {
        const colorBar = this.getDOMElement().querySelector('svg .editor-icon-color-bar');

        if (!colorBar) {
            throw new Error(`Could not find expected color bar in the icon for this ${this.definition.label} button`);
        }

        return (colorBar as HTMLElement);
    }

    updateState(state: EditorUiStateUpdate): void {
        super.updateState(state);

        if ($isRangeSelection(state.selection)) {
            const value = $getSelectionStyleValueForProperty(state.selection, this.style);
            const colorBar = this.getColorBar();
            colorBar.setAttribute('fill', value);
        }
    }

}