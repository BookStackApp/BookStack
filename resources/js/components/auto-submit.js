import {Component} from './component';

export class AutoSubmit extends Component {

    setup() {
        this.form = this.$el;

        this.form.submit();
    }

}
