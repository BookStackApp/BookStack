import {ComponentStore} from "./services/components";
import {EventManager} from "./services/events";
import {HttpManager} from "./services/http";
import {Translator} from "./services/translations";

declare global {
    const __DEV__: boolean;

    interface Window {
        __DEV__: boolean;
        $components: ComponentStore;
        $events: EventManager;
        $trans: Translator;
        $http: HttpManager;
        baseUrl: (path: string) => string;
        importVersioned: (module: string) => Promise<object>;
    }
}

export type CodeModule = (typeof import('./code/index.mjs'));