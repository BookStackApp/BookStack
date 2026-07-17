import * as DOM from '../services/dom';
import {Component} from './component';
import {copyTextToClipboard} from '../services/clipboard';
import {hashElement, normalizeNodeTextOffsetToParent} from "../services/dom";
import {PageComments} from "./page-comments";

export class Pointer extends Component {

    protected showing: boolean = false;
    protected isMakingSelection: boolean = false;
    protected targetElement: HTMLElement|null = null;
    protected targetSelectionRange: Range|null = null;

    protected pointer!: HTMLElement;
    protected linkInput!: HTMLInputElement;
    protected linkButton!: HTMLElement;
    protected includeInput!: HTMLInputElement;
    protected includeButton!: HTMLElement;
    protected sectionModeButton!: HTMLElement;
    protected commentButton!: HTMLElement;
    protected modeToggles!: HTMLElement[];
    protected modeSections!: HTMLElement[];
    protected pageId!: string;

    setup() {
        this.pointer = this.$refs.pointer;
        this.linkInput = this.$refs.linkInput as HTMLInputElement;
        this.linkButton = this.$refs.linkButton;
        this.includeInput = this.$refs.includeInput as HTMLInputElement;
        this.includeButton = this.$refs.includeButton;
        this.sectionModeButton = this.$refs.sectionModeButton;
        this.commentButton = this.$refs.commentButton;
        this.modeToggles = this.$manyRefs.modeToggle;
        this.modeSections = this.$manyRefs.modeSection;
        this.pageId = this.$opts.pageId;

        this.setupListeners();
    }

    setupListeners() {
        // Copy on copy button click
        this.includeButton.addEventListener('click', () => copyTextToClipboard(this.includeInput.value));
        this.linkButton.addEventListener('click', () => copyTextToClipboard(this.linkInput.value));

        // Select all contents on input click
        DOM.onSelect([this.includeInput, this.linkInput], event => {
            (event.target as HTMLInputElement).select();
            event.stopPropagation();
        });

        // Prevent closing pointer when clicked or focused
        DOM.onEvents(this.pointer, ['click', 'focus'], event => {
            event.stopPropagation();
        });

        // Hide pointer when clicking away
        DOM.onEvents(document.body, ['click', 'focus'], () => {
            if (!this.showing || this.isMakingSelection) return;
            this.hidePointer();
        });

        // Hide pointer on escape press
        DOM.onEscapePress(this.pointer, this.hidePointer.bind(this));

        // Show pointer when selecting a single block of tagged content
        const pageContent = document.querySelector('.page-content');
        DOM.onEvents(pageContent, ['mouseup', 'keyup'], event => {
            event.stopPropagation();
            const targetEl = (event.target as HTMLElement).closest('[id^="bkmrk"]');
            if (targetEl instanceof HTMLElement && (window.getSelection() || '').toString().length > 0) {
                const xPos = (event instanceof MouseEvent) ? event.pageX : 0;
                this.showPointerAtTarget(targetEl, xPos, false);
            }
        });

        // Start section selection mode on button press
        DOM.onSelect(this.sectionModeButton, this.enterSectionSelectMode.bind(this));

        // Toggle between pointer modes
        DOM.onSelect(this.modeToggles, event => {
            const targetToggle = (event.target as HTMLElement);
            for (const section of this.modeSections) {
                const show = !section.contains(targetToggle);
                section.toggleAttribute('hidden', !show);
            }

            const otherToggle = this.modeToggles.find(b => b !== targetToggle);
            otherToggle && otherToggle.focus();
        });

        if (this.commentButton) {
            DOM.onSelect(this.commentButton, this.createCommentAtPointer.bind(this));
        }
    }

    hidePointer() {
        this.pointer.style.removeProperty('display');
        this.showing = false;
        this.targetElement = null;
        this.targetSelectionRange = null;
    }

    /**
     * Move and display the pointer at the given element, targeting the given screen x-position if possible.
     */
    showPointerAtTarget(element: HTMLElement, xPosition: number, keyboardMode: boolean) {
        this.targetElement = element;
        this.targetSelectionRange = window.getSelection()?.getRangeAt(0) || null;
        this.updateDomForTarget(element);

        this.pointer.style.display = 'block';
        const targetBounds = element.getBoundingClientRect();
        const pointerBounds = this.pointer.getBoundingClientRect();

        const xTarget = Math.min(Math.max(xPosition, targetBounds.left), targetBounds.right);
        const xOffset = xTarget - (pointerBounds.width / 2);
        const yOffset = (targetBounds.top - pointerBounds.height) - 16;

        this.pointer.style.left = `${xOffset}px`;
        this.pointer.style.top = `${yOffset}px`;

        this.showing = true;
        this.isMakingSelection = true;

        setTimeout(() => {
            this.isMakingSelection = false;
        }, 100);

        const scrollListener = () => {
            this.hidePointer();
            window.removeEventListener('scroll', scrollListener);
        };

        element.parentElement?.insertBefore(this.pointer, element);
        if (!keyboardMode) {
            window.addEventListener('scroll', scrollListener, {passive: true});
        }
    }

    /**
     * Update the pointer inputs/content for the given target element.
     */
    updateDomForTarget(element: HTMLElement) {
        const permaLink = window.baseUrl(`/link/${this.pageId}#${element.id}`);
        const includeTag = `{{@${this.pageId}#${element.id}}}`;

        this.linkInput.value = permaLink;
        this.includeInput.value = includeTag;

        // Update anchor if present
        const editAnchor = this.pointer.querySelector('#pointer-edit');
        if (editAnchor instanceof HTMLAnchorElement && element) {
            const {editHref} = editAnchor.dataset;
            const elementId = element.id;

            // Get the first 50 characters.
            const queryContent = (element.textContent || '').substring(0, 50);
            editAnchor.href = `${editHref}?content-id=${elementId}&content-text=${encodeURIComponent(queryContent)}`;
        }
    }

    enterSectionSelectMode() {
        const sections = Array.from(document.querySelectorAll('.page-content [id^="bkmrk"]')) as HTMLElement[];
        for (const section of sections) {
            section.setAttribute('tabindex', '0');
        }

        sections[0].focus();

        DOM.onEnterPress(sections, event => {
            this.showPointerAtTarget(event.target as HTMLElement, 0, true);
            this.pointer.focus();
        });
    }

    createCommentAtPointer() {
        if (!this.targetElement) {
            return;
        }

        const refId = this.targetElement.id;
        const hash = hashElement(this.targetElement);
        let range = '';
        if (this.targetSelectionRange) {
            const commonContainer = this.targetSelectionRange.commonAncestorContainer;
            if (this.targetElement.contains(commonContainer)) {
                const start = normalizeNodeTextOffsetToParent(
                    this.targetSelectionRange.startContainer,
                    this.targetSelectionRange.startOffset,
                    this.targetElement
                );
                const end = normalizeNodeTextOffsetToParent(
                    this.targetSelectionRange.endContainer,
                    this.targetSelectionRange.endOffset,
                    this.targetElement
                );
                range = `${start}-${end}`;
            }
        }

        const reference = `${refId}:${hash}:${range}`;
        const pageComments = window.$components.first('page-comments') as PageComments;
        pageComments.startNewComment(reference);
    }

}
