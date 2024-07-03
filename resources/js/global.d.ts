import {ComponentStore} from "./services/components";

declare global {
    interface Window {
        $components: ComponentStore,
    }
}