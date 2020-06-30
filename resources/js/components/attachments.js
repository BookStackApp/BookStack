
/**
 * Attachments
 * @extends {Component}
 */
class Attachments {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;
        this.editContainer = this.$refs.editContainer;
        this.mainTabs = this.$refs.mainTabs;
        this.list = this.$refs.list;

        this.setupListeners();
    }

    setupListeners() {
        this.container.addEventListener('dropzone-success', event => {
            this.mainTabs.components.tabs.show('items');
            window.$http.get(`/attachments/get/page/${this.pageId}`).then(resp => {
                this.list.innerHTML = resp.data;
                window.components.init(this.list);
            })
        });

        this.container.addEventListener('sortable-list-sort', event => {
            this.updateOrder(event.detail.ids);
        });

        this.editContainer.addEventListener('keypress', event => {
            if (event.key === 'Enter') {
                // TODO - Update editing file
            }
        })
    }

    updateOrder(idOrder) {
        window.$http.put(`/attachments/sort/page/${this.pageId}`, {order: idOrder}).then(resp => {
            window.$events.emit('success', resp.data.message);
        });
    }

}

export default Attachments;