import {EditorDecorator} from "../framework/decorator";
import {EditorUiContext} from "../framework/core";
import {BaseSelection, CLICK_COMMAND, COMMAND_PRIORITY_NORMAL} from "lexical";
import {DiagramNode} from "@lexical/rich-text/LexicalDiagramNode";
import {$selectionContainsNode, $selectSingleNode} from "../../utils/selection";
import {$openDrawingEditorForNode} from "../../utils/diagrams";


export class DiagramDecorator extends EditorDecorator {
    protected completedSetup: boolean = false;

    setup(element: HTMLElement) {
        const diagramNode = this.getNode();
        element.classList.add('editor-diagram');

        this.context.editor.registerCommand(CLICK_COMMAND, (event: MouseEvent): boolean => {
            if (!element.contains(event.target as HTMLElement)) {
                return false;
            }

            this.context.editor.update(() => {
                $selectSingleNode(this.getNode());
            });
            return true;
        }, COMMAND_PRIORITY_NORMAL);

        element.addEventListener('dblclick', event => {
            this.context.editor.getEditorState().read(() => {
                $openDrawingEditorForNode(this.context, (this.getNode() as DiagramNode));
            });
        });

        const selectionChange = (selection: BaseSelection|null): void => {
            element.classList.toggle('selected', $selectionContainsNode(selection, diagramNode));
        };
        this.context.manager.onSelectionChange(selectionChange);
        this.onDestroy(() => {
            this.context.manager.offSelectionChange(selectionChange);
        });

        this.completedSetup = true;
    }

    update() {
        //
    }

    render(element: HTMLElement): void {
        if (this.completedSetup) {
            this.update();
        } else {
            this.setup(element);
        }
    }
}