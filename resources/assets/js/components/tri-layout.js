
class TriLayout {

    constructor(elem) {
        this.elem = elem;
        this.middle = elem.querySelector('.tri-layout-middle');
        this.right = elem.querySelector('.tri-layout-right');
        this.left = elem.querySelector('.tri-layout-left');

        this.lastLayoutType = 'none';
        this.onDestroy = null;


        this.updateLayout();
        window.addEventListener('resize', event => {
            this.updateLayout();
        });
    }

    updateLayout() {
        let newLayout = 'tablet';
        if (window.innerWidth <= 1000) newLayout =  'mobile';
        if (window.innerWidth >= 1400) newLayout =  'desktop';
        if (newLayout === this.lastLayoutType) return;

        if (this.onDestroy) {
            this.onDestroy();
            this.onDestroy = null;
        }

        if (newLayout === 'desktop') {
            this.setupDesktop();
        } else if (newLayout === 'mobile') {
            this.setupMobile();
        }

        this.lastLayoutType = newLayout;
    }

    setupMobile() {
        const mobileSidebarClickBound = this.mobileSidebarClick.bind(this);
        const mobileContentClickBound = this.mobileContentClick.bind(this);
        this.left.addEventListener('click', mobileSidebarClickBound);
        this.right.addEventListener('click', mobileSidebarClickBound);
        this.middle.addEventListener('click', mobileContentClickBound);

        this.onDestroy = () => {
            this.left.removeEventListener('click', mobileSidebarClickBound);
            this.right.removeEventListener('click', mobileSidebarClickBound);
            this.middle.removeEventListener('click', mobileContentClickBound);
        }
    }

    setupDesktop() {
        //
    }

    /**
     * Slide the main content back into view if clicked and
     * currently slid out of view.
     * @param event
     */
    mobileContentClick(event) {
        this.elem.classList.remove('mobile-open');
    }

    /**
     * On sidebar click, Show the content by sliding the main content out.
     * @param event
     */
    mobileSidebarClick(event) {
        if (this.elem.classList.contains('mobile-open')) {
            this.elem.classList.remove('mobile-open');
        } else {
            event.preventDefault();
            event.stopPropagation();
            this.elem.classList.add('mobile-open');
        }
    }

}

export default TriLayout;