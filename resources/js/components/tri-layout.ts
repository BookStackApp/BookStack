import {Component} from './component';

export class TriLayout extends Component {
    private container!: HTMLElement;
    private tabs!: HTMLElement[];
    private sidebarScrollContainers!: HTMLElement[];

    private lastLayoutType = 'none';
    private onDestroy: (()=>void)|null = null;
    private scrollCache: Record<string, number> = {
        content: 0,
        info: 0,
    };
    private lastTabShown = 'content';

    setup(): void {
        this.container = this.$refs.container;
        this.tabs = this.$manyRefs.tab;
        this.sidebarScrollContainers = this.$manyRefs.sidebarScrollContainer;

        // Bind any listeners
        this.mobileTabClick = this.mobileTabClick.bind(this);

        // Watch layout changes
        this.updateLayout();
        window.addEventListener('resize', () => {
            this.updateLayout();
        }, {passive: true});

        this.setupSidebarScrollHandlers();
    }

    updateLayout(): void {
        let newLayout = 'tablet';
        if (window.innerWidth <= 1000) newLayout = 'mobile';
        if (window.innerWidth > 1400) newLayout = 'desktop';
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

    setupDesktop(): void {
        //
    }

    /**
     * Action to run when the mobile info toggle bar is clicked/tapped
     */
    mobileTabClick(event: MouseEvent): void {
        const tab = (event.target as HTMLElement).dataset.tab || '';
        this.showTab(tab);
    }

    /**
     * Show the content tab.
     * Used by the page-display component.
     */
    showContent(): void {
        this.showTab('content', false);
    }

    /**
     * Show the given tab
     */
    showTab(tabName: string, scroll: boolean = true): void {
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
            const pageHeader = document.querySelector('header') as HTMLElement;
            const defaultScrollTop = pageHeader.getBoundingClientRect().bottom;
            document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
            setTimeout(() => {
                document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
            }, 50);
        }

        this.lastTabShown = tabName;
    }

    setupSidebarScrollHandlers(): void {
        for (const sidebar of this.sidebarScrollContainers) {
            sidebar.addEventListener('scroll', () => this.handleSidebarScroll(sidebar), {
                passive: true,
            });
            this.handleSidebarScroll(sidebar);
        }

        window.addEventListener('resize', () => {
            for (const sidebar of this.sidebarScrollContainers) {
                this.handleSidebarScroll(sidebar);
            }
        });
    }

    handleSidebarScroll(sidebar: HTMLElement): void {
        const scrollable = sidebar.clientHeight !== sidebar.scrollHeight;
        const atTop = sidebar.scrollTop === 0;
        const atBottom = (sidebar.scrollTop + sidebar.clientHeight) === sidebar.scrollHeight;

        if (sidebar.parentElement) {
            sidebar.parentElement.classList.toggle('scroll-away-from-top', !atTop && scrollable);
            sidebar.parentElement.classList.toggle('scroll-away-from-bottom', !atBottom && scrollable);
        }
    }

}
