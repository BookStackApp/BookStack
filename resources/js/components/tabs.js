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
        this.tabs = Array.from(this.container.querySelectorAll('[role="tab"]'));
        this.panels = Array.from(this.container.querySelectorAll('[role="tabpanel"]'));

        this.container.addEventListener('click', event => {
            const button = event.target.closest('[role="tab"]');
            if (button) {
                this.show(button.getAttribute('aria-controls'));
            }
        });
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

}
