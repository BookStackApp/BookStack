const Vue = require("vue");

function exists(id) {
    return document.getElementById(id) !== null;
}

let vueMapping = {
    'search-system': require('./search'),
    'entity-dashboard': require('./entity-search'),
};

Object.keys(vueMapping).forEach(id => {
    if (exists(id)) {
        let config = vueMapping[id];
        config.el = '#' + id;
        new Vue(config);
    }
});