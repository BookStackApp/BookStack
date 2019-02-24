/**
 * Dropdown
 * Provides some simple logic to create simple dropdown menus.
 */
class DropDown {

    constructor(elem) {
        this.container = elem;
        this.menu = elem.querySelector('ul, [dropdown-menu]');
        this.toggle = elem.querySelector('[dropdown-toggle]');
        this.setupListeners();
    }

    show() {
        this.menu.style.display = 'block';
        this.menu.classList.add('anim', 'menuIn');
        this.container.addEventListener('mouseleave', this.hide.bind(this));

        // Focus on first input if existing
        let input = this.menu.querySelector('input');
        if (input !== null) input.focus();
    }

    hide() {
        this.menu.style.display = 'none';
        this.menu.classList.remove('anim', 'menuIn');
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