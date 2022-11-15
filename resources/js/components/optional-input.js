import {onSelect} from "../services/dom";
import {Component} from "./component";

export class OptionalInput extends Component {
    setup() {
        this.removeButton = this.$refs.remove;
        this.showButton = this.$refs.show;
        this.input = this.$refs.input;
        this.setupListeners();
    }

    setupListeners() {
        onSelect(this.removeButton, () => {
            this.input.value = '';
            this.input.classList.add('hidden');
            this.removeButton.classList.add('hidden');
            this.showButton.classList.remove('hidden');
        });

        onSelect(this.showButton, () => {
            this.input.classList.remove('hidden');
            this.removeButton.classList.remove('hidden');
            this.showButton.classList.add('hidden');
        });
    }

}