
class ExpandToggle {

    constructor(elem) {
        this.elem = elem;

        // Component state
        this.isOpen = elem.getAttribute('expand-toggle-is-open') === 'yes';
        this.updateEndpoint = elem.getAttribute('expand-toggle-update-endpoint');
        this.selector = elem.getAttribute('expand-toggle');

        // Listener setup
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

        const matchingElems = document.querySelectorAll(this.selector);
        for (let match of matchingElems) {
            this.isOpen ?  this.close(match) : this.open(match);
        }

        this.isOpen = !this.isOpen;
        this.updateSystemAjax(this.isOpen);
    }

    updateSystemAjax(isOpen) {
        window.$http.patch(this.updateEndpoint, {
            expand: isOpen ? 'true' : 'false'
        });
    }

}

export default ExpandToggle;