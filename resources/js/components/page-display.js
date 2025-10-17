import * as DOM from '../services/dom.ts';
import {escapeHtml, scrollAndHighlightElement} from '../services/util.ts';
import {Component} from './component';

function toggleAnchorHighlighting(elementId, shouldHighlight) {
    DOM.forEach(`#page-navigation a[href="#${elementId}"]`, anchor => {
        anchor.closest('li').classList.toggle('current-heading', shouldHighlight);
    });
}

function headingVisibilityChange(entries) {
    for (const entry of entries) {
        const isVisible = (entry.intersectionRatio === 1);
        toggleAnchorHighlighting(entry.target.id, isVisible);
    }
}

function addNavObserver(headings) {
    // Setup the intersection observer.
    const intersectOpts = {
        rootMargin: '0px 0px 0px 0px',
        threshold: 1.0,
    };
    const pageNavObserver = new IntersectionObserver(headingVisibilityChange, intersectOpts);

    // observe each heading
    for (const heading of headings) {
        pageNavObserver.observe(heading);
    }
}

export class PageDisplay extends Component {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;
        this.contentContainer = this.$refs.content || null;
        this.previewContainer = this.$refs.preview || null;
        this.previewBody = this.$refs.previewBody || null;
        this.previewTitle = this.$refs.previewTitle || null;
        this.previewClose = this.$refs.previewClose || null;
        this.previewRequestToken = null;

        window.importVersioned('code').then(Code => Code.highlight());
        this.setupAttachmentPreview();
        this.setupNavHighlighting();

        // Check the hash on load
        if (window.location.hash) {
            const text = window.location.hash.replace(/%20/g, ' ').substring(1);
            this.goToText(text);
        }

