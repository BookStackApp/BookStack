import {Component} from "./component";

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
    }
}