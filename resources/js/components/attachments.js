import {showLoading} from "../services/dom";
import {Component} from "./component";

export class Attachments extends Component {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;
        this.editContainer = this.$refs.editContainer;
        this.listContainer = this.$refs.listContainer;
        this.mainTabs = this.$refs.mainTabs;
        this.list = this.$refs.list;

        this.setupListeners();
    }

    setupListeners() {
        const reloadListBound = this.reloadList.bind(this);
        this.container.addEventListener('dropzone-success', reloadListBound);
        this.container.addEventListener('ajax-form-success', reloadListBound);

        this.container.addEventListener('sortable-list-sort', event => {
            this.updateOrder(event.detail.ids);
        });

        this.container.addEventListener('event-emit-select-edit', event => {
            this.startEdit(event.detail.id);
        });

        this.container.addEventListener('event-emit-select-edit-back', event => {
            this.stopEdit();
        });

        this.container.addEventListener('event-emit-select-insert', event => {
            const insertContent = event.target.closest('[data-drag-content]').getAttribute('data-drag-content');
            const contentTypes = JSON.parse(insertContent);
            window.$events.emit('editor::insert', {
                html: contentTypes['text/html'],
                markdown: contentTypes['text/plain'],
            });
        });
    }

    reloadList() {
        this.stopEdit();
        this.mainTabs.components.tabs.show('items');
        window.$http.get(`/attachments/get/page/${this.pageId}`).then(resp => {
            this.list.innerHTML = resp.data;
            window.$components.init(this.list);
        });
    }

    updateOrder(idOrder) {
        window.$http.put(`/attachments/sort/page/${this.pageId}`, {order: idOrder}).then(resp => {
            window.$events.emit('success', resp.data.message);
        });
    }

    async startEdit(id) {
        this.editContainer.classList.remove('hidden');
        this.listContainer.classList.add('hidden');

        showLoading(this.editContainer);
        const resp = await window.$http.get(`/attachments/edit/${id}`);
        this.editContainer.innerHTML = resp.data;
        window.$components.init(this.editContainer);
    }

    stopEdit() {
        this.editContainer.classList.add('hidden');
        this.listContainer.classList.remove('hidden');
    }

}

export default Attachments;