        // Sidebar page nav click event
        const sidebarPageNav = document.querySelector('.sidebar-page-nav');
        if (sidebarPageNav) {
            DOM.onChildEvent(sidebarPageNav, 'a', 'click', (event, child) => {
                event.preventDefault();
                window.$components.first('tri-layout').showContent();
                const contentId = child.getAttribute('href').substr(1);
                this.goToText(contentId);
                window.history.pushState(null, null, `#${contentId}`);
            });
        }
    }

    goToText(text) {
        this.hideAttachmentPreview();
        const idElem = document.getElementById(text);

        DOM.forEach('[data-page-content] [data-highlighted]', elem => {
            elem.removeAttribute('data-highlighted');
            elem.style.backgroundColor = null;
        });

        if (idElem !== null && (!this.contentContainer || this.contentContainer.contains(idElem))) {
            scrollAndHighlightElement(idElem);
        } else {
            const textElem = DOM.findText('[data-page-content] > *', text);
            if (textElem) {
                scrollAndHighlightElement(textElem);
            }
        }
    }

    setupNavHighlighting() {
        const pageNav = document.querySelector('.sidebar-page-nav');
        const contentRoot = this.contentContainer || document.querySelector('[data-page-content]') || document.querySelector('.page-content');
        if (!contentRoot) {
            return;
        }

        // fetch all the headings.
        const headings = contentRoot.querySelectorAll('h1, h2, h3, h4, h5, h6');
        // if headings are present, add observers.
        if (headings.length > 0 && pageNav !== null) {
            addNavObserver(headings);
        }
    }

    setupAttachmentPreview() {
        if (!this.previewContainer || !this.contentContainer || !this.previewBody || !this.previewClose) {
            return;
        }

        DOM.onSelect(this.previewClose, () => {
            this.hideAttachmentPreview();
        });

        this.previewEscapeHandler = event => {
            if (event.key === 'Escape' && !this.previewContainer.hidden) {
                this.hideAttachmentPreview();
            }
        };
        document.addEventListener('keydown', this.previewEscapeHandler);

        this.previewEventHandler = event => {
            const {attachmentId, attachmentName, attachmentExtension, attachmentUrl} = event.detail || {};
            const id = Number(attachmentId);
            if (!id || !attachmentUrl) {
                return;
            }

            this.showAttachmentPreview({
                id,
                name: attachmentName || '',
                extension: (attachmentExtension || '').toLowerCase(),
                url: attachmentUrl,
            });
        };
        document.addEventListener('attachments-list-preview', this.previewEventHandler);
    }

    setPreviewContent(html, type) {
        if (!this.previewBody || !this.previewContainer) {
            return;
        }

        this.previewBody.innerHTML = html;
        if (type) {
            this.previewContainer.dataset.previewType = type;
        } else {
            delete this.previewContainer.dataset.previewType;
        }
        window.$components.init(this.previewBody);
        this.previewBody.scrollTop = 0;
    }

    hideAttachmentPreview() {
        if (!this.previewContainer || !this.contentContainer || !this.previewBody) {
            return;
        }

        this.previewContainer.hidden = true;
        this.contentContainer.hidden = false;
        this.previewBody.innerHTML = '';
        if (this.previewTitle) {
            this.previewTitle.textContent = '';
        }
        delete this.previewContainer.dataset.previewType;
        this.previewRequestToken = null;
    }

    async showAttachmentPreview({id, name, extension, url}) {
        if (!this.previewContainer || !this.contentContainer || !this.previewBody) {
            return;
        }

        const requestToken = Symbol('attachment-preview');
        this.previewRequestToken = requestToken;
        this.previewContainer.dataset.previewAttachmentId = String(id);
        if (this.previewTitle) {
            this.previewTitle.textContent = name;
        }

        this.contentContainer.hidden = true;
        this.previewContainer.hidden = false;
        this.previewBody.innerHTML = '';
        DOM.showLoading(this.previewBody);

        if (this.previewClose instanceof HTMLElement) {
            this.previewClose.focus();
        }

        try {
            if (extension === 'pdf') {
                const safeName = escapeHtml(name);
                const iframe = `<iframe class="attachment-preview-frame" src="${url}" title="${safeName}" loading="lazy"></iframe>`;
                this.setPreviewContent(iframe, 'pdf');
                return;
            }

            if (extension === 'docx') {
                const html = await this.renderDocxPreview(url);
                if (this.previewRequestToken !== requestToken) {
                    return;
                }
                this.setPreviewContent(html, 'word');
                return;
            }

            if (extension === 'xls' || extension === 'xlsx') {
                const html = await this.renderSpreadsheetPreview(url);
                if (this.previewRequestToken !== requestToken) {
                    return;
                }
                this.setPreviewContent(html, 'excel');
                return;
            }

            throw new Error(
                this.previewContainer.dataset.previewUnsupported
                || this.previewContainer.dataset.previewError
                || 'Unable to load preview.'
            );
        } catch (error) {
            if (this.previewRequestToken !== requestToken) {
                return;
            }

            this.hideAttachmentPreview();
            const defaultMessage = this.previewContainer.dataset.previewError || 'Unable to load preview.';
            const message = (error && error.message) ? error.message : defaultMessage;
            window.$events.error(message);
        } finally {
            if (this.previewRequestToken === requestToken) {
                this.previewRequestToken = null;
            }
        }
    }

    async renderDocxPreview(url) {
        const arrayBuffer = await this.fetchAttachmentArrayBuffer(url);
        const mammothModule = await import('mammoth/mammoth.browser');
        const mammoth = mammothModule.default || mammothModule;
        const result = await mammoth.convertToHtml({arrayBuffer});
        return `<div class="attachment-preview-document">${result.value}</div>`;
    }

    async renderSpreadsheetPreview(url) {
        const arrayBuffer = await this.fetchAttachmentArrayBuffer(url);
        const xlsxModule = await import('xlsx');
        const XLSX = xlsxModule.default || xlsxModule;
        const workbook = XLSX.read(arrayBuffer, {type: 'array'});

        const parts = workbook.SheetNames.map(sheetName => {
            const sheet = workbook.Sheets[sheetName];
            const sheetHtml = XLSX.utils.sheet_to_html(sheet, {
                header: `<h3 class="attachment-preview-sheet-title">${escapeHtml(sheetName)}</h3>`,
            });
            return `<div class="attachment-preview-sheet">${sheetHtml}</div>`;
        });

        return `<div class="attachment-preview-spreadsheet">${parts.join('')}</div>`;
    }

    async fetchAttachmentArrayBuffer(url) {
        const response = await fetch(url, {
            credentials: 'same-origin',
        });

        if (!response.ok) {
            const defaultMessage = this.previewContainer?.dataset.previewError || 'Unable to load preview.';
            let errorMessage = defaultMessage;
            try {
                const text = await response.text();
                if (text) {
                    errorMessage = text;
                }
            } catch (e) {
                // Ignore errors when reading error text
            }
            throw new Error(errorMessage);
        }

        return response.arrayBuffer();
    }

}
