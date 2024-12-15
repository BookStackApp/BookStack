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

export function handleDropdown(options: HandleDropdownParams) {
    const {menu, toggle, onClose, onOpen, showOnHover, showAside} = options;
    let clickListener: Function|null = null;

    const hide = () => {
        menu.hidden = true;
        menu.style.removeProperty('position');
        menu.style.removeProperty('left');
        menu.style.removeProperty('top');
        if (clickListener) {
            window.removeEventListener('click', clickListener as EventListener);
        }
        if (onClose) {
            onClose();
        }
    };

    const show = () => {
        menu.hidden = false
        positionMenu(menu, toggle, Boolean(showAside));
        clickListener = (event: MouseEvent) => {
            if (!toggle.contains(event.target as HTMLElement) && !menu.contains(event.target as HTMLElement)) {
                hide();
            }
        }
        window.addEventListener('click', clickListener as EventListener);
        if (onOpen) {
            onOpen();
        }
    };

    const toggleShowing = (event: MouseEvent) => {
        menu.hasAttribute('hidden') ? show() : hide();
    };
    toggle.addEventListener('click', toggleShowing);
    if (showOnHover) {
        toggle.addEventListener('mouseenter', toggleShowing);
    }

    menu.parentElement?.addEventListener('mouseleave', (event: MouseEvent) => {

        // Prevent mouseleave hiding if withing the same bounds of the toggle.
        // Avoids hiding in the event the mouse is interrupted by a high z-index
        // item like a browser scrollbar.
        const toggleBounds = toggle.getBoundingClientRect();
        const withinX = event.clientX <= toggleBounds.right && event.clientX >= toggleBounds.left;
        const withinY = event.clientY <= toggleBounds.bottom && event.clientY >= toggleBounds.top;
        const withinToggle = withinX && withinY;

        if (!withinToggle) {
            hide();
        }
    });
}