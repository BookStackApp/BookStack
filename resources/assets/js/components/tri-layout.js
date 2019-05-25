
class TriLayout {

    constructor(elem) {
        this.elem = elem;

        this.lastLayoutType = 'none';
        this.onDestroy = null;
        this.scrollCache = {
            'content': 0,
            'info': 0,
        };
        this.lastTabShown = 'content';

        // Bind any listeners
        this.mobileTabClick = this.mobileTabClick.bind(this);

        // Watch layout changes
        this.updateLayout();
        window.addEventListener('resize', event => {
            this.updateLayout();
        }, {passive: true});
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
        const layoutTabs = document.querySelectorAll('[tri-layout-mobile-tab]');
        for (let tab of layoutTabs) {
            tab.addEventListener('click', this.mobileTabClick);
        }

        this.onDestroy = () => {
            for (let tab of layoutTabs) {
                tab.removeEventListener('click', this.mobileTabClick);
            }
        }
    }

    setupDesktop() {
        //
    }


    /**
     * Action to run when the mobile info toggle bar is clicked/tapped
     * @param event
     */
    mobileTabClick(event) {
        const tab = event.target.getAttribute('tri-layout-mobile-tab');
        this.showTab(tab);
    }

    /**
     * Show the content tab.
     * Used by the page-display component.
     */
    showContent() {
        this.showTab('content');
    }

    /**
     * Show the given tab
     * @param tabName
     */
    showTab(tabName) {
        this.scrollCache[this.lastTabShown] = document.documentElement.scrollTop;

        // Set tab status
        const tabs = document.querySelectorAll('.tri-layout-mobile-tab');
        for (let tab of tabs) {
            const isActive = (tab.getAttribute('tri-layout-mobile-tab') === tabName);
            tab.classList.toggle('active', isActive);
        }

        // Toggle section
        const showInfo = (tabName === 'info');
        this.elem.classList.toggle('show-info', showInfo);

        // Set the scroll position from cache
        const pageHeader = document.querySelector('header');
        const defaultScrollTop = pageHeader.getBoundingClientRect().bottom;
        document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
        setTimeout(() => {
            document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
        }, 50);

        this.lastTabShown = tabName;
    }

}

export default TriLayout;