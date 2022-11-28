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
        this.settingContainer = this.$refs.settingContainer;

        this.editor = null;
        initEditor({
            pageId: this.pageId,
            container: this.elem,
            displayEl: this.display,
            inputEl: this.input,
            drawioUrl: this.getDrawioUrl(),
            text: {
                serverUploadLimit: this.serverUploadLimitText,
                imageUploadError: this.imageUploadErrorText,
            },
            settings: this.loadSettings(),
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

        // Setting changes
        this.settingContainer.addEventListener('change', e => {
            const actualInput = e.target.parentNode.querySelector('input[type="hidden"]');
            const name = actualInput.getAttribute('name');
            const value = actualInput.getAttribute('value');
            window.$http.patch('/preferences/update-boolean', {name, value});
            this.editor.settings.set(name, value === 'true');
        });

        // Refresh CodeMirror on container resize
        const resizeDebounced = debounce(() => this.editor.cm.refresh(), 100, false);
        const observer = new ResizeObserver(resizeDebounced);
        observer.observe(this.elem);
    }

    loadSettings() {
        const settings = {};
        const inputs = this.settingContainer.querySelectorAll('input[type="hidden"]');

        for (const input of inputs) {
            settings[input.getAttribute('name')] = input.value === 'true';
        }

        return settings;
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
