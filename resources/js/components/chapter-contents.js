import {slideUp, slideDown} from '../services/animations';
import {Component} from './component';

export class ChapterContents extends Component {

    setup() {
        this.list = this.$refs.list;
        this.toggle = this.$refs.toggle;

        this.isOpen = this.toggle.classList.contains('open');
        this.toggle.addEventListener('click', this.click.bind(this));
    }

    open() {
        this.toggle.classList.add('open');
        this.toggle.setAttribute('aria-expanded', 'true');
        slideDown(this.list, 180);
        this.isOpen = true;
    }

    close() {
        this.toggle.classList.remove('open');
        this.toggle.setAttribute('aria-expanded', 'false');
        slideUp(this.list, 180);
        this.isOpen = false;
    }

    click(event) {
        event.preventDefault();
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

}
