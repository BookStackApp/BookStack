/**
 * Tabs
 * Works by matching 'tabToggle<Key>' with 'tabContent<Key>' sections.
 * @extends {Component}
 */
import {onSelect} from "../services/dom";

class Tabs {

    setup() {
        this.tabContentsByName = {};
        this.tabButtonsByName = {};
        this.allContents = [];
        this.allButtons = [];

        for (const [key, elems] of Object.entries(this.$manyRefs || {})) {
            if (key.startsWith('toggle')) {
                const cleanKey = key.replace('toggle', '').toLowerCase();
                onSelect(elems, e => this.show(cleanKey));
                this.allButtons.push(...elems);
                this.tabButtonsByName[cleanKey] = elems;
            }
            if (key.startsWith('content')) {
                const cleanKey = key.replace('content', '').toLowerCase();
                this.tabContentsByName[cleanKey] = elems;
                this.allContents.push(...elems);
            }
        }
    }

    show(key) {
        this.allContents.forEach(c => {
            c.classList.add('hidden');
            c.classList.remove('selected');
        });
        this.allButtons.forEach(b => b.classList.remove('selected'));

        const contents = this.tabContentsByName[key] || [];
        const buttons = this.tabButtonsByName[key] || [];
        if (contents.length > 0) {
            contents.forEach(c => {
                c.classList.remove('hidden')
                c.classList.add('selected')
            });
            buttons.forEach(b => b.classList.add('selected'));
        }
    }

}

export default Tabs;