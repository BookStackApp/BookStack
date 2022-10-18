import * as DOM from "../services/dom";
import Clipboard from "clipboard/dist/clipboard.min";

/**
 * @extends Component
 */
class Pointer {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;

        // Instance variables
        this.showing = false;
        this.isSelection = false;
        this.pointerModeLink = true;
        this.pointerSectionId = '';

        this.init();
        this.setupListeners();
    }

    init() {
        // Set up pointer by removing it
        this.container.parentNode.removeChild(this.container);

        // Set up clipboard
        new Clipboard(this.container.querySelector('button'));
    }

    setupListeners() {
        // Select all contents on input click
        DOM.onChildEvent(this.container, 'input', 'click', (event, input) => {
            input.select();
            event.stopPropagation();
        });

        // Prevent closing pointer when clicked or focused
        DOM.onEvents(this.container, ['click', 'focus'], event => {
            event.stopPropagation();
        });

        // Pointer mode toggle
        DOM.onChildEvent(this.container, 'span.icon', 'click', (event, icon) => {
            event.stopPropagation();
            this.pointerModeLink = !this.pointerModeLink;
            icon.querySelector('[data-icon="include"]').style.display = (!this.pointerModeLink) ? 'inline' : 'none';
            icon.querySelector('[data-icon="link"]').style.display = (this.pointerModeLink) ? 'inline' : 'none';
            this.updateForTarget();
        });

        // Hide pointer when clicking away
        DOM.onEvents(document.body, ['click', 'focus'], event => {
            if (!this.showing || this.isSelection) return;
            this.container.parentElement.removeChild(this.container);
            this.showing = false;
        });

        // Show pointer when selecting a single block of tagged content
        const pageContent = document.querySelector('.page-content');
        DOM.onEvents(pageContent, ['mouseup', 'keyup'], event => {
            event.stopPropagation();
            const targetEl = event.target.closest('[id^="bkmrk"]');
            if (targetEl) {
                this.showPointerAtTarget(targetEl, event.pageX);
            }
        });
    }

    /**
     * Move and display the pointer at the given element, targeting the given screen x-position if possible.
     * @param {Element} element
     * @param {Number} xPosition
     */
    showPointerAtTarget(element, xPosition) {
        const selection = window.getSelection();
        if (selection.toString().length === 0) return;

        // Show pointer and set link
        this.pointerSectionId = element.id;
        this.updateForTarget(element);

        element.parentNode.insertBefore(this.container, element);
        this.container.style.display = 'block';
        this.showing = true;
        this.isSelection = true;

        // Set pointer to sit near mouse-up position
        requestAnimationFrame(() => {
            const bookMarkBounds = element.getBoundingClientRect();
            const pointerLeftOffset = Math.max((xPosition - bookMarkBounds.left - 164), 0);
            const pointerLeftOffsetPercent = (pointerLeftOffset / bookMarkBounds.width) * 100;

            this.container.children[0].style.left = pointerLeftOffsetPercent + '%';

            setTimeout(() => {
                this.isSelection = false;
            }, 100);
        });
    }

    /**
     * Update the pointer inputs/content for the given target element.
     * @param {?Element} element
     */
    updateForTarget(element) {
        let inputText = this.pointerModeLink ? window.baseUrl(`/link/${this.pageId}#${this.pointerSectionId}`) : `{{@${this.pageId}#${this.pointerSectionId}}}`;
        if (this.pointerModeLink && !inputText.startsWith('http')) {
            inputText = window.location.protocol + "//" + window.location.host + inputText;
        }

        this.container.querySelector('input').value = inputText;

        // Update anchor if present
        const editAnchor = this.container.querySelector('#pointer-edit');
        if (editAnchor && element) {
            const editHref = editAnchor.dataset.editHref;
            const elementId = element.id;

            // get the first 50 characters.
            const queryContent = element.textContent && element.textContent.substring(0, 50);
            editAnchor.href = `${editHref}?content-id=${elementId}&content-text=${encodeURIComponent(queryContent)}`;
        }
    }
}

export default Pointer;