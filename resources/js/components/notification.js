import {Component} from "./component";

export class Notification  extends Component {

    setup() {
        this.container = this.$el;
        this.type = this.$opts.type;
        this.textElem = this.container.querySelector('span');
        this.autoHide = this.$opts.autoHide === 'true';
        this.initialShow = this.$opts.show === 'true'
        this.container.style.display = 'grid';

        window.$events.listen(this.type, text => {
            this.show(text);
        });
        this.container.addEventListener('click', this.hide.bind(this));

        if (this.initialShow) {
            setTimeout(() => this.show(this.textElem.textContent), 100);
        }

        this.hideCleanup = this.hideCleanup.bind(this);
    }

    show(textToShow = '') {
        this.container.removeEventListener('transitionend', this.hideCleanup);
        this.textElem.textContent = textToShow;
        this.container.style.display = 'grid';
        setTimeout(() => {
            this.container.classList.add('showing');
        }, 1);

        if (this.autoHide) {
            const words = textToShow.split(' ').length;
            const timeToShow = Math.max(2000, 1000 + (250 * words));
            setTimeout(this.hide.bind(this), timeToShow);
        }
    }

    hide() {
        this.container.classList.remove('showing');
        this.container.addEventListener('transitionend', this.hideCleanup);
    }

    hideCleanup() {
        this.container.style.display = 'none';
        this.container.removeEventListener('transitionend', this.hideCleanup);
    }

}