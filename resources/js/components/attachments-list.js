/**
 * Attachments List
 * Adds '?open=true' query to file attachment links
 * when ctrl/cmd is pressed down.
 * @extends {Component}
 */
class AttachmentsList {

    setup() {
        this.container = this.$el;
        this.setupListeners();
    }

    setupListeners() {
        const isExpectedKey = (event) => event.key === 'Control' || event.key === 'Meta';
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
        const links = this.container.querySelectorAll('a.attachment-file');
        for (const link of links) {
            if (link.href.split('?')[1] !== 'open=true') {
                link.href = link.href + '?open=true';
                link.setAttribute('target', '_blank');
            }
        }
    }

    removeOpenQueryFromLinks() {
        const links = this.container.querySelectorAll('a.attachment-file');
        for (const link of links) {
            link.href = link.href.split('?')[0];
            link.removeAttribute('target');
        }
    }
}

export default AttachmentsList;