import {Component} from "./component";
import {createEventSource} from "eventsource-client";

export class QueryManager extends Component {
    protected input!: HTMLTextAreaElement;
    protected generatedLoading!: HTMLElement;
    protected generatedDisplay!: HTMLElement;
    protected contentLoading!: HTMLElement;
    protected contentDisplay!: HTMLElement;
    protected form!: HTMLFormElement;

    setup() {
        this.input = this.$refs.input as HTMLTextAreaElement;
        this.form = this.$refs.form as HTMLFormElement;
        this.generatedLoading = this.$refs.generatedLoading;
        this.generatedDisplay = this.$refs.generatedDisplay;
        this.contentLoading = this.$refs.contentLoading;
        this.contentDisplay = this.$refs.contentDisplay;

        // TODO - Start lookup if query set

        // TODO - Update URL on query change

        // TODO - Handle query form submission
        this.form.addEventListener('submit', event => {
            event.preventDefault();
            this.runQuery();
        });
    }

    async runQuery() {
        this.contentLoading.hidden = false;
        this.generatedLoading.hidden = false;
        this.contentDisplay.innerHTML = '';
        this.generatedDisplay.innerHTML = '';

        const query = this.input.value;
        const es = window.$http.eventSource('/query', 'POST', {query});

        let messageCount = 0;
        for await (const {data, event, id} of es) {
            messageCount++;
            if (messageCount === 1) {
                // Entity results
                this.contentDisplay.innerText = data; // TODO - Update to HTML
                this.contentLoading.hidden = true;
            } else if (messageCount === 2) {
                // LLM Output
                this.generatedDisplay.innerText = data; // TODO - Update to HTML
                this.generatedLoading.hidden = true;
            } else {
                es.close()
                break;
            }
        }
    }
}