const Vue = require("vue");

function exists(id) {
    return document.getElementById(id) !== null;
}

let vueMapping = {
    'search-system': require('./search'),
    'entity-dashboard': require('./entity-dashboard'),
    'code-editor': require('./code-editor'),
    'image-manager': require('./image-manager'),
    'tag-manager': require('./tag-manager'),
    'attachment-manager': require('./attachment-manager'),
    'page-editor': require('./page-editor'),
};

window.vues = {};

let ids = Object.keys(vueMapping);
for (let i = 0, len = ids.length; i < len; i++) {
    if (!exists(ids[i])) continue;
    let config = vueMapping[ids[i]];
    config.el = '#' + ids[i];
    window.vues[ids[i]] = new Vue(config);
}