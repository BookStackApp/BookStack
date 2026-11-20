import {findClosestScrollContainer, onSelect} from '../services/dom';
import {KeyboardNavigationHandler} from '../services/keyboard-navigation';
import {Component} from './component';

/**
 * Dropdown
 * Provides some simple logic to create simple dropdown menus.
 */
export class Dropdown extends Component {

    protected container!: HTMLElement;
    protected menu!: HTMLElement;
    protected toggle!: HTMLElement;

    protected fixedPositionMenu: boolean = false;
    protected bubbleEscapes: boolean = false;
    protected direction: string = 'left';
    protected body!: HTMLElement;
    protected showing: boolean = false;
    protected onCloseAbortController: AbortController = new AbortController();

    setup() {
        this.container = this.$el;
        this.menu = this.$refs.menu;
        this.toggle = this.$refs.toggle;
        this.fixedPositionMenu = this.$opts.fixedPositionMenu === 'true';
        this.bubbleEscapes = this.$opts.bubbleEscapes === 'true';

        this.direction = (document.dir === 'rtl') ? 'right' : 'left';
        this.body = document.body;

        this.hide = this.hide.bind(this);
        this.setupListeners();
    }

    show(event: Event|null = null) {
        this.hideAll();

        this.menu.style.display = 'block';
        this.menu.classList.add('anim', 'menuIn');
        this.toggle.setAttribute('aria-expanded', 'true');

        const menuOriginalRect = this.menu.getBoundingClientRect();
        let heightOffset = 0;
        const toggleHeight = this.toggle.getBoundingClientRect().height;
        const containerEl = this.fixedPositionMenu ? this.body : findClosestScrollContainer(this.menu);
        const containerBounds = containerEl.getBoundingClientRect();
        const dropUpwards = menuOriginalRect.bottom > containerBounds.bottom;
        const containerRect = this.container.getBoundingClientRect();

        // If enabled, Move to body to prevent being trapped within scrollable sections
        if (this.fixedPositionMenu) {
            this.menu.style.position = 'fixed';
            this.menu.style.width = `${menuOriginalRect.width}px`;
            this.menu.style.left = `${menuOriginalRect.left}px`;
            if (dropUpwards) {
                heightOffset = (window.innerHeight - menuOriginalRect.top - toggleHeight / 2);
            } else {
                heightOffset = menuOriginalRect.top;
            }
        }

        // Adjust menu to display upwards if near the bottom of the screen
        if (dropUpwards) {
            this.menu.style.top = 'initial';
            this.menu.style.bottom = `${heightOffset}px`;
            const maxHeight = (window.innerHeight - 40) - (window.innerHeight - containerRect.bottom);
            this.menu.style.maxHeight = `${Math.floor(maxHeight)}px`;
        } else {
            this.menu.style.top = `${heightOffset}px`;
            this.menu.style.bottom = 'initial';
            const maxHeight = (window.innerHeight - 40) - containerRect.top;
            this.menu.style.maxHeight = `${Math.floor(maxHeight)}px`;
        }

        this.onCloseAbortController = new AbortController();

        // Set listener to hide on mouse leave or window click
        this.menu.addEventListener('mouseleave', (event: MouseEvent) => {
            const focusedOnInputWithin = document.activeElement
                && this.menu.contains(document.activeElement)
                && document.activeElement.matches('input,textarea,select');
            if (!focusedOnInputWithin) {
                this.hide();
            }
        }, { signal: this.onCloseAbortController.signal });
        window.addEventListener('click', clickEvent => {
            if (!this.menu.contains(clickEvent.target as HTMLElement)) {
                this.hide();
            }
        }, { signal: this.onCloseAbortController.signal });

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
        for (const dropdown of window.$components.get('dropdown') as Dropdown[]) {
            dropdown.hide();
        }
    }

    hide() {
        this.menu.style.display = 'none';
        this.menu.classList.remove('anim', 'menuIn');
        this.toggle.setAttribute('aria-expanded', 'false');
        this.menu.style.top = '';
        this.menu.style.bottom = '';
        this.menu.style.maxHeight = '';

        if (this.fixedPositionMenu) {
            this.menu.style.position = '';
            this.menu.style.setProperty(this.direction, '');
            this.menu.style.width = '';
            this.menu.style.left = '';
        }

        this.showing = false;
        this.onCloseAbortController.abort();
    }

    setupListeners() {
        const keyboardNavHandler = new KeyboardNavigationHandler(this.container, event => {
            this.hide();
            this.toggle.focus();
            if (!this.bubbleEscapes) {
                event.stopPropagation();
            }
        }, event => {
            if (event.target instanceof HTMLElement && event.target.nodeName === 'INPUT') {
                event.preventDefault();
                event.stopPropagation();
            }
            this.hide();
        });

        // Hide menu on option click
        this.container.addEventListener('click', event => {
            const possibleChildren: HTMLElement[] = Array.from(this.menu.querySelectorAll('a'));
            if (event.target instanceof HTMLElement && possibleChildren.includes(event.target)) {
                this.hide();
            }
        });

        onSelect(this.toggle, event => {
            event.stopPropagation();
            event.preventDefault();
            this.show(event);
            if (event instanceof KeyboardEvent) {
                keyboardNavHandler.focusNext();
            }
        });
    }

}
