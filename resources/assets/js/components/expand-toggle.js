
class ExpandToggle {

    constructor(elem) {
        this.elem = elem;
        this.isOpen = false;
        this.selector = elem.getAttribute('expand-toggle');
        elem.addEventListener('click', this.click.bind(this));
    }

    open(elemToToggle) {
        elemToToggle.style.display = 'block';
        elemToToggle.style.height = '';
        let height = elemToToggle.getBoundingClientRect().height;
        elemToToggle.style.height = '0px';
        elemToToggle.style.overflow = 'hidden';
        elemToToggle.style.transition = 'height ease-in-out 240ms';

        let transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            elemToToggle.style.overflow = '';
            elemToToggle.style.height = '';
            elemToToggle.style.transition = '';
            elemToToggle.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            elemToToggle.style.height = `${height}px`;
            elemToToggle.addEventListener('transitionend', transitionEndBound)
        }, 1);
    }

    close(elemToToggle) {
        elemToToggle.style.display =  'block';
        elemToToggle.style.height = elemToToggle.getBoundingClientRect().height + 'px';
        elemToToggle.style.overflow = 'hidden';
        elemToToggle.style.transition = 'all ease-in-out 240ms';

        let transitionEndBound = onTransitionEnd.bind(this);
        function onTransitionEnd() {
            elemToToggle.style.overflow = '';
            elemToToggle.style.height = '';
            elemToToggle.style.transition = '';
            elemToToggle.style.display =  'none';
            elemToToggle.removeEventListener('transitionend', transitionEndBound);
        }

        setTimeout(() => {
            elemToToggle.style.height = `0px`;
            elemToToggle.addEventListener('transitionend', transitionEndBound)
        }, 1);
    }

    click(event) {
        event.preventDefault();
        let matchingElems = document.querySelectorAll(this.selector);
        for (let i = 0, len = matchingElems.length; i < len; i++) {
            this.isOpen ?  this.close(matchingElems[i]) : this.open(matchingElems[i]);
        }
        this.isOpen = !this.isOpen;
    }

}

module.exports = ExpandToggle;