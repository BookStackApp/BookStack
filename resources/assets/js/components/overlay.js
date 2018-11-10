
class Overlay {

    constructor(elem) {
        this.container = elem;
        elem.addEventListener('click', event => {
             if (event.target === elem) return this.hide();
        });
        let closeButtons = elem.querySelectorAll('.overlay-close');
        for (let i=0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', this.hide.bind(this));
        }
    }

    toggle(show = true) {
        let start = Date.now();
        let duration = 240;

        function setOpacity() {
            let elapsedTime = (Date.now() - start);
            let targetOpacity = show ? (elapsedTime / duration) : 1-(elapsedTime / duration);
            this.container.style.opacity = targetOpacity;
            if (elapsedTime > duration) {
                this.container.style.display = show ? 'flex' : 'none';
                this.container.style.opacity = '';
            } else {
                requestAnimationFrame(setOpacity.bind(this));
            }
        }

        requestAnimationFrame(setOpacity.bind(this));
    }

    hide() { this.toggle(false); }
    show() { this.toggle(true); }

}

export default Overlay;