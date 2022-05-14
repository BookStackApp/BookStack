import {onSelect} from "../services/dom";

/**
 * Dropdown
 * Provides some simple logic to create simple dropdown menus.
 * @extends {Component}
 */
class DropDown {

    setup() {
        this.container = this.$el;
        this.menu = this.$refs.menu;
        this.toggle = this.$refs.toggle;
        this.moveMenu = this.$opts.moveMenu;
        this.bubbleEscapes = this.$opts.bubbleEscapes === 'true';

        this.direction = (document.dir === 'rtl') ? 'right' : 'left';
        this.body = document.body;
        this.showing = false;
        this.setupListeners();
        this.hide = this.hide.bind(this);
    }

    show(event = null) {
        this.hideAll();

        this.menu.style.display = 'block';
        this.menu.classList.add('anim', 'menuIn');
        this.toggle.setAttribute('aria-expanded', 'true');

        const menuOriginalRect = this.menu.getBoundingClientRect();
        let heightOffset = 0;
        const toggleHeight = this.toggle.getBoundingClientRect().height;
        const dropUpwards = menuOriginalRect.bottom > window.innerHeight;

        // If enabled, Move to body to prevent being trapped within scrollable sections
        if (this.moveMenu) {
            this.body.appendChild(this.menu);
            this.menu.style.position = 'fixed';
            if (this.direction === 'right') {
                this.menu.style.right = `${(menuOriginalRect.right - menuOriginalRect.width)}px`;
            } else {
                this.menu.style.left = `${menuOriginalRect.left}px`;
            }
            this.menu.style.width = `${menuOriginalRect.width}px`;
            heightOffset = dropUpwards ? (window.innerHeight - menuOriginalRect.top  - toggleHeight / 2) : menuOriginalRect.top;
        }

        // Adjust menu to display upwards if near the bottom of the screen
        if (dropUpwards) {
            this.menu.style.top = 'initial';
            this.menu.style.bottom = `${heightOffset}px`;
        } else {
            this.menu.style.top = `${heightOffset}px`;
            this.menu.style.bottom = 'initial';
        }

        // Set listener to hide on mouse leave or window click
        this.menu.addEventListener('mouseleave', this.hide.bind(this));
        window.addEventListener('click', event => {
            if (!this.menu.contains(event.target)) {
                this.hide();
            }
        });

        // Focus on first input if existing
        const input = this.menu.querySelector('input');
        if (input !== null) input.focus();

        this.showing = true;

        const showEvent = new Event('show');
        this.container.dispatchEvent(showEvent);

        if (event) {
            event.stopPropagation();
        }
    }

    hideAll() {
        for (let dropdown of window.components.dropdown) {
            dropdown.hide();
        }
    }

    hide() {
        this.menu.style.display = 'none';
        this.menu.classList.remove('anim', 'menuIn');
        this.toggle.setAttribute('aria-expanded', 'false');
        this.menu.style.top = '';
        this.menu.style.bottom = '';

        if (this.moveMenu) {
            this.menu.style.position = '';
            this.menu.style[this.direction] = '';
            this.menu.style.width = '';
            this.container.appendChild(this.menu);
        }

        this.showing = false;
    }

    getFocusable() {
        return Array.from(this.menu.querySelectorAll('[tabindex]:not([tabindex="-1"]),[href],button,input:not([type=hidden])'));
    }

    focusNext() {
        const focusable = this.getFocusable();
        const currentIndex = focusable.indexOf(document.activeElement);
        let newIndex = currentIndex + 1;
        if (newIndex >= focusable.length) {
            newIndex = 0;
        }

        focusable[newIndex].focus();
    }

    focusPrevious() {
        const focusable = this.getFocusable();
        const currentIndex = focusable.indexOf(document.activeElement);
        let newIndex = currentIndex - 1;
        if (newIndex < 0) {
            newIndex = focusable.length - 1;
        }

        focusable[newIndex].focus();
    }

    setupListeners() {
        // Hide menu on option click
        this.container.addEventListener('click', event => {
             const possibleChildren = Array.from(this.menu.querySelectorAll('a'));
             if (possibleChildren.includes(event.target)) {
                 this.hide();
             }
        });

        onSelect(this.toggle, event => {
            event.stopPropagation();
            this.show(event);
            if (event instanceof KeyboardEvent) {
                this.focusNext();
            }
        });

        // Keyboard navigation
        const keyboardNavigation = event => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
                this.focusNext();
                event.preventDefault();
            } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
                this.focusPrevious();
                event.preventDefault();
            } else if (event.key === 'Escape') {
                this.hide();
                this.toggle.focus();
                if (!this.bubbleEscapes) {
                    event.stopPropagation();
                }
            }
        };
        this.container.addEventListener('keydown', keyboardNavigation);
        if (this.moveMenu) {
            this.menu.addEventListener('keydown', keyboardNavigation);
        }

        // Hide menu on enter press or escape
        this.menu.addEventListener('keydown ', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                event.stopPropagation();
                this.hide();
            }
        });
    }

}

export default DropDown;