import {Component} from './component';

/**
 * Tabs
 * Uses accessible attributes to drive its functionality.
 * On tab wrapping element:
 * - role=tablist
 * On tabs (Should be a button):
 * - id
 * - role=tab
 * - aria-selected=true/false
 * - aria-controls=<id-of-panel-section>
 * On panels:
 * - id
 * - tabindex=0
 * - role=tabpanel
 * - aria-labelledby=<id-of-tab-for-panel>
 * - hidden (If not shown by default).
 */
export class Tabs extends Component {

    setup() {
        this.container = this.$el;
        this.tabList = this.container.querySelector('[role="tablist"]');
        this.tabs = Array.from(this.tabList.querySelectorAll('[role="tab"]'));
        this.panels = Array.from(this.container.querySelectorAll(':scope > [role="tabpanel"], :scope > * > [role="tabpanel"]'));
        this.activeUnder = this.$opts.activeUnder ? Number(this.$opts.activeUnder) : 10000;
        this.active = null;

        this.container.addEventListener('click', event => {
            const tab = event.target.closest('[role="tab"]');
            if (tab && this.tabs.includes(tab)) {
                this.show(tab.getAttribute('aria-controls'));
            }
        });

        window.addEventListener('resize', this.updateActiveState.bind(this), {
            passive: true,
        });
        this.updateActiveState();
    }

    show(sectionId) {
        for (const panel of this.panels) {
            panel.toggleAttribute('hidden', panel.id !== sectionId);
        }

        for (const tab of this.tabs) {
            const tabSection = tab.getAttribute('aria-controls');
            const selected = tabSection === sectionId;
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
        }

        this.$emit('change', {showing: sectionId});
    }

    updateActiveState() {
        const active = window.innerWidth < this.activeUnder;
        if (active === this.active) {
            return;
        }

        if (active) {
            this.activate();
        } else {
            this.deactivate();
        }

        this.active = active;
    }

    activate() {
        const panelToShow = this.panels.find(p => !p.hasAttribute('hidden')) || this.panels[0];
        this.show(panelToShow.id);
        this.tabList.toggleAttribute('hidden', false);
    }

    deactivate() {
        for (const panel of this.panels) {
            panel.removeAttribute('hidden');
        }
        for (const tab of this.tabs) {
            tab.setAttribute('aria-selected', 'false');
        }
        this.tabList.toggleAttribute('hidden', true);
    }

}
