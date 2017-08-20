const Vue = require("vue");

function exists(id) {
    return document.getElementById(id) !== null;
}

let vueMapping = {
    'search-system': require('./search'),
    'entity-dashboard': require('./entity-search'),
    'code-editor': require('./code-editor'),
    'image-manager': require('./image-manager'),
    'tag-manager': require('./tag-manager'),
    'page-comments': require('./page-comments')
};

window.vues = {};

Object.keys(vueMapping).forEach(id => {
    if (exists(id)) {
        let config = vueMapping[id];
        config.el = '#' + id;
        window.vues[id] = new Vue(config);
    }
});