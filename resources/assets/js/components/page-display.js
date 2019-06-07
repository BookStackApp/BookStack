import Clipboard from "clipboard/dist/clipboard.min";
import Code from "../services/code";
import * as DOM from "../services/dom";
import {scrollAndHighlightElement} from "../services/util";

class PageDisplay {

    constructor(elem) {
        this.elem = elem;
        this.pageId = elem.getAttribute('page-display');

        Code.highlight();
        this.setupPointer();
        this.setupNavHighlighting();

        // Check the hash on load
        if (window.location.hash) {
            let text = window.location.hash.replace(/\%20/g, ' ').substr(1);
            this.goToText(text);
        }

        // Sidebar page nav click event
        const sidebarPageNav = document.querySelector('.sidebar-page-nav');
        if (sidebarPageNav) {
            DOM.onChildEvent(sidebarPageNav, 'a', 'click', (event, child) => {
                window.components['tri-layout'][0].showContent();
                this.goToText(child.getAttribute('href').substr(1));
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

    setupPointer() {
        let pointer = document.getElementById('pointer');
        if (!pointer) {
            return;
        }

        // Set up pointer
        pointer = pointer.parentNode.removeChild(pointer);
        const pointerInner = pointer.querySelector('div.pointer');

        // Instance variables
        let pointerShowing = false;
        let isSelection = false;
        let pointerModeLink = true;
        let pointerSectionId = '';

        // Select all contents on input click
        DOM.onChildEvent(pointer, 'input', 'click', (event, input) => {
            input.select();
            event.stopPropagation();
        });

        // Prevent closing pointer when clicked or focused
        DOM.onEvents(pointer, ['click', 'focus'], event => {
            event.stopPropagation();
        });

        // Pointer mode toggle
        DOM.onChildEvent(pointer, 'span.icon', 'click', (event, icon) => {
            event.stopPropagation();
            pointerModeLink = !pointerModeLink;
            icon.querySelector('[data-icon="include"]').style.display = (!pointerModeLink) ? 'inline' : 'none';
            icon.querySelector('[data-icon="link"]').style.display = (pointerModeLink) ? 'inline' : 'none';
            updatePointerContent();
        });

        // Set up clipboard
        new Clipboard(pointer.querySelector('button'));

        // Hide pointer when clicking away
        DOM.onEvents(document.body, ['click', 'focus'], event => {
            if (!pointerShowing || isSelection) return;
            pointer = pointer.parentElement.removeChild(pointer);
            pointerShowing = false;
        });

        let updatePointerContent = (element) => {
            let inputText = pointerModeLink ? window.baseUrl(`/link/${this.pageId}#${pointerSectionId}`) : `{{@${this.pageId}#${pointerSectionId}}}`;
            if (pointerModeLink && !inputText.startsWith('http')) {
                inputText = window.location.protocol + "//" + window.location.host + inputText;
            }

            pointer.querySelector('input').value = inputText;

            // Update anchor if present
            const editAnchor = pointer.querySelector('#pointer-edit');
            if (editAnchor && element) {
                const editHref = editAnchor.dataset.editHref;
                const elementId = element.id;

                // get the first 50 characters.
                const queryContent = element.textContent && element.textContent.substring(0, 50);
                editAnchor.href = `${editHref}?content-id=${elementId}&content-text=${encodeURIComponent(queryContent)}`;
            }
        };

        // Show pointer when selecting a single block of tagged content
        DOM.forEach('.page-content [id^="bkmrk"]', bookMarkElem => {
            DOM.onEvents(bookMarkElem, ['mouseup', 'keyup'], event => {
                event.stopPropagation();
                let selection = window.getSelection();
                if (selection.toString().length === 0) return;

                // Show pointer and set link
                pointerSectionId = bookMarkElem.id;
                updatePointerContent(bookMarkElem);

                bookMarkElem.parentNode.insertBefore(pointer, bookMarkElem);
                pointer.style.display = 'block';
                pointerShowing = true;
                isSelection = true;

                // Set pointer to sit near mouse-up position
                requestAnimationFrame(() => {
                    const bookMarkBounds = bookMarkElem.getBoundingClientRect();
                    let pointerLeftOffset = (event.pageX - bookMarkBounds.left - 164);
                    if (pointerLeftOffset < 0) {
                        pointerLeftOffset = 0
                    }
                    const pointerLeftOffsetPercent = (pointerLeftOffset / bookMarkBounds.width) * 100;

                    pointerInner.style.left = pointerLeftOffsetPercent + '%';

                    setTimeout(() => {
                        isSelection = false;
                    }, 100);
                });

            });
        });
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
}

export default PageDisplay;
