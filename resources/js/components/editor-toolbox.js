import {Component} from './component';

export class EditorToolbox extends Component {

    setup() {
        // Elements
        this.container = this.$el;
        this.buttons = this.$manyRefs.tabButton;
        this.contentElements = this.$manyRefs.tabContent;
        this.toggleButton = this.$refs.toggle;
        this.editorWrapEl = this.container.closest('.page-editor');

        this.setupListeners();

        // Set the first tab as active on load
        this.setActiveTab(this.contentElements[0].dataset.tabContent);
    }

    setupListeners() {
        // Toolbox toggle button click
        this.toggleButton.addEventListener('click', () => this.toggle());
        // Tab button click
        this.container.addEventListener('click', event => {
            const button = event.target.closest('button');
            if (this.buttons.includes(button)) {
                const name = button.dataset.tab;
                this.setActiveTab(name, true);
            }
        });
    }

    toggle() {
        this.container.classList.toggle('open');
        const isOpen = this.container.classList.contains('open');
        this.toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        this.editorWrapEl.classList.toggle('toolbox-open', isOpen);
    }

    setActiveTab(tabName, openToolbox = false) {
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
    }

}
