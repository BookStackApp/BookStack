import Vue from "vue";

function exists(id) {
    return document.getElementById(id) !== null;
}

import imageManager from "./image-manager";

let vueMapping = {
    'image-manager': imageManager,
};

window.vues = {};

function load() {
    let ids = Object.keys(vueMapping);
    for (let i = 0, len = ids.length; i < len; i++) {
        if (!exists(ids[i])) continue;
        let config = vueMapping[ids[i]];
        config.el = '#' + ids[i];
        window.vues[ids[i]] = new Vue(config);
    }
}

export default load;



