import {Component} from "./component";

export class QueryManager extends Component {
    protected input!: HTMLTextAreaElement;
    protected generatedLoading!: HTMLElement;
    protected generatedDisplay!: HTMLElement;
    protected contentLoading!: HTMLElement;
    protected contentDisplay!: HTMLElement;
    protected form!: HTMLFormElement;
    protected fieldset!: HTMLFieldSetElement;

    setup() {
        this.input = this.$refs.input as HTMLTextAreaElement;
        this.form = this.$refs.form as HTMLFormElement;
        this.fieldset = this.$refs.fieldset as HTMLFieldSetElement;
        this.generatedLoading = this.$refs.generatedLoading;
        this.generatedDisplay = this.$refs.generatedDisplay;
        this.contentLoading = this.$refs.contentLoading;
        this.contentDisplay = this.$refs.contentDisplay;

        this.setupListeners();

        // Start lookup if a query is set
        if (this.input.value.trim() !== '') {
            this.runQuery();
        }
    }

    protected setupListeners(): void {
        // Handle form submission
        this.form.addEventListener('submit', event => {
            event.preventDefault();
            this.runQuery();
        });

        // Allow Ctrl+Enter to run a query
        this.input.addEventListener('keydown', event => {
            if (event.key === 'Enter' && event.ctrlKey && this.input.value.trim() !== '') {
                this.runQuery();
            }
        });
    }

    protected async runQuery(): Promise<void> {
        this.contentLoading.hidden = false;
        this.generatedLoading.hidden = false;
        this.contentDisplay.innerHTML = '';
        this.generatedDisplay.innerHTML = '';
        this.fieldset.disabled = true;

        const query = this.input.value.trim();
        const url = new URL(window.location.href);
        url.searchParams.set('ask', query);
        window.history.pushState({}, '', url.toString());

        const es = window.$http.eventSource('/query', 'POST', {query});

        let messageCount = 0;
        for await (const {data, event, id} of es) {
            messageCount++;
            if (messageCount === 1) {
                // Entity results
                this.contentDisplay.innerHTML = JSON.parse(data).view;
                this.contentLoading.hidden = true;
            } else if (messageCount === 2) {
                // LLM Output
                this.generatedDisplay.innerText = JSON.parse(data).result;
                this.generatedLoading.hidden = true;
            } else {
                es.close();
                break;
            }
        }

        this.fieldset.disabled = false;
    }
}