
class HeaderMobileToggle {

    constructor(elem) {
        this.elem = elem;
        this.toggleButton = elem.querySelector('.mobile-menu-toggle');
        this.menu = elem.querySelector('.header-links');
        this.open = false;

        this.toggleButton.addEventListener('click', this.onToggle.bind(this));
        this.onWindowClick = this.onWindowClick.bind(this);
    }

    onToggle(event) {
        this.open = !this.open;
        this.menu.classList.toggle('show', this.open);
        if (this.open) {
            window.addEventListener('click', this.onWindowClick)
        } else {
            window.removeEventListener('click', this.onWindowClick)
        }
        event.stopPropagation();
    }

    onWindowClick(event) {
        this.onToggle(event);
    }

}

module.exports = HeaderMobileToggle;