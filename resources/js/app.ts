import {EventManager} from './services/events';
import {HttpManager} from './services/http';
import {Translator} from './services/translations';
import * as componentMap from './components/index';
import {ComponentStore} from './services/components';
import {baseUrl, importVersioned} from "./services/util";

// eslint-disable-next-line no-underscore-dangle
window.__DEV__ = false;

// Make common important util functions global
window.baseUrl = baseUrl;
window.importVersioned = importVersioned;

// Setup events, http & translation services
window.$http = new HttpManager();
window.$events = new EventManager();
window.$trans = new Translator();

// Load & initialise components
window.$components = new ComponentStore();
window.$components.register(componentMap);
window.$components.init();
