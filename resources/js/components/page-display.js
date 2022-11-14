import * as DOM from "../services/dom";
import {scrollAndHighlightElement} from "../services/util";

class PageDisplay {

    constructor(elem) {
        this.elem = elem;
        this.pageId = elem.getAttribute('page-display');

        window.importVersioned('code').then(Code => Code.highlight());
        this.setupNavHighlighting();
        this.setupDetailsCodeBlockRefresh();

        // Check the hash on load
        if (window.location.hash) {
            let text = window.location.hash.replace(/\%20/g, ' ').substr(1);
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
                window.history.pushState(null, null, '#' + contentId);
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
        // Check if support is present for IntersectionObserver
        if (!('IntersectionObserver' in window) ||
            !('IntersectionObserverEntry' in window) ||
            !('intersectionRatio' in window.IntersectionObserverEntry.prototype)) {
            return;
        }

        let pageNav = document.querySelector('.sidebar-page-nav');

        // fetch all the headings.
        let headings = document.querySelector('.page-content').querySelectorAll('h1, h2, h3, h4, h5, h6');
        // if headings are present, add observers.
        if (headings.length > 0 && pageNav !== null) {
            addNavObserver(headings);
        }

        function addNavObserver(headings) {
            // Setup the intersection observer.
            let intersectOpts = {
                rootMargin: '0px 0px 0px 0px',
                threshold: 1.0
            };
            let pageNavObserver = new IntersectionObserver(headingVisibilityChange, intersectOpts);

            // observe each heading
            for (let heading of headings) {
                pageNavObserver.observe(heading);
            }
        }

        function headingVisibilityChange(entries, observer) {
            for (let entry of entries) {
                let isVisible = (entry.intersectionRatio === 1);
                toggleAnchorHighlighting(entry.target.id, isVisible);
            }
        }

        function toggleAnchorHighlighting(elementId, shouldHighlight) {
            DOM.forEach('a[href="#' + elementId + '"]', anchor => {
                anchor.closest('li').classList.toggle('current-heading', shouldHighlight);
            });
        }
    }

    setupDetailsCodeBlockRefresh() {
        const onToggle = event => {
            const codeMirrors = [...event.target.querySelectorAll('.CodeMirror')];
            codeMirrors.forEach(cm => cm.CodeMirror && cm.CodeMirror.refresh());
        };

        const details = [...this.elem.querySelectorAll('details')];
        details.forEach(detail => detail.addEventListener('toggle', onToggle));
    }
}

export default PageDisplay;
