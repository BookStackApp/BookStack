class EditorToolbox {

    constructor(elem) {
        // Elements
        this.elem = elem;
        this.buttons = elem.querySelectorAll('[toolbox-tab-button]');
        this.contentElements = elem.querySelectorAll('[toolbox-tab-content]');
        this.toggleButton = elem.querySelector('[toolbox-toggle]');

        // Toolbox toggle button click
        this.toggleButton.addEventListener('click', this.toggle.bind(this));
        // Tab button click
        this.elem.addEventListener('click', event => {
            let button = event.target.closest('[toolbox-tab-button]');
            if (button === null) return;
            let name = button.getAttribute('toolbox-tab-button');
            this.setActiveTab(name, true);
        });

        // Set the first tab as active on load
        this.setActiveTab(this.contentElements[0].getAttribute('toolbox-tab-content'));
    }

    toggle() {
        this.elem.classList.toggle('open');
    }

    setActiveTab(tabName, openToolbox = false) {
        // Set button visibility
        for (let i = 0, len = this.buttons.length; i < len; i++) {
            this.buttons[i].classList.remove('active');
            let bName =  this.buttons[i].getAttribute('toolbox-tab-button');
            if (bName === tabName) this.buttons[i].classList.add('active');
        }
        // Set content visibility
        for (let i = 0, len = this.contentElements.length; i < len; i++) {
            this.contentElements[i].style.display = 'none';
            let cName = this.contentElements[i].getAttribute('toolbox-tab-content');
            if (cName === tabName) this.contentElements[i].style.display = 'block';
        }

        if (openToolbox) this.elem.classList.add('open');
    }

}

module.exports = EditorToolbox;