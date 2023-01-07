import {debounce} from "../services/util";
import {Component} from "./component";
import {init as initEditor} from "../markdown/editor";

export class MarkdownEditor extends Component {

    setup() {
        this.elem = this.$el;

        this.pageId = this.$opts.pageId;
        this.textDirection = this.$opts.textDirection;
        this.imageUploadErrorText = this.$opts.imageUploadErrorText;
        this.serverUploadLimitText = this.$opts.serverUploadLimitText;

        this.display = this.$refs.display;
        this.input = this.$refs.input;
        this.divider = this.$refs.divider;
        this.displayWrap = this.$refs.displayWrap;

        const settingContainer = this.$refs.settingContainer;
        const settingInputs = settingContainer.querySelectorAll('input[type="checkbox"]');

        this.editor = null;
        initEditor({
            pageId: this.pageId,
            container: this.elem,
            displayEl: this.display,
            inputEl: this.input,
            drawioUrl: this.getDrawioUrl(),
            settingInputs: Array.from(settingInputs),
            text: {
                serverUploadLimit: this.serverUploadLimitText,
                imageUploadError: this.imageUploadErrorText,
            },
        }).then(editor => {
            this.editor = editor;
            this.setupListeners();
            this.emitEditorEvents();
            this.scrollToTextIfNeeded();
            this.editor.actions.updateAndRender();
        });
    }

    emitEditorEvents() {
        window.$events.emitPublic(this.elem, 'editor-markdown::setup', {
            markdownIt: this.editor.markdown.getRenderer(),
            displayEl: this.display,
            codeMirrorInstance: this.editor.cm,
        });
    }

    setupListeners() {

        // Button actions
        this.elem.addEventListener('click', event => {
            let button = event.target.closest('button[data-action]');
            if (button === null) return;

            const action = button.getAttribute('data-action');
            if (action === 'insertHeadline') this.editor.actions.insertHeadline();
            if (action === 'insertBold') this.editor.actions.insertStyling('bold');
            if (action === 'insertItalic') this.editor.actions.insertStyling('italic');
            if (action === 'insertStrikethrough') this.editor.actions.insertStyling('strikethrough');
            if (action === 'insertListBulleted') this.editor.actions.insertList('bulleted');
            if (action === 'insertListNumbered') this.editor.actions.insertList('numbered');
            if (action === 'insertCode') this.editor.actions.insertCode();
            if (action === 'insertTable') this.editor.actions.insertSnippet('table');
            if (action === 'insertCollapsibleSection') this.editor.actions.insertSnippet('collapsibleSection');
            if (action === 'insertCalloutInfo') this.editor.actions.insertCallout('info');
            if (action === 'insertCalloutSuccess') this.editor.actions.insertCallout('success');
            if (action === 'insertCalloutWarning') this.editor.actions.insertCallout('warning');
            if (action === 'insertCalloutDanger') this.editor.actions.insertCallout('danger');
            if (action === 'insertImage') this.editor.actions.insertImage();
            if (action === 'insertLink') this.editor.actions.showLinkSelector();
            if (action === 'insertDrawing' && (event.ctrlKey || event.metaKey)) {
                this.editor.actions.showImageManager();
                return;
            }
            if (action === 'insertDrawing') this.editor.actions.startDrawing();
            if (action === 'fullscreen') this.editor.actions.fullScreen();
        });

        // Mobile section toggling
        this.elem.addEventListener('click', event => {
            const toolbarLabel = event.target.closest('.editor-toolbar-label');
            if (!toolbarLabel) return;

            const currentActiveSections = this.elem.querySelectorAll('.markdown-editor-wrap');
            for (const activeElem of currentActiveSections) {
                activeElem.classList.remove('active');
            }

            toolbarLabel.closest('.markdown-editor-wrap').classList.add('active');
        });

        // Refresh CodeMirror on container resize
        const resizeDebounced = debounce(() => this.editor.cm.refresh(), 100, false);
        const observer = new ResizeObserver(resizeDebounced);
        observer.observe(this.elem);

        this.handleDividerDrag();
    }

    handleDividerDrag() {
        this.divider.addEventListener('pointerdown', event => {
            const wrapRect = this.elem.getBoundingClientRect();
            const moveListener = (event) => {
                const xRel = event.pageX - wrapRect.left;
                const xPct = Math.min(Math.max(20, Math.floor((xRel / wrapRect.width) * 100)), 80);
                this.displayWrap.style.flexBasis = `${100-xPct}%`;
                this.editor.settings.set('editorWidth', xPct);
            };
            const upListener = (event) => {
                window.removeEventListener('pointermove', moveListener);
                window.removeEventListener('pointerup', upListener);
                this.display.style.pointerEvents = null;
                document.body.style.userSelect = null;
                this.editor.cm.refresh();
            };

            this.display.style.pointerEvents = 'none';
            document.body.style.userSelect = 'none';
            window.addEventListener('pointermove', moveListener);
            window.addEventListener('pointerup', upListener);
        });
        const widthSetting = this.editor.settings.get('editorWidth');
        if (widthSetting) {
            this.displayWrap.style.flexBasis = `${100-widthSetting}%`;
        }
    }

    scrollToTextIfNeeded() {
        const queryParams = (new URL(window.location)).searchParams;
        const scrollText = queryParams.get('content-text');
        if (scrollText) {
            this.editor.actions.scrollToText(scrollText);
        }
    }

    /**
     * Get the URL for the configured drawio instance.
     * @returns {String}
     */
    getDrawioUrl() {
        const drawioAttrEl = document.querySelector('[drawio-url]');
        if (!drawioAttrEl) {
            return '';
        }

        return drawioAttrEl.getAttribute('drawio-url') || '';
    }

}
