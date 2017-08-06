
class ChapterToggle {

    constructor(elem) {
        this.elem = elem;
        this.isOpen = elem.classList.contains('open');
        elem.addEventListener('click', this.click.bind(this));
    }

    open() {
        let list = this.elem.parentNode.querySelector('.inset-list');

        this.elem.classList.add('open');
        list.style.display = 'block';
        list.style.height = '';
        let height = list.getBoundingClientRect().height;
        list.style.height = '0px';
        list.style.overflow = 'hidden';
        list.style.transition = 'height ease-in-out 240ms';

        let transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            list.style.overflow = '';
            list.style.height = '';
            list.style.transition = '';
            list.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            list.style.height = `${height}px`;
            list.addEventListener('transitionend', transitionEndBound)
        }, 1);
    }

    close() {
        let list = this.elem.parentNode.querySelector('.inset-list');

        this.elem.classList.remove('open');
        list.style.display =  'block';
        list.style.height = list.getBoundingClientRect().height + 'px';
        list.style.overflow = 'hidden';
        list.style.transition = 'height ease-in-out 240ms';

        let transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            list.style.overflow = '';
            list.style.height = '';
            list.style.transition = '';
            list.style.display =  'none';
            list.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            list.style.height = `0px`;
            list.addEventListener('transitionend', transitionEndBound)
        }, 1);
    }

    click(event) {
        event.preventDefault();
        this.isOpen ?  this.close() : this.open();
        this.isOpen = !this.isOpen;
    }

}

module.exports = ChapterToggle;