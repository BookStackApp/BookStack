
export type MouseDragTrackerDistance = {
    x: number;
    y: number;
}

export type MouseDragTrackerOptions = {
    down?: (event: MouseEvent, element: HTMLElement) => any;
    move?: (event: MouseEvent, element: HTMLElement, distance: MouseDragTrackerDistance) => any;
    up?: (event: MouseEvent, element: HTMLElement, distance: MouseDragTrackerDistance) => any;
}

export class MouseDragTracker {
    protected container: HTMLElement;
    protected dragTargetSelector: string;
    protected options: MouseDragTrackerOptions;

    protected startX: number = 0;
    protected startY: number = 0;
    protected target: HTMLElement|null = null;

    constructor(container: HTMLElement, dragTargetSelector: string, options: MouseDragTrackerOptions) {
        this.container = container;
        this.dragTargetSelector = dragTargetSelector;
        this.options = options;

        this.onMouseDown = this.onMouseDown.bind(this);
        this.onMouseMove = this.onMouseMove.bind(this);
        this.onMouseUp = this.onMouseUp.bind(this);
        this.container.addEventListener('mousedown', this.onMouseDown);
    }

    teardown() {
        this.container.removeEventListener('mousedown', this.onMouseDown);
        this.container.removeEventListener('mouseup', this.onMouseUp);
        this.container.removeEventListener('mousemove', this.onMouseMove);
    }

    protected onMouseDown(event: MouseEvent) {
        this.target = (event.target as HTMLElement).closest(this.dragTargetSelector);
        if (!this.target) {
            return;
        }

        this.startX = event.screenX;
        this.startY = event.screenY;

        window.addEventListener('mousemove', this.onMouseMove);
        window.addEventListener('mouseup', this.onMouseUp);
        if (this.options.down) {
            this.options.down(event, this.target);
        }
    }

    protected onMouseMove(event: MouseEvent) {
        if (this.options.move && this.target) {
            this.options.move(event, this.target, {
                x: event.screenX - this.startX,
                y: event.screenY - this.startY,
            });
        }
    }

    protected onMouseUp(event: MouseEvent) {
        window.removeEventListener('mousemove', this.onMouseMove);
        window.removeEventListener('mouseup', this.onMouseUp);

        if (this.options.up && this.target) {
            this.options.up(event, this.target, {
                x: event.screenX - this.startX,
                y: event.screenY - this.startY,
            });
        }
    }

}