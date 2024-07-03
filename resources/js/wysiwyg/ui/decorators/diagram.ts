import {EditorDecorator} from "../framework/decorator";
import {EditorUiContext} from "../framework/core";


export class DiagramDecorator extends EditorDecorator {
    protected completedSetup: boolean = false;

    setup(context: EditorUiContext, element: HTMLElement) {
        //

        this.completedSetup = true;
    }

    update() {
        //
    }

    render(context: EditorUiContext, element: HTMLElement): void {
        if (this.completedSetup) {
            this.update();
        } else {
            this.setup(context, element);
        }
    }
}