import * as DOM from "../services/dom";

class TemplateManager {

    constructor(elem) {
        this.elem = elem;
        this.list = elem.querySelector('[template-manager-list]');
        this.searching = false;

        // Template insert action buttons
        DOM.onChildEvent(this.elem, '[template-action]', 'click', this.handleTemplateActionClick.bind(this));

        // Template list pagination click
        DOM.onChildEvent(this.elem, '.pagination a', 'click', this.handlePaginationClick.bind(this));

        // Template list item content click
        DOM.onChildEvent(this.elem, '.template-item-content', 'click', this.handleTemplateItemClick.bind(this));

        // Template list item drag start
        DOM.onChildEvent(this.elem, '.template-item', 'dragstart', this.handleTemplateItemDragStart.bind(this));

        this.setupSearchBox();
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
        const eventName = 'editor::' + action;
        window.$events.emit(eventName, resp.data);
    }

    async handlePaginationClick(event, paginationLink) {
        event.preventDefault();
        const paginationUrl = paginationLink.getAttribute('href');
        const resp = await window.$http.get(paginationUrl);
        this.list.innerHTML = resp.data;
    }

    setupSearchBox() {
        const searchBox = this.elem.querySelector('.search-box');

        // Search box may not exist if there are no existing templates in the system.
        if (!searchBox) return;

        const input = searchBox.querySelector('input');
        const submitButton = searchBox.querySelector('button');
        const cancelButton = searchBox.querySelector('button.search-box-cancel');

        async function performSearch() {
            const searchTerm = input.value;
            const resp = await window.$http.get(`/templates`, {
                search: searchTerm
            });
            cancelButton.style.display = searchTerm ? 'block' : 'none';
            this.list.innerHTML = resp.data;
        }
        performSearch = performSearch.bind(this);

        // Search box enter press
        searchBox.addEventListener('keypress', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                performSearch();
            }
        });

        // Submit button press
        submitButton.addEventListener('click', event => {
            performSearch();
        });

        // Cancel button press
        cancelButton.addEventListener('click', event => {
            input.value = '';
            performSearch();
        });
    }
}

export default TemplateManager;