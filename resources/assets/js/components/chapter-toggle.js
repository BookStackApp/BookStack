
class ChapterToggle {

    constructor(elem) {
        this.elem = elem;
        this.isOpen = elem.classList.contains('open');
        elem.addEventListener('click', this.click.bind(this));
    }

    open() {
        const list = this.elem.parentNode.querySelector('.inset-list');

        this.elem.classList.add('open');
        list.style.display = 'block';
        list.style.maxHeight = '';
        const maxHeight = list.getBoundingClientRect().height + 10;
        list.style.maxHeight = '0px';
        list.style.overflow = 'hidden';
        list.style.transition = 'max-height ease-in-out 240ms';

        let transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            list.style.overflow = '';
            list.style.maxHeight = '';
            list.style.transition = '';
            list.style.display = `block`;
            list.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            requestAnimationFrame(() => {
                list.style.maxHeight = `${maxHeight}px`;
                list.addEventListener('transitionend', transitionEndBound)
            });
        }, 1);
    }

    close() {
        const list = this.elem.parentNode.querySelector('.inset-list');

        list.style.display =  'block';
        this.elem.classList.remove('open');
        list.style.maxHeight = list.getBoundingClientRect().height + 'px';
        list.style.overflow = 'hidden';
        list.style.transition = 'max-height ease-in-out 240ms';

        const transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            list.style.overflow = '';
            list.style.maxHeight = '';
            list.style.transition = '';
            list.style.display =  'none';
            list.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            requestAnimationFrame(() => {
                list.style.maxHeight = `0px`;
                list.addEventListener('transitionend', transitionEndBound)
            });
        }, 1);
    }

    click(event) {
        event.preventDefault();
        this.isOpen ?  this.close() : this.open();
        this.isOpen = !this.isOpen;
    }

}

module.exports = ChapterToggle;
