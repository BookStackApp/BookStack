import {onSelect} from '../services/dom';
import {KeyboardNavigationHandler} from '../services/keyboard-navigation';
import {Component} from './component';

/**
 * Dropdown
 * Provides some simple logic to create simple dropdown menus.
 */
export class Dropdown extends Component {

    setup() {
        this.container = this.$el;
        this.menu = this.$refs.menu;
        this.toggle = this.$refs.toggle;
        this.moveMenu = this.$opts.moveMenu;
        this.bubbleEscapes = this.$opts.bubbleEscapes === 'true';

        this.direction = (document.dir === 'rtl') ? 'right' : 'left';
        this.body = document.body;
        this.showing = false;

        this.hide = this.hide.bind(this);
        this.setupListeners();
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
            this.menu.style.width = `${menuOriginalRect.width}px`;
            this.menu.style.left = `${menuOriginalRect.left}px`;
            heightOffset = dropUpwards ? (window.innerHeight - menuOriginalRect.top - toggleHeight / 2) : menuOriginalRect.top;
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
        this.menu.addEventListener('mouseleave', this.hide);
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
        for (const dropdown of window.$components.get('dropdown')) {
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
            this.menu.style.left = '';
            this.container.appendChild(this.menu);
        }

        this.showing = false;
    }

    setupListeners() {
        const keyboardNavHandler = new KeyboardNavigationHandler(this.container, event => {
            this.hide();
            this.toggle.focus();
            if (!this.bubbleEscapes) {
                event.stopPropagation();
            }
        }, event => {
            if (event.target.nodeName === 'INPUT') {
                event.preventDefault();
                event.stopPropagation();
            }
            this.hide();
        });

        if (this.moveMenu) {
            keyboardNavHandler.shareHandlingToEl(this.menu);
        }

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
                keyboardNavHandler.focusNext();
            }
        });
    }

}
