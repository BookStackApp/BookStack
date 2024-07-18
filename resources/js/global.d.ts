import {ComponentStore} from "./services/components";
import {EventManager} from "./services/events";
import {HttpManager} from "./services/http";

declare global {
    interface Window {
        $components: ComponentStore,
        $events: EventManager,
        $http: HttpManager,
        baseUrl: (path: string) => string;
    }
}