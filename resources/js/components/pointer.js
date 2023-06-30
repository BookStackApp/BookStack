import * as DOM from '../services/dom';
import {Component} from './component';
import {copyTextToClipboard} from '../services/clipboard';

export class Pointer extends Component {

    setup() {
        this.container = this.$el;
        this.pointer = this.$refs.pointer;
        this.linkInput = this.$refs.linkInput;
        this.linkButton = this.$refs.linkButton;
        this.includeInput = this.$refs.includeInput;
        this.includeButton = this.$refs.includeButton;
        this.sectionModeButton = this.$refs.sectionModeButton;
        this.modeToggles = this.$manyRefs.modeToggle;
        this.modeSections = this.$manyRefs.modeSection;
        this.pageId = this.$opts.pageId;

        // Instance variables
        this.showing = false;
        this.isSelection = false;

        this.setupListeners();
    }

    setupListeners() {
        // Copy on copy button click
        this.includeButton.addEventListener('click', () => copyTextToClipboard(this.includeInput.value));
        this.linkButton.addEventListener('click', () => copyTextToClipboard(this.linkInput.value));

        // Select all contents on input click
        DOM.onSelect([this.includeInput, this.linkInput], event => {
            event.target.select();
            event.stopPropagation();
        });

        // Prevent closing pointer when clicked or focused
        DOM.onEvents(this.pointer, ['click', 'focus'], event => {
            event.stopPropagation();
        });

        // Hide pointer when clicking away
        DOM.onEvents(document.body, ['click', 'focus'], () => {
            if (!this.showing || this.isSelection) return;
            this.hidePointer();
        });

        // Hide pointer on escape press
        DOM.onEscapePress(this.pointer, this.hidePointer.bind(this));

        // Show pointer when selecting a single block of tagged content
        const pageContent = document.querySelector('.page-content');
        DOM.onEvents(pageContent, ['mouseup', 'keyup'], event => {
            event.stopPropagation();
            const targetEl = event.target.closest('[id^="bkmrk"]');
            if (targetEl && window.getSelection().toString().length > 0) {
                this.showPointerAtTarget(targetEl, event.pageX, false);
            }
        });

        // Start section selection mode on button press
        DOM.onSelect(this.sectionModeButton, this.enterSectionSelectMode.bind(this));

        // Toggle between pointer modes
        DOM.onSelect(this.modeToggles, event => {
            for (const section of this.modeSections) {
                const show = !section.contains(event.target);
                section.toggleAttribute('hidden', !show);
            }

            this.modeToggles.find(b => b !== event.target).focus();
        });
    }

    hidePointer() {
        this.pointer.style.display = null;
        this.showing = false;
    }

    /**
     * Move and display the pointer at the given element, targeting the given screen x-position if possible.
     * @param {Element} element
     * @param {Number} xPosition
     * @param {Boolean} keyboardMode
     */
    showPointerAtTarget(element, xPosition, keyboardMode) {
        this.updateForTarget(element);

        this.pointer.style.display = 'block';
        const targetBounds = element.getBoundingClientRect();
        const pointerBounds = this.pointer.getBoundingClientRect();

        const xTarget = Math.min(Math.max(xPosition, targetBounds.left), targetBounds.right);
        const xOffset = xTarget - (pointerBounds.width / 2);
        const yOffset = (targetBounds.top - pointerBounds.height) - 16;

        this.pointer.style.left = `${xOffset}px`;
        this.pointer.style.top = `${yOffset}px`;

        this.showing = true;
        this.isSelection = true;

        setTimeout(() => {
            this.isSelection = false;
        }, 100);

        const scrollListener = () => {
            this.hidePointer();
            window.removeEventListener('scroll', scrollListener, {passive: true});
        };

        element.parentElement.insertBefore(this.pointer, element);
        if (!keyboardMode) {
            window.addEventListener('scroll', scrollListener, {passive: true});
        }
    }

    /**
     * Update the pointer inputs/content for the given target element.
     * @param {?Element} element
     */
    updateForTarget(element) {
        const permaLink = window.baseUrl(`/link/${this.pageId}#${element.id}`);
        const includeTag = `{{@${this.pageId}#${element.id}}}`;

        this.linkInput.value = permaLink;
        this.includeInput.value = includeTag;

        // Update anchor if present
        const editAnchor = this.pointer.querySelector('#pointer-edit');
        if (editAnchor && element) {
            const {editHref} = editAnchor.dataset;
            const elementId = element.id;

            // Get the first 50 characters.
            const queryContent = element.textContent && element.textContent.substring(0, 50);
            editAnchor.href = `${editHref}?content-id=${elementId}&content-text=${encodeURIComponent(queryContent)}`;
        }
    }

    enterSectionSelectMode() {
        const sections = Array.from(document.querySelectorAll('.page-content [id^="bkmrk"]'));
        for (const section of sections) {
            section.setAttribute('tabindex', '0');
        }

        sections[0].focus();

        DOM.onEnterPress(sections, event => {
            this.showPointerAtTarget(event.target, 0, true);
            this.pointer.focus();
        });
    }

}
