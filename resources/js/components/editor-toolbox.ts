import {Component} from './component';

export interface EditorToolboxChangeEventData {
    tab: string;
    open: boolean;
}

export class EditorToolbox extends Component {

    protected container!: HTMLElement;
    protected buttons!: HTMLButtonElement[];
    protected contentElements!: HTMLElement[];
    protected toggleButton!: HTMLElement;
    protected editorWrapEl!: HTMLElement;

    protected open: boolean = false;
    protected tab: string = '';

    setup() {
        // Elements
        this.container = this.$el;
        this.buttons = this.$manyRefs.tabButton as HTMLButtonElement[];
        this.contentElements = this.$manyRefs.tabContent;
        this.toggleButton = this.$refs.toggle;
        this.editorWrapEl = this.container.closest('.page-editor') as HTMLElement;

        this.setupListeners();

        // Set the first tab as active on load
        this.setActiveTab(this.contentElements[0].dataset.tabContent || '');
    }

    protected setupListeners(): void {
        // Toolbox toggle button click
        this.toggleButton.addEventListener('click', () => this.toggle());
        // Tab button click
        this.container.addEventListener('click', (event: MouseEvent) => {
            const button = (event.target as HTMLElement).closest('button');
            if (button instanceof HTMLButtonElement && this.buttons.includes(button)) {
                const name = button.dataset.tab || '';
                this.setActiveTab(name, true);
            }
        });
    }

    protected toggle(): void {
        this.container.classList.toggle('open');
        const isOpen = this.container.classList.contains('open');
        this.toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        this.editorWrapEl.classList.toggle('toolbox-open', isOpen);
        this.open = isOpen;
        this.emitState();
    }

    protected setActiveTab(tabName: string, openToolbox: boolean = false): void {
        // Set button visibility
        for (const button of this.buttons) {
            button.classList.remove('active');
            const bName = button.dataset.tab;
            if (bName === tabName) button.classList.add('active');
        }

        // Set content visibility
        for (const contentEl of this.contentElements) {
            contentEl.style.display = 'none';
            const cName = contentEl.dataset.tabContent;
            if (cName === tabName) contentEl.style.display = 'block';
        }

        if (openToolbox && !this.container.classList.contains('open')) {
            this.toggle();
        }

        this.tab = tabName;
        this.emitState();
    }

    protected emitState(): void {
        const data: EditorToolboxChangeEventData = {tab: this.tab, open: this.open};
        this.$emit('change', data);
    }

}
