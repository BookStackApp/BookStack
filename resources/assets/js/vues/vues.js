
function exists(id) {
    return document.getElementById(id) !== null;
}

let vueMapping = {
    'search-system': require('./search')
};

Object.keys(vueMapping).forEach(id => {
    if (exists(id)) {
        let config = vueMapping[id];
        config.el = '#' + id;
        new Vue(config);
    }
});