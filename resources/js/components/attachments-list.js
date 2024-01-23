import {Component} from './component';

/**
 * Attachments List
 * Adds '?open=true' query to file attachment links
 * when ctrl/cmd is pressed down.
 */
export class AttachmentsList extends Component {

    setup() {
        this.container = this.$el;
        this.fileLinks = this.$manyRefs.linkTypeFile;

        this.setupListeners();
    }

    setupListeners() {
        const isExpectedKey = event => event.key === 'Control' || event.key === 'Meta';
        window.addEventListener('keydown', event => {
            if (isExpectedKey(event)) {
                this.addOpenQueryToLinks();
            }
        }, {passive: true});
        window.addEventListener('keyup', event => {
            if (isExpectedKey(event)) {
                this.removeOpenQueryFromLinks();
            }
        }, {passive: true});
    }

    addOpenQueryToLinks() {
        for (const link of this.fileLinks) {
            if (link.href.split('?')[1] !== 'open=true') {
                link.href += '?open=true';
                link.setAttribute('target', '_blank');
            }
        }
    }

    removeOpenQueryFromLinks() {
        for (const link of this.fileLinks) {
            link.href = link.href.split('?')[0];
            link.removeAttribute('target');
        }
    }

}
