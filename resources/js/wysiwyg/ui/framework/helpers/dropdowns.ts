


export function handleDropdown(toggle: HTMLElement, menu: HTMLElement, onOpen: Function|undefined = undefined, onClose: Function|undefined = undefined) {
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

    toggle.addEventListener('click', event => {
        menu.hasAttribute('hidden') ? show() : hide();
    });
    menu.addEventListener('mouseleave', hide);
}