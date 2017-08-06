
class Notification {

    constructor(elem) {
        this.elem = elem;
        this.type = elem.getAttribute('notification');
        this.textElem = elem.querySelector('span');
        this.autohide = this.elem.hasAttribute('data-autohide');
        window.Events.listen(this.type, text => {
            console.log('show', text);
            this.show(text);
        });
        elem.addEventListener('click', this.hide.bind(this));
        if (elem.hasAttribute('data-show')) this.show(this.textElem.textContent);
    }

    show(textToShow = '') {
        this.textElem.textContent = textToShow;
        this.elem.style.display = 'block';
        setTimeout(() => {
            this.elem.classList.add('showing');
        }, 1);

        if (this.autohide) setTimeout(this.hide.bind(this), 2000);
    }

    hide() {
        this.elem.classList.remove('showing');

        function transitionEnd() {
            this.elem.style.display = 'none';
            this.elem.removeEventListener('transitionend', transitionEnd);
        }

        this.elem.addEventListener('transitionend', transitionEnd.bind(this));
    }

}

module.exports = Notification;