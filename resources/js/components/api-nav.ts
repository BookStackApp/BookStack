import {Component} from "./component";

export class ApiNav extends Component {
    private select!: HTMLSelectElement;
    private sidebar!: HTMLElement;
    private body!: HTMLElement;

    setup() {
        this.select = this.$refs.select as HTMLSelectElement;
        this.sidebar = this.$refs.sidebar;
        this.body = this.$el.ownerDocument.documentElement;
        this.select.addEventListener('change', () => {
            const section = this.select.value;
            const sidebarTarget = document.getElementById(`sidebar-header-${section}`);
            const contentTarget = document.getElementById(`section-${section}`);
            if (sidebarTarget && contentTarget) {

                const sidebarPos = sidebarTarget.getBoundingClientRect().top - this.sidebar.getBoundingClientRect().top + this.sidebar.scrollTop;
                this.sidebar.scrollTo({
                    top: sidebarPos - 120,
                    behavior: 'smooth',
                });

                const bodyPos = contentTarget.getBoundingClientRect().top + this.body.scrollTop;
                this.body.scrollTo({
                    top: bodyPos - 20,
                    behavior: 'smooth',
                });
            }
        });
    }
}