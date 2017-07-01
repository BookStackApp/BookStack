const Vue = require("vue");

function exists(id) {
    return document.getElementById(id) !== null;
}

let vueMapping = {
    'search-system': require('./search'),
    'entity-dashboard': require('./entity-search'),
    'code-editor': require('./code-editor')
};

window.vues = {};

Object.keys(vueMapping).forEach(id => {
    if (exists(id)) {
        let config = vueMapping[id];
        config.el = '#' + id;
        window.vues[id] = new Vue(config);
    }
});