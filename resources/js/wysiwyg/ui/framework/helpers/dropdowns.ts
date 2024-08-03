


interface HandleDropdownParams {
    toggle: HTMLElement;
    menu: HTMLElement;
    showOnHover?: boolean,
    onOpen?: Function | undefined;
    onClose?: Function | undefined;
}

export function handleDropdown(options: HandleDropdownParams) {
    const {menu, toggle, onClose, onOpen, showOnHover} = options;
    let clickListener: Function|null = null;

    const hide = () => {
        menu.hidden = true;
        if (clickListener) {
            window.removeEventListener('click', clickListener as EventListener);
        }
        if (onClose) {
            onClose();
        }
    };

    const show = () => {
        menu.hidden = false
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

    menu.parentElement?.addEventListener('mouseleave', hide);
}