import * as DOM from '../services/dom';
import {scrollAndHighlightElement} from '../services/util';
import {Component} from './component';

export class PageDisplay extends Component {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;

        window.importVersioned('code').then(Code => Code.highlight());
        this.setupNavHighlighting();
        this.setupDetailsCodeBlockRefresh();

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
        const idElem = document.getElementById(text);

        DOM.forEach('.page-content [data-highlighted]', elem => {
            elem.removeAttribute('data-highlighted');
            elem.style.backgroundColor = null;
        });

        if (idElem !== null) {
            scrollAndHighlightElement(idElem);
        } else {
            const textElem = DOM.findText('.page-content > div > *', text);
            if (textElem) {
                scrollAndHighlightElement(textElem);
            }
        }
    }

    setupNavHighlighting() {
        const pageNav = document.querySelector('.sidebar-page-nav');

        // fetch all the headings.
        const headings = document.querySelector('.page-content').querySelectorAll('h1, h2, h3, h4, h5, h6');
        // if headings are present, add observers.
        if (headings.length > 0 && pageNav !== null) {
            addNavObserver(headings);
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

        function headingVisibilityChange(entries, observer) {
            for (const entry of entries) {
                const isVisible = (entry.intersectionRatio === 1);
                toggleAnchorHighlighting(entry.target.id, isVisible);
            }
        }

        function toggleAnchorHighlighting(elementId, shouldHighlight) {
            DOM.forEach(`a[href="#${elementId}"]`, anchor => {
                anchor.closest('li').classList.toggle('current-heading', shouldHighlight);
            });
        }
    }

    setupDetailsCodeBlockRefresh() {
        const onToggle = event => {
            const codeMirrors = [...event.target.querySelectorAll('.CodeMirror')];
            codeMirrors.forEach(cm => cm.CodeMirror && cm.CodeMirror.refresh());
        };

        const details = [...this.container.querySelectorAll('details')];
        details.forEach(detail => detail.addEventListener('toggle', onToggle));
    }

}
