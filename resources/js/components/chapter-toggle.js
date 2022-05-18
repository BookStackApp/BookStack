import {slideUp, slideDown} from "../services/animations";

class ChapterToggle {

    constructor(elem) {
        this.elem = elem;
        this.isOpen = elem.classList.contains('open');
        elem.addEventListener('click', this.click.bind(this));
    }

    open() {
        const list = this.elem.parentNode.querySelector('.inset-list');
        this.elem.classList.add('open');
        this.elem.setAttribute('aria-expanded', 'true');
        slideDown(list, 180);
    }

    close() {
        const list = this.elem.parentNode.querySelector('.inset-list');
        this.elem.classList.remove('open');
        this.elem.setAttribute('aria-expanded', 'false');
        slideUp(list, 180);
    }

    click(event) {
        event.preventDefault();
        this.isOpen ?  this.close() : this.open();
        this.isOpen = !this.isOpen;
    }

}

export default ChapterToggle;
