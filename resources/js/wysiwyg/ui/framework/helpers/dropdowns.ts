interface HandleDropdownParams {
    toggle: HTMLElement;
    menu: HTMLElement;
    showOnHover?: boolean,
    onOpen?: Function | undefined;
    onClose?: Function | undefined;
    showAside?: boolean;
}

function positionMenu(menu: HTMLElement, toggle: HTMLElement, showAside: boolean) {
    const toggleRect = toggle.getBoundingClientRect();
    const menuBounds = menu.getBoundingClientRect();

    menu.style.position = 'fixed';

    if (showAside) {
        let targetLeft = toggleRect.right;
        const isRightOOB = toggleRect.right + menuBounds.width > window.innerWidth;
        if (isRightOOB) {
            targetLeft = Math.max(toggleRect.left - menuBounds.width, 0);
        }

        menu.style.top = toggleRect.top + 'px';
        menu.style.left = targetLeft + 'px';
    } else {
        const isRightOOB = toggleRect.left + menuBounds.width > window.innerWidth;
        let targetLeft = toggleRect.left;
        if (isRightOOB) {
            targetLeft = Math.max(toggleRect.right - menuBounds.width, 0);
        }

        menu.style.top = toggleRect.bottom + 'px';
        menu.style.left = targetLeft + 'px';
    }
}

export class DropDownManager {

    protected dropdownOptions: WeakMap<HTMLElement, HandleDropdownParams> = new WeakMap();
    protected openDropdowns: Set<HTMLElement> = new Set();

    constructor() {
        this.onMenuMouseOver = this.onMenuMouseOver.bind(this);
        this.onWindowClick = this.onWindowClick.bind(this);

        window.addEventListener('click', this.onWindowClick);
    }

    teardown(): void {
        window.removeEventListener('click', this.onWindowClick);
    }

    protected onWindowClick(event: MouseEvent): void {
        const target = event.target as HTMLElement;
        this.closeAllNotContainingElement(target);
    }

    protected closeAllNotContainingElement(element: HTMLElement): void {
        for (const menu of this.openDropdowns) {
            if (!menu.parentElement?.contains(element)) {
                this.closeDropdown(menu);
            }
        }
    }

    protected onMenuMouseOver(event: MouseEvent): void {
        const target = event.target as HTMLElement;
        this.closeAllNotContainingElement(target);
    }

    /**
     * Close all open dropdowns.
     */
    public closeAll(): void {
        for (const menu of this.openDropdowns) {
            this.closeDropdown(menu);
        }
    }

    protected closeDropdown(menu: HTMLElement): void {
        menu.hidden = true;
        menu.style.removeProperty('position');
        menu.style.removeProperty('left');
        menu.style.removeProperty('top');

        this.openDropdowns.delete(menu);
        menu.removeEventListener('mouseover', this.onMenuMouseOver);

        const onClose = this.getOptions(menu).onClose;
        if (onClose) {
            onClose();
        }
    }

    protected openDropdown(menu: HTMLElement): void {
        const {toggle, showAside, onOpen} = this.getOptions(menu);
        menu.hidden = false
        positionMenu(menu, toggle, Boolean(showAside));

        this.openDropdowns.add(menu);
        menu.addEventListener('mouseover', this.onMenuMouseOver);

        if (onOpen) {
            onOpen();
        }
    }

    protected getOptions(menu: HTMLElement): HandleDropdownParams {
        const options = this.dropdownOptions.get(menu);
        if (!options) {
            throw new Error(`Can't find options for dropdown menu`);
        }

        return options;
    }

    /**
     * Add handling for a new dropdown.
     */
     public handle(options: HandleDropdownParams) {
        const {menu, toggle, showOnHover} = options;

        // Register dropdown
        this.dropdownOptions.set(menu, options);

        // Configure default events
        const toggleShowing = (event: MouseEvent) => {
            menu.hasAttribute('hidden') ? this.openDropdown(menu) : this.closeDropdown(menu);
        };
        toggle.addEventListener('click', toggleShowing);
        if (showOnHover) {
            toggle.addEventListener('mouseenter', () => {
                this.openDropdown(menu);
            });
        }
    }
}