import {ComponentStore} from "./services/components";
import {EventManager} from "./services/events";
import {HttpManager} from "./services/http";

declare global {
    const __DEV__: boolean;

    interface Window {
        $components: ComponentStore;
        $events: EventManager;
        $http: HttpManager;
        baseUrl: (path: string) => string;
    }
}