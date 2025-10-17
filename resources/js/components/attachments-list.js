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
        this.previewLinks = Array.from(this.container.querySelectorAll('[data-attachment-preview]'));

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

        this.setupPreviewLinks();
    }

    setupPreviewLinks() {
        for (const link of this.previewLinks) {
            link.addEventListener('click', event => {
                event.preventDefault();
                const attachmentId = Number(link.dataset.attachmentPreview || '');
                if (!attachmentId) {
                    return;
                }

                this.$emit('preview', {
                    attachmentId,
                    attachmentName: link.dataset.attachmentName || '',
                    attachmentExtension: link.dataset.attachmentExtension || '',
                    attachmentUrl: link.dataset.attachmentUrl || '',
                });
            });
        }
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
