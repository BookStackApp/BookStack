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
        slideDown(list, 240);
    }

    close() {
        const list = this.elem.parentNode.querySelector('.inset-list');
        this.elem.classList.remove('open');
        slideUp(list, 240);
    }

    click(event) {
        event.preventDefault();
        this.isOpen ?  this.close() : this.open();
        this.isOpen = !this.isOpen;
    }

}

export default ChapterToggle;
