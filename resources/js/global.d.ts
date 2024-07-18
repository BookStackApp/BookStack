import {ComponentStore} from "./services/components";
import {EventManager} from "./services/events";

declare global {
    interface Window {
        $components: ComponentStore,
        $events: EventManager,
    }
}