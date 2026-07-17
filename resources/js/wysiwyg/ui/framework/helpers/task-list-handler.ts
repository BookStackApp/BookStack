import {$getNearestNodeFromDOMNode, LexicalEditor} from "lexical";
import {$isListItemNode} from "@lexical/list";

class TaskListHandler {
    protected editorContainer: HTMLElement;
    protected editor: LexicalEditor;

    constructor(editor: LexicalEditor, editorContainer: HTMLElement) {
        this.editor = editor;
        this.editorContainer = editorContainer;
        this.setupListeners();
    }

    protected setupListeners() {
        this.handleClick = this.handleClick.bind(this);
        this.editorContainer.addEventListener('click', this.handleClick);
    }

    handleClick(event: MouseEvent) {
        const target = event.target;
        if (target instanceof HTMLElement && target.nodeName === 'LI' && target.classList.contains('task-list-item')) {
            this.handleTaskListItemClick(target, event);
            event.preventDefault();
        }
    }

    handleTaskListItemClick(listItem: HTMLElement, event: MouseEvent) {
        const bounds = listItem.getBoundingClientRect();
        const withinBounds = event.clientX <= bounds.right
            && event.clientX >= bounds.left
            && event.clientY >= bounds.top
            && event.clientY <= bounds.bottom;

        // Outside task list item bounds means we're probably clicking the pseudo-element
        if (withinBounds) {
            return;
        }

        this.editor.update(() => {
            const node = $getNearestNodeFromDOMNode(listItem);
            if ($isListItemNode(node)) {
                node.setChecked(!node.getChecked());
            }
        });
    }

    teardown() {
        this.editorContainer.removeEventListener('click', this.handleClick);
    }
}


export function registerTaskListHandler(editor: LexicalEditor, editorContainer: HTMLElement): (() => void) {
    const handler = new TaskListHandler(editor, editorContainer);

    return () => {
        handler.teardown();
    };
}