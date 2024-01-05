import {Component} from './component';

// ContextMenu class extends Component class
export class ContextMenu extends Component {

    // setup method initializes the class properties and event listeners
    setup() {
        // Get the header, tabs, container and context menus elements
        this.header = document.getElementById('header');
        this.tabs = document.querySelector('.tri-layout-mobile-tabs');
        this.container = document.querySelector('.tri-layout-container');
        this.contextMenus = document.querySelectorAll('.context-menu');

        // Get the height of the header and tabs, and the margin of the container
        this.headerHeight = this.header.offsetHeight;
        this.tabsHeight = this.tabs ? this.tabs.offsetHeight : 0;
        this.containerMargin = parseFloat(getComputedStyle(this.container).marginLeft);

        // Create a map to store the context menus
        this.contextMenuMap = new Map();
        this.contextMenus.forEach((contextMenu) => {
            const entityType = contextMenu.dataset.entityType;
            const entityId = contextMenu.dataset.entityId;
            const key = entityType + '-' + entityId;
            this.contextMenuMap.set(key, contextMenu);
        });

        // Add event listeners for resize, contextmenu and click events
        window.addEventListener('resize', this.handleResize.bind(this));
        this.container.addEventListener('contextmenu', this.handleContextMenu.bind(this));
        this.container.addEventListener('click', this.handleClick.bind(this));
    }

    // handleResize method updates the tabs height and container margin on window resize
    handleResize() {
        clearTimeout(this.resizeTimeout);
        this.resizeTimeout = setTimeout(() => {
            this.tabsHeight = this.tabs ? this.tabs.offsetHeight : 0;
            this.containerMargin = parseFloat(getComputedStyle(this.container).marginLeft);
        }, 100);
    }

    // handleContextMenu method shows the context menu when right clicked on an entity item
    handleContextMenu(event) {
        const entityItem = event.target.closest('.entity-list-item, .grid-card');
        if (!entityItem) return;

        event.preventDefault();

        const entityType = entityItem.dataset.entityType;
        const entityId = entityItem.dataset.entityId;
        const key = entityType + '-' + entityId;
        const contextMenu = this.contextMenuMap.get(key);

        if (contextMenu) {
            // Remove active class from all context menus
            this.contextMenus.forEach((menu) => {
                menu.classList.remove('active');
            });

            // Add active class to the clicked context menu and set its position
            contextMenu.classList.add('active');
            contextMenu.style.left = event.pageX - this.containerMargin + 'px';
            contextMenu.style.top = event.pageY - this.headerHeight - this.tabsHeight + 'px';
        }
    }

    // handleClick method removes active class from all context menus when clicked outside
    handleClick(event) {
        this.contextMenus.forEach((contextMenu) => {
            contextMenu.classList.remove('active');
        });
    }
}