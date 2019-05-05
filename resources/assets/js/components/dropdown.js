/**
 * Dropdown
 * Provides some simple logic to create simple dropdown menus.
 */
class DropDown {

    constructor(elem) {
        this.container = elem;
        this.menu = elem.querySelector('.dropdown-menu, [dropdown-menu]');
        this.moveMenu = elem.hasAttribute('dropdown-move-menu');
        this.toggle = elem.querySelector('[dropdown-toggle]');
        this.body = document.body;
        this.setupListeners();
    }

    show(event) {
        this.hide();

        this.menu.style.display = 'block';
        this.menu.classList.add('anim', 'menuIn');

        if (this.moveMenu) {
            // Move to body to prevent being trapped within scrollable sections
            this.rect = this.menu.getBoundingClientRect();
            this.body.appendChild(this.menu);
            this.menu.style.position = 'fixed';
            this.menu.style.left = `${this.rect.left}px`;
            this.menu.style.top = `${this.rect.top}px`;
            this.menu.style.width = `${this.rect.width}px`;
        }

        // Set listener to hide on mouse leave or window click
        this.menu.addEventListener('mouseleave', this.hide.bind(this));
        window.addEventListener('click', event => {
            if (!this.menu.contains(event.target)) {
                this.hide();
            }
        });

        // Focus on first input if existing
        let input = this.menu.querySelector('input');
        if (input !== null) input.focus();

        event.stopPropagation();
    }

    hide() {
        this.menu.style.display = 'none';
        this.menu.classList.remove('anim', 'menuIn');
        if (this.moveMenu) {
            this.menu.style.position = '';
            this.menu.style.left = '';
            this.menu.style.top = '';
            this.menu.style.width = '';
            this.container.appendChild(this.menu);
        }
    }

    setupListeners() {
        // Hide menu on option click
        this.container.addEventListener('click', event => {
             let possibleChildren = Array.from(this.menu.querySelectorAll('a'));
             if (possibleChildren.indexOf(event.target) !== -1) this.hide();
        });
        // Show dropdown on toggle click
        this.toggle.addEventListener('click', this.show.bind(this));
        // Hide menu on enter press
        this.container.addEventListener('keypress', event => {
                if (event.keyCode !== 13) return true;
                event.preventDefault();
                this.hide();
                return false;
        });
    }

}

export default DropDown;