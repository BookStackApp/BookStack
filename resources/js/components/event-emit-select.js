import {onSelect} from "../services/dom";
import {Component} from "./component";

/**
 * EventEmitSelect
 * Component will simply emit an event when selected.
 *
 * Has one required option: "name".
 * A name of "hello" will emit a component DOM event of
 * "event-emit-select-name"
 *
 * All options will be set as the "detail" of the event with
 * their values included.
 */
export class EventEmitSelect extends Component{
    setup() {
        this.container = this.$el;
        this.name = this.$opts.name;


        onSelect(this.$el, () => {
            this.$emit(this.name, this.$opts);
        });
    }

}