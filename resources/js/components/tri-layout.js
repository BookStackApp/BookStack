import {Component} from './component';

export class TriLayout extends Component {

    setup() {
        this.container = this.$refs.container;
        this.tabs = this.$manyRefs.tab;

        this.lastLayoutType = 'none';
        this.onDestroy = null;
        this.scrollCache = {
            content: 0,
            info: 0,
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
        if (window.innerWidth <= 1000) newLayout = 'mobile';
        if (window.innerWidth >= 1400) newLayout = 'desktop';
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
        for (const tab of this.tabs) {
            tab.addEventListener('click', this.mobileTabClick);
        }

        this.onDestroy = () => {
            for (const tab of this.tabs) {
                tab.removeEventListener('click', this.mobileTabClick);
            }
        };
    }

    setupDesktop() {
        //
    }

    /**
     * Action to run when the mobile info toggle bar is clicked/tapped
     * @param event
     */
    mobileTabClick(event) {
        const {tab} = event.target.dataset;
        this.showTab(tab);
    }

    /**
     * Show the content tab.
     * Used by the page-display component.
     */
    showContent() {
        this.showTab('content', false);
    }

    /**
     * Show the given tab
     * @param {String} tabName
     * @param {Boolean }scroll
     */
    showTab(tabName, scroll = true) {
        this.scrollCache[this.lastTabShown] = document.documentElement.scrollTop;

        // Set tab status
        for (const tab of this.tabs) {
            const isActive = (tab.dataset.tab === tabName);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        }

        // Toggle section
        const showInfo = (tabName === 'info');
        this.container.classList.toggle('show-info', showInfo);

        // Set the scroll position from cache
        if (scroll) {
            const pageHeader = document.querySelector('header');
            const defaultScrollTop = pageHeader.getBoundingClientRect().bottom;
            document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
            setTimeout(() => {
                document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
            }, 50);
        }

        this.lastTabShown = tabName;
    }

}
