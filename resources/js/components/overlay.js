import {fadeIn, fadeOut} from "../services/animations";

class Overlay {

    constructor(elem) {
        this.container = elem;
        elem.addEventListener('click', event => {
             if (event.target === elem) return this.hide();
        });

        window.addEventListener('keyup', event => {
            if (event.key === 'Escape') {
                this.hide();
            }
        });

        let closeButtons = elem.querySelectorAll('.popup-header-close');
        for (let i=0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', this.hide.bind(this));
        }
    }

    hide(onComplete = null) { this.toggle(false, onComplete); }
    show(onComplete = null) { this.toggle(true, onComplete); }

    toggle(show = true, onComplete) {
        if (show) {
            fadeIn(this.container, 240, onComplete);
        } else {
            fadeOut(this.container, 240, onComplete);
        }
    }

    focusOnBody() {
        const body = this.container.querySelector('.popup-body');
        if (body) {
            body.focus();
        }
    }

}

export default Overlay;