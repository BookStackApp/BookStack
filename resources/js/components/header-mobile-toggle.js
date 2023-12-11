import {Component} from './component';

export class HeaderMobileToggle extends Component {

    setup() {
        this.elem = this.$el;
        this.toggleButton = this.$refs.toggle;
        this.menu = this.$refs.menu;

        this.open = false;
        this.toggleButton.addEventListener('click', this.onToggle.bind(this));
        this.onWindowClick = this.onWindowClick.bind(this);
        this.onKeyDown = this.onKeyDown.bind(this);
    }

    onToggle(event) {
        this.open = !this.open;
        this.menu.classList.toggle('show', this.open);
        this.toggleButton.setAttribute('aria-expanded', this.open ? 'true' : 'false');
        if (this.open) {
            this.elem.addEventListener('keydown', this.onKeyDown);
            window.addEventListener('click', this.onWindowClick);
        } else {
            this.elem.removeEventListener('keydown', this.onKeyDown);
            window.removeEventListener('click', this.onWindowClick);
        }
        event.stopPropagation();
    }

    onKeyDown(event) {
        if (event.code === 'Escape') {
            this.onToggle(event);
        }
    }

    onWindowClick(event) {
        this.onToggle(event);
    }

}
