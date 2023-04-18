import * as DOM from '../services/dom';
import {Component} from './component';

export class TemplateManager extends Component {

    setup() {
        this.container = this.$el;
        this.list = this.$refs.list;

        this.searchInput = this.$refs.searchInput;
        this.searchButton = this.$refs.searchButton;
        this.searchCancel = this.$refs.searchCancel;

        this.setupListeners();
    }

    setupListeners() {
        // Template insert action buttons
        DOM.onChildEvent(this.container, '[template-action]', 'click', this.handleTemplateActionClick.bind(this));

        // Template list pagination click
        DOM.onChildEvent(this.container, '.pagination a', 'click', this.handlePaginationClick.bind(this));

        // Template list item content click
        DOM.onChildEvent(this.container, '.template-item-content', 'click', this.handleTemplateItemClick.bind(this));

        // Template list item drag start
        DOM.onChildEvent(this.container, '.template-item', 'dragstart', this.handleTemplateItemDragStart.bind(this));

        // Search box enter press
        this.searchInput.addEventListener('keypress', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                this.performSearch();
            }
        });

        // Search submit button press
        this.searchButton.addEventListener('click', event => this.performSearch());

        // Search cancel button press
        this.searchCancel.addEventListener('click', event => {
            this.searchInput.value = '';
            this.performSearch();
        });
    }

    handleTemplateItemClick(event, templateItem) {
        const templateId = templateItem.closest('[template-id]').getAttribute('template-id');
        this.insertTemplate(templateId, 'replace');
    }

    handleTemplateItemDragStart(event, templateItem) {
        const templateId = templateItem.closest('[template-id]').getAttribute('template-id');
        event.dataTransfer.setData('bookstack/template', templateId);
        event.dataTransfer.setData('text/plain', templateId);
    }

    handleTemplateActionClick(event, actionButton) {
        event.stopPropagation();

        const action = actionButton.getAttribute('template-action');
        const templateId = actionButton.closest('[template-id]').getAttribute('template-id');
        this.insertTemplate(templateId, action);
    }

    async insertTemplate(templateId, action = 'replace') {
        const resp = await window.$http.get(`/templates/${templateId}`);
        const eventName = `editor::${action}`;
        window.$events.emit(eventName, resp.data);
    }

    async handlePaginationClick(event, paginationLink) {
        event.preventDefault();
        const paginationUrl = paginationLink.getAttribute('href');
        const resp = await window.$http.get(paginationUrl);
        this.list.innerHTML = resp.data;
    }

    async performSearch() {
        const searchTerm = this.searchInput.value;
        const resp = await window.$http.get('/templates', {
            search: searchTerm,
        });
        this.searchCancel.style.display = searchTerm ? 'block' : 'none';
        this.list.innerHTML = resp.data;
    }

}
